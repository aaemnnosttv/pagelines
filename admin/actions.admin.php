<?php


// ===============================================
// = Show Options Panel after theme activation   =
// ===============================================
if(is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {

	//Do redirect
	header( 'Location: '.admin_url().'admin.php?page=pagelines&activated=true&pageaction=activated' ) ;

}


/**
 * 
 *  Checks if PHP5
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.0.0
 *
 */
add_action('pagelines_before_optionUI', 'pagelines_check_php');
function pagelines_check_php(){
	if(floatval(phpversion()) < 5.0){
		_e('<div class="config-error"><h2>PHP Version Problem</h2>Looks like you are using PHP version: <strong>'.phpversion().'</strong>. To run this framework you will need PHP <strong>5.0</strong> or better...<br/><br/> Don\'t worry though! Just check with your host about a quick upgrade.</div>', 'pagelines');
	}

}

// ====================================================================================
// = AJAX OPTION SAVING - Used to save via AJAX theme options and image uploads 
// ====================================================================================

	add_action('wp_ajax_pagelines_ajax_post_action', 'pagelines_ajax_callback');

	function pagelines_ajax_callback() {
		global $wpdb; // this is how you get access to the database
	
		if($_POST['type']){
			$save_type = $_POST['type'];
		}else $save_type = null;
	
		//Uploads
		if( $save_type == 'upload' ) {
		$clickedID = $_POST['data']; // Acts as the name

		$arr_file_type = wp_check_filetype( basename( $_FILES[$clickedID]['name']));
		$uploaded_file_type = $arr_file_type['type'];
		// Set an array containing a list of acceptable formats
		$allowed_file_types = array( 'image/jpg','image/jpeg','image/gif','image/png', 'image/x-icon' );
		if( in_array( $uploaded_file_type, $allowed_file_types ) ) {

			$filename = $_FILES[$clickedID];
			$filename['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $filename['name'] ); 
			$override['test_form'] = false;
			$override['action'] = 'wp_handle_upload';    
			$uploaded_file = wp_handle_upload( $filename, $override );
			$upload_tracking[] = $clickedID;
			pagelines_update_option( $clickedID , $uploaded_file['url'] );

			$name = 'PageLines- ' . addslashes( $filename['name'] );
			$old_file = $wpdb->get_row("
				SELECT ID
				FROM {$wpdb->posts}
				WHERE post_title = '{$name}'
				AND post_type = 'attachment'
				");

			if ( $old_file ) wp_delete_attachment( $old_file->ID );

			$attachment = array(
                                'post_mime_type' => $uploaded_file_type,
                                'post_title' => $name,
                                'post_content' => '',
                                'post_status' => 'inherit'
                        );

			$attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );
			wp_update_attachment_metadata( $attach_id,  $attach_data );
			} else {
				$uploaded_file['error'] = 'Unsupported file type!';
		}
		if( !empty( $uploaded_file['error'] ) ) {
			echo 'Upload Error: ' . $uploaded_file['error'];
		} else {
			echo $uploaded_file['url']; // Is the Response
			}
		}
		elseif( $save_type == 'image_reset' ){
			$id = $_POST['data']; // Acts as the name
			pagelines_update_option( $id, null );
		}
		die();
	}
	
// ====================================================================================
// = AJAX TEMPLATE MAP SAVING - Used to save via AJAX theme options and image uploads =
// ====================================================================================

	add_action('wp_ajax_pagelines_save_sortable', 'ajax_save_template_map');

	function ajax_save_template_map() {
		global $wpdb; // this is how you get access to the database
		
		
		/* Full Template Map */
		
		$templatemap = get_option('pagelines_template_map');
		
		/* Order of the sections */
		$section_order =  $_GET['orderdata'];
		
		/* Get array / variable format */
		parse_str($section_order);
		
		/* Selected Template */
		$selected_template = esc_attr($_GET['template']);
		
			/* Explode by slash to get heirarchy */
			$template_heirarchy = explode('-', $selected_template);
			
			if(isset($template_heirarchy[1])){
				$templatemap[$template_heirarchy[0]]['templates'][$template_heirarchy[1]]['sections'] = urlencode_deep($section);
			} else {
				$templatemap[$selected_template]['sections'] = $section;
			}
		
		
		save_template_map($templatemap);
		
		echo true;
		
		die();
	}
	
	add_action('pagelines_ajax_save_option', 'pagelines_ajax_save_option_callback');
	function pagelines_ajax_save_option_callback() {
		global $wpdb; // this is how you get access to the database

		$option_name = $_POST['option_name'];
		$option_value = $_POST['option_value'];

		update_option($option_name, $option_value);
		
		die();
	}
	
	
	// Creates Dynamic CSS file when saving with AJAX
	add_action('wp_ajax_pagelines_ajax_create_dynamic_css', 'pagelines_ajax_create_dynamic_css_callback');
	function pagelines_ajax_create_dynamic_css_callback() {
		global $wpdb; // this is how you get access to the database

		pagelines_build_dynamic_css( 'AJAX' );
		
		die();
	}

	
	// Check Platform version with API
	
	add_action( 'admin_init', 'pagelines_check_version' );
	function pagelines_check_version() {
		if ( VPRO ) {
			$pl_update = new PageLinesUpdateCheck( THEMENAME, CORE_VERSION );
			$pl_update->pagelines_theme_check_version();
		}
	}

	add_action( 'admin_init', 'pagelines_check_plugins', 30 );
	function pagelines_check_plugins() {
	set_site_transient('update_plugins', null);
		if ( VPRO ) {
			foreach( pagelines_register_plugins() as $plugin ) {
			$pl_update = new PageLinesUpdateCheck( null, null, $plugin );
			$pl_update->pagelines_plugins_check_version();
		}
		}
	}