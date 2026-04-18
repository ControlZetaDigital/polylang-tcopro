<?php
/**
 * Plugin Name:       Polylang for Tco Pro
 * Plugin URI:        https://github.com/ControlZetaDigital/polylang-tcopro
 * Description:       Integrates Polylang with Theme.co Pro theme headers, footers and layouts
 * Version:           1.2.0
 * Author:            ControlZeta
 * Author URI:        https://controlzetadigital.com
 * Text Domain:       polylang-tcopro
 * Domain Path:       /languages
 * Requires at least: 5.4
 * Tested up to:      6.9
 *
 * @link              https://github.com/ControlZetaDigital/polylang-tcopro
 * @since             1.0.0
 * @package           polylang-tcopro
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

// Constants
define( 'POLYLANG_TCOPRO_NAME',     'polylang-tcopro' );
define( 'POLYLANG_TCOPRO_VERSION',  '1.2.0' );
define( 'POLYLANG_TCOPRO_MIN_WP',   '5.4' );
define( 'POLYLANG_TCOPRO_ENV',      'prod' );
define( 'POLYLANG_TCOPRO_BASEPATH', plugin_dir_path( __FILE__ ) );
define( 'POLYLANG_TCOPRO_BASEURL',  plugin_dir_url( __FILE__ ) );
define( 'POLYLANG_TCOPRO_BASENAME', plugin_basename( __FILE__ ) );

// PSR-4 autoloader for PolylangTcoPro\ namespace → src/
spl_autoload_register( function( string $class ): void {
	$prefix = 'PolylangTcoPro\\';
	if ( strncmp( $prefix, $class, strlen( $prefix ) ) !== 0 ) {
		return;
	}
	$file = POLYLANG_TCOPRO_BASEPATH . 'src/' . str_replace( '\\', '/', substr( $class, strlen( $prefix ) ) ) . '.php';
	if ( file_exists( $file ) ) {
		require $file;
	}
} );

// Activation / deactivation hooks
register_activation_hook( __FILE__, function() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
} );

register_deactivation_hook( __FILE__, '__return_null' );

// Dependency checks
require_once ABSPATH . 'wp-admin/includes/plugin.php';

function polylang_tcopro_pro_notice(): void {
	$theme  = wp_get_theme();
	$parent = $theme->parent() ?: $theme;
	?>
	<div class="notice notice-error">
		<p>
			<strong><?php esc_html_e( 'Pro Theme must be installed and enabled in order to activate this plugin.', 'polylang-tcopro' ); ?></strong>
			<pre><code><?php
				echo sprintf(
					esc_html__( 'Theme name: %s | Template: %s | Stylesheet: %s', 'polylang-tcopro' ),
					esc_html( $parent->get( 'Name' ) ),
					esc_html( $parent->get_template() ),
					esc_html( $parent->get_stylesheet() )
				);
			?></code></pre>
		</p>
	</div>
	<?php
}

function polylang_tcopro_polylang_notice(): void {
	?>
	<div id="message" class="error">
		<p>
			<strong><?php esc_html_e( 'Polylang or Polylang Pro must be installed and enabled in order to activate this plugin.', 'polylang-tcopro' ); ?></strong>
		</p>
	</div>
	<?php
}

// Bootstrap
function polylang_tcopro_run(): void {
	global $wp_version;

	// WP version check
	if ( version_compare( $wp_version, POLYLANG_TCOPRO_MIN_WP, '<' ) ) {
		add_action( 'admin_notices', function() {
			?>
			<div id="message" class="error">
				<p>
					<strong><?php
						printf(
							/* translators: %s is Minimum WP version. */
							esc_html__( 'Sorry, Polylang for Tco Pro requires WordPress %s or higher', 'polylang-tcopro' ),
							esc_html( POLYLANG_TCOPRO_MIN_WP )
						);
					?></strong>
				</p>
			</div>
			<?php
		} );
		return;
	}

	// Theme and Polylang checks
	$theme    = wp_get_theme();
	$parent   = $theme->parent() ?: $theme;
	$is_pro   = in_array( 'pro', [
		strtolower( $parent->get( 'Name' ) ),
		strtolower( $parent->get_template() ),
		strtolower( $parent->get_stylesheet() ),
	], true );

	$has_polylang = (
		is_plugin_active( 'polylang/polylang.php' ) ||
		is_plugin_active( 'polylang-pro/polylang.php' )
	);

	if ( ! $is_pro || ! $has_polylang ) {
		if ( ! $is_pro ) {
			add_action( 'admin_notices', 'polylang_tcopro_pro_notice' );
		}
		if ( ! $has_polylang ) {
			add_action( 'admin_notices', 'polylang_tcopro_polylang_notice' );
		}
		deactivate_plugins( POLYLANG_TCOPRO_BASENAME );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		return;
	}

	\PolylangTcoPro\Plugin::run();
}

polylang_tcopro_run();
