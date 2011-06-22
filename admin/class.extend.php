<?php
/**
 * Plugin installer class
 *
 * Install PageLines plugins and looks after them.
 *
 * @since 2.0.b3
 */

 class PagelinesExtensions {
 	

 	function __construct() {

		add_action('admin_head', array(&$this, 'extension_js'));
		add_action('wp_ajax_pagelines_ajax_extension_install', array(&$this, 'extension_install'));
 	}



	function extension_themes() {
		return 'Fetch from api list of themes and show ajax buttons...';		
	}

	function extension_plugins() {

		plprint( pagelines_register_plugins(), 'pagelines_register_plugins');
		
		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		$plugins = json_decode( $api['body'] );

	
		if ( is_object($plugins) ) {
			$count = 3;
			$output = '';
			$output .= sprintf('<div class="install_response"></div><div class="clear"></div>');
			foreach( $plugins as $plugin ) {
				
				$start_row = ($count % 3 == 0) ? true : false;
				$end_row = ( ($count+1) % 3 == 0) ? true : false;
				$cl = ($end_row) ? 'pplast' : '';
			
				/**
				 * 
				 * Remember this form is a hack up!
				 * TODO here we need to check if our plugin is installed already, and change the form to ajax activate/deactivate
				 *
				 */
				$url = 'http://api.pagelines.com/plugins/test1.zip';
				
				$install_js_call = sprintf('onClick="extendInstall(\'%s\', \'%s\')"', 'plugin', $url);
				
				switch ( $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file ) ) {

					case 'active':
						$button = OptEngine::input_button('ID', 'Deactivate Plugin', '', 'onClick="extendInstall"');
						break;
					
					case 'notactive':
						$button = OptEngine::input_button('ID', 'Activate Plugin');
						break;
					
					default:
						// were not installed, show the form!
						$button = OptEngine::input_button('ID', 'Install Plugin', '', $install_js_call);
						break;
						
				}
				
				
				// Output
				
				
				//if($start_row) $output .= sprintf('<div class="pprow">');
				
				$t = sprintf('<h3 class="pane-title">%s</h3><div class="pane-sub">%s</div><div class="pane-desc">%s</div><div class="pane-button">%s</div>', $plugin->name, 'Version '.$plugin->version, $plugin->text, $button);
				
				$output .= sprintf('<div class="plpane pane-plugin pp3 %s"><div class="plpane-hl fix"><div class="plpane-pad fix">%s</div></div></div>', $cl, $t);
				
				//if($end_row) $output .= sprintf('</div>');
				$count++;
			}
		}
		
		
		
		return $output;
	}
	
	/**
	 * 
	 * Add Javascript to header (hook in contructor)
	 * 
	 */
	function extension_js(){ ?>
		
		<script type="text/javascript">/*<![CDATA[*/

		function extendInstall(type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_install',
					extend_type: type,
					extend_url: url
				};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.post(ajaxurl, data, function(response) {
					
					jQuery('.install_response').show().html(response).delay(10000).slideUp();
				});
			
		}

		/*]]>*/</script>
		
<?php }

	/**
	 * 
	 * Extension AJAX callback
	 * 
	 */
	function extension_install(  ) {
		// 1. Libraries
		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		
		// 2. Variable Setup
		$type =  $_POST['extend_type'];
		$url =  $_POST['extend_url'];
		
		// 3. Do our thing...
		$upgrader = ( $type == 'theme' ) ? new Theme_Upgrader() : new Plugin_Upgrader();

		@$upgrader->install($url);
	
		if ( is_wp_error($upgrader->skin->result ) )
			$error = $upgrader->skin->result->get_error_message();
		
		// 4. Output
		echo ( !isset($error) ) ? true : 'error'; // nothing needs to be returned, just echo'd
	
		die(); // needed at the end of ajax callbacks
	}

	function plugin_check_status( $file ) {
		
		if ( !file_exists( $file ) )
			return ;
			 
		if (in_array( str_replace( '.php', '', basename($file) ), pagelines_register_plugins() ) )
			return 'active';
		else
			return 'notactive';
	}

 } // end PagelinesPlugins class