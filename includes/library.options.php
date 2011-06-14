<?php

/**
 * Sets up option name for saving of option settings
 *
 **/
function pagelines_option_name( $oid, $sub_oid = null, $grand_oid = null, $setting = PAGELINES_SETTINGS){
	
	if( isset($grand_oid) )
		echo $setting . '['.$oid.']' . '['.$sub_oid.']' . '['.$grand_oid.']';	
	elseif( isset($sub_oid) )
		echo $setting . '['.$oid.']' . '['.$sub_oid.']';
	else 
		echo $setting .'['.$oid.']';
	
}

function pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){
	echo get_pagelines_option_id($oid, $sub_oid, $grand_oid, $namespace);
}

function get_pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){

	if( isset($grand_oid) )
		$a = array($namespace, $oid, $sub_oid, $grand_oid);
	elseif( isset($sub_oid) )
		$a = array($namespace, $oid, $sub_oid);
	else 
		$a = array($namespace, $oid);
		
	return join('_', $a);
}

/**
 * Sanitize user input
 * 
 **/
function pagelines_settings_callback( $input ) {

	// We whitelist some of the settings, these need to have html/js/css.
	$whitelist = array( 'excerpt_tags', 'headerscripts', 'customcss', 'footerscripts', 'asynch_analytics', 'typekit_script', 'footer_terms', 'footer_more' );

	// We run through the $input array, if it is not in the whitelist we run it through the wp filters.
	foreach ($input as $name => $value){
		if ( !is_array( $value ) && !in_array( $name, apply_filters( 'pagelines_settings_whitelist', $whitelist ) ) ) $input[$name] = wp_filter_nohtml_kses( $value );
	}
	// Return our safe $input array.
	return $input;
}

/**
 * These functions pull options/settings
 * from the options database.
 *
 **/
function get_pagelines_option($key, $setting = null) {



	// get setting
	$setting = $setting ? $setting : PAGELINES_SETTINGS;

	if(!isset($setting) || $setting == PAGELINES_SETTINGS){
		
		global $global_pagelines_settings;
		
		if(isset($global_pagelines_settings[$key]))
			return $global_pagelines_settings[$key];
		else
			return false;
		
	}

	

}

function pagelines_option( $key, $post_id = null, $setting = null){
	
	if(isset($post_id) && get_post_meta($post_id, $key, true)){
		
		return get_post_meta($post_id, $key, true); //if option is set for a page/post
		
	}elseif( get_pagelines_option($key, $setting) ){
		
		return get_pagelines_option($key, $setting);
			
	}else {
		return false;
	}
}

function pagelines_sub_option( $key, $subkey, $post_id = '', $setting = null){
	
	$primary_option = pagelines_option($key, $post_id, $setting);
	
	if(is_array($primary_option) && isset($primary_option[$subkey]))
		return $primary_option[$subkey];
	else 
		return false;

}

// Need to keep until the forums are redone, or don't check for it.
function pagelines( $key, $post_id = null, $setting = null ){ 
	return pagelines_option($key, $post_id, $setting);
}

function e_pagelines($key, $alt = null, $post_id = null, $setting = null){
	print_pagelines_option( $key, $alt, $post_id, $setting);
}


function pagelines_pro($key, $post_id = null, $setting = null){

	if(VPRO) 
		return pagelines_option($key, $post_id, $setting);
	else 
		return false;
}

function print_pagelines_option($key, $alt = null, $post_id = null, $setting = null) {
	
	echo load_pagelines_option($key, $alt, $post_id, $setting);
	
}

function load_pagelines_option($key, $alt = null, $post_id = null, $setting = null) {
	
		if($post_id && get_post_meta($post_id, $key, true) && !is_home()){
			
			//if option is set for a page/post
			return get_post_meta($post_id, $key, true);
			
		}elseif(pagelines_option($key, $post_id, $setting)){
			
			return pagelines_option($key, $post_id, $setting);
			
		}else{
			return $alt;
		}
	
}

function pagelines_update_option($optionid, $optionval){
	
		$theme_options = get_option(PAGELINES_SETTINGS);
		$new_options = array(
			$optionid => $optionval
		);

		$settings = wp_parse_args($new_options, $theme_options);
		update_option(PAGELINES_SETTINGS, $settings);
}

function get_pagelines_meta($option, $post){
	$meta = get_post_meta($post, $option, true);
	if(isset($meta) && !pagelines_is_posts_page()){
		return $meta;
	}else{
		return false;
	}
}

	/* Deprecated in favor of get_pagelines_meta */
	function m_pagelines($option, $post){
		return get_pagelines_meta($option, $post);
	}


	function em_pagelines($option, $post, $alt = ''){
		$post_meta = m_pagelines($option, $post);
	
		if(isset($post_meta)){
			echo $post_meta;
		}else{
			echo $alt;
		}
	}

/**
 * Used as a filter on the master option array generated for settings
 *
 * @param $optionarray the master option array
 * @return rebuilt $optionsarray with addon options if plugin is active.
 * @since 2.0
 **/
