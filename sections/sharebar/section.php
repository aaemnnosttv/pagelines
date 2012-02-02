<?php
/*
	Section: ShareBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Adds ways to share content on pages/single posts
	Class Name: PageLinesShareBar
	Workswith: main
	Failswith: pagelines_special_pages() 
	Cloning: true
*/

class PageLinesShareBar extends PageLinesSection {
		
   function section_template() { 
	
	global $post; 
	
	$perm = get_permalink($post->ID);
	$hash = ploption('site-hashtag');
	$twitter_handle = ploption('twittername');
	$title = get_the_title();
	$text = __('Share &rarr;', 'pagelines');
	
	?>
	<div class="pl-sharebar">
		<div class="pl-sharebar-pad media">
			<div class="img">
			<?php 
				printf('<em class="pl-sharebar-text">%s</em>', $text); 
			?>
			
			</div>
			<div class="bd fix">
				<?php 
				
				if(ploption('share_facebook')):
					// Facebook
					?>
					<script>(function(d, s, id) {
  							var js, fjs = d.getElementsByTagName(s)[0];
  							if (d.getElementById(id)) return;
  							js = d.createElement(s); js.id = id;
  							js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
  							fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));
					</script>
					<?php
					printf('<div class="fb-like" data-href="%s" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>', $perm);
				
				endif;
			
				if(ploption('share_google')):
				
					// G+
					printf('<div class="g-plusone" data-size="medium" data-width="80" data-href="%s"></div>', $perm);
			
				?>
				<!-- Place this render call where appropriate -->
				<script type="text/javascript">
				  (function() {
				    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
				    po.src = 'https://apis.google.com/js/plusone.js';
				    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
				  })();
				</script>
			
				<?php endif;
				
				if(ploption('share_twitter')):
					// Twitter
					printf(
						'<a href="https://twitter.com/share" class="twitter-share-button" data-url="%s" data-via="%s" data-hashtags="%s">Tweet</a>', 
						$perm, 
						(ploption('twitter_via')) ? $twitter_handle : '', 
						(ploption('twitter_hash')) ? $hash : ''
					);
				
				?>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			
				<?php 
			
				endif; 
				
				if(ploption('share_buffer')):
					// Buffer
					printf('<a href="http://bufferapp.com/add" class="buffer-add-button" data-text="hello" data-url="%s" data-count="horizontal" data-via="%s">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>', $perm, $title, $twitter_handle);
			
				endif;
				
				if(ploption('share_stumble')):
				?>
			
				<su:badge layout="2" ></su:badge>

				 <script type="text/javascript"> 
				 (function() { 
				     var li = document.createElement('script'); li.type = 'text/javascript'; li.async = true; 
				      li.src = 'https://platform.stumbleupon.com/1/widgets.js'; 
				      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(li, s); 
				 })(); 
				 </script>
				<?php endif;
				
				if(ploption('share_linkedin')): ?>
				<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
				<script width="100" type="IN/Share" data-url="<?php echo $perm;?>" data-width="80" data-counter="right"></script>
				
				<?php endif;?>
		
			</div>
			


		<div class="clear"></div>
		</div>
	</div>
	
<?php	}

}

/*
	End of section class
*/