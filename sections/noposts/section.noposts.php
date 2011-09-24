<?php
/*
	Section: No Posts
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shown when no posts or 404 is returned
	Class Name: PageLinesNoPosts
	Tags: internal
*/

class PageLinesNoPosts extends PageLinesSection {

   function __construct( $registered_settings = array() ) {

		$default_settings = array(
			'type' 			=> 'main',
			'workswith' 	=> array('404'),
		);
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($settings);    
   }

   function section_template() { ?>
	<div id="notfound">
	<?php if(current_user_can( 'edit_posts' ) && isset($_GET['boxes']) || isset($_GET['feature']) || isset($_GET['banners']) ):?>
	
			<h2 class="notavail center"><?php _e('Direct Previewing <em>of</em> "Special Post Types" Not Available... Yet','pagelines');?></h2>
			<p class="subhead center"><?php _e('Sorry, direct previewing of special "post types" such as <strong>"features"</strong> or <strong>"boxes"</strong> isn\'t unavailable. This WordPress functionality is new and rapidly developing, so it should be available soon.', 'pagelines');?></p>
			<p class="subhead center">
				<?php _e('To preview a "custom post type" just view a page with that "section" on it.', 'pagelines');?>
			</p>
	
	<?php else: ?>
			
				<h2 class="notfound-splash center"><?php _e('404!','pagelines');?></h2>
				<p class="subhead center"><?php _e('Sorry, This Page Does not exist.', 'pagelines');?><br/><?php _e('Go','pagelines');?> <a href="<?php echo home_url(); ?>"><?php _e('home','pagelines');?></a> <?php _e('or try a search?', 'pagelines');?></p>
			
	<?php endif;?>
		<div class="center fix"><?php get_search_form(); ?> </div>
	</div>
	<?php }

}

/*
	End of section class
*/