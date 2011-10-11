<?php
/**
 * Admin main init.
 *
 * 
 * @author PageLines
 *
 * @since 2.0.b21
 */

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
 * Load Panel UI
 */

require_once( PL_ADMIN . '/class.options.panel.php' );

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
require_once ( PL_ADMIN . '/class.ui.extend.php' );
$extension_control = new PagelinesExtensions;


/**
 * Load admin actions
 */
require_once (PL_ADMIN.'/actions.admin.php'); 

/**
 * Load option actions
 */
require_once (PL_ADMIN.'/actions.options.php');

