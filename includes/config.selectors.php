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
	
	// Layout
	$s['page_width'] = 'body.fixed_width #page, body.fixed_width #footer, body.canvas .page-canvas'; 
	$s['content_width'] = '#site .content, .wcontent, #footer .content';
	
	// Main Colors
	$s['bodybg'] = 'body, body.fixed_width, #footer .content';
	
	$s['pagebg'] = 'body #page, .sf-menu li, #primary-nav ul.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active, .commentlist ul.children .even, .alt #commentform textarea';
	
	$s['contentbg'] = '.canvas .page-canvas, .content';
	
	// Boxes & Elements 
	
	$s['box_color_primary'] = '#feature-footer, .main-nav li.current-page-ancestor a, .main-nav li.current_page_item a, .main-nav li.current-page-ancestor ul a, .main-nav li.current_page_item ul a, #wp-calendar caption, #buddypress-page #subnav, #buddypress-page .activity .activity-inner, #buddypress-page table.forum th, #grandchildnav.widget, input, textarea, .searchform .searchfield, .wp-caption, .commentlist .alt, #wp-calendar #today, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, .post-nav, .current_posts_info, .post-footer,  #twitterbar, #twitterbar .content, #carousel .content-pad, .success, .sf-menu li li, .sf-menu li li, .sf-menu li li li, .content-pagination a .cp-num, .hentry table .alternate td';
	
	$s['box_color_secondary'] = '.main_nav .main-nav li a:hover, #wp-calendar thead th, #buddypress-page #object-nav, .item-avatar a, .comment blockquote, #grandchildnav .current_page_item a, #grandchildnav li a:hover, #grandchildnav .current_page_item  ul li a:hover, #carousel .carousel_text, pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .sf-menu li:hover, .sf-menu li.sfHover, #featurenav a, #feature-footer span.playpause, .content-pagination .cp-num, .content-pagination a:hover .cp-num, ins';
	
	// Borders 
	
	$s['border_layout'] = 'hr, .fpost, .clip_box, .widget-title, #buddypress-page .item-list li, .metabar a, #morefoot .widget-title, #site #dsq-content h3, .main_nav_container, ul.sf-menu ul li';
	
	$s['box_color_tertiary'] = '#buddypress-page #object-nav ul li a:hover,#buddypress-page #object-nav ul li.selected a, #buddypress-page #subnav a:hover, #buddypress-page #subnav li.current a, #featurenav a.activeSlide';

	$s['border_primary'] = 'ul.sf-menu ul li, .post-nav, .current_posts_info, .post-footer, blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, .fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img, #carousel .content ul li a img, #carousel .content ul li a:hover img, #feature-footer';
	
	$s['border_secondary'] = '#featurenav a, #feature-footer span.playpause';
	
	$s['border_tertiary'] = '#featurenav a.activeSlide';
	
	$s['border_primary_shadow'] = 'blockquote, input, textarea, .searchform .searchfield, .wp-caption, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img';
	
	$s['border_primary_highlight'] = '#feature-footer .feature-footer-pad';
	
	// Text
	
	$s['headercolor'] = 'h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, a.site-title, .entry-title a, .entry-title a:hover, .widget-tit	le a:hover, h3.widget-title a:hover';
	
	$s['text_primary'] = '#page, .tcolor1, #subnav ul li a:active, .commentlist cite a, #breadcrumb a, .metabar a:hover, .post-nav a:hover, .post-footer a, #buddypress-page #object-nav ul li a, #buddypress-page table.forum .td-title a, #buddypress-page #subnav a:hover, #buddypress-page #subnav li.current a, #twitterbar a, #carousel .carousel_text, #site #dsq-content .dsq-request-user-info td a, #pagination .wp-pagenavi a:hover, #pagination .wp-pagenavi .current, #featurenav a.activeSlide, .content-pagination a:hover .cp-num';
	
	$s['text_secondary'] = '.tcolor2, .lcolor2 a, .subhead, .widget-title, #branding .site-description, #callout, #commentform .required, #postauthor .subtext, #buddypress-page .standard-form .admin-links,  #carousel .thecarousel, #pagination .wp-pagenavi span.pages, .commentlist .comment-meta  a,  #highlight .highlight-subhead, .content-pagination span, .content-pagination a .cp-num, .comment.alt .comment-author';

	$s['text_tertiary'] = '.tcolor3, .lcolor3 a, .main_nav li a,  .widget-title a, h3.widget-title a, #subnav_row li a, .metabar em, .metabar a, .tags, #commentform label, .form-allowed-tags code, .rss-date, #breadcrumb, .comment.alt, .reply a, .post-nav a, .post-nav a:visited, .post-footer, .auxilary a, #buddypress-page .standard-form .admin-links a, #twitterbar .content .tbubble, .widget ul.twitter .twitter-item, .cform .emailreqtxt,.cform .reqtxt, #pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .main_nav ul.sf-menu a, .sf-menu a:visited, #featurenav a, #feature-footer span.playpause';
	
	
	$s['text_box'] = '#wp-calendar caption, .searchform .searchfield, #primary-nav .current-menu-item a';
	
	
	$s['linkcolor'] = 'a, #subnav_row li.current_page_item a, #subnav_row li a:hover, #grandchildnav .current_page_item > a, .branding h1 a:hover, .post-comments a:hover, .bbcrumb a:hover, 	#feature_slider .fcontent.fstyle-lightbg a, #feature_slider .fcontent.fstyle-nobg a';
	
	$s['linkcolor_hover'] = 'a:hover,.commentlist cite a:hover,  #grandchildnav .current_page_item a:hover, .headline h1 a:hover';
	
	$s['footer_text'] = '#footer, #footer li.link-list a, #footer .latest_posts li .list-excerpt';
	$s['footer_highlight'] = '#footer a, #footer .widget-title,  #footer li h5 a';
	
	
	
	// Text Shadows & Effects
	
	$s['text_shadow_color']	= '#feature-footer, #grandchildnav li a, #grandchildnav .current_page_item  ul li a, #buddypress-page #object-nav ul li a';
	$s['footer_text_shadow_color'] = '#footer, .fixed_width #footer';
	
	// Output & Extension 
	
	if(isset($s[ $group ]))
		return apply_filters('pagelines_css_group', $s[ $group ], $group);
	else 
		return apply_filters('pagelines_css_group_'.$group, '');
}

