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
		
		

		$intro = $this->get_welcome_billboard();
		
		$intro .= $dash->wrap_dashboard_pane('tips', $args);
		
		$intro .= $this->get_plugins_billboard();
		

		return apply_filters('pagelines_welcome_intro', $intro);
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
				'text'	=> "Check out the Template Setup panel! Using drag and drop you can completely control the appearance of your templates. Learn more in the <a href='http://www.pagelines.com/wiki/'>docs</a>.", 
				'img'	=> PL_ADMIN_ICONS . '/dash-drag-drop.png'
			),
			'story4'	=> array(
				'title'	=> 'Set Up Your Extensions', 
				'text'	=> "In order to maximize PageLines your gonna need to get some extensions. Head over to the extensions page to get supported plugins and learn about extensions in the Store and Plus.", 
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
     * Default Headers
     *
     * Builds and returns the default Welcome header area (not the actual web page header)
     *
     * @uses    get_welcome_billboard
     * @uses    get_welcome_features
     *
     * @return mixed|string|void
     */
	function default_headers() {
	
		$intro = $this->get_welcome_billboard();
	
		$intro .= '<ul class="welcome_feature_list">';
	
		$count = 1;
		foreach($this->get_welcome_features() as $k => $i){
			$endrow = ($count % 2 == 0) ? true : false;
			$intro .= sprintf(
				'<li class="welcomef %s %s"><div class="welcomef-pad"><div class="feature_icon"></div><strong>%s</strong><p>%s</p></div></li>', 
				$i['class'], 
				($endrow) ? 'rlast' : '', 
				$i['name'], 
				$i['desc']
			);
			if($endrow) $intro .= '<div class="clear"></div>';
			$count++; 
		}
		$intro .= '<div class="clear"></div></ul>';
		return $intro;

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
	

	/**
     * Get Welcome Features
     *
     * Uses an array to create and return the theme features on the Welcome page via the 'pagelines_welcome_features' filter
     *
     * @internal    uses 'pagelines_welcome_features' filter
     *
     * @return      mixed|void
     */
	function get_welcome_features(){
		$f = array(
			'1strule'	=> array(
				'name'			=> __( 'The First Rule', 'pagelines' ),
				'desc'			=> __( "It's time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.", 'pagelines' ),
				'class'			=> 'feature_firstrule', 
				'icon'			=> '',
			),
			'support'	=> array(
				'name'			=> __( 'World Class Support', 'pagelines' ),
				'desc'			=> __( "For help getting started, we offer our customers tons of support including comprehensive <a href='http://www.pagelines.com/wiki/' target='_blank'>docs</a>, and an active, moderated <a href='http://www.pagelines.com/forum/' target='_blank'>forum</a>.", 'pagelines' ),
				'class'			=> 'feature_support', 
				'icon'			=> '',
			),
			'dragdrop'	=> array(
				'name'			=> __( 'Drag &amp; Drop Templates', 'pagelines' ),
				'desc'			=> __( "Check out the Template Setup panel! This is how you will control site elements using drag &amp; drop on your site. Learn more in the <a href='http://www.pagelines.com/wiki/'>docs</a>.", 'pagelines' ),
				'class'			=> 'feature_templates', 
				'icon'			=> '',
			),
			'settings'	=> array(
				'name'			=> __( 'Your Settings', 'pagelines' ),
				'desc'			=> __( 'This panel is where you will start the customization of your website. Any options applied through this interface will make changes site-wide.<br/> ', 'pagelines' ),
				'class'			=> 'feature_options', 
				'icon'			=> '',
			),
			'widgets'	=> array(
				'name'			=> __( 'Draggable Layout &amp; Widgets', 'pagelines' ),
				'desc'			=> __( 'Use the Layout Editor to control your content layout. There are also several <strong>widgetized</strong> areas that are controlled through your widgets panel.', 'pagelines' ),
				'class'			=> 'feature_dynamic', 
				'icon'			=> '',
			),
			'metapanel'	=> array(
				'name'			=> __( 'MetaPanel', 'pagelines' ),
				'desc'			=> __( "You'll find the MetaPanel at the bottom of WordPress page/post creation pages.  It will allow you to set options specific to that page or post.", 'pagelines' ),
				'class'			=> 'feature_meta', 
				'icon'			=> '',
			),
		);
		
		return apply_filters('pagelines_welcome_features', $f);
	}


    /**
     * Supported Elements UI
     *
     * Formats and wraps listed the array of name, URL, and description elements
     *
     * @param   $elements - array items
     *
     * @return  string - formatted items
     */
	function supported_element_ui( $elements ){
	
		$out = '<div class="plpanes"><div class="plpanes-pad">';
	
		if ( !is_array( $elements ) )
			return;
		$count = 1;
		foreach ( $elements as $args ) {
	
			$defaults = array(
				'name'	=> 'No Name', 
				'url'	=> '', 
				'desc'	=> ''
	
			); 
	
			$alt = ($count % 2 == 0) ? 'alt_row' : '';
	
			$args = wp_parse_args($args, $defaults);
		
			$button = sprintf('<a class="extend_button" href="%s" target="_blank">Get It</a>', $args['url']);
		
			$head = sprintf(
				'<div class="pane-head"><div class="pane-head-pad"><h3 class="pane-title">%s</h3><div class="pane-buttons">%s</div></div></div>',
				$args['name'], 
				$button
			);
		
			$body = sprintf(
				'<div class="pane-desc"><div class="pane-desc-pad"><div class="pane-text">%s</div></div></div>', 
				$args['desc']
			);

			$out .= sprintf('<div class="plpane %s"><div class="plpane-pad fix">%s %s</div></div>', $alt, $head, $body);
			
			$count++;
		}
	
		$out .= '</div></div>';
	
		return $out;
	
	}
	

	/**
     * Get Plugins Billboard
     *
     * Used to display the plugins and their title section
     *
     * @uses    show_supported_elements
     * @uses    supported_elements_ui
     * @uses    get_welcome_plugins
     * @uses    constant NICECHILDTHEMENAME
     *
     * @return  mixed|void
     */
	function get_plugins_billboard(){
	
		$plugins = $this->show_supported_elements( 'plugins' );
		$sections = $this->show_supported_elements( 'sections' );
		
		if (is_child_theme() && ($plugins != '' || $sections != ''))
			$support = sprintf('<h3 class="admin_header_main">%s</h3>%s%s', NICECHILDTHEMENAME.__(' Supported Extensions', 'pagelines'), $plugins, $sections);
		else 
			$support = '';
		
		
		$overview = sprintf( __( "<p>Although %s is universally plugin compatible, we have added <strong>advanced</strong> graphical/functional support for several WordPress plugins.</p><p> It's your responsibility to install each plugin, which can be done through <strong>Plugins</strong> &gt; <strong>Add New</strong> or through the <strong>developer's site</strong> where you can download them manually (e.g. CForms).</p>", 'pagelines' ), NICETHEMENAME );
			
		$core = $this->supported_element_ui( $this->get_welcome_plugins() );
			
		$billboard = sprintf( 
			'<div class="admin_billboard plugins_billboard"><div class="admin_billboard_content"><div class="feature_icon"></div>%s<h3 class="admin_header_main">%s</h3>%s%s</div></div>', 
			$support,
			__( 'Core Support', 'pagelines' ), 
			$overview, 
			$core
		);	
			
		return apply_filters( 'pagelines_welcome_plugins_billboard', $billboard );
	}


    /**
     * Get Supported Elements
     *
     * @param   $type - plugin|section
     *
     * @return  array|bool
     * @todo add appropriate description
     */
	function get_supported_elements( $type ) {
	
		global $supported_elements;
		$available = json_decode( get_option( 'pagelines_extend_' . $type, false ) );	

		if ( isset( $supported_elements[$type] ) && is_array( $supported_elements[$type] ) ) {
			$out = array();
				foreach( $supported_elements[$type] as $a ) {
					if ( isset( $a['name'] ) && isset( $a['desc'] ) && isset( $a['url'] ) ) {
						$out[ $a['name'] ] = array(
							'name'	=> $a['name'],
							'url'	=> $a['url'],
							'desc'	=> $a['desc']
							);
					} else {
						if ( ! $a['supported'] || ! isset( $available->$a['slug'] ) )
							continue;
						
						$out[ $a['slug'] ] = array(
							'name'	=> $available->$a['slug']->name,
							'url'	=> sprintf( 'http://www.pagelines.com/store/%s/%s/', $type, $a['slug'] ),
							'desc'	=> $available->$a['slug']->text
							);
					}
				}
            return $out;
        }
        return false;
    }


    /**
     * Show Supported Elements
     *
     * @param   $type
     *
     * @uses    get_supported_elements
     * @uses    supported_element_ui
     * @uses    constant NICECHILDTHEMENAME
     *
     * @return  string
     */
    function show_supported_elements( $type ) {

        if ( false != ( is_child_theme() && $a = $this->get_supported_elements( $type ) ) ) {
            $out = '';
            $out .= sprintf( '<p>%s supports these additional %s:</p>', NICECHILDTHEMENAME, $type );

            $out .= $this->supported_element_ui($a);

            return $out;
        }
            return '';

    }


	/**
     * Get Welcome Plugins
     *
     * Creates the $plugin array which can be filtered with 'pagelines_welcome_plugins'
     *
     * @internal    $plugins (array) for holding list of plugins with name, URL, and description
     * @internal    return 'pagelines_welcome_plugins' filter
     *
     * @return      mixed|void
     */
	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'name'			=> __( 'Post Types Order', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'desc'			=> __( 'Allows you to re-order custom post types like features and boxes.', 'pagelines' ),
			),
			'specialrecent'	=> array(
				'name'			=> __( 'Special Recent Posts', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/special-recent-posts/', 
				'desc'			=> __( 'A sidebar widget that shows your most recent blog posts and their thumbs.', 'pagelines' ),
			),
			'disqus'	=> array(
				'name'			=> __( 'Disqus Comments', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/disqus-comment-system/', 
				'desc'			=> __( 'Improve your commenting system.', 'pagelines' ),
			),
			'cforms'	=> array(
				'name'			=> __( 'CForms', 'pagelines' ),
				'url'			=> 'http://www.deliciousdays.com/cforms-plugin/', 
				'desc'			=> __( 'Advanced contact forms that can be used for creating mailing lists, etc.', 'pagelines' ),
			),
			'wp125'	=> array(
				'name'			=> __( 'WP125', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/wp125/', 
				'desc'			=> __( 'Used to show 125px by 125px ads or images in your sidebar(Widget).', 'pagelines' ),
			),
			'flickrrss'	=> array(
				'name'			=> __( 'FlickrRSS Images', 'pagelines' ),
				'url'			=> 'http://eightface.com/wordpress/flickrrss/', 
				'desc'			=> __( 'Shows pictures from your Flickr Account (Widget &amp; Carousel Section).', 'pagelines' ),
			),
			'nextgen'	=> array(
				'name'			=> __( 'NextGen-Gallery', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/nextgen-gallery/', 
				'desc'			=> __( 'Allows you to create image galleries with special effects (Carousel Section).', 'pagelines' ),
			),
			'pagenavi'	=> array(
				'name'			=> __( 'Wp-PageNavi', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/wp-pagenavi/', 
				'desc'			=> __( 'Creates advanced <strong>paginated</strong> post navigation.', 'pagelines' ),
			)
		);
		
		return apply_filters('pagelines_welcome_plugins', $plugins);
	}
}