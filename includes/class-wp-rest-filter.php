<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://sk8.tech
 * @since      1.0.0
 *
 * @package    Wp_Rest_Filter
 * @subpackage Wp_Rest_Filter/includes
 */

add_action('rest_api_init', 'wp_rest_filter_add_filters');
/**
 * Add the necessary filter to each post type
 **/
function wp_rest_filter_add_filters() {
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
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Rest_Filter
 * @subpackage Wp_Rest_Filter/includes
 * @author     SK8Tech <support@sk8.tech>
 */
class Wp_Rest_Filter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Rest_Filter_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('PLUGIN_NAME_VERSION')) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-rest-filter';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Rest_Filter_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Rest_Filter_i18n. Defines internationalization functionality.
	 * - Wp_Rest_Filter_Admin. Defines all hooks for the admin area.
	 * - Wp_Rest_Filter_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-rest-filter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-wp-rest-filter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-wp-rest-filter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-wp-rest-filter-public.php';

		$this->loader = new Wp_Rest_Filter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Rest_Filter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Rest_Filter_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Rest_Filter_Admin($this->get_plugin_name(), $this->get_version());

		// Adds REST API Route for Process Payment
		// $this->loader->add_action('rest_api_init', $plugin_admin, 'add_api_routes');

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Rest_Filter_Public($this->get_plugin_name(), $this->get_version());

		// Adds REST API Route for Process Payment
		// $this->loader->add_action('rest_api_init', $plugin_public, 'add_api_routes');

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

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
	 * @return    Wp_Rest_Filter_Loader    Orchestrates the hooks of the plugin.
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

}
