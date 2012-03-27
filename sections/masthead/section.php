<?php
/*
	Section: Masthead
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width splash and text area. Great for getting big ideas across quickly.
	Class Name: PLMasthead	
	Workswith: header
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLMasthead extends PageLinesSection {

	var $default_limit = 2;

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
	}
	
	function section_head($clone_id){
		
		
		?>
		
		<script>
		 
		</script>	
		
	<?php }

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
	?>

	
		<?php 
	}


}