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
 	//	$this->plugin_installer_ui();
 	}

	function extension_install( $type = 'plugin', $url ) {

		include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$upgrader = ( $type == 'theme' ) ? new Theme_Upgrader() : new Plugin_Upgrader();

		$upgrader->install($url);
		if ( is_wp_error($upgrader->skin->result ) )
			$error = $upgrader->skin->result->get_error_message();
		return ( !isset($error) ) ? true : 'error';
	}

	function extension_themes() {
		
		
		return 'Fetch from api list of themes and show ajax buttons...';
		
	}

	function extension_plugins() {

plprint( pagelines_register_plugins(), 'pagelines_register_plugins');
		$api = wp_remote_get( 'http://api.pagelines.com/plugins/' );

		$plugins = json_decode( $api['body'] );

		if ( is_object($plugins) ) {

			foreach( $plugins as $plugin ) {
			
				$text .= '<p>Plugin name: ' . $plugin->name;
				$text .= '<br />Version: ' . $plugin->version;
				$text .= '<br />blurb: ' . $plugin->text;

				// Remember this form is a hack up!
				// 
				// here we need to check if our plugin is installed already, and change the form to ajax activate/deactivate
				// 

				switch ( $this->plugin_check_status( WP_PLUGIN_DIR . $plugin->file ) ) {

					case 'active':
						$text .= '<br />Activated - show deactivate ajax button';
						break;
					
					case 'notactive':
						$text .= '<br />Not active - show activate ajax button';
						break;
					
					default:
						// were not installed, show the form!
						$text .= '<br />Not installed - show install button';
						break;
				}
			}
		}
		return $text;
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