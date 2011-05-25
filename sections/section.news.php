<?php
/*

	Section: News Banner
	Author: Adam Munns
	Description: Shows a banner with a news style ticker in it
	Version: 1.0.0
	
*/

class PageLinesNews extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('News Banner Section', 'pagelines');
		$id = 'news';
	
		
		$default_settings = array(
			'description' 	=> 'News Banner - A banner displaying recent news.',
			'workswith' 	=> array('content'),
			'folder' 		=> 'wp', 
			'init_file' 	=> 'news.php', 
			'icon'			=> PL_ADMIN_ICONS . '/newspaper.png', 
			'version'		=> 'pro'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
	   parent::__construct($name, $id, $settings);    
   }

		
 	function section_template() { ?>
	<div class="ticker-wrap">
		<span class="news_title">
			<?php e_pagelines( 'pagelines_news_title', __('Latest News', 'pagelines') );?><strong>|</strong>     
		</span>
		<?php if(function_exists('insert_newsticker') && VPRO):
		 	insert_newsticker(); 
		elseif(function_exists('ticker_use_rss')):
		 	ticker_use_rss();
		else:
		 _e('Please activate the "News-Ticker" Plugin to use this section.', 'pagelines');
		endif;?>
	</div>
	<?php }



function section_options($optionset = null, $location = null) {

	if($optionset == 'template_setup' && $location == 'bottom'){
		return array(
				
				
				'pagelines_news_title' => array(
						'version' => 'pro',
						'default' => '',
						'type' => 'text',
						'inputlabel' => 'News Ticker Title Text',
						'title' => $this->name.' Title Text ',						
						'shortexp' => 'The title of your news ticker (used with the News-Ticker Plugin).',
						'exp' => 'This text will be used as the title to your news ticker and can display posts, comments, or rss'

				),
			);

	}

}


}


/*
	End of section class
*/