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
	
	function section_head(){
		
		$width = (ploption('pins_width', $this->oset)) ? ploption('pins_width', $this->oset) : 237;
		?>
		<style>.postpin-wrap{width: <?php echo $width;?>px; }</style>
		<script>
		
		jQuery(document).ready(function () {
			
			var theContainer = jQuery('.postpin-list');
			var containerWidth = theContainer.width();
			
			theContainer.masonry({
				itemSelector : '.postpin-wrap',
				columnWidth: <?php echo $width;?>,
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
		
			jQuery('.fetchpins a').live('click', function(e) {
				e.preventDefault();
				jQuery(this).addClass('loading').text('<?php _e('Loading...', 'pagelines');?>');
				jQuery.ajax({
					type: "GET",
					url: jQuery(this).attr('href') + '#pinboard',
					dataType: "html",
					success: function(out) {
						result = jQuery(out).find('.pinboard .postpin-wrap');
						nextlink = jQuery(out).find('.fetchpins a').attr('href');
						
						jQuery('.postpin-list').append(result).masonry('appended', result);
						
						jQuery('.fetchpins a').removeClass('loading').text('<?php _e('Load More Posts', 'pagelines');?>');
						
						if (nextlink != undefined) {
							jQuery('.fetchpins a').attr('href', nextlink);
						} else {
							jQuery('.fetchpins').remove();
						}
					}
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
		
		$number_of_pins = (ploption('pins_number', $this->oset)) ? ploption('pins_number', $this->oset) : 15;
	
		$current_url = get_permalink($post->ID);
		
		$page = (isset($_GET['pins']) && $_GET['pins'] != 1) ? $_GET['pins'] : 1;
		
		$out = '';
		
		foreach( $this->load_posts($number_of_pins, $page) as $key => $p ){
			
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
		
		$next_posts = $this->load_posts($number_of_pins, $pg);
		if( !empty($next_posts) ){
			
			$class = ( ploption('pins_loading', $this->oset) == 'infinite' ) ? 'iscroll' : 'fetchpins';
				
			$next_url = sprintf('<div class="%s"><a href="%s">%s</a></div>', $class, $u, __('Load More Posts', 'pagelines'));
		
		} else
			$next_url = '';
			
		printf('<div class="pinboard"><div class="postpin-list fix">%s</div>%s</div>', $out, $next_url);
	}

	function load_posts( $number = 20, $page){
		$query = array();
	
		$query['paged'] = $page;
	
		$query['showposts'] = $number; 		
			
		$q = new WP_Query($query);
		
		return $q->posts;
	}

	/**
	 *
	 * Page-by-page options for PostPins
	 *
	 */
	function section_optionator( $settings ){
		$settings = wp_parse_args( $settings, $this->optionator_default );
		
			$page_metatab_array = array(
					'pins_width' => array(
							'version'		=> 'pro',
							'type' 			=> 'text_small',
							'inputlabel' 	=> __( 'Pin Width in Pixels', 'pagelines' ),
							'title' 		=> __( 'Pin Width', 'pagelines' ),
							'shortexp' 		=> __( 'The width of post pins in pixels. Default is <strong>237px</strong>.', 'pagelines' )
					),
					'pins_number' => array(
						'version'		=> 'pro',
						'type' 			=> 'text_small',
						'inputlabel' 	=> __( 'Number of Pins To Load', 'pagelines' ),
						'title' 		=> __( 'Number of Pins to Load', 'pagelines' ),
						'shortexp' 		=> __( 'Use this feature to change the height of your feature area', 'pagelines' ),
						'exp' 			=> __( "To change the height of your feature area, just enter a number in pixels here.", 'pagelines' ),
					), 
					'pins_loading' => array(
						'version'		=> 'pro',
						'type' 			=> 'select',
						'selectvalues' => array(
							'infinite' 		=> array('name' => __( 'Use Infinite Scrolling', 'pagelines' ) ),
							'ajax' 			=> array('name' => __( 'Use Load Posts Link (AJAX)', 'pagelines' ) ),						
						),
						'inputlabel' 	=> __( 'Pin Loading Method', 'pagelines' ),
						'title' 		=> __( 'Post Pin Loading', 'pagelines' ),
						'shortexp' 		=> __( 'Select the mode for loading new pins on the page', 'pagelines' ),
						'exp' 			=> __( "Use infinite scroll loading to automatically load new pins when users get to the bottom of the page. Alternatively, you can use a link that users can click to 'load new pins' into the page.", 'pagelines' ),
					),
					'pins_category' => array(
						'default' 		=> '380',
						'version'		=> 'pro',
						'taxonomy_id'	=> 'category',
						'type' 			=> 'select_taxonomy',
						'inputlabel' 	=> __( 'Pin Post Category', 'pagelines' ),
						'title' 		=> __( 'Pins Category/Posts Mode', 'pagelines' ),
						'shortexp' 		=> __( 'Select a post category to use with post pins, leave default for all posts.', 'pagelines' ),
						'exp' 			=> __( "You can select to use only posts from a specific category, leave blank to use all posts.", 'pagelines' ),
					)
				);

			$metatab_settings = array(
					'id' 		=> 'postpins_options',
					'name' 		=> __( 'PostPins', 'pagelines' ),
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab( $metatab_settings, $page_metatab_array );

	}

}