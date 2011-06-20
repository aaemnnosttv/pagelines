<?php

/*
	TEMPLATES CONFIG
	
	This file is used to configure template defaults and 
	to set up the HTML/JS sections that will be used in the site. 
	Platform themes should add sections through the appropriate hooks
	
*/




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
		'tag' => array(
				'name'			=> 'Tags Page',
				'sections' 		=> array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'archive' => 	array(
				'name'			=> 'Archive Page',
				'sections' 		=> array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'category' => 	array(
				'name'			=> 'Category Page',
				'sections' 		=> array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'search' => 	array(
				'name'			=> 'Search Results Page',
				'sections' 		=> array('PageLinesContent'),
				'version'		=> 'pro'
			)
		
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
			), 
		'tag' => array(
				'name'			=> 'Tags Page',
				'sections' 		=> array('PageLinesPostLoop'),
				'version'		=> 'pro'
			),
		'archive' => 	array(
				'name'			=> 'Archive Page',
				'sections' 		=> array('PageLinesPostLoop'),
				'version'		=> 'pro'
			),
		'category' => 	array(
				'name'			=> 'Category Page',
				'sections' 		=> array('PageLinesPostLoop'),
				'version'		=> 'pro'
			),
		'search' => 	array(
				'name'			=> 'Search Results Page',
				'sections' 		=> array('PageLinesPostLoop'),
				'version'		=> 'pro'
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





				

