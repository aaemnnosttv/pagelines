<?php
/*
	Section: NavBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive and sticky navigation bar for your website.
	Class Name: PLNavBar	
	Workswith: header
	Compatibility: 2.2
	Format: open
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


	function after_section_template(){
		echo '<div class="section-navbar-spacer"></div>';
	}
	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
	?>
	<div class="navbar fix">
	  <div class="navbar-inner content">
	    <div class="navbar-content-pad fix">

	      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
	      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </a>


			<?php // pagelines_main_logo( $this->id ); ?>
	      <!-- Be sure to leave the brand out there if you want it shown -->
	      <a class="brand" href="#">Project name</a>

	      <!-- Everything you want hidden at 940px or less, place within here -->
	      <div class="nav-collapse">
	       <?php wp_nav_menu( array('menu_class'  => 'font-sub navline '.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 2, 'theme_location'=>'primary', 'fallback_cb'=>'pagelines_nav_fallback') );
	
	?>
	      </div>

	    </div>
	  </div>
	</div>
	
		<?php 
	}


}