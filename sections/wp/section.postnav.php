<?php
/*

	Section: PostNav
	Author: Andrew Powers
	Description: Paginates posts, shows a numerical post navigation
	Version: 1.0.0
	
*/

class PageLinesPostNav extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Post Navigation', 'pagelines');
		$id = 'postnav';
	
		
		$settings = array(
			'type' 			=> 'main',
			'description' 	=> 'Post Navigation - Shows titles for next and previous post.',
			'workswith' 	=> array('main-single', 'main-default'),
			'folder' 		=> 'wp', 
			'init_file' 	=> 'postnav', 
			'icon'			=> PL_ADMIN_ICONS . '/map.png'
		);
		

	   parent::__construct($name, $id, $settings);    
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

/*
	End of section class
*/