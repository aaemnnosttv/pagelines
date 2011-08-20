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
		
		$this->exprint = 'onClick="extendIt(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\')"';
		
		$this->defaultpane = array(
				'name' 		=> 'Unnamed', 
				'version'	=> 'No version', 
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
	function extension_list( $list = array() ){
		
			$panes = '';
			foreach( $list as $eid => $e ){
			
				$panes .= $this->pane_template( $e );
			
			}
		
			$output = sprintf('<ul class="the_sections plpanes">%s</ul>', $panes);
		
		
			return $output;
		
		
	}
	
	function pane_template( $e ){

			$s = wp_parse_args( $e, $this->defaultpane);

			$buttons = sprintf('<div class="pane-buttons">%s</div>', $s['buttons']);

			$tags =  ( $s['tags'] ) ? sprintf('Tags: %s', $s['tags']) : '';

			$count = ( $s['count'] ) ? sprintf('Downloads: %s', $s['count']) : '';

			$screenshot = ( $s['image'] ) ? sprintf('<div class="extend-screenshot"><a class="screenshot-%s" href="http://api.pagelines.com/%s/img/%s.png" rel="http://api.pagelines.com/%s/img/%s.png"><img src="http://api.pagelines.com/%s/img/thumb-%s.png"></a></div>' , str_replace( '.', '-', $s['key']), $s['type'], $s['key'], $s['type'], $s['key'], $s['type'], $s['key']) : '';

			$title = sprintf('<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-sub">%s</div>%s</div></div>', $s['name'], $this->get_extend_buttons( $e ) , $screenshot );

			$auth = sprintf('<div class="pane-dets"><strong>%s</strong> | by <a href="%s">%s</a></div>', 'v' . $s['version'], $s['auth_url'], $s['auth']);

			$body = sprintf('<div class="pane-desc"><div class="pane-desc-pad">%s %s</div></div>', $s['desc'], $auth);

			$response = sprintf('<li id="response%s" class="install_response"><div class="rp"></div></li>', $s['key']);

			return sprintf('<li class="plpane pane-plugin"><div class="plpane-hl fix"><div class="plpane-pad fix">%s %s %s</div></div></li>%s', $title, $body, $buttons, $response);
		
	}
	
	function get_extend_buttons( $e ){
		
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
				$buttons .= $this->extend_button( $e['key'], $a );
			
		}
		
		return $buttons;
		
	}
	
	function extend_button( $key, $a ){
		
		$d = array(
			'mode'	=> '',
			'case'	=> '', 
			'file'	=> '', 
			'text'	=> 'Extend',
			'dtext'	=> '',
			'key'	=> $key, 
			'type'	=> '',
		);
		
		$a = wp_parse_args($a, $d);
		
		$js_call = sprintf( $this->exprint, $a['case'], $a['key'], $a['type'], $a['file'], $a['dtext']);
		
		$button = sprintf('<span class="extend_button %s" %s>%s</span>', $a['mode'], $js_call, $a['text']);
		
		return $button;
	}
	
	
	function install_button( $e ){
		
		
		$install_js_call = sprintf( $this->exprint, 'section_install', $key, 'sections', $key, 'Installing');

		$button = OptEngine::superlink('Install Section', 'black', '', '', $install_js_call);
		
	}
	
	
	
	/**
	 * Draw a list of extended items
	 */
	function get_extend_plugin( $status = '', $tab = '' ){
		
		
		
		if($status == 'notactive'){
			$btext = 'Activate Extend';
			$text = sprintf('Extend plugin installed, now activate it!');
			
		} elseif($status == 'notinstalled'){
			$btext = 'Install It Now!';
			$text = sprintf('You need to install and activate PageLines Extend Plugin');
			
		}
			
		
		$install_js_call = sprintf( $this->exprint, 'plugin_install', 'ExtendPlugin', 'plugins', 'pagelines-extend', 'Installing');
		
		// The button
		$install_button = OptEngine::superlink($btext, 'blue', 'install_now iblock', '', $install_js_call);
		
		// The banner
		return sprintf('<div id="responseExtendPlugin" class="install-control fix"><span class="banner-text"></span> %s<br/><br/>%s</div>', $text, $install_button);
	}
	
	/**
	 * Draw a list of extended items
	 */
	function extension_banner( $text ){
		
		// The banner
		return sprintf('<div class="install-control fix"><span class="banner-text">%s</span></div>', $text);
	}
	
	
	
		/**
		 * 
		 * Add Javascript to header (hook in contructor)
		 * 
		 */
		function extension_js(){ ?>

			<script type="text/javascript">/*<![CDATA[*/

			function extendIt( mode, key, type, file, duringText ){

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
						extend_file: file
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

							responseElement.effect("highlight", {color: "#CCCCCC"}, 2000).html(response).delay(6500).slideUp();
						}
					});

			}
			/*]]>*/</script>

	<?php }
	
	
}