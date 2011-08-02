<?php
/*
	Section: PageLines Features
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a feature slider and custom post type
	Class Name: PageLinesFeatures
	Tags: internal
*/

class PageLinesFeatures extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('PageLines Features', 'pagelines');
		$id = 'feature';
		
		$this->tax_id = 'feature-sets';
		$this->section_root_url = $registered_settings['base_url'];
		
		$default_settings = array(
			'description'	=> 'This is your main feature slider.  Add feature text and media through the admin panel.',
			'icon'			=> $this->section_root_url.'/features.png',
			'version'		=> 'pro',	
			'cloning'		=> true
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
	   parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){
		
		/* 
			Create Custom Post Type 
		*/
			$args = array(
					'label' 			=> __('Features', 'pagelines'),  
					'singular_label' 	=> __('Feature', 'pagelines'),
					'description' 		=> 'For setting slides on the feature page template',
					'taxonomies'		=> array('feature-sets'), 
					'menu_icon'			=> $this->icon
				);	
			$taxonomies = array(
				'feature-sets' => array(	
						"label" => __('Feature Sets', 'pagelines'), 
						"singular_label" => __('Feature Set', 'pagelines'), 
					)
			);
			$columns = array(
				"cb" 					=> "<input type=\"checkbox\" />",
				"title" 				=> "Title",
				"feature-description" 	=> "Text",
				"feature-media" 		=> "Media",
				"feature-sets"			=> "Feature Sets"
			);
		
			$column_value_function = 'feature_column_display';
		
			$this->post_type = new PageLinesPostType($this->id, $args, $taxonomies, $columns, $column_value_function);
		
				/* Set default posts if none are present */
				
				$this->post_type->set_default_posts('pagelines_default_features');
		
		/*
			Create meta fields for the post type
		*/
			$type_meta_array = array(
					'feature-style' => array(
							'type' 	=> 'select',					
							'title' => 'Feature Text Position',
							'shortexp' 	=> 'Select the type of feature style you would like to be shown. E.g. show text on left, right, bottom or not at all (full width)...',
							'selectvalues' => array(
								'text-left'		=> array( 'name' => 'Text On Left'),
								'text-right' 	=> array( 'name' => 'Text On Right'),
								'text-bottom' 	=> array( 'name' => 'Text On Bottom'),
								'text-none' 	=> array( 'name' => 'Full Width Image or Media - No Text')
							),
						),
					'feature-background-image' => array(
							'shortexp'		=> 'Upload an image for the feature background.',
							'title' 		=> 'Feature Background Image',
							'type' 			=> 'image_upload'
						),
					
					'feature-design' => array(
							'type'			=> 'select',
							'shortexp' 			=> 'Select the design style you would like this feature to have (e.g. default background color, text color, overlay? etc...).',
							'title' 	=> 'Feature Design Style',
							'selectvalues' => array(
								'fstyle-darkbg-overlay' => array( 'name' => 'White Text - Dark Feature Background - Transparent Text Overlay (Default)'),
								'fstyle-lightbg'		=> array( 'name' => 'Black Text - Light Feature Background with Border - No Overlay'),
								'fstyle-darkbg'			=> array( 'name' => 'White Text - Dark Feature Background - No Overlay'),
								'fstyle-nobg'			=> array( 'name' => 'Black Text - No Feature Background - No Overlay'),
							),
						),
						
					'feature-media-image' => array(
							'version' 		=> 'pro',
							'type' 			=> 'image_upload',					
							'title' 		=> 'Feature Media Image',
							'label'			=> 'Upload An Image For The Feature Media Area',
							'shortexp' 			=> 'Upload an image of the appropriate size for the feature media area.'
						),
					'feature-media' => array(
							'version' 		=> 'pro',
							'type' 			=> 'textarea',					
							'title' 		=> 'Feature Media HTML (Youtube, Flash etc...)',
							'label'			=> 'Enter HTML For Feature Media Area',
							'shortexp'	 		=> 'Feature Page Media HTML or Embed Code.'
						),
					'feature-thumb' => array(
							'shortexp' 			=> 'Add thumbnails to your post for use in thumb navigation. Create an image 50px wide by 30px tall and upload here.',
							'title' 		=> 'Feature Thumb (50px by 30px)',
							'label'			=> 'Upload Feature Thumbnail (Thumbs Mode)',
							'type' 			=> 'image_upload'
						),
					'feature-link-url' => array(
							'shortexp' 			=> 'Adding a URL here will add a link to your feature slide',
							'title' 		=> 'Feature Link URL',
							'label'			=> 'Enter Feature Link URL',
							'type' 			=> 'text'
						),
					'feature-link-text' => array(
							'default'		=> 'More',
							'shortexp' 			=> 'Enter the text you would like in your feature link',
							'title' 		=> 'Link Text',
							'label'			=> 'Enter Feature Link Text',
							'type' 			=> 'text', 
							'size'			=> 'small'
						),
					'feature-name' => array(
							'default'		=> '',
							'shortexp' 			=> 'Enter the title you would like to appear when the feature nav mode is set to feature names',
							'title' 		=> 'Navigation Label',
							'label'			=> 'Enter Feature Navigation Text (Names Nav Mode)',
							'type' 			=> 'text'
						),
		
			);
			
			// Add options for correct post type.
			$post_types = (pagelines('feature_source') == 'posts') ? array($this->id, 'post') : array($this->id);
			
			$type_metapanel_settings = array(
					'id' 		=> 'feature-metapanel',
					'name' 		=> "Feature Setup Options",
					'posttype' 	=> $post_types, 
					'hide_tabs'	=> true
				);
			
			$type_meta_panel =  new PageLinesMetaPanel( $type_metapanel_settings );
			
			
			$type_metatab_settings = array(
				'id' 		=> 'feature-type-metatab',
				'name' 		=> "Feature Setup Options",
				'icon' 		=> $this->icon,
			);
			
			$type_meta_panel->register_tab( $type_metatab_settings, $type_meta_array );
			
			
			

	}

	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$page_metatab_array = array(
					'feature_items' 	=> array(
						'version' 		=> 'pro',
						'type' 			=> 'text_small',
						'inputlabel'	=> 'Number of features to show',
						'title' 		=> 'Number of Feature Slides',
						'shortexp'		=> 'The amount of slides to show on this page',
						'exp' 			=> 'Enter the max number of feature slides to show on this page. Note: If left blank, the number of posts selected under reading settings in the admin will be used.'
					),
					'feature_set' => array(
						'version' 		=> 'pro',
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> "feature-sets",				
						'title' 		=> 'Select Feature Set To Show',
						'shortexp'		=> 'The "set" or category of feature posts',
						'inputlabel'	=> 'Select Feature Set',
						'exp' 			=> 'If you are using the feature section, select the feature set you would like to show on this page.'
					)
				);

			$metatab_settings = array(
					'id' 		=> 'feature_meta',
					'name' 		=> "Feature Meta",
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab($metatab_settings, $page_metatab_array);
	}

	function section_head() {   
		
		global $pagelines_ID;

		// Get the features from post type
		$f = $this->pagelines_features_set(); 	
	
		$feffect = (pagelines_option('feffect')) ? pagelines_option('feffect') : 'fade';
		$timeout = (pagelines_option('timeout')) ? pagelines_option('timeout') : 0;
		$speed   = (pagelines_option('fspeed')) ? pagelines_option('fspeed') : 1500;
		$fsync   = (pagelines_option('fremovesync')) ? 0 : 1;
		$autostop = ( has_filter('pagelines_feature_autostop') ) ? ', autostop: 1, autostopCount: ' . apply_filters( 'pagelines_feature_autostop', 0) : '';
	
		
		$args = sprintf("slideResize: 0, fit: 1,  fx: '%s', sync: %d, timeout: %d, speed: %d, cleartype: true, cleartypeNoBg: true, pager: 'div#featurenav'%s", $feffect, $fsync, $timeout, $speed, $autostop);
		
?><script type="text/javascript">
/* <![CDATA[ */
	var $j = jQuery.noConflict();
	$j(document).ready(function () {
<?php 
	//Feature Cycle Setup
	printf( "\$j('#cycle').cycle({ %s });", $args);
	
	$this->_js_feature_loop($f);

	if(pagelines('feature_playpause')):?>	
		// Play Pause
		$j('.playpause').click(function() { 
			if ($j('.playpause').hasClass('pause')) {
				$j('#cycle').cycle('pause');
			 	$j('.playpause').removeClass('pause').addClass('resume');
			} else {
			   	$j('.playpause').removeClass('resume').addClass('pause');
			    $j('#cycle').cycle('resume', true); 	
			}
		});
	<?php endif;?>
	
});

/* ]]> */
</script>
<?php }

