<?php



class PageLines_ShortCodes {
	
	
	function __construct() {
				
		self::register_shortcodes( $this->shortcodes_core() );
		
		// Make widgets process shortcodes
		add_filter('widget_text', 'do_shortcode');				
	}
	
	private function register_shortcodes( $shortcodes ) {
		
		foreach ( $shortcodes as $shortcode => $function ) {
			
			add_shortcode( $shortcode, array( &$this, $function ) );
		}	
	}

	private function shortcodes_core() {
		
		$core = array( 
			
			'button'					=>	'pagelines_button_shortcode',
			'post_time'					=>	'pagelines_post_time_shortcode',
			'pl_button'					=>	'pl_button_shortcode',
			'pl_blockquote'				=>	'pl_blockquote_shortcode',
			'pl_alertbox'				=>	'pl_alertbox_shortcode',
			'show_authors'				=>	'show_multiple_authors',
			'like_button'				=>	'pl_facebook_shortcode',
			'tweet_button'				=>	'pl_twitter_button',
			'pinterest_button'			=>	'pl_pinterest_button',
			'post_date'					=>	'pagelines_post_date_shortcode',
			'post_author'				=>	'pagelines_post_author_shortcode',
			'post_author_link'			=>	'pagelines_post_author_link_shortcode',
			'post_author_posts_link'	=>	'pagelines_post_author_posts_link_shortcode',
			'post_comments'				=>	'pagelines_post_comments_shortcode',
			'post_tags'					=>	'pagelines_post_tags_shortcode',
			'post_categories'			=>	'pagelines_post_categories_shortcode',
			'post_edit'					=>	'pagelines_post_edit_shortcode',
			'container'					=>	'dynamic_container',
			'cbox'						=>	'dynamic_box',
			'post_feed'					=>	'get_postfeed',
			'chart'						=>	'chart_shortcode',
			'googlemap'					=>	'googleMaps',
			'themeurl'					=>	'get_themeurl',
			'link'						=>	'create_pagelink',
			'bookmark'					=>	'bookmark_link'
			);
		
		return $core;
	}




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

	//Function for getting template path
	// USAGE: [themeurl]
	function get_themeurl($atts){ return get_template_directory_uri();	 }
	
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
	
	//Created a container for dynamic html layout
	// USAGE: [cbox width="50%" leftgutter="15px" rightgutter="0px"] html box content[/cbox]
	function dynamic_box($atts, $content = null ) {

	 	//extract page name from the shortcode attributes
	 	extract(shortcode_atts(array( 'width' => '30%', 'leftgutter' => '10px', 'rightgutter' => '0px'), $atts));

	 	$cbox = '<div class="cbox" style="float:left;width:'.$width.';"><div class="cbox_pad" style="margin: 0px '.$rightgutter.' 0px '.$leftgutter.'">'.do_shortcode($content).'</div></div>';
 	
	return $cbox;
	}

	//Created a container for dynamic html layout
	// USAGE: [container id="mycontainer" class="myclass"] 'cboxes' see shortcode below [/container]
	function dynamic_container($atts, $content = null ) {

	 	//extract page name from the shortcode attributes
	 	extract(shortcode_atts(array( 'id' => 'container', 'class' => ''), $atts));
	
 		$container = '<div style="width: 100%;" class="container">'.do_shortcode($content).'<div class="clear"></div></div>';

	 	return $container;
	}
	
	/**
	 * This function produces the edit post link for logged in users
	 * 
	 * @example <code>[post_edit]</code> is the default usage
	 * @example <code>[post_edit link="Edit", before="<b>" after="</b>"]</code>
	 */
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
	
