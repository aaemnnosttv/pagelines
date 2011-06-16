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
		
		echo $this->input_select($o['input_id'], $o['input_name'], $opts);
	
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
		
			$id = get_pagelines_option_id( $mid );
			$name = get_pagelines_option_name($mid, null, null, $o['setting']);
			$value = checked((bool) pagelines_option($mid, $o['pid'], $o['setting']), true, false);
		
			// Output
			$input = $this->input_checkbox($id, $name, $value);
			
			echo $this->input_label_inline($mid, $input, $m['inputlabel']);

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
			
			$id = get_pagelines_option_id( $mid );
			
			$name = get_pagelines_option_name($mid, null, null, $o['setting']);
			
			$value = pl_html( pagelines_option($mid, $o['pid'], $o['setting']) );
			
			$class = $o['inputsize'].'-text';
			
			// Output
			echo $this->input_label($mid, $m['inputlabel']);
			echo $this->input_text($mid, $name, $value, $class, $attr );

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
	function _get_image_upload_option( $oid, $o ){ 

		$up_url = sprintf('<input class="regular-text uploaded_url" type="text" name="%s" value="%s" /><br/>', $o['input_name'], esc_url($o['val'])); 
		$up_button =  sprintf('<span id="%s" class="image_upload_button button">Upload Image</span>', $oid); 
		$reset_button = sprintf('<span title="%1$s" id="reset_%1$s" class="image_reset_button button">Remove</span>', $oid); 
		$ajax_url = sprintf('<input type="hidden" class="ajax_action_url" name="wp_ajax_action_url" value="%s" />', admin_url("admin-ajax.php"));
		$preview_size = sprintf('<input type="hidden" class="image_preview_size" name="img_size_%s" value="%s"/>', $oid, $o['imagepreview']);
		
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
			$input = sprintf('<input type="radio" id="%1$s_%2$s" name="%3$s" value="%2$s" %4$s> ', $oid, $sid, $o['input_name'], $checked);
			printf('<p>%s<label for="%s_%s">%s</label></p>', $input, $oid, $sid, $s);
			
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
			<div id="<?php echo $oid;?>_picker" class="colorSelector"><div></div></div>
			<input class="colorpickerclass"  type="text" name="<?php pagelines_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php echo pagelines_option($oid); ?>" />
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
			<?php echo $this->input_label($o['input_id'], $o['inputlabel']); ?>
			<input type="text" id="email_capture_input" class="email_capture" value="<?php echo get_option('pagelines_email_sent');?>" />
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
	 *  INPUT HELPERS
	 */
	function input_text($id, $name, $value, $class = 'regular-text', $attr = 'text'){
		return sprintf('<input type="%s" id="%s" name="%s" value="%s" class="regular-text" />', $attr, $id, $name, $value, $class );
	}
	
	function input_checkbox($id, $name, $value, $class = 'admin_checkbox'){
		return sprintf('<input type="checkbox" id="%s" name="%s" class="%s" %s />', $id, $name, $class, $value);
	}
	
	function input_label_inline($id, $input, $text, $class = 'inln'){
		return sprintf('<label for="%s" class="lbl %s">%s %s</label>', $id, $class, $input, $text);
	}
	
	function input_label($id, $text, $class = 'context'){
		return sprintf('<label for="%s" class="lbl %s">%s</label>', $id, $class, $text);
	}
	
	function input_select($id, $name, $opts){
		return sprintf('<select id="%s" name="%s"><option value="">&mdash;SELECT&mdash;</option>%s</select>', $id, $name, $opts);
	}
	
	function input_option($value, $selected, $text){
		return sprintf('<option value="%s" %s>%s</option>', $value, $selected, $text);
	}

} // End of Class