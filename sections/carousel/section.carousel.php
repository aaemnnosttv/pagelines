<?php
/*
	Section: Carousel
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a flickr, nextgen, or featured image carousel.
	Class Name: PageLinesCarousel
	Tags: internal
*/

class PageLinesCarousel extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('PageLines Carousel', 'pagelines');
		$id = 'carousel';
	
		
		$default_settings = array(
			'description' 	=> 'This is a javascript carousel that can show images and links from posts, FlickRSS, or NextGen Gallery.', 
			'workswith'		=> array('content', 'header', 'footer'),
			'icon'			=> PL_ADMIN_ICONS . '/carousel.png',
			'version'		=> 'pro',
			'cloning'		=> true
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }
	
	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$metatab_array = array(

					'carousel_items' => array(
						'version' 			=> 'pro',
						'type' 				=> 'text_small',			
						'title'	 			=> 'Max Carousel Items (Carousel Page Template)',
						'shortexp' 			=> 'The number of items/thumbnails to show in the carousel.',
						'inputlabel'		=> 'Enter the number of carousel items'
					),
					'carousel_mode' => array(
						'version' => 'pro',
						'type' => 'select',	
						'selectvalues'=> array(
							'flickr'		=> array( 'name' => 'Flickr (default)'),
							'posts' 		=> array( 'name' => 'Post Thumbnails'),
							'ngen_gallery' 	=> array( 'name' => 'NextGen Gallery'), 
							'hook'			=> array( 'name' => 'Hook: "pagelines_carousel_list"')
						),					
						'title' 	=> 'Carousel Image/Link Mode (Carousel Page Template)',
						'shortexp' 	=> 'Select the mode that the carousel should use for its thumbnails.<br/><br/>',
						'exp'		=> '<strong>Flickr</strong> - (default) Uses thumbs from FlickrRSS plugin.<br/><strong> Post Thumbnails</strong> - Uses links and thumbnails from posts <br/><strong>NextGen Gallery</strong> - Uses an image gallery from the NextGen Gallery Plugin'
					),
					'carousel_ngen_gallery' => array(
						'version' => 'pro',
						'type' => 'text',					
						'title' => 'NextGen Gallery ID For Carousel (Carousel Page Template / NextGen Mode)',
						'shortexp' => 'Enter the ID of the NextGen Image gallery for the carousel.', 
						'exp'		=> '<strong>Note:</strong>The NextGen Gallery and carousel template must be selected.'
					),
					'carousel_post_id' => array(
						'version' => 'pro',
						'type' => 'text',					
						'title' => 'Enter Category Slug (Carousel Posts Mode)',
						'shortexp' => 'Enter the name or slug of the category that the carousel should use for its images (posts mode only).'
					)
				);
			
			$metatab_settings = array(
					'id' 		=> 'carousel_meta',
					'name'	 	=> "Carousel Meta",
					'icon' 		=> $this->icon,
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);
			
			register_metatab($metatab_settings, $metatab_array);
	}
	
   function section_template( $clone_id ) { 
	
		global $post;
		global $pagelines_ID;
		
		$oset = array('clone_id' => $clone_id, 'post_id' => $pagelines_ID);
		
		$carousel_class = (isset($clone_id) && $clone_id != 1) ? 'thecarousel'.$clone_id : 'thecarousel';
		
		// Set Up Variables
		$carouselitems = (ploption('carousel_items', $oset)) ? ploption('carousel_items', $oset) : 30;
		$carousel_post_id = (ploption('carousel_post_id', $oset)) ? ploption('carousel_post_id', $oset) : null;
		$carousel_image_width = (ploption('carousel_image_width', $oset)) ? ploption('carousel_image_width', $oset) : 64;
		$carousel_image_height = (ploption('carousel_image_height', $oset)) ? ploption('carousel_image_height', $oset) : 64;
		$cmode = (ploption('carousel_mode', $oset)) ? ploption('carousel_mode', $oset): null;
		$ngen_id = (ploption('carousel_ngen_gallery', $oset)) ? ploption('carousel_ngen_gallery', $oset) : 1;
		
		
	?>		
	<div class="thecarousel">
		<ul id="mycarousel" class="mycarousel">
			<?php 
			
			if(function_exists('nggDisplayRandomImages')  && $cmode == 'ngen_gallery')
				echo do_shortcode('[nggallery id='.$ngen_id.' template=plcarousel]');
				
			elseif(function_exists('get_flickrRSS') && $cmode == 'flickr'){
			
				if(!function_exists('get_and_delete_option')):  // fixes instantiation within the function in the plugin :/
					get_flickrRSS( array(
						'num_items' => $carouselitems, 
						'html' => '<li><a href="%flickr_page%" title="%title%"><img src="%image_square%" alt="%title%"/><span class="list-title">%title%</span></a></li>'	
					));
				endif;
			
			}elseif($cmode == 'hook')
				pagelines_register_hook('pagelines_carousel_list');
			
			elseif( ($cmode == 'flickr' && !function_exists('get_flickrRSS')) || ($cmode == 'ngen_gallery' && !function_exists('nggDisplayRandomImages')))
				printf('<div class="carousel_text">%s</div>', __("The plugin for the selected carousel mode (NextGen-Gallery or FlickrRSS) needs to be installed and activated.", 'pagelines'));
				
			else{
			
				$carousel_post_query = 'numberposts='.$carouselitems;
				
				if($carousel_post_id) 
					$carousel_post_query .= '&category_name='.$carousel_post_id;
				
				$recentposts = get_posts($carousel_post_query);
				
				foreach($recentposts as $cid => $c): ?>

					<li class="list-item fix">
						<a class="carousel_image_link" href="<?php echo get_permalink($c->ID); ?>">
						<?php if(has_post_thumbnail($c->ID)): 
								echo get_the_post_thumbnail( $c->ID, array( $carousel_image_width, $carousel_image_height ),array( 'class' => 'list_thumb list-thumb' )); ?>
						<?php else: ?>
							<img class="list_thumb list-thumb sidebar_thumb" src="<?php echo PL_ADMIN_IMAGES;?>/post-blank.jpg" />
						<?php endif;?> 
							<span class="list-title"><?php echo $c->post_title; ?></span>
						</a>
					</li>

				<?php endforeach;?>
			<?php } ?>
		</ul>
	</div>
		
<?php  }

	function section_styles() {
	
		wp_register_style('carousel', $this->base_url . '/carousel.css', array(), CORE_VERSION, 'screen');
	 	wp_enqueue_style( 'carousel' );
		
	}   

	function section_head( $clone_id = null ) {   
		
		global $pagelines_ID;
		$oset = array( 'clone_id' => $clone_id, 'post_id' => $pagelines_ID );
		
		$carousel_class = ( isset( $clone_id ) && $clone_id != 1 ) ? 'thecarousel' . $clone_id : 'thecarousel';
		
		$num_items = ( ploption('carousel_display_items', $oset) ) ? ploption('carousel_display_items', $oset) : 9;
		$scroll_items = ( ploption('carousel_scroll_items', $oset) ) ? ploption('carousel_scroll_items', $oset) : 6;
		$anim_speed = ( ploption('carousel_animation_speed', $oset) ) ? ploption('carousel_animation_speed', $oset) : 800;
		
		$carousel_args = sprintf('wrap: "%s", visible: %s, easing: "%s", scroll: %s, animation: %s', 'circular', $num_items, 'swing', $scroll_items, $anim_speed);
		?>
<script type="text/javascript">
/* <![CDATA[ */
	jQuery(document).ready(function () {
		<?php printf('jQuery(".%s").show().jcarousel({%s});', $carousel_class, $carousel_args); ?>
		jQuery(".jcarousel-prev, .jcarousel-next").disableTextSelect();
	});
/* ]]> */
</script>
<?php }

	function section_scripts() {  
		
		return array(
				'jcarousel' => array(
						'file' => $this->base_url . '/carousel.jcarousel.js',
						'dependancy' => array('jquery'), 
						'location' => 'footer'
					)
						
			);
		
	}

	function section_options($optionset = null, $location = null) {
	
		if($optionset == 'new' && $location == 'bottom'){
			return array(
				'carousel_settings' => array(
					'carousel_mode' => array(
							'type'			=> 'select',
							'default'		=> 'posts',
							'title'			=> 'Carousel Image Mode', 
							'shortexp'		=> 'Where the carousel is going to get its images.', 
							'selectvalues'=> array(
								'posts' 		=> array("name" => 'Post Featured Images (default)'),
								'flickr'		=> array("name" => 'FlickrRSS Plugin'),
								'ngen_gallery' 	=> array("name" => 'NextGen Gallery Plugin'), 
								'hook'			=> array("name" => 'Hook: "pagelines_carousel_list"')
							),					
							'inputlabel' 	=> 'Carousel Image/Link Mode',
							'exp' 			=> 'Select the mode that the carousel should use for its thumbnails.<br/><br/>' .
									 		'<strong> Post Featured Images</strong> - Uses featured images from posts <br/><strong>FlickrRSS</strong> - Uses thumbs from FlickrRSS plugin.<br/>' .
									 		'<strong>NextGen Gallery</strong> - Uses an image gallery from the NextGen Gallery Plugin'
						),
					'carousel_items' => array(
						'default'		=> 30, 
						'type' 			=> 'text_small',		
						'title'			=> 'Rotating Carousel Items', 
						'shortexp'		=> 'The number of rotating images in your carousel',
						'inputlabel' 	=> 'Max Rotating Carousel Items',
						'exp' 			=> 'This option sets the number of items that will be rotated through in your carousel.'
					),
					'carousel_display_items' => array(
						'default'		=> 7, 
						'type' 			=> 'text_small',		
						'title'			=> 'Displayed Carousel Items', 
						'shortexp'		=> 'The number of displayed images in your carousel',
						'inputlabel' 	=> 'Displayed Carousel Items',
						'exp' 			=> 'This option sets the number of images that will be displayed in the carousel at any given time.'
					),
					'carousel_scroll_items' => array(
						'default'		=> 4, 
						'type' 			=> 'text_small',		
						'title'			=> 'Scrolled Carousel Items', 
						'shortexp'		=> 'The number of images scrolled in one click',
						'inputlabel' 	=> 'Items to scroll',
						'exp' 			=> 'This option sets the number of images that will scroll when a user clicks the arrows, etc..'
					),
					'carousel_animation_speed' => array(
						'default'		=> 800, 
						'type' 			=> 'text_small',		
						'title'			=> 'Carousel Animation Speed', 
						'shortexp'		=> 'Set the time it takes to scroll',
						'inputlabel' 	=> 'Carousel Animation Speed',
						'exp' 			=> 'The speed of the scroll animation as string in  milliseconds (e.g. 800 for .8 seconds). If set to 0, animation is turned off.'
					),
					'carousel_image_dimensions' => array(
							'type' => 'text_multi',
							'selectvalues'=> array(
								'carousel_image_width'		=> array('inputlabel'=>'Max Image Width (in pixels)', 'default'	=> 64),
								'carousel_image_height'		=> array('inputlabel'=>'Max Image Height (in pixels)', 'default' => 64),
							),
							'title' => 'Carousel Image Dimensions (Posts Mode Only)',
							'shortexp' => 'Control the dimensions of the carousel images',
							'exp' => 'Use this option to control the max height and width of the images in the carousel. You may have to use this option in conjunction with the scroll items option.<br/><br/> For the FlickrRSS and NextGen Gallery modes, image sizes are set by Flickr thumb sizes and the NextGen Gallery plugin respectively.'
					),
					'carousel_post_id' => array(
						'default'		=> '', 
						'type' 			=> 'text',		
						'title'			=> 'Carousel - Post Category Name', 
						'shortexp'		=> 'The category slug to pull posts from',
						'inputlabel' 	=> 'Category Slug (Optional)',
						'exp' 			=> 'Posts Mode - Select the default category for carousel post images.  If not set, the carousel will get the most recent posts.'
					),
					'carousel_ngen_gallery' => array(
							'type' 			=> 'text_small',
							'default'		=> '', 
							'title'			=> 'NextGen Gallery ID (NextGen Gallery Mode Only)', 
							'shortexp'		=> 'The ID of the NextGen Gallery selection you would like to use.', 
							'inputlabel' 	=> 'NextGen Gallery ID For Carousel (<em>NextGen Gallery Mode Only</em>)',
							'exp' 			=> 'Enter the ID of the NextGen Image gallery for the carousel. <strong>The NextGen Gallery and carousel template must be selected.</strong>'
						),
					)
				);

	} 
}

// End of Section Class //
}