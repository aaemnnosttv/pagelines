<?php 
/**
 * 
 *
 *  Options Layout Class
 *
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */

class PageLinesOptionsUI {

/*
	Build The Layout
*/
	function __construct() {
		$this->option_array = get_option_array();
		
		$this->build_header();
		$this->build_body();
		$this->build_footer();	
		
	}
		
/**
 * Option Interface Header
 *
 */
function build_header(){?>
			<div class='wrap'>
				<table id="optionstable"><tbody><tr><td valign="top" width="100%">
					
				  <form id="pagelines-settings-form" method="post" action="options.php" class="main_settings_form">
					
						 <!-- hidden fields -->
							<?php wp_nonce_field('update-options') ?>
							<?php settings_fields(PAGELINES_SETTINGS); // important! ?>
							
							<input type="hidden" name="<?php echo PAGELINES_SETTINGS; ?>[theme_version]>" value="<?php echo esc_attr(pagelines_option('theme_version')); ?>" />
							<input type="hidden" name="<?php echo PAGELINES_SETTINGS; ?>[selectedtab]" id="selectedtab" value="<?php print_pagelines_option('selectedtab', 0); ?>" />
							<input type="hidden" name="<?php echo PAGELINES_SETTINGS; ?>[just_saved]" id="just_saved" value="1" />
							<input type="hidden" id="input-full-submit" name="input-full-submit" value="0" />
							
							<?php $this->_get_confirmations_and_system_checking(); ?>
							

					<?php
					
						if(isset($_COOKIE['PageLinesTabCookie']))
							$selected_tab = (int) $_COOKIE['PageLinesTabCookie'];
						elseif(pagelines_option('selectedtab'))
							$selected_tab = pagelines_option('selectedtab');
						else
							$selected_tab = 0;
				
					?>
				
						<script type="text/javascript">
								jQuery.noConflict();
								jQuery(document).ready(function($) {						
									var $myTabs = $("#tabs").tabs({ fx: { opacity: "toggle", duration: "fast" }, selected: <?php echo $selected_tab; ?>});
									
									$('#tabs').bind('tabsshow', function(event, ui) {
										
										var selectedTab = $('#tabs').tabs('option', 'selected');
										
										$("#selectedtab").val(selectedTab);
										
										$.cookie('PageLinesTabCookie', selectedTab);
										
									});

								});
						</script>
								
								<div class="clear"></div>
								<div id="optionsheader" class="fix">
									<div class="ohead-top" class="fix">
										<div class="ohead-top-pad">
											<div class="ohead-title">
												<?php _e('PageLines Settings', 'pagelines');?> 
											</div>
											<div class="ohead-title-right">
												<div class="osave-wrap"><input class="osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" /></div>
											
												
											</div>
										</div>
										
									
									</div>
										<!-- <div class="superlinks ">
																				
																				<a class="superlink slpreview" href="<?php echo home_url(); ?>/" target="_blank" target-position="front">
																					<img src="<?php echo PL_ADMIN_ICONS;?>/discussion.png" />
																				</a>
																				<a class="superlink sldocs" href="http://www.pagelines.com/docs/" target="_blank" >
																					<img src="<?php echo PL_ADMIN_ICONS;?>/discussion.png" />
																				</a>
																				<a class="superlink slforum" href="http://www.pagelines.com/forum/" target="_blank" >
																					<img src="<?php echo PL_ADMIN_ICONS;?>/discussion.png" />
																				</a>
																				
																			</div> -->
								</div>
		<?php }
		
		function _get_confirmations_and_system_checking(){
			
				// Load Ajax confirmation
				printf('<div class="ajax-saved" style=""><div class="ajax-saved-pad"><div class="ajax-saved-icon"></div></div></div>');
			
				// get confirmations
				pagelines_draw_confirms();
				
				// Get server error messages
				pagelines_error_messages();

		}
		
