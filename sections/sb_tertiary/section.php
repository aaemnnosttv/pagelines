<?php
/*
	Section: Tertiary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A 3rd widgetized sidebar for the theme that can be used in standard sidebar templates.
	Class Name: TertiarySidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap
*/

class TertiarySidebar extends PageLinesSection {

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