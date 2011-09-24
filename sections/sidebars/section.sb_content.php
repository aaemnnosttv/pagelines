<?php
/*
	Section: Content Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows sidebar inside the main column content area
	Class Name: ContentSidebar
	Tags: internal
*/

class ContentSidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
		
		$default_settings = array(
			'workswith' 	=> array('main-default', 'main-posts', 'main-single', 'main-404'),
		);
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($settings);    
   }

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		register_sidebar($setup);
	}

   function section_template() { 
	 	pagelines_draw_sidebar($this->id, $this->name);
	}

}

/*
	End of section class
*/