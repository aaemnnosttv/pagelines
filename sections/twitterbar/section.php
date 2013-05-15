<?php
/*
	Section: TwitterBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Class Name: PageLinesTwitterBar
	Workswith: morefoot, footer
	Edition: Pro
*/

/**
 * Twitter Feed Section
 *
 * Uses pagelines_get_tweets() to display the latest tweet in the morefoot area.
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PageLinesTwitterBar extends PageLinesSection {


	function section_styles() {

		wp_enqueue_script( 'twitter', $this->base_url.'/twitter.js', array( 'pagelines-bootstrap-all' ), null, true );
	}
	/**
	* Section template.
	*/
	function section_template() {

		if( !pagelines('twittername') ) :
			printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', __('Set your Twitter account name in your settings to use the TwitterBar Section.', 'pagelines'));

			return;
		endif;

		$account = ploption('twittername');

		$tweet_data = pagelines_get_tweets( $account, true );

		if( ! is_array( $tweet_data ) && '' == $tweet_data )
			$tweet_data = __( 'Unknown Twitter error.', 'pagelines' );

		if( isset( $tweet_data['text'] ) && isset( $tweet_data['user']['id'] ) )
			$twitter = sprintf(
				'<span class="twitter">%s &nbsp;&mdash;&nbsp;<a class="twitteraccount" href="http://twitter.com/#!/%s" %s>%s</a></span>',
				pagelines_tweet_clickable( $tweet_data['text'] ),
				$account,
				sprintf( 'rel="twitterpopover" data-img="https://api.twitter.com/1/users/profile_image?user_id=%s&size=bigger" data-original-title="@%s"', $tweet_data['user']['id'], $account ),
				$account
			);
		else
			$twitter = sprintf( '<span class="twitter">%s</span>', $tweet_data );

		printf('<div class="tbubble"><div class="tbubble-pad">%s</div></div>', $twitter);
	}
}