<?php
/*
	Section: PageLines Banners	
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates banners, great for product tours.
	Class Name: PageLinesBanners
*/

class PageLinesBanners extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('PageLines Banners', 'pagelines');
		$id = 'banners';
		
		$default_settings = array(
			'description' 	=> 'Creates "banners" (image on one side text on the other). Great for product tours, portfolios, etc...',
			'icon'			=> PL_ADMIN_ICONS . '/banners.png', 
			'version'		=> 'pro',
			'cloning'		=> true
			
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
			Create Custom Post Type 
		*/
			$args = array(
					'label' 			=> __('Banners', 'pagelines'),  
					'singular_label' 	=> __('Banner', 'pagelines'),
					'description' 		=> 'For creating banners in banner section layouts.',
					'menu_icon'			=> $this->icon
				);
			$taxonomies = array(
				"banner-sets" => array(	
						"label" => __('Banner Sets', 'pagelines'), 
						"singular_label" => __('Banner Set', 'pagelines'), 
					)
			);
			$columns = array(
				"cb" => "<input type=\"checkbox\" />",
				"title" => "Title",
				"banner-description" => "Text",
				"banner-media" => "Media",
				"banner-sets" => "Banner Sets"
			);
		
			$column_value_function = 'banner_column_display';
		
			$this->post_type = new PageLinesPostType($this->id, $args, $taxonomies, $columns, $column_value_function);
		
				/* Set default posts if none are present */
				$this->post_type->set_default_posts('pagelines_default_banners');


		/*
			Meta Options
		*/
		
		/*
			Create meta fields for the post type
		*/
			$type_meta_array = array(
				
				'the_banner_image' 	=> array(
						'version' => 'pro',
						'type' => 'image_upload',					
						'title' => 'Banner Media',
						'desc' => 'Upload an image for the banner.'
					),
				'the_banner_media' 		=> array(
						'version' => 'pro',
						'type' => 'textarea',					
						'title' => 'Banner Media',
						'desc' => 'Add HTML Media for the banner, e.g. Youtube embed code. This option is used if there is no image uploaded.'
					),
				'banner_text_width' => array(
						'version' 	=> 'pro',
						'type' 		=> 'text',		
						'size'		=> 'small',			
						'title'		=> 'Banner Text Width (In %)',
						'desc' 		=> 'Set the width of the text area as a percentage of full content width.  The media area will fill the rest.'
					),
				'banner_align' => array(
					'version' => 'pro',
					'type' => 'select',
					'selectvalues'	=> array(
							'banner_right'	=> array("name" => "Banner Right"),
							'banner_left'	=> array("name" => "Banner Left")
						), 
					'title' => 'Banner Alignment',				
					'desc' => 'Put the media on the right or the left?',
					
				),
				'banner_text_padding' => array(
					'version' 	=> 'pro',
					'type' 		=> 'text',
					'size'		=> 'small',					
					'title' 	=> 'Banner Text Padding',
					'desc' 		=> '(optional) Set the padding for the text area. Use CSS shorthand, for example:<strong> 25px 30px 25px 35px</strong>; for top, right, bottom, then left padding.'
					
				),
			);

			$post_types = array($this->id); 

			$type_metapanel_settings = array(
					'id' 		=> 'banner-metapanel',
					'name' 		=> "Banner Setup Options",
					'posttype' 	=> $post_types, 
					'hide_tabs'	=> true
				);

			$type_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );
			
			$type_metatab_settings = array(
				'id' 		=> 'banner-type-metatab',
				'name' 		=> "Banner Setup Options",
				'icon' 		=> $this->icon,
			);

			$type_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
		
	}


	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(
				
				'banner_items' => array(
					'default'	=> '5',
					'version' 	=> 'pro',
					'type' 		=> 'text_small',		
					'title' 	=> 'Max Number of Banners',
					'desc' 		=> 'Select the default max number of banners.',
					'inputlabel'=> 'Number of Banners',
					'exp'		=> 'This number will be used as the max number of banners to use in this section.'
				),
				'banner_set' => array(
						'default' 		=> null,
						'version'		=> 'pro',
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> 'banner-sets',
						'desc'		 	=> 'Select Default Banner Set',
						'inputlabel' 	=> 'Select Default Banner Set',
						'title' 		=> 'Default Banner Set',
						'shortexp' 		=> "Select the category (taxonomy) of Banner posts to show",
						'exp' 			=> "Select the taxonomy/category of banners to show on this page.",

				),

			);

		$metatab_settings = array(
				'id' 		=> 'banner_page_meta',
				'name' 		=> "Banner Section",
				'icon' 		=>  $this->icon,
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);

		register_metatab($metatab_settings, $metatab_array);
	}

   function section_template( $clone_id ) {    
	
		global $post, $pagelines_ID; 
		
		// Option Settings
			$oset = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);
		
		// Options
			$set = (ploption('banner_set', $oset)) ? ploption('banner_set', $oset) : null;
			$limit = (ploption('banner_items', $oset)) ? ploption('banner_items', $oset) : null;
		
		// Actions
			$b = $this->load_pagelines_banners($set, $limit);
			$this->draw_banners($b, 'banners ' . $set);
	}

	function draw_banners($b, $class = ""){ ?>		
		<div class="banner_container fix <?php echo $class;?>">
	<?php 
		
		foreach($b as $bpost) : 
			$oset = array('post_id' => $bpost->ID);
			
			$banner_text_width = (ploption('banner_text_width', $oset)) ? ploption('banner_text_width', $oset) : 50;
			$banner_media_width = 100 - $banner_text_width; // Math
			$banner_align = (ploption('banner_align', $oset)) ? ploption('banner_align', $oset) : 'banner_left';
			$banner_text_padding = (ploption('banner_text_padding', $oset)) ? sprintf('padding:%s;', ploption('banner_text_padding', $oset)) : "padding: 20px 60px"; 
			
			
?>		<div class="banner-area <?php echo $banner_align;?>">
				<div class="banner-text" style="width:<?php echo $banner_text_width; ?>%;">
					<div class="banner-text-pad" style="<?php echo $banner_text_padding;?>">
							<div class="banner-title"><h2><?php echo do_shortcode($bpost->post_title); ?></h2></div>
							<div class="banner-content">	
								<?php 
									echo blink_edit($bpost->ID); 	
									echo apply_filters( 'the_content', do_shortcode($bpost->post_content) ); 
								?>
							</div>
					</div>
				</div>
				<div class="banner-media" style="width:<?php echo $banner_media_width; ?>%;" >
					<div class="banner-media-pad">
						<?php echo apply_filters( 'the_content', self::_get_banner_media( $oset ) );?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		<?php endforeach;?>
		</div><div class="clear"></div>
<?php }

	
	function _get_banner_media( $oset ){
		
			
			if(plmeta('the_banner_image', $oset))
				$banner_media = '<img src="'.plmeta('the_banner_image', $oset).'" alt="'.get_the_title().'" />';
			elseif(plmeta('the_banner_media', $oset))
				$banner_media = do_shortcode( plmeta('the_banner_media', $oset) );
			else 
				$banner_media = '';
			
			// Filter output
			return apply_filters('pl_banner_image', $banner_media, $oset);
	}
	
	
	static function load_pagelines_banners($set = null, $limit = null){
		$query = array();
		
		$query['post_type'] = 'banners'; 
		$query['orderby'] 	= 'ID'; 
		
		if(isset($set)) 
			$query['banner-sets'] = $set; 
			
		if(isset($limit)) 
			$query['showposts'] = $limit; 

		$q = new WP_Query($query);
		
		if(is_array($q->posts)) 
			return $q->posts;
		else 
			return array();
	
	}

	