function pagelines_merge_addon_options( $optionarray ) {
	$options = get_option( 'pagelines_addons_options' );
	$plugins = pagelines_register_plugins();
	if ( is_array( $options ) ) {
		
		$build_options = array();
		
		foreach( $options as $optionname => $option )
			if ( in_array( $optionname, $plugins ) ) $build_options[$optionname] = $option;
		
		return array_merge( $optionarray, $build_options );
		
	} else
		return $optionarray;
}

/**
 * Used to register and handle new plugin options
 * Use with register_activation_hook()
 * @since 2.0
 **/
function pagelines_register_addon_options( $addon_name, $addon_options ) {

	$addon_saved_options = get_option( 'pagelines_addons_options' );
	if ( !is_array( $addon_saved_options ) ) $addon_saved_options = array();
	if ( !isset($addon_saved_options[$addon_name] ) ) {
		$addon_saved_options[$addon_name] = $addon_options;
		update_option( 'pagelines_addons_options', $addon_saved_options );
	}
}

/**
 * Used to remove options when addons are deleted.
 * Use with register_deactivation_hook()
 * @since 2.0
 **/
function pagelines_remove_addon_options( $addon_name ) {
	$options = get_option( 'pagelines_addons_options' );
	if (is_array($options) && isset( $options[$addon_name] ) ) {
		unset($options[$addon_name]);
		update_option( 'pagelines_addons_options', $options );
	}
}
	
/**
 * This function registers the default values for pagelines theme settings
 */
function pagelines_settings_defaults() {

	$default_options = array();

		foreach(get_option_array() as $menuitem => $options ){
			foreach($options as $optionid => $o ){

				if($o['type']=='layout'){
					
					$dlayout = new PageLinesLayout;
					$default_options['layout'] = $dlayout->default_layout_setup();
					
				}elseif($o['type']=='check_multi' || $o['type']=='text_multi' || $o['type']=='color_multi'){
					foreach($o['selectvalues'] as $multi_optionid => $multi_o){
						if(isset($multi_o['default'])) $default_options[$multi_optionid] = $multi_o['default'];
					}

				}else{ 
					if(!VPRO && isset($o['version_set_default']) && $o['version_set_default'] == 'pro') 
						$default_options[$optionid] = null;
					elseif(!VPRO && isset($o['default_free'])) 
						$default_options[$optionid] = $o['default_free'];
					elseif(isset($o['default'])) 
						$default_options[$optionid] = $o['default'];
				}

			}
		}

	return apply_filters('pagelines_settings_defaults', $default_options);
}



function pagelines_process_reset_options() {


	foreach(get_option_array() as $menuitem => $options ){
		foreach($options as $optionid => $o ){
			if( $o['type']=='reset' && pagelines_option($optionid) ){

					call_user_func($o['callback']);
				
					// Set the 'reset' option back to not set !important 
					pagelines_update_option($optionid, null);
				
					wp_redirect( admin_url( 'admin.php?page=pagelines&reset=true&opt_id='.$optionid ) );
					exit;

			}

		}
	}

}

function pagelines_import_export(){
	
	if ( isset($_GET['download']) && $_GET['download'] == 'settings') {
		
			header("Cache-Control: public, must-revalidate");
			header("Pragma: hack");
			header("Content-Type: text/plain");
			header('Content-Disposition: attachment; filename="PageLines-'.THEMENAME.'-Settings-' . date("Ymd") . '.dat"');

			$pagelines_settings = get_option(PAGELINES_SETTINGS);
			$pagelines_template = get_option('pagelines_template_map');

			echo (serialize(array('pagelines_settings' => $pagelines_settings, 'pagelines_template' => $pagelines_template)));
			exit();
			
	}
	
	if ( isset($_POST['settings_upload']) && $_POST['settings_upload'] == 'settings') {
		
		if (strpos($_FILES['file']['name'], 'Settings') === false && strpos($_FILES['file']['name'], 'settings') === false){
			wp_redirect( admin_url('admin.php?page=pagelines&pageaction=import&error=wrongfile') ); 
		} elseif ($_FILES['file']['error'] > 0){
			$error_type = $_FILES['file']['error'];
			wp_redirect( admin_url('admin.php?page=pagelines&pageaction=import&error=file&'.$error_type) );
		} else {
			
			ob_start();
			include($_FILES['file']['tmp_name']);
			$raw_options = ob_get_contents();
			ob_end_clean();
			$all_options = unserialize($raw_options);
		
			if(isset($all_options['pagelines_settings']) && isset($all_options['pagelines_template'])){
				$pagelines_settings = $all_options['pagelines_settings'];
				$pagelines_template = $all_options['pagelines_template'];

			
				if (is_array($pagelines_settings)) update_option(PAGELINES_SETTINGS, $pagelines_settings); 
				if (is_array($pagelines_template)) update_option('pagelines_template_map', $pagelines_template); 
			
			}
			if (function_exists('wp_cache_clean_cache')) { 
				global $file_prefix;
				wp_cache_clean_cache($file_prefix); 
			}

			pagelines_build_dynamic_css();
			wp_redirect(admin_url( 'admin.php?page=pagelines&pageaction=import&imported=true' )); 
		}
		
	}

}

