<?php
/*
	Section: NavBar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive and sticky navigation bar for your website.
	Class Name: PLNavBar
	Workswith: header
	Compatibility: 2.2
	Cloning: false
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLNavBar extends PageLinesSection {

	var $default_limit = 2;

	function section_head() {
		?>
			<!--[if IE 8]>
				<style>
					.nav-collapse.collapse {
						height: auto;
						overflow: visible;
					}
				</style>
			<![endif]-->
		<?php
	}

	function section_persistent() {


		$header_options = array(
			'navbar_fixed' => array(
					'default'	=> false,
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
					'title'		=> __( 'Enable Fixed Navigation Bar', 'pagelines' ),
					'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
					'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.', 'pagelines' )
				),
			'navbar_logo' => array(
					'default'	=> $this->base_url.'/logo.png',
					'version'	=> 'pro',
					'type'		=> 'image_upload',
					'inputlabel'=> __( 'Fixed NavBar Logo', 'pagelines' ),
					'title'		=> __( 'Fixed NavBar - NavBar Logo', 'pagelines' ),
					'shortexp'	=> __( 'Applies a fixed navigation bar to the top of your site', 'pagelines' ),
					'exp'		=> __( 'Use this feature to add the NavBar section as a fixed navigation bar on the top of your site.<br/><br/><strong>Notes:</strong> <br/>1. Only visible in Fixed Mode.<br/>2. Image Height is constricted to a maximum 29px.', 'pagelines' )
				),

			'navbar_multi_option_theme' => array(
				'default' => '',
				'type' => 'multi_option',
				'selectvalues'=> array(

					'fixed_navbar_theme' => array(
							'default'		=> 'black-trans',
							'type' 			=> 'select',
							'inputlabel' 	=> __( 'Fixed NavBar - Select Theme', 'pagelines' ),
							'selectvalues'	=> array(
								'black-trans'	=> array( 'name'	=> __( 'Black Transparent (Default)', 'pagelines' ) ),
								'blue'			=> array( 'name'	=> __( 'Blue', 'pagelines' ) ),
								'grey'			=> array( 'name'	=> __( 'Light Grey', 'pagelines' ) ),
								'orange'		=> array( 'name'	=> __( 'Orange', 'pagelines' ) ),
								'red'			=> array( 'name'	=> __( 'Red', 'pagelines' ) ),
							),
						),
					'navbar_theme' => array(
							'default'		=> 'black-trans',
							'type' 			=> 'select',
							'inputlabel' 	=> __( 'Standard NavBar - Select Theme', 'pagelines' ),
							'selectvalues'	=> array(
								'black-trans'	=> array( 'name'	=> __( 'Black Transparent (Default)', 'pagelines' ) ),
								'blue'			=> array( 'name'	=> __( 'Blue', 'pagelines' ) ),
								'grey'			=> array( 'name'	=> __( 'Light Grey', 'pagelines' ) ),
								'orange'		=> array( 'name'	=> __( 'Orange', 'pagelines' ) ),
								'red'			=> array( 'name'	=> __( 'Red', 'pagelines' ) ),
							),
						),
				),
				'title'					=> __( 'NavBar and Fixed NavBar Theme', 'pagelines' ),
				'shortexp'				=> __( 'Select the color and theme of the NavBar', 'pagelines' ),
				'exp'					=> __( 'The NavBar comes with several color options. Select one to automatically configure.', 'pagelines' )

			),
			'navbar_multi_option_menu' => array(
				'default' => '',
				'type' => 'multi_option',
				'selectvalues'=> array(

					'fixed_navbar_menu' => array(
							'default'		=> 'black-trans',
							'type' 			=> 'select_menu',
							'inputlabel' 	=> __( 'Fixed NavBar - Select Menu', 'pagelines' ),
						),
					'navbar_menu' => array(
							'default'		=> 'black-trans',
							'type' 			=> 'select_menu',
							'inputlabel' 	=> __( 'Standard NavBar - Select Menu', 'pagelines' ),
						),
				),
				'title'					=> __( 'NavBar and Fixed NavBar Menu', 'pagelines' ),
				'shortexp'				=> __( 'Select the WordPress Menu for the NavBar(s)', 'pagelines' ),
				'exp'					=> __( 'The NavBar uses WordPress menus. Select one for use.', 'pagelines' )

			),

			'navbar_multi_check' => array(
				'default' => '',
				'type' => 'check_multi',
				'selectvalues'=> array(

					'navbar_enable_hover'		=>	array(
						'inputlabel'			=> __( 'Activate dropdowns on hover.', 'pagelines' ),
					),

					'fixed_navbar_alignment'	=> array(
							'inputlabel'		=> __( 'Fixed NavBar - Align Menu Right? (Defaults Left)', 'pagelines' ),
						),
					'fixed_navbar_hidesearch'	=> array(
							'inputlabel'		=> __( 'Fixed NavBar - Hide Searchform?', 'pagelines' ),
						),
					'navbar_alignment'			=> array(
							'inputlabel'		=> __( 'Standard NavBar - Align Menu Right? (Defaults Left)', 'pagelines' ),
						),
					'navbar_hidesearch'			=> array(
							'inputlabel'		=> __(  'Standard NavBar - Hide Searchform?', 'pagelines' ),
						),
				),
				'inputlabel'			=> __( 'Configure Options for NavBars', 'pagelines' ),
				'title'					=> __( 'NavBar and Fixed NavBar Configuration Options', 'pagelines' ),
				'shortexp'				=> __( 'Control various appearance options for the NavBars', 'pagelines' ),
				'exp'					=> ''
			),
			'navbar_title' => array(
					'type' 		=> 'text',
					'inputlabel'=> __( 'NavBar Title', 'pagelines' ),
					'title'		=> __( 'NavBar Title', 'pagelines' ),
					'shortexp'	=> __( 'Applies text to NavBar on small screens. Not available on Fixed NavBar', 'pagelines' ),
					'exp'		=> __( 'Add text to the NavBar to serve as a title, but only displayed on small screens.', 'pagelines' ),
			),


		);

		$option_args = array(

			'name'		=> 'NavBar',
			'array'		=> $header_options,
			'icon'		=> $this->icon,
			'position'	=> 6
		);

		pl_add_options_page( $option_args );

		//pl_global_option( array( 'menu' => 'header_and_footer', 'options' => $header_options, 'location' => 'top' ) );


		if(ploption('navbar_fixed')){

			build_passive_section( array( 'sid' => $this->class_name ) );

			add_action( 'pagelines_before_page', function() { echo pl_source_comment("Fixed NavBar Section"); } );
			add_action( 'pagelines_before_page', array( &$this,'passive_section_template' ), 10, 2);
			pagelines_add_bodyclass( 'navbar_fixed' );
		}



	}

	/**
	 * Load styles and scripts
	 */
	function section_styles() {

		wp_enqueue_script( 'navbar', $this->base_url.'/navbar.js', array( 'jquery' ) );
	}

	function before_section_template( $location = '' ) {

		$format = ( $location == 'passive' ) ? 'open' : 'standard';
		$this->special_classes = ( $location == 'passive' ) ? ' fixed-top' : '';
		$this->settings['format'] = $format;

	}

	/**
	* Section template.
	*/
  	function section_template( $clone_id = null, $location = '' ) {

		$passive = ( $location == 'passive' ) ? true : false;

		// if fixed mode
		if( $passive ){

			$width_class = 'navbar-full-width';
			$content_width_class = 'content';
			$theme = ( ploption('fixed_navbar_theme', $this->oset ) ) ? ploption( 'fixed_navbar_theme', $this->oset ) : false;
			$align = ( ploption( 'fixed_navbar_alignment', $this->oset ) ) ? ploption( 'fixed_navbar_alignment', $this->oset ) : false;
			$hidesearch = ( ploption( 'fixed_navbar_hidesearch', $this->oset ) ) ? ploption( 'fixed_navbar_hidesearch', $this->oset ) : false;
			$menu = ( ploption( 'fixed_navbar_menu', $this->oset ) ) ? ploption( 'fixed_navbar_menu', $this->oset ) : null;

		} else {

			$width_class = 'navbar-content-width';
			$content_width_class = '';
			$theme = ( ploption( 'navbar_theme', $this->oset ) ) ? ploption( 'navbar_theme', $this->oset ) : false;
			$align = ( ploption('navbar_alignment', $this->oset ) ) ? ploption( 'navbar_alignment', $this->oset ) : false;
			$hidesearch = ( ploption( 'navbar_hidesearch', $this->oset ) ) ? ploption( 'navbar_hidesearch', $this->oset ) : false;
			$menu = ( ploption( 'navbar_menu', $this->oset ) ) ? ploption( 'navbar_menu', $this->oset ) : null;
		}

		$pull = ( $align ) ? 'right' : 'left';
		$align_class = sprintf( 'pull-%s', $pull );
		$theme_class = ( $theme ) ? sprintf( ' pl-color-%s', $theme ) : ' pl-color-black-trans';
		$theme_class = ( ploption( 'navbar_enable_hover', $this->oset ) ) ? $theme_class . ' plnav_hover' : $theme_class;
		$brand = ( ploption( 'navbar_logo', $this->oset ) || ploption( 'navbar_logo', $this->oset ) != '') ? sprintf( '<img src="%s" alt="%s" />', ploption( 'navbar_logo', $this->oset ), esc_attr( get_bloginfo('name') ) ) : sprintf( '<h2 class="plbrand-text">%s</h2>', get_bloginfo( 'name' ) );
		$navbartitle = ploption( 'navbar_title', $this->oset );

		?>
		<div class="navbar fix <?php echo $width_class.' '.$theme_class; ?>">
		  <div class="navbar-inner <?php echo $content_width_class;?>">
			<div class="navbar-content-pad fix">
				<?php
					if($navbartitle)
					printf( '<span class="navbar-title">%s</span>',$navbartitle );
				?>
			  <a href="javascript:void(0)" class="nav-btn nav-btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </a>
				<?php if($passive):
					printf( '<a class="plbrand" href="%s" title="%s">%s</a>',
						esc_url( home_url() ),
						esc_attr( get_bloginfo('name') ),
						apply_filters('navbar_brand', $brand)
					 );
				endif; ?>
					<div class="nav-collapse collapse">
			   <?php 	if( !$hidesearch )
							get_search_form();
						if ( is_array( wp_get_nav_menu_items( $menu ) ) || has_nav_menu( 'primary' ) ) {
						wp_nav_menu(
							array(
								'menu_class'		=> 'font-sub navline pldrop ' . $align_class,
								'menu'				=> $menu,
								'container'			=> null,
								'container_class'	=> '',
								'depth'				=> 3,
								'fallback_cb'		=> ''
							)
						);
						} else {
							$this->nav_fallback( $align_class );
						}
		?>
					</div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
	<?php
	}

	function nav_fallback( $align_class ) {

		printf( '<ul id="menu-main" class="font-sub navline pldrop %s">', $align_class );
		wp_list_pages( 'title_li=&sort_column=menu_order&depth=2' );
		echo '</ul>';
	}
}