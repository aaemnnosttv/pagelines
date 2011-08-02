<?php
/**
 * 
 *
 *  Template Builder 
 *
 *
 *  @package PageLines Admin
 *  @subpackage OptionsUI
 *  @since 2.0.b3
 *
 */

class PageLinesTemplateBuilder {


	/**
	 * Construct
	 */
	function __construct( $oid, $o, $setting = PAGELINES_SETTINGS ) {
		
		global $pagelines_template;
		global $pl_section_factory;
		
		$oset = array( 'setting' => $setting );
		
		$this->sc_settings = ploption('section-control', $oset);
		$this->sc_global = ploption('section-control', array('setting' => PAGELINES_SETTINGS));
		$this->sc_namespace = sprintf('%s[section-control]', $setting);
		
		$this->template_map = get_option('pagelines_template_map');
		
		
		$this->factory = $pl_section_factory->sections;
		
		$this->template = $pagelines_template;
		
	}

	function sc_name( $ta, $sid, $field, $sub = null){
		
		
		if(isset($sub))
			return sprintf('%s[%s][%s][%s][%s]', $this->sc_namespace, $ta, $sid, $field, $sub);
		else 
			return sprintf('%s[%s][%s][%s]', $this->sc_namespace, $ta, $sid, $field);
		
	}
	
	function sc_value( $ta, $sid, $field, $sub = null){
 
		if(isset($sub))
			return isset($this->sc_settings[$ta][$sid][$field][$sub]) ? $this->sc_settings[$ta][$sid][$field][$sub] : null;
		else 
			return isset($this->sc_settings[$ta][$sid][$field]) ? $this->sc_settings[$ta][$sid][$field] : null;
			
	}

	
	
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
	function draw_template_builder(){
		
			$this->do_confirms_and_hidden_fields();
		
			echo '<div class="tbuilder">';
			
				$this->draw_template_select(); 
			
				$this->do_template_builder();
	
				//$this->do_template_select(); 
				
			echo '</div>';
	}

	function do_confirms_and_hidden_fields(){ 
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'show'; 
	?>
		<input type="hidden" value="<?php echo $dtoggle;?>" id="describe_toggle" class="describe_toggle" name="describe_toggle"  />	
		
<?php }

	function do_template_select(){ 
		global $pagelines_template;
		?>
	<label for="tselect" class="tselect_label">Select Template Area</label>
	<select name="tselect" id="tselect" class="template_select" >
	<?php 	foreach($pagelines_template->map as $hook => $hook_info):
	 			if(isset($hook_info['templates'])): ?>
					<optgroup label="<?php echo $hook_info['name'];?>" class="selectgroup_header">
	<?php 			
					foreach($hook_info['templates'] as $template => $tfield){
							if(!isset($tfield['version']) || ($tfield['version'] == 'pro' && VPRO))
								printf('<option value="%s">%s</option>', $hook . '-' . $template, $tfield['name']);
					}?>
						</optgroup>
					<?php else: 
				
						if(!isset($hook_info['version']) || ($hook_info['version'] == 'pro' && VPRO))
							printf('<option value="%s" %s>%s</option>', $hook, ($hook == 'default') ? 'selected="selected"' : '', $hook_info['name']);
		 		endif; 
			endforeach;?>
	</select>
	<?php }

