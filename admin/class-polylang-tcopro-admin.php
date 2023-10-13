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

		if ( 'languages_page_polylang_tcopro_settings' !== $hook ) {
			return;
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/polylang-tcopro-admin.css', array(), ((POLYLANG_TCOPRO_ENV === 'dev') ? time() : $this->version), 'all' ); //$this->version

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

		if ( 'languages_page_polylang_tcopro_settings' !== $hook ) {
			return;
		}

	}

	/**
	 * Add admin menu.
	 *
	 * @since    1.0.0
	 */
	public function admin_menu() {

		add_submenu_page(
			'mlang',
			__( 'Pro Theme Support', 'polylang-tcopro' ),
			__( 'Pro Theme Support', 'polylang-tcopro' ),
			'manage_options',
			'polylang_tcopro_settings',
			array( &$this, 'setting_page' )
		);

		if (POLYLANG_TCOPRO_ENV === 'dev') {
			add_submenu_page(
				'mlang',
				__( 'Pro Theme Debug', 'polylang-tcopro' ),
				__( 'Pro Theme Debug', 'polylang-tcopro' ),
				'manage_options',
				'polylang_tcopro_debug',
				array( &$this, 'debug_page' )
			);
		}

	}

	/**
	 * Display settings.
	 *
	 * @since    1.0.0
	 */
	public function setting_page() {
		
		$ptco = new Polylang_Tcopro_Integration( $this->plugin_name, $this->version );

		if ( isset( $_POST ) )
            $ptco->update();

        $widgets = $ptco->get_widgets();
        $languages = $ptco->get_languages();

		include plugin_dir_path( __FILE__ ) . 'views/polylang-tcopro-admin-display.php';
	}

	/**
	 * Display debug.
	 *
	 * @since    1.0.0
	 */
	public function debug_page() {
		
		$ptco = new Polylang_Tcopro_Integration( $this->plugin_name, $this->version );

		include plugin_dir_path( __FILE__ ) . 'views/polylang-tcopro-debug-display.php';
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