		/**
		 * Option Interface Body, including vertical tabbed nav
		 *
		 */
		function build_body(){
			global $pl_section_factory; 
?>
			<div id="tabs">	
				<ul id="tabsnav">
					<li><span class="graphic top">&nbsp;</span></li>
				
					<?php foreach($this->option_array as $menu => $oids):?>
						
						<li>
							<a class="<?php echo $menu;?>  tabnav-element" href="#<?php echo $menu;?>">
								<span><?php echo ucwords( str_replace('_',' ',$menu) );?></span>
							</a>
						</li>
					
					<?php endforeach;?>

					<li><span class="graphic bottom">&nbsp;</span></li>
					
					<div class="framework_loading"> 
						<a href="http://www.pagelines.com/forum/topic.php?id=6489#post-34852" target="_blank" title="Javascript Issue Detector"><span class="framework_loading_gif" >&nbsp;</span></a>
						
					</div>
				</ul>
				<div id="thetabs" class="fix">
					
					<?php if(!VPRO):?>
						<div id="vpro_billboard" class="">
							<div class="vpro_billboard_height">
								<a class="vpro_thumb" href="<?php echo PROVERSIONOVERVIEW;?>"><img src="<?php echo PL_IMAGES;?>/pro-thumb-125x50.png" alt="<?php echo PROVERSION;?>" /></a>
								<div class="vpro_desc">
									<strong style="font-size: 1.2em">Get the Pro Version </strong><br/>
									<?php echo THEMENAME;?> is the free version of <?php echo PROVERSION;?>, a premium product by <a href="http://www.pagelines.com" target="_blank">PageLines</a>.<br/> 
									Buy <?php echo PROVERSION;?> for tons more options, sections and templates.<br/> 	
								
									<a class="vpro_link" href="#" onClick="jQuery(this).parent().parent().parent().find('.whatsmissing').slideToggle();">Pro Features &darr;</a>
									<a class="vpro_link" href="<?php echo PROVERSIONOVERVIEW;?>">Why Pro?</a>
									<a class="vpro_link"  href="<?php echo PROVERSIONDEMO;?>"><?php echo PROVERSION;?> Demo</a>
									<?php if(defined('PROBUY')):?><a class="vpro_link vpro_call"  href="<?php echo PROBUY;?>"><strong>Buy Now &rarr;</strong></a><?php endif;?>
								
								</div>
							
							</div>
							<div class="whatsmissing">
								 <h3>Pro Only Features</h3>
								<?php if(isset($pl_section_factory->unavailable_sections) && is_array($pl_section_factory->unavailable_sections)):?>
									<p class="mod"><strong>Pro Sections</strong> (drag &amp; drop)<br/>
									<?php foreach( $pl_section_factory->unavailable_sections as $unavailable_section ):?>
										<?php echo $unavailable_section->name;if($unavailable_section !== end($pl_section_factory->unavailable_sections)) echo ' &middot; ';?>
									<?php endforeach;?></p>
								<?php endif;?>
								
								<?php 
								$unavailable_section_areas = get_unavailable_section_areas();
								if(isset($unavailable_section_areas) && is_array($unavailable_section_areas)):?>
									<p class="mod"><strong>Pro Templates &amp; Section Areas</strong> (i.e. places to put sections)<br/>
									<?php foreach( $unavailable_section_areas as $unavailable_section_area_name ):?>
										<?php echo $unavailable_section_area_name; if($unavailable_section_area_name !== end($unavailable_section_areas)) echo ' &middot; ';?> 
									<?php endforeach;?></p>
								<?php endif;?>
								
								<p class="mod"><strong>Pro Settings &amp; Options</strong><br/>
								<?php foreach( get_option_array(true) as $optionset ):
										foreach ( $optionset as $oid => $o): 
											if( isset($o['version']) && $o['version'] == 'pro' ):
												echo $o['title']; echo ' &middot; ';
											endif;
										endforeach; 
									endforeach;?></p>
								
								<p class="mod"><strong>Plus additional meta options, integrated plugins, technical support, and more...</strong></p>
							
							</div>
						</div>
					<?php endif;?>
					<?php foreach($this->option_array as $menu => $oids):?>
						
							<div id="<?php echo $menu;?>" class="tabinfo">
							
								<?php if( stripos($menu, '_') !== 0 ): ?>
									<div class="tabtitle"><?php echo ucwords(str_replace('_',' ',$menu));?></div>
								<?php endif;?>
							
								<?php foreach($oids as $oid => $o){
									$this->option_engine($oid, $o);
								} ?>
								<div class="clear"></div>
							</div>
						
					<?php endforeach; ?>	
				</div> <!-- End the tabs -->
			</div> <!-- End tabs -->
<?php 	}
		
/**
 * Option generation engine
 *
 */
function option_engine($oid, $o){
	
	$defaults = array(
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

	$o = wp_parse_args( $o, $defaults );

	if($o['wp_option']) 
		$val = get_option($oid);
	else 
		$val = pagelines_option($oid);

if( !isset( $o['version'] ) || ( isset($o['version']) && $o['version'] == 'free') || (isset($o['version']) && $o['version'] == 'pro' && VPRO ) ): 
?>
<div class="optionrow fix <?php if( isset( $o['layout'] ) && $o['layout']=='full' ) echo 'wideinputs'; if( $o['type'] == 'options_info' ) echo ' options_info_row';?>">
		<?php if( $o['title'] ): ?>
		<div class="optiontitle fix">
			<?php if( $o['optionicon'] ) echo '<img src="'.$o['optionicon'].'" class="optionicon" />'; ?>
			
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
		<?php endif;?>
		<div class="theinputs ">
			<div class="optioninputs">
				<?php $this->option_breaker($oid, $o, $val); ?>
			</div>
		</div>

		<?php if($o['exp'] && $o['type'] != 'text_content' && $o['type'] != 'options_info'):?>
		<div class="theexplanation">
			<div class="context">More Info</div>
			<p><?php echo $o['exp'];?></p>
			<?php if( $o['pro_note'] && !VPRO ): ?>
				<p class="pro_note"><strong>Pro Version Note:</strong><br/><?php echo $o['pro_note']; ?></p>
			<?php endif; ?>
		</div>
		<?php endif;?>
<div class="clear"></div>
</div>
<?php endif; 
}
		
function option_breaker($oid, $o, $val = ''){
	
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
				$this->_get_template_builder(); 
				break;
			case 'text_content' :
				$this->_get_text_content($oid, $o, $val);
				break;
			case 'options_info' :
				$this->_get_options_info($oid, $o, $val);
				break;
			case 'reset' :
				$this->_get_reset_option($oid, $o, $val);
				break;
		
			default :
				do_action( 'pagelines_options_' . $o['type'] , $oid, $o);
				break;

		} 
	
}

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
		
