<?php
/**
 * Define the integration functionality
 *
 * Loads and defines the integration methods and hooks for this plugin
 *
 * @link       https://github.com/ControlZetaDigital/polylang-tcopro
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
		$this->version = $version;
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
            $language['default_lang'] = ($this->default_language() === $language['slug']) ? true : false;
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
            "title" => __( 'Headers', 'polylang-tcopro' ),
            "slug" => "headers",
            "items" => $this->get_headers()
        ];

        $widgets[] = (object) [
            "title" => __( 'Footers', 'polylang-tcopro' ),
            "slug" => "footers",
            "items" => $this->get_footers()
        ];

        $widgets[] = (object) [
            "title" => __( 'Archive layouts', 'polylang-tcopro' ),
            "slug" => "archive_layouts",
            "items" => $this->get_layouts("archive")
        ];

        $widgets[] = (object) [
            "title" => __( 'Single layouts', 'polylang-tcopro' ),
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
	 * Get the languages assigned to a header, footer or layout item
	 *
	 * @since    1.1.0
     * @param    integer    $item_id       The item ID
     * @return   mixed      Languages object or false
	 */
    public function get_item_languages( $item_id ) {

        $languages = get_post_meta($item_id, "polylang_tcopro_language_assignments", true);

        if ($languages) {
            return (object) [
                'list' => explode('|', $languages),
                'value' => $languages
            ];
        } else {
            return false;
        }
    }

    /**
	 * Helper function to sort an array of items by priority and ID
	 *
	 * @since    1.1.0
	 */
    protected function get_priority_items($a, $b) {
        if ($a->priority == $b->priority) {
            return $a->ID - $b->ID;
        }
        return $a->priority - $b->priority;
    }

    /**
	 * Get the matched item ID using cornerstone rule matching and language assignments
	 *
	 * @since    1.1.0
     * @param    array      $items       Headers, footers or layout items
     * @return   integer    The matched item ID
	 */
    public function get_matched_item( $items ) {
        $matcher = cornerstone('RuleMatching');
        $matches = [];
        $current_lang = pll_current_language();

        foreach ($items as $item) {
            $assignments = [];
            $item_languages = $this->get_item_languages($item->ID);
            foreach($item->assignments as $assignment) {
                $assignments[] = (array) $assignment;
            }
            if ($matcher->match( $assignments ) && $item_languages && in_array($current_lang, $item_languages->list) ) {
                $matches[] = $item;
            }
        }

        if (count($matches) > 1) {
            usort($matches, [$this, 'get_priority_items']);
        }

        return ($matches) ? $matches[0]->ID : null;
    }

    /**
	 * This function hooks into the 'cs_match_header_assignment' filter and returns 
     * the header's ID assigned to the current language.
	 *
	 * @since    1.0.0
	 */
    public function header_assignment( $match ) {

        if (is_admin())
            return $match;

        $headers = $this->get_headers();
        $match = $this->get_matched_item( $headers );

        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_footer_assignment' filter and returns 
     * the footer's ID assigned to the current language.
	 *
	 * @since    1.0.0
	 */
    public function footer_assignment( $match ) {

        if (is_admin())
            return $match;

        $footers = $this->get_footers();
        $match = $this->get_matched_item( $footers );
    
        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_layout_archive_assignment' filter and returns 
     * the archive layout's ID assigned to the current language.
	 *
	 * @since    1.0.1
	 */
    public function layout_archive_assignment( $match ) {

        if (is_admin())
            return $match;

        $archives = $this->get_layouts("archive");
        $match = $this->get_matched_item( $archives );
    
        return $match;
    }

    /**
	 * This function hooks into the 'cs_match_layout_single_assignment' filter and returns 
     * the single layout's ID assigned to the current language.
	 *
	 * @since    1.0.1
	 */
    public function layout_single_assignment( $match ) {

        if (is_admin())
            return $match;

        $singles = $this->get_layouts("single");
        $match = $this->get_matched_item( $singles );
    
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
	 * Function that updates the languages assigned to each cornerstone element by the user.
	 *
	 * @since    1.0.0
	 */
    public function update() {
        if ( ! isset( $_POST["{$this->plugin_name}_update_nonce"] ) || ! wp_verify_nonce( $_POST["{$this->plugin_name}_update_nonce"], "{$this->plugin_name}_update" ) )
            return;

        $widgets = $this->get_widgets();

        foreach($widgets as $widget) {
            foreach($widget->items as $item) {
                $field_name = "item_{$item->ID}_languages";
                if (isset($_POST[$field_name])) {
                    update_post_meta($item->ID, "polylang_tcopro_language_assignments", $_POST[$field_name]);
                }
            }
        }
    }
}