function _js_feature_loop($fposts = array()){
	
	$count = 1;
	$link_js = '';
	$fmode = pagelines_option('feature_nav_type');
	
	
		foreach($fposts as $fid => $f){
	
			$feature_name = (pagelines_option('feature-name', $f->ID)) ? pagelines_option('feature-name', $f->ID): __('feature ', 'pagelines') . $count;
			$feature_thumb = pagelines_option('feature-thumb', $f->ID);
		
			if($fmode == 'names' || $fmode == 'thumbs'){
				if($fmode == 'names')
					$replace_value = $feature_name;
			
				elseif ($fmode == 'thumbs')
					$replace_value = sprintf("<span class='nav_thumb' style='background:#fff url(%s);'><span class='nav_overlay'>&nbsp;</span></span>", $feature_thumb);
		
				$replace_js = sprintf('$j(this).html("%s");', $replace_value );
			} else {
				$replace_js = '';
			}
			
			
			$link_title = sprintf('$j(this).attr("title", "%s");', $feature_name );
			
		
			$link_js .= sprintf('if($j(this).html() == "%s") { %s %s }', $count, $link_title, $replace_js);
		
			$count++; 
		}
	
		printf('$j("div#featurenav").children("a").each(function() { %s });', $link_js);

}

function section_template() {    

	$f = $this->pagelines_features_set(); 


	// $this->set set in pagelines_feature_set, better way to do this?
	$this->draw_features($f, $this->set);

}

