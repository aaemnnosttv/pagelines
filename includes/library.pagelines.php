<?php

if( !defined('VPRO' ) )
	define( 'VPRO', true );

add_action('pagelines_max_mem', function() {
	@ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
});

