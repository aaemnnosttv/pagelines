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
 * @deprecated Sections are now autoloaded and registered by platform core.
 **/
function pagelines_register_section() {
	_deprecated_function( __FUNCTION__, '2.0', 'You no longer need to register sections, simply drop them into CHILDTHEME/sections/ folder.' );
	return;
}
