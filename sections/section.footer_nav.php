<?php
/*

	Section: Simple Footer Navigation
	Author: Adam Munns
	Description: Creates footer navigation.
	Version: 1.0.0
	
*/

class PageLinesSimpleFooterNav extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Simple Footer Navigation', 'pagelines');
		$id = 'simple_footer_nav';

		
		$default_settings = array(
			'description' 	=> __('A single column footer for a simpler footer navigation', 'pagelines'),
			'workswith' => array('footer'),
			'description' => 'Footer Site Navigation.',
	
			'icon'			=> PL_ADMIN_ICONS . '/map.png'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );

	   parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){
		register_nav_menus( array( 'footer_nav_simple' => __( 'Page Navigation in Simple Footer Section', 'pagelines' ) ) );
	}
	
	
	
   function section_template() { 
	?>
	<div id="logo">
		<?php if(pagelines_option('footer_logo') && VPRO):?>
			<a class="home" href="<?php echo home_url(); ?>" title="<?php _e('Home','pagelines');?>">
				<img src="<?php echo pagelines_option('footer_logo');?>" alt="<?php bloginfo('name');?>" />
			</a>
		<?php else:?>
			<h1 class="site-title">
				<a class="home" href="<?php echo home_url(); ?>" title="<?php _e('Home','pagelines');?>">
					<?php bloginfo('name');?>
				</a>
			</h1>
		<?php endif;?>
	</div>
	
	<?php function nav_fallback_simple() {?>
			<ul id="simple_footer">
			  	<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=1'); ?>
			</ul><?php
		}
	if(function_exists('wp_nav_menu')):
		wp_nav_menu( array('theme_location'=>'footer_nav_simple','depth' => 1,  'fallback_cb'=>'nav_fallback_simple') );
	else:
		nav_fallback();
	endif;?>
	
	<span class="terms">
		<?php e_pagelines('footer_terms');?>
	</span>	

<?php }

}
/*
	End of section class
*/