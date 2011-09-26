<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
	Workswith: morefoot
	Edition: Pro
*/
class PageLinesTwitterBar extends PageLinesSection {

	function section_template() { 

		if( !pagelines('twittername') ) :
			echo '<div class="tbubble">';
			_e('Set your Twitter account name in your settings to use the TwitterBar Section.</div>', 'pagelines');
			return;
		endif;
		// Fetch latest tweet from db
	
		echo '<div class="tbubble">';
		echo '<span class="twitter">';	
			pagelines_register_hook( 'pagelines_before_twitterbar_text', $this->id ); // Hook			
			
		echo make_clickable( pagelines_get_tweets( pagelines('twittername'), true ) );	
		// close the tweet and div.
		echo '&nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/' . pagelines('twittername') . '">' . pagelines('twittername') . '</a></span></div>';
		}
}
/*
	End of section class
*/