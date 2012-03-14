<?php
/*
	Section: PostPins
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A continuous list of post 'pins', inspired by Pinterest. Loaded dynamically and arranged organically.
	Class Name: PostPins	
	Workswith: templates, main
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PostPins extends PageLinesSection {

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		wp_enqueue_script('masonry', $this->base_url.'/script.masonry.js');
		wp_enqueue_script('infinitescroll', $this->base_url.'/script.infinitescroll.js');
	}
	
	function section_head(){?>
		<script>
		
		jQuery(document).ready(function () {
			
			var theContainer = jQuery('.postpin-list');
			var containerWidth = theContainer.width();
			
			theContainer.masonry({
				itemSelector : '.postpin-wrap',
				columnWidth: 237,
				isFitWidth: true
			});
			
			theContainer.infinitescroll({
				navSelector : '.iscroll',
				nextSelector : '.iscroll a',
				itemSelector : '.postpin-list .postpin-wrap',
				loadingText : 'Loading...',
				loadingImg :  '<?php echo $this->base_url."/load.gif";?>',
				donetext : 'No more pages to load.',
				debug : true,
				loading: {
					finishedMsg: 'No more pages to load.'
				}
			}, function(arrayOfNewElems) {
				theContainer.masonry('appended', jQuery(arrayOfNewElems));
				
			});
			
	
		});
		
			
		</script>
	<?php }

	/**
	* Section template.
	*/
   function section_template() { 
		global $wp_query;
		global $post; 
		
	
		$current_url = get_permalink($post->ID);
		
		if(isset($_GET['pins']) && $_GET['pins'] != 1)
			$page = $_GET['pins'];
		else{
			$page = 1;
		}
		
		$out = '';
		
		foreach( $this->load_posts(8, $page) as $key => $p ){
			
			if(has_post_thumbnail($p->ID) && get_the_post_thumbnail($p->ID) != ''){
				$thumb = get_the_post_thumbnail($p->ID); 
				$image = sprintf('<div class="pin-img-wrap"><a class="pin-img" href="%s">%s</a></div>', get_permalink( $p->ID ), $thumb);
			} else 
				$image = '';
				
			$meta_bottom = sprintf(
				'<div class="pin-meta pin-bottom subtext">%s <span class="divider">/</span> %s</div>', 
				get_the_time('M j, Y', $p->ID),
				do_shortcode('[post_comments]')
			);
			
			$meta_top = sprintf(
				'<div class="pin-meta pin-top subtext">%s</div>', 
				get_the_category_list( ', ', '', $p->ID)
			);
			
			$content = sprintf(
				'%s<h4 class="headline pin-title"><a href="%s">%s</a></h4><div class="pin-excerpt summary">%s %s</div>%s', 
				$meta_top,
				get_permalink( $p->ID ), 
				$p->post_title, 
				custom_trim_excerpt($p->post_content, 25), 
				pledit($p->ID),
				$meta_bottom
			);
			
			
			
			$out .= sprintf(
				'<div class="postpin-wrap"><article class="postpin">%s<div class="postpin-pad">%s</div></article></div>', 
				$image,
				$content
			);
		}
		$pg = $page+1;
		$u = $current_url.'?pins='.$pg;
		$next = sprintf('<div class="iscroll"><a href="%s">More Posts</a></div>', $u);
		
		printf('<div class="postpin-list fix">%s</div>%s', $out, $next);
	}

	function load_posts( $number = 20, $page){
		$query = array();
	
		$query['paged'] = $page;
	
		$query['showposts'] = $number; 		
			
		$q = new WP_Query($query);
		
		return $q->posts;
	}

}