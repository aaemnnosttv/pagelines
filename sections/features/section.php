<?php
/*
	Section: Features
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Creates a feature slider and custom post type
	Class Name: PageLinesFeatures
	Workswith: templates, main, header, morefoot	
	Cloning: true
	Edition: pro
	Tax: feature-sets
*/

class PageLinesFeatures extends PageLinesSection {

	var $taxID = 'feature-sets';
	var $ptID = 'feature';

	function section_persistent(){
		
		$this->post_type_setup();
		
		$this->post_meta_setup();	 

	}

	function section_head( $clone_id ) {   
		
		global $pagelines_ID;

		$oset = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);

		$f = $this->post_set[ $clone_id ] = $this->pagelines_features_set( $clone_id ); 	
	
		$feffect = (ploption('feffect', $oset)) ? ploption('feffect', $oset) : 'fade';
		$timeout = (ploption('timeout', $oset)) ? ploption('timeout', $oset) : 0;
		$speed   = (ploption('fspeed', $oset)) ? ploption('fspeed', $oset) : 1500;
		$fsync   = (ploption('fremovesync', $oset)) ? 0 : 1;
		$autostop = ( has_filter('pagelines_feature_autostop') ) ? ', autostop: 1, autostopCount: ' . apply_filters( 'pagelines_feature_autostop', 0) : '';
		$playpause = (ploption('feature_playpause', $oset)) ? true : false;
		$fmode = ploption('feature_nav_type', $oset);
		
		$clone_class = sprintf('fclone%s', $clone_id);
		
		$selector = sprintf('#cycle.%s', $clone_class);
		$fnav_selector = sprintf('#featurenav.%s', 'fclone'.$clone_id);
		$playpause_selector = sprintf('.playpause.%s', 'fclone'.$clone_id);
		
		$args = sprintf("slideResize: 0, fit: 1,  fx: '%s', sync: %d, timeout: %d, speed: %d, cleartype: true, cleartypeNoBg: true, pager: '%s' %s", $feffect, $fsync, $timeout, $speed, $fnav_selector, $autostop);
		
