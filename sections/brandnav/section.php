<?php
/*
	Section: BrandNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Branding and Nav Inline
	Class Name: PageLinesBrandNav
	Depends: PageLinesNav
	Workswith: header
*/

class PageLinesBrandNav extends PageLinesNav {
	
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
		
		if(ploption('enable_drop_down')){
			wp_register_style('superfish', self::$nav_url . '/style.superfish.css', array(), CORE_VERSION, 'screen');
		 	wp_enqueue_style( 'superfish' );
		}
	}

} /* End of section class - No closing tag needed */