<?php

/**
 * Uses controls to find and retrieve the appropriate option value
 * 
 * @param 'key' the id of the option
 * 
 **/
function ploption( $key, $args = array() ){

	$d = array(
		'subkey'	=> null, 
		'post_id'	=> null, 
		'setting'	=> null, 
		'clone_id'	=> null,
	);
	
	$o = wp_parse_args($args, $d);
	
	if(is_pagelines_special() && plspecial($key, $args))
		return plspecial($key, $args);
	
	elseif(isset($o['post_id']) && plmeta( $key, $args ))
		return plmeta( $key, $args );
		
	elseif( get_ploption($key, $args) )
		return get_ploption( $key, $args );
		
	elseif( get_ploption($key, $args) === null )
		if ( $newkey = plnewkey( $key ) )
			return $newkey;			
		
	else
		return false;
}
/**
 * Attempt to set default value if not found with ploption()
 * 
 * @param 'key' the id of the option
 * 
 **/
function plnewkey( $key ) {
	
	if ( !is_admin() )
		return false;
	$settings = get_option_array();

	foreach ($settings as $group)
		foreach($group as $name => $setting)
			if ($name == $key && isset( $setting['default'] ) ) {
				plupop( $key, $setting['default'] );
				return $setting['default'];
			} 
		return false;
}


function plupop($key, $val, $oset = array()){
	
	$d = array(
		'parent'	=> null,
		'subkey'	=> null, 
		'setting'	=> PAGELINES_SETTINGS,
	);
	
	$o = wp_parse_args($oset, $d);
	
	$the_set = get_option($o['setting']);

	$new = array( $key => $val );

	$parent = ( isset($o['parent']) ) ? $o['parent'] : null;



	$child_option = ( isset($parent) && isset($the_set[$parent]) && is_array($the_set[$parent]) ) ? true : false;
	
	$parse_set = ( $child_option ) ? $the_set[ $parent ] : $the_set;
	
	$new_set = wp_parse_args($new, $parse_set);
		
		
	if($child_option)
		$the_set[ $parent ] = $new_set;
	else
		$the_set = $new_set;
	
	update_option( $o['setting'], $the_set );

}

/**
 * Locates a meta option if it exists
 * 
 * @param string $key the key of the option
 */
function plmeta( $key, $args ){
	
	$d = array(
		'subkey'	=> null, 
		'post_id'	=> null, 
		'setting'	=> null, 
		'clone_id'	=> null,
	);
	
	$o = wp_parse_args($args, $d);
		
	if( isset($args['clone_id']) && $args['clone_id'] != 1 )
		$id_key = $key.'_'.$args['clone_id'];	
	else 
		$id_key = $key;

	if( isset($o['post_id']) && !empty($o['post_id']) )
		return get_post_meta($o['post_id'], $id_key, true);
	
	else
		return false;
	
}

function plspecial($key, $args){

	global $pagelines_special_meta;
	
	$type = PageLinesTemplate::page_type_breaker();
	
	if( isset($args['clone_id']) && $args['clone_id'] != 1 )
		$id_key = $key.'_'.$args['clone_id'];
	else
		$id_key = $key;

	if(isset($pagelines_special_meta[$type]) && is_array($pagelines_special_meta[$type]) && isset($pagelines_special_meta[$type][$id_key]))
		return $pagelines_special_meta[$type][$id_key];
	else 
		return false;
}

function pldefault($key){
	global $pagelines_special_meta;
	
	if(isset($pagelines_special_meta['defaults'][$key]))
		return $pagelines_special_meta['defaults'][$key];
	else
		return false;
	
}

