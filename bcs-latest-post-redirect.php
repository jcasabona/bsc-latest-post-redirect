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

	//@TODO: Option to sortby 'modified' date
	
	$latest_post = get_posts( $args );
	$latest_id = wp_list_pluck( $latest_post, 'ID' );
	return ( ! empty( $latest_id[0] ) ) ? get_permalink( $latest_id[0] ) : get_home_url(); 
}

// Create redirect using permalink
function bsc_create_latest_redirect() {
	$current_url = home_url( $_SERVER['REQUEST_URI'] );

	if( 1 === preg_match( '%latest(/*)%', $current_url ) ) {
		wp_redirect( bsc_get_latest_permalink() );
		exit;
	}
}
add_action( 'init', 'bsc_create_latest_redirect' );