	/**
	 * 
	 *
	 *  Do Template Area Selector
	 *
	 *
	 */
	function draw_template_select(){ 
		global $pagelines_template;
		global $unavailable_section_areas;
		
		?>	
		
			<script type="text/javascript">
				jQuery(document).ready(function() {		 
					// 
					// jQuery('.tg-format').click( function() {
					// 
					// 	var area = jQuery(this).attr('id');
					// 
					// 	$.cookie('PageLinesTemplateTab', area);
					// 
					// });

				});
			</script>
	<div class="template-selector fix">	
		<div class="template-selector-pad fix">
			<h4 class="over">1. Select Template Area</h4>
			<div class="tgraph tgraph-templates">
				<div class="tgraph-pad">
					<div class="tgraph-controls">
						<div class="tgraph-controls-pad fix">
							<div id="ta-header" class="load-build tg-format tg-header"><div class="tg-pad">Header</div></div>
							<div id="ta-templates" class="tg-format tg-templates"><div class="tg-pad">Page Templates</div></div>
							<div id="ta-morefoot" class="load-build tg-format tg-morefoot"><div class="tg-pad">Morefoot</div></div>
							<div id="ta-footer" class="load-build tg-format tg-footer"><div class="tg-pad">Footer</div></div>
						</div>
					</div>
				</div>
			</div>
			<div class="tgraph tgraph-content">
				<div class="tgraph-pad">
					<div class="tgraph-controls">
						<div class="tgraph-controls-pad fix">
							<div class="tg-content-area">
							
								<div class="tg-rm">
									<div clas="tgc">
										<div id="ta-content" class="tg-format tg-content-templates">
											<div class="tg-pad">Content Area</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tg-wrap">
								<div class="tg-sidebarwrap">
									<div class="tgc">
										<div id="ta-sidebar_wrap" class="load-build tg-format">
											<div class="tg-pad">Sidebar Wrap</div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar1">
									<div class="tg-mmr">
										<div class="tgc">
											<div id="ta-sidebar1" class="load-build tg-format">
												<div class="tg-pad">SB1</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar2">
									<div class="tg-mml">
										<div class="tgc">
											<div id="ta-sidebar2" class="load-build tg-format">
												<div class="tg-pad">SB2</div>
											</div>
										</div>
									</div>
								</div>
					
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>	
	<div class="clear"></div>
	<?php
	
		$this->_sub_selector('templates', 'sel-templates-sub', 'For Which Type of Page?');
		
		$this->_sub_selector('main', 'sel-content-sub', 'Which Content Area Type?');
		
	?>

<?php }

	function _sub_selector($type = 'templates', $class, $title = '', $subtitle = ''){
		global $pagelines_template;
		
		
		// The Buttons
		$buttons = '';
		foreach($pagelines_template->map[$type]['templates'] as $template => $t){
			
			if( (!isset($t['version']) || ($t['version'] == 'pro' && VPRO)) && isset($t['name']))
				$buttons .= sprintf('<div id="%s" class="sss-button"><div class="sss-button-pad">%s</div></div>', join( '-', array($type, $template) ), $t['name']);
				
		}
		
		// Output
		printf('<div class="sub-template-selector fix %s"><div class="sub-templates fix"><h4 class="over">%s</h4>%s</div></div>', $class, $title, $buttons);
		
	}

	function do_template_builder(){
		
		global $pagelines_template;
		global $unavailable_section_areas;
		?>
		<div class="the_template_builder">
			<div class="the_template_builder_pad">
				
<?php 
			foreach($pagelines_template->map as $hook => $h){
				
				if( isset($h['templates']) ){
					
					foreach($h['templates'] as $tid => $t )
						$this->section_banks( $tid, $t, $hook, $h );	
						
				} else 
					$this->section_banks( $hook, $h );
			
			}?>
			</div>
		</div>
	<?php }
	
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
	function section_banks($template, $tfield, $hook = null, $hook_info = array()){
		
			$template_slug = ( isset($hook) ) ? join('-', array( $hook, $template )) : $template;
			$template_area = ( isset($hook) ) ? $hook : $template;
?>
				
				<div id="template_data" class="<?php echo $template_slug; ?> layout-type-<?php echo $template_area;?>" title="<?php echo $template_slug; ?>">
					<div class="ttitle" id="highlightme">
						<span>Editing &rarr;</span> <?php echo $tfield['name'];?> 
						<div class="confirm_save"><div class="confirm_save_pad">Section Order Saved!</div></div>
					</div>
					<div id="section_map" class="template-edit-panel ">
						<h4 class='over' >2. Arrange Sections In Area With Drag &amp; Drop</h4>
						<div class="sbank template_layout">
							<div class="sbank-pad">
								<div class="bank_title">Displayed <?php echo $tfield['name'];?> Sections</div>
								<ul id="sortable_template" class="connectedSortable ">
								 	<?php  $this->active_bank( $template, $tfield, $template_area, $template_slug ); ?>
								</ul>
								<?php $this->section_setup_controls(); ?>
								
							</div>		
						</div>
						<div class="sbank available_sections">
							<div class="sbank-pad">
								<div class="bank_title">Available/Disabled Sections</div>
								<ul id="sortable_sections" class="connectedSortable ">
									<?php $this->passive_bank( $template, $tfield, $hook, $hook_info ); ?>
								</ul>
							</div>
							<div class="clear"></div>
						</div>
					</div>
					<div class="clear"></div>
				</div>

<?php  }
	
