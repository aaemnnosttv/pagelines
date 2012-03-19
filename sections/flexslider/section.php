<?php
/*
	Section: FlexSlider
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A reponsive slider that is easy to use and setup.
	Class Name: PageLinesFlexSlider	
	Workswith: main, templates, sidebar_wrap
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesFlexSlider extends PageLinesSection {

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		wp_enqueue_script('flexslider', $this->base_url.'/flexslider/jquery.flexslider-min.js');
	}
	
	function section_head(){
		
		$animation = (ploption('flex_transition', $this->oset) == 'slide_v' || ploption('flex_transition', $this->oset) == 'slide_h') ? 'slide' : 'fade';
		$transfer = (ploption('flex_transition', $this->oset) == 'slide_v') ? 'vertical' : 'horizontal';
		
		$slideshow = (ploption('flex_slideshow', $this->oset)) ? 'true' : 'false';
		?>
		
		<script>
		  jQuery(window).load(function() {
		    jQuery('.flexslider').flexslider({
				controlsContainer: '.fs-nav-container',
				animation: '<?php echo $animation;?>', 
				slideDirection: '<?php echo $transfer;?>',
				slideshow: <?php echo $slideshow;?>
			});
		  });
		</script>	
		
	<?php }

	/**
	* Section template.
	*/
   function section_template() { 
	?>
	<div class="flexwrap">
		<div class="fslider">
		<div class="flexslider">
		  <ul class="slides">
			
			<?php
			
			for($i = 1; $i <= 4; $i++){
			
				if(ploption('flex_image_'.$i, $this->oset)){
					
					$text = (ploption('flex_text_'.$i, $this->oset)) ? sprintf('<p class="flex-caption">%s</p>', ploption('flex_text_'.$i, $this->oset)) : '';
					$img = sprintf('<img src="%s" />', ploption('flex_image_'.$i, $this->oset) );
					$slide = (ploption('flex_link_'.$i, $this->oset)) ? sprintf('<a href="%s">%s</a>', ploption('flex_link_'.$i, $this->oset), $img ) : $img;
					
					printf('<li>%s %s</li>', $slide, $text);
				}
			}
			
			?>
		  </ul>
		</div>
		</div>
		<div class="fs-nav-container">
		</div>
	</div>
		<?php 
	}

	/**
	 *
	 * Page-by-page options for PostPins
	 *
	 */
	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );
		
			$array = array(); 
			
			$array['flex_transition'] = array(
				'type' 			=> 'select',
				'selectvalues' => array(
					'fade' 		=> array('name' => __( 'Use Fading Transition', 'pagelines' ) ),
					'slide_h' 	=> array('name' => __( 'Use Slide/Horizontal Transition', 'pagelines' ) ),						
				),
				'inputlabel' 	=> __( 'Select Transition Type', 'pagelines' ),
				'title' 		=> __( 'Slider Transition Type', 'pagelines' ),
				'shortexp' 		=> __( 'Configure the way slides transfer to one another.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),
		
			);
			
			$array['flex_slideshow'] = array(
				'type' 			=> 'check',
				
				'inputlabel' 	=> __( 'Animate Slideshow Automatically?', 'pagelines' ),
				'title' 		=> __( 'Automatic Slideshow?', 'pagelines' ),
				'shortexp' 		=> __( 'Autoplay the slides, transitioning every 7 seconds.', 'pagelines' ),
				'exp' 			=> __( "", 'pagelines' ),
		
			);
			
			
			for($i = 1; $i <= 4; $i++){
				
				
				$array['flex_slide_'.$i] = array(
					'type' 			=> 'multi_option',
					'selectvalues' => array(
						'flex_image_'.$i 	=> array(
							'inputlabel' 	=> __( 'Slide Image', 'pagelines' ), 
							'type'			=> 'image_upload'
						),
						'flex_text_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Text', 'pagelines' ), 
							'type'			=> 'text'
						),	
						'flex_link_'.$i 	=> array(
							'inputlabel'	=> __( 'Slide Link URL', 'pagelines' ), 
							'type'			=> 'text'
						),					
					),
					'title' 		=> __( 'FlexSlider Slide ', 'pagelines' ) . $i,
					'shortexp' 		=> __( 'Setup options for slide number ', 'pagelines' ) . $i,
				);
				
			}
				
			

			$metatab_settings = array(
					'id' 		=> 'flexslider_options',
					'name' 		=> __( 'FlexSlider', 'pagelines' ),
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab( $metatab_settings, $array );

	}

}