<?php
/**
 * 
 *
 *  PageLines Posts Handling
 *
 *
 *  @package PageLines Platform
 *  @subpackage Posts
 *  @since 2.0.b2
 *
 */
class PageLinesPosts {

	var $tabs = array();	
	
	/**
	 * PHP5 constructor
	 *
	 */
	function __construct( ) {
	
		global $pagelines_layout; 
		global $post;
		global $wp_query;
		
		$count = 1;  // Used to get the number of the post as we loop through them.
		$clipcount = 2; // The number of clips in a row

		$post_count = $wp_query->post_count;  // Used to prevent markup issues when there aren't an even # of posts.
		$paged = intval(get_query_var('paged')); // Control output if on a paginated page

		if(is_admin()) query_posts('showposts=1'); // For parsing in admin, no posts so set it to one.

		$thumb_space = get_option('thumbnail_size_w') + 33; // Space for thumb with padding
	
	
	}
	
	function load_loop(){
	
		if(have_posts())
			while (have_posts()) : the_post();  $this->get_article(); endwhile;
		else 
			$this->posts_404();
		
	}
	
	function get_article(){
		
		if( pagelines_show_clip($count, $paged) ):
		
		if($clipcount % 2 == 0):?>
			<div class="clip_box fix">
			<?php pagelines_register_hook( 'pagelines_loop_clipbox_start', 'theloop' ); // Hook ?>
			<?php $clips_in_row = 1;?>
		<?php endif;?>
		
		<article <?php post_class('fpost') ?> id="post-<?php the_ID(); ?>">
			
			<?php pagelines_register_hook( 'pagelines_loop_post_start', 'theloop' ); // Hook ?>
			
			<?php $this->post_header(); ?>
			
			<?php $this->post_entry(); ?>
			
			<?php pagelines_register_hook( 'pagelines_loop_post_end', 'theloop' ); // Hook ?>
			
		</article>
		
	<?php }
	
	function post_entry(){ ?>
		
		<?php  if(pagelines_show_content( get_the_ID() )): // Post and Page Content ?>  	
			<div class="entry_wrap fix">
			<?php pagelines_register_hook( 'pagelines_loop_before_post_content', 'theloop' ); // Hook ?>

				<div class="entry_content">
					<?php 
						the_content(__('<p>Continue reading &raquo;</p>','pagelines'));?>
						<div class="clear"></div> 
						<?php 
							// Content pagination
							if( is_single() || is_page() ) wp_link_pages(array('before'=> __('<p class="content-pagination"><span class="cp-desc">pages:</span>', 'pagelines'), 'after' => '</p>', 'pagelink' => '<span class="cp-num">%</span>')); 
							
							// Edit Link
							$edit_type = (is_page()) ? __('Edit Page','pagelines') : __('Edit Post','pagelines');
							edit_post_link( '['.$edit_type.']', '', ''); 
							
							pagelines_register_hook( 'pagelines_loop_after_post_content', 'theloop' ); // Hook 
					?>
				</div>	
				<div class="tags">
					<?php the_tags(__('Tagged with: ', 'pagelines'),' &bull; ','<br />'); ?>&nbsp;
				</div>
			</div>
		<?php endif;?>
		
	<?php }
	
	function post_header(){ ?>
		
		<?php if(!is_page() || (is_page() && pagelines_option('pagetitles'))):?>
			<section class="post-meta fix">	
				<?php if(pagelines_show_thumb( get_the_ID() )): // Thumbnails ?>
	            		<div class="post-thumb" style="margin-right:-<?php echo $thumb_space;?>px">
							<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Permanent Link To', 'pagelines');?> <?php the_title_attribute();?>">
								<?php the_post_thumbnail('thumbnail');?>
							</a>
			            </div>
				<?php endif; ?>

				<section class="post-header fix <?php if(!pagelines_show_thumb($post->ID)) echo 'post-nothumb';?>" style="<?php 
			
					if(pagelines_show_thumb($post->ID)) echo 'margin-left:'.$thumb_space.'px';
				
					?>" >
					<?php pagelines_register_hook( 'pagelines_loop_post_header_start', 'theloop' ); // Hook ?>
					<section class="post-title-section fix">

						<hgroup class="post-title fix">
						
							<?php 
								pagelines_get_post_title();
								pagelines_get_post_metabar();
							?>
						
						</hgroup>
					</section>
				
					<?php if(pagelines_show_excerpt( $post->ID ) && !is_page()): // Post Excerpt ?>
					
							<aside class="post-excerpt"><?php the_excerpt(); ?></aside>

							<?php 
							if(pagelines_is_posts_page() && !pagelines_show_content( $post->ID )) // 'Continue Reading' link
								echo get_continue_reading_link($post->ID);
						
							pagelines_register_hook( 'pagelines_after_excerpt', 'theloop' ); // Hook ?>
					<?php endif; ?>
				</section>			
				
			</section>
		<?php endif; ?>
		
	<?php }
	
	function posts_404(){
		
		$head = ( is_search() ) ? sprintf(__('No results for "%s"', 'pagelines'), get_search_query()) : __('Nothing Found', 'pagelines');
		
		$subhead = ( is_search() ) ? __('Try another search?', 'pagelines') : __('Sorry, what you are looking for isn\'t here.', 'pagelines');
		
		$the_text = sprintf('<h2 class="center">"%s"</h2><p class="subhead center">%s</p>', $head, $subhead);
		
		printf( '<section class="billboard">%s <div class="center fix">%s</div></section', apply_filters('pagelines_posts_404', $the_text), get_search_form( false ));
		
	}
	

}
/* ------- END OF CLASS -------- */

