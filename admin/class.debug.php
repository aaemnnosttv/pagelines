<?php
/**
 *
 * PageLines Debugging system
 *
 * Enabled in Framework admin 'advanced' tab
 * Show server debug info using special URL
 *
 * @package PageLines Framework
 * @subpackage Debugging
 * @since 2.1
 *
 */


/**
 * PageLines Debugging Information Class
 *
 * @package PageLines Framework
 * @subpackage Debugging
 * @since 2.1
 *
 */
class PageLinesDebug {

	// Array of debugging information
	var $debug_info = array();


	/**
	*
	* @TODO document
	*
	*/
	function __construct( ) {

		$this->wp_debug_info();
		$this->debug_info_template();
	}

	/**
	 * Main output.
	 * @return str Formatted results for page.
	 *
	 */
	function debug_info_template(){

		$out = '';
		foreach($this->debug_info as $element ) {

			if ( $element['value'] ) {

				$out .= '<h4>'.ucfirst($element['title']).' : '. ucfirst($element['value']);
				$out .= (isset($element['extra'])) ? "<br /><code>{$element['extra']}</code>" : '';
				$out .= '</h4>';
			}
		}
		wp_die( $out, 'PageLines Debug Info', array( 'response' => 200, 'back_link' => true) );
	}

	/**
	 * Debug tests.
	 * @return array Test results.
	 */
	function wp_debug_info(){

		global $wpdb, $wp_version, $platform_build;

			// Set data & variables first
			$uploads = wp_upload_dir();
			// Get user role
			$current_user = wp_get_current_user();
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);

			// Format data for processing by a template

			$this->debug_info[] = array(
				'title'	=> 'WordPress Version',
				'value' => $wp_version,
				'level'	=> false,
			);

			$this->debug_info[] = array(
				'title'	=> 'Multisite Enabled',
				'value' => ( is_multisite() ) ? 'Yes' : 'No',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Current Role',
				'value' => $user_role,
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework Path',
				'value' => '<code>' . get_template_directory() . '</code>',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework URI',
				'value' => '<code>' . get_template_directory_uri() . '</code>',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Framework Version',
				'value' => PL_CORE_VERSION,
				'level'	=> false
			);
			$this->debug_info[] = array(
				'title'	=> 'Framework Build',
				'value' => $platform_build ,
				'level'	=> false
			);
			$this->debug_info[] = array(
				'title'	=> 'PHP Version',
				'value' => floatval( phpversion() ),
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Child theme',
				'value' => ( get_template_directory() != get_stylesheet_directory() ) ? 'Yes' : '',
				'level'	=> false,
				'extra' => get_stylesheet_directory() . '<br />' . get_stylesheet_directory_uri()
			);

			$this->debug_info[] = array(
				'title'	=> 'Ajax disbled',
				'value' => ( get_pagelines_option( 'disable_ajax_save' ) ) ? 'Yes':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Safe Mode',
				'value' => ( (bool) ini_get('safe_mode') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Open basedir restriction',
				'value' => ( (bool) ini_get('open_basedir') ) ? 'Yes!':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Register Globals',
				'value' => ( (bool) ini_get('register_globals') ) ? 'Yes (bad)':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP Magic Quotes gpc',
				'value' => ( (bool) ini_get('magic_quotes_gpc') ) ? 'Yes (bad)':'',
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'PHP memory',
				'value' => intval(ini_get('memory_limit') ),
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'Mysql version',
				'value' => ( version_compare( $wpdb->get_var("SELECT VERSION() AS version"), '6' ) < 0  ) ? $wpdb->get_var("SELECT VERSION() AS version"):'',
				'level'	=> false
			);
			$ex_dir = PL_EXTEND_DIR;

			if( ! is_dir( $ex_dir ) ) {

				$this->debug_info[] = array(
					'title'	=> 'Sections Plugin',
					'value' => 'Not Installed!',
					'level'	=> true,
				);

			}

			if( is_dir( $ex_dir ) ) {
				$this->debug_info[] = array(
					'title'	=> 'Sections DIR',
					'value' => ( !is_writable( PL_EXTEND_DIR ) ) ? "Not Writable!":'Writable.',
					'level'	=> true,
					'extra' => PL_EXTEND_DIR
				);
			}

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if( is_dir( $ex_dir ) && ! is_plugin_active( 'pagelines-sections/pagelines-sections.php' ) ) {
				$this->debug_info[] = array(
					'title'	=> 'Sections Plugin',
					'value' => 'Not Active!',
					'level'	=> true,
				);
			}

			$this->debug_info[] = array(
				'title'	=> 'PHP type',
				'value' => php_sapi_name(),
				'level'	=> false
			);



			$processUser = ( ! function_exists( 'posix_geteuid') || ! function_exists( 'posix_getpwuid' ) ) ? 'posix functions are disabled on this host!' : posix_getpwuid(posix_geteuid());
			if ( is_array( $processUser ) )
				$processUser = $processUser['name'];

			$this->debug_info[] = array(
				'title'	=> 'PHP User',
				'value' => $processUser,
				'level'	=> false
			);

			$this->debug_info[] = array(
				'title'	=> 'OS',
				'value' => PHP_OS,
				'level'	=> false
			);

			if ( get_pagelines_option('disable_updates') == true || ( is_multisite() && ! is_super_admin() ) ) {
				$this->debug_info[] = array(
					'title'	=> 'Automatic Updates',
					'value' => 'Disabled',
					'level'	=> false
				);
			} else {
				$this->debug_info[] = array(
					'title'	=> 'Launchpad',
					'value' => ( ! pagelines_check_credentials() ) ? 'Not logged in.' : sprintf( 'Logged in ( %s ) ', get_pagelines_credentials( 'user' ) ),
					'level'	=> false
				);
			if ( pagelines_check_credentials() ) {
				$this->debug_info[] = array(
					'title'	=> 'Licence',
					'value' => get_pagelines_credentials( 'licence' ),
					'extra'	=> get_pagelines_credentials( 'error' ),
					'level'	=> false
				);
			}
		}
			$this->debug_info[] = array(
				'title'	=> 'Plugins',
				'value' => $this->debug_get_plugins(),
				'level'	=> false
			);

	}
	/**
	 * Get active plugins.
	 * @return str List of plugins.
	 *
	 */
	function debug_get_plugins() {
		$plugins = get_option('active_plugins');
		if ( $plugins ) {
			$plugins_list = '';
			foreach($plugins as $plugin_file) {
					$plugins_list .= '<code>' . $plugin_file . '</code>';
					$plugins_list .= '<br />';

			}
			return ( isset( $plugins_list ) ) ? count($plugins) . "<br />{$plugins_list}" : '';
		}
	}
//-------- END OF CLASS --------//
}

if ( ! is_admin() ) {

	if( isset( $_GET['pldebug'] ) )
		new PageLinesDebug;

}