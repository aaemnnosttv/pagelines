<?php
/*
	Section: PostLoop
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Paginates posts, shows a numerical post navigation
	Class Name: PageLinesPostLoop
	Tags: internal
*/

class PageLinesPostLoop extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Post Loop', 'pagelines');
		$id = 'theloop';
	
		
		$settings = array(
			'type' 			=> 'main',
			'description' 	=> 'The Main Posts Loop. Includes content and post information',
			'workswith' 	=> array('main'),
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
			'page_background_image' => array(
				'title' 	=> 'Page Background Image',						
				'shortexp' 	=> 'Setup A Background Image For This Page',
				'exp' 		=> 'Use this option to apply a background image to this page. This option will only be applied to the current page.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the "top" or "left" side, 50% to center, etc..',
				'type' 		=> 'background_image',
				'selectors'	=> cssgroup('page_background_image')
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