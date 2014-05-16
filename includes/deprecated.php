<?php
/**
 * Deprecated functions
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */

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