			if(!VPRO && !$f['free']):
				
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
		
function _get_check_option($oid, $o){ ?>
	<p>
		<label for="<?php pagelines_option_id($oid); ?>" class="context">
			<input class="admin_checkbox" type="checkbox" id="<?php pagelines_option_id($oid); ?>" name="<?php pagelines_option_name($oid); ?>" <?php checked((bool) pagelines_option($oid)); ?> />
			<?php echo $o['inputlabel'];?>
		</label>
	</p>
<?php }	

function _get_check_multi($oid, $o, $val){ 
	foreach($o['selectvalues'] as $mid => $mo):?>
	<p>
		<label for="<?php echo $mid;?>" class="context"><input class="admin_checkbox" type="checkbox" id="<?php echo $mid;?>" name="<?php pagelines_option_name($mid); ?>" <?php checked((bool) pagelines_option($mid)); ?>  /><?php echo $mo['inputlabel'];?></label>
	</p>
<?php endforeach; 
}

function _get_text_multi($oid, $o, $val){ 
	foreach($o['selectvalues'] as $mid => $m):?>
	<p>
		<label for="<?php echo $mid;?>" class="context"><?php echo $m['inputlabel'];?></label><br/>
		<input class="<?php echo $o['inputsize'];?>-text" <?php echo ( strpos( $mid, 'password' ) ) ? 'type="password"' : 'type="text"'; ?> id="<?php echo $mid;?>" name="<?php pagelines_option_name($mid); ?>" value="<?php echo esc_attr( pagelines_option($mid) ); ?>"  />
	</p>
	<?php endforeach;
}

function _get_text_small($oid, $o, $val){ ?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<input class="small-text"  type="text" name="<?php pagelines_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php pl_ehtml( pagelines_option($oid) ); ?>" />
	</p>
<?php }

function _get_text($oid, $o, $val){ 
	
	global $pl_data;
	
	?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label>
		<input class="regular-text"  type="text" name="<?php pagelines_option_name($oid); ?>" id="<?php echo $oid;?>" value="<?php pl_ehtml( pagelines_option($oid) ); ?>" />
	</p>
<?php }

function _get_textarea($oid, $o, $val){ ?>
	<p>
		<label for="<?php echo $oid;?>" class="context"><?php echo $o['inputlabel'];?></label><br/>
		<textarea name="<?php pagelines_option_name($oid); ?>" class="html-textarea <?php if($o['type']=='textarea_big') echo "longtext";?>" cols="70%" rows="5"><?php pl_ehtml( pagelines_option($oid) ); ?></textarea>
	</p>
<?php }


function _get_text_content($oid, $o, $val){ ?>
	<div class="text_content fix"><?php echo $o['exp'];?></div>
<?php }

function _get_reset_option($oid, $o, $val){ 
	
	pl_action_confirm('Confirm'.$oid, 'Are you sure?');
	
?>
	<div class="insidebox context">
		<input class="button-secondary reset-options" type="submit" name="<?php pagelines_option_name($oid); ?>" onClick="return Confirm<?php echo $oid;?>();" value="<?php echo $o['inputlabel'];?>" /> <?php echo $o['exp'];?>
	</div>
<?php 

}

function _get_options_info($oid, $o, $val){ ?>
	<span class="toggle_option_info" onClick="jQuery(this).next().slideToggle();">Additional <?php echo ucwords(str_replace('_', ' ', $oid));?> Info &darr;</span>
	<div class="text_content admin_option_info fix">
		<h3>More Information on <?php echo ucwords(str_replace('_', ' ', $oid));?></h3>
		<?php echo $o['exp'];?>
	</div>
<?php }
		

function _get_image_upload_option( $oid, $o, $optionvalue = ''){ 

	?><p>	
		<label class="context" for="<?php echo $oid;?>"><?php echo $o['inputlabel'];?></label><br/>
		<input class="regular-text uploaded_url" type="text" name="<?php pagelines_option_name($oid); ?>" value="<?php echo esc_url(pagelines_option($oid));?>" /><br/><br/>
		<span id="<?php echo $oid; ?>" class="image_upload_button button">Upload Image</span>
		<span title="<?php echo $oid;?>" id="reset_<?php echo $oid; ?>" class="image_reset_button button">Remove</span>
		<input type="hidden" class="ajax_action_url" name="wp_ajax_action_url" value="<?php echo admin_url("admin-ajax.php"); ?>" />
		<input type="hidden" class="image_preview_size" name="img_size_<?php echo $oid;?>" value="<?php echo $o['imagepreview'];?>"/>
	</p>
	<?php if(pagelines_option($oid)):?>
		<img class="pagelines_image_preview" id="image_<?php echo $oid;?>" src="<?php echo pagelines_option($oid);?>" style="max-width:<?php echo $o['imagepreview'];?>px"/>
	<?php endif;?>
	
<?php }
		
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

function _get_select_option( $oid, $o ){ ?>
	
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


function _get_color_picker($oid, $o){ // Color Picker Template ?>
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
		 * 
		 *
		 *  Layout Builder (Layout Drag & Drop)
		 *
		 *
		 *  @package PageLines Core
		 *  @subpackage Options
		 *  @since 4.0
		 *
		 */
		function _get_layout_builder($optionid, $option_settings){ ?>
			<div class="layout_controls selected_template">
			

				<div id="layout-dimensions" class="template-edit-panel">
					<h3>Configure Layout Dimensions</h3>
					<div class="select-edit-layout">
						<div class="layout-selections layout-builder-select fix">
							<div class="layout-overview">Select Layout To Edit</div>
							<?php


							global $pagelines_layout;
							foreach(get_the_layouts() as $layout):
								
								$the_last_edited = (pagelines_sub_option('layout', 'last_edit')) ? pagelines_sub_option('layout', 'last_edit') : 'one-sidebar-right';
								
								$load_layout = ($the_last_edited == $layout) ? true : false;
							
							?>
							<div class="layout-select-item">
								<span class="layout-image-border <?php if($load_layout) echo 'selectedlayout';?>">
									<span class="layout-image <?php echo $layout;?>">&nbsp;</span>
								</span>
								<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'last_edit'); ?>" value="<?php echo $layout;?>" <?php if($load_layout) echo 'checked';?> />
							</div>
							<?php endforeach;?>

						</div>	
					</div>
					<?php

				foreach(get_the_layouts() as $layout):

				$buildlayout = new PageLinesLayout($layout);
					?>
				<div class="layouteditor <?php echo $layout;?> <?php if($buildlayout->layout_map['last_edit'] == $layout) echo 'selectededitor';?>">
						<div class="layout-main-content" style="width:<?php echo $buildlayout->builder->bwidth;?>px">

							<div id="innerlayout" class="layout-inner-content" >
								<?php if($buildlayout->west->id != 'hidden'):?>
								<div id="<?php echo $buildlayout->west->id;?>" class="ui-layout-west innerwest loelement locontent"  style="width:<?php echo $buildlayout->west->bwidth;?>px">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->west->text;?></div>
											<div class="width "><span><?php echo $buildlayout->west->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php endif;?>
								<div id="<?php echo $buildlayout->center->id;?>" class="ui-layout-center loelement locontent innercenter">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->center->text;?></div>
											<div class="width "><span><?php echo $buildlayout->center->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php if( $buildlayout->east->id != 'hidden'):?>
								<div id="<?php echo $buildlayout->east->id;?>" class="ui-layout-east innereast loelement locontent" style="width:<?php echo $buildlayout->east->bwidth;?>px">
									<div class="loelement-pad">
										<div class="loelement-info">
											<div class="layout_text"><?php echo $buildlayout->east->text;?></div>
											<div class="width "><span><?php echo $buildlayout->east->width;?></span>px</div>
										</div>
									</div>
								</div>
								<?php endif;?>
								<div id="contentwidth" class="ui-layout-south loelement locontent" style="background: #fff;">
									<div class="loelement-pad"><div class="loelement-info"><div class="width"><span><?php echo $buildlayout->content->width;?></span>px</div></div></div>
								</div>
								<div id="top" class="ui-layout-north loelement locontent"><div class="loelement-pad"><div class="loelement-info">Content Area</div></div></div>
							</div>
							<div class="margin-west loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>
							<div class="margin-east loelement"><div class="loelement-pad"><div class="loelement-info">Margin<div class="width"></div></div></div></div>

						</div>


							<div class="layoutinputs">
								<label class="context" for="input-content-width">Global Content Width</label>
								<input type="text" name="<?php pagelines_option_name('layout', 'content_width'); ?>" id="input-content-width" value="<?php echo $buildlayout->content->width;?>" size=5 readonly/>
								<label class="context"  for="input-maincolumn-width">Main Column Width</label>
								<input type="text" name="<?php pagelines_option_name('layout', $layout, 'maincolumn_width'); ?>" id="input-maincolumn-width" value="<?php echo $buildlayout->main_content->width;?>" size=5 readonly/>

								<label class="context"  for="input-primarysidebar-width">Sidebar1 Width</label>
								<input type="text" name="<?php pagelines_option_name('layout', $layout, 'primarysidebar_width'); ?>" id="input-primarysidebar-width" value="<?php echo  $buildlayout->sidebar1->width;?>" size=5 readonly/>
							</div>
				</div>
				<?php endforeach;?>

			</div>
		</div>
		<?php }
		
/**
 * 
 *
 *  Layout Select (Layout Selector)
 *
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _get_layout_select($optionid, $option_settings){ ?>
	<div id="layout_selector" class="template-edit-panel">

		<div class="layout-selections layout-select-default fix">
			<div class="layout-overview">Default Layout</div>
			<?php


			global $pagelines_layout;
			foreach(get_the_layouts() as $layout):
			?>
			<div class="layout-select-item">
				<span class="layout-image-border <?php if($pagelines_layout->layout_map['saved_layout'] == $layout) echo 'selectedlayout';?>"><span class="layout-image <?php echo $layout;?>">&nbsp;</span></span>
				<input type="radio" class="layoutinput" name="<?php pagelines_option_name('layout', 'saved_layout'); ?>" value="<?php echo $layout;?>" <?php if($pagelines_layout->layout_map['saved_layout'] == $layout) echo 'checked';?>>
			</div>
			<?php endforeach;?>

		</div>

	</div>
	<div class="clear"></div>
<?php }

/**
 * 
 *
 *  Template Builder (Sections Drag & Drop)
 *
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _get_template_builder(){
	
		global $pagelines_template;
		global $unavailable_section_areas;
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'hide'; 
	?>
	<input type="hidden" value="<?php echo $dtoggle;?>" id="describe_toggle" class="describe_toggle" name="describe_toggle"  />	
	<div class="confirm_save">Template Configuration Saved!</div>
	<label for="tselect" class="tselect_label">Select Template Area</label>
	<select name="tselect" id="tselect" class="template_select" >
<?php 	foreach(the_template_map() as $hook => $hook_info):?>
	
	 <?php if(isset($hook_info['templates'])): ?>
		
				<optgroup label="<?php echo $hook_info['name'];?>" class="selectgroup_header">
			<?php foreach($hook_info['templates'] as $template => $tfield):
					if(!isset($tfield['version']) || ($tfield['version'] == 'pro' && VPRO)):
			?>				
						<option value="<?php echo $hook . '-' . $template;?>"><?php echo $tfield['name'];?></option>
				<?php endif;?>
				<?php endforeach;?>
				</optgroup>
			<?php else: ?>
		
		<?php 
				if(!isset($hook_info['version']) || ($hook_info['version'] == 'pro' && VPRO)):
?>
			<option value="<?php echo $hook;?>" <?php if($hook == 'default') echo 'selected="selected"';?>><?php echo $hook_info['name'];?></option>
<?php endif; ?>
			<?php endif;?>
		
	<?php endforeach;?>
	</select>
	<div class="the_template_builder">
		<?php 
		foreach($pagelines_template->map as $hook_id => $hook_info){
			 if(isset($hook_info['templates'])){
				foreach($hook_info['templates'] as $template_id => $template_info ){
					$this->_sortable_section($template_id, $template_info, $hook_id, $hook_info);
				}
			} else {
				$this->_sortable_section($hook_id, $hook_info);
			}

		}?>
	</div>
	<?php 
	
}

/**
 * 
 *
 *  Get Sortable Sections (Sections Drag & Drop)
 *
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 4.0
 *
 */
function _sortable_section($template, $tfield, $hook_id = null, $hook_info = array()){
		global $pl_section_factory;
		
		$available_sections = $pl_section_factory->sections;
		
		$template_slug = ( isset($hook_id) ) ? $hook_id.'-'.$template : $template;
		
		$template_area = ( isset($hook_id) ) ? $hook_id : $template;
		
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'show'; 
		
			?>
		
				<div id="template_data" class="<?php echo $template_slug; ?> layout-type-<?php echo $template_area;?>">
					<div class="editingtemplate fix">
						<span class="edit_template_title"><?php echo $tfield['name'];?> Template Sections</span>

					</div>
					<div class="section_layout_description">
						<div class="config_title">Place Sections <span class="makesubtle">(drag &amp; drop)</span></div>
						<div class="layout-type-frame">
							<div class="layout-type-thumb"></div>
							Template Area: <?php echo ucwords( str_replace('_', ' ', $template_area) );?>
						</div>
					</div>
					
					<div id="section_map" class="template-edit-panel ">

						<div class="sbank template_layout">

							<div class="bank_title">Displayed <?php echo $tfield['name'];?> Sections</div>

							<ul id="sortable_template" class="connectedSortable ">
								<?php if( isset($tfield['sections']) && is_array($tfield['sections'])):?>
									<?php foreach($tfield['sections'] as $section):
									
									 		if(isset( $pl_section_factory->sections[$section] )):
									
												$s = $pl_section_factory->sections[$section];
												
												$section_id =  $s->id;
											
										?>
										<li id="section_<?php echo $section; ?>" class="section_bar <?php if($s->settings['required'] == true) echo 'required-section';?>">
											<div class="section-pad fix" style="background: url(<?php echo $s->settings['icon'];?>) no-repeat 10px 8px;">
												
												<h4><?php echo $s->name;?></h4>
												<span class="s-description" <?php if($dtoggle = 'hide'):?>style="display:none"<?php endif;?> >
												<?php echo $s->settings['description'];?>
												</span>
												
												<?php 
												
												$section_control = pagelines_option('section-control');
												
												// Options 
												$check_name = PAGELINES_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][hide]';
												$check_value = isset($section_control[$template_slug][$section]['hide']) ? $section_control[$template_slug][$section]['hide'] : null;
												
												$posts_check_type = ($check_value) ? 'show' : 'hide';
												
												if($template == 'posts' || $template == 'single' || $template == '404' ){
													$default_display_check_disabled = true;
												} else {
													$default_display_check_disabled = false;
												}

												if($template_area == 'main' || $template_area == 'templates'){
													
													$posts_check_disabled = true;
												} else {
													$posts_check_label = ucfirst($posts_check_type) .' On Posts Pages';
													$posts_check_name = PAGELINES_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][posts-page]['.$posts_check_type.']';
													$posts_check_value = isset($section_control[$template_slug][$section]['posts-page'][$posts_check_type]) ? $section_control[$template_slug][$section]['posts-page'][$posts_check_type] : null;
													$posts_check_disabled = false;
												}
												
