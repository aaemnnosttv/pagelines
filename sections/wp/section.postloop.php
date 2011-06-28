<?php
/*
	Section: PostLoop
	Author: Andrew Powers
	Author URI: http://www.pagelines.com
	Description: Paginates posts, shows a numerical post navigation
	Class Name: PageLinesPostLoop
	Tags: internal, core
*/

class PageLinesPostLoop extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Content (Loop)', 'pagelines');
		$id = 'theloop';
	
		
		$settings = array(
			'type' 			=> 'main',
			'description' 	=> 'The Main Posts Loop. Includes content and post information',
			'workswith' 	=> array('main-single', 'main-default', 'main-posts'),
			'folder' 		=> 'wp', 
			'init_file' 	=> 'postloop',
			'required'		=> true, 
			'icon'			=> PL_ADMIN_ICONS . '/document.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){
		$global_meta = array(
			
			'_pagelines_layout_mode' => array(
				'type' 			=> 'graphic_selector',
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layouts.png', 
				'height'		=> '50px', 
				'width'			=> '50px', 
				'selectvalues'	=> array(
					'fullwidth'				=> array( 'name' => 'Fullwidth layout', 'version' => 'pro', 'offset' => '0px 0px'),
					'one-sidebar-right' 	=> array( 'name' => 'One sidebar on right', 'offset' => '0px -50px'),
					'one-sidebar-left'		=> array( 'name' => 'One sidebar on left', 'offset' => '0px -100px'),
					'two-sidebar-right' 	=> array( 'name' => 'Two sidebars on right', 'version' => 'pro', 'offset' => '0px -150px' ),
					'two-sidebar-left' 		=> array( 'name' => 'Two sidebars on left', 'version' => 'pro', 'offset' => '0px -200px' ),
					'two-sidebar-center' 	=> array( 'name' => 'Two sidebars, one on each side', 'version' => 'pro', 'offset' => '0px -250px' ),
				),
				'title' 		=> 'Individual Page Content Layout',
				'inputlabel'	=> 'Select Page Layout',	
				'layout' 		=> 'interface',						
				'shortexp' 		=> 'Select the layout that will be used on this page',
				'exp' 			=> '',
			),
			'section_control' => array(
				'type' 			=> 'section_control',
				'title' 		=> 'Individual Page Section Control',
				'layout' 		=> 'interface',						
				'shortexp' 		=> 'Control which sections appear on this specific page',
				'exp' 			=> '',
			),
			
		);
		
		add_global_meta_options( $global_meta, 'top');
	}

   function section_template() { 
		//Included in theme root for easy editing.
		$theposts = new PageLinesPosts();
		$theposts->load_loop();
	}

}

/*
	End of section class
*/