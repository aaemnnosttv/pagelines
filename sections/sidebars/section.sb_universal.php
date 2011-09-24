<?php
/*
	Section: Universal Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A universal widgetized sidebar
	Class Name: UniversalSidebar
	Tags: internal
*/

class UniversalSidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {

		$default_settings = array(
			'workswith' 	=> array('sidebar1', 'sidebar2', 'sidebar_wrap', 'templates', 'main', 'header', 'morefoot'),
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

/*
	End of section class
*/