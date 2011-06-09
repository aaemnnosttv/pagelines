<?php

/**
 * 
 *
 *  Options Array
 *
 *
 *  @package PageLines Options
 *  @subpackage Options
 *  @since 2.0.b3
 *
 */

class PageLinesOptionsArray {


	/**
	 * Construct
	 */
	function __construct() {
		
		if(!pagelines_option('hide_introduction') && VPRO)
			$this->options['_welcome'] = $this->welcome();
		
		$this->options['website_setup'] = $this->website_setup();
		$this->options['template_setup'] = $this->template_setup();
		$this->options['layout_editor'] = $this->layout_editor();
		$this->options['design_control'] = $this->design_control();
		$this->options['header_and_footer'] = $this->header_footer();
		$this->options['blog_and_posts'] = $this->blog_posts();
		
		$this->last_options['section_options'] = $this->section_options();
		
		if( pagelines_option('forum_options') )
			$this->last_options['forum_settings'] = $this->forum_options();
		
		$this->last_options['advanced'] 	= $this->advanced();
		$this->last_options['custom_code'] = $this->custom_code();
	}

	function website_setup(){
		$a = array(
			'email_capture'	=> array(
				'default'		=> '',
				'version'		=> 'free',
				'type' 			=> 'email_capture',
				'inputlabel' 	=> 'Email Address',
				'title'			=> 'Email Updates',						
				'shortexp' 		=> 'Optionally sign up for email updates and notifications.',
				'exp' 			=> 'Adding your email here will allow us to send you email notifications about updates and new software from PageLines'
			),
			'pagelines_custom_logo' => array(
				'default' 		=> PL_IMAGES.'/logo-platformpro.png',
				'default_free'	=> '',
				'type' 			=> 'image_upload',
				'imagepreview' 	=> '270',
				'inputlabel' 	=> 'Upload custom logo',
				'title'			=> 'Custom Header Image',						
				'shortexp' 		=> 'Input Full URL to your custom header or logo image.',
				'exp' 			=> 'Optional way to replace "heading" and "description" text for your website ' . 
						    		'with an image.'
			),
			'pagelines_favicon'	=> array(
				'default' 	=> 	PL_ADMIN_IMAGES . "/favicon-pagelines.ico",
				'type' 		=> 	'image_upload',
				'imagepreview' 	=> 	'16',
				'title' 	=> 	'Favicon Image',						
				'shortexp' 	=> 	'Input Full URL to favicon image ("favicon.ico" image file)',
				'exp' 		=> 	'Enter the full URL location of your custom "favicon" which is visible in ' .
							'browser favorites and tabs.<br/> (<strong>Must be .png or .ico file - 16px by 16px</strong> ).'
			),		
			'twittername' => array(
				'default' => '',
				'type' => 'text',
				'inputlabel' => 'Your Twitter Username',
				'title' => 'Twitter Integration',
				'shortexp' => 'Places your Twitter feed in your site (<em>"Twitter for WordPress" plugin required</em>)',
				'exp' => 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.<br/><br/><strong>Note: "Twitter for WordPress" plugin is required for this to work.</strong>'
			),
	
			'pagelines_touchicon'	=> array(
				'version' 	=> 'pro',
				'default' 	=> '',
				'type' 		=> 	'image_upload',
				'imagepreview' 	=> 	'60',
				'title' 	=> 'Apple Touch Image',						
				'shortexp' 	=> 'Input Full URL to Apple touch image (.jpg, .gif, .png)',
				'exp'		=> 'Enter the full URL location of your Apple Touch Icon which is visible when ' .
						  'your users set your site as a <strong>webclip</strong> in Apple Iphone and ' . 
						  'Touch Products. It is an image approximately 57px by 57px in either .jpg, ' .
						  '.gif or .png format.'
			),
		
			'sidebar_no_default' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Hide Sidebars When Empty (no widgets)',
					'title'		=> 'Remove Default Sidebars When Empty',
					'shortexp'	=> 'Hide default sidebars when sidebars have no widgets in them',
					'exp'		=> 'This allows you to remove sidebars completely when they have no widgets in them.'
			),
			'sidebar_wrap_widgets' => array(
					'default' 	=> 'top',
					'version'	=> 'pro',
					'type' 		=> 'select',
					'selectvalues'	=> array(
						'top'		=> array("name" => 'On Top of Sidebar'),
						'bottom'	=> array("name" => 'On Bottom of Sidebar')
					),
					'inputlabel' 	=> 'Sidebar Wrap Widgets Position',
					'title' 	=> 'Sidebar Wrap Widgets',
					'shortexp' 	=> 'Choose whether to show the sidebar wrap widgets on the top or bottom of the sidebar.',
					'exp' 		=> 'You can select whether to show the widgets that you place in the sidebar wrap template in either the top or the bottom of the sidebar.'
			),
		
		);
		if ( get_option( 'pagelines_email_sent') ) unset($a['email_capture']);
		return apply_filters('pagelines_options_website_setup', $a);
	}
	
	/**
	 * Get Template Setup - Drag & Drop Interface
	 *
	 * @since 2.0.0
	 */
	function template_setup(){

		 $a = array(
			'templates'		=> array(
				'default'	=> '',
				'type'		=> 'templates',
				'layout'	=> 'interface',
				'title'		=> THEMENAME.' Template Setup',						
				'shortexp'	=> 'Drag and drop control over your website\'s templates.<br/> Note: Select "Hidden by Default" to hide the section by default; and activate with individual page/post options.',
				'docslink'	=> 'http://www.pagelines.com/docs/template-setup', 
				'vidtitle'	=> 'Template Setup Overview'
			),
			'resettemplates' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Template Section Order", 'pagelines'),
				'type'		=> 'reset',
				'callback'	=> 'reset_templates_to_default',
				'title'		=> 'Reset Section Order To Default',	
				'layout'	=> 'full',					
				'shortexp'	=> 'Changes your template sections back to their default order and layout (options settings are not affected)',
			)		
		);
		
		return apply_filters('pagelines_options_template_setup', $a);
		
	}
	
	/**
	 * Layout Editor Interface & Options
	 *
	 * @since 2.0.0
	 */
	function layout_editor(){

		$a = array(
			'layout_default' => array(
				'default' 	=> "one-sidebar-right",
				'type' 		=> 'layout_select',
				'title' 	=> 'Default Layout Mode',	
				'layout' 	=> 'interface',						
				'shortexp' 	=> 'Select your default layout mode, this can be changed on individual pages.<br />Once selected, you can adjust the layout in the Layout Builder.',
				'exp' 		=> 'The default layout for your site; your blog page will always have this layout. Dimensions can be changed using the content layout editor.',
				'docslink'	=> 'http://www.pagelines.com/docs/editing-layout'
			),
			'layout' => array(
				'default'	=> 'one-sidebar-right',
				'type'		=> 'layout',
				'layout'	=> 'interface',
				'title'		=> 'Content Layout Editor',						
				'shortexp'	=> 'Configure the default layout for your site which is initially selected in the Default Layout Mode option in Global Options. <br/>This option allows you to adjust columns and margins for the default layout.',
			), 
			'resetlayout' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Layout", 'pagelines'),
				'type' 		=> 'reset',
				'callback'	=> 'reset_layout_to_default',
				'title' 	=> 'Reset Layout To Default',	
				'layout'	=> 'full',					
				'shortexp'	=> 'Changes layout mode and dimensions back to default',
			)
		);
		
		return apply_filters('pagelines_options_layout_editor', $a);
		
	}
	
	/**
	 * Design Control and Color Options
	 *
	 * @since 2.0.0
	 */
	function design_control(){

		$a = array(	
			'site_design_mode'	=> array(
				'version'	=> 'pro',
				'default'	=> 'canvas',
				'type'		=> 'select',
				'layout'	=> 'full',
				'selectvalues'	=> array(
					'canvas'	=> array("name" => "Full-Width Design With Canvas"),
					'full_width'	=> array("name" => "Full-Width Design Framework"),
					'fixed_width'	=> array("name" => "Fixed-Width Design Framework", "version" => "pro")
				), 
				'inputlabel'	=> 'Site Design Mode',
				'title'		=> 'Site Design Mode',						
				'shortexp'	=> 'Choose between full width HTML or fixed width HTML',
				'exp'		=> 'There are three css design modes available for '.THEMENAME.'. Each allows a different style of design.<br/><br/><strong>Full-Width Mode With Canvas</strong> This design mode has a full-width page area, and a canvas area behind your content that can be controlled seperately.<br/><br/><strong>Full-Width Mode</strong> Full width design mode allows you to have aspects of your site that are the full-width of your screen; while others are the width of the content area.<br/><br/><strong>Fixed-Width Mode</strong> Fixed width design mode creates a fixed with "page" that can be used as the area for your design.  You can set a background to the page; and the content will have a seperate "fixed-width" background area (i.e. the width of the content).',
				'vidlink'	=> 'http://www.youtube.com/embed/hnaXANV0nlk?hd=1', 
				'vidtitle'	=> 'Design Control Overview'
			),
			'page_colors'		=> array(
				'title' 	=> 'Basic Layout Colors',						
				'shortexp' 	=> 'The Main Layout Colors For Your Site',
				'exp' 		=> 'Use these options to configure the main layout colors for your site.<br/><br/>This theme as two background elements, the "page" or content area and the "body" which sits behind the page area, you can set their colors individually here.',
				'type' 		=> 'color_multi',
				'selectvalues'	=> array(
					'bodybg'	=> array(				
					'default' 	=> '#000000',
					'css_prop'	=> 'background-color',
					'selectors'	=> 'body, body.fixed_width',
					'inputlabel' 	=> 'Body Background Color <small>Shown Behind Page Content Area (e.g. In Footer)</small>',
					),
				'pagebg'		=> array(				
					'default' 	=> '#FFFFFF',
					'selectors'	=>	'body #page, .sf-menu li, #primary-nav ul.sf-menu a:focus, .sf-menu a:hover, .sf-menu a:active, .commentlist ul.children .even, .alt #commentform textarea',
					'css_prop'	=> 'background-color',
					'inputlabel' 	=> 'Page Background Color <small>The Background Of Page Area</small>',
					),
				'page_content_bg'	=> array(				
					'version'	=> 'pro',
					'default' 	=> '#FFFFFF',
					'selectors'	=>	'.canvas #page-canvas',
					'css_prop'	=> 'background-color',
					'inputlabel' 	=> 'Page Canvas Background Color <small>The Background Color Site Content (Canvas Mode Only)</small>',
					),
				'border_layout'		=> array(				
					'default' 	=> '#E9E9E9',
					'selectors'	=>	'hr, .fpost, .clip_box, .widget-title, #buddypress-page .item-list li, .metabar a, #morefoot .widget-title, #site #dsq-content h3, .post.fpost .entry, #soapbox .fboxinfo,  #primary-nav #nav_row, .fpost.sticky',
					'css_prop'	=> 'border-color',
					'inputlabel'	=> 'Border Color - Layout Borders and Dividers <small>Borders In Layout, Against Page Background</small>',
					),
				),
			),
			'page_background_image' => array(
				'title' 	=> 'Site Background Image (Optional)',						
				'shortexp' 	=> 'Setup A Background Image For The Background Of Your Site',
				'exp' 		=> 'Use this option to apply a background image to your site. This option will be applied to different areas depending on the design mode you have set.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the "top" or "left" side, 50% to center, etc..',
				'type' 		=> 'background_image',
				'selectors'	=> '.canvas #page, .full_width #page, body.fixed_width'
			),
			'text_colors'		=> array(
				'title' 	=> 'Page Text Colors',						
				'shortexp' 	=> 'Control The Color Of Text Used Throughout Your Site',
				'exp' 		=> 'These options control the colors of the text throughout the page or content area of your site.<br/><br/>Certain text types are designed to contrast with different box elements and are meant to be used with hover effects.<br/><br/>Experiment to find exactly how colors are combined with text on your site.',
				'type' 		=> 'color_multi',
				'selectvalues'	=> array(
					'headercolor'	=> array(		
					'default' 	=> '#000000',
					'selectors'	=> 'h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a, a.site-title, .entry-title a, .entry-title a:hover, .widget-title a:hover, h3.widget-title a:hover',
					'inputlabel' 	=> 'Page - Text Header Color <small>Titles, H1,H2, etc...</small>',
					),
				'text_primary' => array(		
					'default' 	=> '#000000',
					'selectors'	=>	'#page, .tcolor1, #subnav ul li a:active, .commentlist cite a, #breadcrumb a, .metabar a:hover, .post-nav a:hover, .post-footer a, #buddypress-page #object-nav ul li a, #buddypress-page table.forum .td-title a, #buddypress-page #subnav a:hover, #buddypress-page #subnav li.current a, #twitterbar a, #carousel .carousel_text, #site #dsq-content .dsq-request-user-info td a, #pagination .wp-pagenavi a:hover, #pagination .wp-pagenavi .current, #featurenav a.activeSlide, .content-pagination a:hover .cp-num',
					'inputlabel' 	=> 'Page - Primary Text Color <small>The Main Text Color Used Throughout The Site</small>',
					),
				'text_secondary' => array(
					'default' 	=> '#AFAFAF',
					'selectors'	=>	'.tcolor2, .lcolor2 a, .subhead, .widget-title,  .post-edit-link, .metabar .sword, #branding .site-description, #callout, #commentform .required, #postauthor .subtext, #buddypress-page .standard-form .admin-links, #wp-calendar caption, #carousel .thecarousel, #pagination .wp-pagenavi span.pages, .commentlist .comment-meta  a,  #highlight .highlight-subhead, .content-pagination span, .content-pagination a .cp-num, .searchform .searchfield',
					'inputlabel' 	=> 'Page - Secondary Text Color <small>Used In Subtitles, Widget Titles, Nav, Etc...</small>',
					),
				'text_tertiary' => array(	
					'default' 	=> '#777777',
					'selectors'	=>	'.tcolor3, .lcolor3 a, .main_nav li a,  .widget-title a, h3.widget-title a, #subnav_row li a, .metabar em, .metabar a, .tags, #commentform label, .form-allowed-tags code, .rss-date, #breadcrumb, .reply a, .post-nav a, .post-nav a:visited, .post-footer, .auxilary a, #buddypress-page .standard-form .admin-links a, #twitterbar .content .tbubble, .widget ul.twitter .twitter-item, .cform .emailreqtxt,.cform .reqtxt, #pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .main_nav ul.sf-menu a, .sf-menu a:visited, #featurenav a, #feature-footer span.playpause',
					'inputlabel' 	=> 'Page - Tertiary Text Color <small>Used In Navigation, Hover effects</small>',
					),
				),
			),

			'link_colors' => array(
				'title' 	=> 'Link Colors',						
				'shortexp' 	=> 'Control The Color Of Links',
				'exp' 		=> 'These options control the colors of the links throughout the page or content area of your site.',
				'type' 		=> 'color_multi',
				'selectvalues'	=> array(
					'linkcolor' => array(
						'default'	=> '#225E9B',
						'selectors'	=>	'a, #subnav_row li.current_page_item a, #subnav_row li a:hover, #grandchildnav .current_page_item > a, .branding h1 a:hover, .post-comments a:hover, .bbcrumb a:hover, 	#feature_slider .fcontent.fstyle-lightbg a, #feature_slider .fcontent.fstyle-nobg a',
						'inputlabel' 	=> 'Text Link Color <small>Color Of Links Throughout Your Site</small>',						
					),
					'linkcolor_hover' => array(
							'default' 	=> '#0F457C',
							'selectors'	=> 'a:hover,.commentlist cite a:hover,  #grandchildnav .current_page_item a:hover, .headline h1 a:hover',
							'inputlabel' 	=> 'Text Link Hover Color <small>Color Of Links When Hovered Over</small>',						
					),
				),
			),
			'element_colors_primary' => array(
				'title' 	=> 'Primary Site Element Colors',						
				'shortexp' 	=> 'Setup The Colors For Common Elements Used On Your site',
				'exp' 		=> 'Site elements are the basic contrast elements of your site. For example: selected navigation items, feature footer, blockquotes, etc...<br/><br/>This option sets the most commonly used element color.. it is used with form inputs, blockquotes, etc...',
				'type' 		=> 'color_multi',
				'selectvalues'	=> array(
					'box_color_primary'	=> array(				
						'default' 	=> '#F7F7F7',
						'selectors'	=>	'#feature-footer, .main-nav li.current-page-ancestor a, .main-nav li.current_page_item a, .main-nav li.current-page-ancestor ul a, .main-nav li.current_page_item ul a, #wp-calendar caption, #buddypress-page #subnav, #buddypress-page .activity .activity-inner, #buddypress-page table.forum th, #grandchildnav.widget, blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, .commentlist .alt, #wp-calendar #today, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, .post-nav, .current_posts_info, .post-footer,  #twitterbar, #carousel .content-pad, .success, .sf-menu li li, .sf-menu li li, .sf-menu li li li, .content-pagination a .cp-num, .hentry table .alternate td',
						'css_prop'	=> 'background',
						'inputlabel' 	=> 'Box Color - Primary Elements <small>The Main Contrast Color Between Page Background And Site Elements</small>',
						),
					'border_primary' => array(				
						'default' 	=> '#E9E9E9',
						'selectors'	=>	'ul.sf-menu ul li, .post-nav, .current_posts_info, .post-footer, blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, .fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img, #carousel .content ul li a img, #carousel .content ul li a:hover img, #feature-footer',
						'css_prop'	=> 'border-color',
						'inputlabel' 	=> 'Border Color - Primary Elements Border <small>Standard Border For Site Elements (Make Slightly Darker Than Box Color)</small>',
					),
					'border_primary_shadow' => array(				
						'default' 	=> '#DDDDDD',
						'selectors'	=>	'multi_property',
						'css_prop'	=> array(
							'border-left-color'	=> 'blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img', 
							'border-top-color'	=> 'blockquote, input, textarea, .searchform .searchfield, .wp-caption, .widget-default, #buddypress-page div.activity-comments form .ac-textarea, #buddypress-page form#whats-new-form #whats-new-textarea, #grandchildnav.widget, fpost .post-thumb img, .clip .clip-thumb img, .author-thumb img',
						),
					'inputlabel' 	=> 'Border Color - Primary Elements Shadow <small>Shadow Effect On Some Elements (Make Slightly Darker Than Border)</small>',
					),
					'border_primary_highlight' => array(				
						'default' 	=> '#FFFFFF',
						'selectors'	=>	'multi_property',
						'css_prop'	=> array(
						'border-left-color'	=> '#feature-footer .feature-footer-pad', 
						'border-top-color'	=> '#feature-footer .feature-footer-pad',
					),
					'inputlabel' 	=> 'Border Color - Primary Elements Highlight <small>Highlight Effect On Some Elements (Make Slightly Lighter Than Box Color)</small>',
					),
					'text_shadow_color' => array(				
						'default' 	=> '#FFFFFF',
						'selectors'	=> 'multi_property',
						'css_prop'	=> array(
							'text-shadow'	=> '#feature-footer, #grandchildnav li a, #grandchildnav .current_page_item  ul li a, #buddypress-page #object-nav ul li a',
						),
						'inputlabel' 	=> 'Text Shadow Color <small>Used To Create An Indented Effect On Selected Text</small>',
					),
				),

			),
			'element_colors_secondary' => array(	
				'title' 	=> 'Secondary Site Element Colors',						
				'shortexp' 	=> 'Setup Colors For Elements Designed To Contrast With The Primary Elements (Hover Effects, etc...)',
				'exp' 		=> 'The secondary elements are designed to contrast with the primary elements. For example, a navigation hover effect, or calendar subheading.',
				'type' 		=> 'color_multi',
				'selectvalues'	=> array(
					'box_color_secondary' => array(				
						'default' 		=> '#F1F1F1',
						'selectors'		=>	'#wp-calendar thead th, #buddypress-page #object-nav, .item-avatar a, .comment blockquote, #grandchildnav .current_page_item a, #grandchildnav li a:hover, #grandchildnav .current_page_item  ul li a:hover, #carousel .carousel_text, pagination .wp-pagenavi a, #pagination .wp-pagenavi .current, #pagination .wp-pagenavi .extend, .sf-menu li:hover, .sf-menu li.sfHover, #featurenav a, #feature-footer span.playpause, .content-pagination .cp-num, .content-pagination a:hover .cp-num, ins',
						'css_prop'		=> 'background',
						'inputlabel' 	=> 'Box Color - Secondary Site Elements <small>Recommended Slightly Darker/Lighter Than Primary Element Color</small>',
					),
				'border_secondary' => array(				
						'default' 		=> '#DDDDDD',
						'selectors'		=> '#featurenav a, #feature-footer span.playpause',
						'css_prop'		=> 'border-color',
						'inputlabel' 	=> 'Border Color - Secondary Elements <small>Around Secondary Box Elements (Make Slightly Darker)</small>',
					),
				'border_secondary_shadow' => array(				
						'default' 		=> '#CCCCCC',
						'selectors'		=>	'multi_property',
						'css_prop'		=> array(
							'border-left-color'		=> '#featurenav a, #feature-footer span.playpause', 
							'border-top-color'		=> '#featurenav a, #feature-footer span.playpause',
						),
				'inputlabel' 	=> 'Border Color - Secondary Elements Shadow <small>Shadow Effect On Secondary Box Elements (Make Slightly Darker Than Border)</small>',
					),
				),
			),
			'element_colors_tertiary' => array(	
				'title' 		=> 'Tertiary Site Element Colors',						
				'shortexp' 		=> 'Setup Colors For Elements Designed To Contrast With The Secondary Elements',
				'exp' 			=> 'The tertiary elements are usually designed to contrast with the secondary elements. For example, a hover or selected effect that occurs over a primary element. Example: Feature Footer, selected navigation (dots, names, etc..)',
				'type' 			=> 'color_multi',
				'selectvalues'	=> array(
					'box_color_tertiary' => array(				
						'default' 		=> '#E1E1E1',
						'selectors'		=>	'#buddypress-page #object-nav ul li a:hover,#buddypress-page #object-nav ul li.selected a, #buddypress-page #subnav a:hover, #buddypress-page #subnav li.current a, #featurenav a.activeSlide',
						'css_prop'		=> 'background',
						'inputlabel' 	=> 'Box Color - Tertiary Site Elements <small>Slightly Darker/Lighter Than Secondary Element Color</small>',
					),
					'border_tertiary' => array(				
						'default' 		=> '#CCCCCC',
						'selectors'		=> '#featurenav a.activeSlide',
						'css_prop'		=> 'border-color',
						'inputlabel' 	=> 'Border Color - Tertiary Elements <small>Around Tertiary Box Elements (Make Slightly Darker)</small>',
					),
					'border_tertiary_shadow' => array(				
						'default' 		=> '#999999',
						'selectors'		=>	'multi_property',
						'css_prop'		=> array(
							'border-left-color'		=> '#featurenav a.activeSlide', 
							'border-top-color'		=> '#featurenav a.activeSlide',
						),
					'inputlabel' 	=> 'Border Color - Tertiary Elements Shadow <small>Shadow Effect On Tertiary Box Elements (Make Slightly Darker Than Border)</small>',
					),
				),
			 ),
			'footer_text_colors' => array(
				'title' 		=> 'Footer/Body Text Colors',						
				'shortexp' 		=> 'Control The Color Of Text In The Footer or Body Background Of Your Site',
				'exp' 			=> 'These options control the colors of the text in the footer of your site.',
				'type' 			=> 'color_multi',
				'selectvalues'	=> array(
					'footer_text' => array(
						'default'		=> '#999999',
						'selectors'		=>	'#footer, #footer li.link-list a, #footer .latest_posts li .list-excerpt',
						'inputlabel' 	=> 'Footer Text Color <small>Default Color Of Text In The Footer</small>',						
					),
					'footer_highlight' => array(
						'default' 		=> '#FFFFFF',
						'selectors'		=>	'#footer a, #footer .widget-title,  #footer li h5 a',
						'inputlabel' 	=> 'Footer Highlight Text <small>Used With Links, Titles, etc..</small>',						
					),
					'footer_text_shadow_color' => array(
						'default' 		=> '#000000',
						'selectors'		=> 'multi_property',
						'css_prop'		=> array(
							'text-shadow-top'	=> '#footer, .fixed_width #footer',
						),
					'inputlabel' 	=> 'Footer Text Shadow Color <small>Used To Create An Indented Effect On Footer Text</small>',
					),
				),
			),

		);
		
		return apply_filters('pagelines_options_design_control', $a);
		
	}
	
	/**
	 * Typography Options
	 *
	 * @since 2.0.0
	 */
	function typography(){

		$a = array(

			'type_headers' => array(
					'default' 	=> array(
					'font' 		=> 'georgia',
					),
					'type' 		=> 'typography',
					'selectors'	=> 'h1, h2, h3, h4, h5, h6, .site-title',
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Text Headers',
					'shortexp' 	=> 'Select and Style Your Site\'s Header Tags (H1, H2, H3...)',
					'exp' 		=> 'Set typography for your h1, h2, etc.. tags. <br/><br/><strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts<br/><br/><strong>Note:</strong> These options make use of the <a href="http://code.google.com/webfonts" target="_blank">Google fonts API</a> to vastly increase the number of websafe fonts you can use.',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),

			'type_primary' => array(
					'default' 	=> array(
					'font' 		=> 'georgia', 
					),
					'type'		=> 'typography',
					'selectors'	=> 'body, .font1, .font-primary, .commentlist',
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Primary Font',
					'shortexp' 	=> 'Select and Style The Standard Type Used In Your Site (body)',
					'exp' 		=> 'Set typography for your primary site text. This is assigned to your site\'s body tag. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),


			'type_secondary' => array(
					'default' 	=> array( 'font' => 'lucida_grande' ),
					'type' 		=> 'typography',
					'selectors'	=> '.font2, .font-sub, ul.main-nav li a, #secondnav li a, .metabar, .subtext, .subhead, .widget-title, .post-comments, .reply a, .editpage, #pagination .wp-pagenavi, .post-edit-link, #wp-calendar caption, #wp-calendar thead th, .soapbox-links a, .fancybox, .standard-form .admin-links, #featurenav a, .pagelines-blink, .ftitle small',
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Secondary Font ',
					'shortexp' 	=> 'Select and Style Your Site\'s Secondary or Sub Title Text (Metabar, Sub Titles, etc..)',
					'exp' 		=> 'This options sets the typography for secondary text used throughout your site. This includes your navigation, subtitles, widget titles, etc.. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),

			'type_inputs' => array(
					'version' 	=> 'pro',
					'default' 	=> array(
					'font' 		=> 'courier_new',
					),
					'type' 		=> 'typography',
					'selectors'	=> 'input[type="text"], input[type="password"], textarea, #dsq-content textarea',
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Inputs and Textareas',
					'shortexp' 	=> 'Select and Style Your Site\'s Text Inputs and Textareas.',
					'exp' 		=> 'This options sets the typography for general text inputs and textarea inputs. This includes default WordPress comment fields, etc.. <br/><br/> This option makes use of the <a href="http://code.google.com/webfonts">Google fonts API</a> to vastly increase the number of websafe fonts you can use.<br/><strong>*</strong> Denotes web safe fonts<br/><strong>G</strong> Denotes Google fonts<br/><br/><strong>Note:</strong> the "preview" pane represents the font in your current browser and OS. If developing locally, Google fonts require an internet connection.',
			),

			'typekit_script' => array(
					'default'	=> "",
					'type'		=> 'textarea',
					'inputlabel'	=> 'Typekit Header Script',
					'title'		=> 'Typekit Font Replacement',
					'shortexp'	=> 'Typekit is a service that allows you to use tons of new fonts on your site.',
					'exp'		=> 'Typekit is a new service and technique that allows you to use fonts outside of the 10 or so "web-safe" fonts. <br/><br/>' .
							 'Visit <a href="www.typekit.com" target="_blank">Typekit.com</a> to get the script for this option. Instructions for setting up Typekit are <a href="http://typekit.assistly.com/portal/article/6780-Adding-fonts-to-your-site" target="_blank">here</a>.'
			),
			'fontreplacement' => array(
					'version'	=> 'pro',
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'	=> 'Use Cufon font replacement?',
					'title'		=> 'Use Cufon Font Replacement',
					'shortexp'	=> 'Use a special font replacement technique for certain text',
					'exp'		=> 'Cufon is a special technique for allowing you to use fonts outside of the 10 or so "web-safe" fonts. <br/><br/>' .
							 THEMENAME.' is equipped to use it.  Select this option to enable it. Visit the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a>.'
			),
			'font_file'	=> array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> 'Cufon replacement font file URL',
					'title'		=> 'Cufon: Replacement Font File URL',
					'shortexp'	=> 'The font file used to replace text.',
					'exp'		=> 'Use the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a> to generate a font file for use with this theme.  Place it in your theme folder and add the full URL to it here. The default font is Museo Sans.'
			),
			'replace_font' => array(
					'version'	=> 'pro',
					'default'	=> 'h1',
					'type'		=> 'text',
					'inputlabel'	=> 'CSS elements for font replacement',
					'title'		=> 'Cufon: CSS elements for font replacement',
					'shortexp'	=> 'Add selectors of elements you would like replaced.',
					'exp'		=> 'Use standard CSS selectors to replace them with your Cufon font. Font replacement must be enabled.'
			),
		);
		
		return apply_filters('pagelines_options_typography', $a);
		
	}
	
	/**
	 * Header and Footer Options
	 *
	 * @since 2.0.0
	 */
	function header_footer(){

		$a = array(
			'icon_position' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'tiny',
					'selectvalues'	=> array(
						'icon_pos_bottom'	=> array('inputlabel'=>'Distance From Bottom (in pixels)', 'default'=> 21),
						'icon_pos_right'	=> array('inputlabel'=>'Distance From Right (in pixels)', 'default'=> 1),
					),
					'title'		=> 'Social Icon Position',
					'shortexp'	=> 'Control the location of the social icons in the branding section',
					'exp'		=> 'Set the position of your header icons with these options. They will be relative to the "branding" section of your site.'
			),
			'rsslink' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'	=> 'Display the Blog RSS icon and link?',
					'title'		=> 'News/Blog RSS Icon',
					'shortexp'	=> 'Places News/Blog RSS icon in your header',
					'exp'		=> ''
				),
			'icon_social' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'regular',
					'selectvalues'	=> array(
						'facebooklink'		=> array('inputlabel'=>'Your Facebook Profile URL', 'default'=> ''),
						'twitterlink'		=> array('inputlabel'=>'Your Twitter Profile URL', 'default'=> ''),
						'linkedinlink'		=> array('inputlabel'=>'Your LinkedIn Profile URL', 'default'=> ''),
						'youtubelink'		=> array('inputlabel'=>'Your YouTube Profile URL', 'default'=> ''),
					),
					'title'		=> 'Social Icons',
					'shortexp'	=> 'Add social network profile icons to your header',
					'exp'		=> 'Fill in the URLs of your social networking profiles. This option will create icons in the header/branding section of your site.'
			),
			'nav_use_hierarchy' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'	=> 'Use Child Pages For Secondary Nav?',
					'title'		=> 'Use Child Pages for Secondary Nav',
					'shortexp'	=> 'Use this options if you want child pages in secondary nav, instead of WP menus.',
					'exp'		=> ''
				),
			'footer_logo' => array(
					'version'	=> 'pro',
					'default'	=> PL_IMAGES.'/logo-platformpro-small.png',
					'type'		=> 'image_upload',
					'imagepreview'	=> '100',
					'inputlabel'	=> 'Add Footer logo',
					'title'		=> 'Footer Logo',
					'shortexp'	=> 'Show a logo in the footer',
					'exp'		=> 'Add the full url of an image for use in the footer. Recommended size: 140px wide.'
			),
			'footer_more' => array(
					'default'	=> "Thanks for dropping by! Feel free to join the discussion by leaving " . 
							"comments, and stay updated by subscribing to the <a href='".get_bloginfo('rss2_url')."'>RSS feed</a>.",
					'type'		=> 'textarea',
					'inputlabel'	=> 'More Statement In Footer',
					'title'		=> 'More Statement',
					'shortexp'	=> 'Add a quick statement for users who want to know more...',
					'exp'		=> "This statement will show in the footer columns under the word more. It is for users who may want to know more about your company or service."
			),
			'footer_terms' => array(
					'default' 	=> '&copy; '.date('Y').' '.get_bloginfo('name'),
					'type' 		=> 'textarea',
					'inputlabel' 	=> 'Terms line in footer:',
					'title' 	=> 'Site Terms Statement',
					'shortexp' 	=> 'A line in your footer for "terms and conditions text" or similar',
					'exp' 		=> "It's sometimes a good idea to give your users a terms and conditions statement so they know how they should use your service or content."
			)
		);
		
		return apply_filters('pagelines_options_header_footer', $a);
		
	}

	/**
	 * Blog and Post Options
	 *
	 * @since 2.0.0
	 */
	function blog_posts(){

		$a = array(
			'blog_layout_mode'	=> array(
					'version'		=> 'pro',
					'default'		=> 'magazine',
					'type'			=> 'select',
					'selectvalues'	=> array(
						'magazine'	=> array("name" => "Magazine Layout Mode", "version" => "pro"),
						'blog'		=> array("name" => "Blog Layout Mode")
						), 
					'inputlabel'	=> 'Post Layout Mode',
					'title'			=> 'Blog Post Layout Mode',						
					'shortexp'		=> 'Choose between magazine style and blog style layout.',
					'exp'			=> 'Choose between two magazine or blog layout mode. <br/><br/> <strong>Magazine Layout Mode</strong><br/> Magazine layout mode makes use of post "clips". These are summarized excerpts shown at half the width of the main content column.<br/>  <strong>Note:</strong> There is an option for showing "full-width" posts on your main "posts" page.<br/><br/><strong>Blog Layout Mode</strong><br/> This is your classical blog layout. Posts span the entire width of the main content column.'
				), 
			'full_column_posts'	=> array(
					'version'		=> 'pro',
					'default'		=> 2,
					'type'			=> 'count_select',
					'count_number'	=> get_option('posts_per_page'),
					'inputlabel'	=> 'Number of Full Width Posts?',
					'title'			=> 'Full Width Posts (Magazine Layout Mode Only)',						
					'shortexp'		=> 'When using magazine layout mode, select the number of "featured" or full-width posts.',
					'exp'			=> 'Select the number of posts you would like shown at the full width of the main content column in magazine layout mode (the rest will be half-width post "clips").'
				),

			'posts_page_layout' => array(
					'type'		=> 'select',
					'selectvalues'=> array(
						'fullwidth'		=> array( 'name' => 'Fullwidth layout', 'version' => 'pro' ),
						'one-sidebar-right' 	=> array( 'name' => 'One sidebar on right' ),
						'one-sidebar-left'	=> array( 'name' => 'One sidebar on left' ),
						'two-sidebar-right' 	=> array( 'name' => 'Two sidebars on right', 'version' => 'pro' ),
						'two-sidebar-left' 	=> array( 'name' => 'Two sidebars on left', 'version' => 'pro' ),
						'two-sidebar-center' 	=> array( 'name' => 'Two sidebars, one on each side', 'version' => 'pro' ),
					),
					'title'		=> "Posts Page-Content Layout",
					'shortexp'	=> "Select the content layout on posts pages only",
					'inputlabel'	=> 'Posts Page Layout Mode (optional)',
					'exp'		=> 'Use this option to change the content layout mode on all posts pages (if different than default layout).'
				),
			'thumb_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'thumb_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> true),
						'thumb_single'		=> array('inputlabel'=>'Single Post Pages', 'default'=> false),
						'thumb_search' 		=> array('inputlabel'=>'Search Results', 'default'=> false),
						'thumb_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> true),
						'thumb_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> true),
						'thumb_clip' 		=> array('inputlabel'=>'In Post Clips (Magazine Mode)', 'default'=> true),
					),
					'title'		=> 'Post Thumbnail Placement',
					'shortexp'	=> 'Where should the theme use post thumbnails?',
					'exp'		=> 'Use this option to control where post "featured images" or thumbnails are used. Note: The post clips option only applies when magazine layout is selected.'
			),
			'byline_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'byline_author'		=> array('inputlabel'=>'Author', 'default'=> true),
						'byline_date'		=> array('inputlabel'=>'Date', 'default'=> true),
						'byline_comments'	=> array('inputlabel'=>'Comments', 'default'=> true),
						'byline_categories' 	=> array('inputlabel'=>'Categories', 'default'=> false),
					),
					'title'		=> 'Post Byline Information (Blog Mode and Full-Width Posts Only)',
					'shortexp'	=> 'What should be shown in post bylines?',
					'exp'		=> 'The byline shows meta information about who wrote the post, what category it is in, etc... Use this option to control what is shown.'
			),
			'excerpt_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'excerpt_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> true),
						'excerpt_single'	=> array('inputlabel'=>'Single Post Pages', 'default'=> false),
						'excerpt_search'	=> array('inputlabel'=>'Search Results', 'default'=> true),
						'excerpt_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> true),
						'excerpt_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> true),
					),
					'title'		=> 'Post Excerpt or Summary Handling',
					'shortexp'	=> 'Where should the theme use post excerpts when showing full column posts?',
					'exp'		=> 'This option helps you control where post excerpts are displayed.<br/><br/> <strong>About:</strong> Excerpts are small summaries of articles filled out when creating a post.'
			),
			'pagetitles' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Automatically show Page titles?',
					'title'		=> 'Page Titles',						
					'shortexp'	=> 'Show the title of pages above the page content.',
					'exp'		=> 'This option will automatically place page titles on all pages.'
			),
			'continue_reading_text' => array(
					'version'	=> 'pro',
					'default'	=> 'Read Full Article &rarr;',
					'type'		=> 'text',
					'inputlabel'	=> 'Continue Reading Link Text',
					'title'		=> '"Continue Reading" Link Text (When Using Excerpts)',						
					'shortexp'	=> 'The link at the end of your excerpt.',
					'exp' 		=> "This text will be used as the link to your full article when viewing articles on your posts page (when excerpts are turned on)."
			),
			'content_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'=> array(
						'content_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> false),
						'content_search'	=> array('inputlabel'=>'Search Results', 'default'=> false),
						'content_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> false),
						'content_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> false),
					),
					'title'		=> 'Full Post Content',
					'shortexp'	=> 'In addition to single post pages and page templates, where should the theme place the full content of posts?',
					'exp'		=> 'Choose where the full content of posts is displayed. Choose between all posts pages or just single post pages (i.e. posts pages can just show excerpts or titles).'
			),

			'post_footer_social_text' => array(
					'default'	=> 'If you enjoyed this article, please consider sharing it!',
					'type'		=> 'text',
					'inputlabel'	=> 'Post Footer Social Links Text',
					'title'		=> 'Post Footer Social Links Text',						
					'shortexp'	=> 'The text next to your social icons',
					'exp'		=> "Set the text next to your social links shown on single post pages or on all " . 
							 "posts pages if the post footer link is set to 'always sharing links'."
			),

			'post_footer_share_links' => array(
					'default'	=> '',
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'share_facebook'	=> array('inputlabel'=>'Facebook Sharing Icon', 'default'=> true),
						'share_twitter'		=> array('inputlabel'=>'Twitter Sharing Icon', 'default'=> true),
						'share_delicious'	=> array('inputlabel'=>'Del.icio.us Sharing Icon', 'default'=> true),
						'share_mixx'		=> array('inputlabel'=>'Mixx Sharing Icon', 'default'=> false),
						'share_reddit'		=> array('inputlabel'=>'Reddit Sharing Icon', 'default'=> true),
						'share_digg'		=> array('inputlabel'=>'Digg Sharing Icon', 'default'=> false),
						'share_stumbleupon'	=> array('inputlabel'=>'StumbleUpon Sharing Icon', 'default'=> false)
					),
					'inputlabel'	=> 'Select Which Share Links To Show',
					'title'		=> 'Post Footer Sharing Icons',						
					'shortexp'	=> 'Select Which To Show',
					'exp'		=> "Select which icons you would like to show in your post footer when sharing " . 
							 "links are shown."
		    ), 

			'excerpt_tags' => array(
					'version'	=> 'pro',
					'default' 	=> '<p><br><a>',
					'type' 		=> 'text',
					'inputlabel' 	=> 'Allowed Tags',
					'title' 	=> 'Allow Tags in Excerpt',
					'shortexp' 	=> 'Control which tags are stripped from excerpts.',
					'exp' 		=> 'By default WordPress strips all HTML tags from excerpts. You can use this option to allow certain tags. Simply enter the allowed tags in this field. <br/>An example of allowed tags could be: <strong>&lt;p&gt;&lt;br&gt;&lt;a&gt;</strong>. <br/><br/> <strong>Note:</strong> Enter a period "<strong>.</strong>" to disallow all tags.'
			),
		);
		
		return apply_filters('pagelines_options_blog_posts', $a);
		
	}
	
	/**
	 * Advanced and Misc Options
	 *
	 * @since 2.0.0
	 */
	function advanced(){

		$a = array(
			'partner_link' => array(
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> 'Enter Partner Link',
					'title'		=> 'PageLines Partner Link',
					'shortexp'	=> 'Change your PageLines footer link to a partner link',
					'exp'		=> 'If you are a <a href="http://www.pagelines.com/partners">PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.'
			),
			'google_ie' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'	=> 'Include Google IE Compatibility Script?',
					'title'		=> 'Google IE Compatibility Fix',
					'shortexp'	=> 'Include a Google JS script that fixes problems with IE.',
					'exp'		=> 'More info on this can be found here: <strong>http://code.google.com/p/ie7-js/</strong>.'
			),

			'forum_options' => array(
					'default' 	=> '',
					'type' 		=> 'check',
					'inputlabel' 	=> 'Show bbPress Forum Addon Options',
					'title' 	=> 'Activate Forum Options',
					'shortexp'	=> 'If you have integrated a PageLines bbPress forum, activate its options here.',
					'exp' 		=> 'This theme has some integrated options for its bbPress forum addon (if installed).'
			),
			'multisite_options' => array(
					'default' 	=> '',
					'version'	=> 'pro',
					'type' 		=> 'check',
					'inputlabel' 	=> 'Show Multisite Options',
					'title' 	=> 'Activate Multisite Options',
					'shortexp'	=> 'If you have multisite enabled, activate its options here.',
					'exp' 		=> ''
			),

			'disable_ajax_save' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Disable AJAX Saving?',
					'title'		=> 'Disable AJAX Saving',
					'shortexp'	=> 'Check to disable AJAX saving.',
					'exp'		=> "Check this option if you are having problems with AJAX saving. For example, if design control or typography options aren't working"
			),
			'inline_dynamic_css' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Make Dynamic CSS Inline?',
					'title'		=> 'Inline Dynamic CSS',
					'shortexp'	=> 'Makes Dynamic CSS Load Inline, Resolves Caching Issues',
					'exp'		=> "If you are having problems with layout, design control and typography, you may have caching issues on your server. Make dynamic.css 'inline' for fast relief. Note: there is a small performance issue with this."
			),

			'enable_debug' => array(
					'default' => '',
					'version'	=> 'pro',
					'type' => 'check',
					'inputlabel' => 'Enable debug settings tab?',
					'title' => 'PageLines debug',
					'shortexp' => 'Show detailed settings information.',
					'exp' => "This information can be useful in the forums if you have a problem."
			),

			'hide_introduction' => array(
					'default' => '',
					'version'	=> 'pro',
					'type' => 'check',
					'inputlabel' => 'Hide the introduction?',
					'title' => 'Show Theme Introduction',
					'shortexp' => 'Uncheck this option to show theme introduction.',
					'exp' => ""
			)	

		);
		
		return apply_filters('pagelines_options_advanced', $a);
		
	}
	
	/**
	 * Custom Coding Options
	 *
	 * @since 2.0.0
	 */
	function custom_code(){

		$a = array(
			'customcss' => array(
					'version' 	=> 'pro',
					'default' 	=> 'body{}',
					'type' 		=> 'textarea',
					'layout' 	=> 'full',
					'inputlabel' 	=> 'CSS Rules',
					'title' 	=> 'Custom CSS',
					'shortexp' 	=> 'Insert custom CSS styling here. It will be stored in the DB and not overwritten. <br/>Note: The easiest way to customize your site is using "Base" the child theme for PlatformPro.',
					'exp' 		=> '<div class="theexample">Example:<br/> <strong>body{<br/> &nbsp;&nbsp;color:  #3399CC;<br/>&nbsp;&nbsp;line-height: 20px;<br/>&nbsp;&nbsp;font-size: 11px<br/>}</strong></div>Enter CSS Rules to change the style of your site.<br/><br/> A lot can be accomplished by simply changing the default styles of the "body" tag such as "line-height", "font-size", or "color" (as in text color).', 
					'docslink'	=> 'http://www.pagelines.com/docs/changing-colors-fonts', 
					'vidtitle'	=> 'View Customization Documentation'
				),

			'headerscripts' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Headerscripts Code',
					'title'		=> 'Header Scripts',
					'shortexp'	=> 'Scripts inserted directly before the end of the HTML &lt;head&gt; tag',
					'exp'		=> ''
				),
			'footerscripts' => array(
					'default'	=> '',						
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Footerscripts Code or Analytics',
					'title'		=> 'Footer Scripts &amp; Analytics',
					'shortexp'	=> 'Any footer scripts including Google Analytics',
					'exp'		=> ''
				), 
			'asynch_analytics' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Asynchronous Analytics',
					'title'		=> 'Asynchronous Analytics',
					'shortexp'	=> 'Placeholder for Google asynchronous analytics. Goes underneath "body" tag.',
					'exp'		=> ''
			),
		);
		
		return apply_filters('pagelines_options_custom_code', $a);
		
	}
	
	/**
	 * Forum Related Options
	 *
	 * @since 2.0.0
	 */
	function forum_options(){

		$a = array(
			'forum_tags'		=> array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'	=> 'Show tags in sidebar?',
				'title'		=> 'Tag Cloud In Sidebar',
				'shortexp'	=> 'Including post tags on the forum sidebar.',
				'exp'		=> 'Tags are added by users and moderators on your forum and can help people locate posts.'
			),
			'forum_image_1'		=> array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'inputlabel'	=> 'Upload Forum Image',
				'imagepreview'	=> 125,
				'title'		=> 'Forum Sidebar Image #1',
				'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
				'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
			),
			'forum_image_link_1' => array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'	=> 'Image Link URL',
				'title'		=> 'Forum Image #1 Link',
				'shortexp'	=> 'Full URL for your forum image.',
				'exp'		=> "Add the full url for your forum image."
			),
			'forum_image_2' => array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'imagepreview'	=> 125,
				'inputlabel'	=> 'Upload Forum Image',
				'title'		=> 'Forum Sidebar Image #2',
				'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
				'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
			),
			'forum_image_link_2'	=> array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'	=> 'Image Link URL',
				'title'		=> 'Forum Image #2 Link',
				'shortexp'	=> 'Full URL for your forum image.',
				'exp'		=> "Add the full url for your forum image."
			),
			'forum_sidebar_link'	=> array(
				'default'	=> '#',
				'type'		=> 'text',
				'inputlabel'	=> 'Forum Image Caption URL',
				'title'		=> 'Forum Caption Link URL (Text Link)',
				'shortexp'	=> 'Add the URL for your forum caption (optional)',
				'exp'		=> "Text link underneath your forum images."
			),
			'forum_sidebar_link_text' => array(
				'default'	=> 'About '.get_bloginfo('name'),
				'type'		=> 'text',
				'inputlabel'	=> 'Forum Sidebar Link Text',
				'title'		=> 'Forum Sidebar Link Text',
				'shortexp'	=> 'The text of your image caption link',
				'exp'		=> "Change the text of the caption placed under your forum images."
			)
	
		);
		
		return apply_filters('pagelines_options_forum_options', $a);
		
	}
	
	/**
	 * Welcome Message
	 *
	 * @since 2.0.0
	 */
	function welcome(){

		$a = array(
			'theme_introduction'	=> array(
				'type'		=> 'text_content',
				'layout'	=> 'full',
				'exp'		=> get_theme_intro()
			),
			'hide_introduction'	=> array(
				'default'	=> '',
				'type'		=> 'check',
				'inputlabel'	=> '',
				'inputlabel'	=> 'Hide the introduction',
				'title'		=> 'Remove This Theme Introduction',
				'shortexp'	=> 'Remove this introduction from the admin.',
				'exp'		=> "This introduction can be added back under the 'custom code' tab (once hidden)..."
			),
		);
		
		return apply_filters('pagelines_options_welcome', $a);
		
	}
	
	/**
	 * Plugged In Section Options
	 *
	 * @since 2.0.0
	 */
	function section_options(){

		$a = array(	);
		
		return apply_filters('pagelines_options_section_options', $a);
		
	}
	
	/**
	 * Custom Options (Deprecated)
	 *
	 * @since 2.0.0
	 */
	function custom_options(){

		$a = array(	);
		
		return apply_filters('pagelines_custom_options', $a);
		
	}
	
	
}


