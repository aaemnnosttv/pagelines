<?php
/*
	Section: Full Width Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows full width widgetized sidebar.
	Class Name: FullWidthSidebar
	Workswith: templates, footer, morefoot
*/

/**
 * Full Width Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class FullWidthSidebar extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() {
		 pagelines_draw_sidebar($this->id, $this->name);
	}
}