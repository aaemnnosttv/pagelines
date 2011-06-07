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
	
			$this->do_template_select(); 
			echo '</div>';
	}

	function do_confirms_and_hidden_fields(){ 
		$dtoggle = (get_option('pl_section_desc_toggle')) ? get_option('pl_section_desc_toggle') : 'show'; 
		
		?>
		<input type="hidden" value="<?php echo $dtoggle;?>" id="describe_toggle" class="describe_toggle" name="describe_toggle"  />	
		<div class="confirm_save">Template Configuration Saved!</div>
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
	function draw_template_select(){ ?>	
	<div class="template-selector fix">	
		<div class="template-selector-pad fix">
			<h4 class="over s-description" <?php $this->help_control();?>>1. Select Template Area</h4>
			<div class="tgraph tgraph-templates">
				<div class="tgraph-pad">
					<div class="tgraph-controls">
						<div class="tgraph-controls-pad fix">
							<div class="tg-format tg-header"><div class="tg-pad">Header</div></div>
							<div class="tg-format tg-templates"><div class="tg-pad">Page Templates</div></div>
							<div class="tg-format tg-morefoot"><div class="tg-pad">Morefoot</div></div>
							<div class="tg-format tg-footer"><div class="tg-pad">Footer</div></div>
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
										<div class="tg-format">
											<div class="tg-pad">Content Area</div>
										</div>
									</div>
								</div>
							</div>
							<div class="tg-wrap">
								<div class="tg-sidebarwrap">
									<div class="tgc">
										<div class="tg-format">
											<div class="tg-pad">Sidebar Wrap</div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar1">
									<div class="tg-mmr">
										<div class="tgc">
											<div class="tg-format">
												<div class="tg-pad">SB1</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tg-sidebar2">
									<div class="tg-mml">
										<div class="tgc">
											<div class="tg-format">
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
<?php }

	function do_template_builder(){
		
		global $pagelines_template;
		global $unavailable_section_areas;
		?>
		<div class="the_template_builder">
			<div class="the_template_builder_pad">
				
<?php 
			foreach($pagelines_template->map as $hook_id => $hook_info){
				if(isset($hook_info['templates'])){
					foreach($hook_info['templates'] as $template_id => $template_info )
						$this->_sortable_section($template_id, $template_info, $hook_id, $hook_info);	
				} else 
					$this->_sortable_section($hook_id, $hook_info);
			
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
	function _sortable_section($template, $tfield, $hook_id = null, $hook_info = array()){
			global $pl_section_factory;

			$available_sections = $pl_section_factory->sections;

			$template_slug = ( isset($hook_id) ) ? $hook_id.'-'.$template : $template;

			$template_area = ( isset($hook_id) ) ? $hook_id : $template;

				?>
				
				<div id="template_data" class="<?php echo $template_slug; ?> layout-type-<?php echo $template_area;?>">
					<div class="ttitle"><span>Editing &rarr;</span> <?php echo $tfield['name'];?></div>
					<div id="section_map" class="template-edit-panel ">
						<h4 class='over s-description' <?php $this->help_control();?>>2. Arrange Sections In Area With Drag &amp; Drop</h4>
						<div class="sbank template_layout">
							<div class="sbank-pad">
								<div class="bank_title">Displayed <?php echo $tfield['name'];?> Sections</div>
								<ul id="sortable_template" class="connectedSortable ">
										<?php if( isset($tfield['sections']) && is_array($tfield['sections'])):?>
											<?php foreach($tfield['sections'] as $section):

											 		if(isset( $pl_section_factory->sections[$section] )):

														$s = $pl_section_factory->sections[$section];

														$section_id =  $s->id;

														$section_args = array(
															'section'	=> $section,
															'template'	=> $template,
															'id'		=> 'section_' . $section, 
															'icon'		=> $s->settings['icon'], 
															'name'		=> $s->name, 
															'desc'		=> $s->settings['description'],
															'req'		=> $s->settings['required'],
															'controls'	=> true,
															'tslug'		=> $template_slug,
															'tarea'		=> $template_area,
														
														);

														$this->draw_section( $section_args );

											
												if(isset($available_sections[$section]))
													unset($available_sections[$section]);
											
												 endif; endforeach;
											endif;?>
								</ul>
								<?php $this->section_setup_controls(); ?>
								
							</div>
								
						</div>
							<div class="sbank available_sections">
								<div class="sbank-pad">
									<div class="bank_title">Available/Disabled Sections</div>
									<ul id="sortable_sections" class="connectedSortable ">
										<?php 
										foreach($available_sections as $sectionclass => $section):


											/* Flip values and keys */
											$works_with = array_flip($section->settings['workswith']);
											$fails_with = array_flip($section->settings['failswith']);

											$markup_type = (!empty($hook_info)) ? $hook_info['markup'] : $tfield['markup'];

											if(isset( $works_with[$template] ) || isset( $works_with[$hook_id]) || isset($works_with[$hook_id.'-'.$template]) || isset($works_with[$markup_type])):
												$section_args = array(
													'id'		=> 'section_' . $sectionclass,
													'template'	=> $template,
													'section'	=> $sectionclass, 
													'icon'		=> $section->settings['icon'], 
													'name'		=> $section->name, 
													'desc'		=> $section->settings['description']
												);
										
												if( !isset($fails_with[$template]) && !isset($fails_with[$hook_id]) )
													$this->draw_section( $section_args );
												
													
											endif;
										endforeach;
									?>
									</ul>
								</div>
								<div class="clear"></div>
							</div>
						</div>
						<div class="clear"></div>
						<?php $this->inline_docs(); ?>	
					</div>

	<?php
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
			'req'			=> false
		);

		$a = wp_parse_args( $args, $defaults );
		
		?>
		 
		<li id="<?php echo $a['id'];?>">
			<div class="section-bar <?php if($a['req'] == true) echo 'required-section';?>">
				<div class="section-bar-pad fix" style="background: url(<?php echo $a['icon'];?>) no-repeat 10px 6px;">
					<h4><?php echo $a['name'];?></h4>
					<span class="s-description" <?php $this->help_control();?>>
						<?php echo $a['desc'];?>
					</span>
					<?php if($a['controls'] == true): ?>
					<span class="section-controls-toggle" onClick="jQuery(this).parent().parent().next('.section-controls').slideToggle('fast');">
							Section Control &darr;
					</span>
				
					<?php endif;?>
				</div>
			</div>
			<?php
				if($a['controls'] == true)
					$this->inline_section_control($a['name'], $a['template'], $a['section'], $a['tslug'], $a['tarea']);
			
			?>
		</li>
		
	<?php }
	
	function inline_section_control($name, $template, $section, $template_slug, $template_area){

		$section_control = pagelines_option('section-control');

		// Options 
		$check_name = PAGELINES_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][hide]';
		$check_value = isset($section_control[$template_slug][$section]['hide']) ? $section_control[$template_slug][$section]['hide'] : null;

		$posts_check_type = ($check_value) ? 'show' : 'hide';

		if($template == 'posts' || $template == 'single' || $template == '404' )
			$default_display_check_disabled = true;
		else
			$default_display_check_disabled = false;

		if($template_area == 'main' || $template_area == 'templates')
			$posts_check_disabled = true;
		else {
			$posts_check_label = ucfirst($posts_check_type) .' On Posts Pages';
			$posts_check_name = PAGELINES_SETTINGS.'[section-control]['.$template_slug.']['.$section.'][posts-page]['.$posts_check_type.']';
			$posts_check_value = isset($section_control[$template_slug][$section]['posts-page'][$posts_check_type]) ? $section_control[$template_slug][$section]['posts-page'][$posts_check_type] : null;
			$posts_check_disabled = false;
		}

		?>
		<div class="section-controls">
			<div class="section-controls-pad">
				<?php if(!$default_display_check_disabled):?>
					<strong>Section Settings</strong> 
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
	<?php }
	
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
	
	function inline_docs(){ ?>
		
		<div class="vpro_sections_call s-description" <?php $this->help_control();?>>
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