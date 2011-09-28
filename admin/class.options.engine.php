<?php
/**
 * 
 *
 *  Option Engine Class 
 *  Sorts and Draws options based on the 'option array'
 *  Option array is loaded in config.option.php and through filters
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
class OptEngine {

	function __construct( $settings_field = null ) {
		
		$this->settings_field = $settings_field;
		
		$this->defaults = array(
			'post_id'				=> '', 
			'setting'				=> '',
			'default' 				=> '',
			'default_free'		 	=> null,
			'inputlabel' 			=> '',
			'type' 					=> 'check',
			'title' 				=> '',				
			'shortexp' 				=> '',
			'exp'					=> '',
			'wp_option'				=> false,
			'version' 				=> null,
			'version_set_default' 	=> 'free',
			'imagepreview' 			=> 200, 
			'selectvalues' 			=> array(),
			'fields'				=> array(),
			'optionicon' 			=> '', 
			'vidlink' 				=> null, 
			'vidtitle'				=> '',
			'docslink' 				=> null,
			'layout' 				=> 'normal', 
			'count_number' 			=> 10, 
			'selectors'				=> '', 
			'inputsize'				=> 'regular',
			'callback'				=> '',
			'css_prop'				=> '',
			'pro_note'				=> false, 
			'htabs'					=> array(), 
			'height'				=> '0px',
			'width'					=> '0px',
			'sprite'				=> '',
			'showname'				=> false, 
			'special'				=> null,
			'flag'					=> ''
		);
		
	}

	/**
	 * Option generation engine
	 *
	 */
	function option_engine($oid, $o, $pid = null, $setting = null){

		$o = wp_parse_args( $o, $this->defaults );

		$setting = (isset($this->settings_field)) ? $this->settings_field : PAGELINES_SETTINGS;
		
		$o['pid'] = $pid;
		
		$oset = array('post_id' => $pid, 'setting' => $setting);
		
		if($o['type'] == 'select_same'){
			
			$new = array_flip($o['selectvalues']);
			
			foreach($new as $key => $val)
				$new[$key] = array('name' => $key);
			
			$o['selectvalues'] = $new;
			
		}
				
		
		if($this->settings_field == 'meta'){
			
			$o['val'] = plmeta($oid, $oset);
			$o['input_name'] = $oid;
			$o['input_id'] = get_pagelines_option_id( $oid );
			
			
			if(!empty($o['selectvalues'])){
				foreach($o['selectvalues'] as $sid => $s){

					$o['selectvalues'][$sid]['val'] = plmeta($sid, $oset);
					$o['selectvalues'][$sid]['input_id'] = get_pagelines_option_id( $sid );
					$o['selectvalues'][$sid]['input_name'] = $sid;

				}
			}
			
		} elseif($this->settings_field == PAGELINES_SPECIAL){
			
			$oset['subkey'] = $oid;
			
			$o['val'] = ploption( $o['special'], $oset );			
			$o['input_name'] = plname($o['special'], $oset);
			$o['input_id'] = plid( $o['special'], $oset);
			
			if(!empty($o['selectvalues'])){
				foreach($o['selectvalues'] as $sid => $s){
					$oset['subkey'] = $sid;
					$o['selectvalues'][$sid]['val'] = ploption( $o['special'], $oset);
					$o['selectvalues'][$sid]['input_id'] = plid( $o['special'], $oset);
					$o['selectvalues'][$sid]['input_name'] = plname( $o['special'], $oset);

				}
			}
			
		}
		 else {
			$o['val'] = ploption( $oid, $oset );
			$o['input_name'] = get_pagelines_option_name( $oid, null, null, $setting );
			$o['input_id'] = get_pagelines_option_id( $oid, null, null, $setting );		

			if(!empty($o['selectvalues'])){
				foreach($o['selectvalues'] as $sid => $s){
					$o['selectvalues'][$sid]['val'] = ploption( $sid, $oset );
					$o['selectvalues'][$sid]['input_id'] = get_pagelines_option_id( $sid );
					$o['selectvalues'][$sid]['input_name'] = get_pagelines_option_name($sid, null, null, $setting);
				}
			}
		}
		
		
		
		if( $this->_do_the_option($oid, $o) ){
		
			printf('<div class="optionrow fix %s">', $this->_layout_class( $o ));
		
			$this->get_option_title( $oid, $o ); 
	
			printf('<div class="optin fix"><div class="oinputs"><div class="oinputs-pad">');
	
			$this->option_breaker($oid, $o);
	
			printf('</div></div>');
		
			echo $this->_get_explanation($oid, $o);
			
			echo '<div class="clear"></div></div></div>';
		
		}
	}
	
	function _get_explanation($oid, $o){
		
		$fullwidth = ($o['layout'] == 'full') ? true : false;
		
		$show = ($o['exp'] && $o['type'] != 'text_content' && $o['layout'] != 'interface') ? true : false;
		
		if($show){
					
			$toggle = ($fullwidth) ? '<div class="more_exp" onClick="jQuery(this).next().next().fadeToggle();">More Info &rarr;</div><div class="clear"></div>' : '';		
					
			$text = sprintf('%s<p>%s</p>', (!$fullwidth) ? '<h5>More Info</h5>' :'', $o['exp']);
				
			$pro_note = ($o['pro_note'] && !VPRO) ? sprintf('<p class="pro_note"><strong>Pro Version Note:</strong><br/>%s</p>',  $o['pro_note']) : '';
			 
			printf('<div class="oexp">%s<div class="oexp-effect"><div class="oexp-pad">%s %s</div></div></div>', $toggle, $text, $pro_note);
			
		}
	
	}
	
	function _layout_class( $o ){
		$layout_class = '';
		$layout_class .= ( isset( $o['layout'] ) && $o['layout']=='full' ) ? ' wideinputs' : '';
		$layout_class .= ( isset( $o['layout'] ) && $o['layout']=='interface' ) ? ' interface' : '';
		return $layout_class;
	}
	
	function get_option_title($oid, $o){ 
		if( $o['title'] ): ?>
		<div class="optiontitle fix">
			<div class="optiontitle-pad fix">
				<?php if( isset($o['vidlink']) ):?>
					<a class="vidlink thickbox" title="<?php if($o['vidtitle']) echo $o['vidtitle']; ?>" href="<?php echo $o['vidlink']; ?>?hd=1&KeepThis=true&height=450&width=700&TB_iframe=true">
						<img src="<?php echo PL_ADMIN_IMAGES . '/link-video.jpg';?>" class="docslink-video" alt="Video Tutorial" />
					</a>
				<?php endif;
				
				if( isset($o['docslink']) )
					printf('<a class="vidlink" title="%s" href="%s" target="_blank"><img src="%s" class="docslink-video" alt="Video Tutorial" /></a>', ($o['vidtitle'] ? $o['vidtitle'] : ''), $o['docslink'], PL_ADMIN_IMAGES . '/link-docs.jpg' ); 
				
				printf('<strong>%s</strong><br/><small>%s</small><br/>', $o['title'], $o['shortexp']);
				
				?>
			</div>
		</div>
		<?php endif;
	}
	
	function _do_the_option($oid, $o){
		
		$draw = (!isset( $o['version'] ) || ( isset($o['version']) && $o['version'] == 'free' && !VPRO) || (isset($o['version']) && $o['version'] == 'pro' && VPRO )) ? true : false;
		return $draw;
	}

	/**
	 * 
	 * Option Breaker 
	 * Switches through an option array, generating the option handling and markup
	 *
	 */
	function option_breaker($oid, $o, $setting = '', $val = ''){

		switch ( $o['type'] ){

			case 'select' :
				$this->_get_select_option($oid, $o);
				break;
			case 'select_same' :
				$this->_get_select_option($oid, $o);
				break;
			case 'radio' :
				$this->_get_radio_option($oid, $o);
				break;
			case 'colorpicker' :
				$this->_get_color_picker($oid, $o);
				break;
			case 'color_multi' :
				$this->_get_color_multi($oid, $o);
				break;
			case 'count_select' :
				$this->_get_count_select_option($oid, $o);
				break;
			case 'select_taxonomy' :
				$this->_get_taxonomy_select($oid, $o);
				break;
			case 'textarea' :
				$this->_get_textarea($oid, $o, $val);
				break;
			case 'textarea_big' :
				$this->_get_textarea($oid, $o, $val);
				break;
			case 'text' :
				$this->_get_text($oid, $o, $val);
				break;
			case 'text_small' :
				$this->_get_text_small($oid, $o, $val);
				break;
			case 'text_multi' :
				$this->_get_text_multi($oid, $o, $val);
				break;
			case 'check' :
				$this->_get_check_option($oid, $o);
				break;
			case 'check_multi' :
				$this->_get_check_multi($oid, $o, $val);
				break;
			case 'typography' :
				$this->_get_type_control($oid, $o);
				break;
			case 'select_menu' :
				$this->_get_menu_select($oid, $o);
				break;
			case 'image_upload' :
				$this->_get_image_upload_option($oid, $o);
				break;
			case 'background_image' :
				$this->_get_background_image_control($oid, $o); 
				break;
			case 'layout' :
				$this->_get_layout_builder($oid, $o);
				break;
			case 'layout_select' :
				$this->_get_layout_select($oid, $o); 
				break;
			case 'templates' :
				$this->do_template_builder($oid, $o); 
				break;
			case 'section_control' :
				$this->do_section_control($oid, $o); 
				break;
			case 'text_content' :
				$this->_get_text_content($oid, $o, $val);
				break;
			case 'reset' :
				$this->_get_reset_option($oid, $o, $val);
				break;
			case 'email_capture' :
				$this->_get_email_capture($oid, $o, $val);
				break;
			case 'horizontal_tabs' :
				$this->get_horizontal_nav($oid, $o);
				break;
			case 'graphic_selector' :
				$this->graphic_selector($oid, $o);
				break;
			case 'import_export' :
				$this->import_export($oid, $o);
				break;
			case 'updates_setup' :
				$this->updates_setup($oid, $o);
				break;
			default :
				do_action( 'pagelines_options_' . $o['type'] , $oid, $o);
				break;

		} 

	}



	/**
	 * 
	 * Gets a menu selector for WP menus. Can be used in navigation, etc...
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_menu_select($oid, $o){
		
		echo $this->input_label($o['input_id'], $o['inputlabel']);
		
		$menus = wp_get_nav_menus( array('orderby' => 'name') );
		$opts = '';
		foreach ( $menus as $menu ){
			$opts .= $this->input_option($menu->term_id, selected($menu->term_id, $o['val'], false), esc_html( $menu->name ) );
		}

		
		if($opts != '')
			echo $this->input_select($o['input_id'], $o['input_name'], $opts);
		else
			printf( __( '<div class="option_default_statement">WP menus need to be created to use this option!<br/> Edit <a href="%s">WordPress Menus</a></div>', 'pagelines' ), admin_url( 'nav-menus.php'));
			
	}

	/**
	 * 
	 * Gets Typography Control Panel
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_type_control($oid, $o){
		
		$control = new PageLinesTypeUI();
		
		$control->build_typography_control( $oid, $o );
	}


	/**
	 * 
	 * Standard Checkbox Option
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_check_option($oid, $o){ 
		
		$checked = checked((bool) $o['val'], true, false);
				
		$input = $this->input_checkbox($o['input_id'], $o['input_name'], $checked);
			
		echo $this->input_label_inline($o['input_id'], $input, $o['inputlabel']);
	}	
	
	

	/**
	 * 
	 * Multiple Checkbox Fields
	 * Shows several checkbox fields based on 'selectvalues' attr
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_check_multi($oid, $o, $val){ 
		
		foreach($o['selectvalues'] as $mid => $m):
		
			$value = checked((bool) $m['val'], true, false);
			
		
			// Output
			$input = $this->input_checkbox($m['input_id'], $m['input_name'], $value);
			
			echo $this->input_label_inline($m['input_id'], $input, $m['inputlabel']);

		endforeach; 
	}


	/**
	 * 
	 * Multiple Text Fields
	 * Shows several text fields based on 'selectvalues' attr
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_text_multi($oid, $o){ 
		
		foreach($o['selectvalues'] as $mid => $m){
			
			$attr = ( strpos( $mid, 'password' ) ) ? 'password' : 'text';
			
			$class = $o['inputsize'].'-text';
			
			// Output
			echo $this->input_label($m['input_id'], $m['inputlabel']);
			echo $this->input_text($m['input_id'], $m['input_name'], pl_html($m['val']), $class, $attr );

		}
	}


	/**
	 * 
	 * Small Text Option Field
	 * Displays Small Text Option & Escapes HTML
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_text_small($oid, $o, $val){ 
		
		echo $this->input_label($o['input_id'], $o['inputlabel']);
		echo $this->input_text($o['input_id'], $o['input_name'], pl_html($o['val']), 'small-text');
	}

	/**
	 * 
	 * Regular Text Field
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_text($oid, $o, $val){ 

		echo $this->input_label($o['input_id'], $o['inputlabel']);
		echo $this->input_text($o['input_id'], $o['input_name'], pl_html($o['val']));
		
	}



	/**
	 * 
	 * Regular Textarea
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_textarea($oid, $o, $val){ 
		
		$class = ($o['type']=='textarea_big') ? 'longtext' : '';
		
		// Output
		echo $this->input_label($o['input_id'], $o['inputlabel']);
		echo $this->input_textarea($o['input_id'], $o['input_name'], pl_html($o['val']), $class);
	}

	
	

	/**
	 * 
	 * Text or Written Content. E.g. Welcome Screen
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_text_content($oid, $o, $val){ 	
		printf('<div class="text_content fix">%s</div>', $o['exp']);
	}

	/**
	 * 
	 * Prints a button that can be used to reset an option
	 * Works with 'pagelines_process_reset_options()' & a callback in the option array
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_reset_option($oid, $o, $val){ 

		$confirmID = 'Confirm'.$oid;

		pl_action_confirm($confirmID, __( 'Are you sure?', 'pagelines' ) ); // print JS confirmation script
		
		
		$extra = sprintf('onClick="return %s();"', $confirmID);
		
		$input = $this->superlink($o['inputlabel'], 'grey', 'reset-options', 'submit', $extra, $o['input_name']);
		
		printf('<div class="insidebox context fix">%s %s</div>', $input, $o['exp']);


	}

	/**
	 * 
	 * Creates An AJAX Image Uploader
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_image_upload_option( $oid, $o ){ 

		$up_url = $this->input_text($o['input_id'], $o['input_name'], esc_url($o['val']), 'regular-text uploaded_url');
		
		$button_id = (isset($o['special'])) ? $oid.'OID'.$o['special'] : $oid;
		
		$up_button = $this->input_button( $button_id, __( 'Upload Image', 'pagelines' ), 'image_upload_button', 'title="'.$this->settings_field.'"' );
		
		$reset_button = sprintf('<span title="%1$s" id="%2$s" class="image_reset_button button reset_%1$s">Remove</span>', $button_id, $this->settings_field); 
		
		$ajax_url = $this->input_hidden('', 'wp_ajax_action_url', admin_url("admin-ajax.php"), 'ajax_action_url');
		$preview_size = $this->input_hidden('', 'img_size_'.$oid, $o['imagepreview'], 'image_preview_size'); 
		
		// Output
		$label = $this->input_label($oid, $o['inputlabel']);
		printf('<p>%s %s %s %s %s %s</p>',$label, $up_url, $up_button, $reset_button, $ajax_url, $preview_size);		
				
		if($o['val'])
			printf('<img class="pagelines_image_preview" id="image_%s" src="%s" style="max-width:%spx"/>', $button_id, $o['val'], $o['imagepreview']);
	}
	



	/**
	 * 
	 * Gets a select field based on a count parameter
	 * Starts at 0 or if a start value is given, starts there
	 * 
	 * @param count_start = starting value
	 * @param count_number = ending value
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_count_select_option( $oid, $o ){ 
		
		
		$count_start = (isset($o['count_start'])) ? $o['count_start'] : 0;
		
		$opts = '';
		for($i = $count_start; $i <= $o['count_number']; $i++)
			$opts .= $this->input_option($i, selected($i, $o['val'], false), $i);
		
		
		// Output
		echo $this->input_label($o['input_id'], $o['inputlabel']);
		echo $this->input_select($o['input_id'], $o['input_name'], $opts);
		
	}

	/**
	 * 
	 * Get Radio Options
	 * 
	 * @param selectvalues array a set of options to select from
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_radio_option( $oid, $o ){
	
		foreach($o['selectvalues'] as $sid => $s){
			
			$checked = checked($sid, $o['val'], false);
			
			$input = $this->input_radio($s['input_id'], $o['input_name'], $sid, $checked);
			echo $this->input_label_inline($s['input_id'], $input, $s['name']);
			
		}
	}


	/**
	 * 
	 * Get Select Option 
	 * 'select_same' means both value and name are the same
	 * 
	 * @param selectvalues array a set of options to select from
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_select_option( $oid, $o ){ 
		
		echo $this->input_label($o['input_id'], $o['inputlabel']);
		
		$opts = '';
		
		foreach($o['selectvalues'] as $sval => $s){
			
			$opts .= $this->input_option($sval, selected($sval, $o['val'], false), $s['name']);
	
		}
		
		echo $this->input_select($o['input_id'], $o['input_name'], $opts);

	}
	
	/**
	 * 
	 * Graphical Selector
	 * 
	 * @param selectvalues array a set of options to select from
	 * 
	 * @since 2.0.b3
	 * @author Andrew Powers
	 * 
	 **/
	function graphic_selector( $oid, $o ){ 
		
		
		?>
		<div id="graphic_selector_option" class="graphic_selector_wrap">
			<div class="graphic_selector fix">
				<div class="graphic_selector_pad fix">
					<label for="<?php echo $o['input_id'];?>" class="graphic_selector_overview"><?php echo $o['inputlabel'];?></label>
					
<?php 					foreach( $o['selectvalues'] as $sid => $s ): 
							$css = sprintf('background: url(%s) no-repeat %s; width: %s; height: %s;', $o['sprite'], $s['offset'], $o['width'], $o['height']);
					?>
					<span class="graphic_select_item">
						<span class="graphic_select_border <?php if($sid == $o['val']) echo 'selectedgraphic';?> fix">
							<span class="graphic_select_image <?php echo $sid;?>" style="<?php echo $css;?>">
								&nbsp;
							</span>
						</span>
						<?php 
						if($o['showname'] && isset($s['name']))
							printf('<span class="graphic_title clear">%s</span>', $s['name']);
							
						printf('<input type="radio" id="%s" class="graphic_select_input" name="%s" value="%s" %s />', $o['input_id'], $o['input_name'], $sid, checked($sid, $o['val'], false));
						
						?>
					</span>
					<?php endforeach;?>
					
					<?php if(isset($o['exp']) && $o['exp'] != ''):?>
					<div class="gselect_toggle" onclick="jQuery(this).parent().parent().next().slideToggle();">
						<div class="gselect_toggle_pad">More Info &darr;</div>
					</div>
					<?php endif;?>
				</div>
				
			</div>
			<?php if(isset($o['exp']) && $o['exp'] != ''):?>
			<div class="exp_gselect" style="display: none">
				<div class="exp_pad">
					<?php echo $o['exp'];?>
				</div>
			</div>
			<?php endif;?>
		</div><div class="clear"></div><?php }
	

	/**
	 * 
	 * Horizontal Navigation Option w/ Callbacks
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function get_horizontal_nav( $menu, $oids){ 
		$handle = 'htabs'.$menu; ?>
	<script type="text/javascript"> 
		jQuery(document).ready(function() {	
			var <?php echo $handle;?> = jQuery("#<?php echo $handle;?>").tabs({ cookie: { name: "<?php echo $menu;?>-tabs" } }); 
		});
	</script>
	<div id="<?php echo $handle;?>" class="htabs-menu" >	
		<ul class="tabbed-list horizontal-tabs fix">
			<?php foreach($oids['htabs'] as $key => $t){
					$class = (isset($t['class'])) ? $t['class'] : 'left';
					printf('<li class="ht-%s"><a href="#%s" >%s</a></li>', $class, $key,  ui_key($key) );
				}
			?>
		</ul>
		<?php 
		
	
		
		foreach($oids['htabs'] as $key => $t){
			
			$callback = ( isset($t['type']) && $t['type'] == 'subtabs' ) ? self::get_horizontal_subtabs( $key, $t ) : $t['callback'];
				
			printf('<div id="%s" class="htab-content"><div class="htab-content-pad"><h3 class="htab-title">%s</h3>%s</div></div>', $key, $t['title'], $callback);
			
		}
		
		
		
			?>
	</div>
	<?php }
	
	function get_horizontal_subtabs( $key, $t ){
		
		$handle = 'subtabs_'.$key;
		
		$thescript = self::get_tabs_script($handle, $key);
		
		$list_items = '';
		if(isset($t['type'])) unset($t['type']);
		if(isset($t['title'])) unset($t['title']);
		if(isset($t['class'])) unset($t['class']);
		
		$wlist = (1 / count($t)) * 100;
		foreach( $t as $skey => $st){
			
			$list_items .= sprintf('<li class="st-%s" style="width: %s%%"><a href="#%s" ><span class="st-pad">%s</span></a></li>', 'subtab', $wlist, $skey,  ui_key($skey) );
			
		}
		
		$thelist = sprintf('<ul class="tabbed-list horizontal-tabs subtabs fix">%s</ul>', $list_items);
		
		$stabs = '';
		foreach($t as $skey => $st){
			$stabs .= sprintf('<div id="%s" class="htab-sub"><div class="htab-content-pad"><h3 class="htab-title">%s</h3>%s</div></div>', $skey, $st['title'], $st['callback']);
		}
		
		$thewrapper = sprintf('<div id="%s" class="subtabs-menu" >%s %s</div>%s', $handle, $thelist, $stabs, $thescript);
		
		return $thewrapper;
	}
	
	function get_tabs_script($handle, $key){
	
		$thescript = sprintf('var %1$s = jQuery("#%1$s").tabs( { cookie: { name: "sub-tabs-%2$s" } } );', $handle, $key);
		
		$wrapper = sprintf('<script type="text/javascript">jQuery(document).ready(function() { %s });</script>', $thescript);
		
		return $wrapper;
	}

	/**
	 * 
	 * Get Taxonomy Selector
	 * Based on all applied to a post type
	 * 
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_taxonomy_select( $oid, $o ){ 
		
		$terms_array = get_terms( $o['taxonomy_id']); 

		if(is_array($terms_array) && !empty($terms_array)){
		
			echo $this->input_label($o['input_id'], $o['inputlabel']);
			
			$opts = '';
			foreach($terms_array as $term)
				$opts .= $this->input_option($term->slug, selected($term->slug, $o['val'], false), $term->name);
			
			echo $this->input_select($o['input_id'], $o['input_name'], $opts);
			
		} else
			printf('<div class="meta-message">%s</div>', __('No sets have been created and added to this post-type yet!', 'pagelines')); 

	}

	function _get_color_multi($oid, $o){ 	

		$num_options = count($o['selectvalues']);
		
		if($num_options == 4 || ($num_options % 4 == 0) )
			$per_row = 4;
		else 
			$per_row = 3;
		
		foreach($o['selectvalues'] as $mid => $m){

			$last = (end($o['selectvalues']) == $m) ? true : false;
				
			if( !isset($m['version']) || (isset($m['version']) && $m['version'] != 'pro') || (isset($m['version']) && $m['version'] == 'pro' && VPRO ))
				$this->_get_color_picker($mid, $m, $per_row, $last);

		}

	}


	function _get_color_picker($oid, $o, $per_row = 3, $last = false){ // Color Picker Template 
		
		$the_id = $o['input_id'];
		
 		$gen = do_color_math($the_id, $o, $o['val'], 'palette');
		
		$picker = sprintf('<div id="%s" class="colorSelector"><div></div></div> %s', $the_id.'_picker', $this->input_text($the_id, $o['input_name'], $o['val'], 'colorpickerclass'));
		
		$pick_contain = sprintf('<div class="pick_contain">%s</div>', $picker);
	
		printf('<div class="the_picker picker_row_%s %s"><div class="picker_panel"><div class="the_picker_pad">%s %s</div></div></div>', $per_row, ($last) ? 'p_end' : '', $this->input_label($the_id, $o['inputlabel']), $pick_contain);
		
		printf('<script type="text/javascript">setColorPicker("%s", "%s");</script>', $the_id, $o['val']);
  	}
	
	
	function _get_background_image_control($oid, $o){

		$bg = $this->_background_image_array();
		
		$oset = array( 'post_id' => $o['pid'], 'setting' => $this->settings_field);

		// set value, id, name
		foreach($bg as $k => $i){
			$bgid = $oid.$k;

			

			if($this->settings_field == 'meta'){
				$bg[$k]['val'] = plmeta($bgid, $oset);
				$bg[$k]['input_name'] = $bgid;
				$bg[$k]['input_id'] = get_pagelines_option_id( $bgid );
					
			} elseif($this->settings_field == PAGELINES_SPECIAL){

				$oset['subkey'] = $bgid;

				$bg[$k]['val'] = ploption( $o['special'], $oset );			
				$bg[$k]['input_name'] = plname($o['special'], $oset);
				$bg[$k]['input_id'] = plid( $o['special'], $oset);

			} else {
			
				$bg[$k]['val'] = ploption( $bgid, $oset);
				$bg[$k]['input_id'] = plid( $bgid, $oset);
				$bg[$k]['input_name'] = plname( $bgid, $oset);
			}
			
			$bg[$k] = wp_parse_args($bg[$k], $o);
			
		}
			
		

		$this->_get_image_upload_option($oid.'_url', $bg['_url']);
		$this->_get_select_option($oid.'_repeat', $bg['_repeat']);
		$this->_get_count_select_option( $oid.'_pos_vert', $bg['_pos_vert']);
		$this->_get_count_select_option( $oid.'_pos_hor', $bg['_pos_hor']);
		$this->_get_select_option($oid.'_attach', $bg['_attach']);

	}

		function _background_image_array(){
			return array(
				'_url' => array(		
						'inputlabel' 	=> __( 'Background Image', 'pagelines' ),
						'imagepreview'	=> 150
				),
				'_repeat' => array(			
						'inputlabel'	=> __( 'Set Background Image Repeat', 'pagelines' ),
						'type'			=> 'select',
						'selectvalues'	=> array(
							'no-repeat'	=> array('name' => __( 'Do Not Repeat', 'pagelines' )), 
							'repeat'	=> array('name' => __( 'Tile', 'pagelines' )), 
							'repeat-x'	=> array('name' => __( 'Repeat Horizontally', 'pagelines' )), 
							'repeat-y'	=> array('name' => __( 'Repeat Vertically', 'pagelines' ))
						)
				),
				'_pos_vert' => array(				
						'inputlabel'	=> __( 'Vertical Position In Percent', 'pagelines' ),
						'type'			=> __( 'count_select', 'pagelines' ),
						'count_start'	=> 0, 
						'count_number'	=> 100,
				),
				'_pos_hor' => array(				
						'inputlabel'	=> __( 'Horizontal Position In Percent', 'pagelines' ),
						'type'			=> __( 'count_select', 'pagelines' ),
						'count_start'	=> 0, 
						'count_number'	=> 100,
				),
				'_attach' => array(				
						'inputlabel'	=> __( 'Set Background Attachement', 'pagelines' ),
						'type'			=> 'select',
						'selectvalues'	=> array(
							'scroll'	=> array('name' => __( 'Scroll', 'pagelines' )), 
							'fixed'		=> array('name' => __( 'Fixed', 'pagelines' )),
						)
				),

			);
		}

	/**
	 *  Creates an email capture field that sends emails to PageLines.com
	 */
	function _get_email_capture($oid, $o){ ?>
		<div class="email_capture_container">
			<?php 
			echo $this->input_label($o['input_id'], $o['inputlabel']); 
			echo $this->input_text('email_capture_input', '', get_option('pagelines_email_sent'), 'email_capture');
			
			?>
			<input type="button" id="" class="button-secondary" onClick="sendEmailToMothership(jQuery('#email_capture_input').val(), '#email_capture_input');" value="Send" />
			<div class="the_email_response"></div>
		</div>
<?php }

	function updates_setup($oid, $o){
		
		if ( is_array( $a = get_transient('pagelines-update-' . THEMENAME ) ) && isset($a['package']) && $a['package'] !== 'bad' )
			$updates_exp = sprintf( __( 'Successfully logged in to PageLines%1$s.', 'pagelines' ), ( $a['ssl'] ) ? ' using SSL' : '' );
		else	
			if ( isset( $a ) && isset( $a['api_error'] ) ) 
				$updates_exp = sprintf( __( 'ERROR: %1$s<br />There was a problem logging in to PageLines.', 'pagelines' ), $a['api_error'] );
			else
				$updates_exp = __( 'Unknown error??', 'pagelines' );
		
		if ( isset( $a ) && isset( $a['licence'] ) )
			$updates_exp .= sprintf( __( '<br />We found a %s licence.', 'pagelines' ), $a['licence'] );

		if ( ploption( 'disable_updates' ) )
			$updates_exp = __( 'Updates are disabled.', 'pagelines' );

		if ( EXTEND_NETWORK )
			$updates_exp = __( 'Updates are disabled for non Network Admins</div>', 'pagelines' );
		?>
		<div class="pl_form">
			<div class="pl_form_feedback">
				<?php echo $updates_exp; ?>
			</div>
			
				<?php if ( EXTEND_NETWORK )
						return;
					?>			
			<form method="post" class="pl_account_info fix">
				<div class="pl_account_info_pad">
					
					<div class="pl_account_form">
						<div class="plform_title">
							<h2>PageLines Account Info</h2>
						</div>
						<input type="hidden" name="form_submitted" value="plinfo" />
				<?php 
			
				echo $this->input_label( 'lp_username', __( 'PageLines Username', 'pagelines' )); 
				echo $this->input_text( 'lp_username', 'lp_username', get_pagelines_option( 'lp_username' ), 'bigtext pluser');
				echo $this->input_label( 'lp_password', __( 'PageLines Password', 'pagelines' )); 
				echo $this->input_text( 'lp_password', 'lp_password', get_pagelines_option( 'lp_password' ), 'bigtext pluser', 'password');
		
				$checked = checked((bool) ploption('disable_updates'), true, false);

				$input = $this->input_checkbox('disable_auto_update', 'disable_auto_update', $checked);
				echo $this->input_label_inline('disable_auto_update', $input, __( 'Disable Auto Updates', 'pagelines' ));
	
				echo $this->superlink(__( 'Save Account Info', 'pagelines' ), 'blue', 'updates-setup', 'submit'); 
			
				?>
						</div>
					<div class="clear"></div>
				</div>
			</form>
		</div>
		
		
<?php }


	function import_export($oid, $o){
		
		?>
		<div class="pl_form">

			<form method="post" class="pl_account_info fix"> 
				<div class="pl_account_info_pad">
					<div class="pl_account_form fix">
				
						<div class="plform_title">
							<h2>PageLines Export Settings</h2>
						</div>
						<input type="hidden" name="form_submitted" value="export_settings_form" />
						<?php echo $this->superlink(__( 'Export Settings File &darr;', 'pagelines' ), 'blue', 'export_settings_form', 'submit'); ?>
						<div class="clear"></div>
					</div>
				</div>
			</form>
			<form method="post" enctype="multipart/form-data" class="pl_account_info fix">
				<div class="pl_account_info_pad">
	
					<div class="pl_account_form">
						<div class="plform_title">
							<h2>PageLines Import Settings</h2>
						</div>
						<input type="hidden" name="form_submitted" value="import_settings_form" />
				<?php 

						$input = $this->input_checkbox('pagelines_template', 'pagelines_template', 'checked');
						echo $this->input_label_inline('pagelines_template', $input, __( 'Import Template Setup', 'pagelines' ));

						$input = $this->input_checkbox('pagelines_settings', 'pagelines_settings', 'checked');
						echo $this->input_label_inline('pagelines_settings', $input, __( 'Import Primary Settings', 'pagelines' ));


						$input = $this->input_checkbox('pagelines_special', 'pagelines_special', 'checked');
						echo $this->input_label_inline('pagelines_special', $input, __( 'Import Special Meta Settings', 'pagelines' ));
				
						$input = $this->input_checkbox('pagelines_layout', 'pagelines_layout', 'checked');
						echo $this->input_label_inline('pagelines_layout', $input, __( 'Import Layout Configuration', 'pagelines' ));

						echo '<input type="file" class="file_uploader text_input" name="file" id="settings-file" /><div class="clear"></div>';

						echo $this->superlink( __( 'Import Settings To Install &uarr;', 'pagelines' ), 'blue', 'import_settings_form', 'submit' , 'onClick="return ConfirmImportSettings();"'); 
						pl_action_confirm('ConfirmImportSettings', __( 'Are you sure? This will overwrite your current settings and configurations with the information in this file!', 'pagelines' ));
				?>
			</form>
						</div>
					<div class="clear"></div>
				
			</div>	
		</div>
	<?php
	}

	/**
	 *  Layout Builder (Layout Drag & Drop)
	 */
	function _get_layout_builder($oid, $o){ 
		
		$builder = new PageLinesLayoutControl();
		$builder->draw_layout_control( $oid, $o );
	 }

	/**
	 *  Layout Select (Layout Selector)
	 */
	function _get_layout_select($oid, $o){
		
		$builder = new PageLinesLayoutControl();
		$builder->get_layout_selector($oid, $o);
	}

	/**
	 *  Template Drag and Drop (Sortable Sections)
	 */
	function do_template_builder($oid, $o){

		$builder = new PageLinesTemplateBuilder($oid, $o, $this->settings_field);
		$builder->draw_template_builder();

	}
	
	/**
	 *  Template Drag and Drop (Sortable Sections)
	 */
	function do_section_control($oid, $o){

		$builder = new PageLinesTemplateBuilder($oid, $o, $this->settings_field);
		$builder->section_control_interface($oid, $o);

	}
	
	

	
	
	/**
	 *  INPUT HELPERS
	 */

	function superlink($text, $mode = 'grey', $class = '', $type = '', $extra='', $name = ''){
		
		if(false !== strpos($type, 'http'))
			$att = 'a';
		else 
			$att = 'div';
		
		if ($type == 'submit')
			$button = sprintf('<input class="superlink supersave %s" type="submit" name="%s" value="%s" %s />', $class, $name, $text, $extra);
		else
			$button = sprintf('<%s id="%s" class="%s superlink" href="%s" %s ><span class="superlink-pad">%s</span></%s>', $att, $class, $class, $type, $extra, $text, $att);
		
		if($mode == 'purchase' || $mode == 'activate' || $mode == 'install')
			$color = 'blue';
		else 
			$color = $mode;
		
		$wrap = sprintf('<div class="superlink-%s-wrap superlink-wrap sl-%s">%s</div>', $class, $color, $button);
		
		return $wrap;
	}
	
	function input_hidden($id, $name, $value, $class = ''){
		return sprintf('<input type="hidden" id="%s" name="%s" value="%s" class="%s" />', $id, $name, $value, $class);
	}
	function input_textarea($id, $name, $value, $class = 'regular-text' ){
		return sprintf('<textarea id="%s" name="%s" class="html-textarea %s" />%s</textarea>', $id, $name, $class, $value );
	}
	
	function input_text($id, $name, $value, $class = 'regular-text', $attr = 'text', $extra = ''){
		return sprintf('<input type="%s" id="%s" name="%s" value="%s" class="%s" %s  />', $attr, $id, $name, $value, $class, $extra);
	}
	
	function input_checkbox($id, $name, $value, $class = 'admin_checkbox'){
		return sprintf('<input type="checkbox" id="%s" name="%s" class="%s" %s />', $id, $name, $class, $value);
	}
	
	function input_label_inline($id, $input, $text, $class = 'inln'){
		return sprintf('<label for="%s" class="lbl %s">%s <span>%s</span></label>', $id, $class, $input, $text);
	}
	
	function input_radio($id, $name, $value, $checked, $class = ''){
		return sprintf('<input type="radio" id="%s" name="%s" value="%s" class="%s" %s> ', $id, $name, $value, $class, $checked);
	}
	
	function input_label($id, $text, $class = 'context'){
		return sprintf('<label for="%s" class="lbl %s">%s</label>', $id, $class, $text);
	}
	
	function input_select($id, $name, $opts, $class = '', $extra = ''){
		return sprintf('<select id="%s" name="%s" class="%s" %s><option value="">&mdash;SELECT&mdash;</option>%s</select>', $id, $name, $class, $extra, $opts);
	}
	
	function input_option($value, $selected, $text, $id = '', $extra = ''){ 
		return sprintf('<option id=\'%s\' value="%s" %s %s >%s</option>', $id, $value, $extra, $selected, $text);
	}
		
	function input_button($id, $text, $class = '', $extra = ''){ 
		return sprintf('<span id=\'%s\' class="%s button" %s >%s</span>', $id, $class, $extra, $text);
	}	

} // End of Class