<?php
/*
	Section: Universal Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A universal widgetized sidebar
	Class Name: UniversalSidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap, templates, main, header, morefoot
	Edition: pro
*/

class UniversalSidebar extends PageLinesSection {

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