<?php

// ==============================
// = PageLines Function Library =
// ==============================

/**
 * 
 *  Sets up classes for controlling design and layout and is used on the body tag
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_body_classes(){
	
	$design_mode = (pagelines_option('site_design_mode')) ? pagelines_option('site_design_mode') : 'full_width';

	$body_classes = '';
	$body_classes .= $design_mode;
	
	if(pagelines_is_buddypress_active() && !pagelines_bbpress_forum()){
		$body_classes .= ' buddypress';
	} 
	
	if (pagelines_bbpress_forum()){
		
		$body_classes .= ' bbpress';
		
	} else {
		
		global $pagelines_template;
		$body_classes .= ' ttype-'.$pagelines_template->template_type.' tmain-'.$pagelines_template->main_type;
		
	}	
	
	return $body_classes;
}


/**
 * 
 *  Sets up global post ID and $post global for handling, reference and consistency
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.0.0
 *
 */
function pagelines_id_setup(){
	global $post;
	global $pagelines_ID;
	global $pagelines_post;

	if(isset($post) && is_object($post)){
		$pagelines_ID = $post->ID;
		$pagelines_post = $post;	
	}
	else {
		$pagelines_post = '';
		$pagelines_ID = '';
	}
	
}

/**
 * 
 *  Registered PageLines Hooks. Stores for reference or use elsewhere.
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_register_hook( $hook_name, $hook_area_id = null){

	/*
		Do The Hook
	*/
	do_action( $hook_name, $hook_name, $hook_area_id);
		
}

/**
 * 
 *  Does hooks for template areas
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_template_area( $hook_name, $hook_area_id = null){

	/*
		Do The Hook
	*/
	do_action( $hook_name, $hook_area_id);
		
}

/**
 * 
 *  Check the authentication level for administrator status (security)
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.x.x
 *
 */
function checkauthority(){
	if (!current_user_can('edit_themes'))
	wp_die('Sorry, but you don&#8217;t have the administrative privileges needed to do this.');
}

/**
 * 
 *  Checks for IE and Returns Version
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.0.0
 *
 */
function ie_version() {
  $match=preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
  if($match==0)
    return false;
  else
    return floatval($reg[1]);
}

/**
 * 
 *  Gets a 'tiny' url. Returns URL if response is not 200
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.5.0
 *  
 *  Uses filter 'pagelines_shorturl_provider'
 *  Uses filter 'pagelines_shorturl_cachetimeout' 
 */
function pagelines_shorturl( $url, $timeout = 86400 ) {

	if ( !pagelines_option( 'share_twitter_cache' ) )
		return $url;

	$provider = 'http://pln.so/api.php?action=shorturl&format=json&url=';

	// If cache exists send it back
	$cache = get_transient( 'pagelines_shorturl_cache' );
	if ( is_array( $cache) && array_key_exists( md5($url), $cache ) ) {
		return $cache[md5($url)];
	}

	// Fetch the short url from the api
	$response = wp_remote_get(  apply_filters( 'pagelines_shorturl_provider' , $provider ) . $url );

	if( is_wp_error( $response ) ) return $url; // return original url if there is an error

	// Check the body from the api is actually a url and not a 400 error
	// If its OK we will cache it and return it, othwise return original url
	
	$out = ( $response['response']['code'] == 200 ) ? $response['body'] : false; 
	if ( !is_object( $out = json_decode( $out ) ) ) return $url;

	if ( $cache == false ) {
		unset( $cache );
		$cache = array();
	}
	delete_transient( 'pagelines_shorturl_cache' );
	$cache = array_merge( $cache, array( md5($url) => $out->shorturl ) );
	set_transient( 'pagelines_shorturl_cache', $cache, apply_filters( 'pagelines_shorturl_cachetimeout', $timeout ) );
	return $out->shorturl;
}

/**
 * 
 *  Returns Current Layout Mode
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.0.0
 *
 */
function pagelines_layout_mode() {

	global $pagelines_layout;
	global $post;

	if(!pagelines_is_posts_page() && isset($post) && get_post_meta($post->ID, '_pagelines_layout_mode', true)){
		$pagelines_layout->build_layout(get_post_meta($post->ID, '_pagelines_layout_mode', true));
		return get_post_meta($post->ID, '_pagelines_layout_mode', true);
	} elseif(pagelines_is_posts_page() && pagelines_option('posts_page_layout')){
		$pagelines_layout->build_layout(pagelines_option('posts_page_layout'));
		return pagelines_option('posts_page_layout');
	} else {
		return $pagelines_layout->layout_mode;
	}

}


/**
 * 
 *  Sets Content Width for Large images when adding media
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.2.3
 *
 */
function pagelines_current_page_content_width() {

	global $pagelines_layout;
	global $content_width;
	global $post;

	$mode = pagelines_layout_mode();
	
	$c_width = $pagelines_layout->layout_map[$mode]['maincolumn_width'];
	
	if ( !isset( $content_width ) ) $content_width = $c_width - 45;

}

