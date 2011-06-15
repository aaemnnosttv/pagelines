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

class PageLinesOptionEngine {

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
			'pro_note'				=> false
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
		$o['val'] = pagelines_option( $oid, $pid, $setting );
		$o['input_name'] = get_pagelines_option_name( $oid, null, null, $setting );
		$o['input_id'] = get_pagelines_option_id( $oid, null, null, $setting );		

	if( $this->_do_the_option() ):  ?>
	<div class="optionrow fix <?php echo $this->_layout_class( $o );?>">
		<?php $this->get_option_title( $oid, $o ); ?>
		
		<div class="oinputs">
			<div class="oinputs-pad">
				<?php $this->option_breaker($oid, $o); ?>
			</div>
		</div>

		<?php echo $this->_get_explanation($oid, $o);?>
			
		<div class="clear"></div>
	</div>
<?php endif; 
	}
	
	function _get_explanation($oid, $o){
		if($o['exp'] && $o['type'] != 'text_content' && $o['layout'] != 'interface'):?>
		<div class="oexp">
			<div class="oexp-effect">
				<div class="oexp-pad">
					<h5>More Info</h5>
					<p>
						<?php echo $o['exp'];?>
					</p>
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
				$this->_get_select_option($oid, $o, $val);
				break;
			case 'select_same' :
				$this->_get_select_option($oid, $o, $val);
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
				$this->_get_typography_option($oid, $o, $val);
				break;
			case 'select_menu' :
				$this->_get_menu_select($oid, $o);
				break;
			case 'image_upload' :
				$this->_get_image_upload_option($oid, $o, $val);
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
			case 'text_content' :
				$this->_get_text_content($oid, $o, $val);
				break;
			case 'reset' :
				$this->_get_reset_option($oid, $o, $val);
				break;
				
			case 'email_capture' :
				$this->_get_email_capture($oid, $o, $val);
				break;

			default :
				do_action( 'pagelines_options_' . $o['type'] , $oid, $o);
				break;

		} 

	}

	function _get_email_capture($oid, $o){
		 ?>
		<p>
			<div class="email_capture_container">
					<label for="<?php pagelines_option_id($oid); ?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
				<input type="text" id="email_capture_input" class="email_capture" value="<?php echo get_option('pagelines_email_sent');?>" />
				<input type="button" id="" class="button-secondary" onClick="sendEmailToMothership(jQuery('#email_capture_input').val(), '#email_capture_input');" value="Send" />
				<div class="the_email_response"></div>
			</div>
		</p>

	<?php }

	function _get_menu_select($oid, $o){ ?>
		<p>
			<label for="<?php pagelines_option_id($oid); ?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
			<select id="<?php pagelines_option_id($oid); ?>" name="<?php pagelines_option_name($oid); ?>">
				<option value="">&mdash;SELECT&mdash;</option>
				<?php	$menus = wp_get_nav_menus( array('orderby' => 'name') );
						foreach ( $menus as $menu )
							printf( '<option value="%d" %s>%s</option>', $menu->term_id, selected($menu->term_id, pagelines_option($oid)), esc_html( $menu->name ) );
				?>
			</select>
		</p>

	<?php }

	function _get_typography_option($oid, $o, $val){

		global $pl_foundry; 

		$fonts = $pl_foundry->foundry; 

		$preview_styles = '';

		$preview_styles = $pl_foundry->get_type_css(pagelines_option($oid));

		// Choose Font
		?>
		<label for="<?php pagelines_option_id($oid, 'font'); ?>" class="context">Select Font</label><br/>
		<select id="<?php pagelines_option_id($oid, 'font'); ?>" name="<?php pagelines_option_name($oid, 'font'); ?>" onChange="PageLinesStyleFont(this, 'font-family')" class="fontselector" size="1" >
			<option value="">&mdash;SELECT&mdash;</option>
			<?php foreach($fonts as $fid => $f):

				$free = (isset($f['free']) && $f['free']) ? true : false;

				if(!VPRO && !$free):

				else: 
					$font_name = $f['name']; 

					if($f['web_safe']) $font_name .= ' *';
					if($f['google']) $font_name .= ' G';

			?>
				<option value='<?php echo $fid;?>' id='<?php echo $f['family'];?>' title="<?php echo $pl_foundry->gfont_key($fid);?>" <?php selected( $fid, pagelines_sub_option($oid, 'font') ); ?>><?php echo $font_name;?></option>
			<?php endif; endforeach;?>
		</select>
		<div class="font_preview_wrap">
			<label class="context">Preview</label>
			<div class="font_preview" >
				<div class="font_preview_pad" style='<?php echo $preview_styles;?>' >
					The quick brown fox jumps over the lazy dog.
				</div>
			</div>
		</div>
		<span id="<?php pagelines_option_id($oid, '_set_styling_button'); ?>" class="button" onClick="PageLinesSimpleToggle('#<?php pagelines_option_id($oid, '_set_styling'); ?>', '#<?php pagelines_option_id($oid, '_set_advanced'); ?>')">Edit Font Styling</span>

		<span id="<?php pagelines_option_id($oid, '_set_advanced_button'); ?>" class="button" onClick="PageLinesSimpleToggle('#<?php pagelines_option_id($oid, '_set_advanced'); ?>', '#<?php pagelines_option_id($oid, '_set_styling'); ?>')">Advanced</span>

		<div id="<?php pagelines_option_id($oid, '_set_styling'); ?>" class="font_styling type_inputs">
			<?php $this->get_type_styles($oid, $o); ?>
			<div class="clear"></div>
		</div>

		<div id="<?php pagelines_option_id($oid, '_set_advanced'); ?>" class="advanced_type type_inputs">
			<?php $this->get_type_advanced($oid, $o); ?>
			<div class="clear"></div>
		</div>


	<?php }

	function get_type_styles($oid, $o){

		// Set Letter Spacing (em)
		$this->_get_type_em_select($oid, array());

		// Convert to caps, small-caps?
		$this->_get_type_select($oid, array('id' => 'transform', 'inputlabel' => 'Text Transform', 'prop' => 'text-transform',  'selectvalues' => array('none' => 'None', 'uppercase' => 'Uppercase', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase'), 'default' => 'none'));

		// Small Caps?
		$this->_get_type_select($oid, array('id' => 'variant', 'inputlabel' => 'Variant', 'prop' => 'font-variant',  'selectvalues' => array('normal' => 'Normal', 'small-caps' => 'Small-Caps'), 'default' => 'normal'));

		// Bold? 
		$this->_get_type_select($oid, array('id' => 'weight', 'inputlabel' => 'Weight', 'prop' => 'font-weight', 'selectvalues' => array('normal' => 'Normal', 'bold' => 'Bold'), 'default' => 'normal'));
		// 
		// Italic?
		$this->_get_type_select($oid, array('id' => 'style', 'inputlabel' => 'Style', 'prop' => 'font-style',  'selectvalues' => array('normal' => 'Normal', 'italic' => 'Italic'), 'default' => 'normal'));
	}

	function get_type_advanced($oid, $o){ ?>
		<div class="type_advanced">
			<label for="<?php pagelines_option_id($oid, 'selectors'); ?>" class="context">Additional Selectors</label><br/>
			<textarea class=""  name="<?php pagelines_option_name($oid, 'selectors'); ?>" id="<?php pagelines_option_id($oid, 'selectors'); ?>" rows="3"><?php esc_attr_e( pagelines_sub_option($oid, 'selectors'), 'pagelines' ); ?></textarea>
		</div>
	<?php }

	function _get_type_em_select($oid, $o){ 

		$option_value = ( pagelines_sub_option($oid, 'kern') ) ? pagelines_sub_option($oid, 'kern') : '0.00em';
		?>
		<div class="type_select">
		<label for="<?php pagelines_option_id($oid, 'kern'); ?>" class="context">Letter Spacing</label><br/>
		<select id="<?php pagelines_option_id($oid, 'kern'); ?>" name="<?php pagelines_option_name($oid, 'kern'); ?>" onChange="PageLinesStyleFont(this, 'letter-spacing')">
			<option value="">&mdash;SELECT&mdash;</option>
			<?php 
				$count_start = -.3;
				for($i = $count_start; $i <= 1; $i += 0.05):
					$em = number_format(round($i, 2), 2).'em';
			?>
					<option value="<?php echo $em;?>" <?php selected($em, $option_value); ?>><?php echo $em;?></option>
			<?php endfor;?>
		</select>
		</div>
	<?php }

	function _get_type_select($oid, $o){ 

		$option_value = ( pagelines_sub_option($oid, $o['id']) ) ? pagelines_sub_option($oid, $o['id']) : $o['default'];
		?>
		<div class="type_select">
			<label for="<?php pagelines_option_id($oid, $o['id']); ?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
			<select id="<?php pagelines_option_id($oid, $o['id']); ?>" name="<?php pagelines_option_name($oid, $o['id']); ?>" onChange="PageLinesStyleFont(this, '<?php echo $o['prop'];?>')">
				<option value="">&mdash;SELECT&mdash;</option>
				<?php foreach($o['selectvalues'] as $sid => $s):?>
						<option value="<?php echo $sid;?>" <?php selected($sid, $option_value); ?>><?php echo $s;?></option>
				<?php endforeach;?>
			</select>
		</div>
	<?php }	


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
				
		$input = sprintf('<input class="admin_checkbox" type="checkbox" id="%s" name="%s" %s />', $o['input_id'], $o['input_name'], $checked);
				
		printf('<label for="%s" class="context">%s %s</label>', $o['input_id'], $input, $o['inputlabel']);

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
		
			$id = get_pagelines_option_id( $mid );
			$name = get_pagelines_option_name($mid, null, null, $o['setting']);
			$value = checked((bool) pagelines_option($mid, $o['pid'], $o['setting']), true, false);
		
			// Output
			$input = sprintf('<input class="admin_checkbox" type="checkbox" id="%s" name="%s" %s />', $id, $name, $value);
			
			printf('<p><label for="%s" class="context">%s</label></p>', $mid, $m['inputlabel'], $input);

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
			
			$attr = ( strpos( $mid, 'password' ) ) ? 'type="password"' : 'type="text"';
			
			$id = get_pagelines_option_id( $mid );
			
			$name = get_pagelines_option_name($mid, null, null, $o['setting']);
			
			$value = pl_html( pagelines_option($mid, $o['pid'], $o['setting']) );
			
			
			// Output
			$input = sprintf('<input class="%s-text" %s id="%s" name="%s" value="%s"  />', $o['inputsize'], $attr, $mid, $name, $value);
			
			printf('<p><label for="%s" class="context">%s</label><br/>%s</p>', $mid, $m['inputlabel'], $input);
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
		
		printf('<label for="%s" class="context">%s</label><br/>', $o['input_id'], $o['inputlabel']);
		printf('<input class="small-text"  type="text" name="%s" id="%s" value="%s" />', $o['input_name'], $o['input_id'], pl_html($o['val']) );
	
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

		printf('<label for="%s" class="context">%s</label><br/>', $o['input_id'], $o['inputlabel']);
		printf('<input class="regular-text" type="text" name="%s" id="%s" value="%s" />', $o['input_name'], $o['input_id'], pl_html($o['val']) );
		
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
		
		printf('<label for="%s" class="context">%s</label><br/>', $o['input_id'], $o['inputlabel']);
		printf('<textarea class="html-textarea %s" type="text" name="%s" id="%s" />%s</textarea>', $class, $o['input_name'], $o['input_id'], pl_html($o['val']) );
	
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
		
		$input = sprintf('<input class="button-secondary reset-options" type="submit" name="%s" onClick="return %s();" value="%s" />', $o['input_name'], $confirmID, $o['inputlabel']);
		
		printf('<div class="insidebox context">%s %s</div>', $input, $o['exp']);


	}

	/**
	 * 
	 * Creates An AJAX Image Uploader
	 * 
	 * @since 1.0.0
	 * @author Andrew Powers
	 * 
	 **/
	function _get_image_upload_option( $oid, $o, $optionvalue = ''){ 

		?><p>	
			<label class="context" for="<?php echo $oid;?>"><?php echo $o['inputlabel'];?></label><br/>
			<input class="regular-text uploaded_url" type="text" name="<?php pagelines_option_name($oid); ?>" value="<?php echo esc_url(pagelines_option($oid));?>" /><br/><br/>
			<span id="<?php echo $oid; ?>" class="image_upload_button button">Upload Image</span>
			<?php printf('<span title="%1$s" id="reset_%1$s" class="image_reset_button button">Remove</span>', $oid); ?>
		</p>
		<?php
		
		
		printf('<input type="hidden" class="ajax_action_url" name="wp_ajax_action_url" value="%s" />', admin_url("admin-ajax.php"));
		printf('<input type="hidden" class="image_preview_size" name="img_size_%s" value="%s"/>', $oid, $o['imagepreview']);
	
		if($o['val'])
			printf('<img class="pagelines_image_preview" id="image_%s" src="%s" style="max-width:%spx"/>', $oid, $o['val'], $o['imagepreview']);
	}

	function _get_count_select_option( $oid, $o, $optionvalue = '' ){ ?>

			<p>
				<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
				<select id="<?php echo $oid;?>" name="<?php pagelines_option_name($oid); ?>">
					<option value="">&mdash;SELECT&mdash;</option>
					<?php if(isset($o['count_start'])): $count_start = $o['count_start']; else: $count_start = 0; endif;?>
					<?php for($i = $count_start; $i <= $o['count_number']; $i++):?>
							<option value="<?php echo $i;?>" <?php selected($i, pagelines_option($oid)); ?>><?php echo $i;?></option>
					<?php endfor;?>
				</select>
			</p>

	<?php }

	function _get_radio_option( $oid, $o ){ ?>

			<?php foreach($o['selectvalues'] as $selectid => $selecttext):?>
				<p>
					<input type="radio" id="<?php echo $oid;?>_<?php echo $selectid;?>" name="<?php pagelines_option_name($oid); ?>" value="<?php echo $selectid;?>" <?php checked($selectid, pagelines_option($oid)); ?>> 
					<label for="<?php echo $oid;?>_<?php echo $selectid;?>"><?php echo $selecttext;?></label>
				</p>
			<?php endforeach;?>

	<?php }

	function _get_select_option( $oid, $o, $val ){ ?>

			<p>
				<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
				<select id="<?php echo $oid;?>" name="<?php pagelines_option_name($oid); ?>">
					<option value="">&mdash;SELECT&mdash;</option>

					<?php foreach($o['selectvalues'] as $sval => $select_set):?>
						<?php if($o['type'] == 'select_same'):?>
								<option value="<?php echo $select_set;?>" <?php selected($select_set, pagelines_option($oid)); ?>><?php echo $select_set;?></option>
						<?php else:?>
								<option value="<?php echo $sval;?>" <?php selected($sval, pagelines_option($oid)); ?>><?php echo $select_set['name'];?></option>
						<?php endif;?>

					<?php endforeach;?>
				</select>
			</p>
	<?php }

	function _get_taxonomy_select( $oid, $o ){ 
		$terms_array = get_terms( $o['taxonomy_id']); 

		if(is_array($terms_array) && !empty($terms_array)):	?>
			<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
			<select id="<?php echo $oid;?>" name="<?php pagelines_option_name($oid); ?>">
				<option value="">&mdash;<?php _e("SELECT", 'pagelines');?>&mdash;</option>
				<?php foreach($terms_array as $term):?>
					<option value="<?php echo $term->slug;?>" <?php if( pagelines_option($oid) == $term->slug ) echo 'selected';?>><?php echo $term->name; ?></option>
				<?php endforeach;?>
			</select>
	<?php else:?>
			<div class="meta-message"><?php _e('No sets have been created and added to a post yet!', 'pagelines');?></div>
	<?php endif;

	}

	function _get_color_multi($oid, $o){ 	

		foreach($o['selectvalues'] as $mid => $m):

			if( !isset($m['version']) || (isset($m['version']) && $m['version'] != 'pro') || (isset($m['version']) && $m['version'] == 'pro' && VPRO )):
				$this->_get_color_picker($mid, $m);
			endif;

		endforeach; 

	}


	function _get_color_picker($oid, $o){ // Color Picker Template 
		?>

		<div class="the_picker">
			<label for="<?php echo $oid;?>" class="colorpicker_label context"><?php echo $o['inputlabel'];?></label>
			<div id="<?php echo $oid;?>_picker" class="colorSelector"><div></div></div>
			<input class="colorpickerclass"  type="text" name="<?php pagelines_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php echo pagelines_option($oid); ?>" />
		</div>
	<?php  }

	function _get_background_image_control($oid, $option_settings){

		$bg_fields = $this->_background_image_array();

		$this->_get_image_upload_option($oid.'_url', $bg_fields['_url'], pagelines_option($oid.'_url'));
		$this->_get_select_option($oid.'_repeat', $bg_fields['_repeat']);
		$this->_get_count_select_option( $oid.'_pos_vert', $bg_fields['_pos_vert']);
		$this->_get_count_select_option( $oid.'_pos_hor', $bg_fields['_pos_hor']);

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

} // End of Class