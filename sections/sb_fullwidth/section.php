<?php
/*
	Section: Full Width Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows full width widgetized sidebar.
	Class Name: FullWidthSidebar
	Edition: pro
	Workswith: templates, footer, morefoot
*/

class FullWidthSidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		register_sidebar($setup);
	
	}

   function section_template() { 
		 pagelines_draw_sidebar($this->id, $this->name);
	}
}