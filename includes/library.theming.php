<?php

/**
 * 
 * This file is for functions designed to make PageLines theming easier
 * 
 **/

/**
 * Uses controls to find and retrieve the appropriate option value
 * 
 * @param 'key' the id of the option
 * 
 **/
function pagelines_disable_settings( $key ){

	global $disabled_settings;
	
	$disabled_settings[] = $key;

}