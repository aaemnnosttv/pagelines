<?php 
/**
 * PageLines Welcome (class)
 *
 * This generates and returns the Welcome Page of the theme's Global Settings
 * 
 */
class PageLinesWelcome {
	
	/**
     * PHP5 Constructor
     */
	function __contruct(){ }
	

	/**
     * Get Welcome
     *
     * Pull all of the components together and returns them via the 'pagelines_welcome_intro' filter
     *
     * @uses        get_intro
     * @uses        get_plugins_billboard
     *
     * @internal    uses 'pagelines_welcome_finally' filter - text at the end of the welcome page
     *
     * @return      mixed|void
     */
	function get_welcome(){

		$dash = new PageLinesDashboard;
		
		// PageLines Plus
		$args = array(
			'title'			=> 'Some Tips To Get You Started', 
			'data'			=> $this->test_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/light-bulb.png', 
			'excerpt-trim'	=> false
		); 
		
		

		$view = $this->get_welcome_billboard();
		
		$view .= $dash->wrap_dashboard_pane('tips', $args);
		

		$args = array(
			'title'			=> 'Core WordPress Graphical/Functional Support', 
			'data'			=> $this->get_welcome_plugins(), 
			'icon'			=> PL_ADMIN_ICONS . '/extend-plugins.png', 
			'excerpt-trim'	=> false, 
			'align'			=> 'right', 
			'btn-text'		=> 'Get It', 
			'target'		=> 'new'
		); 

		$view .= $this->get_support_banner();

		$view .= $dash->wrap_dashboard_pane('support-plugins', $args);
		

		return apply_filters('pagelines_welcome_intro', $view);
	}
	
	function test_array(){
		
		$data = array(
			'story1'	=> array(
				'title'	=> 'The First Rule', 
				'text'	=> "It's time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.", 
				'img'	=> PL_ADMIN_ICONS . '/first-rule.png'
			), 
			'story3'	=> array(
				'title'	=> 'Drag &amp; Drop Template Setup', 
				'text'	=> "Check out the <a href='".admin_url(PL_TEMPLATE_SETUP_URL)."'>Template Setup panel</a>! Using drag and drop you can completely control the appearance of your templates. Learn more in the <a href='http://www.pagelines.com/wiki/'>docs</a>.", 
				'img'	=> PL_ADMIN_ICONS . '/dash-drag-drop.png'
			),
			'story4'	=> array(
				'title'	=> 'Set Up Your Extensions', 
				'text'	=> "To maximize PageLines you're gonna need some extensions. Head over to the extensions page to get supported plugins and learn about extensions in the Store and Plus.", 
				'img'	=> PL_ADMIN_ICONS . '/dash-plug.png'
			),
			'spprt'	=> array(
				'title'	=> 'Get Fast Support', 
				'text'	=> "For help getting started, we offer our customers tons of support including comprehensive <a href='http://www.pagelines.com/wiki/' target='_blank'>docs</a>, and an active, moderated <a href='http://www.pagelines.com/forum/' target='_blank'>forum</a>.", 
				'img'	=> PL_ADMIN_ICONS . '/dash-light-bulb.png'
			),
			'opts'	=> array(
				'title'	=> 'Site-Wide Vs. Page-by-Page Options', 
				'text'	=> "PageLines is completely set up using a combination of site-wide and page-by-page options. Configure your site wide settings in the 'site options' panel, and setup your page by page options on individual pages, and in the 'page options' panel, which is used to set defaults and manage multiple post pages (like your blog).", 
				'img'	=> PL_ADMIN_ICONS . '/dash-opts.png'
			),
			'widgets'	=> array(
				'title'	=> 'Menus and Widgets', 
				'text'	=> "PageLines makes use of WordPress functionality to help you manage your site faster and better. Specifically, you'll be using WP menus and widgets so you may want to familiarize yourself with those interfaces as well. ", 
				'img'	=> PL_ADMIN_ICONS . '/dash-setting.png'
			),
		);
		
		return $data;
		
	}
	

	/**
     * Get Intro
     *
     * Includes the 'welcome.php' file from Child-Theme's root folder if it exists; else, the PageLines default 'welcome.php' file is returned.
     *
     * @uses    default_headers
     *
     * @return  string
     */
	function get_intro() {
		
		if ( is_file( get_stylesheet_directory() . '/welcome.php' ) ) {
			
			ob_start();
				include( get_stylesheet_directory() . '/welcome.php' );
			return ob_get_clean();	
			
		} else {
			
			ob_start();
			include( PL_ADMIN . '/t.welcome.php' );
			$intro = ob_get_clean();
			
			return $this->default_headers() . $intro;
		}
	}
	


	/**
     * Get Welcome Billboard
     *
     * Used to produce the content at the top of the theme Welcome page.
     *
     * @uses        CHILD_URL (constant)
     * @internal    uses 'pagelines_welcome_billboard' filter
     *
     * @return      mixed|void
     */
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
						Getting Started with PageLines
					</h3>
					<div class='admin_billboard_text'>
						Congratulations and Welcome! Here are a few tips to get you started...
					</div>
			</div>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	

	function get_support_banner(){
		
		ob_start();
		?>

		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
						Core Plugin Support
					</h3>
					<div class='admin_billboard_text'>
						These common WordPress plugins that have special support within the framework
					</div>
			</div>
		</div>
		<?php 

		$banner = ob_get_clean();


		return $banner;
	}

	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'title'			=> __( 'Post Types Order', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'text'			=> __( 'Allows you to re-order custom post types like features and boxes.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'specialrecent'	=> array(
				'title'			=> __( 'Special Recent Posts', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/special-recent-posts/', 
				'text'			=> __( 'A sidebar widget that shows your most recent blog posts and their thumbs.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'disqus'	=> array(
				'title'			=> __( 'Disqus Comments', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/disqus-comment-system/', 
				'text'			=> __( 'Improve your commenting system.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'cforms'	=> array(
				'title'			=> __( 'CForms', 'pagelines' ),
				'link'			=> 'http://www.deliciousdays.com/cforms-plugin/', 
				'text'			=> __( 'Advanced contact forms that can be used for creating mailing lists, etc.', 'pagelines' ),
				'btn-text'		=> 'Get On DeliciousDays.com'
			),
			'wp125'	=> array(
				'title'			=> __( 'WP125', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/wp125/', 
				'text'			=> __( 'Used to show 125px by 125px ads or images in your sidebar(Widget).', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'flickrrss'	=> array(
				'title'			=> __( 'FlickrRSS Images', 'pagelines' ),
				'link'			=> 'http://eightface.com/wordpress/flickrrss/', 
				'text'			=> __( 'Shows pictures from your Flickr Account (Widget &amp; Carousel Section).', 'pagelines' ),
				'btn-text'		=> 'Get On EightFace.com'
			),
			'nextgen'	=> array(
				'title'			=> __( 'NextGen-Gallery', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/nextgen-gallery/', 
				'text'			=> __( 'Allows you to create image galleries with special effects (Carousel Section).', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			),
			'pagenavi'	=> array(
				'title'			=> __( 'Wp-PageNavi', 'pagelines' ),
				'link'			=> 'http://wordpress.org/extend/plugins/wp-pagenavi/', 
				'text'			=> __( 'Creates advanced <strong>paginated</strong> post navigation.', 'pagelines' ),
				'btn-text'		=> 'Get On WordPress.org'
			)
		);
		
		return apply_filters('pagelines_welcome_plugins', $plugins);
	}


	
}