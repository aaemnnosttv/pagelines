<?php
if( !defined('VPRO' ) )
	define( 'VPRO', true );
if( is_admin() && current_user_can( 'edit_themes' ) )
	@ini_set('memory_limit', WP_MAX_MEMORY_LIMIT );