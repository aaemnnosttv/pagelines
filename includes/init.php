<?php
/**
 * This file initializes the PageLines framework 
 *
 * @package Platform
 *
 **/

/**
* Before we start, check for PHP4. It is not supported and crashes with a parse error.
* We have to do it here before any other files are loaded.
*
* This can be removed with WordPress 3.2, which will only support PHP 5.2
*
**/ 
if( floatval( phpversion() ) < 5.0 ) {
	echo '<div style="border: 1px red solid">This server is running <strong>PHP ' . phpversion() . '</strong> we are switching back to the default theme for you!<br />';
	echo 'Please contact your host and switch to PHP5 before activating Platform. <a href="' . get_admin_url() . '">Site admin</a></div>';
	switch_theme( 'twentyten', 'twentyten');
	die(); // Brutal but we need to suppress those ugly php errors!
}

/**
 * Run the starting hook
 */
do_action('pagelines_hook_pre', 'core'); // Hook

define('PL_INCLUDES', TEMPLATEPATH . "/includes");

/**
 * Setup all the globals for the framework
 */
require_once( PL_INCLUDES . '/init.globals.php');

/**
 * Load deprecated functions
 */
require_once (PL_INCLUDES.'/deprecated.php');

/**
 * Localization - Needs to come after config_theme and before localized config files
 */
require_once( PL_INCLUDES . '/library.I18n.php');

/**
 * Load core functions
 */
require_once( PL_INCLUDES . '/library.functions.php');

/**
 * Load Options Functions 
 */
require_once( PL_INCLUDES . '/library.options.php' );

/**
 * Load template related functions
 */
require_once( PL_INCLUDES . '/library.templates.php');

/**
 * Load shortcode library
 */
require_once( PL_INCLUDES . '/library.shortcodes.php');


/**
 * Theme configuration files
 */
require_once( PL_INCLUDES . '/config.options.php' );

/**
 * Dynamic CSS Selectors
 */
require_once( PL_INCLUDES . '/config.selectors.php' );


/* Options Singleton */
$GLOBALS['global_pagelines_settings'] = get_option(PAGELINES_SETTINGS);	
$GLOBALS['pagelines_special_meta'] = get_option(PAGELINES_SPECIAL);	


/**
 * Load Custom Post Type Class
 */
require_once( PL_INCLUDES . '/class.types.php' );

/**
 * Posts Handling
 */	
require_once( PL_INCLUDES . '/class.posts.php' );


/**
 * Load layout class and setup layout singleton
 * @global object $pagelines_layout
 */
require_once( PL_INCLUDES . '/class.layout.php' ); 

	
/**
 * Load sections handling class
 */
require_once( PL_INCLUDES . '/class.sections.php' );

/**
 * Load template handling class
 */	
require_once( PL_INCLUDES . '/class.template.php' );


/**
 * Load metapanel option handling class
 */
require_once( PL_ADMIN . '/class.options.metapanel.php' );

/**
 * Singleton for Metapanel Options
 */
$GLOBALS['metapanel_options'] =  new PageLinesMetaPanel();

/**
 * Load Drag and Drop UI
 */
require_once( PL_ADMIN . '/class.ui.templates.php' );

/**
 * Load Layout Controls
 */
require_once( PL_ADMIN . '/class.ui.layout.php' );

/**
 * Load Type Control
 */
require_once( PL_ADMIN . '/class.ui.typography.php' );

/**
 * Load Color Controls
 */
require_once( PL_ADMIN . '/class.ui.color.php' );

/**
 * Load options UI
 */
require_once( PL_ADMIN . '/class.options.ui.php' );

/**
 * Load options engine and breaker
 */
require_once( PL_ADMIN . '/class.options.engine.php' );

/**
 * Load Data Handling
 */
require_once( PL_ADMIN . '/library.data.php' );

/**
 * Load HTML Objects
 */
require_once( PL_INCLUDES . '/class.objects.php' );


/**
 * Load Type Foundry Class
 */
require_once( PL_INCLUDES . '/class.typography.php' );

/**
 * Load Colors
 */
require_once( PL_INCLUDES . '/class.colors.php' );

/**
 * Load dynamic CSS handling
 */
require_once( PL_INCLUDES . '/class.css.php' );

/**
 * PageLines Section Factory Object (Singleton)
 * Note: Must load before the config template file
 * @global object $pl_section_factory
 * @since 1.0.0
 */
$GLOBALS['pl_section_factory'] = new PageLinesSectionFactory();

/**
 * Dynamic CSS Factory
 * @global object $css_factory
 * @since 2.0.b6
 */
$GLOBALS['css_factory'] = array();

/**
 * Add Extension Handlers
 */
require_once( PL_INCLUDES . '/class.extension.php' );

/**
 * Register and load all sections
 */
$load_sections = new PageLinesExtension();
$load_sections->pagelines_register_sections();

pagelines_register_hook('pagelines_setup'); // Hook

load_section_persistent(); // Load persistent section functions (e.g. custom post types)
if(is_admin()) load_section_admin(); // Load admin only functions from sections
do_global_meta_options(); // Load the global meta settings tab
	
/**
 * Support optional WordPress functionality
 */
add_theme_support( 'post-thumbnails', apply_filters( 'pagelines_post-thumbnails', array('post') ) );
add_theme_support( 'menus' );
add_theme_support( 'automatic-feed-links' );


/** 
 * Add editor styling
 * -- relative link
 */
add_editor_style( 'admin/css/editor-style.css' );

/**
 * Setup Framework Versions
 */
if(VPRO) require_once(PAGELINES_PRO . '/init_pro.php');
if(VDEV) require_once(PAGELINES_DEV . '/init_dev.php');	
	
require_once( PL_INCLUDES . '/version.php' );

/**
 * Enable debug if required.
 * 
 * @since 1.4.0
 */
if ( get_pagelines_option( 'enable_debug' ) ) {

	require_once ( PL_ADMIN . '/class.debug.php');
	add_filter( 'pagelines_options_array', 'pagelines_enable_debug' );
}
/**
 * Load updater class
 */
require_once (PL_ADMIN.'/class.updates.php');

/**
 * Load inline help
 */
require_once (PL_ADMIN . '/library.help.php' );

/**
 * Load plugin installer class
 */
require_once ( PL_ADMIN . '/class.extend.php' );
$extension_control = new PagelinesExtensions;

/**
 * Load admin actions
 */
require_once (PL_ADMIN.'/actions.admin.php'); 

/**
 * Load option actions
 */
require_once (PL_ADMIN.'/actions.options.php');

/**
 * Load site actions
 */
require_once (PL_INCLUDES.'/actions.site.php');

/**
 * Load actions list
 */
//require_once (PL_INCLUDES.'/class.actions.php');

/**
 * Run the pagelines_init Hook
 */
pagelines_register_hook('pagelines_hook_init'); // Hook
