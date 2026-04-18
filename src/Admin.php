<?php

namespace PolylangTcoPro;

class Admin {

	private const SETTINGS_HOOK = 'languages_page_polylang_tcopro_settings';

	private function assetVersion(): string {
		return ( POLYLANG_TCOPRO_ENV === 'dev' ) ? (string) time() : POLYLANG_TCOPRO_VERSION;
	}

	public function enqueueStyles( string $hook ): void {
		if ( self::SETTINGS_HOOK !== $hook ) {
			return;
		}

		wp_enqueue_style(
			POLYLANG_TCOPRO_NAME . '-css',
			POLYLANG_TCOPRO_BASEURL . 'admin/css/polylang-tcopro-admin.css',
			[],
			$this->assetVersion(),
			'all'
		);
	}

	public function enqueueScripts( string $hook ): void {
		if ( self::SETTINGS_HOOK !== $hook ) {
			return;
		}

		wp_register_script(
			POLYLANG_TCOPRO_NAME . '-js',
			POLYLANG_TCOPRO_BASEURL . 'admin/js/polylang-tcopro-admin.js',
			[ 'jquery' ],
			$this->assetVersion(),
			true
		);

		wp_localize_script( POLYLANG_TCOPRO_NAME . '-js', 'plytco', [
			'pluginName' => POLYLANG_TCOPRO_NAME,
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
		] );
	}

	public function adminMenu(): void {
		add_submenu_page(
			'mlang',
			__( 'Pro Theme Support', 'polylang-tcopro' ),
			__( 'Pro Theme Support', 'polylang-tcopro' ),
			'manage_options',
			'polylang_tcopro_settings',
			[ $this, 'settingsPage' ]
		);

		if ( POLYLANG_TCOPRO_ENV === 'dev' ) {
			add_submenu_page(
				'mlang',
				__( 'Pro Theme Debug', 'polylang-tcopro' ),
				__( 'Pro Theme Debug', 'polylang-tcopro' ),
				'manage_options',
				'polylang_tcopro_debug',
				[ $this, 'debugPage' ]
			);
		}
	}

	public function settingsPage(): void {
		$ptco = new Integration();

		if ( ! empty( $_POST ) ) {
			$ptco->update();
		}

		$widgets   = $ptco->getWidgets();
		$languages = $ptco->getLanguages();

		wp_enqueue_script( POLYLANG_TCOPRO_NAME . '-js' );

		include POLYLANG_TCOPRO_BASEPATH . 'admin/views/settings.php';
	}

	public function debugPage(): void {
		$ptco = new Integration();
		include POLYLANG_TCOPRO_BASEPATH . 'admin/views/debug.php';
	}
}