/**
 * 
 *  Sets background cascade for use in color mixing.
 *
 *  @since 2.0.b6
 *
 */
function pl_background_cascade(){
	
	$cascade = array(
		pagelines_option('contentbg'),
		pagelines_option('pagebg'),
		pagelines_option('bodybg'),
	);
	
	return apply_filters('background_cascade', $cascade);
}


/**
 * 
 *  Checks if currently viewed page is part of BuddyPress Template
 *
 *  @package PageLines
 *  @subpackage BuddyPress Support
 *  @since 4.0
 *
 */
function pagelines_is_buddypress_page(){
	global $bp; 
	if(isset($bp) && isset($bp->current_component) && !empty($bp->current_component)){
		return true;
	}else{
		return false;
	}
}

/**
 * 
 *   Checks if BuddyPress is active
 *
 *  @package PageLines
 *  @subpackage BuddyPress Support
 *  @since 4.0
 *
 */
function pagelines_is_buddypress_active(){
	global $bp; 
	
	if(isset($bp))
		return true;
	else
		return false;
}


/**
 * Checks to see if there is more than one page for nav.
 * TODO does this add a query?
 * 
 * @since 4.0.0
 */
function show_posts_nav() {
	global $wp_query;
	return ($wp_query->max_num_pages > 1);
}


/**
 * Pulls a global identifier from a bbPress forum installation
 * @since 3.x.x
 */
function pagelines_bbpress_forum(){
	global $bbpress_forum;
	if($bbpress_forum ){
		return true;
	} else return false;
}


/**
 * Displays query information in footer (For testing - NOT FOR PRODUCTION)
 * @since 4.0.0
 */
function show_query_analysis(){
	if (current_user_can('administrator')){
	    global $wpdb;
	    echo "<pre>";
	    print_r($wpdb->queries);
	    echo "</pre>";
	}
}

function custom_trim_excerpt($text, $length) {
	
	$text = strip_shortcodes( $text ); // optional
	$text = strip_tags($text);
	
	$words = explode(' ', $text, $length + 1);
	if ( count($words) > $length) {
		array_pop($words);
		$text = implode(' ', $words);
	}
	return $text.'&nbsp;[&hellip;]';
}
	



function pagelines_add_page($file, $name){
	global $pagelines_user_pages;
	
	$pagelines_user_pages[$file] = array('name' => $name);
	
}

/**
 * Used for Callback calls, returns nothing
 * 
 * @since 1.0.0
 */
function pagelines_setup_menu() {
	echo 'Add links using WordPress menus in your site admin.';
}

/**
 * Includes the loading template that sets up all PageLines templates
 * 
 * @since 1.1.0
 */
function setup_pagelines_template() {
	get_header();

	pagelines_template_area('pagelines_template', 'templates');

	get_footer();
}


/**
 * Adds pages from the child theme.
 * 
 * @since 1.1.0
 */
function pagelines_add_page_callback( $page_array, $template_area ){
	global $pagelines_user_pages;
	
	if( is_array($pagelines_user_pages) ){
		foreach($pagelines_user_pages as $file => $pageinfo){
			$page_array[$file] = array('name'=>$pageinfo['name']);
		}
	}
	
	return $page_array;
}

/**
 * Overrides default excerpt handling so we have more control
 * 
 * @since 1.2.4
 */
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'improved_trim_excerpt');
function improved_trim_excerpt($text) {
	
	// Set allowed excerpt tags
	$allowed_tags = (pagelines_option('excerpt_tags')) ? pagelines_option('excerpt_tags') : '';
	
	$raw_excerpt = $text;
	if ( '' == $text ) {
		$text = get_the_content('');

		$text = strip_shortcodes( $text );


		$text = apply_filters('the_content', $text);
		
		$text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text); // PageLines - Strip JS
		
		$text = str_replace(']]>', ']]&gt;', $text);
		
		$text = strip_tags($text, $allowed_tags); // PageLines - allow more tags
		
		$excerpt_length = apply_filters('excerpt_length', 55);
		$excerpt_more = apply_filters('excerpt_more', ' ' . '[...]');
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . $excerpt_more;
		} else {
			$text = implode(' ', $words);
		}
	}
	return apply_filters('wp_trim_excerpt', $text, $raw_excerpt);
}

/**
 * 
 *  Returns nav menu classes
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_nav_classes(){ 
	
	$additional_menu_classes = '';
		
	if(pagelines_option('enable_drop_down'))
		$additional_menu_classes .= ' sf-menu';
	
	return $additional_menu_classes;
}

/**
 * 
 *  Loads Special PageLines CSS Files, Optimized
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.2.0
 *
 */
function pagelines_draw_css( $css_url, $id = '', $enqueue = false){ 
	echo '<link href="'.$css_url.'" rel="stylesheet"/>'."\n";
}


/**
 * 
 *  Abstracts the Enqueue of Stylesheets, fixes bbPress issues with dropping hooks
 *
 *  @package Platform
 *  @since 1.3.0
 *
 */
