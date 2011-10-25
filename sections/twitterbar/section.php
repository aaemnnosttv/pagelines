<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
	Workswith: morefoot
	Tags: internal
	Edition: Pro
*/
class PageLinesTwitterBar extends PageLinesSection {

	function section_template() { 

		if( !pagelines('twittername') ) :
			printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', __('Set your Twitter account name in your settings to use the TwitterBar Section.</div>', 'pagelines'));

			return;
		endif;
	
		$account = ploption('twittername');
	
		$twitter = sprintf(
			'<span class="twitter">%s &nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/%s">%s</a></span>',
			make_clickable( pagelines_get_tweets( $account, true ) ), 
			$account,
			$account
		);
	
		printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', $twitter);
		
		
		
	}
}
/*
	End of section class
*/