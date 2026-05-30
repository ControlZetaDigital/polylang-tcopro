<?php

namespace PolylangTcoPro;

class Plugin {

	public static function run(): void {
		self::loadTextdomain();
		self::registerHooks();
	}

	private static function loadTextdomain(): void {
		add_action( 'plugins_loaded', function() {
			load_plugin_textdomain(
				POLYLANG_TCOPRO_NAME,
				false,
				dirname( POLYLANG_TCOPRO_BASENAME ) . '/languages/'
			);
		} );
	}

	private static function registerHooks(): void {
		// If WPML is active, step aside completely. CS's native WPML service
		// handles both layout assignment and the builder UI. Running our hooks
		// alongside it would produce conflicts (cs_match_* returning null can
		// override a valid WPML-resolved assignment).
		if ( class_exists( 'SitePress' ) ) {
			return;
		}

		self::registerLegacyHooks();
		self::registerBuilderHooks();
	}

	// ------------------------------------------------------------------
	// Legacy hooks (layout assignment via post-meta, admin UI)
	// ------------------------------------------------------------------

	/**
	 * Registers the original layout-assignment and admin hooks.
	 *
	 * These remain active while the meta-based assignment system is the
	 * canonical mechanism for mapping CS layouts to Polylang languages.
	 * They are candidates for deprecation once in-builder assignment is
	 * implemented.
	 *
	 * @deprecated-candidate Superseded by Builder\Integration when in-builder
	 *                        layout assignment lands.
	 */
	private static function registerLegacyHooks(): void {
		$admin       = new Admin();
		$integration = new Integration();

		// Admin UI for language → layout assignment.
		add_action( 'admin_enqueue_scripts', [ $admin, 'enqueueStyles' ] );
		add_action( 'admin_enqueue_scripts', [ $admin, 'enqueueScripts' ] );
		add_action( 'admin_menu', [ $admin, 'adminMenu' ], 99 );

		// Cornerstone layout-matching filters.
		add_filter( 'cs_match_header_assignment',           [ $integration, 'headerAssignment' ] );
		add_filter( 'cs_match_footer_assignment',           [ $integration, 'footerAssignment' ] );
		add_filter( 'cs_match_layout-archive_assignment',   [ $integration, 'layoutArchiveAssignment' ] );
		add_filter( 'cs_match_layout-single_assignment',    [ $integration, 'layoutSingleAssignment' ] );
		add_filter( 'cs_match_layout-archive-wc_assignment', [ $integration, 'layoutArchiveWcAssignment' ] );
		add_filter( 'cs_match_layout-single-wc_assignment', [ $integration, 'layoutSingleWcAssignment' ] );

		// CS Looper provider: exposes current language and language list as
		// a dynamic data source inside the builder.
		add_filter( 'cs_looper_custom_languages', [ $integration, 'languagesProvider' ] );
	}

	// ------------------------------------------------------------------
	// Builder integration hooks (WPML-style language UI in CS builder)
	// ------------------------------------------------------------------

	private static function registerBuilderHooks(): void {
		Builder\Integration::setup();
		Builder\TranslationEndpoint::setup();
	}
}
