<?php
/**
 * 
 *
 *  PageLines Posts Handling
 *
 *
 *  @package PageLines Framework
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
		
		$this->count = 1;  // Used to get the number of the post as we loop through them.
		$this->clipcount = 2; // The number of clips in a row

		$this->post_count = $wp_query->post_count;  // Used to prevent markup issues when there aren't an even # of posts.
		$this->paged = intval(get_query_var('paged')); // Control output if on a paginated page

		$this->thumb_space = get_option('thumbnail_size_w') + 33; // Space for thumb with padding

		$this->continue_reading = apply_filters('continue_reading_link_text', load_pagelines_option('continue_reading_text', __('[Continue Reading...]', 'pagelines')));
		
		add_filter('pagelines_post_metabar', 'do_shortcode', 20);
		
		if(has_action('add_social_under_meta') || ploption('share_under_meta'))
			add_filter('pagelines_post_metabar', array( &$this,'add_social_share'), 10, 2);
			
		if(has_action('add_social_under_excerpt'))
			add_filter('pagelines_post_header', array( &$this,'add_social'));

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function add_social_share($input, $format){
		
		if ( ! class_exists( 'PageLinesShareBar' ) || $format == 'clip')
			return $input;
		global $post;
		
		$share = PageLinesShareBar::get_shares();
		$meta_share = sprintf('<div class="meta-share">%s</div>', $share);
		
		return $input.$meta_share;
	}
	
	
	/**
	*
	* @TODO document
	*
	*/
	function add_social($input, $format){
		
		if ( ! class_exists( 'PageLinesShareBar' ) || $format == 'clip')
			return $input;
		global $post;
		
		$args = array('permalink' => get_permalink($post->ID), 'width'=>'50', 'title' => get_the_title($post->ID));
		$share = PageLinesShareBar::facebook($args);
		$share .= PageLinesShareBar::twitter($args);
		$meta_share = sprintf('<div class="meta-share">%s</div>', $share);
		
		return $input.$meta_share;
	}
	
	
	/**
	 * Loads the content using WP's standard output functions
	 *
	 * @since 2.0.0
	 *
	 */
	function load_loop(){
	
		if(have_posts())
			while (have_posts()) : the_post();  $this->get_article(); endwhile;
		else 
			$this->posts_404();
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function get_article(){
		global $wp_query;
		
		/* clip handling */
		$clip = ($this->pagelines_show_clip($this->count, $this->paged)) ? true : false;
		$format = ($clip) ? 'clip' : 'feature';
		$clip_row_start = ($this->clipcount % 2 == 0) ? true : false;
		$clip_right = ( ($this->clipcount+1) % 2 == 0 ) ? true : false;
		$clip_row_end = ( $clip_right || $this->count == $this->post_count ) ? true : false;
		
		$post_type_class = ($clip) ? ( $clip_right ? 'clip clip-right' : 'clip' ) : 'fpost';
		
		$pagelines_post_classes = apply_filters( 'pagelines_get_article_post_classes', sprintf( '%s post-number-%s', $post_type_class, $this->count ) );
		
		$post_classes = join(' ', get_post_class( $pagelines_post_classes ));
		
		$wrap_start = ( $clip && $clip_row_start ) ? sprintf('<div class="clip_box fix">') : ''; 	
		$wrap_end = ( $clip && $clip_row_end ) ? sprintf('</div>') : '';
	
		$out = sprintf(
			'%s<article class="%s" id="post-%s"><div class="hentry-pad %s">%s%s</div></article>%s', 
			$wrap_start, 
			$post_classes, 
			get_the_ID(),
			($clip) ? 'blocks' : '', 
			$this->post_header( $format ), 
			$this->post_entry(), 
			$wrap_end
		);
		
		echo apply_filters( 'pagelines_get_article_output', $out );
		
		// Count the clips
		if( $clip ) 
			$this->clipcount++;
		
		// Count the posts
		$this->count++;
	 }
	

	/**
	*
	* @TODO document
	*
	*/
	function post_entry(){ 
		
		if( $this->pagelines_show_content( get_the_ID() ) ){
				
			$post_entry = sprintf('<div class="entry_wrap fix"><div class="entry_content">%s</div></div>', $this->post_content());
		
			return apply_filters('pagelines_post_entry', $post_entry);
		
		} else 
			return '';
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function post_content(){
	
		ob_start();
		
			pagelines_register_hook( 'pagelines_loop_before_post_content', 'theloop' ); // Hook

		//	global  $post;
			
			$content = get_the_content( $this->continue_reading );
			
			$content .= pledit( get_the_ID() );
			
			echo apply_filters('the_content', $content);
	
			if( is_single() || is_page() )
				wp_link_pages(array('before'=> __("<p class='content-pagination'><span class='cp-desc'>pages:</span>", 'pagelines'), 'after' => '</p>', 'pagelink' => '<span class="cp-num">%</span>')); 
		
			if ( is_single() && get_the_tags() )
				printf('<div class="p tags">%s&nbsp;</div>', get_the_tag_list(__('Tagged with: ', 'pagelines'),' &bull; ','') );
		
			//	if (is_home() && ploption('content_comments'))
			//	echo do_shortcode('<div class="cnt-comments">[post_comments]</div>');
	
			pagelines_register_hook( 'pagelines_loop_after_post_content', 'theloop' ); // Hook 
		
		$the_content = ob_get_clean();

		return $the_content;
	
		
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function post_header( $format = '' ){ 
		
		if( $this->show_post_header() ){
			
			global $post;
			
			$id = get_the_ID();
			
			$excerpt_mode = ($format == 'clip') ? ploption('excerpt_mode_clip') : ploption('excerpt_mode_full');
			
			$thumb = ( $this->pagelines_show_thumb( $id ) ) ? $this->post_thumbnail_markup( $excerpt_mode, $format ) : '';
			
			$excerpt = ( $this->pagelines_show_excerpt( $id ) ) ? $this->post_excerpt_markup( $excerpt_mode, $thumb ) : '';
			
			$classes = 'post-meta fix ';
			$classes .= (!$this->pagelines_show_thumb( $id )) ? 'post-nothumb ' : '';
			$classes .= (!$this->pagelines_show_content( $id )) ? 'post-nocontent ' : '';
				
			$title = sprintf('<section class="bd post-title-section fix"><hgroup class="post-title fix">%s</hgroup>%s</section>', $this->pagelines_get_post_title($format), $this->pagelines_get_post_metabar( $format ));
			
			
			if($excerpt_mode == 'left-excerpt' || $excerpt_mode == 'right-excerpt')
				$post_header = sprintf('<section class="%s"><section class="bd post-header fix " >%s %s</section></section>', $classes, $title, $excerpt);
			elseif($excerpt_mode == 'top')
				$post_header = sprintf('<section class="%s">%s<section class="bd post-header fix" >%s %s</section></section>',$classes, $thumb, $title, $excerpt);
			else
				$post_header = sprintf('<section class="%s media">%s<section class="bd post-header fix" >%s %s</section></section>', $classes, $thumb, $title, $excerpt);
			
			
			return apply_filters( 'pagelines_post_header', $post_header );
			
		} else 
			return '';
		
			
	}
	
	
	
	/**
	 * Determines if the post title area should be shown
	 *
	 * @since 2.0.0
	 *
	 * @return bool True if the title area should be shown
	 */
	function show_post_header( ) {
		
		if( !is_page() || (is_page() && ploption('pagetitles')) )
			return true;
		else
			return false;
		
	}
	
	/**
	 * Get post excerpt and markup
	 *
	 * @since 2.0.0
	 *
	 * @return string the excerpt markup
	 */
	function post_excerpt_markup( $mode = '', $thumbnail = '' ) {
		
		ob_start();
		
		pagelines_register_hook( 'pagelines_loop_before_excerpt', 'theloop' ); // Hook
		
		if($mode == 'left-excerpt' || $mode == 'right-excerpt')
			printf( '<aside class="post-excerpt">%s %s</aside>', $thumbnail, get_the_excerpt() );
		else
			printf( '<aside class="post-excerpt">%s</aside>', get_the_excerpt() );
		
	
		if(pagelines_is_posts_page() && !$this->pagelines_show_content( get_the_ID() )) // 'Continue Reading' link
			echo $this->get_continue_reading_link( get_the_ID() );
		
//		if (is_home() && ploption('content_comments'))
//			$pagelines_excerpt .= do_shortcode('<div class="cnt-comments">[post_comments]</div>');
		
		pagelines_register_hook( 'pagelines_loop_after_excerpt', 'theloop' ); // Hook 
			
		$pagelines_excerpt = ob_get_clean();
		
		return apply_filters('pagelines_excerpt', $pagelines_excerpt);
		
	}
	
	
	/**
	 * Get post thumbnail and markup
	 *
	 * @since 2.0.0
	 *
	 * @return string the thumbnail markup
	 */
	function post_thumbnail_markup( $mode = '', $format = '', $frame = '') {
		
		$thumb_width = get_option('thumbnail_size_w');
		
		$classes = 'post-thumb img fix';
		
		$percent_width  = ($mode == 'top') ? 100 : 25;
		
		$style = sprintf('width: %s%%; max-width: %spx', $percent_width, $thumb_width);
		
		if($mode == 'left-excerpt')
			$classes .= ' alignleft';
		elseif($mode == 'right-excerpt')
			$classes .= ' alignright';
		elseif($mode == 'top'){
			$classes .= ' left';
		}
		
		global $post;
		
		$img = ($mode == 'top') ? get_the_post_thumbnail(null, 'large') : get_the_post_thumbnail(null, 'thumbnail');
		
		$the_image = sprintf('<span class="c_img">%s</span>', $img);
		
		$thumb_link = sprintf('<a class="%s" href="%s" rel="bookmark" title="%s %s" style="%s">%s</a>', $classes, get_permalink( $post ), __('Link To', 'pagelines'), the_title_attribute( array('echo' => false) ), $style, $the_image );
		
		$output = ($mode == 'top') ? sprintf('<div class="full_img fix">%s</div>', $thumb_link) : $thumb_link;
		
		return apply_filters('pagelines_thumb_markup', $output, $mode, $format);
		
	}
	
	/**
	 * Adds the metabar or byline under the post title
	 *
	 * @since 1.1.0
	 */
	function pagelines_get_post_metabar( $format = '' ) {

		$metabar = '';
		$before = '<em>';
		$after = '</em>';
		if ( is_page() )
			return; // don't do post-info on pages

		if( $format == 'clip'){
			
			$metabar = ( pagelines_option( 'metabar_clip' ) ) 
				? $before . pagelines_option( 'metabar_clip' ) . $after
				: sprintf( '%s%s [post_date] %s [post_author_posts_link] [post_edit]%s', $before, __('On','pagelines'), __('By','pagelines'), $after );

		} else {

			$metabar = ( pagelines_option( 'metabar_standard' ) ) 
				? $before . pagelines_option( 'metabar_standard' ) . $after
				: sprintf( '%s%s [post_author_posts_link] %s [post_date] &middot; [post_comments] &middot; %s [post_categories] [post_edit]%s', $before, __('By','pagelines'), __('On','pagelines'), __('In','pagelines'), $after);

		}

		return sprintf( '<div class="metabar"><div class="metabar-pad">%s</div></div>', apply_filters('pagelines_post_metabar', $metabar, $format) );

	}

	/**
	 * 
	 *  Gets the Post Title for Blog Posts
	 *
	 *  @package PageLines Framework
	 *  @subpackage Functions Library
	 *  @since 1.1.0
	 *
	 */
	function pagelines_get_post_title( $format = '' ){ 
		
		global $post;

		if(is_page() && pagelines_option('pagetitles')){
			$title = sprintf( '<h1 class="entry-title pagetitle">%s</h1>', apply_filters( 'pagelines_post_title_text', get_the_title() ) );	
		} elseif(!is_page()) {

			if ( is_singular() ) 
				$title = sprintf( '<h1 class="entry-title">%s</h1>', apply_filters( 'pagelines_post_title_text', get_the_title() ) );
			elseif( $format == 'clip')
				$title = sprintf( '<h4 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h4>', get_permalink( $post ), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );
			else
				$title = sprintf( '<h2 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h2>', get_permalink( $post ), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );

		} else {$title = '';}


		return apply_filters('pagelines_post_title_output', $title) . "\n";

	}



	/**
	 * 
	 *  Gets the continue reading link after excerpts
	 *
	 *  @package PageLines Framework
	 *  @subpackage Functions Library
	 *  @since 1.3.0
	 *
	 */
	function get_continue_reading_link($post_id){

		$link = sprintf(
			'<a class="continue_reading_link" href="%s" title="%s %s">%s</a>', 
			get_permalink(), 
			__("View", 'pagelines'), 
			the_title_attribute(array('echo'=> 0)), 
			$this->continue_reading 
		);

		return apply_filters('continue_reading_link', $link);
	}
	

	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_thumb($post = null, $location = null){

		 if( function_exists('the_post_thumbnail') && has_post_thumbnail($post) ){

			// For Hook Parsing
			if( is_admin() || ! get_option(PAGELINES_SETTINGS) ) return true;

			if( $location == 'clip' && ploption('thumb_clip') ) return true;

			if( !isset($location) ){
				// Thumb Page
				if( is_single() && ploption('thumb_single') ) return true;

				// Blog Page
				elseif( is_home() && ploption('thumb_blog') ) return true;

				// Search Page
				elseif( is_search() && ploption('thumb_search') ) return true;

				// Category Page
				elseif( is_category() && ! is_date() && ploption('thumb_category') ) return true;

				// Archive Page
				elseif( ! is_category() && is_archive() && ploption('thumb_archive') ) return true;

				else return false;
			} else return false;
		} else return false;

	}
	

	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_excerpt( $post = null ){

			if( is_page() )
				return false;

			// Thumb Page
			if( is_single() && ploption('excerpt_single') ) 
				return true;

			// Blog Page
			elseif( is_home() && ploption('excerpt_blog') ) 
				return true;

			// Search Page
			elseif( is_search() && ploption('excerpt_search') ) 
				return true;

			// Category Page
			elseif( is_category() && ! is_date() && ploption('excerpt_category') )
				return true;
				
			// Archive Page
			elseif( ! is_category() && is_archive() && ploption('excerpt_archive') ) 
				return true;
			else 
				return false;
	}


	/**
	*
	* @TODO document
	*
	*/
	function pagelines_show_content($post = null){
			// For Hook Parsing
			if( is_admin() ) 
				return true;

			// show on single post pages only
			if( is_page() || is_single() ) 
				return true;

			// Blog Page
			elseif( is_home() && ploption('content_blog') ) 
				return true;

			// Search Page
			elseif( is_search() && ploption('content_search') ) 
				return true;

			// Category Page
			elseif( is_category() && ploption('content_category') ) 
				return true;

			// Archive Page
			elseif( ! is_category() && is_archive() && ploption('content_archive') ) 
				return true;

			else 
				return false;

	}

	/*
		Show clip or full width post
	*/
	function pagelines_show_clip($count, $paged){

		if(!VPRO) 
			return false;

		if(is_home() && ploption('blog_layout_mode') == 'magazine' && $count <= ploption('full_column_posts') && $paged == 0)
			return false;

		elseif(ploption('blog_layout_mode') != 'magazine') 
			return false;

		elseif(is_page() || is_single()) 
			return false;

		else 
			return true;
	}
	
	

	/**
	*
	* @TODO document
	*
	*/
	function posts_404(){
		
		$head = ( is_search() ) ? sprintf(__('No results for &quot;%s&quot;', 'pagelines'), get_search_query()) : __('Nothing Found', 'pagelines');
		
		$subhead = ( is_search() ) ? __('Try another search?', 'pagelines') : __("Sorry, what you are looking for isn't here.", 'pagelines');
		
		$the_text = sprintf('<h2 class="center">%s</h2><p class="subhead center">%s</p>', $head, $subhead);
		
			printf( '<section class="billboard">%s <div class="center fix">%s</div></section>', apply_filters('pagelines_posts_404', $the_text), pagelines_search_form( false ));
		
	}
	

}
/* ------- END OF CLASS -------- */
