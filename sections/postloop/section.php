<?php
/*
	Section: PostLoop
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: The Main Posts Loop. Includes content and post information.
	Class Name: PageLinesPostLoop
	Workswith: main
	Failswith: 404_page
*/

/**
 * Main Post Loop Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesPostLoop extends PageLinesSection
{

	function section_persistent()
	{
		if ( file_exists("{$this->base_dir}/inc/PageLinesPosts.php") )
		{
			require_once "{$this->base_dir}/inc/PageLinesPosts.php";
		}
	}

	function section_template()
	{
   		$posts_class = apply_filters( 'pagelines_posts_class', 'PageLinesPosts' );
		$theposts = new $posts_class();
		$theposts->load_loop();
	}

}