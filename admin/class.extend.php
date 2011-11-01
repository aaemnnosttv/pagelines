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
		add_filter( 'http_request_args', array( &$this, 'pagelines_plugins_remove' ), 10, 2 );
		
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

	private function master_array( $type, $key, $ext, $tab ){
		
		$a = array( 
			'plversion'		=>	CORE_VERSION,
			'price'		=>	'free',
			'featured'	=>	'false',
			'upgrade'	=>	''
			);
		
		$ext = wp_parse_args( $ext, $a );
		
		if ( !$ext['upgrade'] )
			$ext['upgrade'] = $ext['version'];

		$actions = array(
			'install'	=> array(
				'mode'		=> 'install',
				'text'		=> __( 'Install', 'pagelines' ),
				'dtext'		=> __( 'Installing', 'pagelines' ),
				'case'		=> $type.'_install',
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'install', $type, $key, $ext, $tab ),
				'condition'	=> $this->show_install_button( $type, $key, $ext, $tab),			
				'path'		=> $this->get_the_path( 'install', $type, $key, $ext, $tab),	
			),
			'redirect'	=> array(
				'mode'		=> 'redirect',
				'case'		=> 'redirect',
				'text'		=> __( 'Install &darr;', 'pagelines' ),
				'type'		=> $type,
				'condition'	=> $this->do_redirect(),
				'file'		=> $this->get_the_file( 'redirect', $type, $key, $ext, $tab ),
				'path'		=> $this->get_the_path( 'redirect', $type, $key, $ext, $tab),	
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
				'path'		=> $this->get_the_path( 'activate', $type, $key, $ext, $tab),	
				'file'		=> $this->get_the_file( 'activate', $type, $key, $ext, $tab ),
				'text'		=> __( 'Activate', 'pagelines' ),
				'dtext'		=> __( 'Activating', 'pagelines' ),
			) ,
			'deactivate'=> array(
				'mode'		=> 'deactivate',
				'condition'	=> $this->show_deactivate_button( $type, $key, $ext, $tab ),
				'case'		=> $type.'_deactivate',
				'type'		=> $type,
				'path'		=> $this->get_the_path( 'deactivate', $type, $key, $ext, $tab),
				'file'		=> $this->get_the_file( 'deactivate', $type, $key, $ext, $tab ),
				'text'		=> __( 'Deactivate', 'pagelines' ),
				'dtext'		=> __( 'Deactivating', 'pagelines' ),
			),
			'upgrade'	=> array(
				'mode'		=> 'upgrade',
				'condition'	=> $this->show_upgrade_available($type, $key, $ext, $tab),
				'case'		=> $type.'_upgrade',
				'type'		=> $type,
				'file'		=> $this->get_the_file( 'upgrade', $type, $key, $ext, $tab ),
				'path'		=> $key,
				'text'		=> sprintf(__( 'Upgrade to %s', 'pagelines' ), $ext['upgrade'] ),
				'dtext'		=> sprintf( __( 'Upgrading to version %s', 'pagelines' ), $ext['upgrade'] ),
			),
			'delete'	=> array(
				'mode'		=> 'delete',
				'condition'	=> $this->show_delete_button($type, $key, $ext, $tab),
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

	private function show_in_tab( $type, $key, $ext, $tab ){

		$a = array( 
			'plversion'		=>	CORE_VERSION,
			'price'		=>	'free',
			'featured'	=>	"false"
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
			elseif( $tab == 'featured' && $ext['featured']  )
				return false;
			else 
				return true;
			
		} elseif($type == 'plugin'){
			
			if ( $tab === 'installed' && (!$ext['loaded'] || $ext['sections-plugin']) )
				return false;
				
			elseif ( ( $tab === 'premium' || $tab === 'featured' ) && $ext['price'] === 'free' )
				return false;

			elseif ( $tab === 'free' && $ext['price'] != 'free' )
				return false;
			
			else 
				return true;
				
		} elseif($type == 'theme'){

			$featured 	= ( isset( $ext['featured'] ) ) ? (bool) $ext['featured'] : false; 
			$ext['exists'] 		= $this->is_installed('theme', $key, $ext);
			
			if ( file_exists( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key ) ) )
				$exists = true;
			
			if ( $tab === 'featured' && $ext['featured'] === "true" ) 
				return true;
			elseif ( $tab === 'featured' && $ext['featured'] === "false" ) 
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
	}
	
	private function show_download_button( $type, $key, $ext, $tab){

		if( $type == 'integration' && $this->updates_configured() )
			return true;
		else
			return false;
	}

	private function version_check( $version ){
		return ( version_compare( CORE_VERSION, $version ) >= 0 ) ? true : false;
	}
		
	function show_upgrade_available($type, $key, $ext, $tab){
	
		if ( $type == 'plugin' ) {
			
			if( $this->is_installed($type, $key, $ext)
				&& $this->upgrade_available( $this->get_the_version($type, $key, $ext), $ext['status']['version'])
			){
				return true;
			} else 
				return false;		
		}
	
		if( $this->is_installed($type, $key, $ext)
			&& $this->upgrade_available( $this->get_the_version($type, $key, $ext), $ext['version'])
		){
			return true;
		} else 
			return false;
		
	}
		
	private function show_login_button( $type, $key, $ext, $tab ){

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
	
	private function show_install_button( $type, $key, $ext, $tab){

		if ( $type == 'integration' )
			return false;

		if( !$this->is_installed($type, $key, $ext) 
			&& $this->is_purchased($type, $key, $ext) 
			&& $this->in_the_store( $type, $key, $ext, $tab ) 
			&& !EXTEND_NETWORK
			&& ! $this->version_fail( $ext['plversion'] )
		)
			return true;
		else
			return false;
	}
	
	function show_installed_button( $type, $key, $ext, $tab ){
		
		if( $this->is_installed($type, $key, $ext)
			&& $this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
		
	}
	
	function show_delete_button( $type, $key, $ext, $tab ){
		
		if( !$this->is_active($type, $key, $ext)
			&& $this->is_installed($type, $key, $ext)
			&& !EXTEND_NETWORK
			&& !$this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
		
	}

	private function is_installed( $type, $key, $ext, $tab = '' ){

		if( $type == 'section' ){
			
			$status = ( isset($ext['status']) ) ? true : false;
			
			if ( isset( $ext['base_file'] ) )
				$path = $ext['base_file'];
			else
				$path = sprintf( '%s/%s/section.php', PL_EXTEND_DIR, $ext['slug'] );
		
			if( file_exists($path) )
				return true;
			else 
				return false;

		} elseif( $type == 'plugin'){

			if( isset($ext['status']['status']) && $ext['status']['status'] != '')
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
			&& !$this->is_purchased($type, $key, $ext) 
			&& !$this->is_installed($type, $key, $ext)
			&& $this->is_premium($type, $key, $ext)
		){
			return true;
		} else 
			return false;
	}

	function is_premium( $type, $key, $ext ){
		$ext = (array) $ext;
		if( isset($ext['price']) 
			&& $ext['price'] != 'free' 
			&& $ext['price'] >= 0 
		){
			return true;
		} else 
			return false;
	}

	private function is_purchased( $type, $key, $info ){

		if($type == 'section'){
			
			return (isset( $info['purchased'] )) ? true : false;
			
		} else {
			
			if( isset( $info['purchased'] ) )
				return true; 
			else
				return false;
		}
	}
/*
	private function update_available( $type, $key, $info ){
		
		if($type == 'plugin'){
			
			$version = $this->get_the_version( $type, $key, $info);
			
			return ( $version && $info['version'] > $version ) ? true : false;
			
		}
		
	}
*/	
	function show_activate_button( $type, $key, $ext, $tab ){
		
		if ( $type == 'integration' && $this->is_active( $type, $key, $ext ) == 'false' )
			return true;

		if( !$this->in_the_store( $type, $key, $ext, $tab )
			&& $this->is_installed($type, $key, $ext, $tab)
			&& !$this->is_active($type, $key, $ext)
		){
			return true;
		} else 
			return false;
	}
	
	function show_deactivate_button( $type, $key, $ext, $tab ){
		
		if ( $type == 'integration' ) {
			
			if( $this->is_active($type, $key, $ext) == 'true' )
				return true;
		}
		if( $this->is_active($type, $key, $ext)
			&& !$this->in_the_store( $type, $key, $ext, $tab )
		){
			return true;
		} else 
			return false;
	}
	
	private function is_active( $type, $key, $ext ){

		if ( $type == 'integration' ) {
			$active = ploption( $key );
			if ( is_array( $active ) )
				return $active['activated'];
		}
		
		if($type == 'plugin'){
			if( isset($ext['status']['status']) && $ext['status']['status'] == 'active')
				return true;
			else 
				return false;
				
		}elseif($type == 'section'){
			
			if(isset($ext['status']) && $ext['status'] == 'enabled')
				return true;
			else
				return false;
			
		}	elseif($type == 'theme'){

				if( $key  == basename( get_stylesheet_directory() ) )
					return true;
				else
					return false;
			}
		
	}

	private function in_the_store( $type, $key, $ext, $tab ){
		
		if ( $type == 'integration' )
			return true;
		
		if($tab == 'free' || $tab == 'premium' || $tab == 'featured')
			return true;
		else
			return false;
		
	}

	private function purchase_button( $purchased = false, $store = true ){
		return ( $store && !EXTEND_NETWORK && !$purchased && !$this->login_button($purchased) ) ? true : false;
	}
	
	private function install_button( $installed = false, $purchased = false, $version = 0 ){
		return ( $this->version_check( $version ) && !EXTEND_NETWORK && $purchased && ! $installed) ? true : false;
	}
	
	private function version_fail( $version ){
		return ( ! $this->version_check( $version ) ) ? true : false;
	}
	
	private function updates_configured( ){
		return ( pagelines_check_credentials() ) ? true : false;
	}
	
	private function do_redirect( ){
		return ( EXTEND_NETWORK ) ? true : false;
	}

	private function upgrade_available( $api_version, $installed_version ){

		if ( $api_version > $installed_version )
			return $api_version;
		else
			return false;
	}

// ===================================
// = Images, files, links and paths. =
// ===================================
	private function image_path( $type, $key, $ext, $tab ) {
		
		if( $type == 'integration' ) {
			if( $ext['screen'])
				return sprintf( 'http://www.pagelines.com/api/files/integrations/img/%s-thumb.png', $key );
			else
				return PL_ADMIN_IMAGES . '/thumb-default.png';
		}

		if ( $type == 'theme' ) {
			
			if ( ( $this->show_install_button($type, $key, $ext, $tab) 
					|| $this->show_purchase_button($type, $key, $ext, $tab) 
					|| $this->show_login_button($type, $key, $ext, $tab) 
				)
				&& isset( $ext['screen'])
				&& $ext['screen']
			){
				
				return sprintf( 'http://www.pagelines.com/api/files/themes/img/%s-thumb.png', $key );
					
			}elseif ( file_exists( sprintf('%s/%s/thumb.png', get_theme_root(), $key) ) )
				return sprintf('%s/%s/thumb.png', get_theme_root_uri(), $key);
			else
				return PL_ADMIN_IMAGES . '/thumb-default.png';
		}

	}
	
	private function get_the_path( $button, $type, $key, $ext, $tab ){

		if ( ( $button == 'deactivate' || $button == 'activate' ) && $type == 'section' )
			return $ext['type'];
		
		if ( ( $button == 'install' || $button == 'delete' ) && $type == 'section' ) {
			return $key;	
		}	
		
	}
	
	private function get_the_file( $button, $type, $key, $ext, $tab ){

		if ( $button == 'delete' || $button == 'upgrade' ) {
			if ( $type == 'section'
			 	&& isset( $ext['base_dir'] ) 
					) {
				return basename($ext['base_dir']);	
			}
		}
		
		
		if ( $type == 'section' ) {
			return $ext['class'];	
		} elseif($type == 'plugin'){
			
			if($button == 'activate'
				|| $button == 'deactivate'
				|| $button == 'delete'
			){
				return $ext['file'];
			} else
				return $key;
	
	
		}elseif($type == 'theme'){
			return $key;				
				
		} else
			return $key;
		
	}
		
	private function paypal_link( $type, $key, $ext, $tab ){
		return ( isset( $ext['productid'] ) ) ? sprintf('%s,%s|%s|%s', $ext['productid'], $ext['uid'], $ext['price'], $ext['name']) : '';		
	}

	private function purchase_text( $type, $key, $ext, $tab ){
		
		$ext = (array) $ext;
		
		$price = (isset($ext['price'])) ? sprintf(' <span class="prc">($%s)</span>', $ext['price']) : '';

		return sprintf('%s%s', __( 'Purchase', 'pagelines' ), $price); 
	}
	
	private function get_the_version($type, $key, $ext){


		if ( $type == 'section' ) 
			return isset( $ext['upgrade'] ) ? $ext['upgrade'] : $ext['version'];	

		if($type == 'plugin')
			return $ext['version'];

		if ( $type == 'theme' ) 
			return isset( $ext['upgrade'] ) ? $ext['upgrade'] : $ext['version'];	
	}

	/**
	 * Section install tab.
	 * 
	 */
 	function extension_sections_install( $tab = '' ) {
 
		if ( !$this->has_extend_plugin() )
			return $this->ui->get_extend_plugin( $this->has_extend_plugin('status'), $tab );
		
 		$sections = $this->get_latest_cached( 'sections' );

		if ( !is_object( $sections ) ) 
			return $sections;

		$list = array();
		
		foreach( $sections as $key => $ext ) {
			
			$ext = (array) $ext;
			
			if ( !isset( $ext['type']) )
				$ext['type'] = 'internal';
			
			if( !$this->show_in_tab( 'section', $key, $ext, $tab ) )
				continue; 
		
			$actions = $this->master_array( 'section', $key, $ext, $tab );
			
			$list[$key] = array(
				'name' 		=> $ext['name'], 
				'version'	=> $ext['version'], 
				'desc'		=> $ext['text'], 
				'auth_url'	=> $ext['author_url'], 
				'auth'		=> $ext['author'],
				'image'		=> ( isset( $ext['image'] ) ) ? $ext['image'] : '',
				'type'		=> 'sections',
				'key'		=> $key, 
				'ext_txt'	=> __( 'Installing', 'pagelines' ), 
				'actions'	=> $actions,
				'screen'	=> isset( $ext['screen'] ) ? $ext['screen'] : false,
				'slug'		=> isset( $ext['slug'] ) ? $ext['slug'] : $key
			);
			
		}
		
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'Available %1$s sections will appear here.', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}

	
	/*
	 * Installed sections tab.
	 */
 	function extension_sections( $tab = '' ) {

 		global $load_sections;

		if($tab == 'child' && !is_child_theme())
			return $this->ui->extension_banner( __( 'A PageLines child theme is not currently activated', 'pagelines' ) );

		// Get sections
 		$available = $load_sections->pagelines_register_sections( true, true );

 		$disabled = get_option( 'pagelines_sections_disabled', array() );

		$upgradable = $this->get_latest_cached( 'sections' );
		
		$list = array();
 		foreach( $available as $type ) {
	
			foreach( $type as $key => $ext)
				$type[$key]['status'] = ( isset( $disabled[ $ext['type'] ][ $ext['class'] ] ) ) ? 'disabled' : 'enabled';

			$type = pagelines_array_sort( $type, 'name' ); // Sort Alphabetically

 			foreach( $type as $key => $ext ) { // main loop


				if ( isset( $ext['base_dir'] ) ) {
					$upgrade = basename( $ext['base_dir'] );
					$ext['upgrade'] = ( isset( $upgradable->$upgrade->version ) ) ? $upgradable->$upgrade->version : '';
				}
				
				$ext['class_exists'] = ( isset( $available['child'][ $ext['class'] ] ) || isset( $available['custom'][ $ext['class'] ] ) ) ? true : false;
				
				if( !$this->show_in_tab( 'section', $key, $ext, $tab ) )
					continue;
		
				$actions = $this->master_array( 'section', $key, $ext, $tab  );
				
				$list[] = array(
					'name' 		=> $ext['name'], 
					'version'	=> !empty( $ext['version'] ) ? $ext['version'] : CORE_VERSION, 
					'desc'		=> $ext['description'],
					'auth_url'	=> $ext['authoruri'],
					'type'		=> 'sections',
					'object'	=> $ext['class'],
					'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
					'image'		=> ( isset( $ext['image'] ) ) ? $ext['image'] : '',
					'auth'		=> $ext['author'],
					'key'		=> $key,
					'status'	=> $ext['status'], 
					'actions'	=> $actions,
					'screen'	=> isset( $ext['screen'] ) ? $ext['screen'] : '',
					'screenshot'=> isset( $ext['screenshot'] ) ? $ext['screenshot'] : '',
					'slug'		=> isset( $ext['slug'] ) ? $ext['slug'] : $key,
				);
 			}
 		} 	
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'Installed %1$s sections will appear here.', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}

	/*
	 * Plugins tab.
	 */
	function extension_plugins( $tab = '' ) {

		$plugins = $this->load_plugins();
		
		$list = array();		
		foreach( $plugins as $key => $ext ) {
				
			$ext['loaded'] = ( isset( $ext['status']['status'] ) ) ? true : false;
			$ext['sections-plugin'] = (str_replace( '.php', '', PL_EXTEND_SECTIONS_PLUGIN ) === $ext['slug'] ) ? true : false;
			
			if( !$this->show_in_tab( 'plugin', $key, $ext, $tab ) )
				continue;
				
			if ( !isset( $ext['status'] ) )
				$ext['status'] = array( 'status' => '' );	

			$actions = $this->master_array( 'plugin', $key, $ext, $tab  );
				
			
			$list[$key] = array(
					'name' 		=> $ext['name'], 
					'version'	=> ( isset( $ext['status']['data'] ) ) ? $ext['status']['data']['Version'] : $ext['version'], 
					'desc'		=> $ext['text'],
					'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
					'auth_url'	=> $ext['author_url'], 
					'image'		=> ( isset( $ext['image'] ) ) ? $ext['image'] : '',
					'auth'		=> $ext['author'], 
					'key'		=> $key,
					'type'		=> 'plugins',
					'count'		=> $ext['count'],
					'actions'	=> $actions,
					'screen'	=> $ext['screen'],
					'extended'	=> $ext['extended'],
					'slug'		=> $ext['slug'],
			);	
				
		}
		
		$add_url = admin_url('admin.php?page=pagelines_extend#add_plugins');
	
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner( __( 'Installed plugins will appear here.', 'pagelines' ) );
		elseif(empty($list))
			return $this->ui->extension_banner( sprintf( __( 'Available %1$s plugins will appear here.', 'pagelines' ), $tab ) );
		else 
			return $this->ui->extension_list( $list );


	}
	
	function load_plugins(){
		
		$plugins = $this->get_latest_cached( 'plugins' );

		if ( !is_object($plugins) ) 
			return $plugins;

		$output = '';

		$plugins = json_decode(json_encode($plugins), true); // convert objects to arrays

		foreach( $plugins as $key => $plugin )
			$plugins[$key]['file'] = sprintf('/%1$s/%1$s.php', $key);

		$plugins = pagelines_array_sort( $plugins, 'name', false, true ); // sort by name

		// get status of each plugin
		foreach( $plugins as $key => $ext ) {
			$plugins[$key]['status'] = $this->plugin_check_status( WP_PLUGIN_DIR . $ext['file'] );
			$plugins[$key]['name'] = ( $plugins[$key]['status']['data']['Name'] ) ? $plugins[$key]['status']['data']['Name'] : $plugins[$key]['name'];
		}

		$plugins = pagelines_array_sort( $plugins, 'status', 'status' ); // sort by status

		// reset array keys ( sort functions reset keys to int )
		foreach( $plugins as $key => $ext ) {

			unset( $plugins[$key] );
			$key = str_replace( '.php', '', basename( $ext['file'] ) );
			$plugins[$key] = $ext;

		}
		
		return $plugins;
	}
	
	
	function parse_buttons($actions, $core_actions){
		
		$actions = wp_parse_args($actions, $core_actions);
		
		foreach($actions as $action => $button){
			if( isset($core_actions[$action]) ){
				$actions[$action] = wp_parse_args($button, $core_actions[$action]);
			}
		}
		
		return $actions;
		
	}	
	
	/**
	 * Themes tab.
	 * 
	 */
	function extension_themes( $tab = '' ) {

		$themes = $this->get_latest_cached( 'themes' );

		if ( !is_object($themes) ) 
			return $themes;

		$list = array();
		
		$themes = $this->extension_scan_themes( $themes );

		foreach( $themes as $key => $ext ) {
			
			$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key );
			
			if ( file_exists( $check_file ) )
				$exists = true;

			if( !$this->show_in_tab( 'theme', $key, $ext, $tab ) )
				continue;

				$actions = $this->master_array( 'theme', $key, $ext, $tab  );

				$list[$key] = array(
						'theme'		=> $ext,
						'name' 		=> $ext['name'], 
						'active'	=> $this->is_active('theme', $key, $ext),
						'version'	=> $this->get_the_version('theme', $key, $ext), 
						'desc'		=> $ext['text'],
						'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
						'auth_url'	=> $ext['author_url'], 
						'image'		=> $this->image_path( 'theme', $key, $ext, $tab ),
						'auth'		=> $ext['author'], 
						'key'		=> $key,
						'type'		=> 'themes',
						'count'		=> $ext['count'],
						'screen'	=> ( isset( $ext['screen'] ) ) ? $ext['screen'] : false,
						'actions'	=> $actions
				);		
		}

		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner( __( 'Installed PageLines themes will appear here.', 'pagelines' ) );
		elseif(empty($list))
			return $this->ui->extension_banner( sprintf( __( 'Available %1$s themes will appear here.', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list, 'graphic' );
	}

	/**
	 * Integrations tab.
	 * 
	 */
	function extension_integrations( $tab = '' ) {

		$integrations = $this->get_latest_cached( 'integrations' );

		if ( !is_object($integrations) ) 
			return $integrations;
		$integrations = json_decode(json_encode($integrations), true); // convert objects to arrays	

		$list = array();

		foreach( $integrations as $key => $ext ) {
		
				$actions = $this->master_array( 'integration', $key, $ext, $tab  );

				$list[$key] = array(
						'theme'		=> $ext,
						'name' 		=> $ext['name'], 
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $ext['version'], 
						'desc'		=> $ext['text'],
						'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
						'auth_url'	=> $ext['author_url'], 
						'image'		=> $this->image_path( 'integration', $key, $ext, $tab ),
						'auth'		=> $ext['author'], 
						'key'		=> $key,
						'type'		=> 'themes',
						'count'		=> $ext['count'],
						'screen'	=> ( isset( $ext['screen'] ) ) ? $ext['screen'] : false,
						'actions'	=> $actions
				);		
		}
		
		return $this->ui->extension_list( $list, 'download' );
		
		
	}


	/**
	 * Get current status for a plugin.
	 * 
	 */
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
	 * Remove our plugins from the maim WordPress updates.
	 * 
	 */
	function pagelines_plugins_remove( $r, $url ) {

		if ( 0 === strpos( $url, 'http://api.wordpress.org/plugins/update-check/' ) ) {

			$installed = get_option('active_plugins');
			$plugins = unserialize( $r['body']['plugins'] );

			foreach ( $installed as $plugin ) {
				$data = get_file_data( sprintf( '%s/%s', WP_PLUGIN_DIR, $plugin ), $default_headers = array( 'pagelines' => 'PageLines' ) );
				if ( !empty( $data['pagelines'] ) ) {

					unset( $plugins->plugins[$plugin] );
					unset( $plugins->active[array_search( $plugin, $plugins->active )] );				
				}
			}
			$r['body']['plugins'] = serialize( $plugins );	
		}
		return $r;		
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
	
	/**
	 * Scan for themes and combine api with installed.
	 * 
	 */	
	function extension_scan_themes( $themes ) {
		
		$themes = json_decode(json_encode($themes), true);
		
		$get_themes = get_themes();

		foreach( $get_themes as $theme => $theme_data ) {
			$up = null;
			
			if ( $theme_data['Template'] != 'pagelines' )
				continue;
				
			if ( 'pagelines' == $theme_data['Stylesheet'] )
				continue;
			
			// check for an update...	
			if ( isset( $themes[ $theme_data['Stylesheet'] ]['version'] ) && $themes[ $theme_data['Stylesheet'] ]['version'] > $theme_data['Version']) 			
				$up = $themes[ $theme_data['Stylesheet'] ]['version'];
			
			if ( in_array( $theme, $themes ) )
				continue;
			// If we got this far, theme is a pagelines child theme not handled by the API
			// So we need to inject it into our themes array.
			
			$new_theme = array();
			$new_theme['name'] =		$theme_data['Name'];
			$new_theme['author'] =		$theme_data['Author Name'];
			$new_theme['author_url'] =	$theme_data['Author URI'];
			$new_theme['upgrade'] =		( isset( $up ) ) ? $up : '';			
			$new_theme['version'] =		$theme_data['Version'];
			$new_theme['text'] =		$theme_data['Description'];
			$new_theme['tags'] =		$theme_data['Tags'];
			$new_theme['featured']	=	( isset( $themes[$theme_data['Stylesheet']]['featured'] ) ) ? $themes[$theme_data['Stylesheet']]['featured'] : null;
			$new_theme['price']		= 	( isset( $themes[$theme_data['Stylesheet']]['price'] ) ) ? $themes[$theme_data['Stylesheet']]['price'] : null;
			$new_theme['productid'] = 	null;
			$new_theme['count'] = 		null;
			$themes[$theme_data['Stylesheet']] = $new_theme;		
		}
		return $themes;
	}
	
} // [END]

