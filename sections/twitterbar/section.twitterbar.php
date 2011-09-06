<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
*/
class PageLinesTwitterBar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Twitter Bar', 'pagelines');
		$id = 'twitterbar';
	
		
		$default_settings = array(
			'type' 			=> 'standard',
			'description' 	=> 'Displays your latest twitter post.',
			'workswith' 	=> array('morefoot'),
			'folder' 		=> '', 
			'init_file' 	=> 'twitterbar.php',
			'icon'			=> PL_ADMIN_ICONS . '/twitter.png',
			'version'		=> 'pro'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }

	function section_template() { 

		if( !pagelines('twittername') ) :
			echo '<div class="tbubble">';
			_e('Set your Twitter account name in your settings to use the TwitterBar Section.</div>', 'pagelines');
			return;
		endif;
		// Fetch latest tweet from db
	
		echo '<div class="tbubble">';
		echo '<span class="twitter">"';	
			pagelines_register_hook( 'pagelines_before_twitterbar_text', $this->id ); // Hook			
			
		echo make_clickable( pagelines_get_tweets( pagelines('twittername'), true ) );	
		// close the tweet and div.
		echo '&nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/' . pagelines('twittername') . '">' . pagelines('twittername') . '</a></span></div>';
		}
}
/*
	End of section class
*/