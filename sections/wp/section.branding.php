<?php
/*
	Section: Branding
	Author: Andrew Powers
	Author URI: http://www.pagelines.com
	Description: Shows the main site logo or the site title and description.
	Class Name: PageLinesBranding
	Tags: internal
*/

class PageLinesBranding extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Site Branding', 'pagelines');
		$id = 'branding';
	
		
		$settings = array(
			'type' 				=> 'header',
			'workswith'		 	=> array('header'),
			'description' 		=> 'Shows the main site logo or the site title and description.',
			'icon'				=> PL_ADMIN_ICONS . '/megaphone.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
			
			printf('<div class="branding_wrap">');
			
				pagelines_main_logo(); 
			
				pagelines_register_hook( 'pagelines_before_branding_icons', 'branding' ); // Hook 
					
				printf('<div class="icons" style="bottom: %spx; right: %spx;">', intval(pagelines_option('icon_pos_bottom')), pagelines_option('icon_pos_right'));
					
					pagelines_register_hook( 'pagelines_branding_icons_start', 'branding' ); // Hook 
					
					if(pagelines_option('rsslink'))
						printf('<a target="_blank" href="%s" class="rsslink"></a>', apply_filters( 'pagelines_branding_rssurl', get_bloginfo('rss2_url') ));
					
					if(VPRO) {
						if(pagelines_option('twitterlink'))
							printf('<a target="_blank" href="%s" class="twitterlink"></a>', pagelines_option('twitterlink'));
					
						if(pagelines_option('facebooklink'))
							printf('<a target="_blank" href="%s" class="facebooklink"></a>', pagelines_option('facebooklink'));
						
						if(pagelines_option('linkedinlink'))
							printf('<a target="_blank" href="%s" class="linkedinlink"></a>', pagelines_option('linkedinlink'));
						
						if(pagelines_option('youtubelink'))
							printf('<a target="_blank" href="%s" class="youtubelink"></a>', pagelines_option('youtubelink'));
						
						pagelines_register_hook( 'pagelines_branding_icons_end', 'branding' ); // Hook 
				
					}
					
			printf('</div></div>');
					
			pagelines_register_hook( 'pagelines_after_branding_wrap', 'branding' ); // Hook
				
		}

	function section_head(){}

	function section_scripts() {}

	function section_options() {
	
	}

}

/*
	End of section class
*/