		$this->_feature_css($clone_id, $oset);
		
?><script type="text/javascript">/* <![CDATA[ */ jQuery(document).ready(function () {
<?php 
	//Feature Cycle Setup
	printf( "jQuery('%s').cycle({ %s });", $selector, $args);
	
	$this->_js_feature_loop($fmode, $f, $clone_class);

	if($playpause):
	?>	
	
		var cSel = '<?php echo $selector;?>';
		var ppSel = '<?php echo $playpause_selector;?>';
		
		jQuery(ppSel).click(function() { 
			if (jQuery(ppSel).hasClass('pause')) {  
				jQuery(cSel).cycle('pause'); jQuery(ppSel).removeClass('pause').addClass('resume');
			} else { 
				jQuery(ppSel).removeClass('resume').addClass('pause'); jQuery(cSel).cycle('resume', true);
			}
		});
	<?php endif;?>
});

/* ]]> */ </script>
<?php }

	function _feature_css( $clone_id, $oset){

		$height = (ploption('feature_stage_height', $oset)) ? ploption('feature_stage_height', $oset).'px' : '380px';	
		$feature_height_selectors = array('#feature-area', '.feature-wrap', '#feature_slider .fmedia, #feature_slider .fcontent', '#feature_slider .text-bottom .fmedia .dcol-pad', '#feature_slider .text-bottom .feature-pad', '#feature_slider .text-none .fmedia .dcol-pad');
		$selectors = array();
		foreach($feature_height_selectors as $sel){
			if( isset($clone_id) && $clone_id != 1)
				$selectors[] = sprintf('.clone_%s %s', $clone_id, $sel);
			else 
				$selectors[] = $sel;
		}
	
		$css = sprintf( '%s{height:%s;}', join(',', $selectors), $height);	
		inline_css_markup('feature-css', $css);
	}

	function _js_feature_loop($fmode, $fposts = array(), $clone_class){
	
		$count = 1;
		$link_js = '';
		$cat_css = '';
		foreach($fposts as $fid => $f){
			$oset = array('post_id' => $f->ID);
			$feature_name = (ploption('feature-name', $oset)) ? ploption('feature-name', $oset) : $f->post_title;
			$feature_thumb = ploption('feature-thumb', $oset);
			
			if ( ( ploption('feature_source', $this->oset) == 'posts' || ploption('feature_source', $this->oset) == 'posts_all' ) ) {
				
				if( has_post_thumbnail( $f->ID )) {
					$feature_thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $f->ID ) );
					$feature_thumb = $feature_thumb[0];
				} else {					
					$feature_thumb = apply_filters( 'pagelines-feature-cat-default-thumb', $this->base_url . '/images/fthumb3.png', $f );
				}
			} 
			
			if($fmode == 'names' || $fmode == 'thumbs'){
				echo "\n".' // '.$fmode.'!!!'."\n";
				if($fmode == 'names')
					$replace_value = $feature_name;
			
				elseif ($fmode == 'thumbs')
					$replace_value = sprintf("<span class='nav_thumb' style='background-image: url(%s);'><span class='nav_overlay'>&nbsp;</span></span>", $feature_thumb);
		
				$replace_js = sprintf('jQuery(this).html("%s");', $replace_value );
			} else
				$replace_js = '';
			
			$link_title = sprintf('jQuery(this).attr("title", "%s");', $feature_name );
		
			$link_js .= sprintf('if(jQuery(this).html() == "%s") { %s %s }', $count, $link_title, $replace_js);
		
			$count++; 
		}	
		printf('jQuery("div#featurenav.%s").children("a").each(function() { %s });', $clone_class, $link_js);
	}

	function section_template( $clone_id ) {    

		// $this->set set in pagelines_feature_set, better way to do this?
		$this->draw_features($this->post_set[ $clone_id ], $this->set, $clone_id);
	}

	function pagelines_features_set( $clone_id ){
	
		if( ploption('feature_set', $this->oset) )
			$this->set = ploption('feature_set', $this->oset);
		elseif (ploption('feature_default_tax', $this->oset))
			$this->set = ploption('feature_default_tax', $this->oset);
		else 
			$this->set = null;

		$limit = ploption('feature_items', $this->oset);
		
		$order = ploption('feature_order', $this->oset);
		
		$orderby = ploption('feature_orderby', $this->oset);
		
		$source = ( ploption('feature_source', $this->oset) == 'posts' || ploption('feature_source', $this->oset) == 'posts_all') ? ploption('feature_source', $this->oset) : 'customtype';	
	
		$category = ( $source == 'posts' ) ? ploption('feature_category', $this->oset) : '';	
		
		
		
		$f = $this->load_pagelines_features( array( 'set' => $this->set, 'limit' => $limit, 'orderby' => $orderby, 'order' => $order, 'source' => $source, 'category' => $category ) ); 
		
		
		return $f;		
	}
	
	function load_pagelines_features( $args ) {
		$defaults = array(
			
			'query'		=>	array(),
			'set'		=>	null,
			'limit'		=>	null,
			'order'		=>	'DESC',
			'orderby'	=>	'ID',
			'source'	=>	null,
			'category'	=>	null
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args, EXTR_SKIP );

		$query['orderby']	= $orderby;

		$query['order']		= $order;
	
		if($source == 'posts' || $source == 'posts_all' ){
		
			$query['post_type'] = 'post';
		
			if( $category )
				$query['cat'] = $category;
		} else {
		
			$query['post_type'] = $this->ptID; 
		
			if( isset( $set ) ) 
				$query[ $this->taxID ] = $set;	
		}
	
		if( isset( $limit ) ) 
			$query['showposts'] = $limit; 

		$q = new WP_Query($query);
		
		if( is_array( $q->posts ) ) 
			return $q->posts;
		else 
			return array();
	}

	function draw_features($f, $class, $clone_id = null) {     
	
	// Setup 
		global $post; 
		global $pagelines_ID;
		global $pagelines_layout;
		$current_page_post = $post;
		
		if ( post_password_required() )
			return;

		if(empty($f)){
			echo setup_section_notify($this, __('No Feature posts matched this page\'s criteria', 'pagelines') );
			return;
		}

	// Options 
		$feature_source = ploption('feature_source', $this->oset);
		$timeout = ploption('timeout', $this->oset);
		$playpause = ploption('feature_playpause', $this->oset);
		$feature_nav_type = ploption('feature_nav_type', $this->oset);
	   
	// Refine
		$no_nav = ( isset($f) && count($f) == 1 ) ? ' nonav' : '';
		$footer_nav_class = $class. ' '. $feature_nav_type . $no_nav;
		$cycle_selector = "fclone".$clone_id;
?>		
	<div id="feature_slider" class="<?php echo $class;?> fix">
		<div id="feature-area">
			<div id="cycle" class="<?php echo $cycle_selector;?>">
			<?php
			
				foreach($f as $post) : 
						
						// Setup For Std WP functions
						setup_postdata($post); 

						$oset = array('post_id' => $post->ID);

						$feature_style = ( ploption( 'feature-style', $oset)) ? ploption('feature-style', $oset) : 'text-left';
						
						$feature_style = apply_filters( 'pagelines-feature-style', $feature_style );
						
						$flink_text = ( ploption( 'feature-link-text', $oset) ) ? __( ploption('feature-link-text', $oset) ) : __('More', 'pagelines');
						
						if ( $feature_source == 'posts' || $feature_source == 'posts_all') {
							
							if ( has_post_thumbnail( $post->ID ) ) {
								$feature_background_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail'  );
								$feature_background_image = $feature_background_image[0];
							} else {
								
								$feature_background_image = apply_filters( 'pagelines-feature-cat-default-image', $this->base_url.'/images/feature1.jpg', $post );
								
							}
							
							$background_class = 'bg_cover';
							
						} else {
							
							$feature_background_image = ploption( 'feature-background-image', $oset);
							
							$background_class = 'bg_cover';
							
						}

							
						$feature_design = (ploption( 'feature-design', $oset)) ? ploption('feature-design', $oset) : '';
						
						if ( $feature_source == 'posts' || $feature_source == 'posts_all' )
							setup_postdata( $post );
						
						$action = ( $feature_source == 'posts' || $feature_source == 'posts_all' ) ? get_permalink() : ploption('feature-link-url', $oset);
						
						$fcontent_class = (ploption( 'fcontent-bg', $oset)) ? ploption('feature-bg', $oset) : '';
						
						$media_image = ploption('feature-media-image', $oset);

						$feature_media = ploption( 'feature-media', $oset); 
						
						$feature_wrap_markup = ($feature_style == 'text-none' && $action) ? 'a' : 'div';
						$feature_wrap_link = ($feature_style == 'text-none' && $action) ? sprintf('href="%s"', $action) : '';
						
						$more_link = ($feature_style != 'text-none' && $action) ? sprintf( ' <a class="plmore" href="%s" >%s</a>', $action, $flink_text ) : '';
						
						$background_css = ($feature_background_image) ? sprintf('style="background-image: url(\'%s\');"', $feature_background_image ) : '';

						printf( '<div id="%s" class="fcontainer %s %s fix" >', 'feature_'.$post->ID, $feature_style, $feature_design ); 
						
						printf('<%s class="feature-wrap %s" %s %s >', $feature_wrap_markup, $background_class, $feature_wrap_link, $background_css); 
						
						if($feature_wrap_markup != 'a'):
						?>
							
								<div class="feature-pad fix">
									<div class="fcontent scale_text <?php echo $fcontent_class;?>">
										<div class="dcol-pad fix">
												<?php
												
													
													pagelines_register_hook( 'pagelines_feature_text_top', $this->id ); // Hook 
													
													$link = ( $feature_source == 'posts' || $feature_source == 'posts_all' ) ? sprintf( '<a href="%s">%s</a>', $action, $post->post_title ) : $post->post_title;
													
													$title = sprintf('<div class="fheading"> <h2 class="ftitle">%s</h2> </div>', $link);
													
													$content = ( $feature_source == 'posts' || $feature_source == 'posts_all' ) ? apply_filters( 'pagelines_feature_output', custom_trim_excerpt(get_the_excerpt(), '30')) : get_the_content(); 
											
													printf(
														'%s<div class="ftext"><div class="fexcerpt">%s%s%s</div></div>', 
														$title,
														$content, 
														$more_link,
														pledit( $post->ID )
													);
													
												pagelines_register_hook( 'pagelines_fcontent_after', $this->id ); // Hook ?>
										</div>
									</div>
						
									<div class="fmedia" style="">
										<div class="dcol-pad">
											<?php 
											
											pagelines_register_hook( 'pagelines_feature_media_top', $this->id ); // Hook 
											
											if( $media_image )											
												printf('<div class="media-frame"><img src="%s" /></div>', $media_image);
											
											elseif( $feature_media )
												echo do_shortcode($feature_media); 
												
												?>
										</div>
									</div>
									<?php pagelines_register_hook( 'pagelines_feature_after', $this->id ); // Hook ?>
									<div class="clear"></div>
								</div>
							
							<?php 
							endif;
							
						printf('</%s>', $feature_wrap_markup); 

					echo '</div>';
					
					
					endforeach; 
							
					$post = $current_page_post;

				 ?>
		
			</div>
		</div>
			<?php 
				
				pagelines_register_hook( 'pagelines_feature_nav_before', $this->id ); // Hook
				
				$playpause = ( $timeout != 0 && $playpause) ? sprintf('<span class="playpause pause %s"><span>&nbsp;</span></span>', $cycle_selector) : '';
				
				$nav = sprintf('<div id="featurenav" class="%s subtext fix"></div>', $cycle_selector);
				
				printf('<div id="feature-footer" class="%s fix"><div class="feature-footer-pad">%s%s<div class="clear"></div></div></div>', $footer_nav_class, $playpause, $nav);
?>	
	</div>
	<div class="clear"></div>
<?php
	}

	function section_scripts() {  
	
		return array(
				'cycle' => array(
						'file' => $this->base_url . '/script.cycle.js',
						'dependancy' => array('jquery'), 
						'location' => 'footer', 
						'version' => '2.9994'
					)	
			);
	}
	
	function post_meta_setup(){
		
			$pt_tab_options = array(
					'feature-style' => array(
							'type' 	=> 'select',					
							'title' => 'Feature Text Position',
							'shortexp' 	=> 'Select the type of feature style you would like to be shown. E.g. show text on left, right, bottom or not at all (full width)...',
							'selectvalues' => array(
								'text-left'		=> array( 'name' => 'Text On Left'),
								'text-right' 	=> array( 'name' => 'Text On Right'),
								'text-bottom' 	=> array( 'name' => 'Text On Bottom'),
								'text-none' 	=> array( 'name' => 'Full Width Background Image - No Text - Links Entire BG')
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
							'title' 			=> 'Feature Link URL',
							'label'				=> 'Enter Feature Link URL',
							'type' 				=> 'text', 
							'exp'				=> 'Adds a "More" link to your text. If you have "Full Width Background Image" mode selected, the entire slide will be linked.'
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
			$post_types = (ploption('feature_source') == 'posts') ? array( $this->ptID, 'post' ) : array( $this->ptID );
			
			$pt_panel = array(
					'id' 		=> 'feature-metapanel',
					'name' 		=> 'Feature Setup Options',
					'posttype' 	=> $post_types, 
					'hide_tabs'	=> true
				);
			
			$pt_panel =  new PageLinesMetaPanel( $pt_panel );
			
			
			$pt_tab = array(
				'id' 		=> 'feature-type-metatab',
				'name' 		=> 'Feature Setup Options',
				'icon' 		=> $this->icon,
			);
			
			$pt_panel->register_tab( $pt_tab, $pt_tab_options );
		
	}

	function post_type_setup(){
		
			$args = array(
					'label' 			=> __('Features', 'pagelines'),  
					'singular_label' 	=> __('Feature', 'pagelines'),
					'description' 		=> 'For setting slides on the feature page template',
					'taxonomies'		=> array( $this->taxID ), 
					'menu_icon'			=> $this->icon
				);	
			$taxonomies = array(
				$this->taxID => array(	
						'label' => __('Feature Sets', 'pagelines'), 
						'singular_label' => __('Feature Set', 'pagelines'), 
					)
			);
			$columns = array(
				'cb' 					=> "<input type=\"checkbox\" />",
				'title' 				=> 'Title',
				"feature-description" 	=> 'Text',
				"feature-media" 		=> 'Media',
				$this->taxID			=> 'Feature Sets'
			);
		
		
			$this->post_type = new PageLinesPostType( $this->ptID, $args, $taxonomies, $columns, array(&$this, 'column_display') );
		
			$this->post_type->set_default_posts( 'update_default_posts', $this);
			
	}

	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$page_metatab_array = array(
					
					'feature_stage_height' => array(
							'default' 		=> '380',
							'version'		=> 'pro',
							'type' 			=> 'text_small',
							'inputlabel' 	=> 'Enter the height (In Pixels) of the Feature Stage Area',
							'title' 		=> 'Feature Area Height',
							'shortexp' 		=> 'Use this feature to change the height of your feature area',
							'exp' 			=> "To change the height of your feature area, just enter a number in pixels here.",
					),
					'feature_items' 	=> array(
						'version' 		=> 'pro',
						'default'		=> 5,
						'type' 			=> 'text_small',
						'inputlabel'	=> 'Number of features to show',
						'title' 		=> 'Number of Feature Slides',
						'shortexp'		=> 'The amount of slides to show on this page',
						'exp' 			=> 'Enter the max number of feature slides to show on this page. Note: If left blank, the number of posts selected under reading settings in the admin will be used.'
					),			
					'feature_orderby' => array(
							'default' => 'ID',
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => array(
								'ID' 		=> array('name' => 'Post ID (default)'),
								'title' 		=> array('name' => 'Title'),
								'date' 		=> array('name' => 'Date'),
								'modified' 		=> array('name' => 'Last Modified'),
								'rand' 		=> array('name' => 'Random'),							
							),
							'inputlabel' => 'Select sort order',
							'title' => 'Feature Post sort order',
							'shortexp' => 'How will the features be sorted.',
							'exp' => "By default the feature section will sort by post ID."
						),
						
					'feature_order' => array(
							'default' => 'DESC',
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => array(
								'DESC' 		=> array('name' => 'Descending'),
								'ASC' 		=> array('name' => 'Ascending'),
							),
							'inputlabel' => 'Select sort order',
							'title' => 'Feature Post sort order',
							'shortexp' => 'How will the features be sorted.',
							'exp' => "By default the features will be in descending order."
						),

					'feature_set' => array(
						'version' 		=> 'pro',
						'default'		=> 'default-features',
						'type' 			=> 'select_taxonomy',
						'taxonomy_id'	=> $this->taxID,				
						'title' 		=> 'Select Feature Set To Show',
						'shortexp'		=> 'The "set" or category of feature posts',
						'inputlabel'	=> 'Select Feature Set',
						'exp' 			=> 'If you are using the feature section, select the feature set you would like to show on this page.'
					), 
					'feature_nav_type' => array(
						'default' => 'thumbs',
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
						'shortexp' => 'Select the mode for your feature navigation',
						'exp' => "Select from the three modes. Using feature names will use the names of the features, using the numbers will use incremental numbers, thumbnails will use feature thumbnails if provided.", 
						'docslink'		=> 'http://www.pagelines.com/docs/feature-slider', 
						'vidtitle'		=> 'View Feature Documentation'
					),
					'timeout' => array(
							'default' => '0',
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
							'selectvalues' => array('blindX', 'blindY', 'blindZ', 'cover', 'curtainX', 'curtainY', 'fade', 'fadeZoom', 'growX', 'growY', 'none', 'scrollUp', 'scrollDown', 'scrollLeft', 'scrollRight', 'scrollHorz', 'scrollVert','shuffle','slideX','slideY','toss','turnUp','turnDown','turnLeft','turnRight','uncover','wipe','zoom'),
							'inputlabel' => 'Select Transition Effect',
							'title' => 'Transition Effect',
							'shortexp' => 'How the features transition',
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
					'feature_source' => array(
							'default' => 'featureposts',
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => array(
								'featureposts' 	=> array('name' => 'Feature Posts (custom post type)'),
								'posts' 		=> array('name' => 'Use Post Category'),
								'posts_all' 	=> array('name' => 'Use all Posts'),
							),
							'inputlabel' => 'Select source',
							'title' => 'Feature Post Source',
							'shortexp' => 'Use feature posts or a post category',
							'exp' => "By default the feature section will use feature posts, you can also set the source for features to a blog post category. Set the category ID in its option below. <br/> <strong>NOTE: If set to posts, excerpts will be used as content (control length through them). Also a new option panel will be added on post creation and editing pages.</strong>"
						),
					'feature_category' => array(
							'default' => 1,
							'version'	=> 'pro',
							'type' => 'select',
							'selectvalues' => $this->get_cats(),
							'title' => 'Post Category (Blog Post Mode Only)',
							'shortexp' => "",
							'exp' => 'Select a category to use if sourcing features from blog posts'
						),
					'fremovesync' => array(
							'default' => false,
							'type' => 'check',
							'version'	=> 'pro',
							'inputlabel' => 'Remove Transition Syncing',
							'title' => 'Remove Feature Transition Syncing',
							'shortexp' => 'Make features wait to move on until after the previous one has cleared the screen',
							'exp' => "This controls whether features can move on to the screen while another is transitioning off. If removed features will have to leave the screen before the next can transition on to it."
						)
				);

			$metatab_settings = array(
					'id' 		=> 'feature_meta',
					'name' 		=> 'Features',
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab($metatab_settings, $page_metatab_array);

	}
	
	function update_default_posts(){

		$posts = array_reverse( $this->default_posts() );

		foreach($posts as $p){
			// Create post object
			$default = array();
			$default['post_title'] = $p['title'];
			$default['post_content'] = $p['text'];
			$default['post_type'] = $this->ptID;
			$default['post_status'] = 'publish';
			
			if ( defined( 'ICL_LANGUAGE_CODE' ) )
				$default_post['icl_post_language'] = ICL_LANGUAGE_CODE;
				
			$newPostID = wp_insert_post( $default );

			update_post_meta($newPostID, 'feature-thumb', $p['thumb']);
			update_post_meta($newPostID, 'feature-link-url', $p['link']);
			update_post_meta($newPostID, 'feature-style', $p['style']);
			update_post_meta($newPostID, 'feature-media', $p['media']);
			update_post_meta($newPostID, 'feature-background-image', $p['background']);
			update_post_meta($newPostID, 'feature-design', $p['fcontent-design']);
			wp_set_object_terms($newPostID, 'default-features', $this->taxID );
		}
	}

	function default_posts( ){

		$posts = array(
				'1' => array(
			        	'title' 			=> 'PageLines',
			        	'text' 				=> 'Welcome to PageLines Framework!',
			        	'media' 			=> '',
						'style'				=> 'text-none',
			        	'link' 				=> '#fake_link',
						'background' 		=> $this->base_url.'/images/feature1.jpg',
						'name'				=> 'Intro',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb1.png'
			    ),
				'2' => array(
					 	'title' 		=> 'Drag &amp; Drop Design',
			        	'text' 			=> 'Welcome to a professional WordPress framework by PageLines. Designed for you in San Francisco, California.',
			        	'media' 		=> '',
			        	'style'			=> 'text-none',
						'link' 			=> '#fake_link',
						'background' 	=> $this->base_url.'/images/feature2.jpg',
						'name'			=>	'Design',
						'fcontent-design'	=> '',
						'thumb'				=> $this->base_url.'/images/fthumb3.png'
			    )
		);

		return apply_filters('pagelines_default_features', $posts);
	}

	function get_cats() {
	
		$cats = get_categories();
		foreach( $cats as $cat )
			$categories[ $cat->cat_ID ] = array( 'name' => $cat->name );
			
		return ( isset( $categories) ) ? $categories : array();
	}

	function column_display($column){

		global $post;

		switch ($column){
			case "feature-description":
				the_excerpt();
				break;
			case "feature-media":
			 	if(m_pagelines('feature-media', $post->ID))
					em_pagelines('feature-media', $post->ID);
				elseif(m_pagelines('feature-media-image', $post->ID))
					echo '<img src="'.m_pagelines('feature-media', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
				elseif(m_pagelines('feature-background-image', $post->ID))
					echo '<img src="'.m_pagelines('feature-background-image', $post->ID).'" style="max-width: 200px; max-height: 200px" />'; 
				
				break;
			case $this->taxID:
				echo get_the_term_list($post->ID, $this->taxID, '', ', ','');
				break;
		}
	}
}