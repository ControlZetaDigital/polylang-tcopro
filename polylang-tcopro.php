<?php
/**
 * Plugin Name:       Polylang for Tco Pro
 * Plugin URI:        https://github.com/ControlZetaDigital/polylang-tcopro
 * Description:       Integrates Polylang with Theme.co Pro theme headers, footers and layouts
 * Version:           1.1.1
 * Author:            ControlZeta
 * Author URI:        https://controlzetadigital.com
 * Text Domain:       polylang-tcopro
 * Domain Path:       /languages
 * Requires at least: 5.4
 * Tested up to:      6.1
 *
 * @link              https://github.com/ControlZetaDigital/polylang-tcopro
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

define('POLYLANG_TCOPRO_ENV', 'prod'); //dev/prod

/**
 * Dispay Polylang activation notice.
 * 
 * @since	1.0.0
 * 
 */
function pro_dependence_notice() {
	?>
	<div id="message" class="error">
		<p>
			<strong>
				<?php
				esc_html_e( 'Pro Theme must be installed and enabled in order to activate this plugin', 'polylang-tcopro' );
				?>
			</strong>
		</p>
	</div>
	<?php
}

/**
 * Dispay Theme Pro activation notice.
 * 
 * @since	1.0.0
 * 
 */
function polylang_dependence_notice() {
	?>
	<div id="message" class="error">
		<p>
			<strong>
				<?php
				esc_html_e( 'Polylang or Polylang Pro must be installed and enabled in order to activate this plugin', 'polylang-tcopro' );
				?>
			</strong>
		</p>
	</div>
	<?php
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

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
	if ( wp_get_theme()->template != 'pro' || 
		 ( !in_array( 'polylang/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) &&
		 	!in_array( 'polylang-pro/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
		) {
		if( wp_get_theme()->template != 'pro' )
			add_action( 'admin_notices', 'pro_dependence_notice' );
	
		if( !in_array( 'polylang/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && 
			!in_array( 'polylang-pro/polylang.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
			add_action( 'admin_notices', 'polylang_dependence_notice' );
			
		deactivate_plugins( plugin_basename( __FILE__ ) );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	} else {
		global $polylang_tcopro;
	
		$polylang_tcopro = new Polylang_Tcopro();
		$polylang_tcopro->run();
	}
}

run_polylang_tcopro();


