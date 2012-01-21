<?php
/*
	Section: Post/Page Pagination
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Pagination - A numerical post/page navigation. (Supports WP-PageNavi)
	Class Name: PageLinesPagination
	Workswith: main
	Failswith: pagelines_special_pages()
*/

class PageLinesPagination extends PageLinesSection {

   function section_template() { 
		pagelines_pagination();
	}
}