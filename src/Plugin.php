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
		$admin       = new Admin();
		$integration = new Integration();

		// Admin
		add_action( 'admin_enqueue_scripts', [ $admin, 'enqueueStyles' ] );
		add_action( 'admin_enqueue_scripts', [ $admin, 'enqueueScripts' ] );
		add_action( 'admin_menu', [ $admin, 'adminMenu' ], 99 );

		// Cornerstone integration
		add_filter( 'cs_match_header_assignment',        [ $integration, 'headerAssignment' ] );
		add_filter( 'cs_match_footer_assignment',        [ $integration, 'footerAssignment' ] );
		add_filter( 'cs_match_layout-archive_assignment', [ $integration, 'layoutArchiveAssignment' ] );
		add_filter( 'cs_match_layout-single_assignment', [ $integration, 'layoutSingleAssignment' ] );
		add_filter( 'cs_looper_custom_languages',        [ $integration, 'languagesProvider' ] );
	}
}
