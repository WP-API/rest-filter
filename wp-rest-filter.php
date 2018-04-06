<?php
/**
 * Plugin Name: WP REST Filter
 * Description: Since WordPress 4.7 the `filter` argument for any post endpoint was removed. This plugin restores the `filter` parameter for websites that were previously using it.
 * Author: SK8Tech
 * Author URI: https://sk8.tech
 * Version: 1.1.6
 * License: GPL2+
 **/

add_action( 'rest_api_init', 'wp_rest_filter_add_filters' );

 /**
  * Add the necessary filter to each post type
  **/
function wp_rest_filter_add_filters() {
	foreach ( get_post_types( array( 'show_in_rest' => true ), 'objects' ) as $post_type ) {
		add_filter( 'rest_' . $post_type->name . '_query', 'wp_rest_filter_add_filter_param', 10, 2 );
	}
}

/**
 * Add the filter parameter
 *
 * @param  array           $args    The query arguments.
 * @param  WP_REST_Request $request Full details about the request.
 * @return array $args.
 **/
function wp_rest_filter_add_filter_param( $args, $request ) {
	// Bail out if no filter parameter is set.
	if ( empty( $request['filter'] ) || ! is_array( $request['filter'] ) ) {
		return $args;
	}

	$filter = $request['filter'];

	if ( isset( $filter['posts_per_page'] ) && ( (int) $filter['posts_per_page'] >= 1 && (int) $filter['posts_per_page'] <= 100 ) ) {
		$args['posts_per_page'] = $filter['posts_per_page'];
	}

	global $wp;
	$vars = apply_filters( 'rest_query_vars', $wp->public_query_vars );

	function allow_meta_query( $valid_vars )
	{
	    $valid_vars = array_merge( $valid_vars, array( 'meta_query', 'meta_key', 'meta_value', 'meta_compare' ) );
	    return $valid_vars;
	}
	$vars = allow_meta_query( $vars );
	
	foreach ( $vars as $var ) {
		if ( isset( $filter[ $var ] ) ) {
			$args[ $var ] = $filter[ $var ];
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
