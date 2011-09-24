<?php
/*
	Section: Full Width Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows full width widgetized sidebar
	Class Name: FullWidthSidebar
	Tags: internal
*/

class FullWidthSidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$default_settings = array(
			'workswith' 	=> array('templates', 'footer', 'morefoot'),
			'version'		=> 'pro'
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
