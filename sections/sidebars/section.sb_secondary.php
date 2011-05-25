<?php
/*

	Section: Secondary Sidebar
	Author: Andrew Powers
	Description: The main widgetized sidebar
	Version: 1.0.0
	
*/

class SecondarySidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Secondary Sidebar', 'pagelines');
		$id = 'sidebar_secondary';
	
		
		$default_settings = array(
			'description' 	=> 'The secondary widgetized sidebar for the theme.',
			'workswith' 	=> array('sidebar1', 'sidebar2', 'sidebar_wrap'),
			'folder' 		=> 'sidebars', 
			'init_file' 	=> 'section.sb_secondary.php',
			'icon'			=> PL_ADMIN_ICONS . '/sidebar.png'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );

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