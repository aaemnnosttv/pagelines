<?php 
class PageLinesUpdateCheck {

    function __construct( $theme = null, $version = null, $plugin = null ){
    	$this->plugin = $plugin;
    	$this->url_theme = apply_filters( 'pagelines_theme_update_url', 'http://api.pagelines.com/v2/' );
    	$this->url_plugins = 'http://updates/';
    	$bad_users = array( 'admin', 'root', 'test', 'testing', '');
    	$this->theme  = $theme;
 		$this->version = $version;
		$this->username = get_pagelines_option( 'lp_username' );
		$this->password = get_pagelines_option( 'lp_password' );
		if ( in_array( strtolower( $this->username ), $bad_users ) ) {
			pagelines_update_option( 'lp_username', '' );
			pagelines_update_option( 'lp_password', '' );
			$this->username = '';
			$this->password = '';
			$this->pagelines_theme_clear_update_transient();
		}
    }
    function pagelines_plugins_check_version() {
  
    	if ( get_pagelines_option('disable_updates') == true ) return;

    	add_filter('pre_set_site_transient_update_plugins', array(&$this,'check_for_plugin_update') );
    	add_filter('plugins_api', array(&$this,'my_plugin_api_call'),10, 3 );



    }
	function pagelines_theme_check_version() {
		add_filter( 'pagelines_options_array', array(&$this,'pagelines_theme_update_tab') );
		if ( get_pagelines_option('disable_updates') == true ) return;
		add_action('admin_notices', array(&$this,'pagelines_theme_update_nag') );
		add_filter('site_transient_update_themes', array(&$this,'pagelines_theme_update_push') );
		add_filter('transient_update_themes', array(&$this,'pagelines_theme_update_push') );		
		add_action('load-update.php', array(&$this,'pagelines_theme_clear_update_transient') );
		add_action('load-themes.php', array(&$this,'pagelines_theme_clear_update_transient') );

	}

	function pagelines_theme_update_tab( $option_array ) {

		$updates_exp = ( is_array( $a = get_transient('pagelines-update-' . $this->theme ) ) && isset($a['package']) && $a['package'] !== 'bad' ) 
							? 'Updates are properly configured.' 
							: 'Please use your login credentials for <a href="http://www.pagelines.com/launchpad/member.php">LaunchPad</a>.<br /><strong>Not</strong> your WordPress login.';

		$updates = array(
					'credentials' => array(
						'version'	=> 'pro',
						'type'		=> 'text_multi',
						'inputsize'	=> 'tiny',
						'selectvalues'	=> array(
							'lp_username'	=> array('inputlabel'=>'Launchpad Username', 'default'=> $this->username ),
							'lp_password'	=> array('inputlabel'=>'Launchpad Password', 'default'=> $this->password ),
						),
						'title'		=> 'Configure automatic updates',
						'shortexp'	=> 'Get the latest theme updates direct from PageLines.',
						'exp'		=> $updates_exp
					),
					'disable_updates' => array(
							'default'	=> true,
							'type'		=> 'check',
							'inputlabel'	=> 'Disable update system?',
							'title'		=> '',
							'shortexp'	=> '',
							'exp'		=> 'Completely disables the update system (includes notifications).'
					)

		);				
				
		
		$option_array['advanced'] = array_merge( $option_array['advanced'], $updates );
		return $option_array;
	}
	function bad_creds( $errors ) {
		$errors['api']['title'] = 'API error';
		$errors['api']['text'] = 'Launchpad Username and Password are required for automatic updates.';
		return $errors;
	}


	function pagelines_theme_clear_update_transient() {

		delete_transient('pagelines-update-' . $this->theme );
		remove_action('admin_notices', array(&$this,'pagelines_theme_update_nag') );

	}

	function pagelines_theme_update_push($value) {

		$pagelines_update = $this->pagelines_theme_update_check();

		if ( $pagelines_update && $pagelines_update['package'] !== 'bad' ) {
			$value->response[strtolower($this->theme)] = $pagelines_update;
		}
		return $value;
	}
	
	function pagelines_theme_update_nag() {
		$pagelines_update = $this->pagelines_theme_update_check();

		if ( !is_super_admin() || !$pagelines_update )
			return false;
		if ( $this->username == '' || $this->password == '' || $pagelines_update['package'] == 'bad' ) {
		//	add_filter('pagelines_admin_notifications', array(&$this,'bad_creds') );

			}
		echo '<div id="update-nag">';
		printf( '%s %s is available. <a href="%s">What\'s new</a>.', $this->theme, esc_html( $pagelines_update['new_version'] ), esc_url( $pagelines_update['changelog_url'] ) );
		echo ( $pagelines_update['package'] != 'bad' ) ? sprintf( ' You should <a href="%s">update now</a>.', admin_url('update-core.php') ) : ' Please configure auto-updates under misc-settings.';
		echo ( $pagelines_update['extra'] ) ? sprintf('<br />%s', $pagelines_update['extra'] ) : '';
		echo '</div>';
	}	
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
		if ( version_compare($this->version, $pagelines_update['new_version'], '>=') )
			return FALSE;

		return $pagelines_update;
	}

function check_for_plugin_update($checked_data) {


	if (empty($checked_data->checked))
		return $checked_data;
	
	$request_args = array(
		'slug' => $this->plugin,
		'version' => $checked_data->checked[$this->plugin .'/'. $this->plugin .'.php'],
	);
	
	$request_string = $this->prepare_request('basic_check', $request_args);
	
	// Start checking for an update
	$raw_response = wp_remote_post($this->url_plugins, $request_string);

	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);
	
	if (is_object($response) && !empty($response)) // Feed the update data into WP updater
		$checked_data->response[$this->plugin .'/'. $this->plugin .'.php'] = $response;
	
	return $checked_data;
}

function my_plugin_api_call($def, $action, $args) {


	if ($args->slug != $this->plugin)
		return false;
	
	// Get the current version
	$plugin_info = get_site_transient('update_plugins');
	$current_version = $plugin_info->checked[$this->plugin .'/'. $this->plugin .'.php'];
	$args->version = $current_version;
	
	$request_string = $this->prepare_request($action, $args);
	
	$request = wp_remote_post($this->url_plugins, $request_string);
	
	if (is_wp_error($request)) {
		$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
	} else {
		$res = unserialize($request['body']);
		
		if ($res === false)
			$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $request['body']);
	}
	
	return $res;
}


function prepare_request($action, $args) {
	global $wp_version;
	
	return array(
		'body' => array(
			'action' => $action, 
			'request' => serialize($args),
			'api-key' => md5(get_bloginfo('url'))
		),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
	);	
}

} // end class