function get_ploption( $key, $args = array() ){
	
	$d = array(
		'subkey'	=> null, 
		'post_id'	=> null, 
		'setting'	=> null, 
		'clone_id'	=> null,
		'special'	=> null
	);
	
	$o = wp_parse_args($args, $d);
	
	// get setting
	$setting = ( isset($o['setting']) && !empty($o['setting'])) ? $o['setting'] : PAGELINES_SETTINGS;

	if(!isset($setting) || $setting == PAGELINES_SETTINGS){
		
		global $global_pagelines_settings;
		
		if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key])  )
			return $global_pagelines_settings[$key];
	
		else
			return false;
		
	} elseif ( isset($setting) ){
		
		$setting_options = get_option($setting);
		
		if( isset($o['subkey']) ){
			
			if(isset($setting_options[$key]) && is_array($setting_options[$key]) && isset($setting_options[$key][$o['subkey']]))
				return $setting_options[$key][$o['subkey']];
			else
				return false;
			
		}elseif( isset($setting_options[$key]) )
			return $setting_options[$key];
	
		else
			return false;
		
	} else 
		return false;
	
}

function plname($key, $a = array()){
	
	$set = (!isset($a['setting']) || empty($a['setting']) || $a['setting'] == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $a['setting'];
	
	$subkey = (isset($a['subkey'])) ? $a['subkey'] : false;
	
	$grandkey = (isset($a['subkey']) && is_array($a['subkey']) && isset($a['subkey']['grandkey'])) ? $a['subkey']['grandkey'] : false;
	
	if( $grandkey )
		$output = $set . '['.$key.']['.$subkey.']['.$grandkey.']';	
	elseif( $subkey )
		$output = $set . '['.$key.']['.$subkey.']';
	else 
		$output = $set .'['.$key.']';
		
	return $output;
	
}

function plid($key, $a){
	
	$set = (!isset($a['setting']) || empty($a['setting']) || $a['setting'] == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $a['setting'];

	$subkey = (isset($a['subkey'])) ? $a['subkey'] : false;
	
	$grandkey = (isset($a['subkey']) && is_array($a['subkey']) && isset($a['subkey']['grandkey'])) ? $a['subkey']['grandkey'] : false;


	if( $grandkey )
		$output = array($set, $key, $subkey, $grandkey);
	elseif( $subkey )
		$output = array($set, $key, $subkey);
	else 
		$output = array($set, $key);
		
	return join('_', $output);
}

function pl_um($key, $args = null){
	
	if(is_array($args)){
		
		$d = array(
			'user_id'	=> null
		);

		$o = wp_parse_args($args, $d);
	} else {
		
		$o['user_id'] = $args;
		
	}

	
	return get_user_meta( $o['user_id'], $key, true );
}

/**
 * Get the option, if its not set, set it.
 * @todo make usable with different settings types.
 *
 **/
function pl_getset_option($key, $default = false) {
	
	global $global_pagelines_settings;
	
	if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key]) )
		return $global_pagelines_settings[$key];

	else{	
		plupop( $key, $default );
		return $default;
	}
}


/**
 * Sets up option name for saving of option settings
 *
 **/
function pagelines_option_name( $oid, $sub_oid = null, $grand_oid = null, $setting = PAGELINES_SETTINGS){
	echo get_pagelines_option_name( $oid, $sub_oid, $grand_oid, $setting );
}

function get_pagelines_option_name( $oid, $sub_oid = null, $grand_oid = null, $setting = PAGELINES_SETTINGS ){
	
	$set = (!isset($setting) || $setting == PAGELINES_SETTINGS) ? PAGELINES_SETTINGS : $setting;
	
	if( isset($grand_oid) )
		$name = $set . '['.$oid.']' . '['.$sub_oid.']' . '['.$grand_oid.']';	
	elseif( isset($sub_oid) )
		$name = $set . '['.$oid.']' . '['.$sub_oid.']';
	else 
		$name = $set .'['.$oid.']';
		
	return $name;
}

function meta_option_name( $array, $hidden = true ){
	
	$prefix = ($hidden) ? '_' : '';
	
	return $prefix.join('_', $array);
	
}

function pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){
	echo get_pagelines_option_id($oid, $sub_oid, $grand_oid, $namespace);
}

