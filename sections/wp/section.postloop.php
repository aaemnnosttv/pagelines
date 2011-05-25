<?php
/*

	Section: PostLoop
	Author: Andrew Powers
	Description: Paginates posts, shows a numerical post navigation
	Version: 1.0.0
	
*/

class PageLinesPostLoop extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Main Content <small>(The Loop - Required)</small>', 'pagelines');
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
				'type' => 'select',
				'selectvalues'=> array(
					'fullwidth'				=> array( 'name' => 'Fullwidth layout', 'version' => 'pro' ),
					'one-sidebar-right' 	=> array( 'name' => 'One sidebar on right' ),
					'one-sidebar-left'		=> array( 'name' => 'One sidebar on left' ),
					'two-sidebar-right' 	=> array( 'name' => 'Two sidebars on right', 'version' => 'pro' ),
					'two-sidebar-left' 		=> array( 'name' => 'Two sidebars on left', 'version' => 'pro' ),
					'two-sidebar-center' 	=> array( 'name' => 'Two sidebars, one on each side', 'version' => 'pro' ),
				),
				'title' => 'Content Section - Select Layout Mode (optional)',
				'desc' => 'Use this option to change the content layout mode on this page.'
			),
		);
		
		add_global_meta_options( $global_meta );
	}

   function section_template() { 
		//Included in theme root for easy editing.
		get_template_part( 'template.postloop' ); 
	}

}

/*
	End of section class
*/