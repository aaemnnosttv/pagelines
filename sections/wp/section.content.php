<?php
/*

	Section: Content
	Author: Andrew Powers
	Description: Creates a flickr, nextgen, or featured image carousel.
	Version: 1.0.0
	Class Name: PageLinesContent
*/

class PageLinesContent extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Content Area', 'pagelines');
		$id = 'maincontent';
	
		
		$settings = array(
			'description' 	=> 'This is the section that contains the main content for your site, including sidebars and page/post content.',
			'workswith' 	=> array('templates'),
			'failswith'		=> array('404'),
			
			'icon'			=> PL_ADMIN_ICONS . '/document.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() {  
	 	global $pagelines_layout;

?>
		<div id="pagelines_content" class="<?php echo $pagelines_layout->layout_mode;?> fix">

			<?php pagelines_register_hook( 'pagelines_content_before_columns', 'maincontent' ); // Hook ?>
			<div id="column-wrap" class="fix">

				<?php pagelines_register_hook( 'pagelines_content_before_maincolumn', 'maincontent' ); // Hook ?>
				<div id="column-main" class="mcolumn fix">
					<div class="mcolumn-pad" >
						<?php pagelines_template_area('pagelines_main', 'main'); ?>
					</div>
				</div>

				<?php if($pagelines_layout->layout_mode == 'two-sidebar-center'):?>
					<?php pagelines_register_hook( 'pagelines_content_before_sidebar1', 'maincontent' ); // Hook ?>
					<div id="sidebar1" class="scolumn fix">
						<div class="scolumn-pad">
							<?php pagelines_template_area('pagelines_sidebar1', 'sidebar1'); ?>
						</div>
					</div>
					<?php pagelines_register_hook( 'pagelines_content_after_sidebar1', 'maincontent' ); // Hook ?>
				<?php endif;?>

			</div>	

			<?php get_sidebar(); ?>
		</div>
<?php }

}

/*
	End of section class
*/