	function active_bank( $tid, $t, $ta, $ts ){
		 
		$this->avail = $this->factory; 
		if( isset($t['sections']) && is_array($t['sections'])){
		  
			foreach($t['sections'] as $sid){

				$pieces = explode("ID", $sid);		
				$section = (string) $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;

			 	if(isset( $this->factory[$section] )){

					$s = $this->factory[$section];

					$section_args = array(
						'section'	=> $section,
						'sid'		=> $sid,
						'template'	=> $tid,
						'id'		=> 'section_' . $sid, 
						'icon'		=> $s->settings['icon'], 
						'name'		=> $s->name, 
						'desc'		=> $s->settings['description'],
						'req'		=> $s->settings['required'],
						'controls'	=> true,
						'tslug'		=> $ts,
						'tarea'		=> $ta,
						'clone'		=> $clone_id, 
						'cloning'	=> $s->settings['cloning']
					
					);

					$this->draw_section( $section_args );

		
					if(isset($this->avail[$section]))
						unset($this->avail[$section]);
		
			 	} 
			}
		}
	} 
	
	function passive_bank( $template, $t, $hook, $h ){
		 
		foreach( $this->avail as $sid => $s){

			/* Flip values and keys */
			$works_with = array_flip( $s->settings['workswith'] );
			$fails_with = array_flip( $s->settings['failswith'] );

			$markup_type = (!empty($h)) ? $h['markup'] : $t['markup'];

			if(isset( $works_with[ $template ] ) || isset( $works_with[ $hook ]) || isset( $works_with[ $hook.'-'.$template ] ) || isset($works_with[$markup_type])){
				$section_args = array(
					'id'		=> 'section_' . $sid,
					'template'	=> $template,
					'section'	=> $sid, 
					'icon'		=> $s->settings['icon'], 
					'name'		=> $s->name, 
					'desc'		=> $s->settings['description'], 
					'cloning'	=> $s->settings['cloning']
				);
		
				if( !isset($fails_with[ $template ]) && !isset($fails_with[ $hook ]) )
					$this->draw_section( $section_args );
			}
		}
	}
	
	function draw_section( $args ){ 
		
		$defaults = array(
			'section'		=> '',
			'sid'			=> '',
			'template'		=> '',
			'id' 			=> '',
			'icon'		 	=> '',
			'name' 			=> '',
			'desc' 			=> '',
			'controls'		=> false,
			'tslug' 		=> '',				
			'tarea' 		=> '',
			'req'			=> false, 
			'clone'			=> '1', 
			'cloning'		=> false
		);

		$a = wp_parse_args( $args, $defaults );
		
?><li id="<?php echo $a['id'];?>">
	<div class="section-bar <?php if($a['req'] == true) echo 'required-section';?>">
		<div class="section-bar-pad fix" style="background: url(<?php echo $a['icon'];?>) no-repeat 10px 9px;">	
			<div class="section-controls-toggle" onClick="toggleControls(this);" <?php if(!$a['controls']) echo 'style="display:none;"'?>>
					<div class="section-controls-toggle-pad">Options</div>
			</div>
			<h4 class="section-bar-title"><?php echo $a['name'];?> <span class="the_clone_id"><?php if($a['clone'] != 1) echo '#'.$a['clone'];?></span></h4>
			<span class="s-description" <?php $this->help_control();?>><?php echo $a['desc'];?></span>
		</div>
	</div>
	<?php $this->inline_section_control($a); ?>
</li><?php }
	
