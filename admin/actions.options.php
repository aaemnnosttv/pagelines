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
	
	// WP themes rep. wants it under the appearance tab.	
	if( !VPRO )
		$_pagelines_options_page_hook = add_theme_page( 'pagelines', 'PageLines Settings', 'edit_theme_options', 'pagelines', 'pagelines_build_option_interface' );
	else {
		$_pagelines_options_page_hook = add_submenu_page('pagelines', 'Settings', 'Settings', 'edit_theme_options', 'pagelines','pagelines_build_option_interface'); // Default
		$_pagelines_ext_page_hook = add_submenu_page('pagelines', 'Extension', 'Extension', 'edit_theme_options', 'pl_extension','pagelines_build_extension_interface');
		$_pagelines_tools_page_hook = add_submenu_page('pagelines', 'Tools', 'Tools', 'edit_theme_options', 'pl_tools','pagelines_build_extension_interface');
	}
}

// Build option interface
function pagelines_build_option_interface(){ 
	pagelines_register_hook('pagelines_before_optionUI');
	$optionUI = new PageLinesOptionsUI;
}

// Build option interface
function pagelines_build_extension_interface(){ 
	$optionUI = new PageLinesOptionsUI('Extension','testingarray', 'pagelines-extension');
}

/**
 * This is a necessary go-between to get our scripts and boxes loaded
 * on the theme settings page only, and not the rest of the admin
 */
add_action('admin_menu', 'pagelines_theme_settings_init');
function pagelines_theme_settings_init() {
	global $_pagelines_options_page_hook;
	global $_pagelines_ext_page_hook;
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ajaxupload', PL_ADMIN_JS . '/jquery.ajaxupload.js');
	wp_enqueue_script( 'jquery-cookie', PL_ADMIN_JS . '/jquery.ckie.js');
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	
	add_action('load-'.$_pagelines_options_page_hook, 'pagelines_theme_settings_scripts');
	add_action('load-'.$_pagelines_ext_page_hook, 'pagelines_theme_settings_scripts');
	wp_enqueue_script( 'script-pagelines-common', PL_ADMIN_JS . '/script.common.js');
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
	
	 /*
	 	Set default settings
	 */
		add_option( PAGELINES_SETTINGS, pagelines_settings_defaults() ); // only fires first time
	

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'pagelines' )
		return;	
	
	/* Typography Options */
	$GLOBALS['pl_foundry'] = new PageLinesFoundry;

	/*
		Import/Exporting
	*/
	pagelines_import_export();
		
	pagelines_process_reset_options();
	
	
	/*
		Regenerate Dynamic CSS ?
	*/
	$new_version_regen = ( !get_option("pl_dynamic_version") || get_option("pl_dynamic_version") != CORE_VERSION ) ? true : false;

	if ( isset($_GET['activated']) || isset($_GET['updated']) || isset($_GET['reset']) || isset($_GET['settings-updated']) || $new_version_regen ) {
	
		if ( get_pagelines_option('lp_username') && get_pagelines_option('lp_password') ) {
			if ( $update = get_transient('pagelines-update-' . THEMENAME ) ) {
				$update = maybe_unserialize($update);
				if ( $update['package'] == 'bad')
					delete_transient('pagelines-update-' . THEMENAME );
					delete_transient('pagelines-update-' . CHILDTHEMENAME );
			}
		}
		if($new_version_regen) update_option("pl_dynamic_version", CORE_VERSION);
	}
	
	if ( pagelines_option('reset') ) {
		update_option(PAGELINES_SETTINGS, pagelines_settings_defaults());

		wp_redirect( admin_url( 'admin.php?page=pagelines&reset=true' ) );
		exit;
	}

}


/*
	Section ON Page disabling
*/

add_action("admin_menu", 'add_section_control_box');

add_action('save_post', 'save_section_control_box');

