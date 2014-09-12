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
 * Load account handling
 */
pl_admin_include( 'paths.admin' );

/**
 * Load Drag and Drop UI
 */
pl_admin_include( 'class.ui.templates' );

/**
 * Load Layout Controls
 */
pl_admin_include( 'class.ui.layout' );

/**
 * Load Type Control
 */
pl_admin_include( 'class.ui.typography' );

/**
 * Load options UI
 */
pl_admin_include( 'class.options.ui' );

/**
 * Load options engine and breaker
 */
pl_admin_include( 'class.options.engine' );

/**
 * Load Panel UI
 */
pl_admin_include( 'class.options.panel' );

/**
 * Load inline help
 */
pl_admin_include( 'library.help' );

/**
 * Load account handling
 */
pl_admin_include( 'class.account' );
global $account_control;
$account_control = new PageLinesAccount;

/**
 * Load store class
 */
pl_admin_include( 'class.extend' );
pl_admin_include( 'class.extend.ui' );
pl_admin_include( 'class.extend.actions' );

pl_admin_include( 'class.extend.integrations' );
pl_admin_include( 'class.extend.themes' );
pl_admin_include( 'class.extend.plugins' );
pl_admin_include( 'class.extend.sections' );

global $extension_control;
$extension_control = new PagelinesExtensions;

pl_admin_include( 'class.rss' );

/**
 * Load admin actions
 */
pl_admin_include( 'actions.admin' );
pl_admin_include( 'actions.ajax' );

/**
 * Load option actions
 */
pl_admin_include( 'actions.options' );

/**
 * Load Dashboard Template
 */
pl_admin_include( 't.dashboard' );
pl_admin_include( 't.extensions' );
pl_admin_include( 't.support' );
