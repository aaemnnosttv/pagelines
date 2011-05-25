<?php
/*

	Section: Footer Columns Sidebar
	Author: Andrew Powers
	Description: A 5 column widgetized sidebar in the footer
	Version: 1.0.0
	
*/

class PageLinesFootCols extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Footer Sidebars <small>(5-Column)</small>', 'pagelines');
		$id = 'footcols';
	
		
		$default_settings = array(
			'type' 			=> 'standard',
			'description' 	=> 'Displays a 5 column sidebar in the footer that can be setup to use widgets or theme options. Add widgets in the widgets panel to activate widget mode.',
			'workswith' 	=> array('footer'),
			'folder' 		=> '', 
			'init_file' 	=> 'footcols.php', 
			'icon'			=> PL_ADMIN_ICONS . '/column.png'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){
		register_sidebar(array(
		'name'=>$this->name,
		'description' => __('Use this sidebar if you want to use widgets in your footer columns instead of the default.', 'pagelines'),
		    'before_widget' => '<div id="%1$s" class="%2$s dcol_5 dcol"><div class="dcol-pad">',
		    'after_widget' => '</div></div>',
		    'before_title' => '<h3 class="widget-title">',
		    'after_title' => '</h3>'
		));
		
		register_nav_menus( array(
			'footer_nav' => __( 'Page Navigation in Footer Columns', 'pagelines' )
		) );
	
		
	}
	
	function section_template() { 
		?>
			<div id="fcolumns_container" class="dcol_container_5 fix">
				
				<?php if (!dynamic_sidebar($this->name) ) : ?>
					<div class="dcol_5 dcol">
						<div class="dcol-pad">
							<?php if(pagelines_option('footer_logo') && VPRO):?>
								<a class="home" href="<?php echo home_url(); ?>" title="<?php _e('Home','pagelines');?>">
									<img src="<?php echo pagelines_option('footer_logo');?>" alt="<?php bloginfo('name');?>" />
								</a>
							<?php else:?>
								<h3 class="site-title">
									<a class="home" href="<?php echo home_url(); ?>" title="<?php _e('Home','pagelines');?>">
										<?php bloginfo('name');?>
									</a>
								</h3>
							<?php endif;?>
						</div>
					</div>
					<div class="dcol_5 dcol">
						<div class="dcol-pad">
							<h3 class="widget-title"><?php _e('Pages','pagelines');?></h3>
								<?php wp_nav_menu( array('menu_class' => 'footer-links list-links', 'theme_location'=>'footer_nav', 'depth' => 1) ); ?>

						</div>
					</div>
					<div class="dcol_5 dcol">
						<div class="dcol-pad">
							<h3 class="widget-title"><?php _e('The Latest','pagelines');?></h3>
								<ul class="latest_posts">
								<?php foreach(get_posts('numberposts=1&offset=0') as $key => $post):
										setup_postdata($post);?>
										<li class="list-item fix">
											<div class="list_item_text">
												<h5><a class="list_text_link" href="<?php echo get_permalink( $post->ID ); ?>"><span class="list-title"><?php echo $post->post_title; ?></span></a></h5>
												<div class="list-excerpt"><?php echo ( !is_404() ) ? custom_trim_excerpt(get_the_excerpt(), 12) : ''; ?></div>
											</div>
										</li>
								<?php endforeach;?></ul>
						</div>
					</div>
					<div class="dcol_5 dcol">
						<div class="dcol-pad">
							<h3 class="widget-title"><?php _e('More','pagelines');?></h3>
							<div class="findent footer-more">
								<?php print_pagelines_option('footer_more');?>
							</div>
						</div>
					</div>
					<div class="dcol_5 dcol">
						<div class="dcol-pad">
							<div class="findent terms">
								<?php print_pagelines_option('footer_terms');?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>	
			<div class="clear"></div>
		<?php
	}

}

/*
	End of section class
*/