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
		// Hooked Actions
		add_action('admin_head', array(&$this, 'extension_js'));
		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));
 	}

 	function extension_sections_install( $tab = '' ) {
 		
 		/*
 			TODO make error checking better...
			TODO Use plugin?
 		*/
		
 		if ( !is_dir( EXTEND_CHILD_DIR . '/sections' ) || ( file_exists( EXTEND_CHILD_DIR . '/pagelines-base.php' ) && current( $this->plugin_check_status( EXTEND_CHILD_DIR . '/pagelines-base.php' ) ) == 'notactive' ) ){
 		
			$install_button = OptEngine::superlink('Install It Now!', 'blue', 'install_now sl-right', '', '');
			return sprintf('<div class="install-control fix"><span class="banner-text">You need to install and activate PageLines Base Plugin</span> %s</div>', $install_button);
			
		}

		$sections = $this->get_latest_cached( 'sections' );

		if ( ! is_object($sections) ) 
			return $sections;

		$output = '';

		foreach( $sections as $key => $section ) {
			
			if ( !isset( $section->type) )
				$section->type = 'free';
			
			if ($tab !== $section->type)
				continue;

			$check_file = EXTEND_CHILD_DIR . '/sections/' . $key . '/' . $key . '.php';

			if ( file_exists( $check_file ) )
				continue;

			$key = str_replace( '.', '', $key );
			$install_js_call = sprintf( $this->exprint, 'section_install', $key, 'sections', $key, 'Installing');

			$button = OptEngine::superlink('Install Section', 'black', '', '', $install_js_call);
				
			$args = array(
					'name' 		=> $section->name, 
					'version'	=> $section->version, 
					'desc'		=> $section->text, 
					'auth_url'	=> $section->author_url, 
					'auth'		=> $section->author,
					'image'		=> $section->image,
					'buttons'	=> $button,
					'type'		=> 'sections',
					'key'		=> $key
			);
				
			$output .= $this->pane_template($args);
				
		}
		return $output;
 	}

	/*
	 * Document!
	 */
 	function extension_sections() {

 		/*
 		 * Clear section cache and re-generate
 		 */
 		global $load_sections;
 		delete_option( 'pagelines_sections_cache' );
 		$load_sections->pagelines_register_sections();
 		$available = get_option( 'pagelines_sections_cache' );
 		$disabled = get_option( 'pagelines_sections_disabled', array() );
		$upgradable = $this->get_latest_cached( 'sections' );
		$output = '';
 		foreach( $available as $type ) {
	
 			if ( !$type )
 				continue;

			foreach( $type as $key => $section) {
	
				$type[$key]['status'] = ( isset( $disabled[$section['type']][$section['class']] ) ) ? 'disabled' : 'enabled';
		
			}

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $section) { // main loop

  				if ( $section['type'] == 'parent' && isset( $available['child'][$section['class']] ) )
 					continue;

				$activate_js_call = sprintf($this->exprint, 'section_activate', $key, $section['type'], $section['class'], 'Activating');
				$deactivate_js_call = sprintf($this->exprint, 'section_deactivate', $key, $section['type'], $section['class'], 'Deactivating');
				
				$button = ( $section['status'] == 'enabled' ) 
							? OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call) 
							: OptEngine::superlink('Activate', 'blue', '', '', $activate_js_call);

				$file = basename($section['base_dir']);
				if ( is_object( $upgradable ) && isset( $upgradable->$file ) ) {
		
					$install_js_call = sprintf( $this->exprint, 'section_upgrade', $key, 'sections', $file, 'Upgrading to version ' . $upgradable->$file->version );

					$button = ( isset( $upgradable->$file ) && $section['version'] < $upgradable->$file->version )
								? OptEngine::superlink('Upgrade available!', 'black', '', '', $install_js_call)
								: $button;
				}
				
				$args = array(
						'name' 		=> $section['name'], 
						'version'	=> !empty( $section['version'] ) ? $section['version'] : CORE_VERSION, 
						'desc'		=> $section['description'],
						'tags'		=> ( isset( $section['tags'] ) ) ? $section['tags'] : '',
						'auth_url'	=> $section['authoruri'],
						'type'		=> 'sections',
						'image'		=> ( isset( $section['image'] ) ) ? $section['image'] : '',
						'auth'		=> $section['author'],
						'importance'=> $section['importance'],
						'buttons'	=> $button, 
						'key'		=> $key
				);
				
				$output .= $this->pane_template($args);

 			}	// end main loop

 		} // end type loop

		return $output;
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
		foreach( $plugins as $key => $plugin ) {
			$plugins[$key]['status'] = $this->plugin_check_status( WP_PLUGIN_DIR . $plugin['file'] );
			$plugins[$key]['name'] = ( $plugins[$key]['status']['data']['Name'] ) ? $plugins[$key]['status']['data']['Name'] : $plugins[$key]['name'];
		}

		$plugins = pagelines_array_sort( $plugins, 'status', 'status' ); // sort by status

		// reset array keys ( sort functions reset keys to int )
		foreach( $plugins as $key => $plugin ) {
			
			unset( $plugins[$key] );
			$key = str_replace( '.php', '', basename( $plugin['file'] ) );
			$plugins[$key] = $plugin;
		}
	
		foreach( $plugins as $key => $plugin ) {
	
			if ( !isset( $plugin['type'] ) )
				$plugin['type'] = 'free';
			if ( $tab === 'installed' && !isset( $plugin['status']['status'] ) )
				continue;
			if ( ( $tab === 'premium' || $tab === 'free' ) && isset( $plugin['status']['status'] ) )
				continue;
			if ( $tab === 'premium' && $plugin['type'] === 'free' )
				continue;
			if ( $tab === 'free' && $plugin['type'] === 'premium' )
				continue;	
			
			$install_js_call = sprintf( $this->exprint, 'plugin_install', $key, 'plugins', $key, 'Installing');
			$activate_js_call = sprintf( $this->exprint, 'plugin_activate', $key, 'plugins', $plugin['file'], 'Activating');
			$deactivate_js_call = sprintf( $this->exprint, 'plugin_deactivate', $key, 'plugins', $plugin['file'], 'Deactivating');
			$upgrade_js_call = sprintf( $this->exprint, 'plugin_upgrade', $key, 'plugins', $key, 'Upgrading');
			$delete_js_call = sprintf( $this->exprint, 'plugin_delete', $key, 'plugins', $plugin['file'], 'Deleting');

			if ( !isset( $plugin['status'] ) )
				$plugin['status'] = array( 'status' => '' );

			if ( isset( $plugin['status']['version'] ) )
				if ( $plugin['version'] > $plugin['status']['version'] )
					$plugin['status']['status'] = 'upgrade';

			switch ( $plugin['status']['status'] ) {
					
				case 'active':
					$button = OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call);
				break;
					
				case 'notactive':
					$button = OptEngine::superlink('Activate', 'blue', '', '', $activate_js_call);
					$button .= OptEngine::superlink('Delete', 'grey', '', '', $delete_js_call);
				break;
					
				case 'upgrade':
					$button = OptEngine::superlink('Upgrade to ' . $plugin['version'], 'black', '', '', $upgrade_js_call);
				break;

				default:
					// were not installed, show the form! ( if we are on install tab )
					$button = OptEngine::superlink('Install', 'black', '', '', $install_js_call);
				break;
						
			}
				
			$args = array(
					'name' 		=> $plugin['name'], 
					'version'	=> ( isset( $plugin['status']['data'] ) ) ? $plugin['status']['data']['Version'] : $plugin['version'], 
					'desc'		=> $plugin['text'],
					'tags'		=> ( isset( $plugin['tags'] ) ) ? $plugin['tags'] : '',
					'auth_url'	=> $plugin['author_url'], 
					'image'		=> ( isset( $plugin['image'] ) ) ? $plugin['image'] : '',
					'auth'		=> $plugin['author'], 
					'buttons'	=> $button,
					'key'		=> $key,
					'type'		=> 'plugins',
					'count'		=> $plugin['count']
			);
				
			$output .= $this->pane_template($args);
				
		}
		return $output;
	}


	function extension_themes( $tab = '' ) {

		$themes = $this->get_latest_cached( 'themes' );

		if ( !is_object($themes) ) 
			return $themes;
			
		$output = '';
		$status = '';

		foreach( $themes as $key => $theme ) {
			
			if ( file_exists( WP_CONTENT_DIR . '/themes/' . strtolower( $theme->name ) . '/style.css' ) )
				if ( $data =  get_theme_data( WP_CONTENT_DIR . '/themes/' . strtolower( $theme->name ) . '/style.css' ) ) 
					$status = 'installed';

				if ($tab == 'premium' && $status == 'installed' )
					continue;
					
				if ( !$tab && !$status)
					continue;
				if ( $status == 'installed' && strtolower( $theme->name ) == basename( STYLESHEETPATH ) )
					$status = 'activated';

			
				$activate_js_call = sprintf( $this->exprint, 'theme_activate', $key, 'themes', $key, 'Activating');
				$deactivate_js_call = sprintf( $this->exprint, 'theme_deactivate', $key, 'themes', $key, 'Deactivating');
				$install_js_call = sprintf( $this->exprint, 'theme_install', $key, 'themes', $key, 'Installing');
				$upgrade_js_call = sprintf( $this->exprint, 'theme_upgrade', $key, 'themes', $key, 'Upgrading');

				if ( isset($data) && $data['Version'] && $theme->version > $data['Version'])
					$status = 'upgrade';

				if ( !$status && !isset( $theme->purchased) )
					$status = 'purchase';
				
				switch ( $status ) {
					
					case 'activated':
						$button = OptEngine::superlink('Deactivate', 'black', '', '', $deactivate_js_call);
						break;					
					
					case 'installed':
						$button = OptEngine::superlink('Activate', 'black', '', '', $activate_js_call);
						break;
					
					case 'upgrade':
						$button = OptEngine::superlink('Upgrade to ' . $theme->version, 'black', '', '', $upgrade_js_call);
						break;

					case 'purchase':
						$button = OptEngine::superlink('Purchase Theme', 'black', '', '', '');
						break;



					default:
						// were not installed, show the form! ( if we are on install tab )
						$button = OptEngine::superlink('Install', 'black', '', '', $install_js_call);
						break;
						
				}
				
				$args = array(
						'name' 		=> $theme->name, 
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $theme->version, 
						'desc'		=> $theme->text,
						'tags'		=> ( isset( $theme->tags ) ) ? $theme->tags : '',
						'auth_url'	=> $theme->author_url, 
						'auth'		=> $theme->author,
						'image'		=> ( isset( $theme->image ) ) ? $theme->image : '',
						'type'		=> 'themes',
						'buttons'	=> $button,
						'key'		=> $key,
						'count'		=> $theme->count
				);
				
				$output .= $this->pane_template($args);
				
			}
		return $output;
	}

	/*
	 * Draws the basic extension pane for sections, plugins based on settings
	 */
	function pane_template( $args = array() ){
		
		$d = array(
				'name' 		=> 'No Name', 
				'version'	=> 'No Version', 
				'desc'		=> '',
				'tags'		=> '', 
				'auth_url'	=> '', 
				'auth'		=> '',
				'image'		=> '',
				'buttons'	=> '',
				'importance'=> '',
				'key'		=> '',
				'type'		=> '',
				'count'		=> ''
		);
		
		$s = wp_parse_args( $args, $d);
		
		$buttons = sprintf('<div class="pane-buttons">%s</div>', $s['buttons']);
		
		$tags =  ( $s['tags'] ) ? sprintf('<br />Tags: %s', $s['tags']) : '';
		
		$count = ( $s['count'] ) ? sprintf('<br />Downloads: %s', $s['count']) : '';
		
		$screenshot = ( $s['image'] ) ? sprintf('<div class="extend-screenshot"><a class="screenshot-%s" href="http://api.pagelines.com/%s/img/%s.png" rel="http://api.pagelines.com/%s/img/%s.png"><img src="http://api.pagelines.com/%s/img/thumb-%s.png"></a></div>' , str_replace( '.', '-', $s['key']), $s['type'], $s['key'], $s['type'], $s['key'], $s['type'], $s['key']) : '';

		$js =  ( $screenshot ) ? "<script type='text/javascript' />jQuery('a.screenshot-" . str_replace( '.', '-', $s['key']) . "').imgPreview({
		    containerID: 'imgPreviewWithStyles',
		    imgCSS: {
		        // Limit preview size:
		        height: 200
		    },
		    // When container is shown:
		    onShow: function(link){
		        // Animate link:
		        jQuery(link).stop().animate({opacity:0.4});
		        // Reset image:
		        jQuery('img', this).stop().css({opacity:0});
		    },
		    // When image has loaded:
		    onLoad: function(){
		        // Animate image
		        jQuery(this).animate({opacity:1}, 300);
		    },
		    // When container hides: 
		    onHide: function(link){
		        // Animate link:
		        jQuery(link).stop().animate({opacity:1});
		    }
		});</script>" : '';
		
		$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div>%s</div></div>', $s['name'], 'Version ' . $s['version'], $screenshot );
		
		$auth = sprintf('<div class="pane-dets">by <a href="%s">%s</a> %s%s</div>', $s['auth_url'], $s['auth'], $tags, $count);
		
		$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);
		
		$r = sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>%s', $s['key'], $js);
		
		return sprintf('<div class="plpane pane-plugin"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></div>%s', $title, $body, $buttons, $r);
			
	}



	/**
	 * 
	 * Add Javascript to header (hook in contructor)
	 * 
	 */
	function extension_js(){ ?>
		
		<script type="text/javascript">/*<![CDATA[*/

		function extendIt( mode, key, type, url, duringText ){
			
				/* 
					'Mode' 	= the type of extension
					'Key' 	= the key of the element in the array, for the response
					'Type' 	= ?
					'Url' 	= the url for the extension/install/update
				*/
			
				var data = {
					action: 'pagelines_ajax_extend_it_callback',
					extend_mode: mode,
					extend_type: type,
					extend_url: url
				};

				var responseElement = jQuery('#response'+key);

				var duringTextLength = duringText.length + 3;
				var dotInterval = 400;
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						responseElement.html( duringText ).slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							
							var text = responseElement.text();
							
							if ( text.length < duringTextLength ){	
								responseElement.text( text + '.' ); 
							} else { 
								responseElement.text( duringText ); 
							} 
							
						}, dotInterval);

					},
				  	success: function( response ){
					
						window.clearInterval( interval ); // clear dots...
						
						responseElement.html(response).delay(6500).slideUp();
					}
				});

		}
		/*]]>*/</script>
		