function pagelines_features_set(){
	
	global $post; 
	global $pagelines_ID;
	
	if( get_pagelines_meta('feature_set', $pagelines_ID) )
		$this->set = get_post_meta($pagelines_ID, 'feature_set', true);
	elseif (pagelines_option('feature_default_tax'))
		$this->set = pagelines_option('feature_default_tax');
	else 
		$this->set = null;
	
	$limit = (pagelines_option('feature_items', $pagelines_ID)) ? pagelines_option('feature_items', $pagelines_ID) : null;
		
	$source = (pagelines_option('feature_source') == 'posts') ? 'posts' : 'customtype';	
		
	$f = $this->load_pagelines_features($this->set, $limit, $source); 
	
	return $f;	
		
}

function load_pagelines_features( $set = null, $limit = null, $source = null){
	$query = array();
	
	$query['orderby'] 	= 'ID'; 
	
	if($source == 'posts'){
		
		$query['post_type'] = 'post';
		
		if(pagelines_option('feature_category'))
			$query['cat'] = pagelines_option('feature_category');

	} else {
		
		$query['post_type'] = 'feature'; 
		
		if(isset($set)) 
			$query['feature-sets'] = $set;
		
	}
	
	if(isset($limit)) 
		$query['showposts'] = $limit; 

	$q = new WP_Query($query);
	
	if(is_array($q->posts)) 
		return $q->posts;
	else 
		return array();
}

