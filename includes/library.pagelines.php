<?php
if( !defined('VPRO' ) )
	define( 'VPRO', true );
	
// Add our menus where they belong.
	
add_action( 'admin_menu', 'pagelines_add_admin_menu' );
add_action( 'admin_menu', 'pagelines_add_admin_submenus' );
	
function pagelines_add_admin_menu() {
		global $menu;

		// Create the new separator
		$menu['2.995'] = array( '', 'edit_theme_options', 'separator-pagelines', '', 'wp-menu-separator' );

		// Create the new top-level Menu
		add_menu_page( 'Page Title', 'PageLines', 'edit_theme_options','pagelines', 'pagelines_build_option_interface', PL_ADMIN_IMAGES. '/favicon-pagelines.png', '2.996' );
}

	// Create theme options panel

function pagelines_add_admin_submenus() {
		global $_pagelines_options_page_hook;
		global $_pagelines_ext_hook;
		global $_pagelines_special_hook;
		global $_pagelines_templates_hook;
		global $_pagelines_account_hook;

		$_pagelines_options_page_hook = add_submenu_page( 'pagelines', __( 'Settings', 'pagelines' ), __( 'Settings', 'pagelines' ), 'edit_theme_options', 'pagelines','pagelines_build_option_interface' ); // Default
		$_pagelines_templates_hook = add_submenu_page( 'pagelines', __( 'Templates', 'pagelines' ), __( 'Templates', 'pagelines' ), 'edit_theme_options', 'pagelines_templates','pagelines_build_templates_interface' );
		$_pagelines_special_hook = add_submenu_page( 'pagelines', __( 'Special', 'pagelines' ), __( 'Special', 'pagelines' ), 'edit_theme_options', 'pagelines_special','pagelines_build_special' );
		$_pagelines_ext_hook = add_submenu_page( 'pagelines', __( 'Store', 'pagelines' ), __( 'Store', 'pagelines' ), 'edit_theme_options', 'pagelines_extend','pagelines_build_extension_interface' );
		$_pagelines_account_hook = add_submenu_page( 'pagelines', __( 'Account', 'pagelines' ), __( 'Account', 'pagelines' ), 'edit_theme_options', 'pagelines_account','pagelines_build_account_interface' );

}