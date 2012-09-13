<?php
/*
	Section: PostAuthor
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds post author to page/single post.
	Class Name: PageLinesPostAuthor	
	Workswith: main-single, author
	Failswith: archive, category, posts, tags, search, 404_page
*/

/**
 * Post Author Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesPostAuthor extends PageLinesSection {

	/**
	* Section template.
	*/
   function section_template() { 
	global $post; 
	setup_postdata($post);
	
	ob_start(); 
		the_author_meta('url');
	$link = ob_get_clean();
?>
		
		<div class="media author-info">
			<div class="img thumbnail author-thumb">
				<a class="thumbnail" href="<?php echo $link; ?>" target="_blank">
					<?php echo get_avatar( get_the_author_meta('email', $post->post_author), $size = '120', $default = PL_IMAGES . '/avatar_default.gif' ); ?>
				</a>
			</div>
			<div class="bd">
				<small class="subtext"><?php _e('About The Author', 'pagelines');?></small>
				<h2>
					<?php echo get_the_author(); ?>
				</h2>
				<p><?php the_author_meta('description', $post->post_author); ?></p>
				<div class="author-details">
					<?php if($link != ''): ?>
					<a href="<?php $link; ?>" target="_blank">
						<?php _e('Visit Authors Website', 'pagelines');?> &rarr;
					</a>
					<?php endif;?>
			<?php	$google_profile = get_the_author_meta( 'google_profile' );
					if ( $google_profile ) {
						echo '<br /><a href="' . $google_profile . '" rel="me">Authors Google Profile &rarr;</a>';
					} ?>
				</div>
			</div>
		
		</div>
		<div class="clear"></div>
<?php	}

}