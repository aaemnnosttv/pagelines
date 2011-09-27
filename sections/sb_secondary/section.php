<?php
/*
	Section: Secondary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The secondary widgetized sidebar for the theme.
	Class Name: SecondarySidebar
	Tags: internal
	Workswith: sidebar1, sidebar2, sidebar_wrap
*/

class SecondarySidebar extends PageLinesSection {

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