<?php
/**
 * This file initializes the PageLines framework
 *
 * @package PageLines Framework
 *
*/

/**
 * Run the starting hook
 */
do_action('pagelines_hook_pre', 'core'); // Hook

define('PL_INCLUDES', get_template_directory() . '/includes');

require_once( PL_INCLUDES . '/library.pagelines.php' );

/**
 * Load deprecated functions
 */
pl_include( 'deprecated' );

/**
 * Version
 */
pl_include( 'init.version' );

/**
 * Setup all the globals for the framework
 */
pl_include( 'init.globals' );

/**
 * Localization - Needs to come after config_theme and before localized config files
 */
pl_include( 'library.I18n' );

/**
 * Load core functions
 */
pl_include( 'library.functions' );

/**
 * Load Options Functions
 */
pl_include( 'library.options' );

/**
 * Load template related functions
 */
pl_include( 'library.templates' );

/**
 * Load template related functions
 */
pl_include( 'library.wordpress' );

/**
 * Load shortcode library
 */
pl_include( 'class.shortcodes' );

/**
 * Load Extension library
 */
pl_include( 'library.extend' );

/**
 * Load Layouts library
 */
pl_include( 'library.layouts' );

/**
 * Load Layouts library
 */
pl_include( 'library.theming' );

/**
 * Theme configuration files
 */
pl_include( 'config.options' );

/**
 * Theme/Framework Welcome
 */
pl_admin_include( 'class.welcome' );

/**
 * Dynamic CSS Selectors
 */
pl_include( 'config.selectors' );

/**
 * Load Custom Post Type Class
 */
pl_include( 'class.types' );

/**
 * Load layout class and setup layout singleton
 * @global object $pagelines_layout
 */
pl_include( 'class.layout' );

pl_include( 'library.layout' );

/**
 * User Handling
 */
pl_include( 'class.users' );

/**
 * Load sections handling class
 */
pl_include( 'class.sections' );

/**
 * Load template handling class
 */
pl_include( 'class.template' );

/**
 * Load Data Handling
 */
pl_admin_include( 'library.data' );

/**
 * Load HTML Objects
 */
pl_include( 'class.objects' );


/**
 * Load Type Foundry Class
 */
pl_include( 'class.typography' );

/**
 * Load Colors
 */
pl_include( 'class.colors' );

/**
 * Load dynamic CSS handling
 */
pl_include( 'class.css' );

/**
 * Load metapanel option handling class
 */
pl_admin_include( 'class.options.metapanel' );

/**
 * Load Profile Handling
 */
pl_admin_include( 'class.profiles' );


pl_include( 'library.upgrades' );
/**
 * Load Singleton Globals
 */
pl_include( 'init.singleton' );


/**
 * Add Extension Handlers
 */
pl_include( 'class.register' );

/**
 * Add Integration Functionality
 */
pl_include( 'class.integration' );

/**
 * Add Multisite
 */
if ( is_multisite() )
	pl_include( 'library.multisite' );

/**
 * Add Integration Functionality
 */
pl_include( 'class.themesupport' );

/**
 * Add Less Extension
 */
pl_include( 'less.plugin' );

/**
 * Add Less Functions
 */
pl_include( 'less.functions' );

/**
 * Add WordPress Plugin Support
 */
pl_include( 'library.plugins' );


/**
 * Register and load all sections
 */
$load_sections = new PageLinesRegister();
$load_sections->pagelines_register_sections();
$load_sections->register_sidebars();

/**
 * Setup
 */
pagelines_register_hook('pagelines_setup'); // Hook

/**
 * Load persistent section functions (e.g. custom post types)
 */
load_section_persistent();

if ( is_admin() )
	load_section_admin();

/**
 * Load the global meta settings tab
 */
do_global_meta_options();


/**
 * Build Version
 */
pl_include( 'version' );

pl_include( 'class.render.css' );

/**
 * Load site actions
 */
pl_include( 'actions.site' );

if ( ploption( 'enable_debug' ) )
	pl_admin_include( 'class.debug' );

/**
 * Run the pagelines_init Hook
 */
pagelines_register_hook('pagelines_hook_init'); // Hook

if ( is_admin() )
	pl_admin_include( 'init.admin' );

/**
 * Load updater class
 */
// uses github-updater plugin for updates since 2.4.5.2
//pl_admin_include( 'class.updates' );
//
//if ( is_admin() )
//	new PageLinesUpdateCheck( PL_CORE_VERSION );
