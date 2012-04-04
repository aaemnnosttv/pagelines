<?php
/*
	Section: Boxes
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates boxes and box layouts
	Class Name: PageLinesBoxes
	Workswith: templates, main, header, morefoot
	Cloning: true
	Edition: pro
*/

/**
 * Boxes Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesBoxes extends PageLinesSection {

	var $taxID = 'box-sets';
	var $ptID = 'boxes';

	/**
	* PHP that always loads no matter if section is added or not.
	*/
	function section_persistent(){
		
		$this->post_type_setup();
		
		$this->post_meta_setup();
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function post_type_setup(){
			$args = array(
					'label' 			=> __('Boxes', 'pagelines'),  
					'singular_label' 	=> __('Box', 'pagelines'),
					'description' 		=> __( 'For creating boxes in box type layouts.', 'pagelines'),
					'menu_icon'			=> $this->icon
				);
			$taxonomies = array(
				$this->taxID => array(	
						'label' => __('Box Sets', 'pagelines'), 
						'singular_label' => __('Box Set', 'pagelines'), 
					)
			);
			$columns = array(
				'cb'	 		=> "<input type=\"checkbox\" />",
				'title' 		=> 'Title',
				'bdescription' 	=> 'Text',
				'bmedia' 		=> 'Media',
				$this->taxID 	=> 'Box Sets'
			);
		
			$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array(&$this, 'column_display'));
		
			$this->post_type->set_default_posts( 'pagelines_default_boxes', $this); // Default 
	}


	/**
	*
	* @TODO document
	*
	*/
	function post_meta_setup(){
		
			$type_meta_array = array(
				'the_box_icon' 		=> array(
						'version' 	=> 'pro',
						'type' 		=> 'image_upload',					
						'title' 	=> __( 'Box Image', 'pagelines'),
						'shortexp' 	=> __( 'Upload an image for the box.', 'pagelines'),
						'exp'		=> __( 'Depending on your settings this image will be used as an icon, or splash image; so desired size may vary.', 'pagelines')
					), 
				'the_box_icon_link'		=> array(
						'version' => 'pro',
						'type' => 'text',					
						'title' => __( 'Box Link (Optional)', 'pagelines'),
						'shortexp' => __( 'Make the box image and title clickable by adding a link here (optional)...', 'pagelines')
					), 
				'box_class' => array(
					'version'		=> 'pro',
					'default'		=> '',
					'type' 			=> 'text',
					'size'			=> 'small',
					'inputlabel' 	=> __( 'Boxes Custom Class', 'pagelines'),
					'title' 		=> __( 'Custom CSS class', 'pagelines'),
					'shortexp' 		=> __( 'Add a custom CSS class to this box only.', 'pagelines'),
				),
				'box_more_text' => array(
					'version'		=> 'pro',
					'default'		=> '',
					'type' 			=> 'text',
					'size'			=> 'small',
					'inputlabel' 	=> __( 'More Link Text', 'pagelines'),
					'title' 		=> __( 'More Link Text', 'pagelines'),
					'shortexp' 		=> __( 'Enter text for this boxes "more" link.', 'pagelines'),
					'exp'			=> __( 'If this option is not set here, in page meta, or meta defaults, then the more link will not show.', 'pagelines')
				),
			);

			$post_types = array($this->id); 
			
			$type_metapanel_settings = array(
					'id' 		=> 'boxes-metapanel',
					'name' 		=> THEMENAME.' Box Options',
					'posttype' 	=> $post_types,
				);
			
			global $boxes_meta_panel;
			
			$boxes_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );
			
			$type_metatab_settings = array(
				'id' 		=> 'boxes-type-metatab',
				'name' 		=> 'Box Setup Options',
				'icon' 		=> $this->icon,
			);

			$boxes_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$tab = array(
					'box_set' => array(
						'version' 		=> 'pro',
						'default'		=> 'default-boxes',
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> $this->taxID,				
						'title'		 	=> __( 'Select Box Set To Show', 'pagelines'),
						'shortexp' 			=> __( 'If you are using the box section, select the box set you would like to show on this page.', 'pagelines')
					), 
					'box_col_number' => array(
						'type' 			=> 'count_select',
						'default'		=> '3',
						'count_number'	=> '5', 
						'count_start'	=> '1',
						'inputlabel' 	=> __( 'Number of Feature Box Columns', 'pagelines'),
						'title' 		=> __( 'Box Columns', 'pagelines'),
						'inputlabel' 		=> __( "Select the number of columns to show boxes in.", 'pagelines'),
						'shortexp' 			=> __( "The number you select here will be the number of boxes listed in a row on a page.", 'pagelines'),
						'exp'				=> __( "Note: This won't work on the blog page (use the global option).", 'pagelines')
					), 
					'box_thumb_type' => array(
						'version' 	=> 'pro',
						'type' 		=> 'radio',
						'default'	=> 'inline_thumbs',
						'selectvalues'	=> array(
								'inline_thumbs'	=> array('name' => __( 'Image At Left', 'pagelines') ),
								'top_thumbs'	=> array('name' => __( 'Image On Top', 'pagelines') ), 
								'only_thumbs'	=> array('name' => __( "Only The Image, No Text", 'pagelines') )
							), 
						'title' => __( 'Box Thumb Style', 'pagelines'),				
						'shortexp' => __( 'Choose between thumbs on left and thumbs on top of boxes.', 'pagelines'),
						
					),
					'box_thumb_size' => array(
						'version'		=> 'pro',
						'default'		=> '64',
						'type' 			=> 'text_small',
						'size'			=> 'small',
						'title' 		=> __( 'Box Icon Size (in Pixels)', 'pagelines'),
						'inputlabel' 		=> __( 'Enter the icon size in pixels', 'pagelines'),
						'shortexp' 			=> __( "Select the default icon size in pixels, set the images when creating new boxes.", 'pagelines')
					),
					'box_items' => array(
						'version'		=> 'pro',
						'default'		=> '6',
						'type' 			=> 'text_small',
						'size'			=> 'small',
						'inputlabel' 	=> __( 'Maximum Boxes To Show On Page', 'pagelines'),
						'title' 		=> __( 'Max Number of Boxes', 'pagelines'),
						'shortexp' 		=> __( "Select the max number of boxes to show on this page (overrides default).", 'pagelines'),
					),
					'box_class' => array(
						'version'		=> 'pro',
						'default'		=> '',
						'type' 			=> 'text',
						'size'			=> 'small',
						'inputlabel' 	=> __( 'Add custom css class to these boxes', 'pagelines'),
						'title' 		=> __( 'Custom CSS class', 'pagelines'),
						'shortexp' 		=> __( 'Add a custom CSS class to this set of boxes.', 'pagelines'),
					),
					'box_more_text' => array(
						'version'		=> 'pro',
						'default'		=> '',
						'type' 			=> 'text',
						'size'			=> 'small',
						'inputlabel' 	=> __( 'More Link Text', 'pagelines'),
						'title' 		=> __( 'More Link Text', 'pagelines'),
						'shortexp' 		=> __( 'Enter text for "more" links on linked box elements for this page.', 'pagelines'),
						'exp'			=> __( 'If this option is blank (and not set in defaults), no more text will show.<br/><br/> This option can be overridden in individual box settings.', 'pagelines')
					),
					'box_orderby' => array(
							'default' => 'ID',
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => array(
								'ID' 		=> array('name' => __( 'Post ID (default)', 'pagelines') ),
								'title' 	=> array('name' => __( 'Title', 'pagelines') ),
								'date' 		=> array('name' => __( 'Date', 'pagelines') ),
								'modified' 	=> array('name' => __( 'Last Modified', 'pagelines') ),
								'rand' 		=> array('name' => __( 'Random', 'pagelines') ),							
							),
							'inputlabel' => __( 'Select sort order', 'pagelines'),
							'title' => __( 'Boxes sort order', 'pagelines'),
							'shortexp' => __( 'How will the boxes be sorted.', 'pagelines'),
							'exp' => __( "By default the boxes section will sort by post ID.", 'pagelines')
						),
						
					'box_order' => array(
							'default' => 'DESC',
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => array(
								'DESC' 		=> array('name' => __( 'Descending', 'pagelines') ),
								'ASC' 		=> array('name' => __( 'Ascending', 'pagelines') ),
							),
							'inputlabel'=> __( 'Select sort order', 'pagelines'),
							'title' 	=> __( 'Boxes sort order', 'pagelines'),
							'shortexp'	=> __( 'How will the boxes be sorted.', 'pagelines'),
							'exp'		=> __( "By default the boxes will be in descending order.", 'pagelines')
						),
				);

			$tab_settings = array(
					'id' 		=> 'fboxes_meta',
					'name' 		=> 'Boxes',
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab($tab_settings, $tab);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id = null ) {    
		
		if( post_password_required() )
			return;		
		
		// Options
			$per_row = ( ploption( 'box_col_number', $this->oset) ) ? ploption( 'box_col_number', $this->oset) : 3; 
			$box_set = ( ploption( 'box_set', $this->oset ) ) ? ploption( 'box_set', $this->oset ) : null;
			$box_limit = ploption( 'box_items', $this->oset );
			$this->thumb_type = ( ploption( 'box_thumb_type', $this->oset) ) ? ploption( 'box_thumb_type', $this->oset) : 'inline_thumbs';	
			$this->thumb_size = ( ploption('box_thumb_size', $this->oset) ) ? ploption('box_thumb_size', $this->oset) : 64;
			$class = ( ploption( 'box_class', $this->oset ) ) ? ploption( 'box_class', $this->oset ) : null;
			
		// Actions	
			// Set up the query for this page
				$orderby = ( ploption('box_orderby', $this->oset) ) ? ploption('box_orderby', $this->oset) : 'ID';
				$order = ( ploption('box_order', $this->oset) ) ? ploption('box_order', $this->oset) : 'DESC';
				$params = array( 'orderby'	=> $orderby, 'order' => $order, 'post_type'	=> $this->ptID );
				$params[ 'showposts' ] = ( ploption('box_items', $this->oset) ) ? ploption('box_items', $this->oset) : $per_row;
				$params[ $this->taxID ] = ( ploption( 'box_set', $this->oset ) ) ? ploption( 'box_set', $this->oset ) : null;

				$q = new WP_Query( $params );
				
				if(empty($q->posts)){
					echo setup_section_notify( $this, 'Add Box Posts To Activate.', admin_url('edit.php?post_type='.$this->ptID), 'Add Posts' );
					return;
				}
			
			// Script 
				//printf('<script type="text/javascript">jQuery(document).ready(function(){ blocks(".box-media-pad", "maxheight");});</script>');
			
			
			
			// Grid Args
				$args = array( 'per_row' => $per_row, 'callback' => array(&$this, 'draw_boxes'), 'class' => $class );

			// Call the Grid
				printf('<div class="fboxes fix">%s</div>', grid( $q, $args ));
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function draw_boxes($p, $args){ 

		setup_postdata($p); 
		
		$oset = array('post_id' => $p->ID);
	 	$box_link = plmeta('the_box_icon_link', $oset);
		$box_icon = plmeta('the_box_icon', $oset);
		
		$class = ( plmeta( 'box_class', $oset ) ) ? plmeta( 'box_class', $oset ) : null;
		
		$image = ($box_icon) ? self::_get_box_image( $p, $box_icon, $box_link, $this->thumb_size, $this->thumb_type) : '';
	
		$title_text = ($box_link) ? sprintf('<a href="%s">%s</a>', $box_link, $p->post_title ) : $p->post_title; 
	
		$title = do_shortcode(sprintf('<div class="fboxtitle"><h3>%s</h3></div>', $title_text));

		if(plmeta('box_more_text', $oset)){
			$more_text = plmeta('box_more_text', $oset);
		} elseif(ploption('box_more_text', $this->oset)){
			$more_text = ploption('box_more_text', $this->oset);
		}else 
			$more_text = false;
		
		$more_link = ($box_link && $more_text) ? sprintf('<span class="fboxmore-wrap"><a class="fboxmore" href="%s">%s</a></span>', $box_link, $more_text) : '';
		
		$more_link = apply_filters('box_more_link', $more_link);
		
		$content = sprintf('<div class="fboxtext">%s %s %s</div>', do_shortcode($p->post_content), pledit( $p->ID ), $more_link);
			
		$info = ($this->thumb_type != 'only_thumbs') ? sprintf('<div class="fboxinfo fix bd">%s%s</div>', $title, $content) : '';				
				
		return sprintf('<div id="%s" class="fbox %s"><div class="media box-media %s"><div class="blocks box-media-pad">%s%s</div></div></div>', 'fbox_'.$p->ID, $class, $this->thumb_type, $image, $info);
	
	}

	

	/**
	*
	* @TODO document
	*
	*/
	function _get_box_image( $bpost, $box_icon, $box_link = false, $box_thumb_size = 65, $thumb_type){
			global $pagelines_ID;
			
			if($this->thumb_type == 'inline_thumbs'){
				$image_style = 'width: 100%';
				$wrapper_style = sprintf('width: 22%%; max-width:%dpx', $box_thumb_size);
				$wrapper_class = 'fboxgraphic img';
			} else {
				$image_style = sprintf('width: 100%%; max-width:%dpx', $box_thumb_size);
				$wrapper_style = '';
				$wrapper_class = 'fboxgraphic';
			}
			
			// Make the image's tag with url
			$image_tag = sprintf('<img src="%s" alt="%s" style="%s" />', $box_icon, esc_html($bpost->post_title), $image_style);
			
			// If link for box is set, add it
			$image_output = ( $box_link ) ? sprintf('<a href="%s" title="%s">%s</a>', $box_link, esc_html($bpost->post_title), $image_tag ) : $image_tag;
			
			$wrapper = sprintf('<div class="%s" style="%s">%s</div>', $wrapper_class, $wrapper_style, $image_output );
			
			// Filter output
			return apply_filters('pl_box_image', $wrapper, $bpost->ID);
	}

	

		/**
		*
		* @TODO document
		*
		*/
		function pagelines_default_boxes($post_type){

			$d = array_reverse( $this->get_default_fboxes() );

			foreach($d as $dp){
				// Create post object
				$default_post = array();
				$default_post['post_title'] = $dp['title'];
				$default_post['post_content'] = $dp['text'];
				$default_post['post_type'] = $post_type;
				$default_post['post_status'] = 'publish';
				if ( defined( 'ICL_LANGUAGE_CODE' ) )
					$default_post['icl_post_language'] = ICL_LANGUAGE_CODE;
				$newPostID = wp_insert_post( $default_post );

				if(isset($dp['media']))
					update_post_meta($newPostID, 'the_box_icon', $dp['media']);

				wp_set_object_terms($newPostID, 'default-boxes', $this->taxID );

				// Add other default sets, if applicable.
				if(isset($dp['set']))
					wp_set_object_terms($newPostID, $dp['set'], $this->taxID, true);

			}
		}


		/**
		*
		* @TODO document
		*
		*/
		function get_default_fboxes(){
			$default_boxes[] = array(
			        				'title' => 'Drag&amp;Drop Control',
					        		'text' 	=> 'Control the structure of your site using drag and drop functionality. Pro web design has never been easier.',
									'media' => $this->base_url.'/images/fbox3.png'
			    				);

			$default_boxes[] = array(
			        				'title' => 'PageLines Framework',
					        		'text' 	=> "The world's first ever drag-and-drop framework designed for professional websites. Build beautiful sites faster.",
									'media' => $this->base_url.'/images/fbox2.png'
			    				);

			$default_boxes[] = array(
			        				'title'	=> 'Add-On Marketplace',
			        				'text' 	=> "Load up your own sections, themes and plugins using PageLines' one of a kind extension marketplace.", 
									'media' => $this->base_url.'/images/fbox1.png'
			    				);

			return apply_filters('pagelines_default_boxes', $default_boxes);
		}
	

	/**
	*
	* @TODO document
	*
	*/
	function column_display($column){
		global $post;

		switch ($column){
			case 'bdescription':
				the_excerpt();
				break;
			case 'bmedia':
				if(get_post_meta($post->ID, 'the_box_icon', true ))
					echo '<img src="'.get_post_meta($post->ID, 'the_box_icon', true ).'" style="max-width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff" />';	
	
				break;
			case $this->taxID:
				echo get_the_term_list($post->ID, 'box-sets', '', ', ','');
				break;
		}
	}
}