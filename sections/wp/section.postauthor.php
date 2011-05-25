<?php
/*

	Section: PostAuthor
	Author: Andrew Powers
	Description: Adds post author to page/single post.
	Version: 1.0.0
	
*/

class PageLinesPostAuthor extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Post Author Info', 'pagelines');
		$id = 'postauthor';
	
		
		$settings = array(
			'description' 	=> 'Adds information about the author of a blog post or page. Add user information under "users" in the admin.',
			'workswith' 	=> array('main-single', 'main-default'),
			'icon'			=> PL_ADMIN_ICONS . '/author.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
	global $post; 
	setup_postdata($post);
?>
		
		<div class="author-info">
			<div class="author-thumb">
				<?php echo get_avatar(get_the_author_meta('email', $post->post_author), $size = '80', $default = PL_IMAGES . '/avatar_default.gif' ); ?>
			</div>
			<small class="subtext"><?php _e('About The Author', 'pagelines');?></small>
			<h2>
				<?php echo get_the_author(); ?>
			</h2>
			<p><?php the_author_meta('description', $post->post_author); ?></p>
			<div class="author-details">
				<a href="<?php the_author_meta('url'); ?>" target="_blank">
				<?php _e('Visit Authors Website', 'pagelines');?> &rarr;
				</a>
			</div>
		
		</div>
		<div class="clear"></div>
<?php	}

}

/*
	End of section class
*/