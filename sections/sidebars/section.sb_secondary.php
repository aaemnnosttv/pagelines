<?php
/*
	Section: Secondary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The main widgetized sidebar
	Class Name: SecondarySidebar
	Tags: internal
*/

class SecondarySidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
			
		$default_settings = array(
			'workswith' 	=> array('sidebar1', 'sidebar2', 'sidebar_wrap'),
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