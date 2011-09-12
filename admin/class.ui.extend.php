<?php
/**
 * 
 *
 *  Extend Control Interface
 *
 *
 *  @package PageLines Admin
 *  @subpackage OptionsUI
 *  @since 2.0.b9
 *
 */


class PageLinesExtendUI {
	

	/**
	 * Construct
	 */
	function __construct() {
		
		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		
		$this->defaultpane = array(
				'name' 		=> 'Unnamed', 
				'version'	=> 'No version', 
				'active'	=> false,
				'desc'		=> 'No description.', 
				'auth_url'	=> 'http://www.pagelines.com',
				'auth'		=> '',
				'image'		=> '',
				'buttons'	=> '',
				'importance'=> '',
				'key'		=> '',
				'type'		=> '',
				'count'		=> '',
				'status'	=> '',
				'actions'	=> array() 
		);
		
		/**
		 * Hooked Actions
		 */
		add_action('admin_head', array(&$this, 'extension_js'));
		
	}

	/**
	 * Draw a list of extended items
	 */
	function extension_list( $list = array(), $mode = 'list'){
		
		$ext = '';
		$active = '';
		
		if($mode == 'graphic'){
			
			foreach( $list as $eid => $e ){
				if($e['active'])
					$active .= $this->graphic_pane( $e, 'active');
				else
					$ext .= $this->graphic_pane( $e );
			}

			$output = sprintf('<ul class="graphic_panes fix">%s%s</ul>', $active, $ext);
			
		} else {
			
			foreach( $list as $eid => $e )
				$ext .= $this->pane_template( $e );
			
			$output = sprintf('<ul class="the_sections plpanes">%s%s</ul>', $active, $ext);
			
		}
		
			return $output;
		
		
	}
	
	function graphic_pane( $e, $style = ''){
	
		$e = wp_parse_args( $e, $this->defaultpane);
		
		$image = ( $e['actions']['install']['condition'] || $e['actions']['login']['condition']|| $e['actions']['purchase']['condition']) ? sprintf( 'http://www.pagelines.com/api/themes/img/%s.png', $e['key'] ) : get_theme_root_uri() .'/'. $e['key'] . '/screenshot.png';
		
		$image = sprintf( '<img class="" src="%s" alt="Screenshot" />', $image );
		
		$title = sprintf('<h2>%s</h2>', $e['name']);
		
		$text = sprintf('<p>%s</p>', $e['desc']);
		
		$link =  $this->get_extend_buttons( $e, 'superlink');
		
		$dtitle = ($style == 'active') ? __('<h4>Active Theme</h4>', 'pagelines') : '';
			
		
		$out = sprintf('<div class="%s graphic_pane media fix">%s<div class="theme-screen img">%s</div><div class="theme-desc bd">%s%s%s</div></div>', $style, $dtitle, $image, $title, $text, $link);
	
		return $out;
		
	}
	
	function pane_template( $e ){

			$s = wp_parse_args( $e, $this->defaultpane);

			// Left for reference
			//$screenshot = ( $s['image'] ) ? sprintf('<div class="extend-screenshot"><a class="screenshot-%s" href="http://api.pagelines.com/%s/img/%s.png" rel="http://api.pagelines.com/%s/img/%s.png"><img src="http://api.pagelines.com/%s/img/thumb-%s.png"></a></div>' , str_replace( '.', '-', $s['key']), $s['type'], $s['key'], $s['type'], $s['key'], $s['type'], $s['key']) : '';

			$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div></div></div>', $s['name'], $this->get_extend_buttons( $e ));

			$auth = sprintf('<div class="pane-dets"><strong>%s</strong> | by <a href="%s">%s</a></div>', 'v' . $s['version'], $s['auth_url'], $s['auth']);

			$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);

			$response = sprintf('<li id="response%s" class="install_response"><div class="rp"></div></li>', $s['key']);

