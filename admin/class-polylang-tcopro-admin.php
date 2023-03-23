<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rtcamp.com/nginx-helper/
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/admin
 * @author     ControlZetaDigital
 */
class Polylang_Tcopro_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Various settings tabs.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $settings_tabs    Various settings tabs.
	 */
	private $settings_tabs;

	/**
	 * Purge options.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $options    Purge options.
	 */
	public $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->options = $this->settings();
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_styles( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Polylang_Tcopro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Polylang_Tcopro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( 'settings_polylang_tcopro' !== $hook ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/polylang-tcopro-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 *
	 * @param string $hook The current admin page.
	 */
	public function enqueue_scripts( $hook ) {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Nginx_Helper_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Nginx_Helper_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( 'settings_polylang_tcopro' !== $hook ) {
			return;
		}

		//wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/nginx-helper-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add admin menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {

		add_submenu_page(
			'cornerstone',
			__( 'Polylang Support', 'polylang-tcopro' ),
			__( 'Polylang Support', 'polylang-tcopro' ),
			'manage_options',
			'polylang-tcopro',
			array( &$this, 'setting_page' )
		);

	}

	/**
	 * Display settings.
	 *
	 * @global $string $pagenow Contain current admin page.
	 *
	 * @since    1.0.0
	 */
	public function setting_page() {
		include plugin_dir_path( __FILE__ ) . 'partials/polylang-tcopro-admin-display.php';
	}

	/**
	 * Get settings.
	 *
	 * @since    1.0.0
	 */
	public function settings() {

		$data = get_site_option('rt_wp_polylang_tcopro_options');

		return $data;

	}

}