	/**
	 * This function produces the category link list
	 * 
	 * @example <code>[post_categories]</code> is the default usage
	 * @example <code>[post_categories sep=", "]</code>
	 */
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
	 * This function produces the tag link list
	 * 
	 * @example <code>[post_tags]</code> is the default usage
	 * @example <code>[post_tags sep=", " before="Tags: " after="bar"]</code>
	 */
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
	 * This function produces the comment link
	 * 
	 * @example <code>[post_comments]</code> is the default usage
	 * @example <code>[post_comments zero="No Comments" one="1 Comment" more="% Comments"]</code>
	 */
	function pagelines_post_comments_shortcode($atts) {

		$defaults = array(
			'zero' => __('Add Comment', 'pagelines'),
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
	 * This function produces the author of the post (link to author archive)
	 * 
	 * @example <code>[post_author_posts_link]</code> is the default usage
	 * @example <code>[post_author_posts_link before="<b>" after="</b>"]</code>
	 */
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
	 * This function produces the author of the post (link to author URL)
	 * 
	 * @example <code>[post_author_link]</code> is the default usage
	 * @example <code>[post_author_link before="<b>" after="</b>"]</code>
	 */
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
	 * This function produces the author of the post (display name)
	 * 
	 * @example <code>[post_author]</code> is the default usage
	 * @example <code>[post_author before="<b>" after="</b>"]</code>
	 */	
	function pagelines_post_author_shortcode($atts) {

		$defaults = array(
			'before' => '',
			'after' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf('<span class="author vcard sc">%2$s<span class="fn">%1$s</span>%3$s</span>', esc_html( get_the_author() ), $atts['before'], $atts['after']);

		return apply_filters('pagelines_post_author_shortcode', $output, $atts);

	}
	
	function pagelines_post_date_shortcode($atts) {

		$defaults = array(
			'format' => get_option('date_format'),
			'before' => '',
			'after' => '',
			'label' => ''
		);
		$atts = shortcode_atts( $defaults, $atts );

		$output = sprintf( '<time class="date time published updated sc" datetime="%5$s">%1$s%3$s%4$s%2$s</time> ', $atts['before'], $atts['after'], $atts['label'], get_the_time($atts['format']), get_the_time('Y-m-d\TH:i:s.uP') );

		return apply_filters('pagelines_post_date_shortcode', $output, $atts);

	}
	
	
	/**
	 * Shortcode to display Pinterest button
	 * 
	 * @example <code>[tweet_button]</code> is the default usage
	 * @example <code>[tweet_button]</code>
	 */
	function pl_pinterest_button( $args ){

			$defaults = array(
				'permalink'	=> '', 
				'width'		=> '80',
				'title'		=> '',
				'image'		=> '', 
				'desc'		=> ''
			); 	

			$a = wp_parse_args($args, $defaults);
			ob_start();
			?>

			<a href="http://pinterest.com/pin/create/button/?url=<?php echo $a['permalink'];?>&media=<?php echo urlencode($a['image']);?>&description=<?php echo urlencode($a['desc']);?>" class="pin-it-button" count-layout="none"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>
			<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
			<?php 

			return ob_get_clean();

		}
		
	/**
	 * Shortcode to display Tweet button
	 * 
	 * @example <code>[tweet_button]</code> is the default usage
	 * @example <code>[tweet_button]</code>
	 */
	function pl_twitter_button( $args ){

			$defaults = array(
				'permalink'	=> '', 
				'width'		=> '80',
				'handle'	=> ploption('twittername'), 
				'title'		=> ''
			); 	

			$a = wp_parse_args($args, $defaults);

			ob_start();

				// Twitter
				printf(
					'<a href="https://twitter.com/share" class="twitter-share-button" data-url="%s" data-text="%s" data-via="%s">Tweet</a>', 
					$a['permalink'], 
					$a['title'],
					(ploption('twitter_via')) ? $a['handle'] : ''
				);

			?>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

			<?php 

			return ob_get_clean();

		}
		
	/**
	 * Shortcode to display Facebook Like button
	 * 
	 * @example <code>[like_button]</code> is the default usage
	 * @example <code>[like_button]</code>
	 */
	function pl_facebook_shortcode( $args ){

			$defaults = array(
				'permalink'	=> '', 
				'width'		=> '80',
			); 

			$a = wp_parse_args($args, $defaults);


			ob_start();
				// Facebook
				?>
				<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
				</script>
				<?php
				printf(
					'<div class="fb-like" data-href="%s" data-send="false" data-layout="button_count" data-width="%s" data-show-faces="false" data-font="arial" style="vertical-align: top"></div>', 
					$a['permalink'], 
					$a['width']);

			return ob_get_clean();

		}
			
	/**
	 * This function/shortcode will show all authors on a post
	 * 
	 * @example <code>[show_authors]</code> is the default usage
	 * @example <code>[show_authors]</code>
	 */
	function show_multiple_authors() {

		if( class_exists('CoAuthorsIterator') ) {

			$i = new CoAuthorsIterator();
			$return = '';
			$i->iterate();
			$return .= '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author_meta('display_name').'</a>';
			while($i->iterate()){
				$return.= $i->is_last() ? ' and ' : ', ';
				$return .= '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'">'.get_the_author_meta('display_name').'</a>';
			}

			return $return;

		} else {
			//fallback
		}
	}
	
	
	/**
	 * PageLines Bootstrap Alertbox Shortcode
	 * 
	 * @example <code>[pl_alertbox type="info"]My alert[/pl_alertbox]</code> is the default usage
	 * @example <code>[pl_alertbox type="info"]<h4 class="pl-alert-heading">Heading</h4>My alert[/pl_alertbox]</code>
	 * @example Available types include info, success, warning, error
	 */
	function pl_alertbox_shortcode($atts, $content = null) {

		extract(shortcode_atts(array(
				    'type' => ''
				), $atts));

	    $out = sprintf('<div class="pl-alert pl-alert-%1$s">%2$s</div>',$type,$content);

		return $out;
	}
	
	/**
	 * PageLines Bootstrap Blockquote Shortcode
	 * 
	 * @example <code>[pl_blockquote type="info"]My quote[/pl_blockquote]</code> is the default usage
	 * @example <code>[pl_blockquote pull="right"]My quote pulled right[/pl_blockquote]</code>
	 */
	function pl_blockquote_shortcode($atts, $content = null) {

		extract(shortcode_atts(array(
				    'pull' => '',
				    'cite' =>''
				), $atts));

	    $out = sprintf('<blockquote class="pull-%1$s"><p>%3$s<small>%2$s</small></p></blockquote>',$pull,$cite,$content);

		return $out;
	}
	
	/**
	 * PageLines Bootstrap Button Shortcode
	 * 
	 * @example <code>[pl_button type="info"]My Button[/pl_button]</code> is the default usage
	 * @example <code>[pl_button type="info" url="#" target="blank"]My Button[/pl_button]</code>
	 * @example Available types include info, success, warning, danger, inverse
	 */
	function pl_button_shortcode($atts, $content = null) {

		extract(shortcode_atts(array(
				    'type' => '',
				    'link' =>'',
				    'target' => 'blank'
				), $atts));

	    $target = ( $target == 'blank' ) ? ' target="_blank"' : '';

	    $out = sprintf('<a href="%2$s" class="pl-btn pl-btn-%1$s" target="%3$s">%4$s</a>', $type,$link,$target,$content);

		return $out;
	}
	
	/**
	 * This function produces the time of post publication
	 * 
	 * @example <code>[post_time]</code> is the default usage
	 * @example <code>[post_time format="g:i a" before="<b>" after="</b>"]</code>
	 */	
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
	 * Used to create general buttons and button links
	 * 
	 * @example <code>[button]</code> is the default usage
	 * @example <code>[button format="edit_post" before="<b>" after="</b>"]</code>
	 */
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
	
//		
} // end of class
//
new PageLines_ShortCodes;