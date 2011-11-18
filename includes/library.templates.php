<?php
/**
 * This file contains a library of common templates accessed by functions
 *
 * @package PageLines Core
 *
 **/

// ======================================
// = Sidebar Setup & Template Functions =
// ======================================

/**
 * Sidebar - Call & Markup
 *
 */

function pagelines_draw_sidebar($id, $name, $default = null, $element = 'ul'){
	
	printf('<%s id="%s" class="sidebar_widgets fix">', $element, 'list_'.$id);
	
	if (!dynamic_sidebar($name))
		pagelines_default_widget( $id, $name, $default); 
	
	printf('</%s>', $element);

}

/**
 * Sidebar - Default Widget
 *
 */
function pagelines_default_widget($id, $name, $default){
	if(isset($default) && !pagelines('sidebar_no_default')):
	
		get_template_part( $default ); 
		
	elseif( current_user_can('edit_themes') ):
	?>	

	<li class="widget widget-default setup_area no_<?php echo $id;?>">
		<div class="widget-pad">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p class="fix">This is your <?php echo $name;?> but it needs some widgets!<br/> Easy! Just add some content to it in your <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a>.	
			</p>
			<?php echo blink('Add Widgets &rarr;', 'link', 'black', array('action' => admin_url('widgets.php'), 'clear' => true)); ?>
		</div>
	</li>

<?php endif;
	}

/**
 * Sidebar - Standard Sidebar Setup
 *
 */
function pagelines_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}


/**
 * Javascript Confirmation
 *
 * @param string $name Function name, to be used in the input
 * @param string $text The text of the confirmation
 */
function pl_action_confirm($name, $text){ 
	?>
	<script language="jscript" type="text/javascript"> function <?php echo $name;?>(){	
			var a = confirm ("<?php echo esc_js( $text );?>");
			if(a) {
				jQuery("#input-full-submit").val(1);
				return true;
			} else return false;
		}
	</script>
<?php }

/**
 * PageLines Search Form
 *
 * @param bool $echo 
 */
function pagelines_search_form( $echo = true ){ 

	$searchfield = sprintf('<input type="text" value="%1$s" name="s" class="searchfield" onfocus="if(this.value == \'%1$s\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \'%1$s\';}" />', __('Search', 'pagelines'));
	
	$searchimage = sprintf('<input type="image" class="submit btn" name="submit" src="%s" alt="Go" />', apply_filters( 'pl_search_image', PL_IMAGES.'/search-btn.png' ) );
	
	$searchform = sprintf('<form method="get" class="searchform" onsubmit="this.submit();return false;" action="%s/" ><fieldset>%s %s</fieldset></form>', home_url(), $searchfield, $searchimage);
	
	if ( $echo )
		echo apply_filters('pagelines_search_form', $searchform);
	else
		return apply_filters('pagelines_search_form', $searchform);
}


/**
 * PageLines <head> Includes
 *
 */
