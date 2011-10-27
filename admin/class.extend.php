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
		
		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));	
		add_action( 'admin_init', array(&$this, 'extension_uploader' ) );
		
		add_action( 'admin_init', array(&$this, 'launchpad_returns' ) );
		add_action( 'admin_init', array(&$this, 'check_creds' ) );
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


	private function login_button( $purchased = false ){
		return ( !EXTEND_NETWORK && !$this->updates_configured() && !$purchased) ? true : false;
	}
	
	private function purchase_button( $purchased = false, $store = true ){
		return ( $store && !EXTEND_NETWORK && !$purchased && !$this->login_button($purchased) ) ? true : false;
	}
	
	private function install_button( $installed = false, $purchased = false, $version = 0 ){
		return ( $this->version_check( $version ) && !EXTEND_NETWORK && $purchased && ! $installed) ? true : false;
	}
	

	
	private function version_check( $version ){
		return ( version_compare( CORE_VERSION, $version ) >= 0 ) ? true : false;
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
	
	private function paypal_link( $product_id, $uid, $price, $name ){
		
		return ( isset( $product_id ) ) ? sprintf('%s,%s|%s|%s', $product_id, $uid, $price, $name) : '';
		
	}
	
	private function purchase_text( $price ){
		return sprintf('%s <span class="prc">($%s)</span>', __( 'Purchase', 'pagelines' ), $price); 
	}
	
	private function show_in_tab( $type, $tab, $args ){
		
		$d = array(
			'price'		=> '', 
			'type'		=> '', 
			'override'	=> false, 
			'status'	=> false,
			'featured'	=> false,
			'exists'	=> false
		);
		
		$a = wp_parse_args($args, $d);
		
		if($type == 'sections_manage'){
			
			if ( $tab === 'user' && ( $a['type'] === 'custom' || $a['type'] === 'parent' ) )
				return false;
			elseif ( $tab === 'internal' && ( $a['type'] === 'custom' || $a['type'] === 'child' ) )
				return false;						
			elseif ( $tab === 'child' && ( $a['type'] === 'child' || $a['type'] === 'parent' ) )
				return false;
			elseif ( $a['type'] == 'parent' && $a['override'] )
				return false;
			else
				return true;
			
		} elseif($type == 'sections_install') {
			
			if( $a['price'] != 'free' && $tab === 'free' )
				return false;

			elseif( $tab == 'premium' && $a['price'] == 'free' )
				return false;

			else 
				return true;
			
		} elseif($type == 'plugins'){
			
			if ( $tab === 'installed' && !$a['status'] )
				return false;
				
			elseif ( $tab === 'installed' && $a['override'])
				return false;

			elseif ( ( $tab === 'premium' || $tab === 'featured' ) && $a['price'] === 'free' )
				return false;

			elseif ( $tab === 'free' && $a['price'] != 'free' )
				return false;
			
			else 
				return true;
				
		} elseif($type == 'themes'){
			
			if ( $tab === 'featured' && $a['featured'] == 'false' ) 
				return false;

			elseif ( ( $tab == 'premium' || $tab === 'featured' ) && $a['featured'] == 'true' )
				return true;
			
			elseif ( ( $tab == 'premium' || $tab == 'featured' ) && $a['exists'] ) 
				return false;

//			elseif (  $tab = 'free' && $a['price'] != 'free' ) 
//				return false;
				
			elseif ( $tab == 'installed' && !$a['exists'] )
				return false;
				
			else
				return true;
			
		}
		
		
	}
	
	private function is_installed( $type, $key, $info ){

		if($type == 'section'){

			$path = sprintf('%1$s/%2$s/section.php', PL_EXTEND_DIR, $key );

			if(file_exists($path))
				return true;
			else 
				return false;

		} elseif( $type == 'plugin'){

			if( isset($info['status']['status']) && $info['status']['status'] != '')
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
	
	private function is_purchased( $type, $key, $info ){

		if($type == 'section'){
			
			return (isset( $ext->purchased )) ? true : false;
			
		} else {
			
			if( isset( $info['purchased'] ) )
				return true; 
			else
				return false;
			
		}
	
	}

	private function upgrade_available( $installed_version, $api_version){
		
		if ( $api_version > $installed_version )
			return $api_version;
		else
			return false;
	}
	
	private function button_logic( $type, $key, $ext, $tab ) {
		
		$logic = array();
		// button logic		
		
		$logic['is_installed'] = $this->is_installed($type, $key, $ext);

		$logic['is_purchased'] = $this->is_purchased($type, $key, $ext);
		
		$logic['version'] = $this->get_the_version($type, $key, $ext);
		
		$logic['upgrade_available'] = $this->upgrade_available( $this->get_the_version($type, $key, $ext), $ext['version']);
		
		$logic['show_login_button'] = ( !$this->updates_configured() && !$logic['is_purchased'] ) ? true : false;
		
		$logic['is_active'] = $this->is_active($type, $key, $ext);
		
		$logic['show_installed_button'] =  ( $this->in_the_store( $tab ) && $logic['is_installed'] ) ? true : false;
		
		$logic['show_purchase_button'] = ( !EXTEND_NETWORK && !$logic['is_purchased'] && !$logic['show_login_button'] && $this->in_the_store( $tab ) && !$logic['is_installed'] ) ? true : false;

		$logic['show_install_button'] = $this->show_install_button( $type, $key, $ext, $tab);

		$logic['show_deactivate_button'] = ($logic['is_active'] && !$this->in_the_store( $tab ) ) ? true : false;
		
		$logic['show_activate_button'] = (!$this->in_the_store( $tab ) && $logic['is_installed'] && !$logic['is_active']) ? true : false;
	
		$logic['delete'] = ( $logic['show_activate_button'] && ! EXTEND_NETWORK ) ? true : false;
		
		$logic['redirect'] = ( EXTEND_NETWORK && $logic['show_install_button'] ) ? true : false;			
			
		$logic['installed'] = (!$logic['show_install_button']) ? true : false;	
		
		$logic['product'] = ( isset( $ext['productid'] ) ) ? $ext['productid'] : 0;
		
		return $logic;
	}
	
	private function image_path( $type, $logic, $ext, $key ) {
		
		if ( $type == 'theme' ) {
			if ( ( $logic['show_install_button'] || $logic['show_purchase_button'] || $logic['show_login_button'] || $logic['redirect'] ) && $ext['screen'])
				return sprintf( 'http://www.pagelines.com/api/files/themes/img/%s-thumb.png', $key );
			elseif ( file_exists( sprintf('%s/%s/thumb.png', get_theme_root(), $key) ) )
				return sprintf('%s/%s/thumb.png', get_theme_root_uri(), $key);
			else
				return PL_ADMIN_IMAGES . '/thumb-default.png';
		}

	}

	private function master_array( $args ){
		
		$d = array(
			'store'			=> false,
			'purchased'		=> false, 
			'installed'		=> false, 
			'version'		=> 0, 
			'product_id'	=> 0, 
			'unit_id'		=> 1, 
			'price'			=> '1.00',
			'name'			=> '',
			'type'			=> 'sections',
			'key'			=> '', 
			'path'			=> '', 
			'class'			=> '', 
			'file'			=> '', 
			'enabled'		=> false, 
			'upgrade'		=> false, 
			'delete'		=> false
		);
		
		$a = wp_parse_args($args, $d);
		
		$actions = array(
			'install'	=> array(
				'mode'		=> 'install',
				'condition'	=> $this->install_button($a['installed'], $a['purchased'], $a['version'] ),
				'case'		=> 'section_install',
				'type'		=> $a['type'],
				'file'		=> $a['key'],
				'path'		=> $a['path'],
				'text'		=> __( 'Install', 'pagelines' ),
				'dtext'		=> __( 'Installing', 'pagelines' )
				),
			'redirect'	=> array(
				'mode'		=> 'redirect',
				'condition'	=> $this->do_redirect(),
				'case'		=> 'redirect',
				'type'		=> $a['type'],
				'file'		=> $a['key'],
				'path'		=> $a['path'],
				'text'		=> __( 'Install &darr;', 'pagelines' ),
			),
			'login'	=> array(
				'mode'		=> 'login',
				'condition'	=> $this->login_button( $a['purchased'] ),
				'case'		=> 'login',
				'type'		=> $a['type'],
				'file'		=> $a['key'],
				'text'		=> __( 'Login &rarr;', 'pagelines' ),
				'dtext'		=> __( 'Redirecting', 'pagelines' ),
			),
			'purchase'	=> array(
				'mode'		=> 'purchase',
				'condition'	=> $this->purchase_button( $a['purchased'], $a['store'] ),
				'case'		=> 'purchase',
				'type'		=> $a['type'],
				'file'		=> $this->paypal_link( $a['product_id'], $a['unit_id'], $a['price'], $a['name'] ), 
				'text'		=> $this->purchase_text( $a['price'] ),
				'dtext'		=> __( 'Redirecting', 'pagelines' ),
			),
			'activate'	=> array(
				'mode'		=> 'activate',
				'condition'	=> (!$a['enabled'] &&  !$a['store'] ) ? true : false,
				'case'		=> 'section_activate',
				'type'		=> $a['type'],
				'path'		=> $a['path'],
				'file'		=> $a['class'],
				'text'		=> __( 'Activate', 'pagelines' ),
				'dtext'		=> __( 'Activating', 'pagelines' ),
			) ,
			'deactivate'=> array(
				'mode'		=> 'deactivate',
				'condition'	=> $a['enabled'],
				'case'		=> 'section_deactivate',
				'type'		=> $a['type'],
				'file'		=> $a['class'],
				'text'		=> __( 'Deactivate', 'pagelines' ),
				'dtext'		=> __( 'Deactivating', 'pagelines' ),
			),
			'upgrade'	=> array(
				'mode'		=> 'upgrade',
				'condition'	=> $a['upgrade'],
				'case'		=> 'section_upgrade',
				'type'		=> 'sections',
				'file'		=> $a['file'],
				'text'		=> sprintf(__( 'Upgrade to %s', 'pagelines' ), $a['upgrade'] ),
				'dtext'		=> sprintf( __( 'Upgrading to version %1$s', 'pagelines' ), $a['upgrade'] ),
			),
			'delete'	=> array(
				'mode'		=> 'delete',
				'condition'	=> $a['delete'],
				'case'		=> 'section_delete',
				'type'		=> 'sections',
				'file'		=> $a['file'],
				'text'		=> __( 'Delete', 'pagelines' ),
				'dtext'		=> __( 'Deleting', 'pagelines' ),
				'confirm'	=> true
			),
			'installed'	=>	array(
				'mode'		=> 'installed',
				'condition'	=> $a['installed'],
				'text'		=> __( 'Installed', 'pagelines' ),
				),
			'version_fail'	=>	array(
				'mode'		=> 'installed',
				'condition'	=> $this->version_fail( $a['version'] ),
				'text'		=> sprintf( __( '%s is required', 'pagelines' ), $a['version'] ),
				)		
		);
		
		return $actions;
		
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

			$key = str_replace( '.', '', $key );

			if ( !isset( $ext->type) )
				$ext->type = 'free';
			
			if( !$this->show_in_tab('sections_install', $tab, array('price' => $ext->price ) ) )
				continue;
			
			
			$purchased = $this->is_purchased( 'section', $key, $ext );

		
			$installed = $this->is_installed( 'section', $key, $ext );
	
	
			$args = array(
				'extend'		=> 'sections',
				'type'			=> 'sections',
				'purchased'		=> $purchased, 
				'installed'		=> $installed, 
				'version'		=> $ext->plversion, 
				'product_id'	=> $ext->productid,
				'unit_id'		=> $ext->uid, 
				'price'			=> $ext->price,
				'name'			=> $ext->name,
				'key'			=> $key, 
				'path'			=> $ext->class, 
				'store'			=> true
			);
			
			$actions = $this->master_array( $args );
			
			
			$list[$key] = array(
				'name' 		=> $ext->name, 
				'version'	=> $ext->version, 
				'desc'		=> $ext->text, 
				'auth_url'	=> $ext->author_url, 
				'auth'		=> $ext->author,
				'image'		=> ( isset( $ext->image ) ) ? $ext->image : '',
				'type'		=> 'sections',
				'key'		=> $key, 
				'ext_txt'	=> __( 'Installing', 'pagelines' ), 
				'actions'	=> $actions,
				'screen'	=> isset( $ext->screen ) ? $ext->screen : false,
				'slug'		=> isset( $ext->slug ) ? $ext->slug : $key
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

 		/*
 		 * Clear section cache and re-generate
 		 */
 		global $load_sections;

		if($tab == 'child' && !is_child_theme())
			return $this->ui->extension_banner( __( 'A PageLines child theme is not currently activated', 'pagelines' ) );

		// Get sections
 		$available = $load_sections->pagelines_register_sections( true, true );

 		$disabled = get_option( 'pagelines_sections_disabled', array() );

		$upgradable = $this->get_latest_cached( 'sections' );
		
		$list = array();
 		foreach( $available as $type ) {
	
 			if ( !$type )
 				continue;

			foreach( $type as $key => $ext)
				$type[$key]['status'] = ( isset( $disabled[ $ext['type'] ][ $ext['class'] ] ) ) ? 'disabled' : 'enabled';

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $ext ) { // main loop
		
				$show = array(
					'type'		=> $ext['type'], 
					'override' 	=> ( isset( $available['child'][ $ext['class'] ] ) || isset( $available['custom'][ $ext['class'] ] ) ) ? true : false
				);
				
				if( !$this->show_in_tab( 'sections_manage', $tab, $show ) )
					continue;
				
				$enabled = ( $ext['status'] == 'enabled' ) ? true : false;

				$file = basename( $ext['base_dir'] );
				
				$delete = ( !EXTEND_NETWORK && !$enabled && ( $tab !== 'child' && $tab !== 'internal' ) ) ? true : false;
				
				
				$args = array(
					'extend'		=> 'sections',
					'type'			=> 'sections',
					'path'			=> $ext['base_file'],
					'file'			=> $ext['class'],
					'delete'		=> $delete,
					'enabled'		=> $enabled, 
					'upgrade'		=> $this->upgrade_available( $upgradable, $file, $ext), 
					'store'			=> false
				);

				$actions = $this->master_array( $args );		
				
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

	private function get_the_version($type, $key, $info){
		
		
		if($type == 'plugin'){
			
			 return (isset($info['status']['version'])) ? $info['status']['version'] : false;
			
		}
		
		
	}
	
	private function update_available( $type, $key, $info ){
		
		if($type == 'plugin'){
			
			$version = $this->get_the_version( $type, $key, $info);
			
			return ( $version && $info['version'] > $version ) ? true : false;
			
		}
		
	}
	
	private function is_active( $type, $key, $info ){
		
		if($type == 'plugin'){
			if( isset($info['status']['status']) && $info['status']['status'] == 'active')
				return true;
			else 
				return false;
				
		}
		
	}

	private function in_the_store( $tab ){
		
		if($tab == 'free' || $tab == 'premium' || $tab == 'featured')
			return true;
		else
			return false;
		
	}
	
	private function show_install_button( $type, $key, $info, $tab){
		
		if(!$this->is_installed($type, $key, $info) && $this->is_purchased($type, $key, $info) && $this->in_the_store($tab) && !EXTEND_NETWORK)
			return true;
		else
			return false;
	}

	/*
	 * Plugins tab.
	 */
	function extension_plugins( $tab = '' ) {

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
		
		$list = array();		
		foreach( $plugins as $key => $ext ) {
				
			$show = array(
				'price'		=> $ext['price'], 
				'override'	=> str_replace( '.php', '', PL_EXTEND_SECTIONS_PLUGIN ) === $ext['slug'], 
				'status'	=> ( isset( $ext['status']['status'] ) ) ? true : false
			);
				
			if( !$this->show_in_tab( 'plugins', $tab, $show ) )
				continue;
				
			if ( !isset( $ext['status'] ) )
				$ext['status'] = array( 'status' => '' );	


			// button logic		
			$logic = $this->button_logic( 'plugin', $key, $ext, $tab );
			

			// This whole block sux! 	
/*			
			$is_installed = $this->is_installed('plugin', $key, $ext);

			$is_purchased = $this->is_purchased('plugin', $key, $ext);
			
			$version = $this->get_the_version('plugin', $key, $ext);
			
			$upgrade_available = $this->upgrade_available('plugin', $key, $ext);
			
			$show_login_button = ( !$this->updates_configured() && !$is_purchased) ? true : false;
			
			$is_active = $this->is_active('plugin', $key, $ext);
			
			$show_installed_button =  ( $this->in_the_store( $tab ) && $is_installed ) ? true : false;
			
			$show_purchase_button = ( !EXTEND_NETWORK && !$is_purchased && !$show_login_button && $this->in_the_store( $tab ) && !$is_installed ) ? true : false;

			$show_install_button = $this->show_install_button( 'plugin', $key, $ext, $tab);

			$show_deactivate_button = ($is_active && !$this->in_the_store( $tab ) ) ? true : false;
			
			$show_activate_button = (!$this->in_the_store( $tab ) && $is_installed && !$is_active) ? true : false;
		
			$delete = ( $show_activate_button && ! EXTEND_NETWORK ) ? true : false;
			
			$redirect = ( EXTEND_NETWORK && $show_install_button ) ? true : false;			
				
			$installed = (!$show_install_button) ? true : false;
			
*/

				
			$args = array(
				'extend'		=> 'plugins',
				'type'			=> 'plugins',
				'installed'		=> $logic['installed'],
				'purchased'		=> $logic['is_purchased'],
				'version'		=> $logic['version'],
				'path'			=> $ext['file'],
				'file'			=> $key,
				'delete'		=> $logic['delete'],
				'upgrade'		=> $logic['upgrade_available'], 
				'price'			=> $ext['price'], 
				'product_id'	=> $ext['productid'], 
				'unit_id'		=> $ext['uid'], 
				'name'			=> $ext['name']
			);

			$core_actions = $this->master_array( $args );
				
				
			$actions = array(
				'install'	=> array(
					'condition'	=> $logic['show_install_button'],
					'case'		=> 'plugin_install',
					'file'		=> $key,
				),
				'activate'	=> array(
					'condition'	=> $logic['show_activate_button'],
					'case'		=> 'plugin_activate',
					'file'		=> $ext['file'],
				),
				'upgrade'	=> array(
					'condition'	=> $logic['upgrade_available'],
					'case'		=> 'plugin_upgrade',
					'path'		=> $key,
				),
				'deactivate'	=> array(
					'condition'	=> $logic['show_deactivate_button'],
					'case'		=> 'plugin_deactivate',
					'file'		=> $ext['file'],
				),
				'delete'	=> array(
					'condition'	=> $logic['delete'],
					'case'		=> 'plugin_delete',
					'file'		=> $ext['file'],
				),
				'redirect'	=> array(
					'type'		=> __( 'plugins', 'pagelines' ),
					'file'		=> $key,
				),
				'purchase'	=> array(
					'condition'	=> $logic['show_purchase_button'],
				),
				'installed'	=>	array(
					'condition'	=> $logic['show_installed_button'],
				)
			);			
			
			$actions = $this->parse_buttons($actions, $core_actions);
			
			
			
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
	
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner( __( 'Installed plugins will appear here.', 'pagelines' ) );
		elseif(empty($list))
			return $this->ui->extension_banner( sprintf( __( 'Available %1$s plugins will appear here.', 'pagelines' ), $tab ) );
		else 
			return $this->ui->extension_list( $list );
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
			
		$output = '';
		$status = false;
		$list = array();
		
		$themes = $this->extension_scan_themes( $themes );

		foreach( $themes as $key => $ext ) {

/*
						
				// reset the vars first numbnuts!	
				$status = null;
				$exists = null;
				$is_active = null;
				$activate = null;
				$deactivate = null;
				$upgrade_available = null;
				$purchase = null;
				$delete = null;
				$login = null;
				$data = null;
				$ext['featured'] = ( isset( $ext['featured'] ) ) ? $ext['featured'] : false;


				$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key );
				
				if ( file_exists( $check_file ) )
					$exists = true;
					
					
				$show = array(
					'featured'	=> $ext['featured'], 
					'exists' 	=> $exists
				);
				
				if( !$this->show_in_tab( 'themes', $tab, $show ) )
					continue;
					
					
				
					
				if ( isset( $exists ) && $data = get_theme_data( $check_file ) ) 
					$status = 'installed';
					
				$is_active = ( $key  == basename( get_stylesheet_directory() ))	? true : false;
					
				$updates_configured = $this->updates_configured();
					
				$activate = ($status == 'installed' && !$is_active) ? true : false;
				$deactivate = ($status == 'installed' && $is_active) ? true : false;
				
				$version = (isset($data)) ? $data['Version'] : false;
				
				$upgrade_available = ($version && $ext['version'] > $version) ? true : false;
			
				$purchase = ( !isset( $ext['purchased'] ) && !$status && $updates_configured ) ? true : false;
				$product = ( isset( $ext['productid'] ) ) ? $ext['productid'] : 0;
				$install = ( !$status && !$purchase && $updates_configured ) ? true : false;
				$delete = ( $activate && !EXTEND_NETWORK ) ? true : false;
				
				$login = ( !$updates_configured && !$status );
				
				$redirect = ( $login && EXTEND_NETWORK ) ? true : false;
				
*/				

				$show = array(
					'featured'	=> ( isset( $ext['featured'] ) ) ? $ext['featured'] : false, 
					'exists' 	=> $this->is_installed('theme', $key, $ext)
				);

				if( !$this->show_in_tab( 'themes', $tab, $show ) )
					continue;
					
				// button logic		
				$logic = $this->button_logic( 'theme', $key, $ext, $tab );

				$image = $this->image_path( 'theme', $logic, $ext, $key);
				

				$args = array(
					'extend'		=> 'themes',
					'type'			=> 'themes',
					'version'		=> $logic['version'],
					'delete'		=> $logic['delete'],
					'upgrade'		=> $logic['upgrade_available'], 
					'product_id'	=> $ext['productid'], 
					'unit_id'		=> $ext['productid'], 
					'price'			=> isset( $ext['price'] ) ? $ext['price'] : 'free', 
					'name'			=> $ext['name'], 
					'file'			=> $key
				);

				$core_actions = $this->master_array( $args );
				
				
				$actions = array(
					'install'	=> array(
						'condition'	=> $logic['show_install_button'],
						'case'		=> 'theme_install',
						'file'		=> $key,
						'product'	=> $logic['product'],
					),
					'activate'	=> array(
						'condition'	=> $logic['show_activate_button'],
						'case'		=> 'theme_activate',
						'file'		=> $key,
					),
					'deactivate'	=> array(
						'condition'	=> $logic['show_deactivate_button'],
						'case'		=> 'theme_deactivate',
						'file'		=> $key,
					),
					'upgrade'	=> array(
						'condition'	=> $logic['upgrade_available'],
						'case'		=> 'theme_upgrade',
						'file'		=> $key,
					),
					'purchase'	=> array(
						'condition'	=> $logic['show_purchase_button'],
					),
					'delete'	=> array(
						'condition'	=> $logic['delete'],
						'case'		=> 'theme_delete',
						'file'		=> $key,
					),
					'login'	=> array(
						'condition'	=> $logic['show_login_button'],
					),
					'redirect'	=> array(
						'condition'	=> $logic['redirect'],
						'type'		=> __( 'themes', 'pagelines' ),
						'file'		=> $key,
					)
				);
				
				$actions = $this->parse_buttons($actions, $core_actions);

				$list[$key] = array(
						'theme'		=> $ext,
						'name' 		=> $ext['name'], 
						'active'	=> $logic['is_active'],
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $ext['version'], 
						'desc'		=> $ext['text'],
						'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
						'auth_url'	=> $ext['author_url'], 
						'image'		=> $image,
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
		$output = '';
		$status = false;
		$list = array();

		foreach( $integrations as $key => $ext ) {
						
				// reset the vars first numbnuts!	

				$purchase = null;
				$delete = null;
				$login = null;
				$product = null;
				$purchased = null;
				$redirect = null;
				$download = null;
				$active = null;
				
				$active = ploption( $key );		
				$active = ( is_array( $active ) ) ? $active : false;
				
				$updates_configured = ( pagelines_check_credentials() ) ? true : false;	
				$purchase = ( !isset( $ext['purchased'] ) ) ? true : false;
				$product = ( isset( $ext['productid'] ) ) ? $ext['productid'] : 0;
				
				$login = ( !$updates_configured ) ? true : false;
				
				$activate = ( !$active || ( $active  && $active['activated'] == 'false' ) ) ? true : false;
				
				$deactivate = ( $active && $active['activated'] == 'true' ) ? true : false;

				$purchased = ( !$purchase && !$login) ? true : false;
				$redirect = ( $login && EXTEND_NETWORK ) ? true : false;
				
				$download = ( $purchased && !$login ) ? true : false;
				
				
				if( $ext['screen'])
					$image = sprintf( 'http://www.pagelines.com/api/files/integrations/img/%s-thumb.png', $key );
				else
					$image = PL_ADMIN_IMAGES . '/thumb-default.png';
				
				
				$args = array(
					'extend'		=> 'integrations',
					'type'			=> 'integrations',
				);

				$core_actions = $this->master_array( $args );

			
				
				$actions = array(

					'purchase'	=> array(
						'condition'	=> $purchase,
						'file'		=> ( isset( $ext['productid'] ) ) ? $ext['productid'] . ',' . $ext['uid'] . '|' . $ext['price'] . '|' . $ext['name'] : '',
						'text'		=> ( isset( $ext['price'] ) ) ? sprintf('%s <span class="prc">($%s)</span>', __( 'Purchase', 'pagelines' ), $ext['price']) : '',
						'dtext'		=> __( 'Redirecting', 'pagelines' ),
					),

					'login'	=> array(
						'condition'	=> ( !EXTEND_NETWORK ) ? $login : false,
						'file'		=> $key,
					),

					'download'	=> array(
						'mode'		=> 'redirect',
						'condition'	=> $download,
						'case'		=> 'integration_download',
						'type'		=> __( 'integrations', 'pagelines' ),
						'file'		=> $key,
						'text'		=> __( 'Download <strong>&darr;</strong>', 'pagelines' ),
						'dtext'		=> __( 'Downloading', 'pagelines' )
					),
					'redirect'	=> array(
						'condition'	=> $redirect,
						'type'		=> __( 'themes', 'pagelines' ),
						'file'		=> $key,
						'dtext'		=> ''
					),
					'activate'	=> array(
						'condition'	=> $activate,
						'case'		=> 'integration_activate',
						'file'		=> $key,
						'text'		=> __( 'Activate Options', 'pagelines' ),
					),
					'deactivate'	=> array(
						'condition'	=> $deactivate,
						'case'		=> 'integration_deactivate',
						'file'		=> $key,
						'text'		=> __( 'Deactivate Options', 'pagelines' ),
					)
	
				);

				$actions = $this->parse_buttons($actions, $core_actions);

				$list[$key] = array(
						'theme'		=> $ext,
						'name' 		=> $ext['name'], 
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $ext['version'], 
						'desc'		=> $ext['text'],
						'tags'		=> ( isset( $ext['tags'] ) ) ? $ext['tags'] : '',
						'auth_url'	=> $ext['author_url'], 
						'image'		=> ( isset( $ext['image'] ) ) ? $ext['image'] : $image,
						'auth'		=> $ext['author'], 
						'key'		=> $key,
						'type'		=> 'themes',
						'count'		=> $ext['count'],
						'screen'	=> ( isset( $ext['screen'] ) ) ? $ext['screen'] : false,
						'actions'	=> $actions
				);		
		}
		
		return $this->ui->extension_list( $list, 'graphic' );
		
		
	}




	function sandbox( $file, $type ) {

		register_shutdown_function( array(&$this, 'error_handler'), $type );
		@include_once( $file );
	}

	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback( $uploader = false, $checked = null) {

		// 1. Libraries
			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			include( PL_ADMIN . '/library.extension.php' );
	
		// 2. Variable Setup
			$mode =  $_POST['extend_mode'];
			$type =  $_POST['extend_type'];
			$file =  $_POST['extend_file'];
			$path =  $_POST['extend_path'];
			$product = $_POST['extend_product'];
			
		// 3. Do our thing...

		switch ( $mode ) {
			
			
			
			case 'integration_download':
				$url = $this->make_url( $type, $file );
				echo __( 'Downloaded', 'pagelines' );
				$this->int_download( $url );
			
			break;
			
			case 'integration_activate':
			
				$a = ploption( $file );
				$int = array(
				'version'	=> ( isset( $a['version'] ) ) ? $a['version'] : null,
				'activated'	=> 'true'
				);
				plupop( $file, $int );
				echo __( 'Activated', 'pagelines' );
			 	$this->page_reload( 'pagelines_extend' );			
			break;
			
			case 'integration_deactivate':

			$a = ploption( $file );
			$int = array(
			'version'	=> ( isset( $a['version'] ) ) ? $a['version'] : null,
			'activated'	=> 'false'
			);
			plupop( $file, $int );
			echo __( 'Deactivated', 'pagelines' );
			$this->page_reload( 'pagelines_extend' );			
			
			break;			
		
			case 'plugin_install': // TODO check status first!

				if ( !$checked )
					$this->check_creds( 'extend', WP_PLUGIN_DIR );		
				global $wp_filesystem;
				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$destination = ( ! $uploader ) ? $this->make_url( $type, $file ) : $file;						
				@$upgrader->install( $destination );

				if ( isset( $wp_filesystem )  && is_object( $wp_filesystem ) && $wp_filesystem->method == 'direct' )
					_e( 'Success', 'pagelines' );
				
				$this->sandbox( WP_PLUGIN_DIR . $path, 'plugin');
				activate_plugin( $path );			
				$text = '&extend_text=plugin_install#installed';
				$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ) ? 0 : 700; 
				$this->page_reload( 'pagelines_extend' . $text, null, $time);
			break;
			
			case 'plugin_upgrade':

				if ( !$checked )
					$this->check_creds( 'extend' );		
				global $wp_filesystem;
				
				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
		
				$active = is_plugin_active( ltrim( $path, '/' ) );
				deactivate_plugins( array( $path ) );
				
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
					$wp_filesystem->delete( trailingslashit( WP_PLUGIN_DIR ) . $file, true, false  );
				else
					extend_delete_directory( trailingslashit( WP_PLUGIN_DIR ) . $file );
				@$upgrader->install( $this->make_url( $type, $file ) );
				$this->sandbox( WP_PLUGIN_DIR . $path, 'plugin');
				if ( $active )
					activate_plugin( ltrim( $path, '/' ) );
				// Output

				$text = '&extend_text=plugin_upgrade';
				$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) ? 0 : 700; 
				$this->page_reload( 'pagelines_extend' . $text, null, $time);		
			break;
			
			case 'plugin_delete':

				if ( !$checked )
					$this->check_creds( 'extend', WP_PLUGIN_DIR );		
				global $wp_filesystem;
				delete_plugins( array( ltrim( $file, '/' ) ) );
				$text = '&extend_text=plugin_delete';
				_e( 'Success', 'pagelines' );
				$time = ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) ? 0 : 700; 
				$this->page_reload( 'pagelines_extend' . $text, null, $time);
			break;
			case 'plugin_activate':

				$this->sandbox( WP_PLUGIN_DIR . $file, 'plugin');
			 	activate_plugin( $file );
			 	_e( 'Activation complete!', 'pagelines' );
			 	$this->page_reload( 'pagelines_extend' );
			break;
					
			case 'plugin_deactivate':

				deactivate_plugins( array( $file ) );
				// Output
		 		_e( 'Deactivation complete!', 'pagelines' );
		 		$this->page_reload( 'pagelines_extend' );			
			break;
			
			case 'section_activate':

				$this->sandbox( $path, 'section');
				$available = get_option( 'pagelines_sections_disabled' );
				unset( $available[$type][$file] );
				update_option( 'pagelines_sections_disabled', $available );
				// Output
				_e( 'Section Activated!', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'section_deactivate':

				$disabled = get_option( 'pagelines_sections_disabled', array( 'child' => array(), 'parent' => array()) );
				$disabled[$type][$file] = true; 
				update_option( 'pagelines_sections_disabled', $disabled );
				// Output
				_e( 'Section Deactivated.', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'section_install':

				if ( !$checked )
					$this->check_creds( 'extend', WP_PLUGIN_DIR );		
				global $wp_filesystem;
				
				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$time = 0;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					@$upgrader->install( $this->make_url( 'sections', $file ) );		
					$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $file, trailingslashit( PL_EXTEND_DIR ) . $file );					
				} else {
							$options = array( 'package' => ( ! $uploader) ? $this->make_url( 'sections', $file ) : $file, 
							'destination'		=> ( ! $uploader) ? trailingslashit( PL_EXTEND_DIR ) . $file : trailingslashit( PL_EXTEND_DIR ) . $path, 
							'clear_destination' => false,
							'clear_working'		=> false,
							'is_multi'			=> false,
							'hook_extra'		=> array() 
					);
					@$upgrader->run($options);
					if ( ! $uploader ) {
						_e( 'Section Installed', 'pagelines' );
						$time = 700;
					}
				}
				$text = '&extend_text=section_install#added';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);
			break;
			
			case 'section_upgrade':
			
				if ( !$checked )
					$this->check_creds( 'extend', PL_EXTEND_DIR );		
				global $wp_filesystem;

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
					$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false  );
				else
					extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );				

				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ) {
					@$upgrader->install( $this->make_url( 'sections', $file ) );			
					$wp_filesystem->move( trailingslashit( WP_PLUGIN_DIR ) . $file, trailingslashit( PL_EXTEND_DIR ) . $file );
					$time = 0;				
				} else {
							$options = array( 'package' => ( ! $uploader) ? $this->make_url( 'sections', $file ) : $file, 
							'destination'		=> ( ! $uploader) ? trailingslashit( PL_EXTEND_DIR ) . $file : trailingslashit( PL_EXTEND_DIR ) . $path, 
							'clear_destination' => false,
							'clear_working'		=> false,
							'is_multi'			=> false,
							'hook_extra'		=> array() 
					);
					@$upgrader->run($options);
					$time = 700;
					_e( 'Success', 'pagelines');		
				}
				// Output
				$text = '&extend_text=section_upgrade';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);	
			break;
			
			case 'section_delete':
				if ( !$checked ) {
					$this->check_creds( 'extend', PL_EXTEND_DIR );		
				}
				global $wp_filesystem;

				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ):
					$wp_filesystem->delete( trailingslashit( PL_EXTEND_DIR ) . $file, true, false  );
					$time = 0;
				else:
					extend_delete_directory( trailingslashit( PL_EXTEND_DIR ) . $file );
					$time = 700;
					_e( 'Success', 'pagelines' );
					endif;
				
				$text = '&extend_text=section_delete';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);
	
			break;
					
			case 'theme_upgrade':

				if ( !$checked )
					$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
				global $wp_filesystem;

				$active = ( basename( get_stylesheet_directory()  ) === $file ) ? true : false;
	
				if ( $active )
					switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
			
				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Theme_Upgrader($skin);

				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) ):
					$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false  );
					$time = 0;
				else:
					extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );
					$time = 700;
					_e( 'Success', 'pagelines' );
				endif;
				@$upgrader->install( $this->make_url( $type, $file ) );
				
				if ( $active )
					switch_theme( basename( get_template_directory() ), $file );
				// Output
				$text = '&extend_text=theme_upgrade#installed';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);	
			break;			
			
			case 'theme_install':

				if ( !$checked ) {
					$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );
				}			
				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Theme_Upgrader($skin);
				global $wp_filesystem;
				@$upgrader->install( $this->make_url( $type, $file, $product ) );
				
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ):
					$time = 0;
				else:
					$time = 700;
					_e( 'Success', 'pagelines' );
				endif;
				// Output
				$text = '&extend_text=theme_install#installed';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);	
			break;			
			
			case 'theme_delete':
	
				if ( !$checked ) {
					$this->check_creds( 'extend', PL_EXTEND_THEMES_DIR );		
				}
				global $wp_filesystem;
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) )
					$wp_filesystem->delete( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file, true, false  );
				else
					extend_delete_directory( trailingslashit( PL_EXTEND_THEMES_DIR ) . $file );
				if ( isset( $wp_filesystem ) && is_object( $wp_filesystem ) && $wp_filesystem->method != 'direct' ):
					$time = 0;
				else:
					$time = 700;
					_e( 'Success', 'pagelines' );
				endif;
				$text = '&extend_text=theme_delete#installed';
				$this->page_reload( 'pagelines_extend' . $text, null, $time);
			
			break;
			
			case 'theme_activate':

				switch_theme( basename( get_template_directory() ), $file );
				// Output
				_e( 'Activated', 'pagelines' );
				delete_transient( 'pagelines_sections_cache' );
				$this->page_reload( 'pagelines&activated=true&pageaction=activated' );	
			break;

			case 'theme_deactivate':
			
				switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
				// Output
				_e( 'Deactivated', 'pagelines' );
				delete_transient( 'pagelines_sections_cache' );
				$this->page_reload( 'pagelines_extend' );
			break;
			case 'redirect':
			
				echo sprintf( __( 'Sorry only network admins can install %s.', 'pagelines' ), $type );
			
			break;
			case 'purchase':
			
				_e( 'Taking you to PayPal.com', 'pagelines' );
				$this->page_reload( 'pagelines_extend', $file );
			
			break;
			
			case 'login':
				_e( 'Moving to account setup..', 'pagelines' );
				$this->page_reload( 'pagelines_account#Your_Account' );
			break;
		}
		die(); // needed at the end of ajax callbacks
	}

	/**
	 * Uploader for sections.
	 * 
	 */
	function extension_uploader() {
		
		if ( !empty($_POST['upload_check'] ) && check_admin_referer( 'pagelines_extend_upload', 'upload_check') ) {

			if ( $_FILES[ $_POST['type']]['size'] == 0 ) {
				$this->page_reload( 'pagelines_extend&extend_error=blank', null, 0);
				exit();
			}

			// right we made it this far! It needs to be a section!
			$type = $_POST['type'];
			$filename = $_FILES[ $type ][ 'name' ];
			$payload = $_FILES[ $type ][ 'tmp_name' ];
			
						
			if ( false === strpos( $filename, 'section' ) ) {
				$this->page_reload( 'pagelines_extend&extend_error=filename', null, 0);
				exit();
			}
				
			switch ( $type ) {
				
				case 'section':
					$uploader = true;
					$_POST['extend_mode']	=	'section_install';
					$_POST['extend_file']	=	$payload;
					$_POST['extend_path']	= 	str_replace( '.zip', '', $filename );
					$_POST['extend_type']	=	'section';
				break;
				
				case 'plugin':
					$uploader = true;
					$_POST['extend_mode']	=	'plugin_install';
					$_POST['extend_file']	=	$payload;
					$_POST['extend_path']	= 	sprintf( '%1$s/%1$s.php', str_replace( '.zip', '', $filename ) );
					$_POST['extend_type']	=	'plugin';
				break;
				
			}
			
			if ( $uploader )
				$this->extend_it_callback( $uploader, null );
			exit;
		
		}	
	}
	
	/**
	 * See if we have filesystem permissions.
	 * 
	 */	
	function check_creds( $extend = null, $context = WP_PLUGIN_DIR) {

		if ( isset( $_GET['creds'] ) && $_POST && WP_Filesystem($_POST) )
			$this->extend_it_callback( false, true );
			
		if ( !$extend )
			return;			

		if (false === ($creds = @request_filesystem_credentials(admin_url( 'admin.php?page=pagelines_extend&creds=yes'), $type = "", $error = false, $context, $extra_fields = array( 'extend_mode', 'extend_type', 'extend_file', 'extend_path')) ) ) {
			exit; 
		}	
	}
	
	/**
	 * Generate a download link.
	 * 
	 */
	function make_url( $type, $file, $product = null ) {
		
		return sprintf('%s%s/download.php?d=%s.zip%s', PL_API_FETCH, $type, $file, (isset( $product ) ) ? '&product=' . $product : '' );
		
	}
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location, $product = null, $time = 700 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );
		$location = ( $product ) ? $this->get_payment_link( $product ) : $admin;

		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );
 	}

 	function int_download( $location, $time = 300 ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, 'pagelines_extend#integrations' ) );
		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $location, $time );	
		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, %s);</script>', $admin, 700 );
 	}


	/**
	 * Get a PayPal link.
	 * 
	 */
	function get_payment_link( $product ) {
		
		return sprintf( 'https://pagelines.com/api/?paypal=%s|%s', $product, admin_url( 'admin.php' ) );
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
	 * Were back! Flush the cache,
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
	 * Throw up on error.
	 * 
	 */
	function error_handler( $type ) { 
		$a = error_get_last();
		$error =  ( $a['type'] == 4 || $a['type'] == 1 ) ? sprintf( 'Unable to activate the %s.', $type ) : '';
		$error .= ( $error && PL_DEV ) ? sprintf( '<br />%s in %s on line: %s', $a['message'], basename( $a['file'] ), $a['line'] ) : '';
		echo $error;
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
			$new_theme['version'] =		( isset( $up ) ) ? $up : $theme_data['Version'];
			$new_theme['text'] =		$theme_data['Description'];
			$new_theme['tags'] =		$theme_data['Tags'];
			$new_theme['productid'] = 	null;
			$new_theme['count'] = 		null;
			$themes[$theme_data['Stylesheet']] = $new_theme;		
		}
		return $themes;
	}
} // end PagelinesExtensions class