<?php }

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
			$url =  $_POST['extend_url'];
		
		// 3. Do our thing...

		switch ( $mode ) {
			
			case 'plugin_install':

				$upgrader = new Plugin_Upgrader();
				@$upgrader->install( $this->make_url( $type, $url ) );
				$this->page_reload( 'pagelines_extend' );
			break;	

			case 'plugin_activate':

			 	activate_plugin( $url );
			 	echo 'Activation complete! ';
			 	$this->page_reload( 'pagelines_extend' );
			break;
					
			case 'plugin_deactivate':

				deactivate_plugins( array( $url ) );
				// Output
		 		echo 'Deactivation complete! ';
		 		$this->page_reload( 'pagelines_extend' );			
			break;
			
			case 'section_activate':

				$available = get_option( 'pagelines_sections_disabled' );
				unset( $available[$type][$url] );
				update_option( 'pagelines_sections_disabled', $available );
				// Output
				echo 'Activated';
				$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'section_deactivate':

				$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
				$disabled[$type][$url] = true; 
				update_option( 'pagelines_sections_disabled', $disabled );
				// Output
				echo 'Deactivated';
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'section_install':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, str_replace( 'section', 'section.', $url ) ), 
						'destination'		=> EXTEND_CHILD_DIR . '/sections/' . str_replace( 'section', 'section.', $url ), 
						'clear_destination' => false,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				echo 'Installed';
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'section_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $url ), 
						'destination'		=> EXTEND_CHILD_DIR .'/sections/' . $url, 
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
			
			case 'plugin_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $url ), 
						'destination'		=> WP_PLUGIN_DIR .'/' . $url, 
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

				delete_plugins( array( ltrim( $url, '/' ) ) );
				echo 'Deleted';
				$this->page_reload( 'pagelines_extend' );
			break;
			
			case 'theme_upgrade':

				$upgrader = new Plugin_Upgrader();
				$options = array( 'package' => $this->make_url( $type, $url ), 
						'destination'		=> WP_CONTENT_DIR .'/themes/' . $url, 
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
				$options = array( 'package' => $this->make_url( $type, $url ), 
						'destination'		=> WP_CONTENT_DIR .'/themes/' . $url, 
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

				switch_theme( basename( get_template_directory() ), $url );
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
	
	function make_url( $type, $url ) {
		
		return 'http://api.pagelines.com/' . $type . '/download.php?d=' . $url . '.zip';		
	}
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location ) {
 		echo '<script type="text/javascript">window.location = \'' . admin_url( 'admin.php?page=' . $location ) . '\';</script>';
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
				'username'	=>	$this->username,
				'password'	=>	$this->password
			)
		);
		
		$api = get_transient( 'pagelines_sections_api_' . $type );
		if ( !$api ) {
			$response = wp_remote_post( $url, $options );
			$api = wp_remote_retrieve_body( $response );
		}

		if( is_wp_error( $api ) )
			return '<h2>Unable to fetch from API</h2>';

		set_transient( 'pagelines_sections_api_' . $type, $api, 300 );

		return json_decode( $api );
	}

 } // end PagelinesExtensions class


