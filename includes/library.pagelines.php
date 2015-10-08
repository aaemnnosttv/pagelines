<?php

if( !defined('VPRO' ) )
	define( 'VPRO', true );

add_action('pagelines_max_mem', function() {
	@ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
});


function pl_extend_default_headers( $extras ) {
	$extras['PageLines'] = 'PageLines';
	return $extras;
}
add_filter('extra_plugin_headers', 'pl_extend_default_headers');
add_filter('extra_theme_headers', 'pl_extend_default_headers');
