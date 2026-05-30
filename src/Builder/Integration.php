<?php

namespace PolylangTcoPro\Builder;

/**
 * Integrates Polylang language data into the Cornerstone builder UI.
 *
 * Replicates the WPML integration that CS ships natively, feeding Polylang
 * data through the same public filters CS exposes:
 *
 * - cs_app_data           → injects wpml-compatible language config into the
 *                           builder JS app (activates language picker UI)
 * - tco_routing_get/*     → enriches each document in the builder list with
 *                           its Polylang language data (enables list filter)
 * - cs_document_builder_info → enriches the active document when editing
 *                           (enables per-document language context in editor)
 *
 * All hooks are guarded: if WPML is active this class is a no-op and lets
 * CS's native WPML service handle everything.
 */
class Integration {

	// ------------------------------------------------------------------
	// Bootstrap
	// ------------------------------------------------------------------

	public static function setup(): void {
		if ( self::isWpmlActive() ) {
			return;
		}

		add_filter( 'cs_app_data', [ self::class, 'injectAppData' ] );

		add_filter( 'tco_routing_get/document-index',      [ self::class, 'enrichDocumentList' ], 20, 2 );
		add_filter( 'tco_routing_get/document-index-full', [ self::class, 'enrichDocumentList' ], 20, 2 );
		add_filter( 'tco_routing_get/document-search',     [ self::class, 'enrichDocumentList' ], 20, 2 );

		add_filter( 'cs_document_builder_info', [ self::class, 'enrichBuilderInfo' ], 10, 2 );
	}

	// ------------------------------------------------------------------
	// cs_app_data → window.csAppData.wpml
	// ------------------------------------------------------------------

	/**
	 * Injects a `wpml` key into the CS app config, mapping Polylang language
	 * data to the structure the CS builder JS expects from WPML.
	 *
	 * The presence of `wpml.advanced_builder = true` is what activates the
	 * language picker UI inside the builder (checked by isWpmlActive() in JS).
	 */
	public static function injectAppData( array $data ): array {
		$data['wpml'] = [
			'defaultLang'        => pll_default_language(),
			'languages'          => self::mapLanguages(),
			'translateableTypes' => self::getTranslatablePostTypes(),
			'advanced_builder'   => true,
		];

		return $data;
	}

	// ------------------------------------------------------------------
	// tco_routing_get/* → document list language field
	// ------------------------------------------------------------------

	/**
	 * Adds a `language` field to each document returned by CS's document-index
	 * routes. CS's Locator::transform_post() populates this field from the WPML
	 * service; when WPML is absent the field is an empty array. We fill it here
	 * at priority 20 (after CS's own handler at 10).
	 *
	 * @param mixed $result The document list array returned by CS's route handler.
	 * @param array $params The incoming request params.
	 */
	public static function enrichDocumentList( $result, array $params ) {
		if ( ! is_array( $result ) ) {
			return $result;
		}

		foreach ( $result as &$item ) {
			if ( empty( $item['language'] ) && ! empty( $item['id'] ) ) {
				$item['language'] = self::getPostLanguageData( (int) $item['id'] );
			}
		}

		return $result;
	}

	// ------------------------------------------------------------------
	// cs_document_builder_info → active document language field
	// ------------------------------------------------------------------

	/**
	 * Enriches the builder info for the document currently open in the editor.
	 * CS populates `language` from the WPML service; it is empty for Polylang
	 * sites. We provide the equivalent data so the language picker context
	 * (current language, available translations) is correct.
	 *
	 * @param array  $info The builder info array for the document.
	 * @param object $doc  The CS Document object.
	 */
	public static function enrichBuilderInfo( array $info, $doc ): array {
		if ( empty( $info['language'] ) ) {
			$info['language'] = self::getPostLanguageData( (int) $doc->id() );
		}

		return $info;
	}

	// ------------------------------------------------------------------
	// Language data helpers
	// ------------------------------------------------------------------

	/**
	 * Returns the Polylang language data for a post formatted to match the
	 * structure that CS's WPML integration provides on each document:
	 *
	 *   code         → current language slug
	 *   translations → { lang_slug: post_id, ... } for all linked translations
	 *   fallback     → language slugs that have no translation yet
	 *   source       → null (Polylang has no WPML-style TRID source concept)
	 *   domain       → home URL (Polylang uses path-based or query-based, not subdomain)
	 *
	 * When Polylang does not know the post's language (e.g. CS layout post types
	 * that are not registered in Polylang), we return a safe default so the CS
	 * builder JS language filter — which calls `fallback.includes(lang)` — does
	 * not crash on an undefined value. Returning an empty PHP array would
	 * JSON-encode to `[]` (JS array), making the {code, fallback} destructuring
	 * yield undefined for `fallback`. The safe default below encodes to a JS
	 * object with an explicit empty `fallback` array.
	 */
	public static function getPostLanguageData( int $postId ): array {
		$lang = pll_get_post_language( $postId, 'slug' );

		if ( ! $lang ) {
			// Return a typed placeholder so the JS filter can safely read
			// fallback.includes(selectedLang) → [].includes(...) → false.
			return [
				'code'         => null,
				'source'       => null,
				'fallback'     => [],
				'translations' => new \stdClass(), // {} not [] in JSON
				'domain'       => home_url( '/' ),
			];
		}

		// pll_get_post_translations always returns an associative array that
		// includes the post itself, so it is never empty for a known post.
		// PHP encodes non-empty associative arrays as JSON objects correctly.
		$translations = pll_get_post_translations( $postId );

		$allLangs = array_keys( pll_the_languages( [ 'raw' => 1, 'echo' => 0 ] ) );
		$fallback  = array_values( array_diff( $allLangs, array_keys( $translations ) ) );

		return [
			'code'         => $lang,
			'source'       => null,
			'fallback'     => $fallback,
			'translations' => $translations,
			'domain'       => home_url( '/' ),
		];
	}

	/**
	 * Maps Polylang language objects to the structure the CS builder JS reads
	 * from window.csAppData.wpml.languages:
	 *
	 *   translated_name  → language display name
	 *   country_flag_url → flag image URL (used in the language picker buttons)
	 *   url              → language home URL
	 *   code             → language slug
	 */
	private static function mapLanguages(): array {
		$mapped = [];

		foreach ( pll_the_languages( [ 'raw' => 1, 'echo' => 0 ] ) as $lang ) {
			$mapped[ $lang['slug'] ] = [
				'code'             => $lang['slug'],
				'translated_name'  => $lang['name'],
				'country_flag_url' => $lang['flag'],
				'url'              => $lang['url'] ?? home_url( '/' ),
			];
		}

		return $mapped;
	}

	/**
	 * Returns the post types registered in Polylang for translation.
	 * Falls back to ['page', 'post'] if the Polylang function is unavailable.
	 */
	private static function getTranslatablePostTypes(): array {
		if ( function_exists( 'pll_get_post_types' ) ) {
			$types = pll_get_post_types( [], false );
			if ( ! empty( $types ) ) {
				return array_values( array_unique( array_merge( [ 'page', 'post' ], $types ) ) );
			}
		}

		return [ 'page', 'post' ];
	}

	// ------------------------------------------------------------------
	// Guard
	// ------------------------------------------------------------------

	private static function isWpmlActive(): bool {
		return class_exists( 'SitePress' );
	}
}
