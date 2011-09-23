<?php
/*
	Section: Post/Page Pagination
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Paginates posts, shows a numerical post navigation
	Class Name: PageLinesPagination
	Tags: internal
*/

class PageLinesPagination extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Pagination', 'pagelines');
		$id = 'pagination';
	
		
		$settings = array(
			'type' 			=> 'main',
			'description' 	=> 'Pagination - A numerical post/page navigation. (Supports WP-PageNavi)',
			'workswith' 	=> array('main'),
			'failswith'		=> pagelines_special_pages(),
			'folder' 		=> 'wp', 
			'init_file' 	=> 'pagination', 
			'icon'			=> PL_ADMIN_ICONS . '/pagination.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
		pagelines_pagination();
	}

}

/*
	End of section class
*/