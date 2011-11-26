<?php 
/**
 * 
 *  Returns Welcome
 *
 */
class PageLinesWelcome {
	
	
	function __contruct(){ }
	
	function get_welcome(){
		
		 
		
		$intro = '<div class="theme_intro"><div class="theme_intro_pad">';
		
		$intro .= $this->get_intro();
		
		$intro .= $this->get_plugins_billboard();
		
		$intro .= sprintf( '<div class="finally"><h3>%s</h3></div>', __( "That's it for now! Have fun and good luck.", 'pagelines' ) );
		
		$intro .= '</div></div>';

		return apply_filters('pagelines_theme_intro', $intro);
	}
	
	
	function get_intro() {
		
		if ( file_exists( get_stylesheet_directory() . '/welcome.php' ) ) {
			
			ob_start();
				include( get_stylesheet_directory() . '/welcome.php' );
			return ob_get_clean();	
		} else {
			ob_start();
			include( PL_ADMIN . '/welcome.php' );
			$intro = ob_get_clean();
			return $this->default_headers() . $intro;
			}
	}
	
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
	function get_welcome_billboard(){
		
		$bill = '<div class="admin_billboard fix"><div class="admin_billboard_pad fix">';
		$bill .= '<div class="admin_theme_screenshot"><img class="" src="'.CHILD_URL.'/screenshot.png" alt="Screenshot" /></div>';
		$bill .= sprintf( '<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">%s</h3></div>' , __( 'Congratulations!', 'pagelines' ) );
		$bill .= __( "<div class='admin_billboard_text'>Welcome to your PageLines site.<br/> Here are a few tips to get you started...<br/><small>(Note: This intro can be removed below.)</small></div>", 'pagelines' );
		$bill .= '<div class="clear"></div></div></div></div>';
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	
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
				'desc'			=> __( "For help getting started, we offer our customers tons of support including comprehensive <a href='http://www.pagelines.com/docs/' target='_blank'>docs</a>, and an active, moderated <a href='http://www.pagelines.com/forum/' target='_blank'>forum</a>.", 'pagelines' ),
				'class'			=> 'feature_support', 
				'icon'			=> '',
			),
			'dragdrop'	=> array(
				'name'			=> __( 'Drag &amp; Drop Templates', 'pagelines' ),
				'desc'			=> __( "Check out the Template Setup panel! This is how you will control site elements using drag &amp; drop on your site. Learn more in the <a href='http://docs.pagelines.com/'>docs</a>.", 'pagelines' ),
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
	
	function supported_element_ui( $elements ){
	
		$out = '<div class="plpanes"><div class="plpanes-pad">';
	
	
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
	
	function get_plugins_billboard(){
	
		$plugins = $this->show_supported_elements( 'plugins' );
		$sections = $this->show_supported_elements( 'sections' );		
		
		$overview = sprintf( __( "<p>Although %s is universally plugin compatible, we have added <strong>advanced</strong> graphical/functional support for several WordPress plugins.</p><p> It's your responsibility to install each plugin, which can be done through <strong>Plugins</strong> &gt; <strong>Add New</strong> or through the <strong>developer's site</strong> where you can download them manually (e.g. CForms).</p>", 'pagelines' ), NICETHEMENAME );
			
		$core = $this->supported_element_ui( $this->get_welcome_plugins() );
			
		$billboard = sprintf( 
			'<div class="admin_billboard plugins_billboard"><div class="admin_billboard_content"><div class="feature_icon"></div><h3 class="admin_header_main">%s</h3>%s%s<h3 class="admin_header_main">%s</h3>%s%s</div></div>', 
			__( 'Supported Extensions', 'pagelines' ), 
			$plugins, 
			$sections,
			__( 'Core Support', 'pagelines' ), 
			$overview, 
			$core
		);	
			
		return $billboard;
	}
	
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

		function show_supported_elements( $type ) {

			if ( false != ( is_child_theme() && $a = $this->get_supported_elements( $type ) ) ) {
				$out = '';
				$out .= sprintf( '<p>%s supports these additional %s:</p>', NICECHILDTHEMENAME, $type );

				$out .= $this->supported_element_ui($a);

				return $out;
			}
				return '';
		}


	

	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'name'			=> __( 'Post Types Order', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'desc'			=> __( 'Allows you to re-order custom post types like features and boxes.', 'pagelines' ),
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
