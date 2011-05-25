<?php 
/*
	
	THE LOOP (Posts, Single Post Content, and Page Content)
	
	This file contains the WordPress "loop" which controls the content in your pages & posts. 
	You can control what shows up where using WordPress and PageLines PHP conditionals
	
	This theme copyright (C) 2008-2010 PageLines
	
*/

	global $pagelines_layout; 
	global $post;
	global $wp_query;
	
	$count = 1;  // Used to get the number of the post as we loop through them.
	$clipcount = 2; // The number of clips in a row
	
	$post_count = $wp_query->post_count;  // Used to prevent markup issues when there aren't an even # of posts.
	$paged = intval(get_query_var('paged')); // Control output if on a paginated page

	if(is_admin()) query_posts('showposts=1'); // For parsing in admin, no posts so set it to one.

	$thumb_space = get_option('thumbnail_size_w') + 33; // Space for thumb with padding

// Start of 'The Loop'	
if(have_posts()){
while (have_posts()) : the_post(); 
 
if(!pagelines_show_clip($count, $paged) || is_admin()):

?><article <?php post_class('fpost') ?> id="post-<?php the_ID(); ?>">
		<?php pagelines_register_hook( 'pagelines_loop_post_start', 'theloop' ); // Hook ?>
		
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
		<?php pagelines_register_hook( 'pagelines_loop_post_end', 'theloop' ); // Hook ?>
	</article>

<?php 
endif; // End of Full-Width Post Area 


if(pagelines_show_clip($count, $paged) || is_admin()): // Start Clips 

	if($clipcount % 2 == 0):?>
		<div class="clip_box fix">
		<?php pagelines_register_hook( 'pagelines_loop_clipbox_start', 'theloop' ); // Hook ?>
		<?php $clips_in_row = 1;?>
	<?php endif;?>
		<?php $clip_class = (($clipcount+1) % 2 == 0) ? $clip_class = 'clip clip-right' : $clip_class = 'clip';?>
			<div <?php post_class($clip_class) ?> id="post-<?php the_ID(); ?>">
				<?php pagelines_register_hook( 'pagelines_loop_clip_start', 'theloop' ); // Hook ?>
					<div class="clip-meta fix">
						<?php if(pagelines_show_thumb( get_the_ID(), 'clip' )): // Thumbnails ?>
			            		<div class="clip-thumb">
									<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php _e('Link To', 'pagelines');?> <?php the_title_attribute();?>">
										<?php the_post_thumbnail(array( 40, 40 ));?>
									</a>
					            </div>
						<?php endif; ?>
						<div class="clip-header">
							<?php pagelines_get_post_title('clip');?>
							
							<?php pagelines_get_post_metabar('clip');?>
							
						</div>
					</div>
					<?php if(pagelines_show_excerpt( $post->ID )): // Excerpt ?>
					<div class="post-excerpt">
						<?php 
							the_excerpt(); 
							echo get_continue_reading_link($post->ID);
						 	pagelines_register_hook( 'pagelines_loop_clip_excerpt_end', 'theloop' ); // Hook 
						?>
					</div>
					<?php endif;?>
					<?php pagelines_register_hook( 'pagelines_loop_clip_end', 'theloop' ); // Hook ?>
			</div>	
	<?php if(($clipcount+1) % 2 == 0 || $count == $post_count ):?>
		<?php pagelines_register_hook( 'pagelines_loop_clipbox_end', 'theloop' ); // Hook ?>
		</div>  <!-- closes .clip_box -->
	<?php endif; $clipcount++;
	
endif; // End of Clips
 
$count++;  // Increment the post counter for formatting purposes.

endwhile; // End of 'The Loop'

// or if no posts... 
} else { ?>
	
	<div class="billboard">
		<?php if(is_search()):?>
			<h2 class="center"><?php _e('No results for ', 'pagelines');?>"<?php the_search_query();?>"</h2>
			
			<p class="subhead center"><?php _e('Try another search?', 'pagelines');?></p>
		<?php else:?>
			<h2 class="center"><?php _e('Nothing Found','pagelines');?></h2>
			
			<p class="subhead center"><?php _e('Sorry, what you are looking for isn\'t here.', 'pagelines');?></p>
		<?php endif;?>
		<div class="center fix"><?php get_search_form(); ?> </div>
	</div>
<?php }