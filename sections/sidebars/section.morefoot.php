<?php
/*

	Section: Morefoot Sidebars
	Author: Andrew Powers
	Description: Three widgetized sidebars above footer
	Version: 1.0.0
	
*/

class PageLinesMorefoot extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('MoreFoot Sidebars <small>(3-Column)</small>', 'pagelines');
		$id = 'morefoot';
	
		
		$settings = array(
			'description' 	=> 'Displays three widgetized sidebars that you can set up in the widgets panel.',
			'workswith' 	=> array('content'),
			'icon'			=> PL_ADMIN_ICONS . '/column.png',
			'version'		=> 'pro'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){
		register_sidebar(array(
		'name'=>'MoreFoot Left',
		'description' => __('Left sidebar in "morefoot" element enabled in options.', 'pagelines'),
		    'before_widget' => '<div id="%1$s" class="%2$s widget fix">',
		    'after_widget' => '</div>',
		    'before_title' => '<h3 class="widget-title">',
		    'after_title' => '</h3>'
		));
		register_sidebar(array(
		'name'=>'MoreFoot Middle',
		'description' => __('Middle sidebar in "morefoot" element enabled in options.', 'pagelines'),
		    'before_widget' => '<div id="%1$s" class="%2$s widget fix">',
		    'after_widget' => '</div>',
		    'before_title' => '<h3 class="widget-title">',
		    'after_title' => '</h3>'
		));
		register_sidebar(array(
		'name'=>'MoreFoot Right',
		'description' => __('Right sidebar in "morefoot" element enabled in options.', 'pagelines'),
		    'before_widget' => '<div id="%1$s" class="%2$s widget fix">',
		    'after_widget' => '</div>',
		    'before_title' => '<h3 class="widget-title">',
		    'after_title' => '</h3>'
		));
	}

   function section_template() { 
		
			global $post;
			global $bbpress_forum;
			if( !VPRO) $hide_footer = true;
			else $hide_footer = false;		
		?>
		<?php if(!$hide_footer):?>

			<div class="morefoot_back fix">
				<div id="morefootbg" class=" fix">
					<div class="dcol_container_3">
						<div class="dcol_3 dcol wcontain fix">	
							<div class="dcol-pad">
								<?php if (!dynamic_sidebar('MoreFoot Left') ) : ?>
								<div class="widget">
									<?php if(!pagelines('sidebar_no_default')):?>
										<h3 class="widget-title"><?php _e('Looking for something?','pagelines');?></h3>
										<p><?php _e('Use the form below to search the site:','pagelines');?></p>
										<?php get_search_form(); ?> 
										<br class="clear"/>
										<p><?php _e('Still not finding what you\'re looking for? Drop a comment on a post or contact us so we can take care of it!','pagelines');?></p>

									<?php endif;?>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="dcol_3 dcol wcontain">
							<div class="dcol-pad">
								<?php if ( !dynamic_sidebar('MoreFoot Middle') ) : ?>
								<div class="widget">
									<?php if(!pagelines('sidebar_no_default')):?>

										<h3 class="widget-title"><?php _e('Visit our friends!','pagelines');?></h3><p><?php _e('A few highly recommended friends...','pagelines');?></p><ul><?php wp_list_bookmarks('title_li=&categorize=0'); ?></ul>

									<?php endif;?>
								</div>
								<?php endif; ?>
							</div>
						</div>
						<div class="dcol_3 lastcol dcol wcontain">
							<div class="dcol-pad">
							<?php if (!dynamic_sidebar('MoreFoot Right') ) : ?>
								<div class="widget">
								<?php if(!pagelines('sidebar_no_default')):?>

									<h3 class="widget-title"><?php _e('Archives', 'pagelines');?></h3><p><?php _e('All entries, chronologically...','pagelines');?></p><ul><?php wp_get_archives('type=monthly&limit=12'); ?> </ul>

								<?php endif;?>
								</div>
							<?php endif; ?>
							</div>
						</div>
					</div>

				</div>
			</div>
		<?php endif; ?>
		<?php
	}

}

/*
	End of section class
*/