<?php

/**
 * 
 * This file is for functions designed to make PageLines theming easier
 * 
 **/

/**
 * Uses controls to find and retrieve the appropriate option value
 * 
 * @param 'panel' the id of the option tab
 * @param 'option_id' the id of the individual setting
 * @param 'keep' whether to keep the default options settings at runtime
 * e.g. keep default color control settings although this panel won't be shown in admin.
 * 
 **/

function pagelines_disable_settings( $args ){

	global $disabled_settings;
	
	$defaults = array(
		'option_id'	=> false,
		'panel'		=> '', 
		'keep'		=> false
	);
	$args = wp_parse_args( $args, $defaults );
	$disabled_settings[$args['panel']] = $args;
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

// =========================
// = Integration functions =
// =========================

function pl_integration_parse( $args ) {
	
	$defaults = array(
		
		'buffer'=>	'',
		'area'	=>	'head',
		'type'	=>	'css'
	);

	$args = wp_parse_args( $args, $defaults );
		
	if ( $args['area'] == 'head' && $args['buffer'] ) {
	
		switch( $args['type'] ) {
			
			case 'css':
				preg_match_all( '#<link rel=[\'|"]stylesheet[\'|"].*\/>#', $args['buffer'], $styles );
				preg_match_all( '#<style type=[\'|"]text\/css[\'|"].*<\/style>#ms', $args['buffer'], $xtra_styles );
				$styles = array_merge( $styles[0], $xtra_styles[0] );
				if ( is_array( $styles ) ) {
					$css = '';
					foreach( $styles as $style )
						$css .= $style . "\n";
					return $css;
				}
			break;
			
			case 'js':
				preg_match_all( '#<script type=[\'|"]text\/javascript[\'|"].*<\/script>#', $args['buffer'], $js );
				if( is_array( $js[0] ) ) {
					$js_out = '';
					foreach( $js[0] as $j )
						$js_out .= $j . "\n";
				return $js_out;
				}
			break;

			case 'divs':
				preg_match( '/<div.*>/ms',$args['buffer'], $divs );
				return ( isset( $divs[0] ) ) ? $divs[0] : '';
			break;

			default:
				return false;
			break;
		} // switch

	} //end if
}