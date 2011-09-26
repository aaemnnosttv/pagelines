<?php
/*
	Section: Secondary Nav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates secondary site navigation.
	Class Name: PageLinesSecondNav
	Workswith: header, content
	Edition: pro
*/

class PageLinesSecondNav extends PageLinesSection {

	// PHP that always loads no matter if section is added or not -- e.g. creates menus, locations, admin stuff...
	function section_persistent(){
		
			$metatab_array = array(
					'_second_nav_menu' => array(
						'type' 			=> 'select_menu',			
						'title' 		=> 'Select Secondary Nav Menu',
						'shortexp' 		=> 'Select the menu you would like to use for your secondary nav.', 
						'inputlabel'	=> 'Select Secondary Navigation Menu'
					)
				);
				
			add_global_meta_options( $metatab_array );
		
	}
	
   	function section_template() { 
		global $post; 
		global $pagelines_ID;

		$pageID = (!pagelines_non_meta_data_page()) ? $post->ID : null;
		
		if(!is_404()){

			if(!is_singular() && ploption('_second_nav_menu'))
				$second_menu = ploption('_second_nav_menu');
				
			elseif (ploption('_second_nav_menu', $pageID))
				$second_menu = ploption('_second_nav_menu', $pageID);
			
			if(isset($second_menu))
				wp_nav_menu( array('menu_class'  => 'secondnav_menu lcolor3', 'menu' => $second_menu, 'container' => null, 'container_class' => '', 'depth' => 1, 'fallback_cb'=>'pagelines_page_subnav') );
						
			elseif(pagelines_option('nav_use_hierarchy', $pagelines_ID))
				pagelines_page_subnav();
			
		}
		

	}


}
/*
	End of section class
*/