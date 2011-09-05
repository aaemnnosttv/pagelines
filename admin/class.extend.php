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

		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		$this->username = get_pagelines_option( 'lp_username' );
		$this->password = get_pagelines_option( 'lp_password' );
		
		$this->ui = new PageLinesExtendUI;
		
		add_action('wp_ajax_pagelines_ajax_extend_it_callback', array(&$this, 'extend_it_callback'));	
		add_action( 'admin_init', array(&$this, 'extension_uploader' ) );
		add_action( 'admin_init', array(&$this, 'update_lpinfo' ) );
		add_action( 'admin_init', array(&$this, 'launchpad_returns' ) );
 	}

	function update_lpinfo() {

		if (isset($_POST['form_submitted']) && $_POST['form_submitted'] === 'plinfo' ) {

			pagelines_update_option( 'lp_password', sanitize_text_field( $_POST['lp_password'] ) );
			pagelines_update_option( 'lp_username', sanitize_text_field( $_POST['lp_username'] ) );
			pagelines_update_option( 'disable_updates', ( isset( $_POST['disable_auto_update'] ) ) ? true : false );
			wp_redirect( admin_url('admin.php?page=pagelines_extend&plinfo=true') );
			$this->flush_caches();
			exit;
		}
	}

	function launchpad_returns() {
		
		if (isset( $_GET['api_returned'] ) )
			$this->flush_caches();
	}
	function flush_caches() {
		
		// Flush all our transienst ( Makes this save button a sort of reset button. )
		delete_transient( 'pagelines-update-' . THEMENAME  );
		delete_transient( 'pagelines_sections_api_themes' );
		delete_transient( 'pagelines_sections_api_sections' );
		delete_transient( 'pagelines_sections_api_plugins' );
		delete_transient( 'pagelines_sections_cache' );
		
	}

 	function extension_sections_install( $tab = '' ) {
 		
 		/*
 			TODO make error checking better...
			TODO Use plugin?
 		*/
		
 		if ( !$this->has_extend_plugin() )
			return $this->ui->get_extend_plugin( $this->has_extend_plugin('status'), $tab );

		$sections = $this->get_latest_cached( 'sections' );

		if ( ! is_object($sections) ) 
			return $sections;


		$list = array();
		
		foreach( $sections as $key => $s ) {

			$check_file = sprintf('%1$s/%2$s/%3$s.php', PL_EXTEND_DIR, $key, 'section.' . $key); 

			if ( !isset( $s->type) )
				$s->type = 'free';
			
			if ($tab !== $s->type)
				continue;

			if ( file_exists( $check_file ) )
				continue;

			$key = str_replace( '.', '', $key );
		
			$actions = array(
				'install'	=> array(
					'mode'		=> 'install',
					'condition'	=> true,
					'case'		=> 'section_install',
					'type'		=> $s->type,
					'file'		=> $key,
					'path'		=> $s->class,
					'text'		=> __( 'Install', 'pagelines' ),
					'dtext'		=> __( 'Installing', 'pagelines' )
					)
			);
			
			$list[$key] = array(
					'name' 		=> $s->name, 
					'version'	=> $s->version, 
					'desc'		=> $s->text, 
					'auth_url'	=> $s->author_url, 
					'auth'		=> $s->author,
					'image'		=> ( isset( $s->image ) ) ? $s->image : '',
					'type'		=> 'sections',
					'key'		=> $key, 
					'ext_txt'	=> __( 'Installing', 'pagelines' ), 
					'actions'	=> $actions
			);
			
		}
		
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'Browsing %1$s sections is currently disabled. <br/>Check back soon!', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}

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
	
	/*
	 * Get sections that are installed.
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

			foreach( $type as $key => $s)
				$type[$key]['status'] = ( isset( $disabled[ $s['type'] ][ $s['class'] ] ) ) ? 'disabled' : 'enabled';

			/*
	 		 * Sort Alphabetically
	 		 */
 			$type = pagelines_array_sort( $type, 'name' );

 			foreach( $type as $key => $s ) { // main loop
	
				if ( $tab === 'user' && ( $s['type'] === 'custom' || $s['type'] === 'parent' ) )
					continue;

				if ( $tab === 'internal' && ( $s['type'] === 'custom' || $s['type'] === 'child' || $s['tags'] == 'internal' ) )
					continue;						

				if ( $tab === 'child' && ( $s['type'] === 'child' || $s['type'] === 'parent' ) )
					continue;
									
  				if ( $s['type'] == 'parent' && ( isset( $available['child'][ $s['class'] ] ) || isset( $available['custom'][ $s['class'] ] ) ) )
					continue;
				
				$enabled = ( $s['status'] == 'enabled' ) ? true : false;

				$file = basename( $s['base_dir'] );

				$upgrade_available = $this->upgrade_available( $upgradable, 'section.' . $file, $s);
				$delete = ( !$enabled && ( $tab !== 'child' && $tab !== 'internal' ) ) ? true : false;
				$actions = array(
					'activate'	=> array(
						'mode'		=> 'activate',
						'condition'	=> (!$enabled) ? true : false,
						'case'		=> 'section_activate',
						'type'		=> $s['type'],
						'file'		=> $s['class'],
						'text'		=> __( 'Activate', 'pagelines' ),
						'dtext'		=> __( 'Activating', 'pagelines' ),
					) ,
					'deactivate'=> array(
						'mode'		=> 'deactivate',
						'condition'	=> $enabled,
						'case'		=> 'section_deactivate',
						'type'		=> $s['type'],
						'file'		=> $s['class'],
						'text'		=> __( 'Deactivate', 'pagelines' ),
						'dtext'		=> __( 'Deactivating', 'pagelines' ),
					),
					'upgrade'	=> array(
						'mode'		=> 'upgrade',
						'condition'	=> $upgrade_available,
						'case'		=> 'section_upgrade',
						'type'		=> 'sections',
						'file'		=> $file,
						'text'		=> __( 'Upgrade', 'pagelines' ),
						'dtext'		=> sprintf( __( 'Upgrading to version %1$s', 'pagelines' ), $upgrade_available ),
					),
					'delete'	=> array(
						'mode'		=> 'delete',
						'condition'	=> $delete,
						'case'		=> 'section_delete',
						'type'		=> 'sections',
						'file'		=> $file,
						'text'		=> __( 'Delete', 'pagelines' ),
						'dtext'		=> __( 'Deleting', 'pagelines' ),
						'confirm'	=> true
					)
					
				);
				
				$list[] = array(
						'name' 		=> $s['name'], 
						'version'	=> !empty( $s['version'] ) ? $s['version'] : CORE_VERSION, 
						'desc'		=> $s['description'],
						'auth_url'	=> $s['authoruri'],
						'type'		=> 'sections',
						'object'	=> $s['class'],
						'tags'		=> ( isset( $s['tags'] ) ) ? $s['tags'] : '',
						'image'		=> ( isset( $s['image'] ) ) ? $s['image'] : '',
						'auth'		=> $s['author'],
						'importance'=> $s['importance'],
						'key'		=> $key,
						'status'	=> $s['status'], 
						'actions'	=> $actions
				);

 			}
 		} 
	
		
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'No %1$s sections are currently available. <br/>Check back soon!', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}

	function upgrade_available( $upgradable, $file, $s){
	
		if ( is_object( $upgradable ) && isset( $upgradable->$file ) && $s['version'] < $upgradable->$file->version ) 
			return $upgradable->$file->version;
		else 
			return false;
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
		foreach( $plugins as $key => $p ) {
			$plugins[$key]['status'] = $this->plugin_check_status( WP_PLUGIN_DIR . $p['file'] );
			$plugins[$key]['name'] = ( $plugins[$key]['status']['data']['Name'] ) ? $plugins[$key]['status']['data']['Name'] : $plugins[$key]['name'];
			
			
		}

		$plugins = pagelines_array_sort( $plugins, 'status', 'status' ); // sort by status

		// reset array keys ( sort functions reset keys to int )
		foreach( $plugins as $key => $p ) {
			
			unset( $plugins[$key] );
			$key = str_replace( '.php', '', basename( $p['file'] ) );
			$plugins[$key] = $p;
			
		}
		
		$list = array();

		foreach( $plugins as $key => $p ) {
	
			if ( !isset( $p['type'] ) )
				$p['type'] = 'free';
			if ( $tab === 'installed' && !isset( $p['status']['status'] ) )
				continue;
			if ( ( $tab === 'premium' || $tab === 'free' ) && isset( $p['status']['status'] ) )
				continue;
			if ( $tab === 'premium' && $p['type'] === 'free' )
				continue;
			if ( $tab === 'free' && $p['type'] === 'premium' )
				continue;
			if ( !isset( $p['status'] ) )
				$p['status'] = array( 'status' => '' );
				
			$install = ($p['status']['status'] == '' ) ? true : false;

			$upgrade_available = ( isset( $p['status']['version'] ) && $p['version'] > $p['status']['version'] ) ? true : false;
			
			$active = ($p['status']['status'] == 'active') ? true : false;
			
			$deactivated = (!$install && !$active) ? true : false;
			
			$delete = ( $deactivated ) ? true : false;
			
			$actions = array(
				'install'	=> array(
					'mode'		=> 'install',
					'condition'	=> $install,
					'case'		=> 'plugin_install',
					'type'		=> 'plugins',
					'file'		=> $key,
					'text'		=> __( 'Install', 'pagelines' ),
					'path'		=> $p['file'],
					'dtext'		=> __( 'Installing', 'pagelines' ),
				),
				'activate'	=> array(
					'mode'		=> 'activate',
					'condition'	=> $deactivated,
					'case'		=> 'plugin_activate',
					'type'		=> 'plugins',
					'file'		=> $p['file'],
					'text'		=> __( 'Activate', 'pagelines' ),
					'dtext'		=> __( 'Activating', 'pagelines' ),
				),
				'upgrade'	=> array(
					'mode'		=> 'upgrade',
					'condition'	=> $upgrade_available,
					'case'		=> 'plugin_upgrade',
					'type'		=> 'plugins',
					'file'		=> $key,
					'text'		=> sprintf( __( 'Upgrade to %1$s', 'pagelines' ), $p['version'] ),
					'dtext'		=> __( 'Upgrading', 'pagelines' ),
				),
				'deactivate'	=> array(
					'mode'		=> 'deactivate',
					'condition'	=> $active,
					'case'		=> 'plugin_deactivate',
					'type'		=> 'plugins',
					'file'		=> $p['file'],
					'text'		=> __( 'Deactivate', 'pagelines' ),
					'dtext'		=> __( 'Deactivating', 'pagelines' ),
				),
				'delete'	=> array(
					'mode'		=> 'delete',
					'condition'	=> $delete,
					'case'		=> 'plugin_delete',
					'type'		=> 'plugins',
					'file'		=> $p['file'],
					'text'		=> __( 'Delete', 'pagelines' ),
					'dtext'		=> __( 'Deleting', 'pagelines' ),
					'confirm'	=> true
				)
			);
					
			$list[$key] = array(
					'name' 		=> $p['name'], 
					'version'	=> ( isset( $p['status']['data'] ) ) ? $p['status']['data']['Version'] : $p['version'], 
					'desc'		=> $p['text'],
					'tags'		=> ( isset( $p['tags'] ) ) ? $p['tags'] : '',
					'auth_url'	=> $p['author_url'], 
					'image'		=> ( isset( $p['image'] ) ) ? $p['image'] : '',
					'auth'		=> $p['author'], 
					'key'		=> $key,
					'type'		=> 'plugins',
					'count'		=> $p['count'],
					'actions'	=> $actions
			);	
				
		}
	
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner( __( 'No Plugins Are Installed.', 'pagelines' ) );
		elseif(empty($list))
			return $this->ui->extension_banner( sprintf( __( 'Browsing %1$s plugins is currently disabled. <br/>Check back soon!', 'pagelines' ), $tab ) );
		else 
			return $this->ui->extension_list( $list );
	}

	function extension_scan_themes( $themes ) {
		
		$themes = json_decode(json_encode($themes), true);
		
		$get_themes = get_themes();

		foreach( $get_themes as $theme => $theme_data ) {
		
			if ( $theme_data['Template'] != 'pagelines' )
				continue;
			if ( 'pagelines' == $theme_data['Stylesheet'] )
				continue;
			if ( in_array( $theme, $themes ) )
				continue;
//			if ( CHILDTHEMENAME === $theme_data['Stylesheet'])
//				continue;
			
			// If we got this far, theme is a pagelines child theme not handled by the API
			// So we need to inject it into our themes array.
			
			$new_theme = array();
			$new_theme['name'] =		$theme_data['Name'];
			$new_theme['author'] =		$theme_data['Author Name'];
			$new_theme['author_url'] =	$theme_data['Author URI'];
			$new_theme['version'] =		$theme_data['Version'];
			$new_theme['text'] =		$theme_data['Description'];
			$new_theme['tags'] =		$theme_data['Tags'];
			$new_theme['productid'] = 	null;
			$new_theme['count'] = 		null;
			$themes[$theme_data['Stylesheet']] = $new_theme;		
		}
		return $themes;
	}
	
	function extension_themes( $tab = '' ) {

		$themes = $this->get_latest_cached( 'themes' );

		if ( !is_object($themes) ) 
			return $themes;
			
		$output = '';
		$status = false;
		$list = array();
		
		$themes = $this->extension_scan_themes( $themes );

		foreach( $themes as $key => $theme ) {
			
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

				if ( $tab == 'featured' ) // featured not implemented yet
					continue;

				$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key );
				
				if ( file_exists( $check_file ) )
					$exists = true;
				
				if ( ( $tab == 'premium' || $tab == 'featured' ) && isset($exists) )
					continue;
					
				if ( $tab == 'installed' && !isset( $exists) )
					continue;
				
				if ( isset( $exists ) && $data = get_theme_data( $check_file ) ) 
					$status = 'installed';
					
				$is_active = ( $key  == basename( get_stylesheet_directory() ))	? true : false;
					
				$updates_configured = ( is_array( $a = get_transient('pagelines-update-' . THEMENAME ) ) && isset($a['package']) && $a['package'] !== 'bad' ) ? true : false;	
					
				$activate = ($status == 'installed' && !$is_active) ? true : false;
				$deactivate = ($status == 'installed' && $is_active) ? true : false;
				$upgrade_available = (isset($data) && $data['Version'] && $theme['version'] > $data['Version']) ? true : false;
			
				$purchase = ( !isset( $theme['purchased'] ) && !$status && $updates_configured ) ? true : false;
				$install = ( !$status && !$purchase && $updates_configured ) ? true : false;
				$delete = ( $activate ) ? true : false;
				
				$login = ( !$updates_configured && !$status );
				
				$actions = array(
					'install'	=> array(
						'mode'		=> 'install',
						'condition'	=> $install,
						'case'		=> 'theme_install',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> __( 'Install', 'pagelines' ),
						'dtext'		=> __( 'Installing', 'pagelines' ),
					),
					'activate'	=> array(
						'mode'		=> 'activate',
						'condition'	=> $activate,
						'case'		=> 'theme_activate',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> __( 'Activate', 'pagelines' ),
						'dtext'		=> __( 'Activating', 'pagelines' ),
					),
					'deactivate'	=> array(
						'mode'		=> 'deactivate',
						'condition'	=> $deactivate,
						'case'		=> 'theme_deactivate',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> __( 'Deactivate', 'pagelines' ),
						'dtext'		=> __( 'Deactivating', 'pagelines' ),
					),
					'upgrade'	=> array(
						'mode'		=> 'upgrade',
						'condition'	=> $upgrade_available,
						'case'		=> 'theme_upgrade',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> sprintf( __( 'Upgrade to %1$s', 'pagelines' ), $theme['version'] ),
						'dtext'		=> __( 'Upgrading', 'pagelines' ),
					),
					'purchase'	=> array(
						'mode'		=> 'purchase',
						'condition'	=> $purchase,
						'case'		=> 'theme_purchase',
						'type'		=> 'themes',
						'file'		=> $theme['productid'],
						'text'		=> __( 'Purchase', 'pagelines' ),
						'dtext'		=> __( 'Redirecting', 'pagelines' ),
					),
					'delete'	=> array(
						'mode'		=> 'delete',
						'condition'	=> $delete,
						'case'		=> 'theme_delete',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> __( 'Delete', 'pagelines' ),
						'dtext'		=> __( 'Deleting', 'pagelines' ),
						'confirm'	=> true
					),
					'login'	=> array(
						'mode'		=> 'login',
						'condition'	=> $login,
						'case'		=> 'theme_login',
						'type'		=> 'themes',
						'file'		=> $key,
						'text'		=> __( 'Login', 'pagelines' ),
						'dtext'		=> __( 'Redirecting', 'pagelines' ),
					)
				);
				$list[$key] = array(
						'theme'		=> $theme,
						'name' 		=> $theme['name'], 
						'active'	=> $is_active,
						'version'	=> ( !empty( $status ) && isset( $data['Version'] ) ) ? $data['Version'] : $theme['version'], 
						'desc'		=> $theme['text'],
						'tags'		=> ( isset( $theme['tags'] ) ) ? $theme['tags'] : '',
						'auth_url'	=> $theme['author_url'], 
						'image'		=> ( isset( $theme['image'] ) ) ? $theme['image'] : '',
						'auth'		=> $theme['author'], 
						'key'		=> $key,
						'type'		=> 'themes',
						'count'		=> $theme['count'],
						'actions'	=> $actions
				);
			
		}
		
		
		
		if(empty($list) && $tab == 'installed')
			return $this->ui->extension_banner( __( 'No PageLines themes are currently installed.', 'pagelines' ) );
		elseif(empty($list))
			return $this->ui->extension_banner( sprintf( __( 'Browsing %1$s themes is currently disabled. <br/>Check back soon!', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list, 'graphic' );
			
	}




	function extension_uploader() {
		
		if ( !empty($_POST['upload_check'] ) && check_admin_referer( 'pagelines_extend_upload', 'upload_check') ) {

			if ( $_FILES[ $_POST['type']]['size'] == 0 )
				return;

			// right we made it this far! Its either a section, plugin or a theme!
			$type = $_POST['type'];
			$filename = $_FILES[ $type ][ 'name' ];
			$payload = $_FILES[ $type ][ 'tmp_name' ];
			
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
				
			}
			
			if ( $uploader )
				$this->extend_it_callback( $uploader );
			exit;
		
		}	
	}
	/**
	 * 
	 * Extension AJAX callbacks
	 * 
	 */
	function extend_it_callback( $uploader = false ) {

		/*
			TODO reload callbacks just go to the panel, need tab as well
		*/	
		
		// 1. Libraries
			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
			include( PL_ADMIN . '/library.extension.php' );
	
		// 2. Variable Setup
			$mode =  $_POST['extend_mode'];
			$type =  $_POST['extend_type'];
			$file =  $_POST['extend_file'];
			$path =  $_POST['extend_path'];

			$this->do_sanity( $mode );		
		// 3. Do our thing...

		switch ( $mode ) {
			
			case 'plugin_install':

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$destination = ( ! $uploader ) ? $this->make_url( $type, $file ) : $file;
				
				@$upgrader->install( $destination );
				activate_plugin( $path );
				echo ( !$uploader ) ? __( 'Plugin installed.', 'pagelines' ) : '';
				$text = ( $uploader ) ? '&extend_upload=plugin' : '';
				$this->page_reload( 'pagelines_extend' . $text );
			break;	

			case 'plugin_activate':

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

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$options = array( 'package' => ( ! $uploader) ? $this->make_url( 'sections', $file ) : $file, 
						'destination'		=> ( ! $uploader) ? trailingslashit( PL_EXTEND_DIR ) . $file : trailingslashit( PL_EXTEND_DIR ) . $path, 
						'clear_destination' => false,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);
				@$upgrader->run($options);
				// Output
				$available = get_option( 'pagelines_sections_disabled' );
				unset( $available['child'][$path] );
				update_option( 'pagelines_sections_disabled', $available );
				echo ( !$uploader ) ? __( 'Section installed.', 'pagelines' ) : '';
				$text = ( $uploader ) ? '&extend_upload=section' : '';
				$this->page_reload( 'pagelines_extend' . $text );
			break;
			
			case 'section_upgrade':

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> trailingslashit( PL_EXTEND_DIR ) . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				_e( 'Success! Section Upgraded.', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'section_delete':
			
			extend_delete_directory(trailingslashit( PL_EXTEND_DIR ) . $file);
			echo 'Section Deleted.';
			$this->page_reload( 'pagelines_extend' );	
			break;
			
			case 'plugin_upgrade':

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> trailingslashit( WP_PLUGIN_DIR ) . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				_e( 'Upgraded', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );		
			break;
			
			case 'plugin_delete':

				delete_plugins( array( ltrim( $file, '/' ) ) );
				_e( 'Deleted', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );
			break;
			
			case 'theme_upgrade':

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> PL_EXTEND_THEMES_DIR . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);

				@$upgrader->run($options);
				// Output
				_e( 'Upgraded', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );		
			break;			
			
			case 'theme_install':

				$skin = new PageLines_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader($skin);
				$options = array( 'package' => $this->make_url( $type, $file ), 
						'destination'		=> PL_EXTEND_THEMES_DIR . $file, 
						'clear_destination' => true,
						'clear_working'		=> false,
						'is_multi'			=> false,
						'hook_extra'		=> array() 
				);
				@$upgrader->run($options);
				// Output
				_e( 'Installed', 'pagelines' );
				$this->page_reload( 'pagelines_extend#installed' );		
			break;			
			
			case 'theme_delete':
				
				extend_delete_directory( PL_EXTEND_THEMES_DIR . $file );
				_e( 'Deleted', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );
			
			break;
			
			case 'theme_activate':

				switch_theme( basename( get_template_directory() ), $file );
				// Output
				_e( 'Activated', 'pagelines' );
				$this->page_reload( 'pagelines' );	
			break;

			case 'theme_deactivate':
			
				switch_theme( basename( get_template_directory() ), basename( get_template_directory() ) );
				// Output
				_e( 'Deactivated', 'pagelines' );
				$this->page_reload( 'pagelines_extend' );
			break;
			
			case 'theme_purchase':
			
				_e( 'Transferring to PageLines.com', 'pagelines' );
				$this->page_reload( 'pagelines_extend', $file );
			
			break;
			
			case 'theme_login':
				_e( 'Moving to account page..', 'pagelines' );
				$this->page_reload( 'pagelines_extend#Your_Account' );
			break;
		}
		die(); // needed at the end of ajax callbacks
	}
	
	function make_url( $type, $file ) {
		
		return sprintf('%s/%s/download.php?d=%s.zip', PL_API_FETCH, $type, $file);
		
	}
	
	/**
	 * Reload the page
	 * Helper function
	 */
 	function page_reload( $location, $product = null ) {
	
		$r = rand( 1,100 );
		$admin = admin_url( sprintf( 'admin.php?r=%1$s&page=%2$s', $r, $location ) );
		$location = ( $product ) ? sprintf( '%1$s?price_group=-%2$s&redir=%3$s', PL_LAUNCHPAD_FRAME, $product, $admin ): $admin;
		printf('<script type="text/javascript">setTimeout(function(){ window.location.href = \'%s\';}, 700);</script>', $location );
 	}

	function do_sanity( $mode ) {

		switch( $mode ) {
			
			case 'plugin_install':
				if ( ! is_writable( WP_PLUGIN_DIR ) )
					$error = __( 'Plugins DIR is not writable by WordPress we cannot install any plugins!', 'pagelines' );
			break;
			
			case 'section_install':
				if ( ! is_writable( PL_EXTEND_DIR ) )
					$error = __( 'The sections DIR is not writable by WordPress we cannot install sections!', 'pagelines' );
			break;
			
			case 'theme_install':
				if ( ! is_writable( PL_EXTEND_THEMES_DIR ) )
					$error = __( 'The themes DIR is not writable by WordPress we cannot install themes!', 'pagelines' );
			break;
		}
		if (isset($error)) {
			$this->page_reload( 'pagelines_extend' . '&extend_error=' . $error );
			exit;
		}
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
		
		$url = trailingslashit( PL_API . $type );
		$options = array(
			'sslverify'	=>	false,
			'body' => array(
				'username'	=>	( $this->username != '' ) ? $this->username : false,
				'password'	=>	( $this->password != '' ) ? $this->password : false
			)
		);
		
		if ( false === ( $api = get_transient( 'pagelines_sections_api_' . $type ) ) ) {
			$response = wp_remote_post( $url, $options );
			$api = wp_remote_retrieve_body( $response );
			set_transient( 'pagelines_sections_api_' . $type, $api, 300 );			
		}
		if( is_wp_error( $api ) )
			return __( '<h2>Unable to fetch from API</h2>', 'pagelines' );

		return json_decode( $api );
	}

 } // end PagelinesExtensions class
