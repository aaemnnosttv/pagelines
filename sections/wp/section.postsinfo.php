<?php
/*

	Section: PageLines PostsInfo
	Author: Andrew Powers
	Description: Shows information about posts being viewed (e.g. "Currently Viewing Archives from...")
	Version: 1.0.0
	
*/

class PageLinesPostsInfo extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Posts Info', 'pagelines');
		$id = 'postsinfo';
	
		
		$default_settings = array(
			'description' 	=> 'Shows information about posts being viewed (e.g. "<em>Currently Viewing Archives from...</em>") Not shown on blog pages.',
			'workswith' 	=> array('main-posts'),
			'icon'			=> PL_ADMIN_ICONS . '/postsinfo.png'
		);
		
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);   
   }

   function section_template() { 	
	
		if(is_category() || is_archive() || is_search()):
		
		?>
		
			<div class="current_posts_info">
				<?php if(is_search()):?>
					<?php _e("Search results for ", 'pagelines');?> 
					<strong>"<?php the_search_query();?>"</strong>
				<?php elseif(is_category()):?>
					<?php _e("Currently viewing the category: ", 'pagelines');?> 
					<strong>"<?php single_cat_title();?>"</strong>
				<?php elseif(is_tag()):?>
					<?php _e("Currently viewing the tag: ", 'pagelines');?>
					<strong>"<?php single_tag_title(''); ?>"</strong>
				<?php elseif(is_archive()):?>
					<?php if (is_author()) { 
						global $author;
						global $author_name;
						$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
						_e('Posts by:', 'pagelines'); ?>
						<strong><?php echo $curauth->display_name; ?></strong>
					<?php } elseif (is_day()) {	?>
					 	<?php _e('From the daily archives:', 'pagelines'); ?>
						<strong><?php the_time('l, F j, Y'); ?></strong>
					<?php } elseif (is_month()) { ?>
						<?php _e('From the monthly archives:', 'pagelines'); ?>
						<strong><?php the_time('F Y'); ?></strong>
					<?php } elseif (is_year()) { ?>
						<?php _e('From the yearly archives:', 'pagelines'); ?>
						<strong><?php the_time('Y'); ?></strong>
					<?php } else {?> 
						<?php _e("Viewing archives for ", 'pagelines');?>
						<strong>"<?php the_date();?>"</strong>
					<?php } ?>
				<?php endif;?>
			</div>
		<?php endif;
	}

}

/*
	End of section class
*/