<?php
/**
 * Plugin Name:       Latest Post Redirect
 * Plugin URI:        https://github.com/jcasabona/bsc-latest-post-redirect
 * Description:       Creates a /latest redirect that sends users to the most recently published (or modified) blog post.
 * Version:           1.1.0
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
 * Get the permalink of the latest post.
 *
 * Orderby can be filtered via `bsc_latest_orderby` ('date' or 'modified').
 *
 * @return string
 */
function bsc_get_latest_permalink() {
	$orderby = apply_filters( 'bsc_latest_orderby', 'date' );

	$query = new WP_Query( array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'orderby'                => $orderby,
		'order'                  => 'DESC',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'                 => 'ids',
	) );

	if ( empty( $query->posts ) ) {
		return home_url( '/' );
	}

	return get_permalink( $query->posts[0] );
}

/**
 * Redirect /latest to the most recent post.
 */
function bsc_create_latest_redirect() {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron()
		|| ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( empty( $_SERVER['REQUEST_URI'] ) ) {
		return;
	}

	$path = wp_parse_url(
		esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
		PHP_URL_PATH
	);
	$path = trim( (string) $path, '/' );

	if ( 'latest' !== $path ) {
		return;
	}

	nocache_headers();
	wp_safe_redirect( bsc_get_latest_permalink(), 302 );
	exit;
}
add_action( 'template_redirect', 'bsc_create_latest_redirect' );