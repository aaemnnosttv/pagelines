<?php 

// ====================================
// = Build PageLines Option Interface =
// ====================================


//	This function adds the top-level menu
if( VPRO ) add_action('admin_menu', 'pagelines_add_admin_menu');
function pagelines_add_admin_menu() {
	global $menu;

	// Create the new separator
	$menu['2.995'] = array( '', 'edit_theme_options', 'separator-pagelines', '', 'wp-menu-separator' );

	// Create the new top-level Menu
	add_menu_page ('Page Title', 'PageLines', 'edit_theme_options','pagelines', 'pagelines_build_option_interface', PL_ADMIN_IMAGES. '/favicon-pagelines.png', '2.996');
}

// Create theme options panel
add_action('admin_menu', 'pagelines_add_admin_submenus');
function pagelines_add_admin_submenus() {
	global $_pagelines_options_page_hook;
	global $_pagelines_ext_hook;
	global $_pagelines_special_hook;
	
	// WP themes rep. wants it under the appearance tab.	
	if( !VPRO )
		$_pagelines_options_page_hook = add_theme_page( 'pagelines', 'PageLines Settings', 'edit_theme_options', 'pagelines', 'pagelines_build_option_interface' );
	else {
		$_pagelines_options_page_hook = add_submenu_page('pagelines', 'Settings', 'Settings', 'edit_theme_options', 'pagelines','pagelines_build_option_interface'); // Default
		$_pagelines_special_hook = add_submenu_page('pagelines', 'Special', 'Special', 'edit_theme_options', 'pagelines_special','pagelines_build_special');
		if(PL_DEV) $_pagelines_ext_hook = add_submenu_page('pagelines', 'Extend', 'Extend', 'edit_theme_options', 'pagelines_extend','pagelines_build_extension_interface');
		
	}
}

// Build option interface
function pagelines_build_option_interface(){ 
	pagelines_register_hook('pagelines_before_optionUI');
	
	$args = array(
		'sanitize' 		=> 'pagelines_settings_callback',
	);
	$optionUI = new PageLinesOptionsUI( $args );
}

/**
 * Build Extension Interface
 * Will handle adding additional sections, plugins, child themes
 */
function pagelines_build_extension_interface(){ 
	
	$args = array(
		'title'			=> 'PageLines Extend', 
		'settings' 		=> PAGELINES_EXTENSION,
		'callback'		=> 'extension_array',
		'show_save'		=> false, 
		'show_reset'	=> false, 
		'fullform'		=> false
	);
	$optionUI = new PageLinesOptionsUI($args);
}

/**
 * Build Meta Interface
 * Will handle meta for non-meta pages.. e.g. tags, categories
 */
function pagelines_build_special(){ 
	
	$args = array(
		'title'			=> 'Special Page Meta', 
		'settings' 		=> PAGELINES_SPECIAL,
		'callback'		=> 'special_page_settings_array',
		'show_reset'	=> false, 
		'basic_reset'	=> true
	);
	$optionUI = new PageLinesOptionsUI($args);
}



/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
add_action('admin_menu', 'pagelines_theme_settings_init');
function pagelines_theme_settings_init() {
	global $_pagelines_options_page_hook;
	global $_pagelines_ext_hook;
	global $_pagelines_special_hook;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ajaxupload', PL_ADMIN_JS . '/jquery.ajaxupload.js');
	wp_enqueue_script( 'jquery-cookie', PL_ADMIN_JS . '/jquery.ckie.js'); 
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'jquery-ui-dialog' );
	wp_enqueue_script( 'jquery-imgpreview',PL_ADMIN_JS . '/jquery.imgpreview.js', array('jquery'));
	
	// Call only on PL pages
	add_action('load-'.$_pagelines_options_page_hook, 'pagelines_theme_settings_scripts');
	add_action('load-'.$_pagelines_ext_hook, 'pagelines_theme_settings_scripts');
	add_action('load-'.$_pagelines_special_hook, 'pagelines_theme_settings_scripts');
	
	wp_enqueue_script( 'script-pagelines-common', PL_ADMIN_JS . '/script.common.js');
	
	// PageLines CSS objects
	pagelines_load_css_relative('css/objects.css', 'pagelines-objects');
}


function pagelines_theme_settings_scripts() {	

	wp_enqueue_script( 'script-pagelines-settings', PL_ADMIN_JS . '/script.settings.js');
	wp_enqueue_script( 'jquery-ui-effects',PL_ADMIN_JS . '/jquery.effects.js', array('jquery')); // just has highlight effect
	wp_enqueue_script( 'jquery-ui-draggable' );	
	wp_enqueue_script( 'jquery-ui-sortable' );
	
	wp_enqueue_script( 'thickbox' );	
	wp_enqueue_style( 'thickbox' ); 
	
	wp_enqueue_script( 'jquery-layout', PL_ADMIN_JS . '/jquery.layout.js');
}

add_action( 'admin_head', 'load_head' );
function load_head(){

	// Always Load
	echo '<link rel="stylesheet" href="'.PL_CSS.'/objects.css?ver='.CORE_VERSION.'" type="text/css" media="screen" />';
	echo '<link rel="stylesheet" href="'.PL_ADMIN_CSS.'/admin.css?ver='.CORE_VERSION.'" type="text/css" media="screen" />';
	
	
	if(pagelines_option('pagelines_favicon'))  
		echo '<link rel="shortcut icon" href="'.pagelines_option('pagelines_favicon').'" type="image/x-icon" />';

	// Load on PageLines pages
	if(isset($_GET['page']) && ($_GET['page'] == 'pagelines'))
		include( PL_ADMIN . '/admin.head.php' );

}