	function inline_section_control($a){

		
		// Options 
		$check_name = $this->sc_name( $a['tslug'], $a['sid'], 'hide' );
		$check_value = $this->sc_value( $a['tslug'], $a['sid'], 'hide' ); 

		 ?>
		<div class="section-controls" <?php if(!$a['controls']) echo 'style="display:none;"'?>>
			<div class="section-controls-pad">
					<?php if($a['cloning']):?>
						<div class="sc_buttons">
							<div class="clone_button" onClick="cloneSection('<?php echo $a['id'];?>');"><div class="clone_button_pad">Clone</div></div>
							<div class="clone_button clone_remove" style="<?php if($a['clone'] == 1) echo 'display: none;';?>" onClick="deleteSection(this, '<?php echo $a['id'];?>');"><div class="clone_button_pad">Remove</div></div>
						</div>
					<?php endif;
					
					if($this->show_sc( $a['template'] )){
						$clone = ($a['clone'] != 1) ? sprintf('<span class="the_clone_id">%s</span>', '#' . $a['clone']) : '';
						printf('<strong>%s %s %s</strong>', $a['name'], $clone, 'Settings');
						
						echo '<div class="section-options">';
					
							
							$checkbox = sprintf('<input class="section_control_check" type="checkbox" id="%1$s" name="%1$s" %2$s/>', $check_name, checked((bool) $check_value, true, false));
							$label = sprintf('<label for="%s" class="%s">%s</label>', $check_name, '', 'Hide This By Default');
							
							printf('<div class="section-options-row">%s %s</div>', $checkbox, $label);
						
							
						echo '</div>';
						
						} else
					 		echo 'No settings in this template area.';
						
						
						?><div class="clear"></div>
			</div>
		</div>
<?php  }
	
	
	/**
	 * Show section control?
	 * On some template areas, e.g. posts, single, 404, they have their own interface.. so none is needed
	 */
	function show_sc( $t ){
			
		return ( $t == 'posts' || $t == 'single' || $t == '404' ) ? false : true;
	}

	
	function section_setup_controls(){?>
		<div class="section_setup_controls fix">
			<span class="setup_control" onClick="PageLinesSlideToggle('.s-description', '.describe_toggle', '.setup_control_text','Hide Section Descriptions', 'Show Section Descriptions', 'pl_section_desc_toggle');">
				<span class="setup_control_text">
					<?php 
					if($this->help()) 
						echo 'Hide Help and Descriptions';
					else
						echo 'Show Help and Descriptions';
					?>
				</span>
			</span>
		</div>
	<?php }
	
