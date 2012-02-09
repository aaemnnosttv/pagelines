<?php
/*
	Section: Secondary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The secondary widgetized sidebar for the theme.
	Class Name: SecondarySidebar	
	Workswith: sidebar1, sidebar2, sidebar_wrap
	Persistant: true
*/

/**
 * Secondary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class SecondarySidebar extends PageLinesSection {

	/**
	* PHP that always loads no matter if section is added or not.
	*/
   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		pagelines_register_sidebar($setup, 2);
	}

	/**
	* Section template.
	*/
   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}

}