function draw_features($f, $class) {     
	
	global $post; 
	global $pagelines_layout; 
	$current_page_post = $post;
	   
?>		
	<div id="feature_slider" class="<?php echo $class;?> fix">
		<div id="feature-area">
			<div id="cycle">
			<?php
				
				if(!empty($f)):
					foreach($f as $post) : 
						
						// Setup For Std WP functions
						setup_postdata($post); 
						
						// Get Feature Style
						$feature_style = (get_post_meta($post->ID, 'feature-style', true)) ? get_post_meta($post->ID, 'feature-style', true) : 'text-left';
						
						$flink_text = ( get_post_meta($post->ID, 'feature-link-text', true) ) ? get_post_meta($post->ID, 'feature-link-text', true) : __('More', 'pagelines');
					
						//Get the Thumbnail URL
						$feature_background_image = get_post_meta($post->ID, 'feature-background-image', true);
						$feature_design = (get_post_meta($post->ID, 'feature-design', true)) ? get_post_meta($post->ID, 'feature-design', true) : '';
				
						?>
						<div id="<?php echo 'feature_'.$post->ID;?>"  class="fcontainer <?php echo $feature_style.' '.$feature_design; ?> fix" >
							<div class="feature-wrap wcontent" <?php if($feature_background_image):?>style="background: url('<?php echo $feature_background_image;?>') no-repeat top center" <?php endif;?>>
								<div class="feature-pad fix">
									<?php pagelines_register_hook( 'pagelines_feature_before', $this->id ); // Hook ?>
									<div class="fcontent <?php if(get_post_meta($post->ID, 'fcontent-bg', true)) echo get_post_meta($post->ID, 'fcontent-bg', true);?>">
										<div class="dcol-pad fix">
												<?php pagelines_register_hook( 'pagelines_fcontent_before', $this->id ); // Hook ?>
												<div class="fheading">
													<h2 class="ftitle"><?php the_title(); ?></h2>
												</div>
												<div class="ftext">
													<?php pagelines_register_hook( 'pagelines_feature_text_top', $this->id ); // Hook ?>
													<div class="fexcerpt">
													<?php 
														if(pagelines_option('feature_source') == 'posts') 
															echo apply_filters( 'pagelines_feature_output', get_the_excerpt() );
													 	else 
															the_content(); 
													?>
													</div>
													<?php 
														$action = get_post_meta($post->ID, 'feature-link-url', true);
														if($action)
															echo blink($flink_text, 'link', 'black', array('action' => $action));
													
													
													
													pagelines_register_hook( 'pagelines_feature_text_bottom', $this->id ); // Hook 
													echo blink_edit($post->ID, 'black');
													
														?>
												</div>
												<?php pagelines_register_hook( 'pagelines_fcontent_after', $this->id ); // Hook ?>
										</div>
										
									</div>
						
									<div class="fmedia" style="">
										<div class="dcol-pad">
											<?php pagelines_register_hook( 'pagelines_feature_media_top', $this->id ); // Hook ?>
											<?php if(get_post_meta($post->ID, 'feature-media-image', true)):?>
												<div class="media-frame">
													<img src="<?php echo get_post_meta( $post->ID, 'feature-media-image', true);?>" />
												</div>
											<?php elseif(get_post_meta( $post->ID, 'feature-media', true)): ?>
												<?php echo do_shortcode(get_post_meta( $post->ID, 'feature-media', true)); ?>
											<?php endif;?>
											
										</div>
									</div>
									<?php pagelines_register_hook( 'pagelines_feature_after', $this->id ); // Hook ?>
									<div class="clear"></div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<h4 style="padding: 50px; text-align: center"><?php _e('No feature posts matched this pages criteria', 'pagelines');?></h4>
				<?php endif;?>
				<?php $post = $current_page_post;?>
		
			</div>
		</div>
		
		<div id="feature-footer" class="<?php e_pagelines('feature_nav_type', '');?> <?php  if( isset($this->the_feature_posts) && count($this->the_feature_posts) == 1) echo 'nonav';?> fix">
			<div class="feature-footer-pad">
				<?php pagelines_register_hook( 'pagelines_feature_nav_before', $this->id ); // Hook ?>
				<?php if(pagelines('timeout') != 0 && pagelines('feature_playpause')):?><span class="playpause pause"><span>&nbsp;</span></span><?php endif;?>
				<div id="featurenav" class="fix">
					
				</div>
				<div class="clear"></div>
			</div>
		</div>
		
	</div>
	<div class="clear"></div>
<?php }



	function section_scripts() {  
		
		return array(
				'cycle' => array(
						'file' => $this->base_url . '/jquery.cycle.js',
						'dependancy' => array('jquery'), 
						'location' => 'footer', 
						'version' => '2.99'
					)	
			);
		
	}

	function section_options($optionset = null, $location = null) {
		
		if($optionset == 'new' && $location == 'bottom'){
			return array(
				'feature_settings' => array(
							'feature_nav_type' => array(
								'default' => "thumbs",
								'version'	=> 'pro',
								'type' => 'radio',
								'selectvalues' => array(
									'nonav' 		=> array( 'name' => 'No Navigation' ),
									'dots' 			=> array( 'name' => 'Squares or Dots' ),
									'names' 		=> array( 'name' => 'Feature Names' ),
									'thumbs' 		=> array( 'name' => 'Feature Thumbs (50px by 30px)' ),								
									'numbers'		=> array( 'name' => 'Numbers' ),
								),
								'inputlabel' => 'Feature navigation type?',
								'title' => 'Feature Navigation Mode',
								'shortexp' => "Select the mode for your feature navigation",
								'exp' => "Select from the three modes. Using feature names will use the names of the features, using the numbers will use incremental numbers, thumbnails will use feature thumbnails if provided.", 
								'docslink'		=> 'http://www.pagelines.com/docs/feature-slider', 
								'vidtitle'		=> 'View Feature Documentation'
							),
							'timeout' => array(
									'default' => 0,
									'version'	=> 'pro',
									'type' => 'text_small',
									'inputlabel' => 'Timeout (ms)',
									'title' => 'Feature Viewing Time (Timeout)',
									'shortexp' => 'The amount of time a feature is set before it transitions in milliseconds',
									'exp' => 'Set this to 0 to only transition on manual navigation. Use milliseconds, for example 10000 equals 10 seconds of timeout.'
								),
							'fspeed' => array(
									'default' => 1500,
									'version'	=> 'pro',
									'type' => 'text_small',
									'inputlabel' => 'Transition Speed (ms)',
									'title' => 'Feature Transition Time (Timeout)',
									'shortexp' => 'The time it takes for your features to transition in milliseconds',
									'exp' => 'Use milliseconds, for example 1500 equals 1.5 seconds of transition time.'
								),
							'feffect' => array(
									'default' => 'fade',
									'version'	=> 'pro',
									'type' => 'select_same',
									'selectvalues' => array('blindX','blindY','blindZ', 'cover','curtainX','curtainY','fade','fadeZoom','growX','growY','none','scrollUp','scrollDown','scrollLeft','scrollRight','scrollHorz','scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom'),
									'inputlabel' => 'Select Transition Effect',
									'title' => 'Transition Effect',
									'shortexp' => "How the features transition",
									'exp' => "This controls the mode with which the features transition to one another."
								),
							'feature_playpause' => array(
									'default' => false,
									'version'	=> 'pro',
									'type' => 'check',
									'inputlabel' => 'Show play pause button?',
									'title' => 'Show Play/Pause Button (when timeout is greater than 0 (auto-transition))',
									'shortexp' => "Show a play/pause button for auto-scrolling features",
									'exp' => "Selecting this option will add a play/pause button for auto-scrolling features, that users can use to pause and watch a video, read a feature, etc.."
								),
							'feature_items' => array(
									'default' => 10,
									'version'	=> 'pro',
									'type' => 'text_small',
									'inputlabel' => 'Number of Features To Show',
									'title' => 'Number of Features',
									'shortexp' => "Limit the number of features that are shown",
									'exp' => "Use this option to limit the number of features shown."
								),
							'feature_source' => array(
									'default' => 'featureposts',
									'version'	=> 'pro',
									'type' => 'select',
									'selectvalues' => array(
										'featureposts' 	=> array("name" => 'Feature Posts (custom post type)'),
										'posts' 		=> array("name" => 'Use Post Category'),
									),
									'inputlabel' => 'Select source',
									'title' => 'Feature Post Source',
									'shortexp' => "Use feature posts or a post category",
									'exp' => "By default the feature section will use feature posts, you can also set the source for features to a blog post category. Set the category ID in its option below. <br/> <strong>NOTE: If set to posts, excerpts will be used as content (control length through them). Also a new option panel will be added on post creation and editing pages.</strong>"
								),
							'feature_category' => array(
									'default' => 1,
									'version'	=> 'pro',
									'type' => 'select',
									'selectvalues' => $this->get_cats(),
									'title' => 'Post Category (Blog Post Mode Only)',
									'shortexp' => "",
									'exp' => "Select a category to use if sourcing features from blog posts"
								),
							'feature_default_tax' => array(
									'default' 		=> 'default-features',
									'version'		=> 'pro',
									'taxonomy_id'	=> 'feature-sets',
									'type' 			=> 'select_taxonomy',
									'inputlabel' 	=> 'Select Posts/404 Feature-Set',
									'title' 		=> 'Select Feature-Set for Posts & 404 Pages',
									'shortexp' 		=> "Posts pages and similar pages (404) Will Use This set ID To Source Features",
									'exp' 			=> "Posts pages and 404 pages in WordPress don't support meta data so you need to assign a set here. (If you want to use 'features' on these pages.)",
								), 
							'feature_stage_height' => array(
									'default' 		=> '330',
									'version'		=> 'pro',
									'type' 			=> 'css_option',
									'selectors'		=> '#feature-area, .feature-wrap, #feature_slider .fmedia, #feature_slider .fcontent, #feature_slider .text-bottom .fmedia .dcol-pad, #feature_slider .text-bottom .feature-pad, #feature_slider .text-none .fmedia .dcol-pad', 
									'css_prop'		=> 'height', 
									'css_units'		=> 'px',
									'inputlabel' 	=> 'Enter the height (In Pixels) of the Feature Stage Area',
									'title' 		=> 'Feature Area Height',
									'shortexp' 		=> "Use this feature to change the height of your feature area",
									'exp' 			=> "To change the height of your feature area, just enter a number in pixels here.",
								),
							'fremovesync' => array(
									'default' => false,
									'type' => 'check',
									'version'	=> 'pro',
									'inputlabel' => 'Remove Transition Syncing',
									'title' => 'Remove Feature Transition Syncing',
									'shortexp' => "Make features wait to move on until after the previous one has cleared the screen",
									'exp' => "This controls whether features can move on to the screen while another is transitioning off. If removed features will have to leave the screen before the next can transition on to it."
								)

				)
			);
		}
	} 


