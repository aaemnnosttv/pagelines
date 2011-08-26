<?php
/**
 * Functions and actions related to PageLines Extension
 * 
 * @since 2.0.b9
 */

/**
 * Load 'child' styles, functions and templates.
 */	
add_action( 'wp_head', 'load_child_style', 20 );
function load_child_style() {

	if ( !defined( 'PL_CUSTOMIZE' ) )
		return;
		
	if ( file_exists( PL_EXTEND_STYLE_PATH ) ){

		$cache_ver = '?ver=' . pl_cache_version( PL_EXTEND_STYLE_PATH ); 
		
		pagelines_draw_css( PL_EXTEND_STYLE . $cache_ver, 'pl-extend-style' );
		
	}	
		
}

add_action( 'init', 'load_child_functions' );
function load_child_functions() {
	if ( !defined( 'PL_CUSTOMIZE' ) )
		return;

	if ( file_exists( PL_EXTEND_FUNCTIONS ) )
		require_once( PL_EXTEND_FUNCTIONS );
}

add_action( 'init', 'base_check_templates' );
function base_check_templates() {
	
	if ( !defined( 'PL_CUSTOMIZE' ) )
		return;

	foreach ( glob( EXTEND_CHILD_DIR . "/*.php") as $file) {

		if ( preg_match( '/page\.([a-z-0-9]+)\.php/', $file, $match ) ) {

			if ( !file_exists( trailingslashit( EXTEND_CHILD_DIR ) . $file ) ) 
				copy( $file, trailingslashit( get_stylesheet_directory() ) . basename( $file ) );

			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . basename( $file ) ) ) {
					$data = get_file_data( trailingslashit( get_stylesheet_directory() ) . basename( $file ), array( 'name' => 'Template Name' ) );
					pagelines_add_page( $match[1], $data['name'] );
			}

		}
	}
}

