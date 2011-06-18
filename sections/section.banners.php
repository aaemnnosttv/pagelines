<?php
/*

	Section: PageLines Banners	
	Author: Andrew Powers
	Description: Creates banners, great for product tours.
	Version: 1.0.0
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
			'failswith'		=> array('404', 'posts'), 
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

					
					$metatab_array = array(
							
							'banner_set' => array(
								'version' => 'pro',
								'type' => 'select_taxonomy',

								'taxonomy_id'	=> "banner-sets",				
								'title' => 'Select Banner Set To Show',
								'desc' => 'If you are using the Banner section, select the banner set you would like to show on this page.'
							),
							'banner_items' => array(
								'version' 	=> 'pro',
								'type' 		=> 'text',
								'size'		=> 'small',				
								'title' 	=> 'Max Number of Banners',
								'desc' 		=> 'Enter the max number of banners to show on this page. If left blank, the number of posts selected under settings > "reading" will be used.'
							),

						);

					$metatab_settings = array(
							'id' => 'banner_page_meta',
							'name' => "Banner Section",
							'icon' =>  $this->icon,
						);

					register_metatab($metatab_settings, $metatab_array);
						
	}

   function section_template() {    
	
		
		global $post, $pagelines_ID; 
		$current_post = $post;

		$set = (pagelines_option('banner_set', $pagelines_ID)) ? pagelines_option('banner_set', $pagelines_ID) : null;
		$limit = (pagelines_option('banner_items', $pagelines_ID)) ? pagelines_option('banner_items', $pagelines_ID) : null;
		$b = $this->load_pagelines_banners($set, $limit);
		
		$this->draw_banners($b, 'banners ' . $set);
	}

	function draw_banners($b, $class = ""){ ?>		
		<div class="banner_container fix <?php echo $class;?>">
	<?php 
		
		foreach($b as $post) : setup_postdata($post); $custom = get_post_custom($post->ID); 

			$banner_text_width = (pagelines('banner_text_width', $post->ID)) ? pagelines('banner_text_width', $post->ID) : 50;
			$banner_media_width = 100 - $banner_text_width; // Math
			$banner_align = (get_pagelines_meta('banner_align', $post->ID)) ? get_pagelines_meta('banner_align', $post->ID) : 'banner_left';
			$banner_text_padding = (get_pagelines_meta('banner_text_padding', $post->ID)) ? "padding:".get_pagelines_meta('banner_text_padding', $post->ID).";" : "padding: 20px 60px"; 
?>
			<div class="banner-area <?php echo $banner_align;?>">
				<div class="banner-text" style="width:<?php echo $banner_text_width; ?>%;">
					<div class="banner-text-pad" style="<?php echo $banner_text_padding;?>">
							<div class="banner-title"><h2><?php echo do_shortcode($post->post_title); ?></h2></div>
							<div class="banner-content">
								<?php echo do_shortcode($post->post_content); ?>
								<?php edit_post_link(__('[Edit Banner]', 'pagelines'), '', '', $post->ID);?>
							</div>

					</div>
				</div>
				<div class="banner-media" style="width:<?php echo $banner_media_width; ?>%;" >
					<div class="banner-media-pad">
						<?php echo self::_get_banner_media( $post );?>
					</div>
				</div>
				<div class="clear"></div>
			</div>

		<?php endforeach;?>
		</div>
		<div class="clear"></div>
<?php }

	
	function _get_banner_media( $bpost ){
			global $pagelines_ID;
			
			if(get_pagelines_meta('the_banner_image', $bpost->ID)){
				$banner_media = '<img src="'.get_pagelines_meta('the_banner_image', $bpost->ID).'" alt="'.get_the_title().'" />';
			} elseif(get_pagelines_meta('the_banner_media', $bpost->ID)){
				$banner_media = get_pagelines_meta('the_banner_media', $bpost->ID);
			} else { $banner_media = ''; }
			
			// Filter output
			return apply_filters('pl_banner_image', $banner_media, $bpost->ID);
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

	function section_options($optionset = null, $location = null) {

		if($optionset == 'section_options' && $location == 'bottom'){
			return array(
					'banner_items' => array(
						'default'	=> '5',
						'version' 	=> 'pro',
						'type' 		=> 'text_small',		
						'title' 	=> 'Default Max Number of Banners',
						'desc' 		=> 'Select the default max number of banners. Can be overridden on ind. pages and posts.',
						'inputlabel'=> 'Enter The Max Number Of Banners',
						'exp'		=> 'This number will be used as the max number of banners to use on individual pages. It can be overridden using meta options.'
					),
					'banner_set' => array(
							'default' 		=> null,
							'version'		=> 'pro',
							'type' 			=> 'select_taxonomy',
							'taxonomy_id'	=> 'banner-sets',
							'desc'		 	=> 'Select Default Banner Set',
							'inputlabel' 	=> 'Select Default Banner Set',
							'title' 		=> 'Default Banner Set',
							'shortexp' 		=> "Posts pages and similar pages (404) will use this banner-set ID",
							'exp' 			=> "Posts pages and 404 pages in WordPress don't support meta data so you need to assign a set here. (If you want to use 'banners' on these pages.)",

					),	
					
				);

		}

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