												// Hooks
											
												//pagelines_ob_section_template( $s );
												global $registered_hooks;
												
												?>
												<div class="section-moreinfo">
													<div><span class="section-moreinfo-toggle" onClick="jQuery(this).parent().next('.section-moreinfo-info').slideToggle();">Advanced Setup &darr;</span></div>
													<div class="section-moreinfo-info">
														<?php if(!$default_display_check_disabled):?>
														<strong>Settings</strong> 
														<div class="section-options">
															<div class="section-options-row">
																<input class="section_control_check" type="checkbox" id="<?php echo $check_name; ?>" name="<?php echo $check_name; ?>" <?php checked((bool) $check_value); ?> />
																<label for="<?php echo $check_name; ?>">Hide This By Default</label>
															</div>
															<?php if(!$posts_check_disabled):?>
															<div class="section-options-row">
																	<input class="section_control_check" type="checkbox" id="<?php echo $posts_check_name; ?>" name="<?php echo $posts_check_name; ?>" <?php checked((bool) $posts_check_value); ?>/>
																	<label for="<?php echo $posts_check_name; ?>" class="<?php echo 'check_type_'.$posts_check_type; ?>"><?php echo $posts_check_label;?></label>
															</div>
															<?php endif;?>
														</div>
														<?php endif;?>
													