// End of Section Class //
}

function banner_column_display($column){
	global $post;
	
	switch ($column){
		case "banner-description":
			the_excerpt();
			break;
		case "banner-media":
			if(get_post_meta($post->ID, 'the_banner_image', true )){
			
				echo '<img src="'.get_post_meta($post->ID, 'the_banner_image', true ).'" style="width: 80px; margin: 10px; border: 1px solid #ccc; padding: 5px; background: #fff" />';	
			}
			
			break;
		case "banner-sets":
			echo get_the_term_list($post->ID, 'banner-sets', '', ', ','');
			break;
	}
}

		
function pagelines_default_banners($post_type){
	
	$d = array_reverse(get_default_banners());
	
	foreach($d as $dp){
		// Create post object
		$default_post = array();
		$default_post['post_title'] = $dp['title'];
		$default_post['post_content'] = $dp['text'];
		$default_post['post_type'] = $post_type;
		$default_post['post_status'] = 'publish';
		
		$newPostID = wp_insert_post( $default_post );

		wp_set_object_terms($newPostID, 'default-banners', 'banner-sets');
	
		if(isset($dp['image'])) 
			update_post_meta($newPostID, 'the_banner_image', $dp['image']);
			
		if(isset($dp['media'])) 
			update_post_meta($newPostID, 'the_banner_media', $dp['media']);
	
		if(isset($dp['set'])) 
			wp_set_object_terms($newPostID, $dp['set'], 'banner-sets', true);
		
		if(isset($dp['width']))
			update_post_meta($newPostID, 'banner_text_width', $dp['width']);
		
		if(isset($dp['align']))
			update_post_meta($newPostID, 'banner_align', $dp['align']);
		
		if(isset($dp['pad']))
			update_post_meta($newPostID, 'banner_text_padding', $dp['pad']);

	}
}

function get_default_banners(){
	return apply_filters('pagelines_default_banners', array());
}