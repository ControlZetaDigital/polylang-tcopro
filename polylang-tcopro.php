<?php
/**
 * Plugin Name:       Polylang for Tco Pro
 * Plugin URI:        https://github.com/ControlZetaDigital/polylang-tcopro
 * Description:       Integrates Polylang with Theme.co Pro theme headers, footers and layouts
 * Version:           1.3.1
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

define( 'POLYLANG_TCOPRO_NAME',     'polylang-tcopro' );
define( 'POLYLANG_TCOPRO_VERSION',  '1.3.1' );
define( 'POLYLANG_TCOPRO_MIN_WP',   '5.4' );
define( 'POLYLANG_TCOPRO_ENV',      'prod' );
define( 'POLYLANG_TCOPRO_BASEPATH', plugin_dir_path( __FILE__ ) );
define( 'POLYLANG_TCOPRO_BASEURL',  plugin_dir_url( __FILE__ ) );
define( 'POLYLANG_TCOPRO_BASENAME', plugin_basename( __FILE__ ) );

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

require_once ABSPATH . 'wp-admin/includes/plugin.php';

register_activation_hook( __FILE__, function(): void {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
} );

register_deactivation_hook( __FILE__, '__return_null' );

\PolylangTcoPro\Bootstrap::init();
