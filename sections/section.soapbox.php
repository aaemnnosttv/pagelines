<?php
/*

	Section: PageLines Soapbox
	Author: Andrew Powers
	Description: Creates boxes and box layouts
	Version: 1.0.0
	Class Name: PageLinesSoapbox
*/

class PageLinesSoapbox extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Soapboxes', 'pagelines');
		$id = 'soapbox';
		
		$default_settings = array(
			'description' 	=> 'Large boxes two per row; with up to three action links . <br/><small>Note: Uses a selected "box-set" for content.</small>',
			'icon'			=> PL_ADMIN_ICONS . '/soap.png', 
			'version'		=> 'pro',
			'dependence'	=> 'PageLinesBoxes', 
			'posttype'		=> 'boxes',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }


	/*
		Loads php that will run on every page load (admin and site)
		Used for creating administrative information, like post types
	*/

	function section_persistent(){

		/*
			Meta Options
		*/
		
				/*
					Create meta fields for the post type
				*/
					$type_meta_array = array(
							'_soapbox_link_1' => array(
									'version'	=> 'pro',
									'type' 		=> 'text',					
									'title'	 	=> 'Soapbox Link 1 URL',
									'desc' 		=> 'Add a full link URL.'
								),
							'_soapbox_link_1_text' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 1 Text',						
									'desc' 		=> 'Add text to be used in this link.',
								),
							'_soapbox_link_1_class' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 1 Classes',						
									'desc' 		=> 'Add CSS classes to add to this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
								),
							'_soapbox_link_2' => array(
									'version' 	=> 'pro',
									'type' 		=> 'text',					
									'title' 	=> 'Soapbox Link 2 URL',
									'desc' 		=> 'Add a full link URL.'
								),
							'_soapbox_link_2_text' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 2 Text',			
									'desc' 		=> 'Add text to be used in this link.',
								),
							'_soapbox_link_2_class' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 2 Classes',			
									'desc' 		=> 'Add CSS classes to add to this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
								),
							'_soapbox_link_3' => array(
									'version' 	=> 'pro',
									'type' 		=> 'text',					
									'title' 	=> 'Soapbox Link 3 URL',
									'desc' 		=> 'Add a full link URL.'
								),
							'_soapbox_link_3_text' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 3 Text',						
									'desc' 		=> 'Add text to be used in this link.',
								),
							'_soapbox_link_3_class' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'title' 	=> $this->name.' Link 3 Classes',						
									'desc' 		=> 'Add CSS classes to add to this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
								),
							
					);

					$post_types = array($this->settings['posttype']);
					
					$type_metapanel_settings = array(
					 						'id' 		=> 'soapbox-metapanel',
					 						'name' 		=> "Soapbox Section Options",
					 						'posttype' 	=> $post_types, 
					 						'hide_tabs'	=> true
					 					);
					 
					
					
					global $boxes_meta_panel;
					$boxes_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );	
						

					$type_metatab_settings = array(
							'id' 		=> 'soapbox-type-metatab',
							'name' 		=> "Soapbox Setup Options",
							'icon' 		=> $this->icon,
					);

					$boxes_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );

						
					$metatab_array = array(

								'_soapbox_set' => array(
									'version' 		=> 'pro',
									'type' 			=> 'select_taxonomy',
									'taxonomy_id'	=> "box-sets",				
									'title' 		=> 'Select Box-Set To Use For Soapbox Section',
									'desc' 			=> 'If you are using the soapbox section, select the box-set you would it to use on this page.'
								), 
								'_soapbox_items' => array(
									'type' 		=> 'text',
									'size'		=> 'small',
									'label'		=> 'Enter max number of soapboxes',
									'title' 	=> 'Soapbox Posts Limit',					
									'desc' 		=> 'Add the limit or soapboxes that can be shown on this page. Default is 10.',
									),
								'_soapbox_height_media' => array(
									'version' 	=> 'pro',
									'type' 		=> 'text',
									'size'		=> 'small',
									'label'		=> 'Enter height in pixels',
									'title' 	=> 'Soapbox Media Height (in Pixels)',
									'desc' 		=> 'For the "soapboxes" to line up correctly, the height of the media needs to be set. Add it here in pixels.'
									), 
								'_soapbox_link_1_text' => array(
									'type' 		=> 'text',
									'title' 	=> $this->name.' Link 1 Text',						
									'desc' 		=> 'Add text to be used in this link. Can be overridden in the box meta options.',
									),	
								'_soapbox_link_1_class' => array(
									'type' 			=> 'text',
									'title' 	=> $this->name.' Link 1 Classes',						
									'desc' 		=> 'Add CSS classes for this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
									),
								'_soapbox_link_2_text' => array(
									'type' 			=> 'text',
									'title' 	=> $this->name.' Link 2 Text',			
									'desc' 		=> 'Add text to be used in this link. Can be overridden in the box meta options.',
									),
								'_soapbox_link_2_class' => array(
									'type' 			=> 'text',
									'title' 	=> $this->name.' Link 2 Classes',			
									'desc' 		=> 'Add CSS classes for this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
									),
								'_soapbox_link_3_text' => array(
									'type' 			=> 'text',
									'title' 	=> $this->name.' Link 3 Text - Callout Link',						
									'desc' 		=> 'Add text to be used in this link. Can be overridden in the box meta options.',
									),
								'_soapbox_link_3_class' => array(
									'type' 			=> 'text',
									'title' 	=> $this->name.' Link 3 Classes',						
									'desc' 		=> 'Add CSS classes for this link.  <strong>Tip:</strong> add <strong>"soapbox_callout"</strong> for a blue link or <strong>"fancybox"</strong> to use with the fancybox plugin.',
									),
						
							);

						$metatab_settings = array(
								'id' => 'soapbox_meta',
								'name' => "Soapbox Section",
								'icon' => $this->icon
							);
						
						
						register_metatab($metatab_settings, $metatab_array);
	}

	function section_template() {    
		global $post;
		$perline = 2;
		$count = $perline;
		
		$soapbox_height_media = ( get_pagelines_meta('_soapbox_height_media', $post->ID) ) ? get_pagelines_meta('_soapbox_height_media', $post->ID) : 200; 
		
		// Link text on parent page
		$soapbox_link_text[1] = ( get_pagelines_meta('_soapbox_link_1_text', $post->ID) ) ? get_pagelines_meta('_soapbox_link_1_text', $post->ID) : 'Link 1'; 
		$soapbox_link_text[2] = ( get_pagelines_meta('_soapbox_link_2_text', $post->ID) ) ? get_pagelines_meta('_soapbox_link_2_text', $post->ID) : 'Link 2'; 
		$soapbox_link_text[3] = ( get_pagelines_meta('_soapbox_link_3_text', $post->ID) ) ? get_pagelines_meta('_soapbox_link_3_text', $post->ID) : 'Link 3'; 
		
		$soapbox_link_class[1] = ( get_pagelines_meta('_soapbox_link_1_class', $post->ID) ) ? get_pagelines_meta('_soapbox_link_1_class', $post->ID) : ''; 
		$soapbox_link_class[2] = ( get_pagelines_meta('_soapbox_link_2_class', $post->ID) ) ? get_pagelines_meta('_soapbox_link_2_class', $post->ID) : ''; 
		$soapbox_link_class[3] = ( get_pagelines_meta('_soapbox_link_3_class', $post->ID) ) ? get_pagelines_meta('_soapbox_link_3_class', $post->ID) : ''; 
	
		
	?>		
	<div class="dcol_container_<?php echo $perline;?> fix">
	<?php 
			// Let's Do This...
		$theposts = $this->get_section_posts();
		$boxes = (is_array($theposts)) ? $theposts : array();
		
		foreach($boxes as $post) : setup_postdata($post);  ?>

				<div class="dcol_<?php echo $perline;?> dcol">
					<div class="dcol-pad">	
						<?php if(get_post_meta($post->ID, 'the_box_icon', true)):?>
								<div class="fboxgraphic" style="line-height: <?php echo $soapbox_height_media;?>px; height: <?php echo $soapbox_height_media;?>px">
									
									<?php if(get_post_meta($post->ID, 'the_box_icon_link', true)):?>
									<a href="<?php echo get_post_meta($post->ID, 'the_box_icon_link', true);?>" alt="">
									<?php endif;?>
									
									<img src="<?php echo get_post_meta($post->ID, 'the_box_icon', true);?>" >
									
									<?php if(get_post_meta($post->ID, 'the_box_icon_link', true)):?>
									</a>
									<?php endif;?>
					            </div>
						<?php endif;?>

							<div class="fboxinfo fix">
									<div class="fboxtitle"><h3><?php the_title(); ?></h3></div>
									<div class="fboxtext">
										<?php the_content(); ?>
									</div>
								<div class="soapbox-links">
									<?php for( $i = 1; $i <= 3; $i++):?>
											<?php if(get_post_meta($post->ID, '_soapbox_link_'.$i, true)):
												$link_text = ( get_post_meta($post->ID,'_soapbox_link_'.$i.'_text', true) ) ? get_post_meta($post->ID,'_soapbox_link_'.$i.'_text', true) : $soapbox_link_text[$i]; 
												$link_class = ( get_post_meta($post->ID, '_soapbox_link_'.$i.'_class', true) ) ?  get_post_meta($post->ID, '_soapbox_link_'.$i.'_class', true) : $soapbox_link_text[$i]; 
											?>
											<a class="soapbox_link <?php echo 'sblink_'.$i.' '.$link_class?>" href="<?php echo get_post_meta($post->ID, '_soapbox_link_'.$i, true);?>">
												<span class="soapbox_link-pad"><span class="soapbox_arrow"><?php echo $link_text; ?></span></span>
											</a>
											<?php endif;?>
									<?php endfor; ?>
									<?php pagelines_register_hook( 'pagelines_soapbox_links', $this->id ); // Hook ?>
									
									<?php edit_post_link(__('<small>[Edit Box]</small>', 'pagelines'), '', '');?>
								</div>
							</div>
							<?php pagelines_register_hook( 'pagelines_soapbox_inside_bottom', $this->id ); // Hook ?>
					</div>
				</div>
				<?php $end = ($count+1) / $perline;  if(is_int($end)):?>
					<div class="clear"></div>
				<?php endif; $count++;?>
		<?php endforeach;?>
	</div>
	<div class="clear"></div>
<?php	}

	
	function get_section_posts(){
		global $post;
		
		if((!isset($this->the_section_posts) || !is_array($this->the_section_posts)) && isset($post)){
			
			$query_args = array('post_type' => $this->settings['posttype'], 'orderby' =>'ID');
		
			if( get_pagelines_meta('_soapbox_set', $post->ID ) ){
				$query_args = array_merge($query_args, array( 'box-sets' => get_post_meta($post->ID, '_soapbox_set', true) ) );
			} elseif (pagelines_non_meta_data_page() && pagelines_option('soapbox_default_tax')){
				$query_args = array_merge($query_args, array( 'box-sets' => pagelines_option('soapbox_default_tax') ) );
			}
			
			$num_items = (pagelines_option('_soapbox_items', $post->ID)) ? pagelines_option('_soapbox_items', $post->ID) : 10;
			
			$query_args = array_merge($query_args, array( 'showposts' => $num_items ) );

	
		
			$section_query = new WP_Query( $query_args );
		
		 	$this->the_section_posts = $section_query->posts;
			
		 	if(is_array($this->the_section_posts)) return $this->the_section_posts;
			else return array();
			
		} elseif(isset($post)) {
			return $this->the_section_posts;
		}
	
	}

	
	function section_options($optionset = null, $location = null) {
		
		if($optionset == 'box_settings' && $location == 'bottom'){
			return array(
					'soapbox_default_tax' => array(
							'default' 		=> 'default-boxes',
							'version'		=> 'pro',
							'taxonomy_id'	=> 'box-sets',
							'type' 			=> 'select_taxonomy',
							'inputlabel' 	=> 'Select Soapbox Posts/404 Box-Set',
							'title' 		=> 'Posts Page and 404 Soapbox Box-Set',
							'shortexp' 		=> "Posts pages and similar pages (404) will use this Box-Set ID to source Soapboxes",
							'exp' 			=> "Posts pages and 404 pages in WordPress don't support meta data so you need to assign a set here. (If you want to use 'soapboxes' on these pages.)",
				)
			);
			
		}
	}

// End of Section Class //
}

