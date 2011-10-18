<?php
/*
	Section: Carousel
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a flickr, nextgen, or featured image carousel.
	Class Name: PageLinesCarousel
	Cloning: true
	Workswith: content, header, footer
	Edition: pro
*/

class PageLinesCarousel extends PageLinesSection {
	
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
						'shortexp' 	=> 'Select the mode that the carousel should use for its thumbnails.',
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
						'default'		=> '', 
						'type' 			=> 'text',		
						'title'			=> 'Carousel - Post Category Name', 
						'shortexp'		=> 'The category slug to pull posts from',
						'inputlabel' 	=> 'Category Slug (Optional)',
						'exp' 			=> 'Posts Mode - Select the default category for carousel post images.  If not set, the carousel will get the most recent posts.'
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
		
		$carousel_class = (isset($clone_id) && $clone_id != 1) ? 'crsl'.$clone_id : 'crsl';
		
		// Set Up Variables
		$carouselitems = (ploption('carousel_items', $this->oset)) ? ploption('carousel_items', $this->oset) : 30;
		$carousel_post_id = (ploption('carousel_post_id', $this->oset)) ? ploption('carousel_post_id', $this->oset) : null;
		$carousel_image_width = (ploption('carousel_image_width', $this->oset)) ? ploption('carousel_image_width', $this->oset) : 64;
		$carousel_image_height = (ploption('carousel_image_height', $this->oset)) ? ploption('carousel_image_height', $this->oset) : 64;
		$cmode = (ploption('carousel_mode', $this->oset)) ? ploption('carousel_mode', $this->oset): null;
		$ngen_id = (ploption('carousel_ngen_gallery', $this->oset)) ? ploption('carousel_ngen_gallery', $this->oset) : 1;
		
		
	if( ($cmode == 'flickr' && !function_exists('get_flickrRSS')) || ($cmode == 'ngen_gallery' && !function_exists('nggDisplayRandomImages')) )
		echo setup_section_notify($this, __("The <strong>plugin</strong> for the selected carousel mode needs to be activated (FlickrRSS or NextGen Gallery).", 'pagelines'), admin_url().'plugins.php', 'Setup Plugin');
	else {
	?>		
	<div class="<?php echo $carousel_class;?> thecarousel">
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
		
<?php  

		}
	}

	function section_head( $clone_id = null ) {   
		
		$carousel_class = ( isset( $clone_id ) && $clone_id != 1 ) ? 'crsl' . $clone_id : 'crsl';
		
		$num_items = ( ploption('carousel_display_items', $this->oset) ) ? ploption('carousel_display_items', $this->oset) : 9;
		$scroll_items = ( ploption('carousel_scroll_items', $this->oset) ) ? ploption('carousel_scroll_items', $this->oset) : 6;
		$anim_speed = ( ploption('carousel_animation_speed', $this->oset) ) ? ploption('carousel_animation_speed', $this->oset) : 800;
		
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

// End of Section Class //
}