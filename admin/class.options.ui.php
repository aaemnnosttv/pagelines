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
	function __construct( $args = array() ) {
		
		$defaults = array(
				'title'			=> (is_child_theme()) ? NICECHILDTHEMENAME . __(' Settings') : __('Settings'),
				'callback'		=> null,
				'settings'		=> PAGELINES_SETTINGS, 
				'sanitize'		=> '',
				'show_save'		=> true,
				'show_reset'	=> true, 
				'basic_reset'	=> false,
				'title_size'	=> 'normal',
				'fullform'		=> true
			);
		
		$this->set = wp_parse_args( $args, $defaults );

		// Set option array callbacks
		$this->option_array = (isset($this->set['callback'])) ? call_user_func( $this->set['callback'] ) : get_option_array();
		
		$this->primary_settings = ($this->set['settings'] == PAGELINES_SETTINGS) ? true : false;
		
		$this->tab_cookie = 'PLTab_'.$this->set["settings"];
		
		// Draw the thing
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
					<?php
								
						 	if( $this->set['fullform'] )
								$this->fullform_head();
					
							$this->get_tab_setup();
			
							$this->_get_confirmations_and_system_checking(); 
						
						?>
					
						<div class="clear"></div>
						<div id="dialog" class="thedialog" title="PageLines Extend"></div>
						<div id="optionsheader" class="fix">
							<div class="ohead" class="fix">
								<div class="ohead-pad fix">
									<div id="the_pl_button" class="sl-black superlink-wrap">
										<a class="superlink" href="<?php echo home_url(); ?>/" target="_blank" title="View Site &rarr;">
											<span class="superlink-pagelines">&nbsp;<span class="slpl">View Site</span></span>
										</a>
									</div>
									<div class="ohead-title">
										<?php echo apply_filters( 'pagelines_settings_main_title', $this->set['title'] ); ?> 
										<a class='btag grey viewsitetag' href="<?php echo home_url();?>" target="_blank" style="display: none;">View Your Site &rarr;</a>
									</div>
									<div class="ohead-title-right">
										<?php if($this->set['show_save']):?>
										<div class="superlink-wrap osave-wrap">
											<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
										</div>
										<?php endif;?>
									</div>
								</div>
							</div>
						</div>
<?php }
		
		function fullform_head(){ ?>
			<form id="pagelines-settings-form" method="post" action="options.php" class="main_settings_form">
			<?php 
						wp_nonce_field('update-options'); // security for option saving
						settings_fields($this->set['settings']); // namespace for options important!  
						echo OptEngine::input_hidden('input-full-submit', 'input-full-submit', 0); // submit the form fully, page refresh needed

		}
		
		function fullform_foot(){ ?>
			<?php if($this->set['show_reset']):?>
			<div class="optionrestore fix">
						<?php echo OptEngine::superlink('Restore To Default', 'grey', 'reset-options', 'submit', 'onClick="return ConfirmRestore();"', plname('reset', array('setting' => $this->set['settings'])));?>
						<div class="ortext">Use this button to restore these settings to default. &mdash; <strong>Note</strong>: Restore template and layout information in their individual tabs.</p></div>
					<?php pl_action_confirm('ConfirmRestore', 'Are you sure? This will restore these settings to default.');?>
				

			</div>
			<?php endif;?>
			</form><!-- close entire form -->
		<?php  }
		
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
							<?php if($this->set['show_save']):?>
							<div class="superlink-wrap osave-wrap">
								<input class="superlink osave" type="submit" name="submit" value="<?php _e('Save Options', 'pagelines');?>" />
							</div>
							<?php else:?>
								<div class="superlink-wrap">
									<a class="superlink" href="http://www.pagelines.com/"><span class="superlink-pad">Visit PageLines Site &rarr;</span></a>
								</div>
							<?php endif;?>
						</div>
					</div>
				</div>

				<?php
				
					if( $this->set['fullform'] )
						$this->fullform_foot();
				
					if($this->set['basic_reset'])
						$this->basic_reset();
												
						?>
				
			</td></tr></tbody></table>

			<div class="clear"></div>
			</div>
		<?php }
		
		function basic_reset(){ ?>
			<form method="post">
				<div class="optionrestore fix">
				
						<?php 
						echo OptEngine::input_hidden('the_pl_setting', 'the_pl_setting', $this->set['settings']);
						echo OptEngine::superlink('Restore To Default', 'grey', 'reset-options', 'submit', 'onClick="return ConfirmRestore();"',  'pl_reset_settings' );
						pl_action_confirm('ConfirmRestore', 'Are you sure? This will restore these settings to default.');
						?>
						<div class="ortext">Use this button to restore these settings to default.</div>
				
				
				</div>
			</form>
<?php }
		
		
		/**
		 * Option Interface Body, including vertical tabbed nav
		 *
		 */
		function build_body(){
			$option_engine = new OptEngine( $this->set['settings'] );
			global $pl_section_factory; 
?>
			<div id="tabs">	
				<ul id="tabsnav">
					<li><span class="graphic top">&nbsp;</span></li>
					<?php 
					
					
					foreach($this->option_array as $menu => $oids){
						
						$bg = (isset($oids['icon'])) ? sprintf('style="background: transparent url(%s) no-repeat 0 0;"', $oids['icon']) : '';
						
						printf('<li><a class="%1$s tabnav-element" href="#%1$s"><span %3$s >%2$s</span></a></li>', $menu, ucwords( str_replace('_',' ',$menu)), $bg);
					}
					?>
					<li><span class="graphic bottom">&nbsp;</span></li>
					
					<div class="framework_loading"> 
						<a href="http://www.pagelines.com/forum/discussion/6489" target="_blank" title="Javascript Issue Detector">
							<span class="framework_loading_gif" >&nbsp;</span>
						</a>
					</div>
					<script type="text/javascript">/*<![CDATA[*/ jQuery(document).ready(function(){ jQuery('.framework_loading').hide(); }); /*]]>*/</script>
				</ul>
				
				<div id="thetabs" class="plpanel fix">
<?php 				if(!VPRO) $this->get_pro_call();
					 
					foreach($this->option_array as $menu => $oids){
						$bg = (isset($oids['icon'])) ? sprintf('style="background: transparent url(%s) no-repeat 10px 16px;"', $oids['icon']) : '';
						
						$is_htabs = ( isset($oids['htabs']) ) ? true : false;
						
						// The tab container start....
						printf('<div id="%s" class="tabinfo %s">', $menu, ($is_htabs) ? 'htabs-interface' : '');
					
							// Draw Menu Title w/ Icon
							if( stripos($menu, '_') !== 0 )
								printf('<div class="tabtitle" %s><div class="tabtitle-pad">%s</div></div>', $bg, ucwords(str_replace('_',' ',$menu)));
							
							
							// Render Options
							if( isset($oids['htabs']))
								OptEngine::get_horizontal_nav( $menu, $oids );
								
							elseif( isset($oids['metapanel']))
								echo $oids['metapanel'];
								
							else
								foreach( $oids as $oid => $o )
									if( $oid != 'icon' )
										$option_engine->option_engine($oid, $o);
								
								
						echo '<div class="clear"></div></div>';
					}
					?>	
				</div>
			</div>
<?php 	}

	/**
	 *  Tab Stuff
	 */
	function get_tab_setup(){ ?>
		<script type="text/javascript">
				jQuery(document).ready(function() {						
					var myTabs = jQuery("#tabs").tabs({ cookie: { name: "<?php echo $this->set['settings'];?>-tabs" }, fx: { opacity: "toggle", duration: 100 }});
				});
		</script>
	<?php }
	
	
	function get_pro_call(){
		global $pl_section_factory; 
		
		$usections = $pl_section_factory->unavailable_sections;
		
		?>
	
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
				<?php if(isset($usections) && is_array($usections)):?>
					<p class="mod"><strong>Pro Sections</strong> (drag &amp; drop)<br/>
					<?php 
						foreach( $usections as $unavailable_section )
							echo $unavailable_section->name;if($unavailable_section !== end($usections)) echo ' &middot; ';?>
					</p>
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
	
	<?php }

} // End Class 