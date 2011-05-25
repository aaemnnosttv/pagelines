<?php
/*

	Section: Primary Sidebar
	Author: Andrew Powers
	Description: The main widgetized sidebar
	Version: 1.0.0
	
*/

class PrimarySidebar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Primary Sidebar', 'pagelines');
		$id = 'sidebar_primary';
		$this->handle = "Primary Sidebar";
	
		
		$settings = array(
			'description' 	=> 'The main widgetized sidebar for the theme.',
			'workswith' 	=> array('sidebar1', 'sidebar2', 'sidebar_wrap'),
			'folder' 		=> 'sidebars', 
			'init_file' 	=> 'sidebar_primary.php', 
			'icon'			=> PL_ADMIN_ICONS . '/sidebar.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		register_sidebar($setup);
	}

   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name, 'template.dwidgets');
	}

}

/*
	End of section class
*/