<?php
/**
 * Plugin installer class
 *
 * Install PageLines plugins and looks after them.
 *
 * @since 2.0.b3
 */

 class PagelinesPlugins {
 	

 	function __construct() {
 		$this->plugin_installer_init();
 	}

	function plugin_installer_init() {
	
		add_filter('install_plugins_nonmenu_tabs', array(&$this,'plugin_installer_extra_tabs' ) );
		add_action('install_plugins_pagelines', array(&$this,'plugin_installer_url' ) );
	}


	function plugin_installer_extra_tabs($tabs) {
		$tabs[] = 'pagelines';
		return $tabs;
	}

	function plugin_installer_ui() {

		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		$plugins = json_decode( $api['body'] );

		if ( is_object($plugins) ) {
		
			$text = '</form>';
			foreach( $plugins as $plugin ) {
			
				$text .= '<p>Plugin name: ' . $plugin->name;
				$text .= '<br />Version: ' . $plugin->version;
				$text .= '<br />blurb: ' . $plugin->text;

				// Remember this form is a hack up!
				// 
				// here we need to check if our plugin is installed already, and change the form to ajax activate/deactivate
				// 

				$text .= '<form method="post" action="'. admin_url('plugin-install.php?tab=pagelines') . '">';
				$text .= '<input type="hidden" name="pluginurl" value="' . $plugin->url . '" />';
				$text .= wp_nonce_field( 'plugin-pagelines');
				$text .= '<input type="submit" class="button" value="Install Now" style="display:block;margin-top:5px;" />';
				$text .= '</form></p>';		
			}
		}
		return $text;
	}

	function plugin_installer_url() {
		check_admin_referer('plugin-pagelines');
	
		if (!is_user_logged_in()) {
			wp_die(__('You are not logged in.', 'improved_plugin_installation')); 	
		} else if (!current_user_can('install_plugins')) {
			wp_die(__('You do not have the necessary administrative rights to be able to install plugins.', 'improved_plugin_installation'));
		} 
	
	
		$url = $_REQUEST['pluginurl'];
	
		if (get_filesystem_method() != 'direct') {
			global $wp_filesystem;
			$credentials_url = 'plugin-install.php?tab=plugin_installer_url&pluginurls[]=' . urlencode($url);
			$credentials_url = wp_nonce_url($credentials_url, 'plugin-plugin_installer_url');
				
			if ( false === ($credentials = request_filesystem_credentials($credentials_url)) ) //preload the credentials in $_POST.. 
				return;
					
			if ( ! WP_Filesystem($credentials) ) {
				request_filesystem_credentials($credentials_url, '', true); //Failed to connect, Error and request again
				return;
			}
		
			if ( $wp_filesystem->errors->get_error_code() ) {
				foreach ( $wp_filesystem->errors->get_error_messages() as $message )
					show_message($message);
			return;
			}
		}
		$this->plugin_installer_do_external_plugin_install($url);
	}

	function plugin_installer_do_external_plugin_install($download_url) {
		global $wp_filesystem;
		add_filter( 'install_plugin_complete_actions', '__return_false' );
	
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('title', 'nonce', 'url', 'plugin' ) ) );
	 	$upgrader->install($download_url);
	
		if (! is_wp_error($upgrader->skin->result) ) {
			echo '<script type="text/javascript">
			window.location = "/wp-admin/admin.php?page=pl_extension";
			</script>';
		} 
	}

 } // end PagelinesPlugins class