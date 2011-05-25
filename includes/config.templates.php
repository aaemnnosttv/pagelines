<?php

/*
	TEMPLATES CONFIG
	
	This file is used to configure template defaults and 
	to set up the HTML/JS sections that will be used in the site. 
	Platform themes should add sections through the appropriate hooks
	
*/


/**
 * 
 *  Register HTML sections for use in the theme.
 *
 *  @package Platform
 *  @subpackage Config
 *  @since 1.4.0
 *
 */
function pagelines_register_sections(){
	
// Common WP 
	pagelines_register_section('PageLinesContent', 'wp', 'content');
	pagelines_register_section('PageLinesPostLoop', 'wp', 'postloop');
	pagelines_register_section('PageLinesPostNav', 'wp', 'postnav');
	pagelines_register_section('PageLinesComments', 'wp', 'comments');
	pagelines_register_section('PageLinesPagination', 'wp', 'pagination');
	pagelines_register_section('PageLinesShareBar', 'wp', 'sharebar');
	pagelines_register_section('PageLinesNoPosts', 'wp', 'noposts');
	pagelines_register_section('PageLinesPostAuthor', 'wp', 'postauthor');
	pagelines_register_section('PageLinesPostsInfo', 'wp', 'postsinfo');
	
// In Header
	pagelines_register_section('PageLinesNav', 'nav');
	pagelines_register_section('PageLinesSecondNav', 'secondnav');
	pagelines_register_section('PageLinesBranding', 'wp', 'branding');	
	pagelines_register_section('BrandNav', 'brandnav', 'brandnav', array('deps'=>'PageLinesNav') );	
	pagelines_register_section('PageLinesBreadcrumb', 'breadcrumb');

// Sections With Custom Post Types
	pagelines_register_section('PageLinesFeatures', 'features'); // 'features'
	pagelines_register_section('PageLinesBoxes', 'boxes'); // 'boxes'
	pagelines_register_section('PageLinesBanners', 'banners'); // 'boxes'

// Sidebar Sections & Widgets
	pagelines_register_section('PrimarySidebar', 'sidebars', 'sb_primary');
	pagelines_register_section('SecondarySidebar', 'sidebars', 'sb_secondary');
	pagelines_register_section('TertiarySidebar', 'sidebars', 'sb_tertiary');
	pagelines_register_section('UniversalSidebar', 'sidebars', 'sb_universal');
	
	pagelines_register_section('FullWidthSidebar', 'sidebars', 'sb_fullwidth');
	pagelines_register_section('ContentSidebar', 'sidebars', 'sb_content');
	
	pagelines_register_section('PageLinesMorefoot', 'sidebars', 'morefoot');
	pagelines_register_section('PageLinesFootCols', 'sidebars', 'footcols');
	
// Misc & Dependent Sections

	pagelines_register_section('PageLinesSoapbox', 'soapbox');
	pagelines_register_section('PageLinesCarousel', 'carousel');
	pagelines_register_section('PageLinesHighlight', 'highlight');
	pagelines_register_section('PageLinesTwitterBar', 'twitterbar');
	pagelines_register_section('PageLinesSimpleFooterNav', 'footer_nav');

	pagelines_register_section('PageLinesCallout','callout');


// Do a hook for registering sections
	pagelines_register_hook('pagelines_register_sections'); // Hook

}

/*
	TEMPLATE MAP
	
	This array controls the default template map of section in the theme
	Each top level needs a hook; and the top-level template needs to be included 
	as an arg in said hook...
*/
function the_template_map() {
	
	$template_map = array();
	
	$page_templates = pagelines_get_page_templates();
	$content_templates = pagelines_get_content_templates();
	
	$template_map['header'] = array(
		'hook' 			=> 'pagelines_header', 
		'name'			=> 'Site Header',
		'markup'		=> 'content', 
		'sections' 		=> array( 'PageLinesBranding' , 'PageLinesNav', 'PageLinesSecondNav' )
	);
	
	$template_map['footer'] = array(
		'hook' 			=> 'pagelines_footer', 
		'name'			=> 'Site Footer', 
		'markup'		=> 'content', 
		'sections' 		=> array('PageLinesFootCols')
	);
	
	$template_map['templates'] = array(
		'hook'			=> 'pagelines_template', 
		'name'			=> 'Page Templates', 
		'markup'		=> 'content', 
		'templates'		=> $page_templates,
	);
	
	$template_map['main'] = array(
		'hook'			=> 'pagelines_main', 
		'name'			=> 'Text Content Area',
		'markup'		=> 'copy', 
		'templates'		=> $content_templates,
	);
	
	$template_map['morefoot'] = array(
		'name'			=> 'Morefoot Area',
		'hook' 			=> 'pagelines_morefoot',
		'markup'		=> 'content', 
		'version'		=> 'pro',
		'sections' 		=> array('PageLinesMorefoot', 'PageLinesTwitterBar')
	);
	
	$template_map['sidebar1'] = array(
		'name'			=> 'Sidebar 1',
		'hook' 			=> 'pagelines_sidebar1',
		'markup'		=> 'copy', 
		'sections' 		=> array('PrimarySidebar')
	);
	
	$template_map['sidebar2'] = array(
		'name'			=> 'Sidebar 2',
		'hook' 			=> 'pagelines_sidebar2',
		'markup'		=> 'copy', 
		'sections' 		=> array('SecondarySidebar')
	);
	
	$template_map['sidebar_wrap'] = array(
		'name'			=> 'Sidebar Wrap',
		'hook' 			=> 'pagelines_sidebar_wrap',
		'markup'		=> 'copy', 
		'version'		=> 'pro',
		'sections' 		=> array()
	);
	
	return apply_filters('pagelines_template_map', $template_map); 
}

