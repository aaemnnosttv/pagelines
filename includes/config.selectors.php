<?php
/**
 * 
 *
 *  CSS Selector Groups 
 *  for dynamic CSS control
 *
 *  @package PageLines Core
 *  @subpackage Options
 *  @since 2.0.b6
 *
 */
function cssgroup( $group ){
	
	$s = array();
	
	/**
	 * Layout Width Control
	 */
	$s['page_width'] = 'body.fixed_width #page, body.fixed_width #footer, body.canvas .page-canvas'; 
	$s['content_width'] = '#site .content, .wcontent, #footer .content';
	
	/**
	 * Main Page Element Colors
	 */
	$s['bodybg'] = 'body, body.fixed_width, #footer .content';
	
	$s['pagebg'] = 'body #page,  .commentlist ul.children .even, .alt #commentform textarea';
	
	$s['contentbg'] = '.canvas .page-canvas, .content, .sf-menu li, #primary-nav ul.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active';

	$s['page_background_image'] = '.canvas #page, .full_width #page, body.fixed_width';
	
	/**
	 * Box & Element Colors
	 */
	$s['box_color_primary'] = '#feature-footer,  #wp-calendar caption, #grandchildnav.widget, input, textarea, .searchform .searchfield, .wp-caption, .commentlist .alt, #wp-calendar #today, .post-nav, .current_posts_info, .post-footer,  #twitterbar, #twitterbar .content, #carousel .content-pad, .success,  .content-pagination a .cp-num, .hentry table .alternate td';
	
	$s['box_color_secondary'] = '#wp-calendar thead th, .item-avatar a, .comment blockquote, #grandchildnav .current_page_item a, #grandchildnav li a:hover, #grandchildnav .current_page_item  ul li a:hover, #carousel .carousel_text, pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend,  #featurenav a, #feature-footer span.playpause, .content-pagination .cp-num, .content-pagination a:hover .cp-num, ins';
	
	$s['box_color_tertiary'] = '#featurenav a.activeSlide';
	
	$s['box_color_lighter'] = '.post-meta .c_img';
	
	/**
	 * Border Colors
	 */
	$s['border_layout'] = 'hr, .fpost, .clip_box, .widget-title, .metabar a, #morefoot .widget-title, #site #dsq-content h3, .main_nav_container, .widget-default, .fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img';
	
	$s['border_primary'] = '#feature-footer, .post-nav, .current_posts_info, .post-footer, blockquote, input, textarea, .searchform .searchfield, .wp-caption,   #grandchildnav.widget,  #carousel .content ul li a img, #carousel .content ul li a:hover img, #soapbox .fboxinfo, #feature-footer.nonav, .post-meta .c_img';
	
	$s['border_secondary'] = '#featurenav a, #feature-footer span.playpause';
	
	$s['border_tertiary'] = '#featurenav a.activeSlide';
	
	$s['border_primary_shadow'] = 'blockquote, input, textarea, .searchform .searchfield, .wp-caption,  #grandchildnav.widget, fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img';
	
	$s['border_primary_highlight'] = '#feature-footer .feature-footer-pad, .bhighlight';
	
	/**
	 * Text Colors
	 */
	$s['headercolor'] = 'h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, a.site-title, .entry-title a, .entry-title a:hover, .widget-title a:hover, h3.widget-title a:hover';
	
	$s['text_primary'] = '#page, .tcolor1, #subnav ul li a:active, .commentlist cite a, #breadcrumb a, .metabar a:hover, .post-nav a:hover, .post-footer a,  #carousel .carousel_text, #site #dsq-content .dsq-request-user-info td a, #pagination .wp-pagenavi a:hover, #pagination .wp-pagenavi .current, #featurenav a.activeSlide, .content-pagination a:hover .cp-num';
	
	$s['text_secondary'] = '.tcolor2, .lcolor2 a, .subhead, .widget-title, #branding .site-description, #callout, #commentform .required, #postauthor .subtext, #carousel .thecarousel, #pagination .wp-pagenavi span.pages, .commentlist .comment-meta  a,  #highlight .highlight-subhead, .content-pagination span, .content-pagination a .cp-num, .comment.alt .comment-author, .tcolor3, .lcolor3 a, .main_nav a, .widget-title a, h3.widget-title a, #subnav_row li a, .metabar em, .metabar a, .tags, #commentform label, .form-allowed-tags code, .rss-date, #breadcrumb, .comment.alt, .reply a,  .post-footer, .auxilary a, .widget ul.twitter .twitter-item, .cform .emailreqtxt,.cform .reqtxt, #pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend';

	$s['text_tertiary'] = '';
	
	$s['text_box'] = '#featurenav a, #feature-footer span.playpause, .post-nav a, .post-nav a:visited, #twitterbar .content .tbubble, #wp-calendar caption, .searchform .searchfield, .main_nav .current-menu-item a, .main_nav li a:hover, .main_nav li a:hover, #wp-calendar thead th';
	$s['text_box_secondary'] = '#twitterbar a';
	
	$s['linkcolor'] = 'a, #subnav_row li.current_page_item a, #subnav_row li a:hover, #grandchildnav .current_page_item > a, .branding h1 a:hover, .post-comments a:hover, .bbcrumb a:hover, 	#feature_slider .fcontent.fstyle-lightbg a, #feature_slider .fcontent.fstyle-nobg a';
	
	$s['linkcolor_hover'] = 'a:hover,.commentlist cite a:hover,  #grandchildnav .current_page_item a:hover, .headline h1 a:hover';
	
	$s['footer_text'] = '#footer, #footer li.link-list a, #footer .latest_posts li .list-excerpt';
	$s['footer_highlight'] = '#footer a, #footer .widget-title,  #footer li h5 a';
	
	/**
	 * Text Shadows & Effects 
	 */
	$s['text_shadow_color']	= '#feature-footer, #grandchildnav li a, #grandchildnav .current_page_item  ul li a';
	$s['footer_text_shadow_color'] = '#footer, .fixed_width #footer';
		
	/**
	 * Typography 
	 */
	$s['type_headers'] = 'h1, h2, h3, h4, h5, h6, .site-title';
	$s['type_primary'] = 'body, .font1, .font-primary, .commentlist';
	$s['type_secondary'] = '.font2, .font-sub, ul.main-nav li a, #secondnav li a, .metabar, .subtext, .subhead, .widget-title, .post-comments, .reply a, .editpage, #pagination .wp-pagenavi, .post-edit-link, #wp-calendar caption, #wp-calendar thead th, .soapbox-links a, .fancybox, .standard-form .admin-links, #featurenav a, .pagelines-blink, .ftitle small';
	$s['type_inputs'] = 'input[type="text"], input[type="password"], textarea, #dsq-content textarea';
	
	
	/**
	 * Output & Extension 
	 */
	
	if(is_array($group)){
		$sel = '';
		foreach($group as $g){
			
			if(isset($s[ $g ]))
				$sel .= apply_filters('pagelines_css_group', $s[ $g ], $g);
			else	
				$sel .= apply_filters('pagelines_css_group_'.$g, '');	
				
		}	
	
		return $sel;
		
	} else {
			
		if(isset($s[ $group ]))
			return apply_filters('pagelines_css_group', $s[ $group ], $group);
		else 
			return apply_filters('pagelines_css_group_'.$group, '');
	
	}
}