function pagelines_head_common(){
	
	pagelines_register_hook('pagelines_code_before_head'); // Hook 

	printf('<meta http-equiv="Content-Type" content="%s; charset=%s" />',  get_bloginfo('html_type'),  get_bloginfo('charset'));

	// Draw Page <title> Tag
	pagelines_title_tag();
	
	// Some Credit
	if(!VDEV)
		echo '<!-- PageLines Professional Drag-and-Drop Framework - www.PageLines.com -->\n';
		
	// Meta Images
	if(ploption('pagelines_favicon'))
		printf('<link rel="shortcut icon" href="%s" type="image/x-icon" />%s', ploption('pagelines_favicon'), "\n");
	
	if(ploption('pagelines_touchicon'))
		printf('<link rel="apple-touch-icon" href="%s" />%s', ploption('pagelines_touchicon'), "\n");

	// Meta Data Profiles
	if(!apply_filters( 'pagelines_xfn', '' ))
		echo '<link rel="profile" href="http://gmpg.org/xfn/11" />'."\n";

	// Removes viewport scaling on Phones, Tablets, etc.
	if(!apply_filters( 'viewport_width', '' ))
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />';

	// Allow for extension deactivation of all css
	if(!has_action('override_pagelines_css_output')){	

		// Get Common CSS & Reset
		pagelines_load_css_relative('css/common.css', 'pagelines-common');

		// Get CSS Layout
		pagelines_load_css_relative('css/layout.css', 'pagelines-layout');

		// Get CSS Objects & Grids
		pagelines_load_css_relative('css/objects.css', 'pagelines-objects');
		
		// WordPress CSS Api
		pagelines_load_css(  get_bloginfo('stylesheet_url'), 'pagelines-stylesheet', pagelines_get_style_ver());
		
		// Allow for PHP include of Framework CSS
		if(is_child_theme() && !apply_filters( 'disable_pl_framework_css', '' ))
			pagelines_load_css(  PARENT_URL.'/style.css', 'pagelines-framework', pagelines_get_style_ver( true ));
		
	
		// RTL Language Support
		if(is_rtl()) 
			pagelines_load_css_relative( 'rtl.css', 'pagelines-rtl');
		
	}

	
		// Queue Common Javascript Libraries
		wp_enqueue_script( 'jquery'); 
		wp_enqueue_script( 'blocks', PL_JS . '/script.blocks.js', array('jquery'));
	
	
	pagelines_supersize_bg();
	
	// Fix IE and special handling
	pagelines_fix_ie();
	
	// Cufon replacement 
	pagelines_font_replacement();
	
	// Headerscripts option > custom code
	if ( ploption( 'headerscripts' ) )
		add_action( 'wp_head', create_function( '',  'print_pagelines_option("headerscripts");' ), 25 );
}

function pagelines_supersize_bg(){
	
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	$url = ploption('page_background_image_url', $oset);

	if(ploption('supersize_bg') && $url && !pl_is_disabled('color_control')){ 
		
		wp_enqueue_script('supersize', PL_JS.'/script.supersize.js', 'jquery' );
		
		add_action('wp_head', 'pagelines_runtime_supersize', 20);
	}
		
	
}

function pagelines_runtime_supersize(){
	
	global $pagelines_ID;
	$oset = array('post_id' => $pagelines_ID);
	$url = ploption('page_background_image_url', $oset);
	?>
	
	<script type="text/javascript"> /* <![CDATA[ */
	jQuery(document).ready(function(){
		jQuery.supersized({ slides  :  	[ { image : '<?php echo $url; ?>' } ] });
	});/* ]]> */
	</script>
	
<?php
}

	
function pagelines_title_tag(){
	/*
		Title Metatag
	*/
	echo "\n<title>";

	if ( !function_exists( 'aiosp_meta' ) && !function_exists( 'wpseo_get_value' ) ) {
	// Pagelines seo titles.
		global $page, $paged;
		$title = wp_title( '|', false, 'right' );

		// Add the blog name.
		$title .= get_bloginfo( 'name' );

		// Add the blog description for the home/front page.
		$title .= ( ( is_home() || is_front_page() ) && get_bloginfo( 'description', 'display' ) ) ? ' | ' . get_bloginfo( 'description', 'display' ) : '';

		// Add a page number if necessary:
		$title .= ( $paged >= 2 || $page >= 2 ) ? ' | ' . sprintf( __( 'Page %s', 'pagelines' ), max( $paged, $page ) ) : '';
	} else
		$title = trim( wp_title( '', false ) );
	
	// Print the title.
	echo apply_filters( 'pagelines_meta_title', $title );
	
	echo "</title>\n";
}	
	
/**
 * 
 *  Do dynamic CSS, hooked in head; should go last.
 *
 */	
function do_dynamic_css(){
	
	if(!apply_filters('disable_dynamic_css', ''))
		get_dynamic_css();
		
}	
	
/**
 * 
 *  Fix IE to the extent possible
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_fix_ie( ){
	
	global $is_IE;
	if ( ! $is_IE )
		return;

	if(pagelines('google_ie'))
		echo '<!--[if lt IE 8]> <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script> <![endif]-->'."\n";
	
	printf('<!--[if lt IE 9]>%3$s<script src="%1$s"></script>%3$s<script src="%2$s" ></script>%3$s<![endif]-->%3$s', 'http://html5shim.googlecode.com/svn/trunk/html5.js', 'http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js',"\n");
	
	/*
		IE File Setting up with conditionals
		TODO Why doesnt WP allow you to conditionally enqueue scripts?
	*/

	// If IE7 add the Internet Explorer 7 specific stylesheet
	global $wp_styles;
	wp_enqueue_style('ie7-style', PL_CSS  . '/ie7.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie7-style', 'conditional', 'IE 7' );
	
} 

