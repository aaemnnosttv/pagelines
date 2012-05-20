<?php
/**
 * 
 *
 *  Account Handling In Admin
 *
 *
 *
 */


class PageLinesAccount {


	/**
	*
	* @TODO document
	*
	*/
	function __construct(){
		
		add_action( 'admin_init', array(&$this, 'update_lpinfo' ) );
		
	}
	
	/**
	 * Save our credentials
	 * 
	 */	

	/**
	*
	* @TODO document
	*
	*/
	function update_lpinfo() {

		if ( isset( $_POST['form_submitted'] ) && $_POST['form_submitted'] === 'plinfo' ) {

			if ( isset( $_POST['creds_reset'] ) )
				update_option( 'pagelines_extend_creds', array( 'user' => '', 'pass' => '' ) );
			else
				set_pagelines_credentials( $_POST['lp_username'], $_POST['lp_password'] );

			PagelinesExtensions::flush_caches();		

			wp_redirect( PLAdminPaths::account( '&plinfo=true' ) );

			exit;
		}
	}
}

/**
 *
 *  Returns Extension Array Config
 *
 */
function pagelines_account_array(  ){

	$d = array(
		'Your_Account'		=> array(
			'icon'			=> PL_ADMIN_ICONS.'/rocket-fly.png',
			'credentials' 	=> array(
				'type'		=> 'updates_setup',
				'title'		=> __( 'Configure PageLines Account &amp; Auto Updates', 'pagelines' ),
				'shortexp'	=> __( 'Get your latest updates automatically, direct from PageLines.', 'pagelines' ),
				'layout'	=> 'full',
			)
		),
		'Import-Export'		=> array(
			'icon'			=> PL_ADMIN_ICONS.'/extend-inout.png',
			'import_set'	=> array(
				'default'	=> '',
				'type'		=> 'import_export',
				'layout'	=> 'full',
				'title'		=> __( 'Import/Export PageLines Settings', 'pagelines' ),						
				'shortexp'	=> __( 'Use this form to upload PageLines settings from another install.', 'pagelines' ),
			)
		)
	);
	return apply_filters( 'pagelines_account_array', $d ); 
}

function pagelines_plus_array(  ){

	$d = array(
		'PageLines_Plus'		=> array(
			'icon'			=> PL_ADMIN_ICONS.'/rocket-fly.png',
			'plus_welcome' 	=> array(
				'type'		=> 'plus_welcome',
//				'title'		=> __( 'Sup bitches! You have the Plus!', 'pagelines' ),
//				'shortexp'	=> __( 'All this is FREE!.', 'pagelines' ),
				'layout'	=> 'full',
			)
		),
		'Support'		=> array(
			'icon'			=> PL_ADMIN_ICONS.'/extend-inout.png',
			'plus_support'	=> array(
				'default'	=> '',
				'type'		=> 'plus_support',
				'layout'	=> 'full',
				'title'		=> __( 'Support stuff', 'pagelines' ),						
				'shortexp'	=> __( 'Blah blah...', 'pagelines' ),
			)
		)
	);
	return apply_filters( 'pagelines_plus_array', $d ); 
}