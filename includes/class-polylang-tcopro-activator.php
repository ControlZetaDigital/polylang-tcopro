<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @link       https://rtcamp.com/nginx-helper/
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/includes
 *
 * @author     ControlZeta
 */

/**
 * Class Polylang_Tcopro_Activator
 */
class Polylang_Tcopro_Activator {

	/**
	 * Create log directory. Add capability of nginx helper.
	 * Schedule event to check log file size daily.
	 *
	 * @since    1.0.0
	 *
	 */
	public static function activate() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

	}

}