function get_pagelines_option_id( $oid, $sub_oid = null, $grand_oid = null, $namespace = 'pagelines'){

	$nm = (!isset($namespace) || $namespace == 'pagelines') ? 'pagelines' : $namespace;

	if( isset($grand_oid) )
		$a = array($nm, $oid, $sub_oid, $grand_oid);
	elseif( isset($sub_oid) )
		$a = array($nm, $oid, $sub_oid);
	else 
		$a = array($nm, $oid);
		
	return join('_', $a);
}

/**
 * Sanitize user input
 * 
 **/
function pagelines_settings_callback( $input ) {

	// We whitelist some of the settings, these need to have html/js/css.
	$whitelist = array( 'excerpt_tags', 'headerscripts', 'customcss', 'footerscripts', 'asynch_analytics', 'typekit_script', 'footer_terms', 'footer_more' );

	if(is_array($input)){
		
		// We run through the $input array, if it is not in the whitelist we run it through the wp filters.
		foreach ($input as $name => $value){
			if ( !is_array( $value ) && !in_array( $name, apply_filters( 'pagelines_settings_whitelist', $whitelist ) ) ) 
				$input[$name] = wp_filter_nohtml_kses( $value );
		}
		
	}
	
	// Return our safe $input array.
	return $input;
}

/**
 * These functions pull options/settings
 * from the options database.
 *
 **/
function get_pagelines_option($key, $setting = null, $default = null) {
	// get setting
	$setting = $setting ? $setting : PAGELINES_SETTINGS;

	if(!isset($setting) || $setting == PAGELINES_SETTINGS){
		
		global $global_pagelines_settings;
		
		if( is_array($global_pagelines_settings) && isset($global_pagelines_settings[$key]) )
			return $global_pagelines_settings[$key];
	
		else
			if ( $default ) {
				plupop( $key, $default );
				return $default;
			}
		return false;
	}
}