/**
 *
 *  Returns Extension Array Config
 *
 */
function extension_array(  ){

	global $extension_control;

	$d = array(
		'Sections' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-sections.png',
			'htabs' 	=> array(
				'installed'	=> array(
					'title'		=> 'Installed PageLines Sections',
					'callback'	=> $extension_control->extension_sections()
					),
				'premium'	=> array(
					'title'		=> 'Premium PageLines Sections',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_sections_install( 'premium' )
					),
				'free'	=> array(
					'title'		=> 'Free PageLines Sections',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_sections_install( 'free' )
					),
				'featured'	=> array(
					'title'		=> 'Featured on PageLines.com',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_sections_install( 'feature' )
					)
				)

			),
		'Themes' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-themes.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> 'Installed PageLines Themes',
					'callback'	=> $extension_control->extension_themes()
					),
				'premium'		=> array(
					'title'		=> 'Premium Themes',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_themes( 'premium' )
					)
					
				)

			),
		'Plugins' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-plugins.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> 'Installed PageLines Plugins',
					'callback'	=> $extension_control->extension_plugins( 'installed' )
					),
				'free'		=> array(
					'title'		=> 'Free Plugins',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_plugins( 'free' )
					),
				'premium'		=> array(
					'title'		=> 'Premium Plugins',
					'class'		=> 'right',
					'callback'	=> $extension_control->extension_plugins( 'premium' )
					)
				)

			),
		'Import-Export' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-inout.png',
			'import_set'	=> array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'title'		=> 'Import Settings',						
				'shortexp'	=> '',
			),
		)

	);

	return apply_filters('extension_array', $d); 
}