/**
 * 
 *  Cufon Font Replacement
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_font_replacement( $default_font = ''){
	
	if(pagelines_option('typekit_script')){
		echo pagelines_option('typekit_script');
	}
	
	if(pagelines_option('fontreplacement')){
		global $cufon_font_path;
		
		if(pagelines_option('font_file')) $cufon_font_path = pagelines_option('font_file');
		elseif($default_font) $cufon_font_path = PL_JS.'/'.$default_font;
		else $cufon_font_path = null;
		
		// ===============================
		// = Hook JS Libraries to Footer =
		// ===============================
		add_action('wp_footer', 'font_replacement_scripts');
		function font_replacement_scripts(){
			
			global $cufon_font_path;

			wp_register_script('cufon', PL_ADMIN_JS.'/type.cufon.js', 'jquery', '1.09i', true);
			wp_print_scripts('cufon');
			
			if(isset($cufon_font_path)){
				wp_register_script('cufon_font', $cufon_font_path, 'cufon');
				wp_print_scripts('cufon_font');
			}
		
		}
		
		add_action('wp_head', 'cufon_inline_script');
		function cufon_inline_script(){
			?><script type="text/javascript"><?php 
			if(pagelines('replace_font')): 
				?>jQuery(document).ready(function () { Cufon.replace('<?php echo pagelines_option("replace_font"); ?>', {hover: true}); });<?php 
			endif;
			?></script><?php
		 }
 	}
}

/**
 * 
 *  Pagination Function
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 2.0.b12 moved
 *
 */
function pagelines_pagination() {
	if(function_exists('wp_pagenavi') && show_posts_nav() && VPRO):
		wp_pagenavi(); 
	elseif (show_posts_nav()) : ?>
		<div class="page-nav-default fix">
			<span class="previous-entries"><?php next_posts_link(__('&larr; Previous Entries','pagelines')) ?></span>
			<span class="next-entries"><?php previous_posts_link(__('Next Entries &rarr;','pagelines')) ?></span>
		</div>
<?php endif;
}

/**
 * 
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_nav_fallback() {
	global $post; ?>
	
	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}


/**
 * 
 *  Blank Nav Fallback
 *
 */
function blank_nav_fallback() {
	
	if(current_user_can('edit_themes'))
		printf( __( '<ul class="inline-list">Please select a nav menu for this area in the <a href="%s">WordPress menu admin</a>.</ul>', 'pagelines' ), admin_url('nav-menus.php') );
}

/**
 * 
 *  Returns child pages for subnav, setup in hierarchy
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_page_subnav(){ 
	global $post; 
	if(!is_404() && isset($post) && is_object($post) && !pagelines_option('hide_sub_header') && ($post->post_parent || wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0'))):?>
	<ul class="secondnav_menu lcolor3">
		<?php 
			if(count($post->ancestors)>=2){
				$reverse_ancestors = array_reverse($post->ancestors);
				$children = wp_list_pages('title_li=&depth=1&child_of='.$reverse_ancestors[0].'&echo=0&sort_column=menu_order');	
			}elseif($post->post_parent){ $children = wp_list_pages('title_li=&depth=1&child_of='.$post->post_parent.'&echo=0&sort_column=menu_order');
			}else{	$children = wp_list_pages('title_li=&depth=1&child_of='.$post->ID.'&echo=0&sort_column=menu_order');}

			if ($children) { echo $children;}
		?>
	</ul>
	<?php endif;
}

/**
 * 
 *  The main site logo template
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_main_logo( $location = null ){ 
	
	global $pagelines_ID; 
	
	$oset = array( 'post_id' => $pagelines_ID );
	
	if(ploption('pagelines_custom_logo', $oset) || apply_filters('pagelines_site_logo', '') || apply_filters('pagelines_logo_url', '')){
		
		$logo_url = apply_filters('pagelines_logo_url', esc_url(ploption('pagelines_custom_logo', $oset) ), $location);
		
		$site_logo = sprintf( '<a class="mainlogo-link" href="%s" title="%s"><img class="mainlogo-img" src="%s" alt="%s" /></a>', home_url(), get_bloginfo('name'), $logo_url, get_bloginfo('name'));
		
		echo apply_filters('pagelines_site_logo', $site_logo, $location);
		
	} else {
		
		$site_title = sprintf( '<div class="title-container"><a class="home site-title" href="%s" title="%s">%s</a><h6 class="site-description subhead">%s</h6></div>', esc_url(home_url()), __('Home','pagelines'), get_bloginfo('name'), get_bloginfo('description'));
		
		echo apply_filters('pagelines_site_title', $site_title, $location);
		
	}
		
}




/**
 * 
 *  Adds PageLines to Admin Bar
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.0
 *
 */
