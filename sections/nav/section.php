<?php
/*
	Section: Navigation
	Author: PageLines
	Author URI: http://www.pagelines.com/
	Description: Creates site navigation, with optional superfish dropdowns.
	Version: 1.0.0
	Class Name: PageLinesNav
	Tags: internal
*/

class PageLinesNav extends PageLinesSection {

	static $nav_url;
	static $nav_dir;

   function __construct( $registered_settings = array() ) {
	
		$default_settings = array(
			'type' 			=> 'header',
			'workswith' 	=> array('header'),
			'description' 	=> 'Primary Site Navigation.',
			'cloning'		=> false
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		self::$nav_dir = PL_SECTIONS.'/nav';
		self::$nav_url = SECTION_ROOT.'/nav';

		parent::__construct($settings);    
   }

	// PHP that always loads no matter if section is added or not -- e.g. creates menus, locations, admin stuff...
	function section_persistent(){
		
		register_nav_menus( array( 'primary' => __( 'Primary Website Navigation', 'pagelines' ) ) );

	}
	

	
   function section_template() {  
	
		$container_class = ( ploption('hidesearch') ) ? 'nosearch' : '';

		printf('<div class="navigation_wrap fix"><div class="main_nav_container %s"><nav id="nav_row" class="main_nav fix">', $container_class );
		
				if(function_exists('wp_nav_menu'))
					wp_nav_menu( array('menu_class'  => 'main-nav'.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 3, 'theme_location'=>'primary', 'fallback_cb'=>'pagelines_nav_fallback') );
				else
					pagelines_nav_fallback();
			
			echo '</nav></div>';
		
		 	if(!ploption('hidesearch'))
				get_search_form();
	 	
		echo '</div>';
	}

	function section_styles(){
		if(ploption('enable_drop_down')){
			
			wp_register_style('superfish', self::$nav_url . '/style.superfish.css', array(), CORE_VERSION, 'screen');
		 	wp_enqueue_style( 'superfish' );
		
		}
	}
	
	function section_head(){
		
		$arrows = (ploption('drop_down_arrows') == 'on') ? 1 : 0;
		$shadows = (ploption('drop_down_shadow') == 'on') ? 1 : 0;
		
		if(ploption('enable_drop_down')): ?>
		
<script type="text/javascript"> /* <![CDATA[ */ jQuery(document).ready(function() {  jQuery('ul.sf-menu').superfish({ delay: 100, speed: 'fast', autoArrows:  <?php echo $arrows;?>, dropShadows: <?php echo $shadows;?> });  }); /* ]]> */ </script>			

<?php 
		endif;
}

	function section_scripts() {  
		
		return array(
				'superfish' => array(
						'file' => self::$nav_url . '/script.superfish.js',
						'dependancy' => array('jquery'), 
						'location' => 'footer'
					), 
				'bgiframe' => array(
					'file' => self::$nav_url . '/script.bgiframe.js',
					'dependancy' => array('jquery', 'superfish'), 
					'location' => 'footer'
					),
					
			);
		
	}


}
