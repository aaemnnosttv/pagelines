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
	function __construct() {
		
		$this->sc_settings = pagelines_option('section-control');
		$this->sc_namespace = PAGELINES_SETTINGS."['section-control']";
		
		global $pl_section_factory;
		$this->factory = $pl_section_factory->sections;
		$this->avail = $this->factory; 
	}

	function sc_name( $ta, $section, $field, $sub = null){
		
		
		if(isset($sub))
			return sprintf('%s[%s][%s][%s][%s]', $this->sc_namespace, $ta, $section, $field, $sub);
		else 
			return sprintf('%s[%s][%s][%s]', $this->sc_namespace, $ta, $section, $field);
		
	}
	
	function sc_value( $ta, $section, $field, $sub = null){
		if(isset($sub))
			return isset($this->sc_settings[$ta][$section][$field][$sub]) ? $this->sc_settings[$ta][$section][$field][$sub] : null;
		else 
			return isset($this->sc_settings[$ta][$section][$field]) ? $this->sc_settings[$ta][$section][$field] : null;
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

	function do_template_select(){ ?>
	<label for="tselect" class="tselect_label">Select Template Area</label>
	<select name="tselect" id="tselect" class="template_select" >
	<?php 	foreach(the_template_map() as $hook => $hook_info):
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
										<div class="tg-format tg-content-templates">
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
	
		$this->_sub_selector('templates', 'sel-templates-sub', 'Which Template?');
		
		$this->_sub_selector('main', 'sel-content-sub', 'Which Content Area Type?');
		
	?>

<?php }

	function _sub_selector($type = 'templates', $class, $title = '', $subtitle = ''){ ?>
		<div class="sub-template-selector fix <?php echo $class;?>">
			<div class="sub-templates fix">
				<h4 class="over"><?php echo $title; ?></h4>
				<?php 	
						$h = the_template_map();
						foreach($h[$type]['templates'] as $template => $t){
							if(!isset($t['version']) || ($t['version'] == 'pro' && VPRO))
								printf('<div id="%s" class="sss-button">%s</div>', join( '-', array($type, $template) ), $t['name']);
						}
				?>
			</div>
		</div>
	<?php }

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
		 
		if( isset($t['sections']) && is_array($t['sections'])){
		  
			foreach($t['sections'] as $sid){

				$pieces = explode("ID", $sid);		
				$section = (string) $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;

			 	if(isset( $this->factory[$section] )){

					$s = $this->factory[$section];

					$section_args = array(
						'section'	=> $section,
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
		
		?>
		 
		<li id="<?php echo $a['id'];?>">
			<div class="section-bar <?php if($a['req'] == true) echo 'required-section';?>">
				<div class="section-bar-pad fix" style="background: url(<?php echo $a['icon'];?>) no-repeat 10px 9px;">
		
					
					<div class="section-controls-toggle" onClick="jQuery(this).parent().parent().next('.section-controls').slideToggle('fast');" <?php if(!$a['controls']) echo 'style="display:none;"'?>>
							<div class="section-controls-toggle-pad">Options</div>
					</div>
					
			
					<h4 class="section-bar-title"><?php echo $a['name'];?> <span class="the_clone_id"><?php if($a['clone'] != 1) echo '#'.$a['clone'];?></span></h4>
					<span class="s-description" <?php $this->help_control();?>>
						<?php echo $a['desc'];?>
					</span>
					
				</div>
			</div>
			<?php $this->inline_section_control($a); ?>
		</li>
		
	<?php }
	
	function inline_section_control($a){

		// Options 
		$check_name = $this->sc_name($a['tslug'], $a['section'], 'hide');
		$check_value = $this->sc_value($a['tslug'], $a['section'], 'hide'); 

		$posts_action = ($check_value) ? 'show' : 'hide';

		if($a['tarea'] == 'main' || $a['tarea'] == 'templates')
			$posts_check_disabled = true;
		else {
			$posts_check_label = ucfirst($posts_action) .' On Posts Pages';
			$posts_check_name = $this->sc_name($a['tslug'], $a['section'], 'posts-page', $posts_action);
			$posts_check_value = $this->sc_value($a['tslug'], $a['section'], 'posts-page', $posts_action);
			$posts_check_disabled = false;
		}

		if($this->show_sc( $a['template'] )): ?>
		<div class="section-controls" <?php if(!$a['controls']) echo 'style="display:none;"'?>>
			<div class="section-controls-pad">
					
					<?php if($a['cloning']):?>
						<div class="sc_buttons">
							<div class="clone_button" onClick="cloneSection('<?php echo $a['id'];?>');"><div class="clone_button_pad">Clone</div></div>
							<div class="clone_button clone_remove" style="<?php if($a['clone'] == 1) echo 'display: none;';?>" onClick="deleteSection(this, '<?php echo $a['id'];?>');"><div class="clone_button_pad">Remove</div></div>
						</div>
					<?php endif;
					
					if($this->show_sc( $a['template'] )):?>
						<strong>
							<?php echo $a['name'];?> 
							<?php if($a['clone'] != 1):?><span class="the_clone_id"><?php echo '#'.$a['clone']; ?></span><?php endif;?> 
							Settings
						</strong> 
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
					<?php else: ?>
						No section settings for this template area.
					<?php endif;?>
			</div>
		</div>
	<?php endif;
	}
	
	
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