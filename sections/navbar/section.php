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
		wp_enqueue_script('bootstrap-dropdown', $this->base_url.'/bootstrap-dropdown.js');
	}
	
	function section_head($clone_id){
		
		
		?>
		
		<script>
		jQuery(document).ready(function() {
			
			var section = 1;
			
			jQuery('.pldrop').find('ul').each(function() {
				jQuery(this).addClass('dropdown-menu');
				jQuery(this).siblings('a')
					   .addClass('dropdown-toggle')
					   .attr('href', '#m' + section)
					   .attr('data-toggle', 'dropdown')
					   .append(' <b class="caret" />')
					   .parent()
					   .attr('id', 'm' + section++)
					   .addClass('dropdown'); 
			});
			
			jQuery('.dropdown-toggle').dropdown()
		});
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
	      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </a>

			<a class="plbrand" href="">
				<img src="<?php echo $this->base_url.'/logo.png';?>" />
			</a>
			<?php // pagelines_main_logo( $this->id ); 
			
			?>
	      <!-- <a class="brand" href="#">Project name</a> -->

	      <div class="nav-collapse">
	       <?php wp_nav_menu( array('menu_class'  => 'font-sub navline pldrop '.pagelines_nav_classes(), 'container' => null, 'container_class' => '', 'depth' => 2, 'theme_location'=>'primary', 'fallback_cb'=>'pagelines_nav_fallback') );
	
	
				if(!ploption('hidesearch'))
					get_search_form();
	?>
				
	      </div>

	    </div>
	  </div>
	</div>
	
		<?php 
	}


}