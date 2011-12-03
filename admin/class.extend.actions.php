<?php
/**
 * Extend Actions
 * 
 * @author PageLines
 *
 * @since 2.0.b3
 */

 class PageLinesExtendActions {
	
	
	function __construct() {

		$this->ui = new PageLinesExtendUI;

		add_action( 'wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback' ) );	
		add_action( 'admin_init', array(&$this, 'extension_uploader' ) );
		add_action( 'admin_init', array(&$this, 'check_creds' ) );
 	}

	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback( $uploader = false, $checked = null ) {

		// 2. Variable Setup
			$mode =  $_POST['extend_mode'];
			$type =  $_POST['extend_type'];
			$file =  $_POST['extend_file'];
			$path =  $_POST['extend_path'];
			$product = $_POST['extend_product'];

		// 3. Do our thing...

		switch ( $mode ) {

			case 'integration_download':
			
				$this->integration_download( $type, $file, $path, $uploader, $checked );

			break;

			case 'integration_activate':

				integration_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'integration_deactivate':

				integration_deactivate( $type, $file, $path, $uploader, $checked );

			break;			

			case 'plugin_install': 
				
				$this->plugin_install( $type, $file, $path, $uploader, $checked );
					
			break;

			case 'plugin_upgrade':

				$this->plugin_upgrade( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'plugin_delete':
				
				$this->plugin_delete( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'plugin_activate':

				$this->plugin_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'plugin_deactivate':

				$this->plugin_deactivate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_install':

				$this->section_install( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_upgrade':

				$this->section_upgrade( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_delete':

				$this->section_delete( $type, $file, $path, $uploader, $checked );

			break;

			case 'section_activate':

				$this->section_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'section_deactivate':

				$this->section_deactivate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'theme_install':

				$this->theme_install( $type, $file, $path, $uploader, $checked );
	
			break;

			case 'theme_upgrade':

				$this->theme_upgrade( $type, $file, $path, $uploader, $checked );

			break;					

			case 'theme_delete':

				$this->theme_delete( $type, $file, $path, $uploader, $checked );

			break;

			case 'theme_activate':

				$this->theme_activate( $type, $file, $path, $uploader, $checked );
	
			break;

			case 'theme_deactivate':

				$this->theme_deactivate( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'redirect':

				$this->redirect( $type, $file, $path, $uploader, $checked );

			break;

			case 'purchase':

				$this->purchase( $type, $file, $path, $uploader, $checked );

			break;

			case 'login':

				$this->login( $type, $file, $path, $uploader, $checked );
				
			break;
			
			case 'version_fail':
			
				$this->version_fail( $type, $file, $path, $uploader, $checked );

			break;
			
			case 'depends_fail':
			
				$this->depends_fail( $type, $file, $path, $uploader, $checked );

			break;
			
		}
		die(); // needed at the end of ajax callbacks
	}

	/**
	 * Uploader for sections.
	 * 
	 */
	function extension_uploader() {
		
		if ( !empty($_POST['upload_check'] ) && check_admin_referer( 'pagelines_extend_upload', 'upload_check') ) {

			if ( $_FILES[ $_POST['type']]['size'] == 0 ) {
				$this->page_reload( 'pagelines_extend&extend_error=blank', null, 0 );
				exit();
			}

			// right we made it this far! It needs to be a section!
			$type = $_POST['type'];
			$filename = $_FILES[ $type ][ 'name' ];
			$payload = $_FILES[ $type ][ 'tmp_name' ];
				
			if ( !preg_match( '/section-([^\.]*)\.zip/i', $filename, $out ) ) {
				$this->page_reload( 'pagelines_extend&extend_error=filename', null, 0 );
				exit();
			}

			$uploader = true;
			$_POST['extend_mode']	= 'section_install';
			$_POST['extend_file']	= $payload;
			$_POST['extend_path']	= $out[1];
			$_POST['extend_type']	= 'section';
			$_POST['extend_product']= '';
			$this->extend_it_callback( $uploader, null );
			exit;
		}	
	}
	
	/**
	 * See if we have filesystem permissions.
	 * 
	 */	
	function check_creds( $extend = null, $context = WP_PLUGIN_DIR ) {

		if ( isset( $_GET['creds'] ) && $_POST && WP_Filesystem( $_POST ) )
			$this->extend_it_callback( false, true );
			
		if ( !$extend )
			return;			

		if ( false === ( $creds = @request_filesystem_credentials( admin_url( 'admin.php?page=pagelines_extend&creds=yes' ), $type = "", $error = false, $context, $extra_fields = array( 'extend_mode', 'extend_type', 'extend_file', 'extend_path', 'extend_product' ) ) ) ) {
			exit; 
		}	
	}
	
	
	// return true if were NOT using direct fs.
	function get_fs_method(){
		
		global $wp_filesystem;
		
		if ( is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' )
			return true;
		else
			return false;
	}
	
	
	/**
	 * Generate a download link.
	 * 
	 */
	function make_url( $type, $file, $product = null ) {
		
		return sprintf( '%s%ss/download.php?d=%s.zip%s', PL_API_FETCH, $type, $file, ( isset( $product ) ) ? '&product=' . $product : '' );
		
	}
	
	/**
	 * Get a PayPal link.
	 * 
	 */
	function get_payment_link( $product ) {
		
		return sprintf( 'https://pagelines.com/api/?paypal=%s|%s', $product, admin_url( 'admin.php' ) );
	}
	
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location, $product = null, $message = '') {
		
		if ( $this->get_fs_method() ) {
			
			$time = 0;
		} else {
			$time = 700;
			echo $message;
		}
		
		$r = rand( 1,100 );
		
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );
		
		$location = ( $product ) ? self::get_payment_link( $product ) : $admin;

		printf( 
			'<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', 
			$location, 
			$time 
		);
		
 	}

 	function int_download( $location, $time = 300 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, 'pagelines_extend#integrations' ) );
		printf( '<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );	
		printf( '<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $admin, 700 );
 	}

	function sandbox( $file, $type ) {

		register_shutdown_function( array(&$this, 'error_handler'), $type );
		@include_once( $file );
	}

	/**
	 * Throw up on error
	 */
	function error_handler( $type ) { 
		
		$a = error_get_last();
		
		$error = '';
		
		// Unable to activate
		if( $a['type'] == 4 || $a['type'] == 1  )
			$error .= sprintf( 'Unable to activate the %s.', $type );
		
		//Error on line
		if( $error && PL_DEV )
			$error .= sprintf( '<br />%s in %s on line: %s', $a['message'], basename( $a['file'] ), $a['line'] );
		
		echo $error;
	}
	
	
	/**
	 * Provide Download to integration
	 */
	function integration_download( $type, $file, $path, $uploader, $checked ) {
		
		$url = $this->make_url( $type, $file );
	
		echo __( 'Downloaded', 'pagelines' );
	
		$this->int_download( $url );
		
	}
	
	
	/**
	 * Activate Integration Options
	 */
	
	function version_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'You need to have version %s of the framework for this %s', 'pagelines' ), $file, $path );
		
	}
	
	function depends_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'You need to install %s first.', 'pagelines' ), $file );
		
	}
	
	function plugin_install( $type, $file, $path, $uploader, $checked ) {
		

		$this->wp_libs();
			
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;
		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$destination = ( ! $uploader ) ? $this->make_url( $type, $file ) : $file;						
		@$upgrader->install( $destination );

		$this->sandbox( WP_PLUGIN_DIR . $path, 'plugin' );
		activate_plugin( $path );
		
		$message = __( 'Plugin Installed.', 'pagelines' );		
		$text = '&extend_text=plugin_install#added';
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function plugin_delete( $type, $file, $path, $uploader, $checked ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;
		delete_plugins( array( ltrim( $file, '/' ) ) );
		$message = __( 'Plugin Deleted.', 'pagelines' );		
		$text = '&extend_text=plugin_delete#added';
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function plugin_upgrade( $type, $file, $path, $uploader, $checked ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend' );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$active = is_plugin_active( ltrim( $file, '/' ) );
		deactivate_plugins( array( $file ) );

		if ( $this->get_fs_method() )
			$wp_filesystem->delete( trailingslashit( WP_PLUGIN_DIR ) . $path, true, false  );
		else
			extend_delete_directory( trailingslashit( WP_PLUGIN_DIR ) . $path );
		@$upgrader->install( $this->make_url( $type, $path ) );
		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin');
		if ( $active )
			activate_plugin( ltrim( $file, '/' ) );
		// Output
		$message = __( 'Plugin Upgraded.', 'pagelines' );
		$text = '&extend_text=plugin_upgrade#added';
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function plugin_activate( $type, $file, $path, $uploader, $checked ) {
		
		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin' );
	 	activate_plugin( $file );
	 	$message = __( 'Plugin Activated.', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend', null, $message );
	
	}
	
	function plugin_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		deactivate_plugins( array( $file ) );
		// Output
 		$message = __( 'Plugin Deactivated.', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend', null, $message );

	}
	
	function section_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$time = 0;
		$url = ( $uploader ) ? $file : $this->make_url( 'section', $path );
		if ( $this->get_fs_method() ) {
			$url = ( $uploader ) ? $file : $this->make_url( 'section', $path );
			$out = @$upgrader->install( $url );		
			$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $path, trailingslashit( PL_EXTEND_DIR ) . $path );
			$message = '';				
		} else {
					$options = array( 'package' => $url, 
					'destination'		=> trailingslashit( PL_EXTEND_DIR ) . $path, 
					'clear_destination' => false,
					'clear_working'		=> false,
					'is_multi'			=> false,
					'hook_extra'		=> array() 
			);
			$out = @$upgrader->run( $options );
			if ( ! $uploader )
				$message = __( 'Section Installed', 'pagelines' );	
		}
		$text = '&extend_text=section_install#added';
		if ( $uploader && is_wp_error( $out ) )
			$this->page_reload( sprintf( 'pagelines_extend&extend_error=%s', $out->get_error_code() ) , null, 0 );
		else
			$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function section_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		}
		global $wp_filesystem;

		if ( $this->get_fs_method() )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );

		$message = __( 'Section Deleted.', 'pagelines' );
		$text = '&extend_text=section_delete#added';
			$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function section_upgrade( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);

		if ( $this->get_fs_method() )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false  );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );				

		if ( $this->get_fs_method() ) {
			@$upgrader->install( $this->make_url( 'section', $file ) );			
			$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $file, trailingslashit( PL_EXTEND_DIR ) . $file );			
		} else {
					$options = array( 'package' => ( ! $uploader) ? $this->make_url( 'section', $file ) : $file, 
					'destination'		=> ( ! $uploader) ? trailingslashit( PL_EXTEND_DIR ) . $file : trailingslashit( PL_EXTEND_DIR ) . $path, 
					'clear_destination' => false,
					'clear_working'		=> false,
					'is_multi'			=> false,
					'hook_extra'		=> array() 
			);
			@$upgrader->run($options);	
		}
		// Output
		$message = __( 'Section Upgraded', 'pagelines' );
		$text = '&extend_text=section_upgrade#added';
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function section_activate( $type, $file, $path, $uploader, $checked ) {

		$this->sandbox( $path, 'section' );
		$available = get_option( 'pagelines_sections_disabled' );
		unset( $available[$path][$file] );
		update_option( 'pagelines_sections_disabled', $available );
		// Output
	 	$message = __( 'Section Activated.', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend', null, $message );
		
	}
	
	function section_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array() ) );
		$disabled[$path][$file] = true; 
		update_option( 'pagelines_sections_disabled', $disabled );
		// Output
	 	$message = __( 'Section Deactivated.', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend', null, $message );
		
	}
	
	function theme_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );
		}			
		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );
		global $wp_filesystem;
		@$upgrader->install( $this->make_url( $type, $file, $product ) );

		// Output
		$text = '&extend_text=theme_install#added';
		$message = __( 'Theme Installed.', 'pagelines' );
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function theme_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
		}
		global $wp_filesystem;
		if ( $this->get_fs_method() )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );

		$text = '&extend_text=theme_delete#added';
		$message = __( 'Theme Deleted.', 'pagelines' );
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function theme_upgrade( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
			
		if ( !$checked )
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
		global $wp_filesystem;

		$active = ( basename( get_stylesheet_directory()  ) === $file ) ? true : false;

		if ( $active )
			switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Theme_Upgrader( $skin );

		if ( $this->get_fs_method() )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );

		@$upgrader->install( $this->make_url( $type, $file ) );

		if ( $active )
			switch_theme( basename( get_template_directory() ), $file );
		// Output
		$text = '&extend_text=theme_upgrade#added';
		$message = __( 'Theme Upgraded.', 'pagelines' );
		$this->page_reload( 'pagelines_extend' . $text, null, $message );
		
	}
	
	function theme_activate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), $file );
		delete_transient( 'pagelines_sections_cache' );

		$message = __( 'Theme Activated.', 'pagelines' );
		$this->page_reload( 'pagelines_extend', null, $message );
		
	}
	
	function theme_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
		delete_transient( 'pagelines_sections_cache' );
		
		$message = __( 'Theme Deactivated.', 'pagelines' );
		$this->page_reload( 'pagelines_extend', null, $message );
		
	}
	
	function redirect( $type, $file, $path, $uploader, $checked ) {
		
		echo sprintf( __( 'Sorry only network admins can install %s.', 'pagelines' ), $type );		
		
	}
	
	function purchase( $type, $file, $path, $uploader, $checked ) {
		
		_e( 'Taking you to PayPal.com', 'pagelines' );
		$this->page_reload( 'pagelines_extend', $file );
		
	}
	
	function login( $type, $file, $path, $uploader, $checked ) {
		
		_e( 'Moving to account setup..', 'pagelines' );
		$this->page_reload( 'pagelines_account#Your_Account' );
		
	}
	
	function wp_libs() {
		
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include( PL_ADMIN . '/library.extension.php' );
		
	}	
}