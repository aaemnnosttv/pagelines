<?php

global $pagelines_template;

// ===================================================================================================
// = Set up Section loading & create pagelines_template global in page (give access to conditionals) =
// ===================================================================================================

/**
 * Build PageLines Template Global (Singleton)
 *
 * Must be built inside the page (wp_head) so conditionals can be used to identify the template
 * in the admin; the template does not need to be identified so it is loaded in the init action
 *
 * @global  object $pagelines_template
 * @since   1.0.0
 */
add_action('pagelines_before_html', 'build_pagelines_template');

/**
 * Build the template in the admin... doesn't need to load in the page
 * @since 1.0.0
 */
add_action('admin_head', 'build_pagelines_template', 5);

add_action('pagelines_before_html', 'build_pagelines_layout', 5);
add_action('admin_head', 'build_pagelines_layout');

/**
 * Optionator
 * Does "just in time" loading of section option in meta; 
 * Will only load section options if the section is present, handles clones
 * @since 1.0.0
 */
add_action('admin_head', array(&$pagelines_template, 'load_section_optionator'));

add_filter( 'pagelines_options_array', 'pagelines_merge_addon_options' );

// Run Before Any HTML
add_action('pagelines_before_html', array(&$pagelines_template, 'run_before_page'));

add_action('wp_print_styles', 'workaround_pagelines_template_styles'); // Used as workaround on WP login page (and other pages with wp_print_styles and no wp_head/pagelines_before_html)

add_action( 'wp_print_styles', 'pagelines_get_childcss' );

add_action('pagelines_head', array(&$pagelines_template, 'hook_and_print_sections'));

add_action('wp_footer', array(&$pagelines_template, 'print_template_section_scripts'));

/**
 * Creates a global page ID for reference in editing and meta options (no unset warnings)
 * 
 * @since 1.0.0
 */
add_action('pagelines_before_html', 'pagelines_id_setup', 5);


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
 *
 * @TODO document
 *
 */
function pagelines_add_google_profile( $contactmethods ) {
	// Add Google Profiles
	$contactmethods['google_profile'] = __( 'Google Profile URL', 'pageines' );
	return $contactmethods;
}
add_filter( 'user_contactmethods', 'pagelines_add_google_profile', 10, 1);

/**
 * ng gallery fix.
 *
 * @return gallery template path
 * 
 */

add_filter( 'ngg_render_template', 'gallery_filter' , 10, 2);


/**
 *
 * @TODO document
 *
 */
function gallery_filter( $a, $template_name) {

	if ( $template_name == 'gallery-plcarousel')
		return sprintf( '%s/carousel/gallery-plcarousel.php', PL_SECTIONS);
	else
		return false;
}

add_action( 'init', 'pagelines_add_sidebars', 1 );


/**
 * PageLines Add Sidebars
 *
 * Registers sidebars
 *
 * @since   ...
 *
 * @uses    ploption
 * @uses    (global) $pagelines_sidebars
 */
function pagelines_add_sidebars() {
	
	if ( ! ploption( 'enable_sidebar_reorder') )
		return;
	global $pagelines_sidebars;

	if ( !is_array( $pagelines_sidebars ) )
		return;

	ksort( $pagelines_sidebars );
	
	foreach ( $pagelines_sidebars as $key => $sidebar )
		register_sidebar( $sidebar );
}


/**
 * Do dynamic CSS last in the wp_head stack
 * 
 * @since 1.3.3
 */
if ( ! ploption( 'less_css' ) ) {
	add_action('wp_head', 'do_dynamic_css', 8);
	add_action('wp_head', array(&$pagelines_template, 'print_template_section_headers_legacy'));
} else {
	add_action( 'template_redirect', 'pl_check_integrations' );
	add_filter('query_vars','pagelines_add_trigger');
	add_filter( 'generate_rewrite_rules', 'pagelines_less_rewrite' );
	add_action( 'wp_print_styles', 'load_less_css', 11 );
	add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ) );
	
//	add_action( 'wp_head', create_function( '', 'pagelines_load_css( PARENT_URL."/pageless-" . ploption( "pl_save_version" ) . ".css", "pagelines-less" );' ) );
}

function load_less_css() {
	
	$url = ( '' != get_option('permalink_structure') ) ? sprintf( '%s/pageless-%s.css/',PARENT_URL, ploption( "pl_save_version" ) ) : sprintf( '%s/?plless=1', site_url() );
	wp_register_style( 'pagelines-less',  $url, false, false, 'all' );
	wp_enqueue_style( 'pagelines-less' );
}

function pagelines_add_trigger( $vars ) {
    $vars[] = 'plless';
    return $vars;
}

add_action('template_redirect', 'pagelines_less_trigger');
function pagelines_less_trigger() {
	if( intval( get_query_var( 'plless' ) ) == 1) {
		build_pagelines_layout();
		$template = new PageLinesTemplate;
		header( 'Content-type: text/css' );
		header( 'Expires: ' );
		header( 'Cache-Control: max-age=604100, public' );
		$template->print_template_section_css();
	exit;
	}
}

function pagelines_less_rewrite( $wp_rewrite ) {
    $feed_rules = array(
        '(.*)/pageless-[0-9]+.css(.*)' => '/index.php?plless=1'
    );

    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}

