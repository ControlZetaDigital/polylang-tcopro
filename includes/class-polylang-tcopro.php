<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://rtcamp.com/nginx-helper/
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/includes
 * @author     ControlZeta
 */
class Polylang_Tcopro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Polylang_Tcopro_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Minimum WordPress Version Required.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $minium_wp
	 */
	protected $minimum_wp;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'polylang-tcopro';
		$this->version     = '1.0.0';
		$this->minimum_wp  = '3.3';

		if ( ! $this->required_wp_version() ) {
			return;
		}

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Polylang_Tcopro_Loader. Orchestrates the hooks of the plugin.
	 * - Polylang_Tcopro_i18n. Defines internationalization functionality.
	 * - Polylang_Tcopro_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-polylang-tcopro-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-polylang-tcopro-i18n.php';

		/**
		 * The class responsible for defining all integration actions between Polylang and Pro Theme.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-polylang-tcopro-integration.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-polylang-tcopro-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		$this->loader = new Polylang_Tcopro_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Nginx_Helper_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Polylang_Tcopro_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $polylang_tcopro_admin;

		$polylang_tcopro_admin = new Polylang_Tcopro_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $polylang_tcopro_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $polylang_tcopro_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $polylang_tcopro_admin, 'admin_menu', 99 );

		//Integration
		$polylang_tcopro_integration = new Polylang_Tcopro_Integration( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_filter( 'cs_match_header_assignment', $polylang_tcopro_integration, 'header_assignment' );
        $this->loader->add_filter( 'cs_match_footer_assignment', $polylang_tcopro_integration, 'footer_assignment' );
		$this->loader->add_filter( 'cs_match_layout-archive_assignment', $polylang_tcopro_integration, 'layout_archive_assignment' );
		$this->loader->add_filter( 'cs_match_layout-single_assignment', $polylang_tcopro_integration, 'layout_single_assignment' );
        $this->loader->add_filter( 'cs_looper_custom_languages', $polylang_tcopro_integration, 'languages_provider' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return Polylang_Tcopro_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Check wp version.
	 *
	 * @since     1.0.0
	 * @global string $wp_version
	 * @return boolean
	 */
	public function required_wp_version() {

		global $wp_version;

		$wp_ok = version_compare( $wp_version, $this->minimum_wp, '>=' );

		if ( false === $wp_ok ) {

			add_action( 'admin_notices', array( &$this, 'display_notices' ) );
			return false;

		}

		return true;

	}

	/**
	 * Dispay plugin notices.
	 */
	public function display_notices() {
		?>
	<div id="message" class="error">
		<p>
			<strong>
				<?php
				printf(
					/* translators: %s is Minimum WP version. */
					esc_html__( 'Sorry, Polylang for Tco Pro requires WordPress %s or higher', 'polylang-tcopro' ),
					esc_html( $this->minimum_wp )
				);
				?>
			</strong>
		</p>
	</div>
		<?php
	}
}
