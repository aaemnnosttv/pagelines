<?php
/*
	Section: BrandNav Section
	Author: PageLines
	Description: Branding and Nav Inline
	Version: 1.0.0

*/
class BrandNav extends PageLinesNav {
   function __construct( $registered_settings = array() ) {
	
		/* The name and ID of the section */
		$name = __('BrandNav Section', 'pagelines');
		$id = 'brandnav';
		
		$section_root_url = $registered_settings['base_url'];
		
		$default_settings = array(
			'name'			=> $name, 
			'id'			=> $id,
			'description'	=> 'Combines branding elements and navigation on one line.',
			'workswith' 	=> array('header'),
			'icon'			=> $section_root_url.'/brandnav.png',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
	   	parent::__construct( $settings );    
   }
	
	/* Use this function to create the template for the section */	
 	function section_template() { 
	
			pagelines_main_logo(); 
			pagelines_register_hook( 'brandnav_after_brand', 'brandnav' ); // Hook
		?>
		
			<div class="inline-nav main_nav fix">		
<?php 	
				if(function_exists('wp_nav_menu')){

					wp_nav_menu( array('menu_class'  => 'main-nav'.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 3, 'theme_location'=>'primary', 'fallback_cb'=>'pagelines_nav_fallback') );

				}else{ pagelines_nav_fallback(); }
				
				pagelines_register_hook( 'brandnav_after_nav', 'brandnav' ); // Hook
?>
			</div>
	
<?php }

	function section_styles(){
		pagelines_load_css( $this->base_url . '/brandnav.css', 'brandnav');
		if(pagelines('enable_drop_down')){
			wp_register_style('superfish', self::$nav_url . '/superfish.css', array(), CORE_VERSION, 'screen');
		 	wp_enqueue_style( 'superfish' );
		}
	}

	// Some of the optional functions not used here.
	// Load to prevent parent class reloading
	function section_options($optionset = null, $location = null) {} 
	function section_persistent(){} 

} /* End of section class - No closing tag needed */