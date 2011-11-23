<?php
/**
 * Plugin/theme installer class and section control.
 *
 * TODO add enable all to sections.
 * TODO Make some use of the tags system
 *
 * Install PageLines plugins and looks after them.
 * 
 * @author PageLines
 *
 * @since 2.0.b3
 */

 class PagelinesExtensions {

 	function __construct() {

		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		$this->username = get_pagelines_credentials( 'user' );
		$this->password = get_pagelines_credentials( 'pass' );
		
		$this->ui = new PageLinesExtendUI;
		$this->fileactions = new PageLinesExtendActions;
		add_action( 'admin_init', array(&$this, 'launchpad_returns' ) );

 	}

	/*
	 *
	 * Cache cleaner.
	 * Flush all our transients ( Makes this save button a sort of reset button. )
	 *
	 */	
	static function flush_caches() {
	
		delete_transient( EXTEND_UPDATE );
		delete_transient( 'pagelines_extend_themes' );
		delete_transient( 'pagelines_extend_sections' );
		delete_transient( 'pagelines_extend_plugins' );
		delete_transient( 'pagelines_extend_integrations' );		
		delete_transient( 'pagelines_sections_cache' );
	}

	function extension_engine( $type, $set = ''){
		
			switch ( $type ){

				case 'section_added' :
					$out = ExtensionSections::extension_sections( $set, 'installed' );
					break;
				case 'section_extend' :
					$out = ExtensionSections::extension_sections( $set, 'install' );
					break;
				case 'theme' :
					$out = ExtensionThemes::extension_themes( $set );
					break;
				case 'plugin' :
					$out = ExtensionPlugins::extension_plugins( $set );
					break;
				case 'integration' :
					$out = ExtensionIntegrations::extension_integrations( $set );
					break;
			} 
			return $out;
	}

	 function master_array( $type, $key, $ext, $tab ){
		
		$a = array( 
			'plversion'		=>	CORE_VERSION,
			'price'		=>	'free',
			'featured'	=>	'false',
			'type'		=>	'internal',
			'depends'	=>	false
			);
		
		$ext = wp_parse_args( $ext, $a );

		$actions = array(
			'install'	=> array(
				'mode'		=> 'install',
				'text'		=> __( 'Install', 'pagelines' ),
				'dtext'		=> __( 'Installing', 'pagelines' ),
				'case'		=> $type.'_install',
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'install', $type, $key, $ext, $tab ),
				'condition'	=> $this->show_install_button( $type, $key, $ext, $tab ),			
				'path'		=> $this->get_the_path( 'install', $type, $key, $ext, $tab ),	
			),
			'redirect'	=> array(
				'mode'		=> 'redirect',
				'case'		=> 'redirect',
				'text'		=> __( 'Install &darr;', 'pagelines' ),
				'type'		=> $type,
				'condition'	=> $this->do_redirect(),
				'file'		=> $this->get_the_file( 'redirect', $type, $key, $ext, $tab ),
				'path'		=> $this->get_the_path( 'redirect', $type, $key, $ext, $tab ),	
			),
			'login'	=> array(
				'mode'		=> 'login',
				'case'		=> 'login',
				'condition'	=> $this->show_login_button( $type, $key, $ext, $tab ),				
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'login', $type, $key, $ext, $tab ),
				'text'		=> __( 'Login &rarr;', 'pagelines' ),
				'dtext'		=> __( 'Redirecting', 'pagelines' ),
			),
			'purchase'	=> array(
				'mode'		=> 'purchase',
				'case'		=> 'purchase',
				'text'		=> $this->purchase_text( $type, $key, $ext, $tab ),
				'dtext'		=> __( 'Redirecting', 'pagelines' ),
				'type'		=> $type,
				'condition'	=> $this->show_purchase_button( $type, $key, $ext, $tab ),
				'file'		=> $this->paypal_link( $type, $key, $ext, $tab ), 			
			),
			'activate'	=> array(
				'mode'		=> 'activate',
				'condition'	=> $this->show_activate_button( $type, $key, $ext, $tab ),
				'case'		=> $type.'_activate',
				'type'		=> $type,
				'path'		=> $this->get_the_path( 'activate', $type, $key, $ext, $tab ),	
				'file'		=> $this->get_the_file( 'activate', $type, $key, $ext, $tab ),
				'text'		=> __( 'Activate', 'pagelines' ),
				'dtext'		=> __( 'Activating', 'pagelines' ),
			) ,
			'deactivate'=> array(
				'mode'		=> 'deactivate',
				'condition'	=> $this->show_deactivate_button( $type, $key, $ext, $tab ),
				'case'		=> $type.'_deactivate',
				'type'		=> $type,
				'path'		=> $this->get_the_path( 'deactivate', $type, $key, $ext, $tab ),
				'file'		=> $this->get_the_file( 'deactivate', $type, $key, $ext, $tab ),
				'text'		=> __( 'Deactivate', 'pagelines' ),
				'dtext'		=> __( 'Deactivating', 'pagelines' ),
			),
			'upgrade'	=> array(
				'mode'		=> 'upgrade',
				'condition'	=> $this->show_upgrade_available( $type, $key, $ext, $tab ),
				'case'		=> $type.'_upgrade',
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'upgrade', $type, $key, $ext, $tab ),
				'path'		=> $key,
				'text'		=> sprintf( __( 'Upgrade to %s', 'pagelines' ), $ext['apiversion'] ),
				'dtext'		=> sprintf( __( 'Upgrading to version %s', 'pagelines' ), $ext['apiversion'] ),
			),
			'delete'	=> array(
				'mode'		=> 'delete',
				'condition'	=> $this->show_delete_button( $type, $key, $ext, $tab ),
				'case'		=> $type.'_delete',
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'delete', $type, $key, $ext, $tab ),
				'text'		=> __( 'Delete', 'pagelines' ),
				'dtext'		=> __( 'Deleting', 'pagelines' ),
				'confirm'	=> true
			),
			'installed'	=>	array(
				'mode'		=> 'installed',
				'condition'	=> $this->show_installed_button( $type, $key, $ext, $tab ),
				'text'		=> __( 'Installed', 'pagelines' ),
				),
			'version_fail'	=>	array(
				'case'		=> 'version_fail',
				'file'		=>	$ext['plversion'],
				'path'		=>	__( 'theme', 'pagelines' ),
				'condition'	=> $this->version_fail( $ext['plversion'] ),
				'text'		=> sprintf( __( '%s is required', 'pagelines' ), $ext['plversion'] ),
				),
			'dependancy'	=>	array(
				'case'		=> 'depends_fail',
				'file'		=>	$this->depends_nice_name( $type, $key, $ext, $tab ),
				'path'		=>	$type,
				'condition'	=> $this->depends_check( $type, $key, $ext, $tab ),
				'text'		=> __( 'Install', 'pagelines' ),
				),
			
			'download'	=> array(
				'mode'		=> 'download',
				'condition'	=> $this->show_download_button( $type, $key, $ext, $tab ),
				'case'		=> $type . '_download',
				'type'		=> 'integration',
				'file'		=> $key,
				'text'		=> __( 'Download <strong>&darr;</strong>', 'pagelines' ),
				'dtext'		=> __( 'Downloading', 'pagelines' )
				)		
		);	
		return $actions;	
	}

