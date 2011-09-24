<?php
/*
	Section: Post/Page Pagination
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Pagination - A numerical post/page navigation. (Supports WP-PageNavi)
	Class Name: PageLinesPagination
	Tags: internal
*/

class PageLinesPagination extends PageLinesSection {

   function __construct( $registered_settings = array() ) {

		$default_settings = array(
			'type' 			=> 'main',
			'workswith' 	=> array('main'),
			'failswith'		=> pagelines_special_pages(),
		);
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($settings);    
   }

   function section_template() { 
		pagelines_pagination();
	}

}

/*
	End of section class
*/