function pagelines_settings_menu_link(  ){ 
	global $wp_admin_bar;
	
	global $pagelines_template;

	
	if ( !current_user_can('edit_theme_options') )
		return;


	$wp_admin_bar->add_menu( array( 'id' => 'pl_settings', 'title' => __('PageLines', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines' ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_main_settings', 'parent' => 'pl_settings', 'title' => __('Settings', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines' ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_templates', 'parent' => 'pl_settings', 'title' => __('Templates', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines_templates' ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_special', 'parent' => 'pl_settings', 'title' => __('Special', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines_special' ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_extend', 'parent' => 'pl_settings', 'title' => __('Store', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines_extend' ) ) );
	$wp_admin_bar->add_menu( array( 'id' => 'pl_account', 'parent' => 'pl_settings', 'title' => __('Account', 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines_account' ) ) );

	$template_name = (isset($pagelines_template->template_name)) ? $pagelines_template->template_name : false;

	if( $template_name ){
		$page_type = __('Current Page: ', 'pagelines') . ucfirst($template_name );
		$wp_admin_bar->add_menu( array( 'id' => 'template_type', 'title' => $page_type, 'href' => admin_url( 'admin.php?page=pagelines_templates' ) ) );
	}
	
	$spurl = pl_special_url( $template_name );
	
	if( $template_name && is_pagelines_special() && $spurl){
		$wp_admin_bar->add_menu( array( 'id' => 'special_settings', 'title' => __('Edit Special', 'pagelines'), 'href' => $spurl ) );
	}
}

function pl_special_url( $t ){
	
	$t = strtolower( trim($t) );
	
	if($t == 'blog')
		$slug = 'blog_page';
	elseif($t == 'category')
		$slug = 'category_page';
	elseif($t == 'archive')
		$slug = 'archive_page';
	elseif($t == 'search')
		$slug = 'search_results';
	elseif($t == 'tag')
		$slug = 'tag_listing';
	elseif($t == '404_error')
		$slug = '404_page';
	elseif($t == 'author')
		$slug = 'author_posts';
	else 
		return false;

	$rurl = sprintf('admin.php?page=pagelines_special%s', '#'.$slug);

	return admin_url( $rurl );

}

/**
 * 
 *  PageLines Attribution
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.3
 *
 */
function pagelines_cred(){ 
	
	if(pagelines_option('no_credit') || !VDEV){
		
		
		$img 	= sprintf('<img src="%s" alt="%s by PageLines" />', apply_filters('pagelines_leaf_image', PL_IMAGES.'/pagelines.png'), THEMENAME);
		
		if(get_edit_post_link()){
			$url = get_edit_post_link();
		} else {
			$url = load_pagelines_option('partner_link', 'http://www.pagelines.com/');
		}
		
		$link = (!apply_filters('no_leaf_link', '')) ? sprintf('<a class="plimage" target="_blank" href="%s" title="%s">%s</a>', $url, 'PageLines', $img ) : $img;
		
		$cred = sprintf('<div id="cred" class="pagelines">%s</div><div class="clear"></div>', $link);
	
		echo apply_filters('pagelines_leaf', $cred);
		
	}

}


