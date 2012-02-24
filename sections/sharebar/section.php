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

/**
 * ShareBar Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesShareBar extends PageLinesSection {

	/**
	* Section template.
	*/		
   function section_template() { 
	
	global $post; 
	
	$perm = get_permalink($post->ID);
	$title = get_the_title($post->ID);
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
				
				if(ploption('share_facebook'))
					echo self::facebook(array('permalink' => $perm));
			
				if(ploption('share_google'))
					echo self::google(array('permalink' => $perm));
				
				
				if(ploption('share_twitter'))
					echo self::twitter(array('permalink' => $perm, 'title' => $title));
				
				if(ploption('share_buffer')):
					// Buffer
					printf('<a href="http://bufferapp.com/add" class="buffer-add-button" data-text="hello" data-url="%s" data-count="horizontal" data-via="%s">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script>', $perm, $title, ploption('twittername'));
			
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

	

	/**
	*
	* @TODO document
	*
	*/
	function twitter( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
			'hash'		=> ploption('site-hashtag'), 
			'handle'	=> ploption('twittername'), 
			'title'		=> '',
		); 	
		
		$a = wp_parse_args($args, $defaults);
		
		ob_start();
		
			// Twitter
			printf(
				'<a href="https://twitter.com/share" class="twitter-share-button" data-url="%s" data-text="%s" data-via="%s" data-hashtags="%s">Tweet</a>', 
				$a['permalink'], 
				$a['title'],
				(ploption('twitter_via')) ? $a['handle'] : '', 
				(ploption('twitter_hash')) ? $a['hash'] : ''
			);
		
		?>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		
		<?php 
		
		return ob_get_clean();
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function google( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
		); 
		
		$a = wp_parse_args($args, $defaults);
		
		ob_start();
		
			// G+
			printf('<div class="g-plusone" data-size="medium" data-width="%s" data-href="%s"></div>', $a['width'], $a['permalink']);
	
		?>
		<!-- Place this render call where appropriate -->
		<script type="text/javascript">
		  (function() {
		    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		    po.src = 'https://apis.google.com/js/plusone.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
		  })();
		</script>
		
		<?php 
		
		return ob_get_clean();
		
	}


	/**
	*
	* @TODO document
	*
	*/
	function facebook( $args ){
		
		$defaults = array(
			'permalink'	=> '', 
			'width'		=> '80',
		); 
		
		$a = wp_parse_args($args, $defaults);
		
		
		ob_start();
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
			printf(
				'<div class="fb-like" data-href="%s" data-send="false" data-layout="button_count" data-width="%s" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>', 
				$a['permalink'], 
				$a['width']);
				
		return ob_get_clean();
		
	}

}