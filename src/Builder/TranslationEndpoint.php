<?php

namespace PolylangTcoPro\Builder;

/**
 * Handles the `cs-translation` endpoint for Polylang-powered sites.
 *
 * CS ships a native `cs-translation` endpoint consumed by the builder's
 * language picker UI ("Create Translation" / "Copy From" flow). When WPML is
 * active, CS's own Wpml service registers and handles this endpoint. When WPML
 * is absent we register our own handler here so the builder can create Polylang
 * translations of pages/posts directly from the builder UI.
 *
 * The endpoint is registered only when the `cs-translation` request key is
 * present in $_REQUEST, matching how CS's Util\Endpoint class works internally.
 */
class TranslationEndpoint {

	// ------------------------------------------------------------------
	// Bootstrap
	// ------------------------------------------------------------------

	public static function setup(): void {
		if ( self::isWpmlActive() ) {
			return;
		}

		if ( isset( $_REQUEST['cs-translation'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- checking key existence only to decide whether to register the handler
			add_filter( 'pre_handle_404', '__return_true' );
			add_action( 'template_redirect', [ self::class, 'handleRequest' ], 0 );
		}
	}

	// ------------------------------------------------------------------
	// Request handler
	// ------------------------------------------------------------------

	/**
	 * Mirrors the structure of CS's Util\Endpoint::detect_request() so our
	 * handler integrates transparently with the builder's expectations.
	 */
	public static function handleRequest(): void {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		do_action( 'cornerstone_before_custom_endpoint' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- CS-owned hook

		send_origin_headers();
		header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();

		ob_start();

		if ( ! is_user_logged_in() || ! cs_permission_user_can_edit_anything() ) {
			wp_send_json_error( [ 'invalid_user' => true, 'message' => 'No logged in user.' ] );
			wp_die();
		}

		try {
			$input  = self::parseInput();
			$result = self::createTranslation( $input );
			wp_send_json_success( $result );
		} catch ( \Exception $e ) {
			wp_send_json_error( [ 'message' => $e->getMessage() ] );
		}

		wp_die();
	}

	// ------------------------------------------------------------------
	// Input parsing (matches Util\Endpoint::get_input())
	// ------------------------------------------------------------------

	/**
	 * Parses and validates the raw JSON request body sent by the CS builder.
	 * Verifies the Cornerstone nonce before returning the inner request payload.
	 *
	 * @throws \Exception On nonce failure or missing payload.
	 */
	private static function parseInput(): array {
		$rawBody = file_get_contents( 'php://input' );
		$body    = json_decode( $rawBody, true );

		$nonce = $body['_nonce'] ?? ( isset( $_REQUEST['_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ) : null );

		if ( ! wp_verify_nonce( $nonce, 'cornerstone_nonce' ) ) {
			throw new \Exception( 'Nonce verification failed.' );
		}

		if ( ! isset( $body['request'] ) ) {
			throw new \Exception( 'Missing request payload.' );
		}

		if ( ! empty( $body['gzip'] ) ) {
			$request = json_decode( gzdecode( base64_decode( $body['request'] ) ), true );
		} else {
			$request = is_array( $body['request'] ) ? $body['request'] : [];
		}

		return $request;
	}

	// ------------------------------------------------------------------
	// Translation creation
	// ------------------------------------------------------------------

	/**
	 * Creates a Polylang translation of the given source post in the target
	 * language. Optionally clones the CS content from another post.
	 *
	 * Expected $data keys:
	 *   source   (int)    — source post ID
	 *   lang     (string) — target Polylang language slug
	 *   copyFrom (int, optional) — post ID whose CS content to duplicate
	 *
	 * @param  array $data Parsed request payload from the builder.
	 * @return array       ['id' => int] with the new (or existing) post ID.
	 * @throws \Exception  On validation or creation failure.
	 */
	private static function createTranslation( array $data ): array {
		if ( empty( $data['source'] ) ) {
			throw new \Exception( 'Source post ID missing.' );
		}

		if ( empty( $data['lang'] ) ) {
			throw new \Exception( 'Target language missing.' );
		}

		$sourceId   = (int) $data['source'];
		$targetLang = sanitize_key( $data['lang'] );
		$copyFrom   = ! empty( $data['copyFrom'] ) ? (int) $data['copyFrom'] : null;

		$sourcePost = get_post( $sourceId );
		if ( ! $sourcePost instanceof \WP_Post ) {
			throw new \Exception( 'Source post not found.' );
		}

		// Return the existing translation if already created and not trashed.
		// CS deletes documents via wp_trash_post(), so a "deleted" translation
		// still exists in the DB with post_status = 'trash'. Treat it as absent
		// so the user can create a fresh one without first permanently deleting.
		$existing = pll_get_post( $sourceId, $targetLang );
		if ( $existing && get_post_status( $existing ) !== 'trash' ) {
			return [ 'id' => $existing ];
		}

		// Ensure the source post has a Polylang language. CS layout types are
		// not registered in Polylang's translation settings, so their language
		// may not have been set yet. Fall back to the plugin's legacy meta.
		$sourceLang = pll_get_post_language( $sourceId, 'slug' );
		if ( ! $sourceLang ) {
			$sourceLang = self::getLanguageFromLegacyMeta( $sourceId );
			if ( $sourceLang ) {
				pll_set_post_language( $sourceId, $sourceLang );
			}
		}

		// Create a new post in the target language.
		$newId = wp_insert_post(
			[
				'post_type'   => $sourcePost->post_type,
				'post_status' => $sourcePost->post_status,
				'post_title'  => $sourcePost->post_title,
			],
			true
		);

		if ( is_wp_error( $newId ) ) {
			throw new \Exception( $newId->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- message is JSON-encoded via wp_send_json_error, not rendered as HTML
		}

		// Assign language and link to the translation group.
		// Include the source itself in the group so both ends are connected.
		pll_set_post_language( $newId, $targetLang );

		$group = $sourceLang ? pll_get_post_translations( $sourceId ) : [];
		if ( $sourceLang && ! isset( $group[ $sourceLang ] ) ) {
			$group[ $sourceLang ] = $sourceId;
		}
		$group[ $targetLang ] = $newId;
		pll_save_post_translations( $group );

		// For CS layout types, mirror the language assignment to the plugin's
		// legacy meta so the frontend assignment system picks it up immediately,
		// without requiring a manual save via the admin UI.
		if ( self::isCsLayoutType( $sourcePost->post_type ) ) {
			update_post_meta( $newId, 'polylang_tcopro_language_assignments', $targetLang );
		}

		// Copy CS content via the native Document clone API (same approach as
		// CS's own WPML service). This is necessary because CS's save_post hooks
		// for cs_* post types override any raw wp_update_post write.
		//
		// For CS layout types the builder cannot distinguish "start blank" from
		// "copy" when the translation map is empty (both arrive with no copyFrom),
		// so we always copy from the source. Regular content types respect
		// copyFrom explicitly: if absent the new post starts blank.
		$effectiveCopyFrom = $copyFrom
			?? ( self::isCsLayoutType( $sourcePost->post_type ) ? $sourceId : null );

		if ( $effectiveCopyFrom ) {
			try {
				$doc = cornerstone( 'Resolver' )->getDocument( $newId );
				if ( $doc ) {
					$doc->update( [ 'clone' => $effectiveCopyFrom ] )->save();
				}
			} catch ( \Exception $e ) {
				$origin = get_post( $effectiveCopyFrom );
				if ( $origin instanceof \WP_Post && $origin->post_content ) {
					wp_update_post( [
						'ID'           => $newId,
						'post_content' => $origin->post_content,
					] );
				}
			}
		}

		return [ 'id' => $newId ];
	}

	// ------------------------------------------------------------------
	// Helpers
	// ------------------------------------------------------------------

	/**
	 * Returns the primary language slug from the plugin's legacy meta, or an
	 * empty string if the post has no meta or is not a CS layout type.
	 *
	 * The meta value is pipe-separated when multiple languages are assigned
	 * (e.g. "es|fr"); we take the first as the canonical language.
	 */
	private static function getLanguageFromLegacyMeta( int $postId ): string {
		if ( ! self::isCsLayoutType( get_post_type( $postId ) ?: '' ) ) {
			return '';
		}

		$meta = get_post_meta( $postId, 'polylang_tcopro_language_assignments', true );
		if ( ! $meta ) {
			return '';
		}

		return explode( '|', $meta )[0];
	}

	private static function isCsLayoutType( string $postType ): bool {
		return in_array( $postType, [
			'cs_header',
			'cs_footer',
			'cs_layout_single',
			'cs_layout_archive',
			'cs_layout_single_wc',
			'cs_layout_archive_wc',
		], true );
	}

	// ------------------------------------------------------------------
	// Guard
	// ------------------------------------------------------------------

	private static function isWpmlActive(): bool {
		return class_exists( 'SitePress' );
	}
}
