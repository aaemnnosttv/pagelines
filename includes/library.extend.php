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

	if ( file_exists( PL_EXTEND_STYLE_PATH ) ){
		
		$cache_ver = pl_cache_version( PL_EXTEND_STYLE_PATH ); 
		
		pagelines_load_css('pl-extend-style', PL_EXTEND_STYLE, $cache_ver );
		
	}	
		
}

add_action( 'init', 'load_child_functions' );
function load_child_functions() {
	if ( file_exists( PL_EXTEND_FUNCTIONS ) )
		require_once( PL_EXTEND_FUNCTIONS );
}

add_action( 'init', 'base_check_templates' );
function base_check_templates() {

	foreach ( glob( PL_EXTEND_DIR . "/*.php") as $file) {

		if ( preg_match( '/page\.([a-z-0-9]+)\.php/', $file, $match ) ) {

			if ( !file_exists( trailingslashit( PL_EXTEND_DIR ) . $file ) ) 
				copy( $file, trailingslashit( STYLESHEETPATH ) . basename( $file ) );

			if ( file_exists( trailingslashit( STYLESHEETPATH ) . basename( $file ) ) ) {
					$data = get_file_data( trailingslashit( STYLESHEETPATH ) . basename( $file ), array( 'name' => 'Template Name' ) );
					pagelines_add_page( $match[1], $data['name'] );
			}

		}
	}
}

