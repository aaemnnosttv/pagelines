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
		
		$default_settings = array(
			'type' 			=> 'main',
			'workswith' 	=> array('main'),
		);
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($settings);    
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