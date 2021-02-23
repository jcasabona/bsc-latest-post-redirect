<?php
/**
 * Plugin Name:      Latest Post Redirect
 * Plugin URI:       https://github.com/jcasabona/bsc-latest-post-redirect
 * Description:      A plugin that creates a redirect called /latest and sends users to the most recently published blog post, with an option to also send to latest modified. 
 * Version:           1.0.0
 * Author:            Joe Casabona
 * Author URI:     https:/casabona.org/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** 
 * Get latest post permalink
 * @return String $url
**/
function bsc_get_latest_permalink() {
	$args = array( 
		'numberposts' => '1',
	);
	
	$latest_post = get_posts( $args );
	$latest_id = wp_list_pluck( $latest_post, 'ID' );
	return get_permalink( $latest_id[0] );
}

// Create redirect using permalink
function bsc_create_latest_redirect() {
	$current_url = home_url($_SERVER['REQUEST_URI']);

	if( strstr( $current_url, 'latest' ) ) {
		wp_redirect( bsc_get_latest_permalink() );
		exit;
	}
}
add_action( 'init', 'bsc_create_latest_redirect' );

// On post publish, re-create permalink
