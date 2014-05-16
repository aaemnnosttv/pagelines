<?php

/**
 * Check if versions have changed.
 */
$updated    = false;
$core       = get_theme_mod( 'pagelines_version' );
$real_core  = pl_get_theme_data( get_template_directory(), 'Version' );
$is_child   = get_theme_mod( 'is_child' );
$child      = false;
$real_child = false;

if ( version_compare( $real_core, $core, '!=' ) )
	$updated = true;

if ( is_child_theme() )
{
	$child      = get_theme_mod( 'pagelines_child_version' );
	$real_child = pl_get_theme_data( get_stylesheet_directory(), 'Version' );

	if ( version_compare( $real_child, $child, '!=' ) )
		$updated = true;

	if ( !$is_child )
	{
		$updated = true;
		set_theme_mod( 'is_child', true );
	}
}
elseif ( $is_child )
{
	// theme_mod was set as child, and !is_child_theme()
	$updated = true;
	set_theme_mod( 'is_child', false );
}

// if new version do some housekeeping.
if ( $updated )
{
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

if ( !$core || $updated )
	set_theme_mod( 'pagelines_version', $real_core );

if ( !$child || $updated )
	set_theme_mod( 'pagelines_child_version', $real_child );


/**
 * Announce the current version
 *
 * @since  2.4.7
 *
 * @param string $real_core core version
 * @param string $real_child child theme version
 * @param bool $updated whether either parent or child theme version changed
 */
do_action( 'pl_version', $real_core, $real_child, $updated );

// clean up
unset( $updated, $core, $real_core, $child, $real_child, $is_child );
