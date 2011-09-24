<?php
/*
	Section: PostNav
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Post Navigation - Shows titles for next and previous post.
	Class Name: PageLinesPostNav
	Tags: internal
*/

class PageLinesPostNav extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
		
		$default_settings = array(
			'type' 			=> 'main',
			'workswith' 	=> array('main'),
			'failswith'		=> pagelines_special_pages(),
		);
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($settings);    
   }

   function section_template() { ?>
   	<?php pagelines_register_hook( 'pagelines_section_before_postnav' ); // Hook ?>
		<div class="post-nav fix"> 
			<span class="previous"><?php previous_post_link('%link') ?></span> 
			<span class="next"><?php next_post_link('%link') ?></span>
		</div>
	<?php pagelines_register_hook( 'pagelines_section_after_postnav' ); // Hook ?>
<?php }

}
