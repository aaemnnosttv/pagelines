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

if ( is_file( PL_INCLUDES . '/library.pagelines.php' ) )
	require_once( PL_INCLUDES . '/library.pagelines.php');

/**
 * Load deprecated functions
 */
require_once (PL_INCLUDES.'/deprecated.php');

/**
 * Check if version has changed.
 */
$installed = get_theme_mod( 'pagelines_version' );
$actual = pl_get_theme_data( get_template_directory(), 'Version' );

// if new version do some housekeeping.
if ( version_compare( $actual, $installed ) > 0 ) {

		delete_transient( 'pagelines_theme_update' );
		delete_transient( 'pagelines_extend_themes' );
		delete_transient( 'pagelines_extend_sections' );
		delete_transient( 'pagelines_extend_plugins' );
		delete_transient( 'pagelines_extend_integrations' );
		delete_transient( 'pagelines_sections_cache' );
		remove_theme_mod( 'available_updates' );
		remove_theme_mod( 'pending_updates' );
		define( 'PL_CSS_FLUSH', true );
}
set_theme_mod( 'pagelines_version', $actual );
set_theme_mod( 'pagelines_child_version', pl_get_theme_data( get_stylesheet_directory(), 'Version' ) );

/**
 * Setup all the globals for the framework
 */
require_once( PL_INCLUDES . '/init.globals.php');

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
 * Load template related functions
 */
require_once( PL_INCLUDES . '/library.wordpress.php');

/**
 * Load shortcode library
 */
require_once( PL_INCLUDES . '/class.shortcodes.php');

/**
 * Load Extension library
 */
require_once( PL_INCLUDES . '/library.extend.php');

/**
 * Load Layouts library
 */
require_once( PL_INCLUDES . '/library.layouts.php');

/**
 * Load Layouts library
 */
require_once( PL_INCLUDES . '/library.theming.php');

/**
 * Theme configuration files
 */
require_once( PL_INCLUDES . '/config.options.php' );

/**
 * Theme/Framework Welcome
 */
require_once( PL_ADMIN . '/class.welcome.php' );

/**
 * Dynamic CSS Selectors
 */
require_once( PL_INCLUDES . '/config.selectors.php' );


/**
 * Load Custom Post Type Class
 */
require_once( PL_INCLUDES . '/class.types.php' );


/**
 * Load layout class and setup layout singleton
 * @global object $pagelines_layout
 */
require_once( PL_INCLUDES . '/class.layout.php' );

require_once( PL_INCLUDES . '/library.layout.php' );

/**
 * Users Handling
 */
require_once( PL_INCLUDES . '/class.users.php' );

/**
 * Load sections handling class
 */
require_once( PL_INCLUDES . '/class.sections.php' );

/**
 * Load template handling class
 */
require_once( PL_INCLUDES . '/class.template.php' );

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
 * Load metapanel option handling class
 */
require_once( PL_ADMIN . '/class.options.metapanel.php' );

/**
 * Load Profile Handling
 */
require_once( PL_ADMIN . '/class.profiles.php' );


include( PL_INCLUDES . '/library.upgrades.php' );
/**
 * Load Singleton Globals
 */
require_once( PL_INCLUDES . '/init.singleton.php' );


/**
 * Add Extension Handlers
 */
require_once( PL_INCLUDES . '/class.register.php' );

/**
 * Add Integration Functionality
 */
require_once( PL_INCLUDES . '/class.integration.php' );

/**
 * Add Multisite
 */
if(is_multisite())
	require_once( PL_INCLUDES . '/library.multisite.php' );

/**
 * Add Integration Functionality
 */
require_once( PL_INCLUDES . '/class.themesupport.php' );

/**
 * Add Less Extension
 */
require_once( PL_INCLUDES . '/less.plugin.php' );

/**
 * Add Less Functions
 */
require_once( PL_INCLUDES . '/less.functions.php' );

/**
 * Add WordPress Plugin Support
 */
require_once( PL_INCLUDES . '/library.plugins.php' );


/**
 * Register and load all sections
 */
$load_sections = new PageLinesRegister();
$load_sections->pagelines_register_sections();
$load_sections->register_sidebars();

pagelines_register_hook('pagelines_setup'); // Hook

load_section_persistent(); // Load persistent section functions (e.g. custom post types)

if(is_admin())
	load_section_admin(); // Load admin only functions from sections

do_global_meta_options(); // Load the global meta settings tab


/**
 * Build Version
 */
require_once( PL_INCLUDES . '/version.php' );

require_once( PL_INCLUDES . '/class.render.css.php' );

/**
 * Load site actions
 */
require_once (PL_INCLUDES.'/actions.site.php');

if ( ploption( 'enable_debug' ) )
	require_once ( PL_ADMIN . '/class.debug.php');

/**
 * Run the pagelines_init Hook
 */
pagelines_register_hook('pagelines_hook_init'); // Hook

if ( is_admin() )
	include( PL_ADMIN . '/init.admin.php' );

/**
 * Load updater class
 */
//require_once (PL_ADMIN.'/class.updates.php');
//
//if ( is_admin() )
//	new PageLinesUpdateCheck( PL_CORE_VERSION );
