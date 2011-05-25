<?php
/*

	Section: Comments
	Author: Andrew Powers
	Description: Adds comments to main on pages/single posts
	Version: 1.0.0
	
*/

class PageLinesComments extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Comment Form', 'pagelines');
		$id = 'pl_comments';
	
		
		$settings = array(
			'type' 			=> 'main',
			'description' 	=> 'This is the section that contains the comment form used on posts (and pages when specified).',
			'workswith' 	=> array('main-single', 'main-default'),
			'icon'			=> PL_ADMIN_ICONS . '/comment.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

	function section_styles() {  
		wp_enqueue_script( 'comment-reply' );
	} 

	function section_template() { 
		
		// Important! Comments.php must be in theme root to work properly. Also 'comments_template() function must be used. Its a wordpress thing.

		global $post;
		comments_template();
		
	}

}

/*
	End of section class
*/