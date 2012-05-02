<?php
/*
	Section: OpenGrid Content
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: (Needs Description)
	Class Name: OpenGrid
	Workswith: templates
	Cloning: false
	Failswith: pagelines_special_pages()
*/

/**
 * Content Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class OpenGrid extends PageLinesSection {


	/**
	* Section template.
	*/
	function section_template() {  
	
		global $post;
			
		printf('<div class="fluid-grid">%s%s</div>', do_shortcode($post->post_content), pledit($post->ID));
	 
	}

}