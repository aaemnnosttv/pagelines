<?php
/*
	Section: Universal Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A universal widgetized sidebar
	Class Name: UniversalSidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap, templates, main, header, morefoot
	Edition: pro
	Persistant: true
*/

/**
 * Universal Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 **/
class UniversalSidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		pagelines_register_sidebar($setup, 4);
	}

   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}

}