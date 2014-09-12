<?php
/**
 * Deprecated functions
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */

/**
 * Checks if PHP5
 *
 * Tests for installed version of PHP higher than 5.0 and prints message if version is found to be lower.
 *
 * @deprecated WordPress requires PHP 5.2.4
 * 
 * @package PageLines Framework
 * @subpackage Functions Library
 * @since 4.0.0
 */
function pagelines_check_php()
{
	if ( version_compare( phpversion(), 5.0, '<' ) )
	{
		printf( __( "<div class='config-error'><h2>PHP Version Problem</h2>Looks like you are using PHP version: <strong>%s</strong>. To run this framework you will need PHP <strong>5.0</strong> or better...<br/><br/> Don't worry though! Just check with your host about a quick upgrade.</div>", 'pagelines' ), phpversion() );
	}
}

/**
 * pagelines_register_section()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated Sections are now autoloaded and registered by the framework.
 */
function pagelines_register_section() {
	_deprecated_function( __FUNCTION__, '2.0', 'the CHILDTHEME/sections/ folder' );
	return;
}

/**
 * cmath()
 *
 * @since 1.0
 * @deprecated 2.0
 * @deprecated A more useful function name
 */
function cmath( $color ) {
	_deprecated_function( __FUNCTION__, '2.0', 'loadmath' );
	return new PageLinesColor( $color );
}

/**
 * Deprecated constants, removing after a couple of revision, this will ensure store products get time to update.
 *
 */
define( 'CORE_VERSION'	, get_theme_mod( 'pagelines_version' ) );
define( 'THEMENAME'		, 'PageLines' );
define( 'CHILD_URL'		, get_stylesheet_directory_uri() );
define( 'CHILD_IMAGES'	, CHILD_URL . '/images' );
define( 'CHILD_DIR'		, get_stylesheet_directory() );
define( 'SECTION_ROOT'	, get_template_directory_uri() . '/sections');
define( 'PL_CORE_LIB'	, PL_INCLUDES); // Used in bbPress forum < 1.2.3