function pagelines_load_css( $css_url, $id, $hash = CORE_VERSION, $enqueue = true){
	
	
	if(pagelines_bbpress_forum()){
		printf('<link rel="stylesheet" id="%s"  href="%s?ver=%s" type="text/css" />%s', $id, $css_url, $hash, "\n");
	} else {
		wp_register_style($id, $css_url, array(), $hash, 'all');
	    wp_enqueue_style( $id );
	}
	

}

/**
 * 
 *  Loading CSS using relative path to theme root. This allows dynamic versioning, overriding in child theme
 *
 *  @package Platform
 *  @since 1.4.0
 *
 */
function pagelines_load_css_relative( $relative_style_url, $id){
	
	$rurl = '/' . $relative_style_url;
	
	if( file_exists(STYLESHEETPATH . $rurl ) ){
		$date_modified = filemtime( STYLESHEETPATH . $rurl );
		$cache_ver = str_replace('.', '', CHILD_VERSION) . '-' . date('mdyGis', $date_modified);
		
		pagelines_load_css( CHILD_URL . $rurl , $id, $cache_ver);
		 
	} elseif(file_exists(TEMPLATEPATH . $rurl) ){
		$date_modified = filemtime( TEMPLATEPATH . $rurl );
		$cache_ver = str_replace('.', '', CORE_VERSION) .'-'.date('mdyGis', $date_modified);
		
		pagelines_load_css( PARENT_URL . $rurl , $id, $cache_ver);
		
	} 
	

}

/**
 * 
 *  Get Stylesheet Version
 *
 *  @package Platform
 *  @since 1.4.0
 *
 */
function pagelines_get_style_ver( $tpath = false ){
	
	// Get cache number that accounts for edits to base.css or style.css
	if( file_exists(STYLESHEETPATH .'/base.css') && !$tpath ){
		$date_modified = filemtime( STYLESHEETPATH .'/base.css' );
		$cache_ver = str_replace('.', '', CHILD_VERSION) . '-' . date('mdGis', $date_modified); 
	} elseif(file_exists(STYLESHEETPATH .'/style.css') && !$tpath ){
		$date_modified = filemtime( STYLESHEETPATH .'/style.css' );
		$cache_ver = str_replace('.', '', CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} elseif(file_exists(TEMPLATEPATH .'/style.css')){
		$date_modified = filemtime( TEMPLATEPATH .'/style.css' );
		$cache_ver = str_replace('.', '', CORE_VERSION) .'-'.date('mdGis', $date_modified);
	} else {
		$cache_ver = CORE_VERSION;
	}
	
	
	return $cache_ver;

}

/**
 * Debugging, prints nice array.
 * Sends to the footer in all cases.
 * 
 * @since 1.5.0
 */
function plprint( $data, $title = false){

			ob_start();
			
				echo 'echo "<pre class=\'plprint\'>';
			
				if($title) 
					echo sprintf('<h3>%s</h3>', $title);
				
				echo esc_html( print_r( $data, TRUE ) );
				
				echo '</pre>";';
				
				$data = ob_get_contents();
				
			ob_end_clean();

	add_action( 'shutdown', create_function( '', $data ) );

}

/**
 * Creates Upload Folders for PageLines stuff
 *
 * @return true if successful
 **/

function pagelines_make_uploads($txt = 'Load'){
add_filter('request_filesystem_credentials', '__return_true' );

	$method = '';
	$url = 'themes.php?page=pagelines';

	if (is_writable(PAGELINES_DCSS)){
		$creds = request_filesystem_credentials($url, $method, false, false, null);
		if ( ! WP_Filesystem($creds) ) {
			// our credentials were no good, ask the user for them again
			request_filesystem_credentials($url, $method, true, false, null);
			return false;
		}

		global $wp_filesystem;
		if ( ! $wp_filesystem->put_contents( PAGELINES_DCSS, $txt, FS_CHMOD_FILE) ) {
			echo "error saving file!";
			return false;
		}
	}

	return true;
}
/**
 * return array of PageLines plugins.
 * Since 2.0
 */
function pagelines_register_plugins() {
	
	$pagelines_plugins = array();
	$plugins = get_option('active_plugins');
	if ( $plugins ) {
		foreach( $plugins as $plugin ) {
			$a = get_file_data( WP_PLUGIN_DIR . '/' . $plugin, $default_headers = array( 'pagelines' => 'PageLines' ) );
			if ( !empty( $a['pagelines'] ) ) {
				$pagelines_plugins[] = rtrim( basename($plugin), '.php');
			}

		}
	}
	return $pagelines_plugins;
}

/**
 *
 * Return sorted array based on supplied key
 * 
 * @since 2.0
 * @return sorted array
 */
function pagelines_array_sort( $a, $subkey, $pre = null, $dec = null ) {
	foreach( $a as $k => $v ) {
		$b[$k] = ( $pre ) ? strtolower( $v[$pre][$subkey] ) : strtolower( $v[$subkey] );
	}
	( !$dec ) ? asort( $b ) : arsort($b);
	foreach( $b as $key => $val ) {
		$c[] = $a[$key];
	}
	return $c;
}