<?php
/*
	Section: Content Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Displays a widgetized sidebar inside the main content area. Set it up in the widgets panel.
	Class Name: ContentSidebar
	Tags: internal
	Workswith: main-default, main-posts, main-single, main-404
*/

class ContentSidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		register_sidebar($setup);
	}

   function section_template() { 
	 	pagelines_draw_sidebar($this->id, $this->name);
	}

}

/*
	End of section class
*/