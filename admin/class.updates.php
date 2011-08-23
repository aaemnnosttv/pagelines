<?php 
class PageLinesUpdateCheck {

    function __construct( $theme = null, $version = null, $plugin = null ){
	
		global $current_user;
    	$this->plugin = $plugin;
    	$this->url_theme = apply_filters( 'pagelines_theme_update_url', 'http://api.pagelines.com/v2/' );
    	$this->theme  = $theme;
 		$this->version = $version;
		$this->username = get_pagelines_option( 'lp_username' );
		$this->password = get_pagelines_option( 'lp_password' );

		get_currentuserinfo();
		$bad_users = apply_filters( 'pagelines_updates_badusernames', array( $current_user->user_login, 'admin', 'root', 'test', 'testing', '' ) );
		if ( in_array( strtolower( $this->username ),  $bad_users ) ) {
			pagelines_update_option( 'lp_username', '' );
			pagelines_update_option( 'lp_password', '' );
			$this->username = '';
			$this->password = '';
		//	$this->pagelines_theme_clear_update_transient();
		}
    }

	/**
	 * TODO Document!
	 */
	function pagelines_theme_check_version() {

		if ( is_multisite() && ! is_super_admin() )
			return;
		if ( get_pagelines_option('disable_updates') == true )
			return;
		add_action('admin_notices', array(&$this,'pagelines_theme_update_nag') );
		add_filter('site_transient_update_themes', array(&$this,'pagelines_theme_update_push') );
		add_filter('transient_update_themes', array(&$this,'pagelines_theme_update_push') );		
		add_action('load-update.php', array(&$this,'pagelines_theme_clear_update_transient') );
		add_action('load-themes.php', array(&$this,'pagelines_theme_clear_update_transient') );

	}
	
	/**
	 * TODO Document!
	 */
	function bad_creds( $errors ) {
		$errors['api']['title'] = 'API error';
		$errors['api']['text'] = 'Launchpad Username and Password are required for automatic updates.';
		return $errors;
	}

	/**
	 * TODO Document!
	 */
	function pagelines_theme_clear_update_transient() {

		delete_transient('pagelines-update-' . $this->theme );
		remove_action('admin_notices', array(&$this,'pagelines_theme_update_nag') );
		delete_transient( 'pagelines_sections_cache' );

	}

	/**
	 * TODO Document!
	 */
	function pagelines_theme_update_push($value) {

		$pagelines_update = $this->pagelines_theme_update_check();

		if ( $pagelines_update && $pagelines_update['package'] !== 'bad' ) {
			$value->response[strtolower($this->theme)] = $pagelines_update;
		}
		return $value;
	}
	
	/**
	 * TODO Document!
	 */
	function pagelines_theme_update_nag() {
		$pagelines_update = $this->pagelines_theme_update_check();

		if ( !is_super_admin() || !$pagelines_update )
			return false;
		if ( $this->username == '' || $this->password == '' || $pagelines_update['package'] == 'bad' ) {
		//	add_filter('pagelines_admin_notifications', array(&$this,'bad_creds') );

			}
		echo '<div id="update-nag">';
		printf( '%s %s is available.', $this->theme, esc_html( $pagelines_update['new_version'] ) );
		
		printf( ' %s', ( $pagelines_update['package'] != 'bad' ) ? sprintf( 'You should <a href="%s">update now</a>.', admin_url('update-core.php') ) : sprintf( '<a href="%s">Click here</a> to setup your PageLines account.', admin_url('admin.php?page=pagelines_extend#Account') ) );

		echo ( $pagelines_update['extra'] ) ? sprintf('<br />%s', $pagelines_update['extra'] ) : '';
		echo '</div>';
	}	
	
	/**
	 * TODO Document!
	 */
	function pagelines_theme_update_check() {
		global $wp_version;

		$pagelines_update = get_transient('pagelines-update-' . $this->theme );

		if ( !$pagelines_update ) {
			$url = $this->url_theme;
			$options = array(
					'body' => array(
						'version' => $this->version,
						'wp_version' => $wp_version,
						'php_version' => phpversion(),
						'uri' => home_url(),
						'theme' => $this->theme,
						'user' => $this->username,
						'password' => $this->password,
						'user-agent' => "WordPress/$wp_version;"
					)
			);

			$response = wp_remote_post($url, $options);
			$pagelines_update = wp_remote_retrieve_body($response);

			// If an error occurred, return FALSE, store for 1 hour
			if ( $pagelines_update == 'error' || is_wp_error($pagelines_update) || !is_serialized( $pagelines_update ) || $pagelines_update['package'] == 'bad' ) {
				set_transient('pagelines-update-' . $this->theme, array('new_version' => $this->version), 60*60); // store for 1 hour
				return FALSE;
			}

			// Else, unserialize
			$pagelines_update = maybe_unserialize($pagelines_update);

			// And store in transient
			set_transient('pagelines-update-' . $this->theme, $pagelines_update, 60*60*24); // store for 24 hours
		}

		// If we're already using the latest version, return FALSE
		if ( !isset($pagelines_update['new_version']) || version_compare($this->version, $pagelines_update['new_version'], '>=') )
			return FALSE;

		return $pagelines_update;
	}
} // end class