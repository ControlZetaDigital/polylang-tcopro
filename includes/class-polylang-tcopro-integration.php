<?php
/**
 * Define the integration functionality
 *
 * Loads and defines the integration methods and hooks for this plugin
 *
 * @link       https://rtcamp.com/nginx-helper/
 * @since      1.0.0
 *
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the integration methods and hooks for this plugin
 *
 * @since      1.0.0
 * @package    polylang-tcopro
 * @subpackage polylang-tcopro/includes
 * @author     ControlZetaDigital
 */
class Polylang_Tcopro_Integration {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

    /**
	 * Get the list of languages
	 *
	 * @since    1.0.0
     * @return   array  $language_list      Array of languages provided by pll_the_languages function
	 */
	public function get_languages() {
        $languages = pll_the_languages( ['echo' => 0, 'raw' => 1] );

        $language_list = [];
        foreach($languages as $language) {
            $language_list[] = $language;
        }

        return $language_list;
    }

    /**
	 * Return the default language
	 *
	 * @since    1.0.1
	 */
    public function default_language() {
        return pll_default_language();
    }

    /**
	 * Get the list of widgets (Cornerstone layout types of elements)
	 *
	 * @since    1.0.0
     * @return   array  $widgets      Array of integrated CS elements
	 */
    public function get_widgets() {
        $widgets = [];

        $widgets[] = (object) [
            "title" => __( 'Headers',  '__cz__' ),
            "slug" => "headers",
            "items" => $this->get_headers()
        ];

        $widgets[] = (object) [
            "title" => __( 'Footers',  '__cz__' ),
            "slug" => "footers",
            "items" => $this->get_footers()
        ];

        $widgets[] = (object) [
            "title" => __( 'Archive layouts',  '__cz__' ),
            "slug" => "archive_layouts",
            "items" => $this->get_layouts("archive")
        ];

        $widgets[] = (object) [
            "title" => __( 'Single layouts',  '__cz__' ),
            "slug" => "single_layouts",
            "items" => $this->get_layouts("single")
        ];

        return $widgets;
    }

    /**
	 * Get the header elements
	 *
	 * @since    1.0.0
     * @return   array  $headers      Array of headers objects
	 */
    public function get_headers() {
        $headers = $this->get_items("cs_header");

        return $headers;
    }

    /**
	 * Get the footers elements
	 *
	 * @since    1.0.0
     * @return   array  $footers      Array of footers objects
	 */
    public function get_footers() {
        $footers = $this->get_items("cs_footer");

        return $footers;
    }

    /**
	 * Get the layouts elements
	 *
	 * @since    1.0.0
     * @return   array  $layouts      Array of layouts objects
	 */
    public function get_layouts( $type = "single" ) {
        $layouts = $this->get_items("cs_layout_{$type}");

        return $layouts;
    }

    /**
	 * Get an array of objects by type
	 *
	 * @since    1.0.0
     * @param    string     $type       String with the post_type of the elements
     * @return   array      $items      Array of objects
	 */
    private function get_items($type) {
        global $wpdb;

        $item_list = $wpdb->get_results( 
            $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = '{$type}'")
        );

        $items = [];
        foreach($item_list as $item) {
            $data = json_decode($item->post_content);
            $items[] = (object) [
                "ID" => $item->ID,
                "title" => $item->post_title,
                "assignments" => $data->settings->assignments,
                "priority" => $data->settings->assignment_priority,
                "settings" => $data->settings
            ];
        }

        return $items;
    }

    /**
	 * This function hooks into the 'cs_match_header_assignment' filter and returns 
     * the header's ID assigned to the current language.
	 *
	 * @since    1.0.0
	 */
    public function header_assignment( $match ) {

        $languages = $this->get_languages();

        foreach($languages as $lang) {
            if ($lang['slug'] === pll_current_language()) {
                $header_assigned = get_option("{$this->plugin_name}_headers_" . $lang['slug']);
                if ($header_assigned && $header_assigned != 0) {
                    $match = $header_assigned;
                    break;
                }
            }
        }
    
        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_footer_assignment' filter and returns 
     * the footer's ID assigned to the current language.
	 *
	 * @since    1.0.0
	 */
    public function footer_assignment( $match ) {

        $languages = $this->get_languages();

        foreach($languages as $lang) {
            if ($lang['slug'] === pll_current_language()) {
                $footer_assigned = get_option("{$this->plugin_name}_footers_" . $lang['slug']);
                if ($footer_assigned && $footer_assigned != 0) {
                    $match = $footer_assigned;
                    break;
                }
            }
        }
    
        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_layout_archive_assignment' filter and returns 
     * the archive layout's ID assigned to the current language.
	 *
	 * @since    1.0.1
	 */
    public function layout_archive_assignment( $match ) {

        $languages = $this->get_languages();

        foreach($languages as $lang) {
            if ($lang['slug'] === pll_current_language()) {
                $layout_assigned = get_option("{$this->plugin_name}_archive_layouts_" . $lang['slug']);
                if ($layout_assigned && $layout_assigned != 0) {
                    $match = $layout_assigned;
                    break;
                }
            }
        }
    
        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_layout_single_assignment' filter and returns 
     * the single layout's ID assigned to the current language.
	 *
	 * @since    1.0.1
	 */
    public function layout_single_assignment( $match ) {

        $languages = $this->get_languages();

        foreach($languages as $lang) {
            if ($lang['slug'] === pll_current_language()) {
                $layout_assigned = get_option("{$this->plugin_name}_single_layouts_" . $lang['slug']);
                if ($layout_assigned && $layout_assigned != 0) {
                    $match = $layout_assigned;
                    break;
                }
            }
        }
    
        return $match;
    }

    /**
	 * Custom CS provider function that returns the list of languages and the 
     * current language for use in Cornerstone.
	 *
	 * @since    1.0.0
	 */
    public function languages_provider( $results ) {

        $results = [
            "current" => pll_current_language( "slug" ),
            "languages" => $this->get_languages()
        ];

        return $results;
    }

    /**
	 * Function that updates and saves the values of elements assigned to the languages.
	 *
	 * @since    1.0.0
	 */
    public function update() {
        if ( ! isset( $_POST["{$this->plugin_name}_update_nonce"] ) || ! wp_verify_nonce( $_POST["{$this->plugin_name}_update_nonce"], "{$this->plugin_name}_update" ) )
            return;

        $widgets = $this->get_widgets();
        $languages = $this->get_languages();

        foreach($widgets as $widget) {
            foreach($languages as $lang) {
                $field_name = $widget->slug . "_" . $lang['slug'];
                if (isset($_POST[$field_name]) && $_POST[$field_name] !== "0") {
                    update_option("{$this->plugin_name}_{$field_name}", $_POST[$field_name]);
                }
            }
        }
    }

}
