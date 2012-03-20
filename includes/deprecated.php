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
 * get_themes()
 *
 * @since 2.2
 * @deprecated 2.2
 * @deprecated WordPress 3.4 introduces wp_get_themes()
 */
if ( ! function_exists( 'wp_get_themes' ) ) {
	function wp_get_themes() { 
		return get_themes();
	}
}