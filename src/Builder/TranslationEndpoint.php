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

		if ( isset( $_REQUEST['cs-translation'] ) ) {
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

		do_action( 'cornerstone_before_custom_endpoint' );

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

		$nonce = $body['_nonce'] ?? ( $_REQUEST['_nonce'] ?? null );

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
	 *   source   (int)  — source post ID
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

		// Return the existing translation if already created.
		$existing = pll_get_post( $sourceId, $targetLang );
		if ( $existing ) {
			return [ 'id' => $existing ];
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
			throw new \Exception( $newId->get_error_message() );
		}

		// Assign language and link to the translation group.
		pll_set_post_language( $newId, $targetLang );

		$group                = pll_get_post_translations( $sourceId );
		$group[ $targetLang ] = $newId;
		pll_save_post_translations( $group );

		// Duplicate CS content (post_content is the JSON element tree).
		if ( $copyFrom ) {
			$origin = get_post( $copyFrom );
			if ( $origin instanceof \WP_Post && $origin->post_content ) {
				wp_update_post( [
					'ID'           => $newId,
					'post_content' => $origin->post_content,
				] );

				// Copy Cornerstone-specific meta.
				foreach ( get_post_meta( $origin->ID ) as $key => $values ) {
					if ( str_starts_with( $key, '_cornerstone' ) ) {
						foreach ( $values as $value ) {
							update_post_meta( $newId, $key, maybe_unserialize( $value ) );
						}
					}
				}
			}
		}

		return [ 'id' => $newId ];
	}

	// ------------------------------------------------------------------
	// Guard
	// ------------------------------------------------------------------

	private static function isWpmlActive(): bool {
		return class_exists( 'SitePress' );
	}
}
