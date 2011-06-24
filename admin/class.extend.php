<?php
/**
 * Plugin/theme installer class and section control.
 *
 * TODO cache api query.
 * TODO add enable all to sections.
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

		$output = '';
 		foreach( $available as $type ) {
	
 			if ( !$type )
 				continue;

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $section) { // main loop
 			

				$activate_js_call = sprintf($this->exprint, 'section_activate', $key, $section['type'], $section['class'], 'Activating');
				$deactivate_js_call = sprintf($this->exprint, 'section_deactivate', $key, $section['type'], $section['class'], 'Deactivating');

				$button = ( !isset( $disabled[$section['type']][$section['class']] ) ) 
							? OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call) 
							: OptEngine::superlink('Activate', 'blue', '', '', $activate_js_call);

				$args = array(
						'name' 		=> $section['name'], 
						'version'	=> $section['version'], 
						'desc'		=> $section['description'], 
						'auth_url'	=> $section['authoruri'], 
						'auth'		=> $section['author'], 
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
	function extension_plugins() {

		
		/*
			TODO cache this api query, it'll hardly EVER change, no need to fetch it on every page load!
		*/
		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		$plugins = json_decode( $api['body'] );
	
		if ( is_object($plugins) ) {
			
			$output = '';
			foreach( $plugins as $key => $plugin ) {
				
				$install_js_call = sprintf( $this->exprint, 'plugin_install', $key, 'plugin', $plugin->url, 'Installing');
				$activate_js_call = sprintf( $this->exprint, 'plugin_activate', $key, 'plugin', $plugin->file, 'Activating');
				$deactivate_js_call = sprintf( $this->exprint, 'plugin_deactivate', $key, 'plugin', $plugin->file, 'Deactivating');

				switch ( $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file ) ) {

					case 'active':
						$button = OptEngine::superlink('Deactivate Plugin', 'grey', '', '', $deactivate_js_call);
						break;
					
					case 'notactive':
						$button = OptEngine::superlink('Activate Plugin', 'blue', '', '', $activate_js_call);
						break;
					
					default:
						// were not installed, show the form!
						$button = OptEngine::superlink('Install Plugin', 'black', '', '', $install_js_call);
						break;
						
				}
				
				$args = array(
						'name' 		=> $plugin->name, 
						'version'	=> $plugin->version, 
						'desc'		=> $plugin->text, 
						'auth_url'	=> $plugin->author_url, 
						'auth'		=> $plugin->author, 
						'buttons'	=> $button, 
						'key'		=> $key
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
				'auth_url'	=> '', 
				'auth'		=> '', 
				'buttons'	=> '', 
				'key'		=> ''
		);
		
		$s = wp_parse_args( $args, $d);
		
		$buttons = sprintf('<div class="pane-buttons">%s</div>', $s['buttons']);
		
		$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $s['name'], 'Version ' . $s['version'] );
		
		$auth = sprintf('<div class="pane-dets">by <a href="%s">%s</a></div>', $s['auth_url'], $s['auth']);
		
		$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);
		
		$r = sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>', $s['key']);
		
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
			
			$upgrader = ( $type == 'theme' ) 
				? new Theme_Upgrader() 
				: new Plugin_Upgrader();

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
			 
		if (in_array( str_replace( '.php', '', basename($file) ), pagelines_register_plugins() ) )
			return 'active';
		else
			return 'notactive';
	}

 } // end PagelinesExtensions class