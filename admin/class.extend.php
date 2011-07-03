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

		// Hooked Actions
		add_action('admin_head', array(&$this, 'extension_js'));
		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));
 	}

 	function extension_sections_install() {
 		
 		/*
 			TODO make error checking better...
			TODO Use plugin?
 		*/
 		if ( !is_dir( WP_PLUGIN_DIR . '/pagelines-sections/sections' ) ){
 		
			$install_button = OptEngine::superlink("Install It Now!", 'blue', 'install_now sl-right', '', '');
			return sprintf('<div class="install-control fix"><span class="banner-text">You need to install PageLines Base Plugin</span> %s</div>', $install_button);
			
		}

		$sections = $this->get_latest_cached( 'sections' );

		if ( is_object($sections) ) {

			$output = '';

			foreach( $sections as $key => $section ) {

				$check_file = WP_PLUGIN_DIR . '/pagelines-sections/sections/' . str_replace( '.zip', '', basename( $section->url ) ) . '/' . str_replace( '.zip', '', basename( $section->url ) ) . '.php';
				if ( file_exists( $check_file ) )
					continue;
				$key = str_replace( '.', '', $key );
				$install_js_call = sprintf( $this->exprint, 'section_install', $key, 'section', $section->url, 'Installing');

				$button = OptEngine::superlink('Install Section', 'black', '', '', $install_js_call);
				
				$args = array(
						'name' 		=> $section->name, 
						'version'	=> $section->version, 
						'desc'		=> $section->text, 
						'auth_url'	=> $section->author_url, 
						'auth'		=> $section->author,
						'image'		=> $section->image,
						'buttons'	=> $button,
						'key'		=> $key
				);
				
				$output .= $this->pane_template($args);
				
			}
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

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $section) { // main loop
	
  				if ( $section['type'] == 'parent' && isset( $available['child'][$section['class']] ) )
 					continue;

				$activate_js_call = sprintf($this->exprint, 'section_activate', $key, $section['type'], $section['class'], 'Activating');
				$deactivate_js_call = sprintf($this->exprint, 'section_deactivate', $key, $section['type'], $section['class'], 'Deactivating');
				
				$button = ( !isset( $disabled[$section['type']][$section['class']] ) ) 
							? OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call) 
							: OptEngine::superlink('Activate', 'blue', '', '', $activate_js_call);

				$file = basename($section['base_dir']);
				if ( is_object( $upgradable ) && isset( $upgradable->$file ) ) {
				
					$install_js_call = sprintf( $this->exprint, 'section_upgrade', $key, $file, $upgradable->$file->url, 'Upgrading to version ' . $upgradable->$file->version );

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

		if ( is_object($plugins) ) {
			
			$output = '';
			foreach( $plugins as $key => $plugin ) {
				

				$status = $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file );
				
				if ($tab != 'free' && !$status['status'] )
					continue;
				if ($tab == 'free' && ( $status['status'] == 'active' || $status['status'] == 'notactive' ) )
					continue;

				$install_js_call = sprintf( $this->exprint, 'plugin_install', $key, 'plugin', $plugin->url, 'Installing');
				$activate_js_call = sprintf( $this->exprint, 'plugin_activate', $key, 'plugin', $plugin->file, 'Activating');
				$deactivate_js_call = sprintf( $this->exprint, 'plugin_deactivate', $key, 'plugin', $plugin->file, 'Deactivating');
				$upgrade_js_call = sprintf( $this->exprint, 'plugin_upgrade', $key, 'plugin', $plugin->url, 'Upgrading');
				$delete_js_call = sprintf( $this->exprint, 'plugin_delete', $key, 'plugin', $plugin->file, 'Deleting');

				if ( !isset( $status['status'] ) )
					$status = array( 'status' => '' );

				if ( $status['status'] && $plugin->version > $status['data']['Version'])
					$status['status'] = 'upgrade';

				switch ( $status['status'] ) {
					
					case 'active':
						$button = OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call);
						break;
					
					case 'notactive':
						$button = OptEngine::superlink('Activate', 'blue', '', '', $activate_js_call);
						$button .= OptEngine::superlink('Delete', 'grey', '', '', $delete_js_call);
						break;
					
					case 'upgrade':
						$button = OptEngine::superlink('Upgrade to ' . $plugin->version, 'black', '', '', $upgrade_js_call);
						break;

					default:
						// were not installed, show the form! ( if we are on install tab )
						$button = OptEngine::superlink('Install', 'black', '', '', $install_js_call);
						break;
						
				}
				
				$args = array(
						'name' 		=> $plugin->name, 
						'version'	=> ( !empty( $status['status'] ) ) ? $status['data']['Version'] : $plugin->version, 
						'desc'		=> $plugin->text,
						'tags'		=> ( isset( $plugin->tags ) ) ? $plugin->tags : '',
						'auth_url'	=> $plugin->author_url, 
						'auth'		=> $plugin->author, 
						'buttons'	=> $button,
						'key'		=> $key,
						'count'		=> $plugin->count
				);
				
				$output .= $this->pane_template($args);
				
			}
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
				'count'		=> ''
		);
		
		$s = wp_parse_args( $args, $d);
		
		$buttons = sprintf('<div class="pane-buttons">%s</div>', $s['buttons']);
		
		$tags =  ( $s['tags'] ) ? sprintf('<br />Tags: %s', $s['tags']) : '';
		
		$count = ( $s['count'] ) ? sprintf('<br />Downloads: %s', $s['count']) : '';
		
		$screenshot = ( $s['image'] ) ? sprintf('<a class="screenshot-' . rtrim( $s['image'], '.jpg' ) . '" href="http://api.pagelines.com/sections/img/%s" rel="http://api.pagelines.com/sections/img/%s">Screenshot</a>' , $s['image'], $s['image']) : '';
		$js =  ( $screenshot ) ? "<script type='text/javascript' />jQuery('a.screenshot-" . rtrim( $s['image'], '.jpg' ) . "').imgPreview({imgCSS:{width:200}});</script>" : '';
		
		$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $s['name'], 'Version ' . $s['version'] );
		
		$auth = sprintf('<div class="pane-dets">by <a href="%s">%s</a> %s%s%s</div>', $s['auth_url'], $s['auth'], $screenshot, $tags, $count);
		
		$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);
		
		$r = sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>%s', $s['key'], $js);
		
		return sprintf('<div class="plpane pane-plugin"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></div>%s', $title, $body, $buttons, $r);
			
	}

 	/*
 	*
 	* Here is the themes function
 	* TODO TODO TODO
 	*
 	*/
	function extension_themes() {

		return 'Fetch from api list of themes and show ajax buttons...';		
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
		
		if( $mode == 'plugin_install' ){
			
			$upgrader = new Plugin_Upgrader();

			@$upgrader->install($url);
	
			if ( is_wp_error($upgrader->skin->result ) )
				$error = $upgrader->skin->result->get_error_message();
		
			$this->page_reload( 'pagelines_extend' );
			
		} elseif( $mode == 'plugin_activate' ){
			
	 		activate_plugin( $url );
	
	 		echo 'Activation complete! ';
	
	 		$this->page_reload( 'pagelines_extend' );
			
		} elseif( $mode == 'plugin_deactivate' ){

			deactivate_plugins( array( $url ) );

	 		echo 'Deactivation complete! ';

	 		$this->page_reload( 'pagelines_extend' );

		} elseif ( $mode == 'section_activate' ){
			
			$available = get_option( 'pagelines_sections_disabled' );
			
			unset( $available[$type][$url] );
			
			update_option( 'pagelines_sections_disabled', $available );
	 		
			// Output
			echo 'Activated';
	
			$this->page_reload( 'pagelines_extend' );
	 		
		} elseif ( $mode == 'section_deactivate' ){
			
			$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
	 		
			$disabled[$type][$url] = true; 
	 		
			update_option( 'pagelines_sections_disabled', $disabled );
	 		
			// Output
			echo 'Deactivated';
	
			$this->page_reload( 'pagelines_extend' );
	 	
	
		} elseif ( $mode == 'section_install' ) {
			
			$upgrader = new Plugin_Upgrader();

			$options = array( 	'package' => $url, 
					'destination' => WP_PLUGIN_DIR .'/pagelines-sections/sections/' . rtrim( basename( $url ), '.zip' ), 
					'clear_destination' => false,
					'clear_working' => false,
					'is_multi' => false,
					'hook_extra' => array() 
			);

			@$upgrader->run($options);
			echo 'Installed';
			$this->page_reload( 'pagelines_extend' );

		} elseif ( $mode == 'section_upgrade' ) {

			$upgrader = new Plugin_Upgrader();

			$options = array( 	'package' => $url, 
					'destination' => WP_PLUGIN_DIR .'/pagelines-sections/sections/' . $type, 
					'clear_destination' => true,
					'clear_working' => false,
					'is_multi' => false,
					'hook_extra' => array() 
			);

			@$upgrader->run($options);
			echo 'Ugraded';
			$this->page_reload( 'pagelines_extend' );
			
		} elseif ( $mode == 'plugin_upgrade' ) {
			
			$upgrader = new Plugin_Upgrader();

			$options = array( 	'package' => $url, 
					'destination' => WP_PLUGIN_DIR .'/' . rtrim( basename( $url ), '.zip' ), 
					'clear_destination' => true,
					'clear_working' => false,
					'is_multi' => false,
					'hook_extra' => array() 
			);

			@$upgrader->run($options);
			echo 'Upgraded';
			$this->page_reload( 'pagelines_extend' );			
		} elseif( $mode == 'plugin_delete' ) {
	
			delete_plugins( array( ltrim( $url, '/' ) ) );
			echo 'Deleted';
			$this->page_reload( 'pagelines_extend' );	
		}

		die(); // needed at the end of ajax callbacks
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
		
		$api = ( ! false == get_transient( 'pagelines_sections_api_' . $type ) )
				? get_transient( 'pagelines_sections_api_' . $type )
				: wp_remote_get( 'http://api.pagelines.com/' . $type . '/' );

		set_transient( 'pagelines_sections_api_' . $type, $api, 300 );

		return json_decode( $api['body'] );
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
		'PageLines_Sections' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-sections.png',
			'htabs' 	=> array(
				'installed'	=> array(
					'title'		=> "Installed PageLines Sections",
					'callback'	=> $extension_control->extension_sections()
					),
				'featured'	=> array(
					'title'		=> "Featured on PageLines.com",
					'class'		=> "right",
					'callback'	=> $extension_control->extension_sections()
					),
				'premium'	=> array(
					'title'		=> "Premium PageLines Sections",
					'class'		=> "right",
					'callback'	=> $extension_control->extension_sections()
					),
				'free'	=> array(
					'title'		=> "Free PageLines Sections",
					'class'		=> "right",
					'callback'	=> $extension_control->extension_sections_install()
					)
				),

			),
		
		'PageLines_Plugins' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-plugins.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> "Installed PageLines Plugins",
					'callback'	=> $extension_control->extension_plugins()
					),
				'free'		=> array(
					'title'		=> "Free Plugins",
					'class'		=> "right",
					'callback'	=> $extension_control->extension_plugins( 'free' )
					)
					
				)

			)
	);

	return apply_filters('extension_array', $d); 
}