													</div>
												</div>
											</div>
										</li>
										<?php if(isset($available_sections[$section])) { unset($available_sections[$section]); } ?>
							
									<?php endif; endforeach;?>

								<?php endif;?>
							</ul>
							<div class="section_setup_controls fix">
							
							
								<span class="setup_control" onClick="PageLinesSlideToggle('.s-description', '.describe_toggle', '.setup_control_text','Hide Section Descriptions', 'Show Section Descriptions', 'pl_section_desc_toggle');">
									<span class="setup_control_text">
										<?php if($dtoggle == 'show'):?>
											Hide Section Descriptions
										<?php else: ?>
											Show Section Descriptions
										<?php endif;?>
									</span>
								</span>
							</div>
						</div>
						<div class="sbank available_sections">

							<div class="bank_title">Available/Disabled Sections</div>
							<ul id="sortable_sections" class="connectedSortable ">
								<?php 
								foreach($available_sections as $sectionclass => $section):
								
							
										/* Flip values and keys */
										$works_with = array_flip($section->settings['workswith']);
										$fails_with = array_flip($section->settings['failswith']);
										
										$markup_type = (!empty($hook_info)) ? $hook_info['markup'] : $tfield['markup'];
									
										if(isset( $works_with[$template] ) || isset( $works_with[$hook_id]) || isset($works_with[$hook_id.'-'.$template]) || isset($works_with[$markup_type])):?>
											<?php if( !isset($fails_with[$template]) && !isset($fails_with[$hook_id]) ):?>
											<li id="section_<?php echo $sectionclass;?>" class="section_bar" >
												<div class="section-pad fix" style="background: url(<?php echo $section->settings['icon'];?>) no-repeat 10px 6px;">
													<h4><?php echo $section->name;?></h4>
													<span class="s-description" <?php if($dtoggle = 'hide'):?>style="display:none"<?php endif;?>>
														<?php echo $section->settings['description'];?>
													</span>
												</div>
											</li>
											<?php endif;?>
										<?php endif;?>
									