/**
 * This registers the settings field and adds defaults to the options table.
 * It also handles settings resets by pushing in the defaults.
 */
add_action('admin_init', 'pagelines_register_settings', 5);
function pagelines_register_settings() {
	
	
	register_setting( PAGELINES_SETTINGS, PAGELINES_SETTINGS, 'pagelines_settings_callback' );
	register_setting( PAGELINES_SPECIAL, PAGELINES_SPECIAL );
	
	 /*
	 	Set default settings
	 */
		add_option( PAGELINES_SETTINGS, pagelines_settings_defaults() ); // only fires first time
	


	
	/* Typography Options */
	$GLOBALS['pl_foundry'] = new PageLinesFoundry;

	/*
		Import/Exporting
	*/
	pagelines_import_export();

	pagelines_process_reset_options();
	
	pagelines_update_lpinfo();
	
	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'pagelines' )
		return;
	
	if ( ploption('reset') ) {
		update_option(PAGELINES_SETTINGS, pagelines_settings_defaults());

		wp_redirect( admin_url( 'admin.php?page=pagelines&reset=true' ) );
		exit;
	}

}


// Add Debug tab to main menu.

function pagelines_enable_debug( $option_array ) {
 
	$debug = new PageLinesDebug;
 	$debug_option_array['debug'] = array(
 		'debug_info' => array(
 		'type'		=> 'text_content',
 		'layout'	=> 'full',
 		'exp'		=> $debug->debug_info_template()
 		) );
 	return array_merge($option_array, $debug_option_array);
}

function pagelines_admin_confirms(){
	
	$confirms = array();
	
	if( isset($_GET['settings-updated']) )
		$confirms[]['text'] = THEMENAME.' Settings Saved. &nbsp;<a class="sh_preview" href="'.home_url().'/" target="_blank" target-position="front">View Your Site &rarr;</a>';
	
	if( isset($_GET['pageaction']) ){
	
		if( $_GET['pageaction']=='activated' && !isset($_GET['settings-updated']) ){
			$confirms['activated']['text'] = "Congratulations! ".THEMENAME ." Has Been Successfully Activated.";
			$confirms['activated']['class'] = "activated";
		}
	
		elseif( $_GET['pageaction']=='import' && isset($_GET['imported'] )){
			$confirms['settings-import']['text'] = "Congratulations! New settings have been successfully imported.";
			$confirms['settings-import']['class'] = "settings-import";
		}
	
		elseif( $_GET['pageaction']=='import' && isset($_GET['error']) && !isset($_GET['settings-updated']) ){
			$confirms['settings-import-error']['text'] = "There was an error with import. Please make sure you are using the correct file.";
		}
	
	}
	
	if( isset($_GET['reset']) ){
		
		if( isset($_GET['opt_id']) && $_GET['opt_id'] == 'resettemplates' )
			$confirms['reset']['text'] = "Template Configuration Restored To Default.";
			
		elseif( isset($_GET['opt_id']) && $_GET['opt_id'] == 'resetlayout' )
			$confirms['reset']['text'] = "Layout Dimensions Restored To Default.";

		else
			$confirms['reset']['text'] = "Settings Restored To Default.";
		
	}
	if ( isset( $_GET['plinfo'] ) )
		$confirms[]['text'] = "Launchpad settings saved.";
		
	return apply_filters('pagelines_admin_confirms', $confirms);
	
 }


function pagelines_draw_confirms(){ 
	
	$confirms = pagelines_admin_confirms();
	$save_text = sprintf('%s Settings Saved. &nbsp;<a class="btag" href="%s/" target="_blank" target-position="front">View Your Site &rarr;</a>', THEMENAME, home_url());
	printf('<div id="message" class="confirmation slideup_message fade c_ajax"><div class="confirmation-pad c_response">%s</div></div>', $save_text);

	if(!empty($confirms)){
		foreach ($confirms as $c){
		
			$class = (isset($c['class'])) ? $c['class'] : null;
			
			printf('<div id="message" class="confirmation slideup_message fade %s"><div class="confirmation-pad">%s</div></div>', $class, $c['text']);
		}
	}

} 

function pagelines_admin_errors(){
	
	$errors = array();
	
	if(ie_version() && ie_version() < 8){
		
		$errors['ie']['title'] = 'You are using Internet Explorer version: ' .ie_version();
		$errors['ie']['text'] = "Advanced options don't support Internet Explorer version 7 or lower. Please switch to a standards based browser that will allow you to easily configure your site (e.g. Firefox, Chrome, Safari, even IE8 or better would work).";
		
	}
	
	if(floatval(phpversion()) < 5.0){
		$errors['php']['title'] = 'You are using PHP version '. phpversion();
		$errors['php']['text'] = "Version 5 or higher is required for this theme to work correctly. Please check with your host about upgrading to a newer version.";
	}
	
	return apply_filters('pagelines_admin_notifications', $errors);
	
}

function pagelines_error_messages(){ 
	
	$errors = pagelines_admin_errors();
	if(!empty($errors)): 
		foreach ($errors as $e): ?>
	<div id="message" class="confirmation plerror fade">	
		<div class="confirmation-pad">
				<div class="confirmation-head">
					<?php echo $e['title'];?>
				</div>
				<div class="confirmation-subtext">
					<?php echo $e['text'];?>
				</div>
		</div>
	</div>
	
<?php 	endforeach;	
	endif;

} 

