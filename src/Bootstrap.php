<?php

namespace PolylangTcoPro;

class Bootstrap {

	public static function init(): void {
		if ( ! self::checkWpVersion() || ! self::checkDependencies() ) {
			return;
		}

		self::initUpdateChecker();
		Plugin::run();
	}

	private static function initUpdateChecker(): void {
		$checker = POLYLANG_TCOPRO_BASEPATH . 'src/UpdateChecker.php';
		if ( file_exists( $checker ) ) {
			require_once $checker;
		}
	}

	// ------------------------------------------------------------------
	// Checks
	// ------------------------------------------------------------------

	private static function checkWpVersion(): bool {
		global $wp_version;

		if ( version_compare( $wp_version, POLYLANG_TCOPRO_MIN_WP, '>=' ) ) {
			return true;
		}

		add_action( 'admin_notices', [ self::class, 'noticeWpVersion' ] );
		return false;
	}

	private static function checkDependencies(): bool {
		$theme    = wp_get_theme();
		$parent   = $theme->parent() ?: $theme;

		$is_pro = in_array( 'pro', [
			strtolower( $parent->get( 'Name' ) ),
			strtolower( $parent->get_template() ),
			strtolower( $parent->get_stylesheet() ),
		], true );

		$has_polylang = (
			is_plugin_active( 'polylang/polylang.php' ) ||
			is_plugin_active( 'polylang-pro/polylang.php' )
		);

		if ( $is_pro && $has_polylang ) {
			return true;
		}

		if ( ! $is_pro ) {
			add_action( 'admin_notices', [ self::class, 'noticeProTheme' ] );
		}

		if ( ! $has_polylang ) {
			add_action( 'admin_notices', [ self::class, 'noticePolylang' ] );
		}

		deactivate_plugins( POLYLANG_TCOPRO_BASENAME );

		if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- unsetting a redirect param, not processing form data
			unset( $_GET['activate'] );
		}

		return false;
	}

	// ------------------------------------------------------------------
	// Admin notices
	// ------------------------------------------------------------------

	public static function noticeWpVersion(): void {
		?>
		<div class="notice notice-error">
			<p><strong><?php
				printf(
					/* translators: %s is the minimum WP version required. */
					esc_html__( 'Sorry, Cornerstone Builder Integration for Polylang requires WordPress %s or higher.', 'polylang-tcopro' ),
					esc_html( POLYLANG_TCOPRO_MIN_WP )
				);
			?></strong></p>
		</div>
		<?php
	}

	public static function noticeProTheme(): void {
		$theme  = wp_get_theme();
		$parent = $theme->parent() ?: $theme;
		?>
		<div class="notice notice-error">
			<p>
				<strong><?php esc_html_e( 'Pro Theme must be installed and enabled in order to activate this plugin.', 'polylang-tcopro' ); ?></strong>
				<pre><code><?php
				/* translators: 1: theme name, 2: template slug, 3: stylesheet slug */
				echo sprintf(
					esc_html__( 'Theme name: %1$s | Template: %2$s | Stylesheet: %3$s', 'polylang-tcopro' ),
					esc_html( $parent->get( 'Name' ) ),
					esc_html( $parent->get_template() ),
					esc_html( $parent->get_stylesheet() )
				); ?></code></pre>
			</p>
		</div>
		<?php
	}

	public static function noticePolylang(): void {
		?>
		<div class="notice notice-error">
			<p><strong><?php esc_html_e( 'Polylang or Polylang Pro must be installed and enabled in order to activate this plugin.', 'polylang-tcopro' ); ?></strong></p>
		</div>
		<?php
	}
}
