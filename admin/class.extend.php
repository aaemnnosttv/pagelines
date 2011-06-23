<?php
/**
 * Plugin/theme installer class and section control.
 *
 * TODO cache api query.
 * TODO add enable all to sections.
 *
 * Install PageLines plugins and looks after them.
 *
 * @since 2.0.b3
 */

 class PagelinesExtensions {

 	function __construct() {

		add_action('admin_head', array(&$this, 'extension_js'));
		add_action('wp_ajax_pagelines_ajax_extension_plugin_install', array(&$this, 'extension_plugin_install'));
		add_action('wp_ajax_pagelines_ajax_extension_plugin_activate', array(&$this, 'extension_plugin_activate'));
		add_action('wp_ajax_pagelines_ajax_extension_plugin_deactivate', array(&$this, 'extension_plugin_deactivate'));
		add_action('wp_ajax_pagelines_ajax_extension_section_activate', array(&$this, 'extension_section_activate'));
		add_action('wp_ajax_pagelines_ajax_extension_section_deactivate', array(&$this, 'extension_section_deactivate'));
 	}

 	function extension_section_activate() {

 		$file = $_POST['extend_url'];
 		$type = $_POST['extend_type'];
 		$available = get_option( 'pagelines_sections_disabled' );
		unset( $available[$type][$file] );
		update_option( 'pagelines_sections_disabled', $available );
 		$this->page_reload( 'pagelines_extend_sections' );
 		echo 'Activated.';
 		die();
 	}

 	function extension_section_deactivate() {

 		$file = $_POST['extend_url'];
 		$type = $_POST['extend_type'];
 		$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
 		$disabled[$type][$file] = true; 
 		update_option( 'pagelines_sections_disabled', $disabled );
 		$this->page_reload( 'pagelines_extend_sections' );
 		echo 'Deactivated';
 		die();
 	}

 	function extension_sections() {

 		/*
 		*
 		* Clear section cache and re-generate
 		*
 		*/
 		global $load_sections;
 		delete_option( 'pagelines_sections_cache' );
 		$load_sections->pagelines_register_sections();
 		$available = get_option( 'pagelines_sections_cache' );
 		$disabled = get_option( 'pagelines_sections_disabled', array() );

		$rn = 2;
		$count = $rn;
		$output = '';
 		foreach( $available as $type ) {
 			if ( !$type )
 				continue;
 			asort($type); // sort
 			foreach( $type as $key => $section) { // main loop
 			

				$activate_js_call = sprintf('onClick="extend_section_Activate(\'%s\', \'%s\', \'%s\')"', $key, $section['type'], $section['class']);
				$deactivate_js_call = sprintf('onClick="extend_section_Deactivate(\'%s\', \'%s\', \'%s\')"', $key, $section['type'], $section['class']);

				$button = ( !isset( $disabled[$section['type']][$section['class']] ) ) 
							? OptEngine::superlink('Deactivate', 'grey', '', '', $deactivate_js_call) 
							: OptEngine::superlink('Activate', 'grey', '', '', $activate_js_call);

				$buttons = sprintf('<div class="pane-buttons">%s</div>', $button);
				
				$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $section['name'], 'Version ' . $section['version'] );
				
				$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s<div class="pane-dets">by <a href="%s">%s</a></div></div></div>', $section['description'], $section['authoruri'], $section['author']);
				
				$output .= sprintf('<div class="plpane pane-plugin %s"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></div>', $cl, $title, $body, $buttons);
				
				$output .= sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>', $key);
				
				$count++;

 			}	// end main loop

 		} // end type loop
	return $output;
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

	function extension_plugins() {

		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		//
		// TODO cache the api query above, it'll hardly EVER change, no need to fetch it on every page load!
		//

		$plugins = json_decode( $api['body'] );
	
		if ( is_object($plugins) ) {
			$rn = 2;
			$count = $rn;
			$output = '';
			
			foreach( $plugins as $key => $plugin ) {
				
				$start_row = ($count % $rn == 0) ? true : false;
				$end_row = ( ($count+1) % $rn == 0 || $plugin == end($plugins)) ? true : false;
				$cl = ($end_row) ? 'pplast' : '';

				$install_js_call = sprintf('onClick="extend_plugin_Install(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->url);
				$activate_js_call = sprintf('onClick="extend_plugin_Activate(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->file);
				$deactivate_js_call = sprintf('onClick="extend_plugin_Deactivate(\'%s\', \'%s\', \'%s\')"', $key, 'plugin', $plugin->file);

				switch ( $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file ) ) {

					case 'active':
						$button = OptEngine::superlink('Deactivate Plugin', '', '', '', $deactivate_js_call);
						break;
					
					case 'notactive':
						$button = OptEngine::superlink('Activate Plugin', '', '', '', $activate_js_call);
						break;
					
					default:
						// were not installed, show the form!
						$button = OptEngine::superlink('Install Plugin', '', '', '', $install_js_call);
						break;
						
				}
				
				// Output
		
				$buttons = sprintf('<div class="pane-buttons">%s</div>', $button);
				
				$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $plugin->name, 'Version ' . $plugin->version);
				
				$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s<div class="pane-dets">by <a href="%s">%s</a></div></div></div>', $plugin->text, $plugin->author_url, $plugin->author );	
				
				$output .= sprintf('<div class="plpane pane-plugin %s"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></div>', $cl, $title, $body, $buttons);
				
				$output .= sprintf('<div id="response%s" class="install_response"><div class="rp"></div></div>', $key);
				
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

		function extend_plugin_Install(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_plugin_install',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Installing').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Installing'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
					}
				});
		}

		function extend_plugin_Activate(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_plugin_activate',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Activating').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Activating'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
					}
				});
		}

		function extend_plugin_Deactivate(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_plugin_deactivate',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Deactivating').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Deactivating'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
					}
				});
		}

		function extend_section_Activate(key, type, url){
			
				var data = {
					action: 'pagelines_ajax_extension_section_activate',
					extend_type: type,
					extend_url: url
				};

				var saveText = jQuery('#response'+key);
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						saveText.html('Activating').slideDown();
						
						// add some dots while saving.
						interval = window.setInterval(function(){
							var text = saveText.text();
							if (text.length < 13){	saveText.text(text + '.'); }
							else { saveText.text('Activating'); } 
						}, 400);

					},
				  	success: function( response ){
					
						window.clearInterval(interval); // clear dots...
						
						saveText.html(response).delay(6500).slideUp();
					}
				});
		}

			function extend_section_Deactivate(key, type, url){
		
			var data = {
				action: 'pagelines_ajax_extension_section_deactivate',
				extend_type: type,
				extend_url: url,
				extend_key: key
			};

			var saveText = jQuery('#response'+key);
			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				beforeSend: function(){

					saveText.html('Deactivating').slideDown();
					
					// add some dots while saving.
					interval = window.setInterval(function(){
						var text = saveText.text();
						if (text.length < 13){	saveText.text(text + '.'); }
						else { saveText.text('Deactivating'); } 
					}, 400);

				},
			  	success: function( response ){
				
					window.clearInterval(interval); // clear dots...
					
					saveText.html(response).delay(6500).slideUp();
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
	function extension_plugin_install(  ) {
		
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
		//	$out = ( !isset($error) ) ? true : 'error'; // nothing needs to be returned, just echo'd
			$this->page_reload( 'pagelines_extend_plugins' );
			die(); // needed at the end of ajax callbacks
	}
 	function extension_plugin_activate() {
 		$file =  $_POST['extend_url'];
 		activate_plugin( $file );
 		echo 'Activation complete! ';
 		$this->page_reload( 'pagelines_extend_plugins' );
 		die();
 	}

 	function extension_plugin_deactivate() {
 		$file =  $_POST['extend_url'];
 		deactivate_plugins( array($file) );
 		echo 'Deactivation complete! ';
 		$this->page_reload( 'pagelines_extend_plugins' );
 		die();
 	}


 	/**
 	*
 	* Helper functions
 	*
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