			return sprintf('<li class="plpane pane-plugin"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s </div></div></li>%s', $title, $body, $response);
		
	}
	
	function active_extension(){
		$e = wp_parse_args( $e, $this->defaultpane);

		$image = ( $e['actions']['install']['condition'] || $e['actions']['purchase']['condition']) ? sprintf( 'http://api.pagelines.com/themes/img/%s.png', $e['key'] ) : get_theme_root_uri() .'/'. $e['key'] . '/screenshot.png';

		$image = sprintf( '<img class="" src="%s" alt="Screenshot" />', $image );

		$title = sprintf('<h2>%s</h2>', $e['name']);

		$text = sprintf('<p>%s</p>', $e['desc']);

		$link =  $this->get_extend_buttons( $e, 'superlink');

		$out = sprintf('<div class="graphic_pane media fix"><div class="theme-screen img">%s</div><div class="theme-desc bd">%s%s%s</div></div>', $image, $title, $text, $link);

		return $out;
		
	}
	
	function get_extend_buttons( $e, $style = 'small'){
		
		/* 
			'Mode' 	= the extension handling mode
			'Key' 	= the key of the element in the array, for the response
			'Type' 	= what is being extended
			'File' 	= the url for the extension/install/update
			'duringText' = the text while the extension is happening
		*/
		
		$buttons = '';
		foreach( $e['actions'] as $type => $a ){
			
			if($a['condition'])
				$buttons .= $this->extend_button( $e['key'], $a, $style);
			
		}
		
		return $buttons;
		
	}
	
	function extend_button( $key, $a, $style = 'small'){
		
		$d = array(
			'mode'	=> '',
			'case'	=> '', 
			'file'	=> '', 
			'text'	=> 'Extend',
			'dtext'	=> '',
			'key'	=> $key, 
			'type'	=> '',
			'path'	=> '', 
			'confirm'	=> false
		);
		
		$a = wp_parse_args($a, $d);
		
		$js_call = sprintf( $this->exprint, $a['case'], $a['key'], $a['type'], $a['file'], $a['path'], $a['dtext']);
		
		if($style == 'superlink')
			$button = OptEngine::superlink( $a['text'], $a['mode'], '', '', $js_call);
		else
			$button = sprintf('<span class="extend_button %s" %s>%s</span>', $a['mode'], $js_call, $a['text']);
		
		return $button;
	}
	
	
	function install_button( $e ){
		
		
		$install_js_call = sprintf( $this->exprint, 'section_install', $key, 'sections', $key, __( 'Installing', 'pagelines' ) );

		$button = OptEngine::superlink( __( 'Install Section', 'pagelines' ), 'black', '', '', $install_js_call);
		
	}
	
	/**
	 * Draw a list of extended items
	 */
	function get_extend_plugin( $status = '', $tab = '' ){
		
		$key = 'ext'.$tab;
		
		$name = 'pagelines-sections';
		
		if($status == 'notactive'){
			$file = '/' . trailingslashit( $name ) . $name . '.php'; 
			$btext = 'Activate Sections';
			$text = sprintf( __( 'Sections plugin installed, now activate it!', 'pagelines' ) );
			$install_js_call = sprintf( $this->exprint, 'plugin_activate', $key, 'plugins', $file, '', __( 'Activating', 'pagelines' ) );
			
		} elseif($status == 'notinstalled'){
			$btext = __( 'Install It Now!', 'pagelines' );
			$text = __( 'You need to install and activate PageLines Sections Plugin', 'pagelines' );
			$install_js_call = sprintf( $this->exprint, 'plugin_install', $key, 'plugins', 'pagelines-sections', '/pagelines-sections/pagelines-sections.php', __( 'Installing', 'pagelines' ) );
		}
			
		$eresponse = 'response'.$key;
		
		// The button
		$install_button = OptEngine::superlink($btext, 'blue', 'install_now iblock', '', $install_js_call);
		
		// The banner
		return sprintf('<div class="install-control fix"><span id="%s" class="banner-text">%s</span><br/><br/>%s</div>', $eresponse, $text, $install_button);
	}
	
	/**
	 * Draw a list of extended items
	 */
	function extension_banner( $text ){
		
		// The banner
		return sprintf('<div class="install-control fix"><span class="banner-text">%s</span></div>', $text);
	}
	
	function upload_form( $type, $disabled = false ){
		
			$file = $type;
			
			if ( $disabled )
				return $this->extension_banner( __( 'Sorry uploads do not work with this server config, please use FTP!', 'pagelines' ) );
			
		ob_start();
		 ?>
		<div class="pagelines_upload_form">
			<h4><?php _e( 'Install a section in .zip format', 'pagelines' ) ?></h4>
			<p class="install-help"><?php _e( 'If you have a section in a .zip format, you may install it by uploading it here.', 'pagelines' ) ?></p>
			<?php printf( '<form method="post" enctype="multipart/form-data" action="%s">', admin_url( 'admin.php?page=pagelines_extend' ) ) ?>
				<?php wp_nonce_field( 'pagelines_extend_upload', 'upload_check' ) ?>
				<label class="screen-reader-text" for="<?php echo $file;?>"><?php _e( 'Section zip file', 'pagelines' ); ?></label>
				<input type="file" id="<?php echo $file;?>" name="<?php echo $file;?>" />
				<input type="hidden" name="type" value="<?php echo $file;?>" />
				<input type="submit" class="button" value="<?php esc_attr_e('Install Now', 'pagelines' ) ?>" />
			</form>
		</div>
	<?php 
	
		return ob_get_clean();
	}
	
	function search_extend( $type ){
		
		return $this->extension_banner( __( 'Search functionality is currently disabled. Check back soon!', 'pagelines' ) );
	}
	
	/**
	 * 
	 * Add Javascript to header (hook in contructor)
	 * 
	 */
	function extension_js(){ 
	
		if ( !isset( $_GET['page'] ) || strpos( $_GET['page'], 'pagelines_extend' ) === false )
			return;
		?>
<script type="text/javascript">/*<![CDATA[*/

		function extendIt( mode, key, type, file, path, duringText ){

				/* 
					'Mode' 	= the type of extension
					'Key' 	= the key of the element in the array, for the response
					'Type' 	= ?
					'File' 	= the url for the extension/install/update
					'duringText' = the text while the extension is happening
				*/

				var data = {
					action: 'pagelines_ajax_extend_it_callback',
					extend_mode: mode,
					extend_type: type,
					extend_file: file,
					extend_path: path
				};

				var responseElement = jQuery('#dialog');
				var duringTextLength = duringText.length + 3;
				var dotInterval = 400;
				
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					beforeSend: function(){

						responseElement.html( duringText ).dialog({ 
							minWidth: 500, 
							minHeight: 100,
							modal: true, 
							dialogClass: 'ajax_dialog', 
							open: function(event, ui) { 
								jQuery(".ui-dialog-titlebar-close").hide(); 
							} 
						});
						
						//responseElement.html( duringText ).slideDown();

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
						responseElement.dialog().html(response);
					
					}
				});

		}
		/*]]>*/</script>

