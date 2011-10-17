<?php

/**
 * 
 * This file is for functions designed to make PageLines theming easier
 * 
 **/

/**
 * Uses controls to find and retrieve the appropriate option value
 * 
 * @param 'key' the id of the option tab
 * @param 'keep' whether to keep the default options settings at runtime
 * e.g. keep default color control settings although this panel won't be shown in admin.
 * 
 **/
function pagelines_disable_settings( $key, $keep = false ){

	global $disabled_settings;
	
	$disabled_settings[$key] = array(
		'slug'	=> $key, 
		'keep'	=> $keep
	);

}

function pl_default_setting( $args ){
	
	if(pagelines_activate_or_reset()){
	
		global $new_default_settings;
	
		$default = array(
			'key'		=> '', 
			'value'		=> '', 
			'parent'	=> null,
			'subkey'	=> null, 
			'setting'	=> PAGELINES_SETTINGS,
		); 
	
		$set = wp_parse_args($args, $default);
	
		$new_default_settings[]  = $set;

	
	}
	
}

function pagelines_activate_or_reset(){
	
	$activated 	= ( isset($_GET['activated']) && $_GET['activated'] ) ? true : false;
	$reset 		= ( isset($_GET['reset']) && $_GET['reset'] ) ? true : false;
	
	if( $activated || $reset ){
		
		if( $activated )
			return 'activated';
		elseif( $reset )
		 	return 'reset';
		
	}else 
		return false;
	
}