								<?php endforeach;?>
							</ul>
						</div>
					
						<div class="clear"></div>
					</div>


					<div class="clear"></div>
						<div class="vpro_sections_call s-description" <?php if($dtoggle = 'hide'):?>style="display:none"<?php endif;?>>
							<?php if(!VPRO):?>
					
									<p>
										<strong>A Note To Free Version Users:</strong><br/> 
										In the Pro version of this product you will find several more "template areas" and HTML sections to play around with.
									</p>
									<p class="mod">
										<?php if(isset($pl_section_factory->unavailable_sections) && is_array($pl_section_factory->unavailable_sections)):?>
											<strong>Missing Pro Sections</strong><br/>
											<?php foreach( $pl_section_factory->unavailable_sections as $unavailable_section ):?>
												<?php echo $unavailable_section->name;if($unavailable_section !== end($pl_section_factory->unavailable_sections)) echo ',';?>
											<?php endforeach;?>
										<?php endif;?>
									</p>
									<p class="mod">
										<?php if(isset($unavailable_section_areas) && is_array($unavailable_section_areas)):?>
											<strong>Missing Pro Templates &amp; Section Areas</strong> (i.e. places to put sections)<br/>
											<?php foreach( $unavailable_section_areas as $unavailable_section_area_name ):?>
												<?php echo $unavailable_section_area_name; if($unavailable_section_area_name !== end($unavailable_section_areas)) echo ',';?> 
											<?php endforeach;?>
										<?php endif;?>
									</p>
						
						
							<?php endif;?>
						
