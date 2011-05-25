<?php
/*

	Section: Tertiary Sidebar
	Author: Andrew Powers
	Description: The main widgetized sidebar
	Version: 1.0.0
	
*/

class TertiarySidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Tertiary Sidebar', 'pagelines');
		$id = 'sidebar_tertiary';
	
		
		$settings = array(
			'description' 	=> 'A 3rd widgetized sidebar for the theme that can be used in standard sidebar templates.',
			'workswith' 	=> array('sidebar1', 'sidebar2', 'sidebar_wrap'),
			'folder' 		=> 'sidebars', 
			'init_file' 	=> 'section.sb_tertiary.php',
			'icon'			=> PL_ADMIN_ICONS . '/sidebar.png'
		);
		

	   parent::__construct($name, $id, $settings);    
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