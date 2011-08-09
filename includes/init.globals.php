<?php 

/**
 * Define framework version
 */
$theme_data = get_theme_data(TEMPLATEPATH . '/style.css');
define('CORE_VERSION', $theme_data['Version']);

$child_theme_data = get_theme_data(STYLESHEETPATH . '/style.css');
define('CHILD_VERSION', $child_theme_data['Version']);
	
/**
 * If Pro Version
 */
if(file_exists(TEMPLATEPATH.'/pro/init_pro.php')){
	define('VPRO',true);
}else{ define('VPRO',false);}
	
/**
 * If Dev Version
 */
if(file_exists(TEMPLATEPATH.'/dev/init_dev.php')){
	define('VDEV',true);
}else{ define('VDEV',false); }

if(!defined('PL_DEV'))
	define('PL_DEV',false);
	
if (! PL_DEV ) add_filter( 'extension_array', create_function( '', 'return array();' ) );	

/**
 * Set Theme Name
 */
if(VPRO) $theme = 'PageLines';
else $theme = 'PageLinesLE';

define('CORE_LIB', PL_INCLUDES); // Deprecated, but used in bbPress forum < 1.2.3

define('THEMENAME', $theme);
define('CHILDTHEMENAME', get_option('stylesheet'));
define('CHANGELOG_URL', 'http://www.pagelines.com/demos/platformpro/wp-content/themes/platformpro/changelog.txt');

define('PARENT_DIR', TEMPLATEPATH);
define('CHILD_DIR', STYLESHEETPATH);

define('PARENT_URL', get_template_directory_uri());
define('CHILD_URL', get_stylesheet_directory_uri());
define('CHILD_IMAGES', CHILD_URL . '/images');

/**
 * Define Settings Constants for option DB storage
 */
define('PAGELINES_SETTINGS', apply_filters('pagelines_settings_field', 'pagelines-settings'));
define('PAGELINES_EXTENSION', apply_filters('pagelines_settings_extension', 'pagelines-extension'));
define('PAGELINES_SPECIAL', apply_filters('pagelines_settings_special', 'pagelines-special'));

/**
 * Define PL Admin Paths
 */
define( 'PL_ADMIN', TEMPLATEPATH . '/admin' );
define( 'PL_ADMIN_URI', PARENT_URL . '/admin' );
define( 'PL_ADMIN_CSS', PL_ADMIN_URI . '/css' );
define( 'PL_ADMIN_JS', PL_ADMIN_URI . '/js' );
define( 'PL_ADMIN_IMAGES', PL_ADMIN_URI . '/images' );
define( 'PL_ADMIN_ICONS', PL_ADMIN_IMAGES . '/icons' );

/**
 * Define theme path constants
 */
define('PL_SECTIONS', TEMPLATEPATH . '/sections');

/**
 * Upload Folder information
 */
define('PAGELINES_DCSS', TEMPLATEPATH . '/css/dynamic.css');
define('PAGELINES_DCSS_URI', PARENT_URL . '/css/dynamic.css');

/**
 * Define web constants
 */
define('SECTION_ROOT', PARENT_URL . '/sections');

/**
 * Define theme web constants
 */
define('PL_CSS', PARENT_URL . '/css');
define('PL_JS', PARENT_URL . '/js');
define('PL_IMAGES', PARENT_URL . '/images');

// Deprecated, remove by 2.0
define( 'CORE_IMAGES', PL_IMAGES );

/**
 * Define version constants
 */
define('PAGELINES_PRO', TEMPLATEPATH . '/pro' );
define('PAGELINES_DEV', TEMPLATEPATH . '/dev' );

define('PAGELINES_PRO_ROOT', PARENT_URL . '/pro' );

/**
 * Define language constants
 */
$lang = ( is_dir( WP_PLUGIN_DIR . '/pagelines-base/language' ) ) ? WP_PLUGIN_DIR . '/pagelines-base/language' : TEMPLATEPATH . '/language';
define( 'PAGELINES_LANGUAGE_DIR', $lang );

/**
 * Functional Singletons - Used to work around hooks/filters
 */
$GLOBALS['pagelines_user_pages'] = array();

$GLOBALS['global_meta_options'] = array();

/**
 * Pro/Free Version Variables
 */
define('PROVERSION','PageLines Pro');
define('PROVERSIONDEMO','http://www.pagelines.com/demos/platformpro');
define('PROVERSIONOVERVIEW','http://www.pagelines.com/themes/platformpro');
define('PROBUY', 'http://www.pagelines.com/launchpad/signup.php?price_group[]=110&price_group[]=210&product_id=46&hide_paysys=paypal_r');

/**
 * Setup Extension constants
 */
define( 'EXTEND_CHILD_DIR', WP_PLUGIN_DIR . '/pagelines-base' );
define( 'EXTEND_CHILD_URL', plugins_url( 'pagelines-base' ) );