function pagelines_option( $key, $post_id = null, $setting = null){
	
	if(isset($post_id) && get_post_meta($post_id, $key, true))
		return get_post_meta($post_id, $key, true); //if option is set for a page/post
		
	elseif( get_pagelines_option($key, $setting) )	
		return get_pagelines_option($key, $setting);
			
	else 
		return false;
	
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
	if(isset($meta))
		return $meta;
	else
		return false;
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
	
		foreach(get_option_array( true ) as $menuitem => $options ){
			
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



function pagelines_process_reset_options( $option_array = null ) {

	if(isset($_POST['pl_reset_settings']) && current_user_can('edit_themes')){
		
		if(isset($_POST['the_pl_setting']) && !isset($_POST['reset_callback']))
			update_option($_POST['the_pl_setting'], array());
		
		if(isset($_POST['reset_callback']))
			call_user_func( $_POST['reset_callback'] );
			
	}

	
	$option_array = (isset($option_array)) ? $option_array : get_option_array();

	foreach($option_array as $menuitem => $options ){
		foreach($options as $oid => $o ){
			if( $o['type']=='reset' && ploption($oid) ){

					call_user_func($o['callback']);
				
					// Set the 'reset' option back to not set !important 
					pagelines_update_option($oid, null);
				
					wp_redirect( admin_url( 'admin.php?page=pagelines&reset=true&opt_id='.$oid ) );
					exit;

			}

		}
	}

}

function pagelines_import_export(){

		if ( isset( $_POST['form_submitted']) && $_POST['form_submitted'] == 'export_settings_form' ) {

			$pagelines_settings = get_option(PAGELINES_SETTINGS);
			$pagelines_template = get_option( PAGELINES_TEMPLATE_MAP );
			$pagelines_special = get_option( PAGELINES_SPECIAL );

			$options['pagelines_template'] = $pagelines_template;
			$options['pagelines_settings'] = $pagelines_settings;
			$options['pagelines_special'] = $pagelines_special;


			if ( isset($options) && is_array( $options) ) {
				
				header("Cache-Control: public, must-revalidate");
				header("Pragma: hack");
				header("Content-Type: text/plain");
				header('Content-Disposition: attachment; filename="PageLines-'.THEMENAME.'-Settings-' . date("Ymd") . '.dat"');				
				
				echo json_encode( $options );
				exit();
			} 

	}

	if ( isset($_POST['form_submitted']) && $_POST['form_submitted'] == 'import_settings_form') {
		
		if (strpos($_FILES['file']['name'], 'Settings') === false && strpos($_FILES['file']['name'], 'settings') === false){
			wp_redirect( admin_url('admin.php?page=pagelines_extend&pageaction=import&error=wrongfile') ); 
		} elseif ($_FILES['file']['error'] > 0){
			$error_type = $_FILES['file']['error'];
			wp_redirect( admin_url('admin.php?page=pagelines_extend&pageaction=import&error=file&'.$error_type) );
		} else {
			
			ob_start();
			include($_FILES['file']['tmp_name']);
			$raw_options = ob_get_contents();
			ob_end_clean();
	
			$all_options = json_decode(json_encode(json_decode($raw_options)), true);
			
			if ( !isset( $_POST['pagelines_layout'] ) && is_array( $all_options) && isset( $all_options['pagelines_settings'] ) )
				unset( $all_options['pagelines_settings']['layout'] );
			
			if ( isset( $_POST['pagelines_settings'] ) && is_array( $all_options) && isset( $all_options['pagelines_settings'] ) ) {
				update_option( PAGELINES_SETTINGS, array_merge( get_option( PAGELINES_SETTINGS ), $all_options['pagelines_settings'] ) );
				$done = 1;
			}
			
			if ( isset( $_POST['pagelines_special'] ) && is_array( $all_options) && isset( $all_options['pagelines_special'] ) ) {
				update_option( PAGELINES_SPECIAL, array_merge( get_option( PAGELINES_SPECIAL ), $all_options['pagelines_special'] ) );
				$done = 1;
			}
			
			if ( isset( $_POST['pagelines_template'] ) && is_array( $all_options) && isset( $all_options['pagelines_template'] ) ) {
				update_option( 'pagelines_template_map', array_merge( get_option( 'pagelines_template_map' ), $all_options['pagelines_template'] ) );
				$done = 1;
			}					
				if (function_exists('wp_cache_clean_cache')) { 
					global $file_prefix;
					wp_cache_clean_cache($file_prefix); 
				}
				if ( isset($done) ) {
				wp_redirect( admin_url( 'admin.php?page=pagelines_extend&pageaction=import&imported=true' ) ); 
			} else {
				wp_redirect( admin_url('admin.php?page=pagelines_extend&pageaction=import&error=wrongfile') );
			}
		}		
	}
}

/*
 * Set user/pass using md5()
 *
 */
function set_pagelines_credentials( $user, $pass ) {
	
	if ( !empty( $user ) && !empty( $pass ) )
		update_option( 'pagelines_extend_creds', array( 'user' => $user, 'pass' => md5( $pass ) ) );
}

/*
 * Get username or password
 *
 */
function get_pagelines_credentials( $t ) {
	
	$creds = get_option( 'pagelines_extend_creds', array( 'user' => '', 'pass' => '' ) );
	
	switch( $t ) {
		
		case 'user':
			return $creds['user'];
		break;

		case 'pass':
			return $creds['pass'];
		break;
	}
}

/*
 * Check updates status including errors and licence information.
 *
 */
function pagelines_check_credentials( $type = 'setup' ) {
	
	switch( $type ) {
		
		case 'setup':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['credentials']) && $a['credentials'] === 'true' )
				return true;
			else
				return false;		
		break;
		
		case 'licence':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['licence']) )
				return $a['licence'];
		break;
		
		case 'error':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['api_error']) )
				return $a['api_error'];
		break;
		
		case 'ssl':
			if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['ssl']) )
				return true;
		break;
		
		case 'echo':
			return get_transient( EXTEND_UPDATE );
		break;
		
		case 'message':
		if ( is_array( $a = get_transient( EXTEND_UPDATE ) ) && isset($a['message']) )
			return $a['message'];
	}
}