<?php }
	
	
}

/**
 *
 *  Returns Extension Array Config
 *
 */
function extension_array(  ){

	global $extension_control;

	$d = array(
		'Sections' => array(
			'icon'		=> PL_ADMIN_ICONS.'/dragdrop.png',
			'htabs' 	=> array(
				'added'	=> array(
					'title'		=> __( 'Sections Added Via Extend', 'pagelines' ),
					'callback'	=> $extension_control->extension_sections( 'user' )
					),
				'core'	=> array(
					'title'		=> __( 'Sections From PageLines Framework', 'pagelines' ),
					'callback'	=> $extension_control->extension_sections( 'internal' )
					),
				'child'	=> array(
					'title'		=> __( 'Sections From Your Child Theme', 'pagelines' ),
					'callback'	=> $extension_control->extension_sections( 'child' )
					),
				'add_sections'	=> array(
					'type'		=> 'subtabs',
					'title'		=> __( 'Extend Sections', 'pagelines' ),
					'class'		=> 'left ht-special',
					'featured'	=> array(
						'title'		=> __( 'Featured on PageLines.com', 'pagelines' ),
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'featured' )
						),
					'top_premium'	=> array(
						'title'		=> __( 'Premium PageLines Sections', 'pagelines' ),
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'premium' )
						),
					'top_free'	=> array(
						'title'		=> __( 'Free PageLines Sections', 'pagelines' ),
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_sections_install( 'free' )
						),
					'search'		=> array(
						'title'		=> __( 'Search Sections', 'pagelines' ),
						'callback'	=> $extension_control->ui->search_extend( 'section' )
					),
					'upload'		=> array(
						'title'		=> __( 'Upload Sections', 'pagelines' ),
						'callback'	=> $extension_control->ui->upload_form( 'section', ( !is_writable( WP_PLUGIN_DIR ) ) ? true : false )
					),
					
				)
			)

		),
		'Themes' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-themes.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> __( 'Installed PageLines Themes', 'pagelines' ),
					'callback'	=> $extension_control->extension_themes( 'installed' )
					),
				'add_themes'	=> array(
					'type'		=> 'subtabs',
					'title'		=> __( 'Extend Themes', 'pagelines' ),
					'class'		=> 'left ht-special',
					'featured'	=> array(
						'title'		=> __( 'Featured PageLines Themes', 'pagelines' ),
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_themes( 'featured' )
						),
					'premium'	=> array(
						'title'		=> __( 'Premium PageLines Themes', 'pagelines' ),
						'class'		=> 'right',
						'callback'	=> $extension_control->extension_themes( 'premium' )
						),
//					'upload'		=> array(
//						'title'		=> __( 'Upload A PageLines Theme', 'pagelines' ),
//						'callback'	=> $extension_control->ui->upload_form( 'theme' )
//						),
					)
				)
		),
		'Plugins' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-plugins.png',
			'htabs' 	=> array(
				
				'installed'	=> array(
					'title'		=> __( 'Installed PageLines Plugins', 'pagelines' ),
					'callback'	=> $extension_control->extension_plugins( 'installed' )
					),
				'add_plugins'	=> array(
					'type'		=> 'subtabs',
					'title'		=> __( 'Add PageLines Plugins', 'pagelines' ),
					'class'		=> 'left ht-special',
					'top_premium'		=> array(
						'title'		=> __( 'Premium Plugins', 'pagelines' ),
						'callback'	=> $extension_control->extension_plugins( 'premium' )
					),
					'top_free'		=> array(
						'title'		=> __( 'Free Plugins', 'pagelines' ),
						'callback'	=> $extension_control->extension_plugins( 'free' )
					),
					'search'		=> array(
						'title'		=> __( 'Search Plugins', 'pagelines' ),
						'callback'	=> $extension_control->ui->search_extend( 'plugin' )
					),
//					'upload'		=> array(
//						'title'		=> __( 'Upload Plugin', 'pagelines' ),
//						'callback'	=> $extension_control->ui->upload_form( 'plugin' )
//					),
				)
			)

		),
		'Import-Export' => array(
			'icon'		=> PL_ADMIN_ICONS.'/extend-inout.png',
			'import_set'	=> array(
				'default'	=> '',
				'type'		=> 'import_export',
				'layout'	=> 'full',
				'title'		=> __( 'Import/Export PageLines Settings', 'pagelines' ),						
				'shortexp'	=> __( 'Use this form to upload PageLines settings from another install.', 'pagelines' ),
			),
		),
		'Your_Account'	=> array(
			'icon'		=> PL_ADMIN_ICONS.'/rocket-fly.png',
			'credentials' => array(
				'version'	=> 'pro',
				'type'		=> 'updates_setup',
				'title'		=> __( 'Configure PageLines Account &amp; Auto Updates', 'pagelines' ),
				'shortexp'	=> __( 'Get your latest updates automatically, direct from PageLines.', 'pagelines' ),
				'layout'	=> 'full',
			),
		)

	);

	return apply_filters('extension_array', $d); 
}


