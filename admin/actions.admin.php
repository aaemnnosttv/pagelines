<?php

/**
 * Show Options Panel after theme activation
 * 
 * @package PageLines Framework
 * @subpackage Redirects
 * @since 1.0.0
 */
if( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" )
	wp_redirect( admin_url( 'admin.php?page=pagelines&activated=true&pageaction=activated' ) );

/**
 * Add Javascript for Layout Controls from the Layout UI class
 * 
 * @package PageLines Framework
 * @subpackage LayoutUI
 * @since 2.0.b3
 */
$layout_control_js = new PageLinesLayoutControl();
add_action( 'pagelines_admin_head', array(&$layout_control_js, 'layout_control_javascript' ) );



/**
 *
 * @TODO document
 *
 */
function pagelines_admin_body_class( $class ){
	
	$class = $class.'pagelines_ui';
	
	return $class;
}
/**
 * 
 * Checks if PHP5
 *
 * @package PageLines Framework
 * @subpackage Functions Library
 * @since 4.0.0
 *
 */
add_action( 'pagelines_before_optionUI', 'pagelines_check_php' );

/**
 *
 * @TODO document
 *
 */
function pagelines_check_php(){
	if( floatval( phpversion() ) < 5.0 ){
		printf( __( "<div class='config-error'><h2>PHP Version Problem</h2>Looks like you are using PHP version: <strong>%s</strong>. To run this framework you will need PHP <strong>5.0</strong> or better...<br/><br/> Don't worry though! Just check with your host about a quick upgrade.</div>", 'pagelines' ), phpversion() );
	}
}

/**
 * AJAX OPTION SAVING - Used to save via AJAX theme options and image uploads
 * 
 * @package PageLines Framework
 * @since 1.2.0
 */
add_action( 'wp_ajax_pagelines_ajax_post_action', 'pagelines_ajax_callback' );

/**
 *
 * @TODO document
 *
 */
function pagelines_ajax_callback() {
	global $wpdb; // this is how you get access to the database


	$save_type = ( $_POST['type'] ) ? $_POST['type'] : null;

	$setting = $_POST['setting'];
	$button_id = $_POST['oid'];

	$pieces = explode( 'OID', $_POST['oid'] );		
	$oid = $pieces[0];
	$parent_oid = ( isset($pieces[1]) ) ? $pieces[1] : null;

	//Uploads
	if( $save_type == 'upload' ) {
	
		$arr_file_type = wp_check_filetype( basename( $_FILES[$button_id]['name'] ) );
		
		$uploaded_file_type = $arr_file_type['type'];
		
		// Set an array containing a list of acceptable formats
		$allowed_file_types = array( 'image/jpg','image/jpeg','image/gif','image/png', 'image/x-icon' );
	
		if( in_array( $uploaded_file_type, $allowed_file_types ) ) {

			$filename = $_FILES[ $button_id ];
			$filename['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $filename['name'] ); 
			
			$override['test_form'] = false;
			$override['action'] = 'wp_handle_upload';    
			
			$uploaded_file = wp_handle_upload( $filename, $override );
			
			$upload_tracking[] = $button_id;
			
			plupop( $oid, $uploaded_file['url'], array( 'setting' => $setting, 'parent' => $parent_oid ) );

			$name = 'PageLines- ' . addslashes( $filename['name'] );

			$attachment = array(
							'post_mime_type'	=> $uploaded_file_type,
							'post_title'		=> $name,
							'post_content'		=> '',
							'post_status'		=> 'inherit'
						);

			$attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			
		} else
			$uploaded_file['error'] = __( 'Unsupported file type!', 'pagelines' );
	
		if( !empty( $uploaded_file['error'] ) )
			echo sprintf( __('Upload Error: %s', 'pagelines' ) , $uploaded_file['error'] );
		else{
			//print_r($r);
			echo $uploaded_file['url']; // Is the Response
		
		}
	} elseif( $save_type == 'image_reset' ){
		plupop( $oid, null, array( 'setting' => $setting, 'parent' => $parent_oid ) );
	}
	
	die();
}
	
// ====================================================================================
// = AJAX TEMPLATE MAP SAVING - Used to save via AJAX theme options and image uploads =
// ====================================================================================

	add_action( 'wp_ajax_pagelines_save_sortable', 'ajax_save_template_map' );


	/**
	*
	* @TODO document
	*
	*/
	function ajax_save_template_map() {
		global $wpdb; // this is how you get access to the database
		
		
		/* Full Template Map */
		
		$templatemap = get_option( PAGELINES_TEMPLATE_MAP );
		
		/* Order of the sections */
		$section_order =  $_GET['orderdata'];
		
		/* Get array / variable format */
		parse_str( $section_order );
		
		/* Selected Template */
		$selected_template = esc_attr( $_GET['template'] );
		
			/* Explode by slash to get heirarchy */
			$template_heirarchy = explode( '-', $selected_template );
			
			if( isset($template_heirarchy[1]) )
				$templatemap[$template_heirarchy[0]]['templates'][$template_heirarchy[1]]['sections'] = urlencode_deep( $section );
			else
				$templatemap[$selected_template]['sections'] = $section;
			
		
		save_template_map( $templatemap );
		
		echo true;
		
		die();
	}
	
add_action( 'wp_ajax_pagelines_ajax_save_option', 'pagelines_ajax_save_option_callback' );

/**
 *
 * @TODO document
 *
 */
function pagelines_ajax_save_option_callback() {
	global $wpdb; // this is how you get access to the database
	$option_name = $_POST['option_name'];
	$option_value = $_POST['option_value'];

	update_option( $option_name, $option_value );

	die();
}


// Check Framework version with API

add_action( 'admin_init', 'pagelines_check_version' );

/**
 *
 * @TODO document
 *
 */
function pagelines_check_version() {
		global $pl_update;
		$pl_update = new PageLinesUpdateCheck( CORE_VERSION );
		$pl_update->pagelines_theme_check_version();
}

// Load Inline help system.

add_action( 'admin_init', 'pagelines_inline_help' );

/**
 *
 * @TODO document
 *
 */
function pagelines_inline_help() {
	
	$pl_help = new PageLines_Inline_Help;
}


/**
 * Add custom columns to page/post views.
 *
 */
add_filter('manage_edit-page_columns', 'pl_page_columns');

/**
 *
 * @TODO document
 *
 */
function pl_page_columns($columns) {

	if ( ploption( 'enable_template_view_page' ) )
    	$columns['template'] = 'PageLines Template';

	if ( ploption( 'enable_feature_view_page' ) )
    	$columns['feature'] = 'Featured Image';
	return $columns;
}

add_filter('manage_edit-post_columns', 'pl_post_columns');

/**
 *
 * @TODO document
 *
 */
function pl_post_columns($columns) {

	if ( ploption( 'enable_feature_view_post' ) )
    	$columns['feature'] = 'Featured Image';
	return $columns;
}

add_action('manage_posts_custom_column',  'pl_posts_show_columns');

/**
 *
 * @TODO document
 *
 */
function pl_posts_show_columns($name) {
    global $post;
    switch ($name) {
	
		case 'feature':
			if( has_post_thumbnail( $post->ID )) {
				the_post_thumbnail( array(48,48) );
			}
		
		break;		
    }
}

add_action('manage_pages_custom_column',  'pl_page_show_columns');

/**
 *
 * @TODO document
 *
 */
function pl_page_show_columns($name) {
    global $post;
    switch ($name) {
        case 'template':
            $template = get_post_meta( $post->ID, '_wp_page_template', true );
            
			if ( 'default' == $template ) {	
				_e( 'Default', 'pagelines' );
				break;
			}

			$file = sprintf( '%s/%s', PARENT_DIR, $template );
			
			if ( !file_exists( $file ) )
				$file = sprintf( '%s/%s', CHILD_DIR, $template );
			
			if ( !file_exists( $file ) ) {
				_e( 'Error', 'pagelines' );
				break;
			}
				
			$data = pl_file_get_contents( $file );
			
			preg_match( '/Template Name:(.*)/', $data, $out );
			
			if ( isset( $out[1] ) )
				$template = $out[1];
			else
				$template = __( 'Default', 'pagelines' );
			
			echo $template;
		break;
		
		case 'feature':
			if( has_post_thumbnail( $post->ID )) {
				the_post_thumbnail( array(48,48) );
			}
		
		break;		
    }
}