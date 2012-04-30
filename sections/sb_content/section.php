<?php
/*
	Section: Content Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Displays a widgetized sidebar inside the main content area. Set it up in the widgets panel.
	Class Name: ContentSidebar	
	Workswith: main-default, main-posts, main-single, main-404
	Persistant: true
*/

/**
 * Content Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class ContentSidebar extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
	//	pagelines_register_sidebar($setup);
	}

	/**
	* Section template.
	*/
   function section_template() { 
	 	pagelines_draw_sidebar($this->id, $this->name);
	}

}