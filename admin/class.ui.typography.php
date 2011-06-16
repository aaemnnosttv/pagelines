<?php
/**
 * 
 *
 *  Typography Control
 *
 *
 *  @package PageLines Admin
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesTypeUI {


	/**
	 * Construct
	 */
	function __construct() { }

	/**
	 *
	 *  Main Layout Drag and Drop
	 *
	 */
	function build_typography_control($oid, $o){ 

		global $pl_foundry; 

		$fonts = $pl_foundry->foundry; 

		$preview_styles = '';

		$preview_styles = $pl_foundry->get_type_css( pagelines_option($oid) );

		// Choose Font
		?>
		<label for="<?php pagelines_option_id($oid, 'font'); ?>" class="lbl context">Select Font</label><br/>
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


	<?php
	
	
	}

	function get_type_styles($oid, $o){

		// Set Letter Spacing (em)
		$this->_get_type_em_select($oid, array());

		// Convert to caps, small-caps?
		$this->_get_type_select($oid, array('id' => 'transform', 'inputlabel' => 'Text Transform', 'prop' => 'text-transform',  'selectvalues' => array('none' => 'None', 'uppercase' => 'Uppercase', 'capitalize' => 'Capitalize', 'lowercase' => 'lowercase'), 'default' => 'none'));

		// Small Caps?
		$this->_get_type_select($oid, array('id' => 'variant', 'inputlabel' => 'Variant', 'prop' => 'font-variant',  'selectvalues' => array('normal' => 'Normal', 'small-caps' => 'Small-Caps'), 'default' => 'normal'));

		// Bold? 
		$this->_get_type_select($oid, array('id' => 'weight', 'inputlabel' => 'Weight', 'prop' => 'font-weight', 'selectvalues' => array('normal' => 'Normal', 'bold' => 'Bold'), 'default' => 'normal'));

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

}