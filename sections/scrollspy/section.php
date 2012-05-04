<?php
/*
	Section: Scroll Spy
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A special section with auto scroll content.
	Class Name: ScrollSpy
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
class ScrollSpy extends PageLinesSection {

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		
		wp_enqueue_script( 'pagelines-bootstrap-all' );
		wp_enqueue_script( 'jquery' );
	}


	function section_head($clone_id){ ?>
		
		<script>
		jQuery(document).ready(function() {
			
			var section = 1;
			
			jQuery('.pagelines-scrollspy').scrollspy()

		});
		</script>	

	<?php }	


	/**
	* Section template.
	*/
	function section_template() {  
	
		global $post;
			
		printf('
			<div id="spynav" class="spynav">
	          <ul class="nav nav-pills">
	            <li class=""><a href="#label1">Label1</a></li>
	            <li class=""><a href="#label2">Label2</a></li>
	            <li class=""><a href="#label3">Label3</a></li>
	            <li class=""><a href="#label4">Label4</a></li>
	          </ul>
	        </div>
			<div class="pagelines-scrollspy" data-target="#spynav" data-spy="scroll" >%s%s</div>', 
			do_shortcode($post->post_content), 
			pledit($post->ID));
	 
	}

}