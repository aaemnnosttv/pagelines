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

		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		$this->username = get_pagelines_credentials( 'user' );
		$this->password = get_pagelines_credentials( 'pass' );

		$this->ui = new PageLinesExtendUI;

		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));	
		add_action( 'admin_init', array(&$this, 'extension_uploader' ) );
		add_action( 'admin_init', array(&$this, 'check_creds' ) );

 	}

	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback( $uploader = false, $checked = null) {

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

				$this->integration_activate( $type, $file, $path, $uploader, $checked );
				
			break;

			case 'integration_deactivate':

				$this->integration_deactivate( $type, $file, $path, $uploader, $checked );

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
				$this->page_reload( 'pagelines_extend&extend_error=blank', null, 0);
				exit();
			}

			// right we made it this far! It needs to be a section!
			$type = $_POST['type'];
			$filename = $_FILES[ $type ][ 'name' ];
			$payload = $_FILES[ $type ][ 'tmp_name' ];
				
			if ( !preg_match( '/section-([^\.]*)\.zip/i', $filename, $out) ) {
				$this->page_reload( 'pagelines_extend&extend_error=filename', null, 0);
				exit();
			}

			$uploader = true;
			$_POST['extend_mode']	=	'section_install';
			$_POST['extend_file']	=	$payload;
			$_POST['extend_path']	= 	$out[1];
			$_POST['extend_type']	=	'section';
			$_POST['extend_product']=	'';
			$this->extend_it_callback( $uploader, null );
			exit;
		
		}	
	}
	
	/**
	 * See if we have filesystem permissions.
	 * 
	 */	
	function check_creds( $extend = null, $context = WP_PLUGIN_DIR) {

		if ( isset( $_GET['creds'] ) && $_POST && WP_Filesystem($_POST) )
			$this->extend_it_callback( false, true );
			
		if ( !$extend )
			return;			

		if (false === ($creds = @request_filesystem_credentials(admin_url( 'admin.php?page=pagelines_extend&creds=yes'), $type = "", $error = false, $context, $extra_fields = array( 'extend_mode', 'extend_type', 'extend_file', 'extend_path')) ) ) {
			exit; 
		}	
	}
	
	/**
	 * Generate a download link.
	 * 
	 */
	function make_url( $type, $file, $product = null ) {
		
		return sprintf('%s%ss/download.php?d=%s.zip%s', PL_API_FETCH, $type, $file, (isset( $product ) ) ? '&product=' . $product : '' );
		
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
 	function page_reload( $location, $product = null, $time = 700 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );
		$location = ( $product ) ? $this->get_payment_link( $product ) : $admin;

		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );
 	}

 	function int_download( $location, $time = 300 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, 'pagelines_extend#integrations' ) );
		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );	
		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $admin, 700 );
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
		$error =  ( $a['type'] == 4 || $a['type'] == 1 ) ? sprintf( 'Unable to activate the %s.', $type ) : '';
		$error .= ( $error && PL_DEV ) ? sprintf( '<br />%s in %s on line: %s', $a['message'], basename( $a['file'] ), $a['line'] ) : '';
		echo $error;
	}
	
	function integration_download( $type, $file, $path, $uploader, $checked ) {
		
		$url = $this->make_url( $type, $file );
		echo __( 'Downloaded', 'pagelines' );
		$this->int_download( $url );
		
	}
	
	function integration_activate( $type, $file, $path, $uploader, $checked ) {
		
		$a = ploption( $file );
		$int = array(
			'version'	=> ( isset( $a['version'] ) ) ? $a['version'] : null,
			'activated'	=> 'true'
		);
		plupop( $file, $int );
		echo __( 'Activated', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend' );
		
	}
	
	function integration_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		$a = ploption( $file );
		$int = array(
			'version'	=> ( isset( $a['version'] ) ) ? $a['version'] : null,
			'activated'	=> 'false'
		);
		plupop( $file, $int );
		echo __( 'Deactivated', 'pagelines' );
		$this->page_reload( 'pagelines_extend' );
		
	}
	
	function version_fail( $type, $file, $path, $uploader, $checked ) {
		
		printf( __( 'You need to have version %s of the framework for this %s', 'pagelines' ), $file, $path);
		
	}
	
	function plugin_install( $type, $file, $path, $uploader, $checked ) {
		

		$this->wp_libs();
			
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;
		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);
		$destination = ( ! $uploader ) ? $this->make_url( $type, $file ) : $file;						
		@$upgrader->install( $destination );

		$this->sandbox( WP_PLUGIN_DIR . $path, 'plugin');
		activate_plugin( $path );
		_e( 'Success', 'pagelines' );		
		$text = '&extend_text=plugin_install#installed';
		$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ) ? 0 : 700; 
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function plugin_delete( $type, $file, $path, $uploader, $checked ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;
		delete_plugins( array( ltrim( $file, '/' ) ) );
		$text = '&extend_text=plugin_delete';
		_e( 'Success', 'pagelines' );
		$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) ? 0 : 700; 
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function plugin_upgrade( $type, $file, $path, $uploader, $checked ) {
		
		$this->wp_libs();
		
		if ( !$checked )
			$this->check_creds( 'extend' );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);

		$active = is_plugin_active( ltrim( $file, '/' ) );
		deactivate_plugins( array( $file ) );

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
			$wp_filesystem->delete( trailingslashit( WP_PLUGIN_DIR ) . $path, true, false  );
		else
			extend_delete_directory( trailingslashit( WP_PLUGIN_DIR ) . $path );
		@$upgrader->install( $this->make_url( $type, $path ) );
		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin');
		if ( $active )
			activate_plugin( ltrim( $file, '/' ) );
		// Output
		_e( 'Success', 'pagelines' );
		$text = '&extend_text=plugin_upgrade';
		$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) ? 0 : 700; 
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function plugin_activate( $type, $file, $path, $uploader, $checked ) {
		
		$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin');
	 	activate_plugin( $file );
	 	_e( 'Activation complete!', 'pagelines' );
	 	$this->page_reload( 'pagelines_extend' );
	
	}
	
	function plugin_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		deactivate_plugins( array( $file ) );
		// Output
 		_e( 'Deactivation complete!', 'pagelines' );
 		$this->page_reload( 'pagelines_extend' );

	}
	
	function section_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', WP_PLUGIN_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);
		$time = 0;
		$url = ( $uploader ) ? $file : $this->make_url( 'section', $path );
		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
			$url = ( $uploader ) ? $file : $this->make_url( 'section', $path );
			$out = @$upgrader->install( $url );		
			$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $path, trailingslashit( PL_EXTEND_DIR ) . $path );					
		} else {
					$options = array( 'package' => $url, 
					'destination'		=> trailingslashit( PL_EXTEND_DIR ) . $path, 
					'clear_destination' => false,
					'clear_working'		=> false,
					'is_multi'			=> false,
					'hook_extra'		=> array() 
			);
			$out = @$upgrader->run($options);
			if ( ! $uploader ) {
				_e( 'Section Installed', 'pagelines' );
				$time = 700;
			}
		}
		$text = '&extend_text=section_install#added';
		if ( $uploader && is_wp_error( $out ) )
			$this->page_reload( sprintf( 'pagelines_extend&extend_error=%s', $out->get_error_code() ) , null, 0);
		else
			$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function section_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		}
		global $wp_filesystem;

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ):
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false  );
			$time = 0;
		else:
			extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );
			$time = 700;
			_e( 'Success', 'pagelines' );
			endif;

		$text = '&extend_text=section_delete';
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function section_upgrade( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked )
			$this->check_creds( 'extend', PL_EXTEND_DIR );		
		global $wp_filesystem;

		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader($skin);

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false  );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );				

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
			@$upgrader->install( $this->make_url( 'section', $file ) );			
			$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $file, trailingslashit( PL_EXTEND_DIR ) . $file );
			$time = 0;				
		} else {
					$options = array( 'package' => ( ! $uploader) ? $this->make_url( 'section', $file ) : $file, 
					'destination'		=> ( ! $uploader) ? trailingslashit( PL_EXTEND_DIR ) . $file : trailingslashit( PL_EXTEND_DIR ) . $path, 
					'clear_destination' => false,
					'clear_working'		=> false,
					'is_multi'			=> false,
					'hook_extra'		=> array() 
			);
			@$upgrader->run($options);
			$time = 700;
			_e( 'Success', 'pagelines');		
		}
		// Output
		$text = '&extend_text=section_upgrade';
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function section_activate( $type, $file, $path, $uploader, $checked ) {

		$this->sandbox( $path, 'section');
		$available = get_option( 'pagelines_sections_disabled' );
		unset( $available[$path][$file] );
		update_option( 'pagelines_sections_disabled', $available );
		// Output
		_e( 'Section Activated!', 'pagelines' );
		$this->page_reload( 'pagelines_extend' );
		
	}
	
	function section_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
		$disabled[$path][$file] = true; 
		update_option( 'pagelines_sections_disabled', $disabled );
		// Output
		_e( 'Section Deactivated.', 'pagelines' );

		$this->page_reload( 'pagelines_extend' );
		
	}
	
	function theme_install( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );
		}			
		$skin = new PageLines_Upgrader_Skin();
		$upgrader = new Theme_Upgrader($skin);
		global $wp_filesystem;
		@$upgrader->install( $this->make_url( $type, $file, $product ) );

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ):
			$time = 0;
		else:
			$time = 700;
			_e( 'Success', 'pagelines' );
		endif;
		// Output
		$text = '&extend_text=theme_install#installed';
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function theme_delete( $type, $file, $path, $uploader, $checked ) {

		$this->wp_libs();
				
		if ( !$checked ) {
			$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
		}
		global $wp_filesystem;
		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false  );
		else
			extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );
		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ):
			$time = 0;
		else:
			$time = 700;
			_e( 'Success', 'pagelines' );
		endif;
		$text = '&extend_text=theme_delete#installed';
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
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
		$upgrader = new Theme_Upgrader($skin);

		if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ):
			$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false  );
			$time = 0;
		else:
			extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );
			$time = 700;
			_e( 'Success', 'pagelines' );
		endif;
		@$upgrader->install( $this->make_url( $type, $file ) );

		if ( $active )
			switch_theme( basename( get_template_directory() ), $file );
		// Output
		$text = '&extend_text=theme_upgrade#installed';
		$this->page_reload( 'pagelines_extend' . $text, null, $time);
		
	}
	
	function theme_activate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), $file );
		// Output
		_e( 'Activated', 'pagelines' );
		delete_transient( 'pagelines_sections_cache' );
		$this->page_reload( 'pagelines&activated=true&pageaction=activated' );
		
	}
	
	function theme_deactivate( $type, $file, $path, $uploader, $checked ) {
		
		switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
		// Output
		_e( 'Deactivated', 'pagelines' );
		delete_transient( 'pagelines_sections_cache' );
		$this->page_reload( 'pagelines_extend' );
		
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