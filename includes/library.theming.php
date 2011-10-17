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


/**
 * Support a specific section in a child theme
 * 
 * @param 'key' the class name of the section
 * @param 'args' controls on how the section will be supported.
 * 
 **/
function pl_support_section( $args ){

	global $supported_elements;

	$defaults = array(
		
		'class_name'		=> '',
		'disable_color'		=> false,
		'slug'				=> '',
		'supported'			=> true 
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	$supported_elements['sections'][ $args['class_name'] ] = $args;

}

/**
 * Support a specific plugin in a child theme
 * 
 * @param 'key' the slug of the plugin
 * @param 'args' controls on how the plugin will be supported.
 * 
 **/
function pl_support_plugin( $args ){

	global $supported_elements;

	$defaults = array(
		
		'slug'		=> '',
		'supported'		=> true, 
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	$supported_elements['plugins'][ $args['slug'] ] = $args;

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