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
	
		$name = __('Navigation', 'pagelines');
		$id = 'primary-nav';
	
		
		$default_settings = array(
			'name'			=> false,
			'id'			=> false,
			'type' 			=> 'header',
			'workswith' 	=> array('header'),
			'description' 	=> 'Primary Site Navigation.',
			'icon'			=> PL_ADMIN_ICONS . '/map.png', 
			'cloning'		=> false
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		self::$nav_dir = PL_SECTIONS.'/nav';
		self::$nav_url = SECTION_ROOT.'/nav';

		$name = ($settings['name']) ? $settings['name'] : $name;
		$id = ($settings['id']) ? $settings['id'] : $id;

		parent::__construct($name, $id, $settings);    
   }

	// PHP that always loads no matter if section is added or not -- e.g. creates menus, locations, admin stuff...
	function section_persistent(){
		
		register_nav_menus( array( 'primary' => __( 'Primary Website Navigation', 'pagelines' ) ) );
		
		add_filter('pagelines_css_group', array(&$this, 'section_selectors'), 10, 2);
		
	}
	
	function section_selectors($selectors, $group){

		$s['nav_standard'] = '.main-nav li:hover, .main-nav .current-page-ancestor a,  .main-nav li.current-page-ancestor ul a, .main-nav li.current_page_item a, .main-nav li.current-menu-item a, .sf-menu li li, .sf-menu li li li';

		$s['nav_standard_border'] = 'ul.sf-menu ul li';
		
		$s['nav_highlight'] = '.main-nav li a:hover, .main-nav .current-page-ancestor .current_page_item a, .main-nav li.current-page-ancestor ul a:hover';
	
		
		if($group == 'box_color_primary')
			$selectors .= ','.$s['nav_standard'];
		elseif($group == 'box_color_secondary')	
			$selectors .= ','.$s['nav_highlight'];
		elseif( $group == 'border_primary' )
			$selectors .= ','.$s['nav_standard_border'];
		
		return $selectors;
	}
	
   function section_template() {  
	
	$container_class = ( ploption('hidesearch') ) ? 'nosearch' : '';
	?>
	<div class="main_nav_container <?php echo $container_class;?>">
		<nav id="nav_row" class="main_nav fix"><?php 			
		
		if(function_exists('wp_nav_menu'))
			wp_nav_menu( array('menu_class'  => 'main-nav'.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 3, 'theme_location'=>'primary', 'fallback_cb'=>'pagelines_nav_fallback') );
		else
			pagelines_nav_fallback();
			
	 ?>
		</nav>
	</div>
	<?php if(!pagelines_option('hidesearch'))
		get_search_form();
	 
	}

	function section_styles(){
		if(pagelines('enable_drop_down')){
			wp_register_style('superfish', self::$nav_url . '/superfish.css', array(), CORE_VERSION, 'screen');
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
						'file' => self::$nav_url . '/superfish.js',
						'dependancy' => array('jquery'), 
						'location' => 'footer'
					), 
				'bgiframe' => array(
					'file' => self::$nav_url . '/jquery.bgiframe.min.js',
					'dependancy' => array('jquery', 'superfish'), 
					'location' => 'footer'
					),
					
			);
		
	}
	
	function section_options($optionset = null, $location = null) {
	
		if($optionset == 'header_and_footer' && $location == 'top'){
			return array(
					'drop_down_options' => array(
							'default' => '',
							'type' => 'check_multi',
							'selectvalues'=> array(
							
								'enable_drop_down' => array(
									'default' => false,
									'type' => 'check',
									'scope' => '',
									'inputlabel' => 'Enable Drop Down Navigation?',
									'title' => 'Drop Down Navigation',
									'shortexp' => 'Enable universal drop down navigation',
									'exp' => 'Checking this option will create drop down menus for all child pages when ' . 
											 'users hover over main navigation items.'
									),
								'drop_down_shadow' => array(
									'default' => true,
									'type' => 'check',
									'scope' => '',
									'inputlabel' => 'Enable Shadow on Drop Down Menu?',
									'title' => 'Drop Down Shadow',
									'shortexp' => 'Enable shadow for drop down navigation',
									'exp' => 'Checking this option will create shadows for the drop down menus'
									),
								'drop_down_arrows' => array(
									'default' => true,
									'type' => 'check',
									'scope' => '',
									'inputlabel' => 'Enable Arrows on Drop Down Menu?',
									'title' => 'Drop Down Arrows',
									'shortexp' => 'Enable arrows for drop down navigation',
									'exp' => 'Checking this option will create arrows for the drop down menus'
									)),
							'inputlabel' => 'Select Which Drop Down Options To Show',
							'title' => 'Drop Down Navigation - Nav and BrandNav Section',						
							'shortexp' => 'Select Which To Show',
							'exp' => "Enable drop downs and choose the options you would like to show" 
								 
							),
					'hidesearch' => array(
							'version' => 'pro',
							'default' => false,
							'type' => 'check',
							'inputlabel' => 'Hide search field?',
							'title' => 'Hide Search - Nav Section',						
							'shortexp' => 'Remove the search field from the nav section',
							'exp' => 'Removes the search field from the PageLines Navigation Section.'
						), 
				
				);

		}
	
	}

}
