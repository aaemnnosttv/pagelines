<?php
/*
	Section: PostLoop
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The Main Posts Loop. Includes content and post information.
	Class Name: PageLinesPostLoop	
	Workswith: main
*/

class PageLinesPostLoop extends PageLinesSection {

   function section_template() { 
		//Included in theme root for easy editing.
		$theposts = new PageLinesPosts();
		$theposts->load_loop();
	}

}

/*
	End of section class
*/