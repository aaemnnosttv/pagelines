<?php
if( !defined('VPRO' ) )
	define( 'VPRO', true );
	
// Add our menus where they belong.
add_action( 'admin_menu', 'pagelines_add_admin_menu' );

function pagelines_insert_menu_full( $page_title, $menu_title, $capability, $menu_slug, $function ) {
	
	return add_submenu_page( 'pagelines', $page_title, $menu_title, $capability, $menu_slug, $function );
	
}

/**
 * Full version menu wrapper.
 * 
 */
function pagelines_add_admin_menu() {
		global $menu;

		// Create the new separator
		$menu['2.995'] = array( '', 'edit_theme_options', 'separator-pagelines', '', 'wp-menu-separator' );

		// Create the new top-level Menu
		add_menu_page( 'Page Title', 'PageLines', 'edit_theme_options','pagelines', 'pagelines_build_option_interface', PL_ADMIN_IMAGES. '/favicon-pagelines.png', '2.996' );
}