function get_cats() {
	
	$cats = get_categories();
	foreach( $cats as $cat ) {
		$categories[ $cat->cat_ID ] = array(
			'name' => $cat->name
		);
	}
	return ( isset( $categories) ) ? $categories : '';
}

// End of Section Class //
}

function feature_column_display($column){
	global $post;
	
	switch ($column){
		case "feature-description":
			the_excerpt();
			break;
		case "feature-media":
		 	if(m_pagelines('feature-media', $post->ID)){
				em_pagelines('feature-media', $post->ID);
			}elseif(m_pagelines('feature-media-image', $post->ID)){
				echo '<img src="'.m_pagelines('feature-media', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
			}elseif(m_pagelines('feature-background-image', $post->ID)){
				echo '<img src="'.m_pagelines('feature-background-image', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
			}
			break;
		case "feature-sets":
			echo get_the_term_list($post->ID, 'feature-sets', '', ', ','');
			break;
	}
}

		
function pagelines_default_features(){
	
	$default_features = array_reverse(get_default_features());

	
	foreach($default_features as $feature){
		// Create post object
		$default_post = array();
		$default_post['post_title'] = $feature['title'];
		$default_post['post_content'] = $feature['text'];
		$default_post['post_type'] = 'feature';
		$default_post['post_status'] = 'publish';
		
		$newPostID = wp_insert_post( $default_post );
	
		update_post_meta($newPostID, 'feature-thumb', $feature['thumb']);
		update_post_meta($newPostID, 'feature-link-url', $feature['link']);
		update_post_meta($newPostID, 'feature-style', $feature['style']);
		update_post_meta($newPostID, 'feature-media', $feature['media']);
		update_post_meta($newPostID, 'feature-background-image', $feature['background']);
		update_post_meta($newPostID, 'feature-design', $feature['fcontent-design']);
		wp_set_object_terms($newPostID, 'default-features', 'feature-sets');
	}
}

function get_default_features(){
	$default_features = array(
			'1' => array(
		        	'title' 			=> 'Welcome to PlatformPro',
		        	'text' 				=> 'Welcome to PlatformPro Framework, we hope you are enjoying this premium product from PageLines.',
		        	'media' 			=> '',
					'style'				=> 'text-none',
		        	'link' 				=> '#fake_link',
					'background' 		=> PL_IMAGES.'/feature1.jpg',
					'name'				=>'PlatformPro',
					'fcontent-design'	=> '',
					'thumb'				=> PL_IMAGES.'/fthumb1.png'
		    ),
			'2' => array(
		        	'title' 		=> 'YouTube Video',
		        	'text' 			=> 'A video on changing things.',
		        	'media'		 	=> '<object width="960" height="330"><param name="movie" value="http://www.youtube.com/v/T6MhAwQ64c0&amp;hl=en_US&amp;fs=1?hd=1&amp;showinfo=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/T6MhAwQ64c0&amp;hl=en_US&amp;fs=1?hd=1&amp;showinfo=0" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="960" height="330"></embed></object>',
		        	'style'			=> 'text-none',
					'link' 			=> '#fake_link',
					'background' 	=> '',
					'name'			=>	'Media',
					'fcontent-design'	=> '',
					'thumb'				=> PL_IMAGES.'/fthumb2.png'
		    ),
			'3' => array(
				 	'title' 		=> '<small>WordPress Framework By</small> PageLines',
		        	'text' 			=> 'Welcome to a professional WordPress framework by PageLines. Designed for you in San Diego, California.',
		        	'media' 		=> '',
		        	'style'			=> 'text-right',
					'link' 			=> '#fake_link',
					'background' 	=> PL_IMAGES.'/feature2.jpg',
					'name'			=>	'Design',
					'fcontent-design'	=> '',
					'thumb'				=> PL_IMAGES.'/fthumb3.png'
		    ),
			'4' => array(
				 	'title' 		=> '<small>Web Design</small> Redesigned.',
		        	'text' 			=> 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
		        	'media' 		=> '',
		        	'style'			=> 'text-left',
					'link' 			=> '#fake_link',
					'background' 	=> PL_IMAGES.'/feature3.jpg',
					'name'			=> 'Pro',
					'fcontent-design'	=> '',
					'thumb'				=> PL_IMAGES.'/fthumb4.png'
		    ), 
			'5' => array(
		        	'title' 		=> '<small>Make An</small> Impression',
		        	'text' 			=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam quam quam, dignissim eu dignissim et,<br/> accumsan ullamcorper risus. Aliquam rutrum, lorem et ornare malesuada, mi magna placerat mi, bibendum volutpat lectus. Morbi nec purus dolor.',
		        	'media'		 	=> '',
		        	'style'			=> 'text-bottom',
					'link' 			=> '#fake_link',
					'background' 	=> PL_IMAGES.'/feature4.jpg',
					'name'			=>'Media',
					'fcontent-design'	=> '',
					'thumb'				=> PL_IMAGES.'/fthumb5.png'
		    ),
	);
	
	return apply_filters('pagelines_default_features', $default_features);
}
