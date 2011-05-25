<?php
/*

	Section: Content
	Author: Andrew Powers
	Description: Creates a flickr, nextgen, or featured image carousel.
	Version: 1.0.0
	
*/

class PageLinesContent extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Content Area', 'pagelines');
		$id = 'maincontent';
	
		
		$settings = array(
			'description' 	=> 'This is the section that contains the main content for your site, including sidebars and page/post content.',
			'workswith' 	=> array('templates'),
			'failswith'		=> array('404'),
			
			'icon'			=> PL_ADMIN_ICONS . '/document.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
		// included in theme root for easy editing.
		get_template_part( 'template.content' ); 
	}

}

/*
	End of section class
*/