function pagelines_get_page_templates(){
	
	$page_templates = array(
		'default' => array(
				'name'			=> 'Default Page',
				'sections' 		=> array('PageLinesContent')
		),
		'posts' => array(
				'name'			=> 'Posts Pages',
				'sections' 		=> array('PageLinesContent')
			),
		"404" => array(
				'name'			=> '404 Error Page',
				'sections' 		=> array( 'PageLinesNoPosts' ),
			),
		'single' => array(
				'name'			=> 'Single Post Page',
				'sections' 		=> array('PageLinesContent')
			),
		'alpha' => array(
				'name'			=> 'Feature Page',
				'sections' 		=> array('PageLinesFeatures', 'PageLinesBoxes', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'beta' => 	array(
				'name'			=> 'Carousel Page',
				'sections' 		=> array('PageLinesCarousel', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'gamma' => 	array(
				'name'			=> 'Box Page',
				'sections' 		=> array( 'PageLinesHighlight', 'PageLinesSoapbox', 'PageLinesBoxes' ),
				'version'		=> 'pro'
			),
		'delta' => 	array(
				'name'			=> 'Highlight Page',
				'sections' 		=> array( 'PageLinesHighlight', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		'epsilon' => 	array(
				'name'			=> 'Banner Page',
				'sections' 		=> array( 'PageLinesHighlight', 'PageLinesBanners', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		
	);

	return apply_filters('pagelines_page_template_array', $page_templates); 
	
}

function pagelines_get_content_templates(){
	$content_templates = array(
		'default' => array(
				'name'			=> 'Page Content Area',
				'sections' 		=> array('PageLinesPostLoop', 'PageLinesComments')
			),
		'posts' => array(
				'name'			=> 'Posts Page Content Area',
				'sections' 		=> array('PageLinesPostsInfo','PageLinesPostLoop', 'PageLinesPagination')
			),

		'single' => array(
				'name'			=> 'Single Post Content Area',
				'sections' 		=> array('PageLinesPostNav', 'PageLinesPostLoop', 'PageLinesShareBar', 'PageLinesComments', 'PageLinesPagination')
			)
	);
	
	return apply_filters('pagelines_content_template_array', $content_templates); 
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


function get_default_fboxes(){
	$default_boxes[] = array(
	        				'title' => 'Drag&amp;Drop Design',
			        		'text' 	=> 'In voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur occaecat cupidatat non proident, in culpas officia deserunt.',
							'media' => PL_IMAGES.'/fbox3.png'
	    				);

	$default_boxes[] = array(
	        				'title' => 'PageLines Framework',
			        		'text' 	=> 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in.',
							'media' => PL_IMAGES.'/fbox2.png'
	    				);

	$default_boxes[] = array(
	        				'title'	=> 'Rock The Web!',
	        				'text' 	=> 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim.', 
							'media' => PL_IMAGES.'/fbox1.png'
	    				);
	
	return apply_filters('pagelines_default_boxes', $default_boxes);
}

function get_default_banners(){
	return apply_filters('pagelines_default_banners', array());
}


/*
	Theme Introduction 
*/

function get_theme_intro(){
		
	$intro = '<div class="admin_billboard fix"><div class="admin_theme_screenshot"><img class="" src="'.PARENT_URL.'/screenshot.png" alt="Screenshot" /></div>' .
		'<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">Welcome to '.THEMENAME.'!</h3><h5 class="admin_header_sub">Pro Web Software From PageLines</h5></div>'.
		'<div class="admin_billboard_text">Your framework now has tons of customization options and editing features.<br/> Here are a few tips to get you started...<br/><small>(Note: This intro can be removed below.)</small></div>'.
		'</div></div>'.
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
		'<br/><h3>That\'s it for now! Have fun and good luck.</h3>';

		return apply_filters('pagelines_theme_intro', $intro);
}


				

