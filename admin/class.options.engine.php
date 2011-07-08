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

	function __construct( $settings_field = '' ) {
		
		$this->settings_field = $settings_field;
		
		$this->defaults = array(
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
			'showname'				=> false
		);
		
	}

	/**
	 * Option generation engine
	 *
	 */
	function option_engine($oid, $o, $pid = null, $setting = null){

		$o = wp_parse_args( $o, $this->defaults );

		$o['setting'] = (isset($setting)) ? $setting : PAGELINES_SETTINGS;
		$o['pid'] = $pid;
		
		if($this->settings_field == 'meta'){
			
			$o['val'] = m_pagelines($oid, $pid);
			$o['input_name'] = $oid;
			$o['input_id'] = get_pagelines_option_id( $oid );
			
		} else {
			$o['val'] = pagelines_option( $oid, $pid, $setting );
			$o['input_name'] = get_pagelines_option_name( $oid, null, null, $setting );
			$o['input_id'] = get_pagelines_option_id( $oid, null, null, $setting );		

			if(!empty($o['selectvalues'])){
				foreach($o['selectvalues'] as $sid => $s){

					$o['selectvalues'][$sid]['val'] = pagelines_option( $sid, $pid, $setting );
					$o['selectvalues'][$sid]['input_id'] = get_pagelines_option_id( $sid );
					$o['selectvalues'][$sid]['input_name'] = get_pagelines_option_name($sid, null, null, $setting);

				}
			}
		}
		
		
		
		if( $this->_do_the_option() ){
		
			printf('<div class="optionrow fix %s">', $this->_layout_class( $o ));
		
			$this->get_option_title( $oid, $o ); 
		
			printf('<div class="oinputs"><div class="oinputs-pad">');
	
			$this->option_breaker($oid, $o);
	
			printf('</div></div>');
		
			echo $this->_get_explanation($oid, $o);
		
			echo '<div class="clear"></div></div>';
		
		}
	}
	
	function _get_explanation($oid, $o){
		if($o['exp'] && $o['type'] != 'text_content' && $o['layout'] != 'interface'):?>
		<div class="oexp">
			<div class="oexp-effect">
				<div class="oexp-pad">
					<h5>More Info</h5>
					<p><?php echo $o['exp'];?></p>
					<?php 
						if( $o['pro_note'] && !VPRO )
							printf('<p class="pro_note"><strong>Pro Version Note:</strong><br/>%s</p>',  $o['pro_note']);
					 
					?>
				</div>
			</div>
		</div>
<?php endif;
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
					<a class="vidlink thickbox" title="<?php if($o['vidtitle']) echo $o['vidtitle']; ?>" href="<?php echo $o['vidlink']; ?>?hd=1&KeepThis=true&TB_iframe=true&height=450&width=700">
						<img src="<?php echo PL_ADMIN_IMAGES . '/link-video.jpg';?>" class="docslink-video" alt="Video Tutorial" />
					</a>
				<?php endif;?>

				<?php if( isset($o['docslink']) ):?>
					<a class="vidlink" title="<?php if($o['vidtitle']) echo $o['vidtitle']; ?>" href="<?php echo $o['docslink']; ?>" target="_blank">
						<img src="<?php echo PL_ADMIN_IMAGES . '/link-docs.jpg';?>" class="docslink-video" alt="Video Tutorial" />
					</a>
				<?php endif;?>

				<strong><?php echo $o['title'];?></strong><br/>
				<small><?php echo $o['shortexp'];?></small><br/>
			</div>
		</div>
		<?php endif;
	}
	
	function _do_the_option(){
		
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
			case 'css_option' :
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
				$this->do_template_builder(); 
				break;
			case 'section_control' :
				$this->do_section_control(); 
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
		foreach ( $menus as $menu )
			$opts = $this->input_option($menu->term_id, selected($menu->term_id, $o['val']), esc_html( $menu->name ) );
		
		if($opts != '')
			echo $this->input_select($o['input_id'], $o['input_name'], $opts);
		else
			printf('<div class="option_default_statement">WP menus need to be created to use this option!<br/> Edit <a href="%s">WordPress Menus</a></div>', admin_url( 'nav-menus.php'));
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
	function _get_text_multi($oid, $o, $val){ 
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

		pl_action_confirm($confirmID, 'Are you sure?'); // print JS confirmation script
		
		
		$extra = sprintf('onClick="return %s();"', $confirmID);
		
		$input = $this->superlink($o['inputlabel'], 'grey', 'reset-options', 'submit', $extra, $o['input_name']);
		
		//$input = sprintf('<input class="button-secondary reset-options" type="submit" name="%s" onClick="return %s();" value="%s" />', $o['input_name'], $confirmID, $o['inputlabel']);
		
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

		
		$up_button = $this->input_button($oid, 'Upload Image', 'image_upload_button');
		
		$reset_button = sprintf('<span title="%1$s" id="reset_%1$s" class="image_reset_button button">Remove</span>', $oid); 
		
		$ajax_url = $this->input_hidden('', 'wp_ajax_action_url', admin_url("admin-ajax.php"), 'ajax_action_url');
		$preview_size = $this->input_hidden('', 'img_size_'.$oid, $o['imagepreview'], 'image_preview_size'); 
		
		// Output
		$label = $this->input_label($oid, $o['inputlabel']);
		printf('<p>%s %s %s %s %s %s</p>',$label, $up_url, $up_button, $reset_button, $ajax_url, $preview_size);		
				
		if($o['val'])
			printf('<img class="pagelines_image_preview" id="image_%s" src="%s" style="max-width:%spx"/>', $oid, $o['val'], $o['imagepreview']);
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
			
			if($o['type'] == 'select_same')
				$opts .= $this->input_option($s, selected($s, $o['val'], false), $s);
			else
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
						<?php if($o['showname'] && isset($s['name'])): ?>
						<span class="graphic_title clear">
							<?php echo $s['name'];?>
						</span>
						<?php endif; ?>
						<input type="radio" id="<?php echo $o['input_id'];?>" class="graphic_select_input" name="<?php echo $o['input_name']; ?>" value="<?php echo $sid;?>" <?php checked($sid, $o['val']); ?>>
					</span>
					<?php endforeach;?>
					
					<?php if(isset($o['exp']) && $o['exp'] != ''):?>
					<div class="gselect_toggle" onclick="jQuery('.exp_gselect').slideToggle();">
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
		</div>
		
		<div class="clear"></div>
	<?php }
	

	/**
	 * 
	 * Horizontal Navigation Option w/ Callbacks
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function get_horizontal_nav( $menu, $oids){ 
		
		$handle = 'htabs'.$menu;
		?>
	<script type="text/javascript"> 
		jQuery(document).ready(function() {	
			var <?php echo $handle;?> = jQuery("#<?php echo $handle;?>").tabs({ 
				fx: { opacity: "toggle", duration: "fast" }
			}); 
		});
	</script>
	<div id="<?php echo $handle;?>">	
		<ul class="tabbed-list horizontal-tabs fix">
			<?php foreach($oids['htabs'] as $key => $t){
					$class = (isset($t['class'])) ? $t['class'] : 'left';
					printf('<li class="ht-%s"><a href="#%s" >%s</a></li>', $class, $key,  ucfirst($key));
				}
			?>
		</ul>
		<?php foreach($oids['htabs'] as $key => $t)
				printf('<div id="%s" class="htab-content"><div class="htab-content-pad"><h3 class="htab-title">%s</h3>%s</div></div>', $key, $t['title'], $t['callback']);
			?>

	</div>
	<?php }

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

		foreach($o['selectvalues'] as $mid => $m){

			if( !isset($m['version']) || (isset($m['version']) && $m['version'] != 'pro') || (isset($m['version']) && $m['version'] == 'pro' && VPRO ))
				$this->_get_color_picker($mid, $m);

		}

	}


	function _get_color_picker($oid, $o){ // Color Picker Template 
		?>

		<div class="the_picker">
			<?php echo $this->input_label($oid, $o['inputlabel']); ?>
			<div id="<?php echo $oid;?>_picker" class="colorSelector">
				<div></div>
			</div>
			<?php echo $this->input_text($oid, get_pagelines_option_name($oid), get_pagelines_option($oid), 'colorpickerclass'); ?>
			
		</div>
	<?php  }
	
	
	function _get_background_image_control($oid, $o){

		$bg = $this->_background_image_array();

		// set value, id, name
		foreach($bg as $k => $i){
			$bg[$k]['val'] = pagelines_option($oid.$k, $o['pid'], $o['setting']);
			$bg[$k]['input_id'] = get_pagelines_option_id( $oid, $k );
			$bg[$k]['input_name'] = get_pagelines_option_name($oid.$k, null, null, $o['setting']);
		}
		
		$this->_get_image_upload_option($oid.'_url', $bg['_url']);
		$this->_get_select_option($oid.'_repeat', $bg['_repeat']);
		$this->_get_count_select_option( $oid.'_pos_vert', $bg['_pos_vert']);
		$this->_get_count_select_option( $oid.'_pos_hor', $bg['_pos_hor']);

	}


	function _background_image_array(){
		return array(
			'_url' => array(		
					'inputlabel' 	=> 'Background Image',
					'imagepreview'	=> 150
			),
			'_repeat' => array(			
					'inputlabel'	=> 'Set Background Image Repeat',
					'type'			=> 'select',
					'selectvalues'	=> array(
						'no-repeat'	=> array('name' => 'Do Not Repeat'), 
						'repeat'	=> array('name' => 'Tile'), 
						'repeat-x'	=> array('name' => 'Repeat Horizontally'), 
						'repeat-y'	=> array('name' => 'Repeat Vertically')
					)
			),
			'_pos_vert' => array(				
					'inputlabel'	=> 'Vertical Position In Percent',
					'type'			=> 'count_select',
					'count_start'	=> 0, 
					'count_number'	=> 100,
			),
			'_pos_hor' => array(				
					'inputlabel'	=> 'Horizontal Position In Percent',
					'type'			=> 'count_select',
					'count_start'	=> 0, 
					'count_number'	=> 100,
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
	function do_template_builder(){

		$builder = new PageLinesTemplateBuilder();
		$builder->draw_template_builder();

	}
	
	/**
	 *  Template Drag and Drop (Sortable Sections)
	 */
	function do_section_control(){

		$builder = new PageLinesTemplateBuilder();
		$builder->section_control_interface();

	}
	
	
	/**
	 *  CSS Rendering In <head>
	 */
	function render_css(){
		$css = '';
		
		foreach (get_option_array() as $menu){

			foreach($menu as $oid => $o){ 
				
				if($o['type'] == 'css_option' && pagelines_option($oid)){
					
					if(pagelines_option($oid) == $o['default']){
						// do nothing
					} elseif(isset($o['css_prop']) && isset($o['selectors'])){
						
						$css_units = (isset($o['css_units'])) ? $o['css_units'] : '';
						
						$css .= $o['selectors'].'{'.$o['css_prop'].':'.pagelines_option($oid).$css_units.';}';
					}

				}
				
				if( $o['type'] == 'background_image' && pagelines_option($oid.'_url')){
					
					$bg_repeat = (pagelines_option($oid.'_repeat')) ? pagelines_option($oid.'_repeat'): 'no-repeat';
					$bg_pos_vert = (pagelines_option($oid.'_pos_vert') || pagelines_option($oid.'_pos_vert') == 0 ) ? (int) pagelines_option($oid.'_pos_vert') : '0';
					$bg_pos_hor = (pagelines_option($oid.'_pos_hor') || pagelines_option($oid.'_pos_hor') == 0 ) ? (int) pagelines_option($oid.'_pos_hor') : '50';
					$bg_selector = (pagelines_option($oid.'_selector')) ? pagelines_option($oid.'_selector') : $o['selectors'];
					$bg_url = pagelines_option($oid.'_url');
					
					$css .= sprintf('%s{ background-image:url(%s);}', $bg_selector, $bg_url);
					$css .= sprintf('%s{ background-repeat: %s;}', $bg_selector, $bg_repeat);
					$css .= sprintf('%s{ background-position: %s%% %s%%;}', $bg_selector, $bg_pos_hor, $bg_pos_vert);
					
					
				}
	
				
				if($o['type'] == 'colorpicker')
					$css .= $this->render_css_colors($oid, $o['selectors'], $o['css_prop']);

				
				elseif($o['type'] == 'color_multi'){
					
					foreach($o['selectvalues'] as $mid => $m){
						
						$selectors = (isset($m['selectors'])) ? $m['selectors'] : null ;
						$property = (isset($m['css_prop'])) ? $m['css_prop'] : null ;
						
						$css .= $this->render_css_colors($mid, $m, $selectors, $property);
					}
					
				}
			} 
		}
		return $css;

	}
	
	function render_css_colors( $oid, $o, $selectors = null, $css_prop = null ){
		if( pagelines_option($oid)){
			$css = '';
			if( isset($o['default']) && pagelines_option($oid) == $o['default']){
				// do nothing
			}elseif(isset($css_prop)){
			
				if(is_array($css_prop)){
				
					foreach( $css_prop as $css_property => $css_selectors ){

						if($css_property == 'text-shadow')
							$css .= $css_selectors . '{ text-shadow:'.pagelines_option($oid).' 0 1px 0;}';		
						elseif($css_property == 'text-shadow-top')
							$css .= $css_selectors . '{ text-shadow:'.pagelines_option($oid).' 0 -1px 0;}';		
						else
							$css .= $css_selectors . '{'.$css_property.':'.pagelines_option($oid).';}';		
						
					}
				
				}else
					$css .= $selectors.'{'.$css_prop.':'.pagelines_option($oid).';}';
				
			
			} else
				$css .= $selectors.'{color:'.pagelines_option($oid).';}';
			
			
			return $css;
		} else 
			return '';
	}

	
	
	/**
	 *  INPUT HELPERS
	 */

	function superlink($text, $color = 'grey', $class = '', $type = '', $extra='', $name = ''){
		
		if(false !== strpos($type, 'http'))
			$att = 'a';
		else 
			$att = 'div';
		
		if ($type == 'submit')
			$button = sprintf('<input class="superlink supersave %s" type="submit" name="%s" value="%s" %s />', $class, $name, $text, $extra);
		else
			$button = sprintf('<%s id="%s" class="%s superlink" href="%s" %s ><span class="superlink-pad">%s</span></%s>', $att, $class, $class, $type, $extra, $text, $att);
		
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
		return sprintf('<input type="%s" id="%s" name="%s" value="%s" class="%s" %s />', $attr, $id, $name, $value, $class, $extra);
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
		return sprintf('<option id=\'%s\' value="%s" %s %s>%s</option>', $id, $value, $extra, $selected, $text);
	}
		
	function input_button($id, $text, $class = '', $extra = ''){ 
		return sprintf('<span id=\'%s\' class="%s button" %s >%s</span>', $id, $class, $extra, $text);
	}	

} // End of Class