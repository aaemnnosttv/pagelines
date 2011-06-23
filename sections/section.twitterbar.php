<?php
/*

	Section: TwitterBar
	Author: Andrew Powers
	Author URI: http://www.pagelines.com
	Description: Loads twitter feed into the site footer
	Version: 1.0.0
	Class Name: PageLinesTwitterBar
*/

class PageLinesTwitterBar extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Twitter Bar', 'pagelines');
		$id = 'twitterbar';
	
		
		$default_settings = array(
			'type' 			=> 'standard',
			'description' 	=> 'Displays your latest twitter post. <strong>"Twitter for WordPress" plugin is required.</strong>',
			'workswith' 	=> array('morefoot'),
			'folder' 		=> '', 
			'init_file' 	=> 'twitterbar.php',
			'icon'			=> PL_ADMIN_ICONS . '/twitter.png',
			'version'		=> 'pro', 
			'cloning'		=> true
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
		?>
		<?php if(pagelines('twittername') ):?>
			<div class="tbubble">
				<?php if(function_exists('twitter_messages') && pagelines('twittername')):?>
					<span class="twitter">
						<?php pagelines_register_hook( 'pagelines_before_twitterbar_text', $this->id ); // Hook ?>
						 "<?php twitter_messages(pagelines('twittername'), 1, false, false, '', false, false, false); ?>" &mdash;&nbsp;<a class="twitteraccount" href="http://www.twitter.com/<?php echo pagelines('twittername');?>"><?php echo pagelines('twittername');?></a>
					</span>
				<?php else:?>
					<span class="twitter"><?php _e('Please install and activate the "Twitter for WordPress" plugin to use this section.', 'pagelines');?></span>
				<?php endif;?>
			</div>
		<?php else:?>
			<div class="tbubble">
			<?php _e('Set your Twitter account name in your settings to use the TwitterBar Section.', 'pagelines');?>
			</div>
		<?php endif;?>
		<?php
	}

}

/*
	End of section class
*/