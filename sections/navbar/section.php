<?php
/*
	Section: NavBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive and sticky navigation bar for your website.
	Class Name: PLNavBar	
	Workswith: header
	Compatibility: 2.2
	Cloning: false
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLNavBar extends PageLinesSection {

	var $default_limit = 2;

	function section_persistent(){
	
		$website_setup_options = array(
			'navbar_fixed' => array(
					'default'	=> true,
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
					'title'		=> __( 'Add Fixed Navigation Bar', 'pagelines' ),
					'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
					'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.', 'pagelines' )
				),
			'navbar_logo' => array(
					'default'	=> $this->base_url.'/logo.png',
					'version'	=> 'pro',
					'type'		=> 'image_upload',
					'inputlabel'=> __( 'Fixed NavBar Logo', 'pagelines' ),
					'title'		=> __( 'Fixed NavBar Logo', 'pagelines' ),
					'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
					'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.', 'pagelines' )
				),
			
		);
		
		$header_options = array(
			'navbar_alignment' => array(
					'default'		=> 'left',
					'type' 			=> 'select',
					'inputlabel' 	=> 'Select Alignment',
					'title' 		=> 'NavBar Navigation Alignment',			
					'shortexp' 		=> 'Aligns the nav left or right (defaults left)',
					'exp' 			=> 'Set the NavBar navigation to display on the right or left', 
					'selectvalues'	=> array(
						'right'		=> array('name'	=>'Align Right'),
						'left'		=> array('name'	=>'Align Left'),
					),
				),
			'navbar_theme' => array(
					'default'		=> 'black-trans',
					'type' 			=> 'select',
					'inputlabel' 	=> 'Select NavBar Theme',
					'title' 		=> 'NavBar Theme',			
					'shortexp' 		=> 'Select the color and theme of the NavBar',
					'exp' 			=> 'The NavBar comes with several color options. Select one to automatically configure.', 
					'selectvalues'	=> array(
						'black-trans'	=> array('name'	=>'Black Transparent (Default)'),
						'blue'			=> array('name'	=>'Blue'),
						'grey'			=> array('name'	=>'Light Grey'),
						'orange'		=> array('name'	=>'Orange'),
						'red'			=> array('name'	=>'Red'),
					),
				),
			
		);

		pl_global_option( array( 'menu' => 'website_setup', 'options' => $website_setup_options, 'location' => 'top' ) );
		pl_global_option( array( 'menu' => 'header_and_footer', 'options' => $header_options, 'location' => 'top' ) );
		
		
		if(ploption('navbar_fixed')){
			
			build_passive_section(array('sid' => $this->class_name));
		
			add_action('pagelines_before_page', array(&$this,'passive_section_template'), 10, 2);
				
			
			
		}
		

		
	}

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
		
		<?php if(ploption('navbar_fixed')): ?>
		<style id="navbar-css" type="text/css">
			#site #page {padding-top: 40px}
			.fixed_width #site #page {padding-top: 52px;}
		</style>
		<?php endif;?>
	<?php }

	function before_section_template( $location = ''){
		
		$format = ($location == 'passive') ? 'open' : 'standard';
		$this->special_classes .= ($location == 'passive') ? ' fixed-top' : '';
		$this->settings['format'] = $format;
		
	}

	/**
	* Section template.
	*/
   function section_template($clone_id, $location = '') { 
	
	$passive = ($location == 'passive') ? true : false;

	$width_class = ($passive) ? 'navbar-full-width' : 'navbar-content-width';

	$content_width_class = ($passive) ? 'content' : '';
		
	$align = (ploption('navbar_alignment')) ? ploption('navbar_alignment') : 'left';
		
	$align_class = sprintf('pull-%s', $align);	
	
	$theme_class = (ploption('navbar_theme')) ? sprintf(' pl-color-%s', ploption('navbar_theme')) : ' pl-color-black-trans';
	?>
	<div class="navbar fix <?php echo $width_class.' '.$theme_class; ?>">
	  <div class="navbar-inner <?php echo $content_width_class;?>">
	    <div class="navbar-content-pad fix">
		
	      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </a>

			<?php if($passive): ?>
				<a class="plbrand" href="<?php echo esc_url(home_url());?>">
					
					<?php 
					
						if(ploption('navbar_logo') || ploption('navbar_logo') != '')
							printf('<img src="%s" />', ploption('navbar_logo'));
						else
							printf('<h2 class="plbrand-text">%s</h2>', get_bloginfo('name'));
						
						?>
				</a>
			<?php endif; ?>

	      <div class="nav-collapse">
	       <?php 	if(!ploption('hidesearch'))
						get_search_form();
				
					wp_nav_menu( 
						array(
							'menu_class'  => 'font-sub navline pldrop '.$align_class, 
							'container' => null, 
							'container_class' => '', 
							'depth' => 2, 
							'theme_location'=>'primary', 
							'fallback_cb'=>'pagelines_nav_fallback'
						) 
					);
	
	
				
	?>
				</div>
			</div>
		</div>
	</div>
	
		<?php 
	}


}