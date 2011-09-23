<?php
/*
	Section: Comments
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds comments to main on pages/single posts
	Class Name: PageLinesComments
	Tags: internal
*/

class PageLinesComments extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
		
		$settings = array(

			'workswith' 	=> array('main'),
			'failswith'		=> pagelines_special_pages(),

		);
		

	   parent::__construct($settings);    
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