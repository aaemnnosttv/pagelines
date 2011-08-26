<?php
/**
 * Plugin/theme installer class and section control.
 *
 * TODO add enable all to sections.
 * TODO Make some use of the tags system
 *
 * Install PageLines plugins and looks after them.
 * 
 * @author Simon Prosser (the one and only)
 *
 * @since 2.0.b3
 */

 class PagelinesExtensions {

 	function __construct() {

		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		$this->username = get_pagelines_option( 'lp_username' );
		$this->password = get_pagelines_option( 'lp_password' );
		
		$this->ui = new PageLinesExtendUI;
		
		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));
 	}

 	function extension_sections_install( $tab = '' ) {
 		
 		/*
 			TODO make error checking better...
			TODO Use plugin?
 		*/
		
 		if ( !$this->has_extend_plugin() )
			return $this->ui->get_extend_plugin( $this->has_extend_plugin('status'), $tab );

		$sections = $this->get_latest_cached( 'sections' );

		if ( ! is_object($sections) ) 
			return $sections;


		$list = array();
		
		foreach( $sections as $key => $s ) {

			$check_file = sprintf('%1$s/%2$s/%3$s.php', PL_EXTEND_DIR, str_replace( 'section.', '', $key ), $key); 

			if ( !isset( $s->type) )
				$s->type = 'free';
			
			if ($tab !== $s->type)
				continue;

			if ( file_exists( $check_file ) )
				continue;

			$key = str_replace( '.', '', $key );
		
			$actions = array(
				'install'	=> array(
					'mode'		=> 'install',
					'condition'	=> true,
					'case'		=> 'section_install',
					'type'		=> $s->type,
					'file'		=> $key,
					'path'		=> $s->class,
					'text'		=> 'Install',
					'dtext'		=> 'Installing'
					)
			);
			
			$list[$key] = array(
					'name' 		=> $s->name, 
					'version'	=> $s->version, 
					'desc'		=> $s->text, 
					'auth_url'	=> $s->author_url, 
					'auth'		=> $s->author,
					'image'		=> $s->image,
					'type'		=> 'sections',
					'key'		=> $key, 
					'ext_txt'	=> 'Installing', 
					'actions'	=> $actions
			);
			
		}
		
		if(empty($list))
			return $this->ui->extension_banner( 'Browsing '. $tab .' sections is currently unavailable. <br/>Check back soon!' );
		else
			return $this->ui->extension_list( $list );
 	}

	function has_extend_plugin( $status = false ){
		
		if($status){
			
			if( file_exists( PL_EXTEND_INIT ) && current( $this->plugin_check_status( PL_EXTEND_INIT ) ) == 'notactive' )
				return 'notactive';
			elseif(!is_dir( PL_EXTEND_DIR ) || !file_exists( PL_EXTEND_INIT ))
				return 'notinstalled';
			else
				return 'active';
			
		} else {
			if ( !is_dir( PL_EXTEND_DIR ) || ( file_exists( PL_EXTEND_INIT ) && current( $this->plugin_check_status( PL_EXTEND_INIT ) ) == 'notactive' ) )
				return false;
			else 
				return true;
		}
	}
	
	/*
	 * Get sections that are installed.
	 */
 	function extension_sections( $tag = '', $tab = '' ) {

 		/*
 		 * Clear section cache and re-generate
 		 */
 		global $load_sections;

		// Get sections
 		$available = $load_sections->pagelines_register_sections( true, true );

 		$disabled = get_option( 'pagelines_sections_disabled', array() );

		$upgradable = $this->get_latest_cached( 'sections' );
		
		$list = array();
 		foreach( $available as $type ) {
	
 			if ( !$type )
 				continue;

			foreach( $type as $key => $s)
				$type[$key]['status'] = ( isset( $disabled[ $s['type'] ][ $s['class'] ] ) ) ? 'disabled' : 'enabled';

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $s ) { // main loop
	

				if ( $tag === 'internal' )
					if ( false === ( strpos( $s['tags'], 'internal') ) )
						continue;
						
				if ( $tag === 'user' )
					if ( $s['tags'] === 'internal' ) 
						continue;

				
  				if ( $s['type'] == 'parent' && isset( $available['child'][ $s['class'] ] ) )
					continue;
				
				$enabled = ( $s['status'] == 'enabled' ) ? true : false;

				$file = basename( $s['base_dir'] );

				$upgrade_available = $this->upgrade_available( $upgradable, 'section.' . $file, $s);
			
				$actions = array(
					'activate'	=> array(
						'mode'		=> 'activate',
						'condition'	=> (!$enabled) ? true : false,
						'case'		=> 'section_activate',
						'type'		=> $s['type'],
						'file'		=> $s['class'],
						'text'		=> 'Activate',
						'dtext'		=> 'Activating',
					) ,
					'deactivate'=> array(
						'mode'		=> 'deactivate',
						'condition'	=> $enabled,
						'case'		=> 'section_deactivate',
						'type'		=> $s['type'],
						'file'		=> $s['class'],
						'text'		=> 'Deactivate',
						'dtext'		=> 'Deactivating',
					),
					'upgrade'	=> array(
						'mode'		=> 'upgrade',
						'condition'	=> $upgrade_available,
						'case'		=> 'section_upgrade',
						'type'		=> 'sections',
						'file'		=> $file,
						'text'		=> 'Upgrade',
						'dtext'		=> 'Upgrading to version '.$upgrade_available,
					)
				);
				
				$list[] = array(
						'name' 		=> $s['name'], 
						'version'	=> !empty( $s['version'] ) ? $s['version'] : CORE_VERSION, 
						'desc'		=> $s['description'],
						'auth_url'	=> $s['authoruri'],
						'type'		=> 'sections',
						'object'	=> $s['class'],
						'tags'		=> ( isset( $s['tags'] ) ) ? $s['tags'] : '',
						'image'		=> ( isset( $s['image'] ) ) ? $s['image'] : '',
						'auth'		=> $s['author'],
						'importance'=> $s['importance'],
						'key'		=> $key,
						'status'	=> $s['status'], 
						'actions'	=> $actions
				);

 			}
 		} 
	
		if(empty($list))
			return $this->ui->extension_banner( 'No '. $tab .' sections are currently available. <br/>Check back soon!' );
		else
			return $this->ui->extension_list( $list );
 	}

	function upgrade_available( $upgradable, $file, $s){
	
		if ( is_object( $upgradable ) && isset( $upgradable->$file ) && $s['version'] < $upgradable->$file->version ) 
			return $upgradable->$file->version;
		else 
			return false;
	}

	/*
	 * Document!
	 */
	function extension_plugins( $tab = '' ) {

		$plugins = $this->get_latest_cached( 'plugins' );

		if ( !is_object($plugins) ) 
			return $plugins;
			
		$output = '';

		$plugins = json_decode(json_encode($plugins), true); // convert objets to arrays

		foreach( $plugins as $key => $plugin )
			$plugins[$key]['file'] = '/' . trailingslashit( $key ) . $key . '.php'; 
		
		$plugins = pagelines_array_sort( $plugins, 'name', false, true ); // sort by name

		// get status of each plugin
		foreach( $plugins as $key => $p ) {
			$plugins[$key]['status'] = $this->plugin_check_status( WP_PLUGIN_DIR . $p['file'] );
			$plugins[$key]['name'] = ( $plugins[$key]['status']['data']['Name'] ) ? $plugins[$key]['status']['data']['Name'] : $plugins[$key]['name'];
			
			
		}

		$plugins = pagelines_array_sort( $plugins, 'status', 'status' ); // sort by status

		// reset array keys ( sort functions reset keys to int )
		foreach( $plugins as $key => $p ) {
			
			unset( $plugins[$key] );
			$key = str_replace( '.php', '', basename( $p['file'] ) );
			$plugins[$key] = $p;
			
		}
		
		$list = array();

		foreach( $plugins as $key => $p ) {
	
			if ( !isset( $p['type'] ) )
				$p['type'] = 'free';
			if ( $tab === 'installed' && !isset( $p['status']['status'] ) )
				continue;
			if ( ( $tab === 'premium' || $tab === 'free' ) && isset( $p['status']['status'] ) )
				continue;
			if ( $tab === 'premium' && $p['type'] === 'free' )
				continue;
			if ( $tab === 'free' && $p['type'] === 'premium' )
				continue;
			if ( !isset( $p['status'] ) )
				$p['status'] = array( 'status' => '' );
				
			$install = ($p['status']['status'] == '' ) ? true : false;

			$upgrade_available = ( isset( $p['status']['version'] ) && $p['version'] > $p['status']['version'] ) ? true : false;
			
			$active = ($p['status']['status'] == 'active') ? true : false;
			
			$deactivated = (!$install && !$active) ? true : false;
			
			$actions = array(
				'install'	=> array(
					'mode'		=> 'install',
					'condition'	=> $install,
					'case'		=> 'plugin_install',
					'type'		=> 'plugins',
					'file'		=> $key,
					'text'		=> 'Install',
					'path'		=> $p['file'],
					'dtext'		=> 'Installing',
				),
				'activate'	=> array(
					'mode'		=> 'activate',
					'condition'	=> $deactivated,
					'case'		=> 'plugin_activate',
					'type'		=> 'plugins',
					'file'		=> $p['file'],
					'text'		=> 'Activate',
					'dtext'		=> 'Activating',
				),
				'upgrade'	=> array(
					'mode'		=> 'upgrade',
					'condition'	=> $upgrade_available,
					'case'		=> 'plugin_upgrade',
					'type'		=> 'plugins',
					'file'		=> $key,
					'text'		=> 'Upgrade to ' . $p['version'],
					'dtext'		=> 'Upgrading',
				),
				'deactivate'	=> array(
					'mode'		=> 'activate',
					'condition'	=> $active,
					'case'		=> 'plugin_deactivate',
					'type'		=> 'plugins',
					'file'		=> $p['file'],
					'text'		=> 'Deactivate',
					'dtext'		=> 'Deactivating',
				),
			);
					
			$list[$key] = array(
					'name' 		=> $p['name'], 
					'version'	=> ( isset( $p['status']['data'] ) ) ? $p['status']['data']['Version'] : $p['version'], 
					'desc'		=> $p['text'],
					'tags'		=> ( isset( $p['tags'] ) ) ? $p['tags'] : '',
					'auth_url'	=> $p['author_url'], 
					'image'		=> ( isset( $p['image'] ) ) ? $p['image'] : '',
					'auth'		=> $p['author'], 
					'key'		=> $key,
					'type'		=> 'plugins',
					'count'		=> $p['count'],
					'actions'	=> $actions
			);	
				
		}
	
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner('No Plugins Are Installed.');
		elseif(empty($list))
			return $this->ui->extension_banner( 'Browsing '. $tab .' plugins is currently unavailable. <br/>Check back soon!' );
		else 
			return $this->ui->extension_list( $list );
	}


	function extension_themes( $tab = '' ) {

		$themes = $this->get_latest_cached( 'themes' );

		if ( !is_object($themes) ) 
			return $themes;
			
		$output = '';
		$status = false;
		$list = array();

		foreach( $themes as $key => $theme ) {
		
			$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, strtolower( $theme->name ) );
		
				if ( file_exists( $check_file ) && $data = get_theme_data( $check_file ) ) 
					$status = 'installed';

				if ($tab == 'premium' && $status == 'installed' )
					continue;
					
				if ($tab == 'installed' && $status != 'installed' )
					continue;
					
				if ( !$tab && !$status)
					continue;
					
				$is_active = (strtolower( $theme->name ) == basename( get_stylesheet_directory() ))	? true : false;
					
				$activate = ($status == 'installed' && !$is_active) ? true : false;
				$deactivate = ($status == 'installed' && $is_active) ? true : false;
				$upgrade_available = (isset($data) && $data['Version'] && $theme->version > $data['Version']) ? true : false;
			
				$purchase = ( !isset( $theme->purchased ) && !$status ) ? true : false;
				$install = ( !$status && !$purchase) ? true : false;	
				$actions = array(
					'install'	=> array(
						'mode'		=> 'install',
						'condition'	=> $install,
						'case'		=> 'theme_install',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> 'Install',
						'dtext'		=> 'Installing',
					),
					'activate'	=> array(
						'mode'		=> 'activate',
						'condition'	=> $activate,
						'case'		=> 'theme_activate',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> 'Activate',
						'dtext'		=> 'Activating',
					),
					'deactivate'	=> array(
						'mode'		=> 'deactivate',
						'condition'	=> $deactivate,
						'case'		=> 'theme_deactivate',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> 'Deactivate',
						'dtext'		=> 'Deactivating',
					),
					'upgrade'	=> array(
						'mode'		=> 'upgrade',
						'condition'	=> $upgrade_available,
						'case'		=> 'theme_upgrade',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> 'Upgrade to '.$theme->version,
						'dtext'		=> 'Upgrading',
					),
					'purchase'	=> array(
						'mode'		=> 'install',
						'condition'	=> $purchase,
						'case'		=> 'theme_install',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> 'Purchase',
						'dtext'		=> 'Installing',
					),
				);

				$list[$key] = array(
						'theme'		=> $theme,
						'name' 		=> $theme->name, 
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $theme->version, 
						'desc'		=> $theme->text,
						'tags'		=> ( isset( $theme->tags ) ) ? $theme->tags : '',
						'auth_url'	=> $theme->author_url, 
						'image'		=> ( isset( $theme->image ) ) ? $theme->image : '',
						'auth'		=> $theme->author, 
						'key'		=> $key,
						'type'		=> 'themes',
						'count'		=> $theme->count,
						'actions'	=> $actions
				);
			
		//		$list[$key.'2'] = $list[$key];
		//		$list[$key.'3'] = $list[$key];
		}
		
		
		
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner('No PageLines themes are currently installed.');
		elseif(empty($list))
			return $this->ui->extension_banner( 'Browsing '. $tab .' themes is currently unavailable. <br/>Check back soon!' );
		else
			return $this->ui->extension_list( $list, 'graphic' );
			
	}





	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback(  ) {
		
		/*
			TODO reload callbacks just go to the panel, need tab as well
		*/	
		
		// 1. Libraries
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		
		// 2. Variable Setup
			$mode =  $_POST['extend_mode'];
			$type =  $_POST['extend_type'];
			$file =  $_POST['extend_file'];
			$path =  $_POST['extend_path'];

			$this->do_sanity( $mode );		
		// 3. Do our thing...

		switch ( $mode ) {
			
			case 'plugin_install':

				$upgrader = new Plugin_Upgrader();
				@$upgrader->install( $this->make_url( $type, $file ) );
				activate_plugin( $path );
				$this->page_reload( 'pagelines_extend' );
			break;	

			case 'plugin_activate':

			 	activate_plugin( $file );
			 	echo 'Activation complete! ';
			 	$this->page_reload( 'pagelines_extend' );
			break;
					
			case 'plugin_deactivate':

				deactivate_plugins( array( $file ) );
				// Output
		 		echo 'Deactivation complete! ';
		 		$this->page_reload( 'pagelines_extend' );			
			break;
			
			case 'section_activate':

				$available = get_option( 'pagelines_sections_disabled' );
				unset( $available[$type][$file] );
				update_option( 'pagelines_sections_disabled', $available );
				// Output
				echo 'Section Activated!';
				$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'section_deactivate':

				$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
				$disabled[$type][$file] = true; 
				update_option( 'pagelines_sections_disabled', $disabled );
				// Output
				echo 'Section Deactivated.';
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'section_install':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( 'sections', str_replace( 'section', 'section.', $file ) ), 
						'destination'		=> trailingslashit( PL_EXTEND_DIR ) . str_replace( 'section', '', $file ), 
						'clear_destination' => false,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				$available = get_option( 'pagelines_sections_disabled' );
				unset( $available['child'][$path] );
				update_option( 'pagelines_sections_disabled', $available );
				echo 'New Section Installed!';
				$this->page_reload( 'pagelines_extend' );
			break;
			
			case 'section_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, 'section.' . $file ), 
						'destination'		=> trailingslashit( PL_EXTEND_DIR ) . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				echo 'Success! Section Upgraded.';
				$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'plugin_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> trailingslashit( WP_PLUGIN_DIR ) . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				echo 'Upgraded';
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'plugin_delete':

				delete_plugins( array( ltrim( $file, '/' ) ) );
				echo 'Deleted';
				$this->page_reload( 'pagelines_extend' );
			break;
			
			case 'theme_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> PL_EXTEND_THEMES_DIR . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				echo 'Upgraded';
				$this->page_reload( 'pagelines_extend' );		
			break;			
			
			case 'theme_install':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> PL_EXTEND_THEMES_DIR . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);
				@$upgrader->run($options);
				// Output
				echo 'Installed';
				$this->page_reload( 'pagelines_extend' );		
			break;			
			
			case 'theme_activate':

				switch_theme( basename( get_template_directory() ), $file );
				// Output
				echo 'Activated';
				$this->page_reload( 'pagelines' );	
			break;

			case 'theme_deactivate':
			
				switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
				// Output
				echo 'Deactivated';
				$this->page_reload( 'pagelines_extend' );
			break;
			
		}
		die(); // needed at the end of ajax callbacks
	}
	
	function make_url( $type, $file ) {
		
		return sprintf('%s/%s/download.php?d=%s.zip', PL_API, $type, $file);
		
	}
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location, $error = '' ) {
	
		printf('<script type="text/javascript">setTimeout(function(){ window.location = \'%s\';}, 700);</script>', admin_url( 'admin.php?page=' . $location ));
 	}

	function do_sanity( $mode ) {

		switch( $mode ) {
			
			case 'plugin_install':
				if ( ! is_writable( WP_PLUGIN_DIR ) )
					$error = 'Plugins DIR is not writable by WordPress we cannot install any plugins!';
			break;
			
			case 'section_install':
				if ( ! is_writable( PL_EXTEND_DIR ) )
					$error = 'The sections DIR is not writable by WordPress we cannot install sections!';
			break;
			
			case 'theme_install':
				if ( ! is_writable( PL_EXTEND_THEMES_DIR ) )
					$error = 'The themes DIR is not writable by WordPress we cannot install themes!';
			break;
		}
		if (isset($error)) {
			$this->page_reload( 'pagelines_extend' . '&extend_error=' . $error );
			exit;
		}
	}

	function plugin_check_status( $file ) {
		
		if ( !file_exists( $file ) )
			return ;
		$data = get_plugin_data( $file );

		if (in_array( str_replace( '.php', '', basename($file) ), pagelines_register_plugins() ) ) 
			return array( 'status' => 'active', 'version' => $data['Version'], 'data' => $data);
		else
			return array( 'status' => 'notactive', 'version' => $data['Version'], 'data' => $data);
	}

	/**
	* Simple cache for plugins and sections
	* @return object
	*/
	function get_latest_cached( $type ) {
		
		$url = 'http://api.pagelines.com/' . $type . '/';
		$options = array(
			'body' => array(
				'username'	=>	( $this->username != '' ) ? $this->username : false,
				'password'	=>	( $this->password != '' ) ? $this->password : false
			)
		);
		
		if ( false === ( $api = get_transient( 'pagelines_sections_api_' . $type ) ) ) {
			$response = wp_remote_post( $url, $options );
			$api = wp_remote_retrieve_body( $response );
			set_transient( 'pagelines_sections_api_' . $type, $api, 300 );			
		}
		
		if( is_wp_error( $api ) )
			return '<h2>Unable to fetch from API</h2>';

		return json_decode( $api );
	}

 } // end PagelinesExtensions class


