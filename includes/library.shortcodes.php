<?php

/**
 * This file defines return functions to be used as shortcodes by users and developers
 * 
 * @example <code>[post_something]</code>
 * @example <code>[post_something before="<b>" after="</b>" foo="bar"]</code>
 */


/**
 * Used to create general buttons and button links
 * 
 * @example <code>[button]</code> is the default usage
 * @example <code>[button format="edit_post" before="<b>" after="</b>"]</code>
 */
add_shortcode('button', 'pagelines_button_shortcode');
function pagelines_button_shortcode($atts) {
	
	$defaults = array(
		'color'	=> 'grey', 
		'size'	=> 'normal',
		'align'	=> 'right', 
		'style'	=> '',
		'type'	=> 'button', 
		'text'	=> '&nbsp;',
		'pid'	=> 0, 
		'class'	=> null, 
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$button = sprintf( '<div class="blink"><div class="blink-pad">%s</div></div>', $text);
	
	$output = sprintf('<div class="%s %s %s blink-wrap">%s</div>', $special, $size, $color, $button);
	
	return apply_filters('pagelines_button_shortcode', $output, $atts);
	
}


/**
 * This function produces the date of post publication
 * 
 * @example <code>[post_date]</code> is the default usage
 * @example <code>[post_date format="F j, Y" before="<b>" after="</b>"]</code>
 */
add_shortcode('post_date', 'pagelines_post_date_shortcode');
function pagelines_post_date_shortcode($atts) {
	
	$defaults = array(
		'format' => get_option('date_format'),
		'before' => '',
		'after' => '',
		'label' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$output = sprintf( '<time class="date time published updated sc" datetime="%5$s" pubdate="pubdate">%1$s%3$s%4$s%2$s</time> ', $atts['before'], $atts['after'], $atts['label'], get_the_time($atts['format']), get_the_time('Y-m-d\TH:i:sO') );
	
	return apply_filters('pagelines_post_date_shortcode', $output, $atts);
	
}

/**
 * This function produces the time of post publication
 * 
 * @example <code>[post_time]</code> is the default usage
 * @example <code>[post_time format="g:i a" before="<b>" after="</b>"]</code>
 */
add_shortcode('post_time', 'pagelines_post_time_shortcode');
function pagelines_post_time_shortcode($atts) {
	
	$defaults = array( 
		'format' => get_option('time_format'),
		'before' => '',
		'after' => '',
		'label' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$output = sprintf( '<span class="time published sc" title="%5$s">%1$s%3$s%4$s%2$s</span> ', $atts['before'], $atts['after'], $atts['label'], get_the_time($atts['format']), get_the_time('Y-m-d\TH:i:sO') );
	
	return apply_filters('pagelines_post_time_shortcode', $output, $atts);
	
}

/**
 * This function produces the author of the post (display name)
 * 
 * @example <code>[post_author]</code> is the default usage
 * @example <code>[post_author before="<b>" after="</b>"]</code>
 */
add_shortcode('post_author', 'pagelines_post_author_shortcode');
function pagelines_post_author_shortcode($atts) {
	
	$defaults = array(
		'before' => '',
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$output = sprintf('<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', esc_html( get_the_author() ), $atts['before'], $atts['after']);
	
	return apply_filters('pagelines_post_author_shortcode', $output, $atts);
	
}

/**
 * This function produces the author of the post (link to author URL)
 * 
 * @example <code>[post_author_link]</code> is the default usage
 * @example <code>[post_author_link before="<b>" after="</b>"]</code>
 */
add_shortcode('post_author_link', 'pagelines_post_author_link_shortcode');
function pagelines_post_author_link_shortcode($atts) {
	
	$defaults = array(
		'nofollow' => FALSE,
		'before' => '',
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$author = get_the_author();
	
	//	Link?
	if ( get_the_author_meta('url') ) {
		
		//	Build the link
		$author = '<a href="' . get_the_author_meta('url') . '" title="' . esc_attr( sprintf(__('Visit %s&#8217;s website', 'pagelines'), $author) ) . '" rel="external">' . $author . '</a>';
		
	}
	
	$output = sprintf('<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after']);
	
	return apply_filters('pagelines_post_author_link_shortcode', $output, $atts);
	
}

/**
 * This function produces the author of the post (link to author archive)
 * 
 * @example <code>[post_author_posts_link]</code> is the default usage
 * @example <code>[post_author_posts_link before="<b>" after="</b>"]</code>
 */
add_shortcode('post_author_posts_link', 'pagelines_post_author_posts_link_shortcode');
function pagelines_post_author_posts_link_shortcode($atts) {
	
	$defaults = array(
		'before' => '',
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	// Prevent automatic WP Output
	ob_start();
	the_author_posts_link();
	$author = ob_get_clean();
	
	$output = sprintf('<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', $author, $atts['before'], $atts['after']);
	
	return apply_filters('pagelines_post_author_shortcode', $output, $atts);
	
}

/**
 * This function produces the comment link
 * 
 * @example <code>[post_comments]</code> is the default usage
 * @example <code>[post_comments zero="No Comments" one="1 Comment" more="% Comments"]</code>
 */
add_shortcode('post_comments', 'pagelines_post_comments_shortcode');
function pagelines_post_comments_shortcode($atts) {
	
	$defaults = array(
		'zero' => __('Leave a Comment', 'pagelines'),
		'one' => __("<span class='num'>1</span> Comment", 'pagelines'),
		'more' => __("<span class='num'>%</span> Comments", 'pagelines'),
		'hide_if_off' => 'disabled',
		'before' => '',
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	if ( ( !comments_open() ) && $atts['hide_if_off'] === 'enabled' )
		return;
	
	// Prevent automatic WP Output
	ob_start();
	comments_number($atts['zero'], $atts['one'], $atts['more']);
	$comments = ob_get_clean();
	
	$comments = sprintf('<a href="%s">%s</a>', get_comments_link(), $comments);

	$output = sprintf('<span class="post-comments sc">%2$s%1$s%3$s</span>', $comments, $atts['before'], $atts['after']);
	
	return apply_filters('pagelines_post_comments_shortcode', $output, $atts);
	
}

/**
 * This function produces the tag link list
 * 
 * @example <code>[post_tags]</code> is the default usage
 * @example <code>[post_tags sep=", " before="Tags: " after="bar"]</code>
 */
add_shortcode('post_tags', 'pagelines_post_tags_shortcode');
function pagelines_post_tags_shortcode($atts) {
	
	$defaults = array(
		'sep' => ', ',
		'before' => __('Tagged With: ', 'pagelines'),
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$tags = get_the_tag_list( $atts['before'], trim($atts['sep']) . ' ', $atts['after'] );
	
	if ( !$tags ) return;
	
	$output = sprintf('<span class="tags sc">%s</span> ', $tags);
	
	return apply_filters('pagelines_post_tags_shortcode', $output, $atts);
	
}

/**
 * This function produces the category link list
 * 
 * @example <code>[post_categories]</code> is the default usage
 * @example <code>[post_categories sep=", "]</code>
 */
add_shortcode('post_categories', 'pagelines_post_categories_shortcode');
function pagelines_post_categories_shortcode($atts) {
	
	$defaults = array(
		'sep' => ', ',
		'before' => '',
		'after' => ''
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	$cats = get_the_category_list( trim($atts['sep']) . ' ' );
	
	$output = sprintf('<span class="categories sc">%2$s%1$s%3$s</span> ', $cats, $atts['before'], $atts['after']);
	
	return apply_filters('pagelines_post_categories_shortcode', $output, $atts);
	
}

/**
 * This function produces the edit post link for logged in users
 * 
 * @example <code>[post_edit]</code> is the default usage
 * @example <code>[post_edit link="Edit", before="<b>" after="</b>"]</code>
 */
add_shortcode('post_edit', 'pagelines_post_edit_shortcode');
function pagelines_post_edit_shortcode($atts) {
	
	$defaults = array(
		'link' => __("<span class='editpage sc'>Edit</span>", 'pagelines'),
		'before' => '[',
		'after' => ']'
	);
	$atts = shortcode_atts( $defaults, $atts );
	
	// Prevent automatic WP Output
	ob_start();
	edit_post_link($atts['link'], $atts['before'], $atts['after']); // if logged in
	$edit = ob_get_clean();
	
	$output = $edit;
	
	return apply_filters('pagelines_post_edit_shortcode', $output, $atts);
	
}




// Make widgets process shortcodes
add_filter('widget_text', 'do_shortcode');
// LAYOUT //////////////////////////////////////////////////

	//Created a container for dynamic html layout
	// USAGE: [container id="mycontainer" class="myclass"] 'cboxes' see shortcode below [/container]
	
		function dynamic_container($atts, $content = null ) {

		 	//extract page name from the shortcode attributes
		 	extract(shortcode_atts(array( 'id' => 'container', 'class' => ''), $atts));

			//$content = remove_filter($content, 'wptexturize');
		
		 		$container = '<div style="width: 100%;" class="container">'.do_shortcode($content).'<div class="clear"></div></div>';

		 	return $container;
		}
		add_shortcode('container', 'dynamic_container');

	//Created a container for dynamic html layout
	// USAGE: [cbox width="50%" leftgutter="15px" rightgutter="0px"] html box content[/cbox]
	
		function dynamic_box($atts, $content = null ) {

		 	//extract page name from the shortcode attributes
		 	extract(shortcode_atts(array( 'width' => '30%', 'leftgutter' => '10px', 'rightgutter' => '0px'), $atts));

		 	$cbox = '<div class="cbox" style="float:left;width:'.$width.';"><div class="cbox_pad" style="margin: 0px '.$rightgutter.' 0px '.$leftgutter.'">'.do_shortcode($content).'</div></div>';
	 	
		return $cbox;
		}
		add_shortcode('cbox', 'dynamic_box');
	
	
	
// GET POST FIELD BY OFFSET //////////////////////////////////////////////////

	// Get a post based on offset from the last post published (0 for last post)
	// USAGE: [postfeed field="post_title"  offset="0" customfield="true" ]
		
			function get_postfeed($atts) {

			 	//extract page name from the shortcode attributes
			 	extract(shortcode_atts(array( 'field' => 'post_title', 'offset' => '0', 'customfield' => ""), $atts));
			
				//returns an array of objects
				$thepost = get_posts('numberposts=1&offset='.$offset);
				
				if($customfield == 'true'){
					$postfield = get_post_meta($thepost[0]->ID, $field, true);
				}else{
			 		$postfield = $thepost[0]->$field;
				}
				return $postfield;
			}
			add_shortcode('postfeed', 'get_postfeed');
	
	
	
	
// GOOGLE CHARTS  //////////////////////////////////////////////////

	// Gets Google charts
	// USAGE 
	//		[chart data="0,12,24,26,32,64,54,24,22,20,8,2,0,0,3" bg="F7F9FA" size="200x100" type="sparkline"]
	//		[chart data="41.52,37.79,20.67,0.03" bg="F7F9FA" labels="Reffering+sites|Search+Engines|Direct+traffic|Other" colors="058DC7,50B432,ED561B,EDEF00" size="488x200" title="Traffic Sources" type="pie"]

		function chart_shortcode( $atts ) {
			extract(shortcode_atts(array(
			    'data' => '',
			    'colors' => '',
			    'size' => '400x200',
			    'bg' => 'ffffff',
			    'title' => '',
			    'labels' => '',
			    'advanced' => '',
			    'type' => 'pie'
			), $atts));
 
			switch ($type) {
				case 'line' :
					$charttype = 'lc'; break;
				case 'xyline' :
					$charttype = 'lxy'; break;
				case 'sparkline' :
					$charttype = 'ls'; break;
				case 'meter' :
					$charttype = 'gom'; break;
				case 'scatter' :
					$charttype = 's'; break;
				case 'venn' :
					$charttype = 'v'; break;
				case 'pie' :
					$charttype = 'p3'; break;
				case 'pie2d' :
					$charttype = 'p'; break;
				default :
					$charttype = $type;
				break;
			}
			$string = '';
			if ($title) $string .= '&chtt='.$title.'';
			if ($labels) $string .= '&chl='.$labels.'';
			if ($colors) $string .= '&chco='.$colors.'';
			$string .= '&chs='.$size.'';
			$string .= '&chd=t:'.$data.'';
			$string .= '&chf='.$bg.'';
 
			return '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$charttype.''.$string.$advanced.'" alt="'.$title.'" />';
		}
		add_shortcode('chart', 'chart_shortcode');

// GOOGLE MAPS //////////////////////////////////////////////////

    // you can use the default width and height
    // The only requirement is to add the address of the map
    // Example:
    // [googlemap address="san diego, ca"]
    // or with options
    // [googlemap width="200" height="200" address="San Francisco, CA 92109"]

    function googleMaps($atts, $content = null) {
       extract(shortcode_atts(array(
          "width"       =>  '480',
          "height"      =>  '480',
          "address"   =>   ''
       ), $atts));
       $src = "http://maps.google.com/maps?f=q&source=s_q&hl=en&q=".$address;
       return '<iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe>';
    }

    add_shortcode("googlemap", "googleMaps");

// CONSTANTS 
	//Function for getting template path
	// USAGE: [themeurl]
	
	function get_themeurl($atts){ return get_template_directory_uri();	 }
	add_shortcode('themeurl', 'get_themeurl');	
	
// LINKS IN POSTS AND PAGES

	// Function for creating a link from a page name
	// USAGE : [link pagename="My Example Page" linktext="Link Text"]

	function create_pagelink($atts) {

	 	//extract page name from the shortcode attributes
	 	extract(shortcode_atts(array( 'pagename' => 'home', 'linktext' => ''), $atts));

	 	//convert the page name to a page ID
	 	$page = get_page_by_title($pagename);

	 	//use page ID to get the permalink for the page
	 	$link = get_permalink($page);

	 	//create the link and output
	 	$pagelink = "<a href=\"".$link."\">".$linktext."</a>";

	 	return $pagelink;
	}
	add_shortcode('link', 'create_pagelink');


	// Return link in page based on Bookmark
	// USAGE : [bookmark id="21" text="Link Text"]

	function bookmark_link($atts) {

	 	//extract page name from the shortcode attributes
	 	extract(shortcode_atts(array( 'id' => '0', 'text' => ''), $atts));

	 	//convert the page name to a page ID
	 	$bookmark = get_bookmark($id);
	
		if(isset($text)) $ltext = $text;
		else $ltext = $bookmark->link_name;; 


		$pagelink = "<a href=\"".$bookmark->link_url."\" target=\"".$bookmark->link_target."\">".$ltext."</a>";
	 	return $pagelink;
	}
	add_shortcode('bookmark', 'bookmark_link');

