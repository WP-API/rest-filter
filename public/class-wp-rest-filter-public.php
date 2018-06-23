<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sk8.tech
 * @since      1.0.0
 *
 * @package    Wp_Rest_Filter
 * @subpackage Wp_Rest_Filter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_Rest_Filter
 * @subpackage Wp_Rest_Filter/public
 * @author     SK8Tech <support@sk8.tech>
 */
class Wp_Rest_Filter_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Add the endpoints to the API
	 */
	public function add_api_routes() {
		/**
		 * Handle Register User request.
		 */
		foreach (get_post_types(array('show_in_rest' => true), 'objects') as $post_type) {
			add_filter('rest_' . $post_type->name . '_query', 'wp_rest_filter_add_filter_param', 10, 2);
		}
		foreach (get_taxonomies(array('show_in_rest' => true), 'objects') as $tax_type) {
			add_filter('rest_' . $tax_type->name . '_query', 'wp_rest_filter_add_filter_param', 10, 2);
		}
	}

	/**
	 * Add the filter parameter
	 *
	 * @since    1.2.0
	 * @param  array           $args    The query arguments.
	 * @param  WP_REST_Request $request Full details about the request.
	 * @return array $args.
	 **/
	function wp_rest_filter_add_filter_param($args, $request) {
		// Bail out if no filter parameter is set.
		if (empty($request['filter']) || !is_array($request['filter'])) {
			return $args;
		}

		$filter = $request['filter'];

		if (isset($filter['posts_per_page']) && ((int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100)) {
			$args['posts_per_page'] = $filter['posts_per_page'];
		}

		global $wp;
		$vars = apply_filters('rest_query_vars', $wp->public_query_vars);

		function allow_meta_query($valid_vars) {
			$valid_vars = array_merge($valid_vars, array('meta_query', 'meta_key', 'meta_value', 'meta_compare'));
			return $valid_vars;
		}
		$vars = allow_meta_query($vars);

		foreach ($vars as $var) {
			if (isset($filter[$var])) {
				$args[$var] = $filter[$var];
			}
		}

		/**
		 * Added support for 'before' & 'after' filtering for Custom Field
		 * @author Jack
		 * @see [How to filter posts modified after specific date in Wordpress API v2](https://stackoverflow.com/questions/47053462/how-to-filter-posts-modified-after-specific-date-in-wordpress-api-v2)
		 * @todo Make it work...
		 */
		// if ( ( isset( $request['before'] ) || isset( $request['after'] ) ) && isset( $request['date_query_column'] ) ) {
		//        $args['date_query'][0]['column'] = $request['date_query_column'];
		// }

		return $args;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Rest_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Rest_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-rest-filter-public.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wp_Rest_Filter_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wp_Rest_Filter_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-rest-filter-public.js', array('jquery'), $this->version, false);

	}

}