// ======================
// = Main button logic. =
// ======================

	 function show_in_tab( $type, $key, $ext, $tab ){

		$a = array( 
			'plversion'			=>	CORE_VERSION,
			'price'				=>	'free',
			'featured'			=>	'false',
			'loaded' 			=> ( isset( $ext['status']['status'] ) ) ? true : false,
			'sections-plugin'	=> ( isset( $ext['file']) && PL_EXTEND_SECTIONS_PLUGIN === basename( $ext['file'] ) ) ? true : false,
			'type'				=> 'internal'
			);
		
		$ext = wp_parse_args( $ext, $a );
				
		if($type == 'section'){
			
			$ext = (array) $ext;
			
			if ( $tab === 'user' && ( $ext['type'] === 'custom' || $ext['type'] === 'parent' ) )
				return false;
			elseif ( $tab === 'internal' && ( $ext['type'] === 'custom' || $ext['type'] === 'child' ) )
				return false;						
			elseif ( $tab === 'child' && ( $ext['type'] === 'child' || $ext['type'] === 'parent' ) )
				return false;
			elseif ( $ext['type'] == 'parent' && $ext['class_exists'] )
				return false;
			elseif( isset($ext['price']) && $ext['price'] != 'free' && $tab == 'free' )
				return false;
			elseif( $tab == 'premium' && $ext['price'] == 'free' )
				return false;
			elseif( $tab == 'featured' && $ext['featured'] == 'false' )
				return false;
			else 
				return true;
			
		} elseif($type == 'plugin'){

			if ( $tab == 'featured' && $ext['featured'] == 'false' )
				return false;
			
			if ( $tab === 'installed' && (!$ext['loaded'] || $ext['sections-plugin']) )
				return false;
				
			elseif ( ( $tab === 'premium' ) && $ext['price'] === 'free' )
				return false;

			elseif ( $tab === 'free' && $ext['price'] != 'free' )
				return false;
			
			else 
				return true;
				
		} elseif($type == 'theme'){

			$featured 	= ( isset( $ext['featured'] ) ) ? (bool) $ext['featured'] : false; 
			$ext['exists'] = $this->is_installed('theme', $key, $ext);
			
			if ( file_exists( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key ) ) )
				$exists = true;
			
			if ( $tab === 'featured' && $ext['featured'] === 'true' ) 
				return true;
			elseif ( $tab === 'featured' && $ext['featured'] === 'false' ) 
				return false;
				
			elseif ( ( $tab == 'premium' || $tab == 'featured' )  && $ext['price'] == 'free' ) 
				return false;

			elseif (  $tab == 'free' && $ext['price'] != 'free' ) 
				return false;
				
			elseif ( $tab == 'installed' && !$ext['exists'] )
				return false;
				
			else
				return true;	
		}
		return true;
	}

	 function version_check( $version ){
		return ( version_compare( CORE_VERSION, $version ) >= 0 ) ? true : false;
	}
	
	function depends_check( $type, $key, $ext, $tab ) {
		
		if ( $type == 'plugin' ) {
						
			if (  !empty( $ext['depends']) ) {		
				$file = sprintf( '%s/%s/%s.php', WP_PLUGIN_DIR, $ext['depends'], $ext['depends'] );
				if ( !file_exists( $file ) )
					return true;
			}
		return false;
		}
		return false;
	}
	
	function show_upgrade_available($type, $key, $ext, $tab){
	
		if ( $type == 'plugin' ) {
			
			if( $this->is_installed($type, $key, $ext)
				&& $this->upgrade_available( $this->get_api_version($type, $key, $ext), $this->get_the_version($type, $key, $ext) )
			){
				return true;
			} else 
				return false;		
		}
	
		if( $this->is_installed( $type, $key, $ext )
			&& $this->upgrade_available( $this->get_api_version( $type, $key, $ext ), $ext['version'] )
		){
			return true;
		} else 
			return false;
		
	}
	
	 function upgrade_available( $api_version, $installed_version ){

		if ( $api_version > $installed_version )
			return $api_version;
		else
			return false;
	}
	
	 function show_download_button( $type, $key, $ext, $tab ){

		if( $type == 'integration' && $this->updates_configured() )
			return true;
		else
			return false;
	}
		
	 function show_login_button( $type, $key, $ext, $tab ){

		if ( $type == 'integration' && !$this->updates_configured() )
			return true;
		
		if( !EXTEND_NETWORK 
			&& !$this->updates_configured()
			&& !$this->is_purchased( $type, $key, $ext )
			&& $this->in_the_store( $type, $key, $ext, $tab )
		) {
			return true;
		} else
			return false;
	}
	
	 function show_install_button( $type, $key, $ext, $tab){

		if ( $type == 'integration' )
			return false;

		if( !$this->is_installed( $type, $key, $ext ) 
			&& $this->is_purchased( $type, $key, $ext ) 
			&& $this->in_the_store( $type, $key, $ext, $tab ) 
			&& !EXTEND_NETWORK
			&& ! $this->version_fail( $ext['plversion'] )
			&& ! $this->depends_check( $type, $key, $ext, $tab )
		)
			return true;
		else
			return false;
	}
	
	function show_installed_button( $type, $key, $ext, $tab ){
		
		if( $this->is_installed( $type, $key, $ext )
			&& $this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
		
	}
	
	function show_delete_button( $type, $key, $ext, $tab ){

		if ( $type == 'section' && ( $tab == 'child' || $tab == 'internal' ) )
			return false;
		
		if( !$this->is_active( $type, $key, $ext )
			&& $this->is_installed( $type, $key, $ext )
			&& !EXTEND_NETWORK
			&& !$this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
		
	}

	 function is_installed( $type, $key, $ext, $tab = '' ){

		if( $type == 'section' ){
			
			$status = ( isset($ext['status'] ) ) ? true : false;
			
			if ( isset( $ext['base_file'] ) )
				$path = $ext['base_file'];
			else
				$path = sprintf( '%s/%s/section.php', PL_EXTEND_DIR, $ext['slug'] );
		
			if( file_exists( $path ) )
				return true;
			else 
				return false;

		} elseif( $type == 'plugin' ){

			if( isset( $ext['status']['status'] ) && $ext['status']['status'] != '' )
				return true;
			else 
				return false;

		} elseif( $type == 'theme' ){
			
			$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key );

			if ( file_exists( $check_file ) )
				$exists = true;
				
			if( isset( $exists ) && $data = get_theme_data( $check_file ) )
				return true;
			else
				return false;
		} 

	}
	
	function show_purchase_button( $type, $key, $ext, $tab ){
		
		if( !EXTEND_NETWORK 
			&& $this->updates_configured() 
			&& $this->in_the_store( $type, $key, $ext, $tab )
			&& !$this->is_purchased( $type, $key, $ext ) 
			&& !$this->is_installed( $type, $key, $ext )
			&& $this->is_premium( $type, $key, $ext )
		){
			return true;
		} else 
			return false;
	}

	function is_premium( $type, $key, $ext ){
		$ext = (array) $ext;
		if( isset( $ext['price'] ) 
			&& $ext['price'] != 'free' 
			&& $ext['price'] >= 0 
		){
			return true;
		} else 
			return false;
	}

	 function is_purchased( $type, $key, $info ){

		if($type == 'section'){
			
			return ( isset( $info['purchased'] ) ) ? true : false;
			
		} else {
			
			if( isset( $info['purchased'] ) )
				return true; 
			else
				return false;
		}
	}
	
	function show_activate_button( $type, $key, $ext, $tab ){
		
		if ( $type == 'integration' && is_integration_active($key) == false )
			return true;

		if( !$this->in_the_store( $type, $key, $ext, $tab )
			&& $this->is_installed( $type, $key, $ext, $tab )
			&& !$this->is_active( $type, $key, $ext )
		){
			return true;
		} else 
			return false;
	}
	
	function show_deactivate_button( $type, $key, $ext, $tab ){
		
		if ( $type == 'integration' ) 
			return is_integration_active( $key );
		
		if( $this->is_active( $type, $key, $ext )
			&& !$this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
	}
	
	 function is_active( $type, $key, $ext ){

		if ( $type == 'integration' )
			return is_integration_active($key);
			
		elseif($type == 'plugin'){
			if( isset( $ext['status']['status'] ) && $ext['status']['status'] == 'active' )
				return true;
			else 
				return false;
				
		}elseif( $type == 'section' ){
			
			if( isset( $ext['status'] ) && $ext['status'] == 'enabled' )
				return true;
			else
				return false;
			
		}	elseif( $type == 'theme' ){

				if( $key  == basename( get_stylesheet_directory() ) )
					return true;
				else
					return false;
			}
		
	}

	 function in_the_store( $type, $key, $ext, $tab ){

		if ( $type == 'integration' )
			return true;
		
		if( $tab == 'free' || $tab == 'premium' || $tab == 'featured' )
			return true;
		else
			return false;
		
	}

	 function purchase_button( $purchased = false, $store = true ){
		return ( $store && !EXTEND_NETWORK && !$purchased && !$this->login_button( $purchased ) ) ? true : false;
	}
	
	 function install_button( $installed = false, $purchased = false, $version = 0 ){
		return ( $this->version_check( $version ) && !EXTEND_NETWORK && $purchased && ! $installed) ? true : false;
	}
	
	 function version_fail( $version ){
		return ( ! $this->version_check( $version ) ) ? true : false;
	}
	
	 function updates_configured( ){
		return ( pagelines_check_credentials() ) ? true : false;
	}
	
	 function do_redirect( ){
		return ( EXTEND_NETWORK ) ? true : false;
	}

// ===================================
// = Images, files, links and paths. =
// ===================================
	 function image_path( $type, $key, $ext, $tab ) {
		
		if( $type == 'integration' ) {
			if( isset( $ext['screen'] ) && $ext['screen'] )
				return sprintf( 'http://www.pagelines.com/api/files/integrations/img/%s-thumb.png', $key );
		}
		
		if ( $type == 'plugin' ) {
			
			if ( $this->is_installed( $type, $key, $ext, $tab ) ) {
				
				if ( file_exists( sprintf( '%s/%s/thumb.png', WP_PLUGIN_DIR, $ext['slug'] ) ) )
					return sprintf( '%s/thumb.png', plugins_url( $ext['slug'] ) );
			} else {
				
				if( isset( $ext['screen'] ) && $ext['screen'] )
					return sprintf( '%s/files/%ss/img/%s-thumb.png', untrailingslashit( PL_API_FETCH ), $type, $ext['slug'] );
			}
		}
		
		if ( $type == 'section' ) {

				if ( isset( $ext['base_dir'] ) && file_exists( sprintf( '%s/thumb.png', $ext['base_dir'] ) ) )
					return sprintf( '%s/thumb.png', $ext['base_url'] );

				if( isset( $ext['screen'] ) && $ext['screen'] )
					return sprintf( '%s/files/%ss/img/%s-thumb.png', untrailingslashit( PL_API_FETCH ), $type, $ext['slug'] );
		}
		

		if ( $type == 'theme' ) {
			
			if ( ( $this->show_install_button( $type, $key, $ext, $tab ) 
					|| $this->show_purchase_button( $type, $key, $ext, $tab ) 
					|| $this->show_login_button( $type, $key, $ext, $tab ) 
				)
				&& isset( $ext['screen'] )
				&& $ext['screen']
			) {
				return sprintf( 'http://www.pagelines.com/api/files/themes/img/%s-thumb.png', $key );
					
			} elseif ( file_exists( sprintf( '%s/%s/thumb.png', get_theme_root(), $key ) ) )
				return sprintf( '%s/%s/thumb.png', get_theme_root_uri(), $key );
			else return sprintf( '%s/%s/screenshot.png', get_theme_root_uri(), $key );
				
		}
		return PL_ADMIN_IMAGES . '/thumb-default.png';
	}
	
	
	/**
	 *
	 *  @Todo make this a serialized array of all data.
	 *
	 */
	 function get_the_path( $button, $type, $key, $ext, $tab ){


		// If Section >>> 
		if ( ( $button == 'deactivate' || $button == 'activate' ) && $type == 'section' )
			return $ext['type'];
		
		if ( ( $button == 'install' || $button == 'delete' ) && $type == 'section' ) {
			return $key;	
		}	
		
		if( $type == 'integration' )
			return get_integration_path($ext);
		
	}
	
	 function get_the_file( $button, $type, $key, $ext, $tab ){

		if ( $button == 'delete' || $button == 'upgrade' ) {
			if ( $type == 'section'
			 	&& isset( $ext['base_dir'] ) 
					) {
				return basename( $ext['base_dir'] );	
			}
		}
		
		
		if ( $type == 'section' ) {
			return $ext['class'];	
		} elseif( $type == 'plugin' ){
			
			if( $button == 'activate'
				|| $button == 'deactivate'
				|| $button == 'delete'
			){
				return $ext['file'];
			} else
				return $key;
	
	
		}elseif( $type == 'theme' ){
			return $key;				
				
		} else
			return $key;
		
	}
		
	 function paypal_link( $type, $key, $ext, $tab ){
		return ( isset( $ext['productid'] ) ) ? sprintf( '%s,%s|%s|%s', $ext['productid'], $ext['uid'], $ext['price'], $ext['name'] ) : '';		
	}

	 function purchase_text( $type, $key, $ext, $tab ){
		
		$ext = (array) $ext;
		
		$price = ( isset( $ext['price'] ) ) ? sprintf( ' <span class="prc">($%s)</span>', $ext['price'] ) : '';

		return sprintf( '%s%s', __( 'Purchase', 'pagelines' ), $price ); 
	}
	
	 function get_the_version($type, $key, $ext){
	
		// has to be the installed version.
		
		if ( $this->is_installed( $type, $key, $ext ) ) {
			
			if ( $type == 'plugin' )
				return $ext['status']['data']['Version'];
		}
			return $ext['version'];
	}

	function get_api_version( $type, $key, $ext ) {
		
		if ( isset( $ext['apiversion'] ) )
			return $ext['apiversion'];
		
		return false;
	}
	
	function parse_buttons( $actions, $core_actions ){
		
		$actions = wp_parse_args( $actions, $core_actions );
		
		foreach( $actions as $action => $button ){
			if( isset( $core_actions[$action] ) ){
				$actions[$action] = wp_parse_args( $button, $core_actions[$action] );
			}
		}
		return $actions;
	}	

	/**
	* Simple cache.
	* @return object
	*/
	function get_latest_cached( $type, $flush = null ) {
		
		$url = trailingslashit( PL_API . $type );
		$options = array(
			'sslverify'	=>	false,
			'timeout'	=>	5,
			'body' => array(
				'username'	=>	( $this->username != '' ) ? $this->username : false,
				'password'	=>	( $this->password != '' ) ? $this->password : false,
				'flush'		=>	$flush
			)
		);
		
		if ( false === ( $api_check = get_transient( 'pagelines_extend_' . $type ) ) ) {
			
			// ok no transient, we need an update...
			
			$response = pagelines_try_api( $url, $options );
			
			if ( $response !== false ) {
				
				// ok we have the data parse and store it
				
				$api = wp_remote_retrieve_body( $response );
				set_transient( 'pagelines_extend_' . $type, true, 86400 );
				update_option( 'pagelines_extend_' . $type, $api );
			} 

		}
		$api = get_option( 'pagelines_extend_' . $type, false );	

		if( ! $api )
			return __( '<h2>Unable to fetch from API</h2>', 'pagelines' );

		return json_decode( $api );
	}

	/**
	 * Refresh the PageLines store cache
	 * 
	 */
	function launchpad_returns() {
		
		if (isset( $_GET['api_returned'] ) || isset( $_POST['reset_store'] ) )
			$this->flush_caches();
	}

	/**
	 * Check if we have the extend plugin.
	 * 
	 */	
	function has_extend_plugin( $status = false ){
		
		if($status){
			
			if( file_exists( PL_EXTEND_INIT ) && current( $this->plugin_check_status( PL_EXTEND_INIT ) ) == 'notactive' )
				return 'notactive';
			elseif( !is_dir( PL_EXTEND_DIR ) || !file_exists( PL_EXTEND_INIT ) )
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
	
	/**
	 * Get current status for a plugin.
	 * 
	 */
	function plugin_check_status( $file ) {
		
		if ( !file_exists( $file ) )
			return ;
		$data = get_plugin_data( $file );

		if ( in_array( str_replace( '.php', '', basename( $file ) ), pagelines_register_plugins() ) ) 
			return array( 'status' => 'active', 'version' => $data['Version'], 'data' => $data );
		else
			return array( 'status' => 'notactive', 'version' => $data['Version'], 'data' => $data );
	}
	
	function get_the_tags( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['tags'] ) && ! empty( $ext['tags'] ) )
			return $ext['tags'];
		else
			return '';
	}
	
	function get_the_author( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['author'] ) && ! empty( $ext['author'] ) )
			return $ext['author'];
		else
			return '';
	}

	function get_the_author_uri( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['external'] ) && ! empty( $ext['external'] ) )
			return $ext['external'];

		if ( isset( $ext['author_url'] ) && ! empty( $ext['author_url'] ) )
			return $ext['author_url'];
		else
			return admin_url();
	}	
	
	function get_the_name( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['name'] ) && ! empty( $ext['name'] ) )
			return $ext['name'];
		else
			return '';
	}
	
	function get_the_desc( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['text'] ) && ! empty( $ext['text'] ) )
			return $ext['text'];

		if ( isset( $ext['description'] ) )
			return wp_kses( $ext['description'], array() );
		
		return '';
	}
	
	function get_the_count( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['count'] ) && ! empty( $ext['count'] ) )
			return $ext['count'];
		else
			return '0';
	}
	
	function get_the_screen( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['screen'] ) && ! empty( $ext['screen'] ) )
			return $ext['screen'];
		else
			return false;
	}
	
	function get_the_object( $type, $key, $ext, $tab ) {
		
		if ( isset( $ext['class'] ) && ! empty( $ext['class'] ) )
			return $ext['class'];
		else
			return false;
	}
	
	function get_info_url( $type, $key, $ext, $tab ) {
		
		$slug = ( isset( $ext['slug'] ) ) ? $ext['slug'] : $key;
		return sprintf( '%s/%ss/%s/?product_ref=true', PL_STORE_URL, $type, $slug );
	}
	
	function get_demo_url( $type, $key, $ext, $tab ) {
		
		return ( isset( $ext['demo'] ) ) ? $ext['demo'] : '';

	}
	
	function get_external_url( $type, $key, $ext, $tab ) {
	
	if ( isset( $ext['external'] ) )
		return $ext['external'];
		
	if ( isset( $ext['authorurl'] ) )
		return $ext['authorurl'];

	}
	
	function depends_nice_name( $type, $key, $ext, $tab ) {

		if ( isset( $ext['depends'] ) ) {
			
			if ( $type == 'plugin' ) {
				
				$plugins = $this->get_latest_cached( 'plugins' );
				
				if ( isset( $plugins->$ext['depends']) && !empty( $plugins->$ext['depends']->name ) )
					return $plugins->$ext['depends']->name;
			}	
		}
	}
	
	function get_master_list( $extension, $type, $tab, $mode = '') {
		
		$list = array();
		foreach( $extension as $key => $ext ) {
			
			$ext = (array) $ext;

			if( !$this->show_in_tab( $type, $key, $ext, $tab ) )
				continue;	
			
			if ( 'installed' == $mode )
				$array_key = basename( $ext['base_dir'] );
			else
				$array_key = $key;

			$list[$array_key] = $this->master_list( $type, $key, $ext, $tab );
		}
		return ( !empty( $list ) ) ? $list : '';
	}

	function master_list( $type, $key, $ext, $tab ) {
		
		$ext['apiversion'] = ( isset( $ext['apiversion'] ) ) ? $ext['apiversion'] : $ext['version'];
		
		if ( !isset( $ext['status'] ) )
			$ext['status'] = array( 'status' => '' );
		
		$list = array(
				$type		=> $ext,
				'name' 		=> $this->get_the_name( $type, $key, $ext, $tab ), 
				'active'	=> $this->is_active( $type, $key, $ext ),
				'version'	=> $this->get_the_version( $type, $key, $ext ), 
				'desc'		=> $this->get_the_desc( $type, $key, $ext, $tab ),
				'tags'		=> $this->get_the_tags( $type, $key, $ext, $tab ),
				'image'		=> $this->image_path( $type, $key, $ext, $tab ),
				'auth'		=> $this->get_the_author( $type, $key, $ext, $tab ),
				'auth_url'	=> $this->get_the_author_uri( $type, $key, $ext, $tab ), 						
				'key'		=> $key,
				'type'		=> $type,
				'infourl'	=> $this->get_info_url( $type, $key, $ext, $tab ),
				'object'	=> $this->get_the_object( $type, $key, $ext, $tab ),
				'count'		=> $this->get_the_count( $type, $key, $ext, $tab ),
				'screen'	=> $this->get_the_screen( $type, $key, $ext, $tab ),
				'actions'	=> $this->master_array( $type, $key, $ext, $tab  ),
				'demo'		=> $this->get_demo_url( $type, $key, $ext, $tab ),
				'external'	=> $this->get_external_url( $type, $key, $ext, $tab ),
		);
		
		return $list;
	}

} // [END]