/**
 * 
 *
 *  Returns Options Array
 *
 *
 *
 */
function get_option_array( $load_unavailable = false ){
	
	
	$default = new PageLinesOptionsArray();
	 
	$optionarray =  array_merge(load_section_options('new', 'top', $load_unavailable), $default->options, load_section_options('new', 'bottom', $load_unavailable), $default->last_options);
	
	if(isset($custom_options['custom_options']) && !empty($custom_options['custom_options']))
		$optionarray = array_merge($optionarray, $custom_options);
	
	foreach($optionarray as $optionset => $options)
		$optionarray[$optionset] = array_merge( load_section_options($optionset, 'top', $load_unavailable), $options, load_section_options($optionset, 'bottom', $load_unavailable));
	
	return apply_filters('pagelines_options_array', $optionarray); 
}

/**
 * 
 *
 *  Returns Welcome
 *
 *
 *
 */
function get_theme_intro(){
		
	$intro = '<div class="theme_intro"><div class="admin_billboard fix"><div class="admin_billboard_pad fix"><div class="admin_theme_screenshot"><img class="" src="'.PARENT_URL.'/screenshot.png" alt="Screenshot" /></div>' .
		'<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">Congratulations!</h3></div>'.
		'<div class="admin_billboard_text">You are ready to start building an awesome website. PageLines has built you tons of customization options and editing features. Here are a few tips to get you started...<br/><small>(Note: This intro can be removed below.)</small></div>'.
		'</div></div></div>'.
		'<ul class="admin_feature_list">'.
		'<li class="feature_firstrule"><div class="feature_icon"></div><strong>The First Rule</strong> <p>If you are a new customer of PageLines, it\'s time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.</p></li> ' .
		'<li class="feature_support"><div class="feature_icon"></div><strong>Support</strong> <p>For help getting started, we offer our customers tons of support including <a href="http://www.pagelines.com/docs/" target="_blank">docs</a>, <a href="http://www.youtube.com/pagelines" target="_blank">video tutorials</a>, and the <a href="http://www.pagelines.com/forum/" target="_blank">forum</a>, where users can post questions if they can\'t find the info they need.<br/> You can also visit our <a href="http://www.pagelines.com/support/" target="_blank">support page</a> for more info.</p></li> ' .
		'<li class="feature_templates"><div class="feature_icon"></div><strong>Drag and Drop Template Setup</strong> <p>'.THEMENAME.' is equipped with advanced template customization tools. This is how you will control elements like feature sliders, or carousels using drag and drop on your site.</p></p> <p>To learn how it works, please visit the <a href="http://www.pagelines.com/docs/template-setup" target="_blank">template setup</a> page in the docs. </p></li>' .
		'<li class="feature_options"><div class="feature_icon"></div><strong>Settings &amp; Setup</strong> <p>This panel is where you will start the customization of your website. Any options applied through this interface will make changes site-wide.</p><p> There are also several more options that you will find on the bottom of each WordPress page and post interface where you create and edit content. These allow you to set options specifically related to that page or post.</p><p><small>Note: create and save the page or post before setting these options.</small></p></li>' .
		'<li class="feature_plugins"><div class="feature_icon"></div><strong>Install Plugins</strong> <p>Although '.THEMENAME.' is universally plugin compatible, we have added "advanced" graphical/functional support for several WordPress plugins.</p><p> It\'s your responsibility to install and activate each plugin, which can be done through "<strong>plugins</strong>" &gt; "<strong>Add New</strong>" or through the <strong>developers site</strong> where you can download them manually (e.g. CForms).</p><p>Pre-configured plugins:</p>'.
		'<ul>'.
		'<li class="first"><p><a href="http://buddypress.org/" target="_blank">BuddyPress</a> &amp; <a href="http://wordpress.org/extend/plugins/bp-template-pack/" target="_blank">BuddyPress Template Pack</a> - Social networking for your WordPress site.</p></li>'.
		'<li class=""><p><a href="http://bbpress.org/" target="_blank">bbPress Forum</a> - Matching forum theme for bbPress (Developer Edition Only)</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/twitter-for-wordpress/" target="_blank">Twitter For WordPress</a> - Latest Twitter Post and Twitter Post Widgets</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/disqus-comment-system/" target="_blank">Disqus</a> or <a href="http://wordpress.org/extend/plugins/facebook-comments-for-wordpress/" target="_blank">Facebook Comments</a> - Improve your commenting system.</p></li>'.
		'<li class="first"><p><a href="http://wordpress.org/extend/plugins/post-types-order/" target="_blank">Post Types Order</a> - Allows you to re-order custom post types like features and boxes.</p></li>'.
		'<li><p><a href="http://www.deliciousdays.com/cforms-plugin/" target="_blank">CFormsII</a> - Advanced contact forms that can be used for creating mailing lists, etc..</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/wp125/" target="_blank">WP125</a> - Used to show 125px by 125px ads or images in your sidebar. (Widget)</p></li>'.
		'<li><p><a href="http://eightface.com/wordpress/flickrrss/" target="_blank">FlickrRSS</a> - Shows pictures from your Flickr Account.  (Widget &amp; Carousel Section)</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/nextgen-gallery/" target="_blank">NextGen-Gallery</a> - Allows you to create image galleries with special effects.  (Carousel Section)</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/wp-pagenavi/" target="_blank">Wp-PageNavi</a> - Creates advanced "paginated" post navigation..</p></li>'.
		'<li><p><a href="http://wordpress.org/extend/plugins/breadcrumb-navxt/" target="_blank">Breadcrumb NavXT</a> - Displays a configurable breadcrumb nav on your site</p></li>'.
		'</ul>'.
		'<li class="feature_dynamic"><div class="feature_icon"></div><strong>Widgets and Dynamic Layout</strong> <p>To make it super easy to customize your layout, we have added tons of sidebars and widget areas.  You can find and set these up under "<strong>appearance</strong>" &gt; "<strong>widgets</strong>"</p> <p>Find more information about your widget areas in the <a href="http://www.pagelines.com/docs">docs</a>. </p></li>' .
		'</ul>' .
		'<br/><h3>That\'s it for now! Have fun and good luck.</h3></div>';

		return apply_filters('pagelines_theme_intro', $intro);
}