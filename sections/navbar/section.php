<?php
/*
	Section: NavBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive and sticky navigation bar for your website.
	Class Name: PLNavBar	
	Workswith: header
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLNavBar extends PageLinesSection {

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
	<div class="navbar">
	  <div class="navbar-inner">
	    <div class="container">

	      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
	      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </a>

	      <!-- Be sure to leave the brand out there if you want it shown -->
	      <a class="brand" href="#">Project name</a>

	      <!-- Everything you want hidden at 940px or less, place within here -->
	      <div class="nav-collapse">
	        <!-- .nav, .navbar-search, .navbar-form, etc -->
	      </div>

	    </div>
	  </div>
	</div>
	
		<?php 
	}


}