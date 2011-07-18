<?php



function cssgroup( $group ){
	
	$s = array();
	
	$s['bodybg'] = 'body, body.fixed_width';
	
	$s['pagebg'] = 'body #page, .sf-menu li, #primary-nav ul.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active, .commentlist ul.children .even, .alt #commentform textarea';
	
	$s['page_content_bg'] = '.canvas .page-canvas, .content';
	
	$s['border_layout'] = 'hr, .fpost, .clip_box, .widget-title, #buddypress-page .item-list li, .metabar a, #morefoot .widget-title, #site #dsq-content h3, .main_nav_container, ul.sf-menu ul li';
	
	$s['box_color_primary'] = '#feature-footer, .main-nav li.current-page-ancestor a, .main-nav li.current_page_item a, .main-nav li.current-page-ancestor ul a, .main-nav li.current_page_item ul a, #wp-calendar caption, #buddypress-page #subnav, #buddypress-page .activity .activity-inner, #buddypress-page table.forum th, #grandchildnav.widget, input, textarea, .searchform .searchfield, .wp-caption, .commentlist .alt, #wp-calendar #today, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, .post-nav, .current_posts_info, .post-footer,  #twitterbar, #twitterbar .content, #carousel .content-pad, .success, .sf-menu li li, .sf-menu li li, .sf-menu li li li, .content-pagination a .cp-num, .hentry table .alternate td';
	
	$s['box_color_secondary'] = '.main_nav .main-nav li a:hover, #wp-calendar thead th, #buddypress-page #object-nav, .item-avatar a, .comment blockquote, #grandchildnav .current_page_item a, #grandchildnav li a:hover, #grandchildnav .current_page_item  ul li a:hover, #carousel .carousel_text, pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .sf-menu li:hover, .sf-menu li.sfHover, #featurenav a, #feature-footer span.playpause, .content-pagination .cp-num, .content-pagination a:hover .cp-num, ins';
	
	$s['box_color_tertiary'] = '#buddypress-page #object-nav ul li a:hover,#buddypress-page #object-nav ul li.selected a, #buddypress-page #subnav a:hover, #buddypress-page #subnav li.current a, #featurenav a.activeSlide';

	$s['border_primary'] = 'ul.sf-menu ul li, .post-nav, .current_posts_info, .post-footer, blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, .fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img, #carousel .content ul li a img, #carousel .content ul li a:hover img, #feature-footer';
	
	$s['border_secondary'] = '#featurenav a, #feature-footer span.playpause';
	
	$s['border_tertiary'] = '#featurenav a.activeSlide';
	
	$s['border_primary_shadow'] = 'blockquote, input, textarea, .searchform .searchfield, .wp-caption, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img';
	
	$s['border_primary_highlight'] = '#feature-footer .feature-footer-pad';
	
	$s['text_shadow_color']	= '#feature-footer, #grandchildnav li a, #grandchildnav .current_page_item  ul li a, #buddypress-page #object-nav ul li a';
	
	$s['text_secondary'] = '.tcolor2, .lcolor2 a, .subhead, .widget-title,  .post-edit-link, .metabar .sword, #branding .site-description, #callout, #commentform .required, #postauthor .subtext, #buddypress-page .standard-form .admin-links, #wp-calendar caption, #carousel .thecarousel, #pagination .wp-pagenavi span.pages, .commentlist .comment-meta  a,  #highlight .highlight-subhead, .content-pagination span, .content-pagination a .cp-num, .searchform .searchfield, .comment.alt .comment-author';

	$s['text_tertiary'] = '.tcolor3, .lcolor3 a, .main_nav li a,  .widget-title a, h3.widget-title a, #subnav_row li a, .metabar em, .metabar a, .tags, #commentform label, .form-allowed-tags code, .rss-date, #breadcrumb, .comment.alt, .reply a, .post-nav a, .post-nav a:visited, .post-footer, .auxilary a, #buddypress-page .standard-form .admin-links a, #twitterbar .content .tbubble, .widget ul.twitter .twitter-item, .cform .emailreqtxt,.cform .reqtxt, #pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .main_nav ul.sf-menu a, .sf-menu a:visited, #featurenav a, #feature-footer span.playpause';
	
	$s['linkcolor_hover'] = 'a:hover,.commentlist cite a:hover,  #grandchildnav .current_page_item a:hover, .headline h1 a:hover';
	
	return $s[ $group ];
	
}

