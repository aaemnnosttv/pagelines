<?php
/*
	Section: PageLines Splash
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Splash for landing pages. Includes image, text, logo and nav.
	Class Name: PageLinesSplash

*/

class PageLinesSplash extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('PageLines Splash', 'pagelines');
		$id = 'pagelines_splash';
	
		
		$default_settings = array(
			'description' 	=> 'Product or service splash. Great for communicating key benefits quickly on your homepage.',
			'workswith' 	=> array('content'),
			'icon'			=> $registered_settings['base_url'].'/splash.png',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		parent::__construct($name, $id, $settings);
   }

	
	function section_styles() {
	
		wp_register_style( 'splash', $this->base_url . '/splash.css', array(), CORE_VERSION, 'screen');
	 	wp_enqueue_style( 'splash' );
		
	}
	
	function section_template() {  ?>
	
	<div class="splash_head">
		<div class="splash_branding">
			<?php pagelines_main_logo();?>
		</div>
		<div class="splash_nav">
		</div>
	</div>
	<div class="splash_area fix">	
		<div class="splash_left">
			<div class="splash_pad">
				<h1>A Better Way To&nbsp;Build Websites</h1>
				<p>Use PageLines Drag &amp; Drop Framework and WordPress to build professional websites faster, easier and better than ever before.</p>
				<a class="splash_button" href="#"><span class="splash_button_pad">See Plans <span class="spl">&amp;</span> Pricing</span></a>
			</div>
		</div>
		<img class="splash_feature" src="<?php echo $this->base_url;?>/test.png" />	
	</div>
		
<?php 	}

}

/*
	End of section class
*/