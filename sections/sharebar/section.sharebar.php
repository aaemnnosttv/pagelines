<?php
/*
	Section: ShareBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds ways to share content on pages/single posts
	Class Name: PageLinesShareBar
	Tags: internal
*/

class PageLinesShareBar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Share Bar', 'pagelines');
		$id = 'sharebar';
	
		
		$default_settings = array(
			'description' 	=> 'Creates a way for users to share your content using their favorite social media or news services.',
			'workswith' 	=> array('main'),
			'failswith'		=> pagelines_special_pages(),
			'icon'			=> PL_ADMIN_ICONS . '/feed.png'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		parent::__construct($name, $id, $settings);    
		
   }

		
   function section_template() { 
	$sharepre  = '';
	$sharepost = '';
	global $post; ?>
			<div class="post-footer">
				<div class="left"><?php echo ploption('post_footer_social_text');?></div>
					<div class="right">
						<?php 
							$upermalink = urlencode( get_permalink( $post->ID ) );
							$utitle = urlencode( strip_tags( get_the_title() ) );
							
							$string = '<a class="sharelink" href="%s" title="%s" rel="nofollow" target="_blank"><img src="%s" alt="%s" /></a>';
							
							echo apply_filters( 'pagelines_before_sharebar', $sharepre ); // Hook
							
						if(ploption('share_reddit')){
							$url = sprintf('http://reddit.com/submit?phase=2&amp;url=%s&amp;title=%s', $upermalink, $utitle);
							printf($string, $url, __('Share on Reddit', 'pagelines'), $this->base_url.'/reddit.png', 'Reddit');
						}
						
						if(ploption('share_facebook')){
							$url = sprintf('http://www.facebook.com/sharer.php?u=%s&amp;t=%s', $upermalink, $utitle);
							printf($string, $url, __('Share on Facebook', 'pagelines'), $this->base_url.'/facebook.png', 'Facebook');
						}
						
						if(ploption('share_twitter')){
							$url = sprintf('http://twitter.com/?status=%s', pagelines_shorturl($upermalink));
							printf($string, $url, __('Share on Twitter', 'pagelines'), $this->base_url.'/twitter.png', 'Twitter');
						}
						
						if(ploption('share_delicious')){
							$url = sprintf('http://del.icio.us/post?url=%s&amp;title=%s', $upermalink, $utitle);
							printf($string, $url, __('Share on Delicious', 'pagelines'), $this->base_url.'/delicious.png', 'Delicious');
						}
						
						if(ploption('share_stumbleupon')){
							$url = sprintf('http://www.stumbleupon.com/submit?url=%s&amp;title=%s', $upermalink, $utitle);
							printf($string, $url, __('Share on StumbleUpon', 'pagelines'), $this->base_url.'/stumble.png', 'StumbleUpon');
						}
						
						if(ploption('share_digg')){
							$url = sprintf('http://digg.com/submit?phase=2&amp;url=%s&amp;title=%s', $upermalink, $utitle);
							printf($string, $url, __('Share on Digg', 'pagelines'), $this->base_url.'/digg.png', 'Digg');
						}
						
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