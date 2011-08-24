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
			'icon'			=> $registered_settings['base_url'].'/water.png',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		parent::__construct($name, $id, $settings);
   }

	
	function section_persistent(){
			register_nav_menus( array( 'splash_nav' => __( 'Splash Page Navigation', 'pagelines' ) ) );
	}
	
	function section_styles() {
	
		wp_register_style( 'splash', $this->base_url . '/splash.css', array(), CORE_VERSION, 'screen');
	 	wp_enqueue_style( 'splash' );
	
		
	}
	
	function section_template() {  ?>
	
	<div class="splash_head fix">
		<div class="splash_branding">
			<?php pagelines_main_logo();?>
		</div>
		<div class="splash_nav">
			
			<?php
			
				wp_nav_menu( array('menu_class'  => 'tabbed-list font-sub', 'theme_location'=>'splash_nav','depth' => 1,  'fallback_cb'=>'blank_nav_fallback') );
			?>
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



	function after_section_template(){ ?>
		
		<div class="splash_shelf">
		
		</div>
		
	<?php  }

}

/*
	End of section class
*/