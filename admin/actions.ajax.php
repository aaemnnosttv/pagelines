<?php

/**
 * Ajax Save Options Callback
 *
 * @package PageLines Framework
 * @since   ...
 *
 */
function pagelines_ajax_save_option_callback()
{
	$option_name  = $_POST['option_name'];
	$option_value = $_POST['option_value'];

	update_option( $option_name, $option_value );
	die();
}
add_action( 'wp_ajax_pagelines_ajax_save_option', 'pagelines_ajax_save_option_callback' );


/**
 * Ajax Callback
 *
 * AJAX OPTION SAVING
 * Used to save via AJAX theme options and image uploads
 *
 * @package PageLines Framework
 * @since 1.2.0
 */
function pagelines_ajax_callback()
{
	$save_type  = ( $_POST['type'] ) ? $_POST['type'] : null;
	$setting    = $_POST['setting'];
	$button_id  = $_POST['oid'];

	$pieces     = explode( 'OID', $_POST['oid'] );
	$oid        = $pieces[0];
	$parent_oid = ( isset($pieces[1]) ) ? $pieces[1] : null;

	// Uploads
	if ( $save_type == 'upload' )
	{
		$arr_file_type = wp_check_filetype( basename( $_FILES[$button_id]['name'] ));

		$uploaded_file_type = $arr_file_type['type'];

		// Set an array containing a list of acceptable formats
		$allowed_file_types = array( 'image/jpg','image/jpeg','image/gif','image/png', 'image/x-icon');

		if ( in_array( $uploaded_file_type, $allowed_file_types ) )
		{
			$filename = $_FILES[ $button_id ];
			$filename['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $filename['name'] );

			$override['test_form'] = false;
			$override['action'] = 'wp_handle_upload';

			$uploaded_file = wp_handle_upload( $filename, $override );

			$upload_tracking[] = $button_id;

			plupop( $oid, $uploaded_file['url'], array( 'setting' => $setting, 'parent' => $parent_oid ) );

			$name = 'PageLines- ' . addslashes( $filename['name'] );

			$attachment = array(
							'guid'           => $uploaded_file['url'],
							'post_mime_type' => $uploaded_file_type,
							'post_title'     => $name,
							'post_content'   => '',
							'post_status'    => 'inherit'
						);

			$attach_id = wp_insert_attachment( $attachment, $uploaded_file['file'] );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $uploaded_file['file'] );

			wp_update_attachment_metadata( $attach_id,  $attach_data );
		}
		else
			$uploaded_file['error'] = __( 'Unsupported file type!', 'pagelines' );

		if ( !empty( $uploaded_file['error'] ) )
			echo sprintf( __('Upload Error: %s', 'pagelines' ) , $uploaded_file['error'] );
		else {
			echo $uploaded_file['url'];
		}
	}
	elseif ( $save_type == 'image_reset' ) {
		plupop( $oid, null, array( 'setting' => $setting, 'parent' => $parent_oid ) );
	}

	die();
}
add_action( 'wp_ajax_pagelines_ajax_post_action', 'pagelines_ajax_callback' );


/**
 * (AJAX) Save Template Map
 *
 * Used to save via AJAX theme options and image uploads
 *
 * @package PageLines Framework
 * @since   ...
 *
 * @uses save_tempalte_map
 */
function ajax_save_template_map()
{
	ddlog( $_POST );

    /** Full Template Map */
    $templatemap = get_option( PAGELINES_TEMPLATE_MAP );

    /** Order of the sections */
    $section_order =  $_POST['orderdata'];

    /** Get array / variable format */
    parse_str( $section_order );

    /** Selected Template */
    $selected_template = esc_attr( $_POST['template'] );

    /** Explode by hyphen to get heirarchy */
    $template_heirarchy = explode( '-', $selected_template );

    if ( isset( $template_heirarchy[1] ) )
        $templatemap[ $template_heirarchy[0] ]['templates'][ $template_heirarchy[1] ]['sections'] = urlencode_deep( $section );
    else
        $templatemap[$selected_template]['sections'] = $section;

    ddlog( $templatemap );

    //save_template_map( $templatemap );

	pl_purge_css();

    die();
}
add_action( 'wp_ajax_pagelines_save_sortable', 'ajax_save_template_map' );