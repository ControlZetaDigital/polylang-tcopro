<?php
/**
 * Plugin Name:       Polylang for Tco Pro
 * Plugin URI:        https://controlzetadigital.com
 * Description:       Integrates Polylang with Theme.co Pro theme headers, footers and layouts
 * Version:           1.0.0
 * Author:            ControlZeta
 * Author URI:        https://controlzetadigital.com
 * Text Domain:       polylang-tcopro
 * Domain Path:       /languages
 * Requires at least: 3.3
 * Tested up to:      6.1
 *
 * @link              https://controlzetadigital.com
 * @since             1.0.0
 * @package           polylang-tcopro
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Base URL of plugin
 */
if ( ! defined( 'POLYLANG_TCOPRO_BASEURL' ) ) {
	define( 'POLYLANG_TCOPRO_BASEURL', plugin_dir_url( __FILE__ ) );
}

/**
 * Base Name of plugin
 */
if ( ! defined( 'POLYLANG_TCOPRO_BASENAME' ) ) {
	define( 'POLYLANG_TCOPRO_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Base PATH of plugin
 */
if ( ! defined( 'POLYLANG_TCOPRO_BASEPATH' ) ) {
	define( 'POLYLANG_TCOPRO_BASEPATH', plugin_dir_path( __FILE__ ) );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-polylang-tcopro-activator.php
 */
function activate_polylang_tcopro() {
	require_once POLYLANG_TCOPRO_BASEPATH . 'includes/class-polylang-tcopro-activator.php';
	Polylang_Tcopro_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-polylang-tcopro-deactivator.php
 */
function deactivate_polylang_tcopro() {
	require_once POLYLANG_TCOPRO_BASEPATH . 'includes/class-polylang-tcopro-deactivator.php';
	Polylang_Tcopro_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_polylang_tcopro' );
register_deactivation_hook( __FILE__, 'deactivate_polylang_tcopro' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require POLYLANG_TCOPRO_BASEPATH . 'includes/class-polylang-tcopro.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_polylang_tcopro() {

	global $polylang_tcopro;

	$polylang_tcopro = new Polylang_Tcopro();
	$polylang_tcopro->run();

}
run_polylang_tcopro();