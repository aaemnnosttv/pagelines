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
		
		
		/*
			TODO 
				- title
				- settings array
		*/
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
									<div class="ohead" class="fix">
										<div class="ohead-pad fix">
											
											<div class="superlink-wrap">
												<a class="superlink" href="#">
													<span class="superlink-pagelines">&nbsp;</span>
												</a>
											</div>
											<div class="ohead-title">
												<?php _e('Settings', 'pagelines');?> 
											</div>
											<div class="ohead-title-right">
												<div class="superlink-wrap osave-wrap">
													<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
												</div>
											
												
											</div>
										</div>
										
									
									</div>
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
		 * Option Interface Footer
		 *
		 */
		function build_footer(){?>
				<div id="optionsfooter" class="fix">
					<div class="ohead fix">
						<div class="ohead-pad fix">
							<div class="superlink-wrap osave-wrap">
								<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
							</div>
						</div>
					</div>
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
		
		/**
		 * Option Interface Body, including vertical tabbed nav
		 *
		 */
		function build_body(){
			$option_engine = new PageLinesOptionEngine;
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
									<div class="tabtitle"><div class="tabtitle-pad"><?php echo ucwords(str_replace('_',' ',$menu));?></div></div>
								<?php endif;?>
							
								<?php 
								
									foreach($oids as $oid => $o)
										$option_engine->option_engine($oid, $o);
								 ?>
								<div class="clear"></div>
							</div>
						
					<?php endforeach; ?>	
				</div> <!-- End the tabs -->
			</div> <!-- End tabs -->
<?php 	}
		


} 
// ===============================
// = END OF OPTIONS LAYOUT CLASS =
// ===============================