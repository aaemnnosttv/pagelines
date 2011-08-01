<?php

/*
	Global Used in bbPress Integration 
*/ 
global $pagelines_template;

// ===================================================================================================
// = Set up Section loading & create pagelines_template global in page (give access to conditionals) =
// ===================================================================================================



/**
 * Build PageLines Template Global (Singleton)
 * Must be built inside the page (wp_head) so conditionals can be used to identify the template
 * In the admin, the template doesn't need to be identified so its loaded in the init action
 * @global object $pagelines_template
 * @since 1.0.0
 */
add_action('pagelines_before_html', 'build_pagelines_template');

/**
 * Build the template in the admin... doesn't need to load in the page
 * @since 1.0.0
 */
add_action('admin_head', 'build_pagelines_template');

add_action('pagelines_before_html', 'build_pagelines_layout');
add_action('admin_head', 'build_pagelines_layout');

/**
 * Optionator
 * Does "just in time" loading of section option in meta; 
 * Will only load section options if the section is present, handles clones
 * @since 1.0.0
 */
add_action('admin_head', array(&$pagelines_template, 'load_section_optionator'));

add_filter( 'pagelines_options_array', 'pagelines_merge_addon_options' );

// In Site
add_action('wp_head', array(&$pagelines_template, 'print_template_section_headers'));
add_action('wp_print_styles', 'workaround_pagelines_template_styles'); // Used as workaround on WP login page (and other pages with wp_print_styles and no wp_head/pagelines_before_html)
add_action('pagelines_head', array(&$pagelines_template, 'hook_and_print_sections'));
add_action('wp_footer', array(&$pagelines_template, 'print_template_section_scripts'));

/**
 * Creates a global page ID for reference in editing and meta options (no unset warnings)
 * 
 * @since 1.0.0
 */
add_action('pagelines_before_html', 'pagelines_id_setup');


/**
 * Adds page templates from the child theme.
 * 
 * @since 1.0.0
 */
add_filter('the_sub_templates', 'pagelines_add_page_callback', 10, 2);

/**
 * Adds link to admin bar
 * 
 * @since 1.0.0
 */
add_action( 'admin_bar_menu', 'pagelines_settings_menu_link', 100 );

// ================
// = HEAD ACTIONS =
// ================

/**
 * Add Main PageLines Header Information
 * 
 * @since 1.3.3
 */
add_action('pagelines_head', 'pagelines_head_common');


/**
 * Do dynamic CSS last in the wp_head stack
 * 
 * @since 1.3.3
 */
add_action('wp_head', 'do_dynamic_css');

/**
 *
 * Load 'child' styles, functions and templates.
 * 
 * @since 2.0
 * 
 */	
add_action( 'wp_enqueue_scripts', 'load_child_style', 30 );
add_action( 'init', 'load_child_functions' );
add_action( 'init', 'base_check_templates' );
function load_child_style() {

	if ( file_exists( EXTEND_CHILD_DIR . '/base-style.css' ) )
		wp_enqueue_style( 'child', EXTEND_CHILD_URL . '/base-style.css' );
}

function load_child_functions() {
	if ( file_exists( EXTEND_CHILD_DIR . '/base-functions.php' ) )
		include( EXTEND_CHILD_DIR . '/base-functions.php' );
}

function base_check_templates() {

	foreach ( glob( EXTEND_CHILD_DIR . "/*.php") as $file) {

		if ( preg_match( '/page\.([a-z-0-9]+)\.php/', $file, $match ) ) {

			if ( !file_exists( trailingslashit( EXTEND_CHILD_DIR ) . $file ) ) 
				copy( $file, trailingslashit( STYLESHEETPATH ) . basename( $file ) );

			if ( file_exists( trailingslashit( STYLESHEETPATH ) . basename( $file ) ) ) {
					$data = get_file_data( trailingslashit( STYLESHEETPATH ) . basename( $file ), array( 'name' => 'Template Name' ) );
					pagelines_add_page( $match[1], $data['name'] );
			}

		}
	}
}