								<p class="">
									<strong>Section Quick Start</strong><br/> 
									Sections are a super powerful way to control the content on your website. Building your site using sections has just 3 steps...
									<ol>
										<li><strong>Place</strong> Place sections in your templates using the interface above. This controls their order and loading.</li>
										<li><strong>Control</strong> If you want more control over where cross-template sections show; use section settings (under "Advanced Setup").  You can hide 'cross-template' sections, like sidebars, by default and activate them on individual pages/posts or on your blog page.</li>
										<li><strong>Customize</strong> Customize your sections using the theme settings on individual pages/posts and in this panel.  You can also do advanced customization through hooks and custom css (for more info please see the <a href="http://www.pagelines.com/docs/">docs</a>).</li>
									</ol>
								</p>
						</div>
						
 
				</div>
	
<?php
}




/**
 * Option Interface Footer
 *
 */
function build_footer(){?>
		<div id="optionsfooter">
			<div class="hl"></div>
				<div class="theinputs">
	  	  			<input class="button-primary" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
					
				</div>
			<div class="clear"></div>
		</div>

		<div class="optionrestore">
				<h4><?php _e('Restore Settings', 'pagelines'); ?></h4>
				<p>
					<div class="context"><input class="button-secondary reset-options" type="submit" name="<?php pagelines_option_name('reset'); ?>" onClick="return ConfirmRestore();" value="Restore Options To Default" />Use this button to restore settings to their defaults. (Note: Restore template and layout information on their individual pages.)</div>
					<?php pl_action_confirm('ConfirmRestore', 'Are you sure? This will restore your settings information to default.');?>
				</p>
			
		</div>

		 <!-- close entire form -->
	  	</form>
	
		<div class="optionrestore restore_column_holder fix">
			<div class="restore_column_split">
				<h4><?php _e('Export Settings', 'pagelines'); ?></h4>
				<p class="fix">
					<a class="button-secondary download-button" href="<?php echo admin_url('admin.php?page=pagelines&amp;download=settings'); ?>">Download Theme Settings</a>
				</p>
			</div>
			
			<div class="restore_column_split">
				<h4><?php _e('Import Settings', 'pagelines'); ?></h4>
				<form method="post" enctype="multipart/form-data">
					<input type="hidden" name="settings_upload" value="settings" />
					<p class="form_input">
						<input type="file" class="text_input" name="file" id="settings-file" />
						<input class="button-secondary" type="submit" value="Upload New Settings" onClick="return ConfirmImportSettings();" />
					</p>
				</form>

				<?php pl_action_confirm('ConfirmImportSettings', 'Are you sure? This will overwrite your current settings and configurations with the information in this file!');?>
			</div>
		</div>
	</td></tr></tbody></table>

	<div class="clear"></div>
	<script type="text/javascript">/*<![CDATA[*/
	jQuery(document).ready(function(){
		jQuery('.framework_loading').hide();
	});
	/*]]>*/</script>
	
	</div>
<?php }


} 
// ===============================
// = END OF OPTIONS LAYOUT CLASS =
// ===============================