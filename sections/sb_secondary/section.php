<?php
/*
	Section: Secondary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The secondary widgetized sidebar for the theme.
	Class Name: SecondarySidebar	
	Workswith: sidebar1, sidebar2, sidebar_wrap
*/

/**
 * Secondary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 **/
class SecondarySidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		pagelines_register_sidebar($setup, 2);
	}

   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}

}