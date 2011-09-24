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


   function section_template() { 
		//Included in theme root for easy editing.
		$theposts = new PageLinesPosts();
		$theposts->load_loop();
	}

}

/*
	End of section class
*/