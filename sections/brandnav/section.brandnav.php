<?php
/*
	Section: BrandNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Branding and Nav Inline
	Class Name: PageLinesBrandNav
	Depends: PageLinesNav
*/

class PageLinesBrandNav extends PageLinesNav {
   function __construct( $registered_settings = array() ) {
		
		$section_root_url = $registered_settings['base_url'];
		
		$default_settings = array(

			'workswith' 	=> array('header'),

		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
	   	parent::__construct( $settings );    
   }
	
	function section_persistent(){
			register_nav_menus( array( 'brandnav' => __( 'BrandNav Section Navigation', 'pagelines' ) ) );
	}
	
	/* Use this function to create the template for the section */	
 	function section_template() { 
	
			pagelines_main_logo( $this->id ); 
			
			pagelines_register_hook( 'brandnav_after_brand', 'brandnav' ); // Hook
		?>
		
			<div class="brandnav-nav main_nav fix">		
<?php 	
				wp_nav_menu( array('menu_class'  => 'main-nav tabbed-list'.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 3, 'theme_location'=>'brandnav', 'fallback_cb'=>'pagelines_nav_fallback') );

				
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

} /* End of section class - No closing tag needed */