function add_section_control_box(){
	
	$blacklist = array( 'banners', 'feature', 'boxes', 'attachment', 'revision', 'nav_menu_item' );
	if ( isset( $_GET['post']) && ! in_array( get_post_type( $_GET['post'] ), apply_filters( 'pagelines_meta_blacklist', $blacklist ) ) ) add_meta_box('section_control', 'PageLines Section Control', "pagelines_section_control_callback", get_post_type( $_GET['post'] ), 'side', 'low');

}

function pagelines_section_control_callback(){
	global $post; 
	global $global_pagelines_settings; 
	global $pl_section_factory;
	global $pagelines_template;
	
	$section_control = pagelines_option('section-control');
		// Check if we are the main blog page
	$postid = ( isset( $_GET['post'] ) ) ? $_GET['post'] : '';
	if ( get_option( 'page_for_posts' ) === $postid ) {
		echo '<div class="section_control_desc"><p><small><strong>Note:</strong> Individual page settings do not work on the blog page (<em>use the settings panel</em>).</small></p></div>';
		return;
	}
	echo '<div class="section_control_desc"><p>Below are all the sections that are active for this template.</p><p>Here you can turn sections off or on (if hidden by default) for this individual page/post.</p></div>';?>
		
		<div class="admin_section_control section_control_individual">
			<div class="section_control_pad">
			<?php  pagelines_process_template_map('section_control_checkbox', array('area_titles'=> true)); ?>
	 		</div>
		</div>
		
<?php 	
}

function section_control_checkbox($section, $template_slug, $template_area, $template){ 
		global $pl_section_factory;
		global $post;
		
		$s = $pl_section_factory->sections[$section];
	
		// Load Global Section Control Options
		$section_control = pagelines_option('section-control');
		$hidden_by_default = isset($section_control[$template_slug][$section]['hide']) ? $section_control[$template_slug][$section]['hide'] : null;
		
		$check_type = ($hidden_by_default) ? 'show' : 'hide';
		
		// used to be _hide_SectionClass;
		// needs to be _hide_TemplateSlug_SectionClass
		// Why? 
		$check_name = "_".$check_type."_".$section;
		
		$check_label = ucfirst($check_type)." ".$s->name;
		
		$check_value = get_pagelines_meta($check_name, $post->ID);
		
	?>
	
	<div class="section_checkbox_row <?php echo 'type_'.$check_type;?>" >
	
		<input class="section_control_check" type="checkbox" id="<?php echo $check_name; ?>" name="<?php echo $check_name; ?>" <?php checked((bool) $check_value); ?> />
		<label for="<?php echo $check_name;?>"><?php echo $check_label;?></label>
		
	</div>
<?php }

function save_section_control_box($postID){
	global $pagelines_template;
	
	global $post; 

	$save_template = new PageLinesTemplate();

	if(isset($_POST['update']) || isset($_POST['save']) || isset($_POST['publish'])){

		foreach($save_template->default_allsections as $section){
					
			$option_value =  isset($_POST['_hide_'.$section]) ? $_POST['_hide_'.$section] : null;
			
			if(!empty($option_value) || get_post_meta($postID, '_hide_'.$section)){
				update_post_meta($postID, '_hide_'.$section, $option_value );
			}
			
			$option_value =  isset($_POST['_show_'.$section]) ? $_POST['_show_'.$section] : null;
			
			if(!empty($option_value) || get_post_meta($postID, '_show_'.$section)){
				update_post_meta($postID, '_show_'.$section, $option_value );
			}
		}
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
	
	return apply_filters('pagelines_admin_confirms', $confirms);
	
 }


function pagelines_draw_confirms(){ 
	
	$confirms = pagelines_admin_confirms();

	if(!empty($confirms)): 
		foreach ($confirms as $c): 
		
			$class = (isset($c['class'])) ? $c['class'] : null;
		?>
	<div id="message" class="confirmation slideup_message fade <?php echo $class;?>">	
		<div class="confirmation-pad">
			<?php echo $c['text'];?>
		</div>
	</div>
	
<?php 	endforeach;	
	endif;

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

