<?php
/**
 * Layout functions.
 *
 * Useful function for developers.
 *
 * @package PageLines Framework
 * @author PageLines
 **/

/**
 * Get current page mode
 *
 * @return str
 **/
function pl_layout_mode() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_mode;
}

/**
 * Get current page width
 *
 * @return int
 **/
function pl_page_width() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map['content_width'];
}

/**
 * Get current page responsive width
 *
 * @return int
 **/
function pl_responsive_width() {
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map['responsive_width'];
}

/**
 * Get current page content width
 *
 * @return int
 **/
function pl_content_width() {
	
	$mode = pl_layout_mode();
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map[ $mode ][ 'maincolumn_width' ];
}

/**
 * Get current page primary sidebar width
 *
 * @return int
 **/
function pl_sidebar_width() {
	
	$mode = pl_layout_mode();
	
	$layout = pagelines_layout_library_data();	
	return $layout->layout_map[ $mode ][ 'primarysidebar_width' ];
}

/**
 * Get current page secondary sidebar width
 *
 * @return int
 **/
function pl_secondary_sidebar_width() {

	$width = pl_page_width() - pl_sidebar_width() - pl_content_width();
		
	return $width;
}

/**
 * Get current page full layout data
 *
 * @return array
 **/
function pagelines_layout_library_data() {
		
		global $pagelines_layout;

		if ( !is_object( $pagelines_layout ) )
			build_pagelines_layout();
		
		return $pagelines_layout;	
}