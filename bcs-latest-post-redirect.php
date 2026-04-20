<?php
/**
 * Plugin Name:       Latest Post Redirect
 * Plugin URI:        https://github.com/jcasabona/bsc-latest-post-redirect
 * Description:       Creates a /latest redirect that sends users to the most recently published (or modified) blog post. Supports /latest/{category} (e.g. /latest/articles, /latest/episodes).
 * Version:           1.2.0
 * Author:            Joe Casabona
 * Author URI:        https://casabona.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.8
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed category slugs for /latest/{category} redirects.
 *
 * Filterable via `bsc_latest_allowed_categories`.
 *
 * @return string[]
 */
function bsc_get_allowed_categories() {
	$defaults = array( 'articles', 'episodes' );
	$allowed  = apply_filters( 'bsc_latest_allowed_categories', $defaults );

	return array_values( array_filter( array_map( 'sanitize_title', (array) $allowed ) ) );
}

/**
 * Get the permalink of the latest post, optionally scoped to a category slug.
 *
 * Orderby can be filtered via `bsc_latest_orderby` ('date' or 'modified').
 *
 * @param string $category Category slug, or empty for all posts.
 * @return string
 */
function bsc_get_latest_permalink( $category = '' ) {
	$orderby = apply_filters( 'bsc_latest_orderby', 'date' );

	$args = array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'orderby'                => $orderby,
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'                 => 'ids',
	);

	if ( '' !== $category ) {
		$args['category_name'] = $category;
	}

	$query = new WP_Query( $args );

	if ( empty( $query->posts ) ) {
		return home_url( '/' );
	}

	return get_permalink( $query->posts[0] );
}

/**
 * Redirect /latest (and /latest/{category}) to the most recent matching post.
 */
function bsc_create_latest_redirect() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$request = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	$path    = trim( (string) wp_parse_url( $request, PHP_URL_PATH ), '/' );

	if ( 'latest' !== $path && 0 !== strpos( $path, 'latest/' ) ) {
		return;
	}

	$segments = explode( '/', $path );
	$category = isset( $segments[1] ) ? sanitize_title( $segments[1] ) : '';

	$query_args   = array();
	$query_string = (string) wp_parse_url( $request, PHP_URL_QUERY );
	if ( '' !== $query_string ) {
		parse_str( $query_string, $query_args );
	}

	if ( '' === $category && ! empty( $query_args['category'] ) ) {
		$category = sanitize_title( wp_unslash( $query_args['category'] ) );
	}

	if ( '' !== $category ) {
		$allowed = bsc_get_allowed_categories();
		if ( ! empty( $allowed ) && ! in_array( $category, $allowed, true ) ) {
			return;
		}
	}

	$destination = bsc_get_latest_permalink( $category );

	$utm_params = array();
	foreach ( $query_args as $key => $value ) {
		if ( is_string( $key ) && 0 === strpos( $key, 'utm_' ) && is_scalar( $value ) ) {
			$utm_params[ $key ] = sanitize_text_field( wp_unslash( (string) $value ) );
		}
	}

	if ( ! empty( $utm_params ) ) {
		$destination = add_query_arg( $utm_params, $destination );
	}

	nocache_headers();
	wp_safe_redirect( $destination, 302 );
	exit;
}
add_action( 'template_redirect', 'bsc_create_latest_redirect', 1 );
