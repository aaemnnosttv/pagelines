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
 * @since 4.0.0
 */
add_action('pagelines_before_html', 'build_pagelines_template');

// In Admin
add_action('admin_head', 'build_pagelines_template');

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
add_filter('pagelines_page_template_array', 'pagelines_add_page_callback');

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


	


