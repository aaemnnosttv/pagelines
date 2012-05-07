<?php
/*
	Section: Grid Content
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A special container section that styles and handles HTML grid oriented content. Fully documented at PageLines.com.
	Class Name: HTMLGrid
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
class HTMLGrid extends PageLinesSection {


	/**
	* Section template.
	*/
	function section_template() {  
	
		global $post;
			
		printf('<div class="pagelines-grid hentry">%s%s</div>', do_shortcode($post->post_content), pledit($post->ID));
	 
	}

}