	/**
	 * 
	 *
	 *  Show Section Control Option in MetaPanel
	 *
	 *
	 *  @package PageLines Core
	 *  @subpackage Options
	 *  @since 4.0
	 *
	 */
	function section_control_interface($oid, $o){ 
		
		if(isset($_GET['page']) && $_GET['page'] == 'pagelines_meta')
			return;
		
		if( isset($o['special']) ){
			$this->template->adjust_template_type($o['special']);
			$is_special = true;
		} else 
			$is_special = false;
		
		$template_slug = join( '-', array('templates', $this->template->template_type) );
		$main_slug = join( '-', array('main', $this->template->template_type) );

		global $metapanel_options;
		
		$editing = ($is_special) ? ucfirst($o['special']) : $metapanel_options->edit_slug;
		?>
		
		<div class="section_control_wrap">
			<div class="sc_gap fix">
				<div class="sc_gap_title"><?php echo $editing;?> - Basic Template</div>
				<div class="sc_gap_pad">
					
					<div class="sc_area sc_header ntb">
						<div class="sc_area_pad fix">
							<div class="scta_head">Header</div>
							<?php $this->sc_inputs('header', $this->template->header, $o); ?>
						</div>
					</div>
					<div class="sc_area sc_templates">
						<div class="sc_area_pad fix">
							<div class="scta_head">Template</div>
							<?php $this->sc_inputs($template_slug, $this->template->templates, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_morefoot">
						<div class="sc_area_pad fix">
							<div class="scta_head">Morefoot</div>
							<?php $this->sc_inputs('morefoot', $this->template->morefoot, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_footer nbb">
						<div class="sc_area_pad fix">
							<div class="scta_head">Footer</div>
							<?php $this->sc_inputs('footer', $this->template->footer, $o ); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="sc_gap fix">
				<div class="sc_gap_title"><?php echo $editing;?> - Content Area</div>
				<div class="sc_gap_pad">
				
					<div class="sc_area sc_header ntb">
						<div class="sc_area_pad fix">
							<div class="scta_head">Content</div>
							<?php $this->sc_inputs($main_slug, $this->template->main, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_header">
						<div class="sc_area_pad fix">
							<div class="scta_head">Wrap</div>
							<?php $this->sc_inputs('sidebar_wrap', $this->template->sidebar_wrap, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_header">
						<div class="sc_area_pad fix">
							<div class="scta_head">Sidebar 1</div>
							<?php $this->sc_inputs('sidebar1', $this->template->sidebar1, $o ); ?>
						</div>
					</div>
					<div class="sc_area sc_header nbb">
						<div class="sc_area_pad fix">
							<div class="scta_head">Sidebar 2</div>
							<?php $this->sc_inputs('sidebar2', $this->template->sidebar2, $o ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	<?php }

	
	function sc_inputs( $template_slug, $sections, $o){
		global $post; 
		
		$is_special = (isset($o['special'])) ? true : false;
		
		// No sections in area
		if(empty($sections)){
			echo '<div class="sc_inputs"><div class="emptyarea">Area is empty.</div></div>';
			return;
		}
		
		echo '<div class="sc_inputs">';
		foreach($sections as $key => $sid){
			
			
			$pieces = explode("ID", $sid);		
			$section = (string) $pieces[0];
			$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;
			
			// Get section information
			if( isset($this->factory[ $section ]) ){
				
				$section_data = $this->factory[ $section ];		
				
				$hidden_by_default = isset($this->sc_global[$template_slug][$sid]['hide']) ? $this->sc_global[$template_slug][$sid]['hide'] : null;

				$check_type = ( $hidden_by_default ) ? 'show' : 'hide';
				
				// Make the field 'key'
				$option_name = ($is_special) ? $this->sc_name( $template_slug, $sid, $o['special'], $check_type ) : meta_option_name( array($check_type, $template_slug, $sid) );
				$check_value = ($is_special) ? $this->sc_value( $template_slug, $sid, $o['special'], $check_type ) : get_pagelines_meta($option_name, $post->ID);
				
				// The name of the section
				$clone = ($clone_id != 1) ? ' #'.$clone_id : '';
				$check_label = ucfirst($check_type)." " . $section_data->name.$clone;

				?>
				<div class="sc_wrap <?php echo 'type_'.$check_type;?>" >
					<label class="sc_button" for="<?php echo $option_name;?>">
						<span class="sc_button_pad fix" >
							<span class="sc_check_wrap">
								<input class="sc_check" type="checkbox" id="<?php echo $option_name; ?>" name="<?php echo $option_name; ?>" <?php checked((bool) $check_value); ?> />
							</span>
							<span class="sc_label" >
								<span class="sc_label_pad" style="background: url(<?php echo $section_data->icon;?>) no-repeat 8px 5px"><?php echo $check_label;?></span>
							</span>
						</span>
					</label>

				</div><?php 
			}
		}
		echo '</div>';
		
	}

	
	function help_control(){
		if(!$this->help()) 
			echo 'style="display:none"';
	}
	
	function help(){
		if(  get_option('pl_section_desc_toggle') == 'hide' || get_option('pl_section_desc_toggle') == false || !get_option('pl_section_desc_toggle') )
			return false;
		else 
			return true; 
	}

}