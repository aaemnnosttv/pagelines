<?php
/*

	Section: ShareBar
	Author: Andrew Powers
	Description: Adds ways to share content on pages/single posts
	Version: 1.0.0
	
*/

class PageLinesShareBar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Content Sharing Tool', 'pagelines');
		$id = 'sharebar';
	
		
		$settings = array(
			'description' 	=> 'Creates a way for users to share your content using their favorite social media or news services.',
			'workswith' 	=> array('main-single', 'main-default'),
			'icon'			=> PL_ADMIN_ICONS . '/feed.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

		
   function section_template() { 
	$sharepre  = '';
	$sharepost = '';
	global $post; ?>
			<div class="post-footer">

					<div class="left">
						<?php e_pagelines('post_footer_social_text', '');?>	
					</div>
					<div class="right">
						<?php 
							$upermalink = urlencode(get_permalink());
							$utitle = urlencode(get_the_title());
							echo apply_filters( 'pagelines_before_sharebar', $sharepre ); // Hook
						?>
							<?php if(pagelines_option('share_reddit')):?>
								<a href="http://reddit.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php echo urlencode( strip_tags(get_the_title($post->ID)) );?>" title="<?php _e('Share on','pagelines');?> Reddit" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-reddit.png" alt="Reddit" /></a>
							<?php endif;?>
							
							<?php if(pagelines_option('share_facebook')):?>
								<a href="http://www.facebook.com/sharer.php?u=<?php echo get_permalink(); ?>&amp;t=<?php echo urlencode( strip_tags(get_the_title($post->ID)) );?>" title="<?php _e('Share on','pagelines');?> Facebook" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-facebook.png" alt="Facebook" /></a>
							<?php endif;?> 

							<?php if(pagelines_option('share_twitter')):?>
								<?php $title = get_the_title($post->ID);?>
								<a href="http://twitter.com/?status=<?php $turl = pagelines_shorturl(get_permalink($post->ID));
 								echo $turl;?>" title="<?php _e('Share on','pagelines');?> Twitter" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-twitter.png" alt="Twitter" /></a>
							<?php endif;?> 

							<?php if(pagelines_option('share_delicious')):?>
								<a href="http://del.icio.us/post?url=<?php the_permalink(); ?>&amp;title=<?php echo urlencode( strip_tags(get_the_title($post->ID)) );?>" title="<?php _e('Share on','pagelines');?> Delicious" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-del.png" alt="Delicious" /></a>
							<?php endif;?>
							
							<?php if(pagelines_option('share_mixx')):?>
								<a href="http://www.mixx.com/submit?page_url=<?php the_permalink(); ?>" title="<?php _e('Share on','pagelines');?> Mixx" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-mixx.png" alt="Mixx" /></a>
							<?php endif;?>
							
							<?php if(pagelines_option('share_stumbleupon')):?>
								<a href="http://www.stumbleupon.com/submit?url=<?php the_permalink() ?>&amp;title=<?php echo urlencode( strip_tags(get_the_title($post->ID)) );?>" title="<?php _e('Share on','pagelines');?> StumbleUpon" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-stumble.png" alt="StumbleUpon" /></a>
							<?php endif;?>
							
							<?php if(pagelines_option('share_digg')):?>
								<a href="http://digg.com/submit?phase=2&amp;url=<?php the_permalink() ?>&amp;title=<?php echo urlencode( strip_tags(get_the_title($post->ID)) );?>" title="<?php _e('Share on','pagelines');?> Digg" rel="nofollow" target="_blank"><img src="<?php echo PL_IMAGES; ?>/ico-digg.png" alt="Digg" /></a>
							<?php endif;
							echo apply_filters( 'pagelines_after_sharebar', $sharepost ); // Hook
							?>
					</div>
				<div class="clear"></div>
			</div>
	
<?php	}

}

/*
	End of section class
*/