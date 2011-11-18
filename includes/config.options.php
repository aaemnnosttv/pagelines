<?php

/**
 * 
 *
 *  Options Array
 *
 *
 *  @package PageLines Options
 *  @subpackage Options
 *  @since 2.0.b3
 *
 */

class PageLinesOptionsArray {


	/**
	 * Construct
	 */
	function __construct() {
		
		if(!ploption('hide_pagelines_introduction') && VPRO)
			$this->options['_welcome'] = $this->welcome();
		
		$this->options['website_setup'] = $this->website_setup();
		$this->options['layout_editor'] = $this->layout_editor();
		$this->options['color_control'] = $this->color_control();
		$this->options['typography'] = $this->typography();
		$this->options['header_and_footer'] = $this->header_footer();
		$this->options['blog_and_posts'] = $this->blog_posts();
		$this->last_options['advanced'] 	= $this->advanced();
		$this->last_options['custom_code'] = $this->custom_code();
	}

	function website_setup(){
		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/compass.png',
			'email_capture'	=> array(
				'default'		=> '',
				'version'		=> 'free',
				'type' 			=> 'email_capture',
				'inputlabel' 	=> __( 'Email Address', 'pagelines' ),
				'title'			=> __( 'Email Updates', 'pagelines' ),						
				'shortexp' 		=> __( 'Optionally sign up for email updates and notifications', 'pagelines' ),
				'exp' 			=> __( 'Adding your email here will allow us to send you email notifications about updates and new software from PageLines.', 'pagelines' )
			),
			'pagelines_custom_logo' => array(
				'default' 		=> PL_IMAGES.'/logo.png',
				'default_free'	=> PL_IMAGES.'/logo-platform.png',
				'type' 			=> 'image_upload',
				'imagepreview' 	=> '270',
				'inputlabel' 	=> __( 'Upload custom logo', 'pagelines' ),
				'title'			=> __( 'Custom Header Image', 'pagelines' ),						
				'shortexp' 		=> __( 'Input Full URL to your custom header or logo image', 'pagelines' ),
				'exp' 			=> __( 'Optional way to replace <strong>heading</strong> and <strong>description</strong> text for your website ' . 
						    		'with an image.', 'pagelines' )
			),
			'pagelines_favicon'		=> array(
				'default' 		=> 	PL_ADMIN_IMAGES . "/favicon-pagelines.ico",
				'inputlabel'	=> 'Upload Favicon (16px by 16px)',
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'16',
				'title' 		=> 	__( 'Favicon Image', 'pagelines' ),						
				'shortexp' 		=> 	__( 'Input Full URL to favicon image (<strong>favicon.ico</strong> image file)', 'pagelines' ),
				'exp' 			=> 	__( 'Enter the full URL location of your custom <strong>favicon</strong> which is visible in ' .
							'browser favorites and tabs.<br/> <strong>Must be .png or .ico file - 16px by 16px</strong>.', 'pagelines' )
			),		
			'twittername' => array(
				'default' 		=> '',
				'type' 			=> 'text',
				'inputlabel' 	=> __( 'Your Twitter Username', 'pagelines' ),
				'title' 		=> __( 'Twitter Integration', 'pagelines' ),
				'shortexp'	 	=> __( 'Places your Twitter feed in your site', 'pagelines' ),
				'exp' 			=> __( 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.', 'pagelines' )
			),
			'pl_login_image'	=> array(
				'version' 		=> 'pro',
				'default' 		=> PL_ADMIN_IMAGES . "/login-pl.png",
				'type' 			=> 	'image_upload',
				'inputlabel'	=> 'Upload Icon (80px Height)',
				'imagepreview' 	=> 	'60',
				'title' 		=> __( 'Login Page Image', 'pagelines' ),						
				'shortexp' 		=> __( "The image to use on your site's login page", 'pagelines' ),
				'exp'			=> __( 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.', 'pagelines' )
			),
			'pagelines_touchicon'	=> array(
				'version' 		=> 'pro',
				'default' 		=> '',
				'inputlabel'	=> 'Upload Icon (57px by 57px)',
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'60',
				'title' 		=> __( 'Apple Touch Image', 'pagelines' ),						
				'shortexp' 		=> __( 'Input Full URL to Apple touch image (.jpg, .gif, .png)', 'pagelines' ),
				'exp'			=> __( 'Enter the full URL location of your Apple Touch Icon which is visible when ' .
						  'your users set your site as a <strong>webclip</strong> in Apple Iphone and ' . 
						  'Touch Products. It is an image approximately 57px by 57px in either .jpg, ' .
						  '.gif or .png format.', 'pagelines' )
			),
		
			'sidebar_no_default' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> __( 'Hide Sidebars When Empty (no widgets)', 'pagelines' ),
					'title'		=> __( 'Remove Default Sidebars When Empty', 'pagelines' ),
					'shortexp'	=> __( 'Hide default sidebars when sidebars have no widgets in them', 'pagelines' ),
					'exp'		=> __( 'This allows you to remove sidebars completely when they have no widgets in them.', 'pagelines' )
			),
			'sidebar_wrap_widgets' => array(
					'default' 	=> 'top',
					'version'	=> 'pro',
					'type' 		=> 'select',
					'selectvalues'	=> array(
						'top'		=> array('name' => __( 'On Top of Sidebar', 'pagelines') ),
						'bottom'	=> array('name' => __( 'On Bottom of Sidebar', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Sidebar Wrap Widgets Position', 'pagelines' ),
					'title' 	=> __( 'Sidebar Wrap Widgets', 'pagelines' ),
					'shortexp' 	=> __( 'Choose whether to show the sidebar wrap widgets on the top or bottom of the sidebar', 'pagelines' ),
					'exp' 		=> __( 'You can select whether to show the widgets that you place in the sidebar wrap template in either the top or the bottom of the sidebar.', 'pagelines' )
			),
		
		);
		
		if ( get_option( 'pagelines_email_sent') ) 
			unset($a['email_capture']);
		
		return apply_filters('pagelines_options_website_setup', $a);
	}
	
	/**
	 * Layout Editor Interface & Options
	 *
	 * @since 2.0.0
	 */
	function layout_editor(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/layout.png',
			'layout_handling' => array(
				'default'		=> 'pixels',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'How should layout be handled?', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layout-modes.png',
				'height'		=> '88px', 
				'width'			=> '130px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'pixels'		=> array( 'name' => __( 'Responsive with Pixel Width', 'pagelines' ), 'offset' => '0px 0px' ), 
					'percent'		=> array( 'name' => __( 'Responsive with Percent Width', 'pagelines' ), 'offset' => '0px -88px' ), 
					'static'		=> array( 'name' => __( 'Static with Pixel Width', 'pagelines' ), 'offset' => '0px -176px' )
				),
				'title'		=> __( 'Layout Handling', 'pagelines' ),						
				'shortexp'	=> __( 'Select between responsive vs. static; pixel based or percentage based layout', 'pagelines' ),
				'exp'		=> __( "Responsive layout adjusts to the size of your user's browser window; static is fixed width. Use this option to switch between the pixel based site width and a percentage based one.", 'pagelines' )
			),
			'layout_default' => array(
				'default' 	=> "one-sidebar-right",
				'type' 		=> 'layout_select',
				'title' 	=> __( 'Default Layout Mode', 'pagelines' ),
				'inputlabel'	=> __( 'Select Default Layout', 'pagelines' ),	
				'layout' 	=> 'interface',						
				'shortexp' 	=> __( 'Select your default layout mode, this can be changed on individual pages.<br />Once selected, you can adjust the layout in the Layout Dimension Editor', 'pagelines' ),
				'exp' 		=> __( 'The default layout for pages and posts on your site. Dimensions can be changed using the Layout Dimension Editor.', 'pagelines' ),
				'docslink'	=> 'http://www.pagelines.com/docs/editing-layout'
			),
			'layout' => array(
				'default'	=> 'one-sidebar-right',
				'type'		=> 'layout',
				'layout'	=> 'interface',
				'title'		=> __( 'Layout Dimension Editor', 'pagelines' ),						
				'shortexp'	=> __( 'Configure the default layout for your site which is initially selected in the Default Layout Mode option in Global Options. <br/>This option allows you to adjust columns and margins for the default layout', 'pagelines' ),
			), 
			
			'resetlayout' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Layout", 'pagelines'),
				'type' 		=> 'reset',
				'callback'	=> 'reset_layout_to_default',
				'title' 	=> __( 'Reset Layout To Default', 'pagelines' ),	
				'layout'	=> 'full',					
				'shortexp'	=> __( 'Changes layout mode and dimensions back to default', 'pagelines' ),
			)
		);
		
		return apply_filters('pagelines_options_layout_editor', $a);
		
	}
	
	/**
	 * Design Control and Color Options
	 *
	 * @since 2.0.0
	 */
	function color_control(){

		$a = array(	
			'icon'			=> PL_ADMIN_ICONS.'/color.png',
			'site_design_mode'	=> array(
				'version'	=> 'pro',
				'default'	=> 'full_width',
				'type'		=> 'graphic_selector',
				'showname'	=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-design-modes.png',
				'height'		=> '88px', 
				'width'			=> '130px',
				'layout' 		=> 'interface',	
				'selectvalues'	=> array(
					'full_width'	=> array('name' => __( "Full-Width Sections", 'pagelines' ), 'offset' => '0px 0px'),
					'fixed_width'	=> array('name' => __( "Content Width Page", 'pagelines' ), 'version' => 'pro', 'offset' => '0px -88px')
				), 
				'inputlabel'	=> __( 'Site Design Mode', 'pagelines' ),
				'title'		=> __( 'Site Design Mode', 'pagelines' ),						
				'shortexp'	=> __( 'The basic HTML layout structure for color and background effects', 'pagelines' ),
				'exp'		=> __( 'This option controls how the basic HTML layout is built. Different layout structures change the way background colors and images behave.<ul><li><strong>Full-Width Mode</strong> Full width design mode allows you to have aspects of your site that are the full-width of your screen; while others are the width of the content area.</li><li><strong>Fixed-Width Mode</strong> Fixed width design mode creates a fixed with <strong>page</strong> that can be used as the area for your design.  You can set a background to the page; and the content will have a seperate <strong>fixed-width</strong> background area (i.e. the width of the content).</li></ul>', 'pagelines' ),
			),	
			'page_colors'		=> array(
				'title' 	=> __( 'Basic Layout Colors', 'pagelines' ),						
				'shortexp' 	=> __( 'The Main Layout Colors For Your Site', 'pagelines' ),
				'exp' 		=> __( 'Use these options to quickly setup the main layout colors for your site.  You can use these options to build custom sites very quickly, or to quickly prototype a design then refine through custom CSS.<br/><br/><strong>Notes:</strong> <ol><li>To make the background transparent, you can leave the options blank (delete text).</li>  <li>Further customize and refine colors through custom CSS or plugins
</li></ol>', 'pagelines' ),
				'type' 		=> 'color_multi',
				'layout'	=> 'full',
				'selectvalues'	=> array(
					'bodybg'	=> array(				
						'default' 		=> '#FFFFFF',
						'css_prop'		=> 'background-color',
						'flag'			=> 'set_default',
						'cssgroup'		=> 'bodybg',
						'inputlabel' 	=> __( 'Body Background', 'pagelines' ),
						
					),
					'pagebg'		=> array(				
						'default' 		=> '',
						'cssgroup'		=>	'pagebg',
						'flag'			=> 'blank_default',
						'css_prop'		=> 'background-color',
						'inputlabel' 	=> __( 'Page Background (Optional)', 'pagelines' ),
						),
					'contentbg'	=> array(				
						'version'		=> 'pro',
						'default' 		=> '',
						'cssgroup'		=>	'contentbg',
						'flag'			=> 'blank_default',
						'css_prop'		=> 'background-color',
						'id'			=> 'the_bg',
						'inputlabel' 	=> __( 'Content Background (Optional)', 'pagelines' ),
						'math'		=> array(
								array( 
									'id'		=> 'bg', // use this for getting stored background color
									'mode' 		=> 'contrast', 
									'cssgroup' 	=> 'border_layout', 
									'css_prop' 	=> 'border-color', 
									'diff' 		=> '8%', 
									'depends' 	=> pl_background_cascade()
								),
								array(
									'mode' 		=> 'darker', 
									'cssgroup' 	=> 'border_layout_darker', 
									'css_prop' 	=> 'border-color', 
									'depends' 	=> pl_background_cascade()
								), 
								array(
									'mode' 		=> 'lighter', 
									'cssgroup' 	=> 'border_layout_lighter', 
									'css_prop' 	=> 'border-color', 
									'depends' 	=> pl_background_cascade()
								),
								
								array( 
									'id'		=> 'box_bg',
									'mode' 		=> 'contrast', 
									'cssgroup' 	=> 'box_color_primary', 
									'css_prop' 	=> 'background-color', 
									'diff' 		=> '5%', 
									'depends' 	=> pl_background_cascade(),
									'math'		=> array(
										array( 'id' => 'text_box', 'mode' => 'contrast', 'cssgroup' => 'text_box', 'css_prop' => 'color', 'diff' => '65%', 'math' => array(
											array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_box') ),
										)),
										array( 'id' => 'primary_border', 'mode' => 'contrast', 'cssgroup' => 'border_primary', 'css_prop' => 'border-color', 'diff' => '8%', 'math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_primary_shadow', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '10%'),
											array( 'mode' => 'lighter', 'cssgroup' => 'border_primary_highlight', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
										)),
										array( 'mode' => 'darker', 'cssgroup' => 'border_primary_darker', 'css_prop' => 'border-color', 'diff' => '10%' ),
										array( 'mode' => 'lighter', 'cssgroup' => 'border_primary_lighter', 'css_prop' => 'border-color', 'diff' => '10%' ),
										array( 'id' => 'box_bg_secondary', 'mode' => 'contrast', 'cssgroup' => 'box_color_secondary', 'css_prop' => array('background-color'), 'diff' => '3%', 'math' => array(
											array( 'id' => 'text_box_second', 'mode' => 'contrast', 'cssgroup' => 'text_box_secondary', 'css_prop' => array('color'), 'diff' => '65%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-color'), 'diff' => '5%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
											
										)),
										array( 'id' => 'box_bg_tertiary', 'mode' => 'contrast', 'cssgroup' => 'box_color_tertiary', 'css_prop' => array('background-color'), 'diff' => '6%','math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_tertiary', 'css_prop' => array('border-color'), 'diff' => '10%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_tertiary', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
										)), 
										
									)
									
								),
								
								array( 'mode' => 'lighter', 'cssgroup' => 'box_color_lighter', 'css_prop' => 'background-color'),
							)
						),
				),
			),
			'text_colors'		=> array(
				'title' 		=> __( 'Page Text Colors', 'pagelines' ),						
				'shortexp' 		=> __( 'Control The Color Of Text Used Throughout Your Site', 'pagelines' ),
				'exp' 			=> __( 'These options control the colors of the text throughout the page or content area of your site.<br/><br/>Certain text types are designed to contrast with different box elements and are meant to be used with hover effects.<br/><br/>Experiment to find exactly how colors are combined with text on your site.', 'pagelines' ),
				'type' 			=> 'color_multi',
				'layout'		=> 'full',
				'selectvalues'	=> array(
					'text_primary' => array(		
						'id'			=> 'text_primary',
						'default' 		=> '#000000',
						'flag'			=> 'set_default',
						'cssgroup'		=>	'text_primary',
						'inputlabel' 	=> __( 'Primary Text', 'pagelines' ),
						'math'		=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'text_secondary', 'css_prop' => 'color', 'diff' => '65%'),
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_primary', 'text_secondary', 'text_tertiary') ),
						)
					),
					'headercolor'	=> array(		
						'default' 	=> '#000000',
						'cssgroup'	=> 'headercolor',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Text Headers', 'pagelines' ),
						'math'		=> array(
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'headercolor'),
						)
					),
					
					'linkcolor' => array(
						'default'		=> '#225E9B',
						'cssgroup'		=>	'linkcolor',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Primary Links', 'pagelines' ),	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(),  'cssgroup' => 'linkcolor_hover', 'css_prop' => 'color', 'diff' => '80%'),	
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'linkcolor'),
						)				
					),
					'footer_text' => array(
						'default'		=> '#AAAAAA',
						'cssgroup'		=>	'footer_highlight',
						'flag'			=> 'set_default',
						'inputlabel' 	=> __( 'Footer Text', 'pagelines' ),	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_body_bg(),  'cssgroup' => 'footer_text', 'css_prop' => 'color', 'diff' => '66%'),
							array( 'mode' => 'shadow', 'mixwith' => pl_body_bg(), 'cssgroup' => array('footer_text', 'footer_highlight') ),
						)					
					),
				),
			),
			'disable_text_shadow' => array(
				'title' 		=> __( 'Disable Text Emboss Effect (Text Shadow)', 'pagelines' ),						
				'shortexp' 		=> __( 'Check this option to disable generated text shadows.', 'pagelines' ),
				'exp' 			=> __( 'By default PageLines generates text shadows designed to create an embossing effect. If you want to disable this, simply check this option.', 'pagelines' ),
				'type' 			=> 'check',
				'inputlabel'	=> 'Disable Text Shadows'
			),
			'page_background_image' => array(
				'title' 	=> __( 'Site Background Image (Optional)', 'pagelines' ),						
				'shortexp' 	=> __( 'Setup A Background Image For The Background Of Your Site', 'pagelines' ),
				'exp' 		=> __( 'Use this option to apply a background image to your site. This option will be applied to different areas depending on the design mode you have set.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the <strong>top</strong> or <strong>left</strong> side, 50% to center, etc..', 'pagelines' ),
				'type' 		=> 'background_image',
				'selectors'	=> cssgroup('page_background_image')
			),
			'supersize_bg' => array(
				'title' 		=> __( '<strong>Supersize</strong> The Background Image (Fixed Width Mode Required)', 'pagelines' ),						
				'shortexp' 		=> __( 'Uses a script to set the background for full width and responsive design', 'pagelines' ),
				'exp' 			=> __( 'Sets the background to match the width of the browser.', 'pagelines' ),
				'type' 			=> 'check',
				'inputlabel'	=> 'Supersize The Background Image'
			),
			


		);
		
		return apply_filters('pl_options_color_control', $a);
		
	}
	
	/**
	 * Typography Options
	 *
	 * @since 2.0.0
	 */
	function typography(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/typography.png',
			'type_headers' => array(
					'default' 	=> array( 'font' => 'georgia' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_headers'),
					'inputlabel' 	=> 'Select Font',
					'title' 	=> __( 'Typography - Text Headers', 'pagelines' ),
					'shortexp' 	=> __( "Select and Style Your Site's Header Tags (H1, H2, H3...)", 'pagelines' ),
					'exp' 		=> __( "Set typography for your h1, h2, etc.. tags. <br/><br/><strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts<br/><br/><strong>Note:</strong> These options make use of the <a href='http://code.google.com/webfonts' target='_blank'>Google fonts API</a> to vastly increase the number of websafe fonts you can use.", 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),

			'type_primary' => array(
					'default' 	=> array( 'font' => 'georgia' ),
					'type'		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_primary'),
					'inputlabel'=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Primary Font', 'pagelines' ),
					'shortexp' 	=> __( 'Select and Style The Standard Type Used In Your Site (body)', 'pagelines' ),
 					'exp' 		=> __( "Set typography for your primary site text. This is assigned to your site's body tag. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts", 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),


			'type_secondary' => array(
					'default' 	=> array( 'font' => 'lucida_grande' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_secondary'),
					'inputlabel' 	=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Secondary Font ', 'pagelines' ),
 					'shortexp' 	=> __( "Select and Style Your Site's Secondary or Sub Title Text (Metabar, Sub Titles, etc..)", 'pagelines' ),
					'exp' 		=> __( 'This options sets the typography for secondary text used throughout your site. This includes your navigation, subtitles, widget titles, etc.. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts', 'pagelines' ),
					'pro_note'	=> __( 'The Pro version of this framework has over 50 websafe and Google fonts.', 'pagelines' )
			),

			'type_inputs' => array(
					'version' 	=> 'pro',
					'default' 	=> array( 'font' => 'courier_new' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_inputs'),
					'inputlabel' 	=> __( 'Select Font', 'pagelines' ),
					'title' 	=> __( 'Typography - Inputs and Textareas', 'pagelines' ),
					'shortexp' 	=> __( "Select and Style Your Site's Text Inputs and Textareas", 'pagelines' ),
					'exp' 		=> __( "This options sets the typography for general text inputs and textarea inputs. This includes default WordPress comment fields, etc.. <br/><br/> This option makes use of the <a href='http://code.google.com/webfonts'>Google fonts API</a> to vastly increase the number of websafe fonts you can use.<br/><strong>*</strong> Denotes web safe fonts<br/><strong>G</strong> Denotes Google fonts<br/><br/><strong>Note:</strong> the <strong>preview</strong> pane represents the font in your current browser and OS. If developing locally, Google fonts require an internet connection.", 'pagelines' ),
			),

			'typekit_script' => array(
					'default'	=> "",
					'type'		=> 'textarea',
					'inputlabel'	=> __( 'Typekit Header Script', 'pagelines' ),
					'title'		=> __( 'Typekit Font Replacement', 'pagelines' ),
					'shortexp'	=> __( 'Typekit is a service that allows you to use tons of new fonts on your site', 'pagelines' ),
					'exp'		=> __( "Typekit is a new service and technique that allows you to use fonts outside of the 10 or so <strong>web-safe</strong> fonts. <br/><br/>Visit <a href='www.typekit.com' target='_blank'>Typekit.com</a> to get the script for this option. Instructions for setting up Typekit are <a href='http://typekit.assistly.com/portal/article/6780-Adding-fonts-to-your-site' target='_blank'>here</a>.", 'pagelines')
			),
			'fontreplacement' => array(
					'version'	=> 'pro',
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Use Cufon font replacement?', 'pagelines' ),
					'title'		=> __( 'Use Cufon Font Replacement', 'pagelines' ),
					'shortexp'	=> __( 'Use a special font replacement technique for certain text', 'pagelines' ),
					'exp'		=> sprintf( __( "Cufon is a special technique for allowing you to use fonts outside of the 10 or so <strong>web-safe</strong> fonts. <br/><br/>%s is equipped to use it.  Select this option to enable it. Visit the <a href='http://cufon.shoqolate.com/generate/'>Cufon site</a>.", 'pagelines' ), NICETHEMENAME )
			),
			'font_file'	=> array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> __( 'Cufon replacement font file URL', 'pagelines' ),
					'title'		=> __( 'Cufon: Replacement Font File URL', 'pagelines' ),
					'shortexp'	=> __( 'The font file used to replace text', 'pagelines' ),
					'exp'		=> __( "Use the <a href='http://cufon.shoqolate.com/generate/'>Cufon site</a> to generate a font file for use with this theme.  Place it in your theme folder and add the full URL to it here. The default font is Museo Sans.", 'pagelines' )
			),
			'replace_font' => array(
					'version'	=> 'pro',
					'default'	=> 'h1',
					'type'		=> 'text',
					'inputlabel'=> __( 'CSS elements for font replacement', 'pagelines' ),
					'title'		=> __( 'Cufon: CSS elements for font replacement', 'pagelines' ),
					'shortexp'	=> __( 'Add selectors of elements you would like replaced', 'pagelines' ),
					'exp'		=> __( 'Use standard CSS selectors to replace them with your Cufon font. Font replacement must be enabled.', 'pagelines' )
			),
		);
		
		return apply_filters('pagelines_options_typography', $a);
		
	}
	
	/**
	 * Header and Footer Options
	 *
	 * @since 2.0.0
	 */
	function header_footer(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/header.png',
			'drop_down_options' => array(
				'default' => '',
				'type' => 'check_multi',
				'selectvalues'=> array(
		
					'enable_drop_down' => array(
						'default' => false,
						'type' => 'check',
						'scope' => '',
						'inputlabel' => 'Enable Drop Down Navigation?',
						'title' => 'Drop Down Navigation',
						'shortexp' => 'Enable universal drop down navigation',
						'exp' => 'Checking this option will create drop down menus for all child pages when ' . 
								 'users hover over main navigation items.'
						),
					'drop_down_shadow' => array(
						'default' => true,
						'type' => 'check',
						'scope' => '',
						'inputlabel' => 'Enable Shadow on Drop Down Menu?',
						'title' => 'Drop Down Shadow',
						'shortexp' => 'Enable shadow for drop down navigation',
						'exp' => 'Checking this option will create shadows for the drop down menus'
						),
					'drop_down_arrows' => array(
						'default' => true,
						'type' => 'check',
						'scope' => '',
						'inputlabel' => 'Enable Arrows on Drop Down Menu?',
						'title' => 'Drop Down Arrows',
						'shortexp' => 'Enable arrows for drop down navigation',
						'exp' => 'Checking this option will create arrows for the drop down menus'
						)),
				'inputlabel' => 'Select Which Drop Down Options To Show',
				'title' => 'Drop Down Navigation - Nav and BrandNav Section',						
				'shortexp' => 'Select Which To Show',
				'exp' => "Enable drop downs and choose the options you would like to show" 
			 
			),
			'hidesearch' => array(
					'version' => 'pro',
					'default' => false,
					'type' => 'check',
					'inputlabel' => 'Hide search field?',
					'title' => 'Hide Search - Nav Section',						
					'shortexp' => 'Remove the search field from the nav section',
					'exp' => 'Removes the search field from the PageLines Navigation Section.'
				), 
			'icon_position' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'tiny',
					'selectvalues'	=> array(
						'icon_pos_bottom'	=> array('inputlabel'=> __( 'Distance From Bottom (in pixels)', 'pagelines' ), 'default'=> 12),
						'icon_pos_right'	=> array('inputlabel'=> __( 'Distance From Right (in pixels)', 'pagelines' ), 'default'=> 1),
					),
					'title'		=> __( 'Social Icon Position', 'pagelines' ),
					'shortexp'	=> __( 'Control the location of the social icons in the branding section', 'pagelines' ),
					'exp'		=> __( 'Set the position of your header icons with these options. They will be relative to the <strong>branding</strong> section of your site.', 'pagelines' )
			),
			'rsslink' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'=> __( 'Display the Blog RSS icon and link?', 'pagelines' ),
					'title'		=> __( 'News/Blog RSS Icon', 'pagelines' ),
					'shortexp'	=> __( 'Places News/Blog RSS icon in your header', 'pagelines' ),
					'exp'		=> ''
				),
			'icon_social' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'regular',
					'selectvalues'	=> array(
						'gpluslink'			=> array('inputlabel'=> __( 'Your Google+ Profile URL', 'pagelines' ), 'default'=> ''),
						'facebooklink'		=> array('inputlabel'=> __( 'Your Facebook Profile URL', 'pagelines' ), 'default'=> ''),
						'twitterlink'		=> array('inputlabel'=> __( 'Your Twitter Profile URL', 'pagelines' ), 'default'=> ''),
						'linkedinlink'		=> array('inputlabel'=> __( 'Your LinkedIn Profile URL', 'pagelines' ), 'default'=> ''),
						'youtubelink'		=> array('inputlabel'=> __( 'Your YouTube Profile URL', 'pagelines' ), 'default'=> ''),
					),
					'title'		=> __( 'Social Icons', 'pagelines' ),
					'shortexp'	=> __( 'Add social network profile icons to your header', 'pagelines' ),
					'exp'		=> __( 'Fill in the URLs of your social networking profiles. This option will create icons in the header/branding section of your site.', 'pagelines' )
			),
			'nav_use_hierarchy' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Use Child Pages For Secondary Nav?', 'pagelines' ),
					'title'		=> __( 'Use Child Pages for Secondary Nav', 'pagelines' ),
					'shortexp'	=> __( 'Use this options if you want child pages in secondary nav, instead of WP menus', 'pagelines' ),
					'exp'		=> ''
				),
			'footer_num_columns' => array(
				'type' 			=> 'count_select',		
				'count_start'	=> '1',
				'count_number'	=> '6', 
				'title' 		=> 'Number of Footer Columns',
				'shortexp' 		=> 'Control the number of columns per row in your footer columns section.', 
				'inputlabel'	=> 'Select Number of Footer Columns'
			),
			'footer_logo' => array(
					'version'	=> 'pro',
					'default'	=> PL_IMAGES.'/logo-small.png',
					'type'		=> 'image_upload',
					'imagepreview'	=> '100',
					'inputlabel'	=> __( 'Add Footer logo', 'pagelines' ),
					'title'		=> __( 'Footer Logo', 'pagelines' ),
					'shortexp'	=> __( 'Show a logo in the footer', 'pagelines' ),
					'exp'		=> __( 'Add the full url of an image for use in the footer. Recommended size: 140px wide.', 'pagelines' )
			),
			'footer_more' => array(
					'defalut'	=> sprintf( __( "Thanks for dropping by! Feel free to join the discussion by leaving comments, and stay updated by subscribing to the <a href='%s'>RSS feed</a>.", 'pagelines' ), get_bloginfo('rss2_url') ),
					'type'		=> 'textarea',
					'inputlabel'=> __( 'More Statement In Footer', 'pagelines' ),
					'title'		=> __( 'More Statement', 'pagelines' ),
					'shortexp'	=> __( 'Add a quick statement for users who want to know more...', 'pagelines' ),
					'exp'		=> __( "This statement will show in the footer columns under the word more. It is for users who may want to know more about your company or service.", 'pagelines' )
			),
			'footer_terms' => array(
					'default' 	=> '&copy; '.date('Y').' '.get_bloginfo('name'),
					'type' 		=> 'textarea',
					'inputlabel'=> __( 'Terms line in footer:', 'pagelines' ),
					'title' 	=> __( 'Site Terms Statement', 'pagelines' ),
					'shortexp' 	=> __( 'A line in your footer for <strong>terms and conditions</strong> text or similar', 'pagelines' ),
					'exp' 		=> __( "It's sometimes a good idea to give your users a terms and conditions statement so they know how they should use your service or content.", 'pagelines' )
			)
		);
		
		return apply_filters('pagelines_options_header_footer', $a);
		
	}

	/**
	 * Blog and Post Options
	 *
	 * @since 2.0.0
	 */
	function blog_posts(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/blog.png',
			'blog_layout_mode'	=> array(
					'version'		=> 'pro',
					'default'		=> 'magazine',
					'type'			=> 'graphic_selector',
					'showname'		=> true,
					'sprite'		=> PL_ADMIN_IMAGES.'/sprite-blog-modes.png',
					'height'		=> '90px', 
					'width'			=> '115px',
					'layout'		=> 'interface',
					'selectvalues'	=> array(
						'magazine'	=> array('name' => __( "Magazine Layout Mode", 'pagelines' ), 'version' => 'pro', 'offset' => '0px -90px'),
						'blog'		=> array('name' => __( "Blog Layout Mode", 'pagelines' ), 'offset' => '0px 0px')
						), 
					'inputlabel'	=> __( 'Select Post Layout Mode', 'pagelines' ),
					'title'			=> __( 'Blog Post Layout Mode', 'pagelines' ),						
					'shortexp'		=> __( 'Choose between magazine style and blog style layout', 'pagelines' ),
					'exp'			=> __( 'Choose between two magazine or blog layout mode. <br/><br/> <strong>Magazine Layout Mode</strong><br/> Magazine layout mode makes use of post <strong>clips</strong>. These are summarized excerpts shown at half the width of the main content column.<br/>  <strong>Note:</strong> There is an option for showing <strong>full-width</strong> posts on your main <strong>posts</strong> page.<br/><br/><strong>Blog Layout Mode</strong><br/> This is your classical blog layout. Posts span the entire width of the main content column.', 'pagelines' )
				), 
			'excerpt_mode_full' => array(
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'Select Excerpt Mode', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px' ), 
					'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px' ), 
					
				),
				'title'		=> __( 'Feature Post Excerpt Mode', 'pagelines' ),						
				'shortexp'	=> __( 'Select how thumbs should be handled in full-width posts', 'pagelines' ),
				'exp'		=> __( 'Use this option to configure how thumbs will be shown in full-width posts on your blog page.', 'pagelines' )
			),
			'metabar_standard' => array(
				'default'		=> 'By [post_author_posts_link] On [post_date] &middot; [post_comments] &middot; In [post_categories] [post_edit]',
				'type'			=> 'text',
				'inputlabel'	=> __( 'Configure Full Width Post Metabar', 'pagelines' ),
				'title'			=> __( 'Full Width Post Meta', 'pagelines' ),				
				'layout'		=> 'full',		
				'shortexp'		=> __( 'Additional information about a post such as Author, Date, etc...', 'pagelines' ),
				'exp'			=> __( 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li></ul>', 'pagelines' )
			),
			'excerpt_mode_clip' => array(
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> __( 'Select Clip Excerpt Mode', 'pagelines' ),
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => __( 'Left Justified', 'pagelines' ), 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => __( 'On Top', 'pagelines' ), 'offset' => '0px 0px' ), 
					'left-excerpt'	=> array( 'name' => __( 'Left, In Excerpt', 'pagelines' ), 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => __( 'Right, In Excerpt', 'pagelines' ), 'offset' => '0px -150px' ), 
					
				),
				'title'		=> __( 'Clip Excerpt Mode', 'pagelines' ),						
				'shortexp'	=> __( 'Select how thumbs should be handled in clips', 'pagelines' ),
				'exp'		=> __( 'Use this option to configure how thumbs will be shown in clips. These are the smaller <strong>magazine</strong> style excerpts on your blog page.', 'pagelines' )
			),
			'metabar_clip' => array(
				'default'		=> 'On [post_date] By [post_author_posts_link] [post_edit]',
				'type'			=> 'text',
				'layout'		=> 'full',
				'inputlabel'	=> __( 'Configure Clip Metabar', 'pagelines' ),
				'title'			=> __( 'Clip Metabar', 'pagelines' ),						
				'shortexp'		=> __( 'Additional information about a clip such as Author, Date, etc...', 'pagelines' ),
				'exp'			=> __( 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li></ul>', 'pagelines' )
			),
			'full_column_posts'	=> array(
					'version'		=> 'pro',
					'default'		=> 2,
					'type'			=> 'count_select',
					'count_number'	=> get_option('posts_per_page'),
					'inputlabel'	=> __( 'Number of Full Width Posts?', 'pagelines' ),
					'title'			=> __( 'Full Width Posts (Magazine Layout Mode Only)', 'pagelines' ),						
					'shortexp'		=> __( 'When using magazine layout mode, select the number of <strong>featured</strong> or full-width posts', 'pagelines' ),
					'exp'			=> __( 'Select the number of posts you would like shown at the full width of the main content column in magazine layout mode (the rest will be half-width post <strong>clips</strong>).', 'pagelines' )
				),
			'thumb_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'thumb_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> true),
						'thumb_single'		=> array('inputlabel'=> __( 'Single Post Pages', 'pagelines' ), 'default'=> false),
						'thumb_search' 		=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> false),
						'thumb_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> true),
						'thumb_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> true),
						'thumb_clip' 		=> array('inputlabel'=> __( 'In Post Clips (Magazine Mode)', 'pagelines' ), 'default'=> true),
					),
					'title'		=> __( 'Post Thumbnail Placement', 'pagelines' ),
					'shortexp'	=> __( 'Where should the theme use post thumbnails?', 'pagelines' ),
					'exp'		=> __( 'Use this option to control where post <strong>featured images</strong> or thumbnails are used. Note: The post clips option only applies when magazine layout is selected.', 'pagelines' )
			),
			'excerpt_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'excerpt_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> true),
						'excerpt_single'	=> array('inputlabel'=> __( 'Single Post Pages', 'pagelines' ), 'default'=> false),
						'excerpt_search'	=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> true),
						'excerpt_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> true),
						'excerpt_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> true),
					),
					'title'		=> __( 'Post Excerpt or Summary Handling', 'pagelines' ),
					'shortexp'	=> __( 'Where should the theme use post excerpts when showing full column posts?', 'pagelines' ),
					'exp'		=> __( 'This option helps you control where post excerpts are displayed.<br/><br/> <strong>About:</strong> Excerpts are small summaries of articles filled out when creating a post.', 'pagelines' )
			),
			'pagetitles' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'=> __( 'Automatically show Page titles?', 'pagelines' ),
					'title'		=> __( 'Page Titles', 'pagelines' ),						
					'shortexp'	=> __( 'Show the title of pages above the page content.', 'pagelines' ),
					'exp'		=> __( 'This option will automatically place page titles on all pages.', 'pagelines' )
			),
			'continue_reading_text' => array(
					'version'	=> 'pro',
					'default'	=> 'Read Full Article &rarr;',
					'type'		=> 'text',
					'inputlabel'=> __( 'Continue Reading Link Text', 'pagelines' ),
					'title'		=> __( '<strong>Continue Reading</strong> Link Text (When Using Excerpts)', 'pagelines' ),						
					'shortexp'	=> __( 'The link at the end of your excerpt', 'pagelines' ),
					'exp' 		=> __( "This text will be used as the link to your full article when viewing articles on your posts page (when excerpts are turned on).", 'pagelines' )
			),
			'content_comments' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'=> __( 'Show Comments Link?', 'pagelines' ),
					'title'		=> __( 'Show Comments Link After Teasers/Excerpt?', 'pagelines' ),						
					'shortexp'	=> __( 'Shows the number of comments after a teaser or excerpt on your blog page', 'pagelines' ),
			),
			
			'content_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'=> array(
						'content_blog'		=> array('inputlabel'=> __( 'Posts/Blog Page', 'pagelines' ), 'default'=> false),
						'content_search'	=> array('inputlabel'=> __( 'Search Results', 'pagelines' ), 'default'=> false),
						'content_category' 	=> array('inputlabel'=> __( 'Category Lists', 'pagelines' ), 'default'=> false),
						'content_archive' 	=> array('inputlabel'=> __( 'Post Archives', 'pagelines' ), 'default'=> false),
					),
					'title'		=> __( 'Full Post Content', 'pagelines' ),
					'shortexp'	=> __( 'In addition to single post pages and page templates, where should the theme place the full content of posts?', 'pagelines' ),
					'exp'		=> __( 'Choose where the full content of posts is displayed. Choose between all posts pages or just single post pages (i.e. posts pages can just show excerpts or titles).', 'pagelines' )
			),

			'post_footer_social_text' => array(
					'default'	=> 'If you enjoyed this article, please consider sharing it!',
					'type'		=> 'text',
					'inputlabel'=> __( 'Post Footer Social Links Text', 'pagelines' ),
					'title'		=> __( 'Post Footer Social Links Text', 'pagelines' ),						
					'shortexp'	=> __( 'The text next to your social icons', 'pagelines' ),
					'exp'		=> __( "Set the text next to your social links shown on single post pages or on all" . 
							 "posts pages if the post footer link is set to 'always sharing links'.", 'pagelines' )
			),

			'post_footer_share_links' => array(
					'default'	=> '',
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
					
						'share_facebook'	=> array('inputlabel'=> __( 'Facebook Sharing Icon', 'pagelines' ), 'default'=> true),
						'share_twitter'		=> array('inputlabel'=> __( 'Twitter Sharing Icon', 'pagelines' ), 'default'=> true),
						'share_delicious'	=> array('inputlabel'=> __( 'Del.icio.us Sharing Icon', 'pagelines' ), 'default'=> true),
						'share_reddit'		=> array('inputlabel'=> __( 'Reddit Sharing Icon', 'pagelines' ), 'default'=> true),
						'share_digg'		=> array('inputlabel'=> __( 'Digg Sharing Icon', 'pagelines' ), 'default'=> false),
						'share_stumbleupon'	=> array('inputlabel'=> __( 'StumbleUpon Sharing Icon', 'pagelines' ), 'default'=> false)
					),
					'inputlabel'=> __( 'Select Which Share Links To Show', 'pagelines' ),
					'title'		=> __( 'Post Footer Sharing Icons', 'pagelines' ),						
					'shortexp'	=> __( 'Select Which To Show', 'pagelines' ),
					'exp'		=> __( "Select which icons you would like to show in your post footer when sharing" . 
							 "links are shown.", 'pagelines' )
		    ), 
			'excerpt_len' => array(
					'version'	=> 'pro',
					'default' 	=> 55,
					'type' 		=> 'text',
					'inputlabel'=> __( 'Number of words.', 'pagelines' ),
					'title' 	=> __( 'Excerpt Length', 'pagelines' ),
					'shortexp' 	=> __( 'Set the length of excerpts to something other than default', 'pagelines' ),
					'exp' 		=> __( 'Excerpts are set to 55 words by default.', 'pagelines' )
			),
			'excerpt_tags' => array(
					'version'	=> 'pro',
					'default' 	=> '<p><br><a>',
					'type' 		=> 'text',
					'inputlabel'=> __( 'Allowed Tags', 'pagelines' ),
					'title' 	=> __( 'Allow Tags in Excerpt', 'pagelines' ),
					'shortexp' 	=> __( 'Control which tags are stripped from excerpts', 'pagelines' ),
					'exp' 		=> __( 'By default WordPress strips all HTML tags from excerpts. You can use this option to allow certain tags. Simply enter the allowed tags in this field. <br/>An example of allowed tags could be: <strong>&lt;p&gt;&lt;br&gt;&lt;a&gt;</strong>. <br/><br/> <strong>Note:</strong> Enter a period <strong>.</strong> to disallow all tags.', 'pagelines' )
			)			
		);
		
		return apply_filters('pagelines_options_blog_posts', $a);
		
	}
	
	/**
	 * Advanced and Misc Options
	 *
	 * @since 2.0.0
	 */
	function advanced(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/settings.png',
			'google_ie' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'=> __( 'Include Google IE Compatibility Script?', 'pagelines' ),
					'title'		=> __( 'Google IE Compatibility Fix', 'pagelines' ),
					'shortexp'	=> __( 'Include a Google JS script that fixes problems with IE', 'pagelines' ),
					'exp'		=> __( 'More info on this can be found here: <strong>http://code.google.com/p/ie7-js/</strong>.', 'pagelines' )
			),
			'partner_link' 	=> array(
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'=> __( 'Enter Partner Link', 'pagelines' ),
					'title'		=> __( 'PageLines Partner Link', 'pagelines' ),
					'shortexp'	=> __( 'Change your PageLines footer link to a partner link', 'pagelines' ),
					'exp'		=> __( "If you are a <a href='http://www.pagelines.com/partners'>PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.", 'pagelines' )
			),

			'disable_ajax_save' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'=> __( 'Disable AJAX Saving?', 'pagelines' ),
					'title'		=> __( 'Disable AJAX Saving', 'pagelines' ),
					'shortexp'	=> __( 'Check to disable AJAX saving', 'pagelines' ),
					'exp'		=> __( "Check this option if you are having problems with AJAX saving. For example, if design control or typography options aren't working", 'pagelines' )
			),

			'enable_debug' => array(
					'default'	=> '',
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Enable debug settings tab?', 'pagelines' ),
					'title'		=> __( 'PageLines debug', 'pagelines' ),
					'shortexp'	=> __( 'Show detailed settings information', 'pagelines' ),
					'exp'		=> __( "This information can be useful in the forums if you have a problem.", 'pagelines' )
			),

			'hide_pagelines_introduction' => array(
					'default'	=> '',
					'version'	=> 'pro',
					'type'		=> 'check',
					'inputlabel'=> __( 'Hide the introduction?', 'pagelines' ),
					'title'		=> __( 'Show Theme Introduction', 'pagelines' ),
					'shortexp'	=> __( 'Uncheck this option to show theme introduction', 'pagelines' ),
					'exp'		=> ""
			),
			'hide_controls_meta'	 => array(
					'default' 		=> 'publish_posts',
					'version'		=> 'pro',
					'type' 			=> 'select',
					'selectvalues'	=> array(
						'edit_users'			=> array('name' => __( 'Administrator', 'pagelines') ),
						'moderate_comments'		=> array('name' => __( 'Editor', 'pagelines') ),
						'publish_posts'			=> array('name' => __( 'Author', 'pagelines') ),
						'edit_posts'			=> array('name' => __( 'Contributor', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Minimum user level for Post/Page Meta Settings', 'pagelines' ),
					'title' 		=> __( 'User Levels', 'pagelines' ),
					'shortexp' 		=> __( 'Set userlevels for the different settings pages. ', 'pagelines' ),
					'exp' 			=> __( 'Members with a user level lower than the settings here will not be able to see the settings.', 'pagelines' )
			),
			'hide_controls_cpt' 	=> array(
					'default' 		=> 'moderate_comments',
					'version'		=> 'pro',
					'type' 			=> 'select',
					'selectvalues'	=> array(
						'edit_users'			=> array('name' => __( 'Administrator', 'pagelines') ),
						'moderate_comments'		=> array('name' => __( 'Editor', 'pagelines') ),
						'publish_posts'			=> array('name' => __( 'Author', 'pagelines') ),
						'edit_posts'			=> array('name' => __( 'Contributor', 'pagelines') )
					),
					'inputlabel' 	=> __( 'Minimum user level for Custom Post Types ( banners, features etc )', 'pagelines' ),

			)

		);
		
		return apply_filters('pagelines_options_advanced', $a);
		
	}
	
	/**
	 * Custom Coding Options
	 *
	 * @since 2.0.0
	 */
	function custom_code(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/code.png',
			'customcss' => array(
					'version' 	=> 'pro',
					'default' 	=> 'body{}',
					'type' 		=> 'code',
					'height'	=> '300px',
					'layout' 	=> 'full',
					'inputlabel'=> __( 'CSS Rules', 'pagelines' ),
					'title' 	=> __( 'Custom CSS', 'pagelines' ),
					'shortexp' 	=> __( 'Insert custom CSS styling here. It will be stored in the DB and not overwritten. <br/>Note: The professional way to customize your site is using a child theme, or customization plugin', 'pagelines' ),
					'exp' 		=> __( "<div class='theexample'>Example:<br/> <strong>body{<br/> &nbsp;&nbsp;color:  #3399CC;<br/>&nbsp;&nbsp;line-height: 20px;<br/>&nbsp;&nbsp;font-size: 11px<br/>}</strong></div>Enter CSS Rules to change the style of your site.<br/><br/> A lot can be accomplished by simply changing the default styles of the <strong>body</strong> tag such as <strong>line-height</strong>, <strong>font-size</strong>, or <strong>color</strong> (as in text color).", 'pagelines' ), 
					'docslink'	=> 'http://www.pagelines.com/docs/changing-colors-fonts', 
					'vidtitle'	=> __( 'View Customization Documentation', 'pagelines' )
				),

			'headerscripts' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Headerscripts Code', 'pagelines' ),
					'title'		=> __( 'Header Scripts', 'pagelines' ),
					'shortexp'	=> __( 'Scripts inserted directly before the end of the HTML &lt;head&gt; tag', 'pagelines' ),
					'exp'		=> ''
				),
			'footerscripts' => array(
					'default'	=> '',						
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Footerscripts Code or Analytics', 'pagelines' ),
					'title'		=> __( 'Footer Scripts &amp; Analytics', 'pagelines' ),
					'shortexp'	=> __( 'Any footer scripts including Google Analytics', 'pagelines' ),
					'exp'		=> ''
				), 
			'asynch_analytics' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'code',
					'layout'	=> 'full',
					'inputlabel'=> __( 'Asynchronous Analytics', 'pagelines' ),
					'title'		=> __( 'Asynchronous Analytics', 'pagelines' ),
					'shortexp'	=> __( 'Placeholder for Google asynchronous analytics. Goes underneath <strong>body</strong> tag', 'pagelines' ),
					'exp'		=> ''
			),
		);
		
		return apply_filters('pagelines_options_custom_code', $a);
		
	}
	
	/**
	 * Forum Related Options
	 *
	 * @since 2.0.0
	 */
	function forum_options(){

		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/forum_options.png',
			'forum_tags'		=> array(
				'default'	=> true,
				'type'		=> 'check',
				'inputlabel'=> __( 'Show tags in sidebar?', 'pagelines' ),
				'title'		=> __( 'Tag Cloud In Sidebar', 'pagelines' ),
				'shortexp'	=> __( 'Including post tags on the forum sidebar', 'pagelines' ),
				'exp'		=> __( 'Tags are added by users and moderators on your forum and can help people locate posts.', 'pagelines' )
			),
			'forum_image_1'		=> array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'inputlabel'=> __( 'Upload Forum Image', 'pagelines' ),
				'imagepreview'	=> 125,
				'title'		=> __( 'Forum Sidebar Image #1', 'pagelines' ),
				'shortexp'	=> __( 'Add a 125px by 125px image to your forum sidebar', 'pagelines' ),
				'exp'		=> __( "Spice up your forum with a promotional image in the forum sidebar.", 'pagelines' )
			),
			'forum_image_link_1' => array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'=> __( 'Image Link URL', 'pagelines' ),
				'title'		=> __( 'Forum Image #1 Link', 'pagelines' ),
				'shortexp'	=> __( 'Full URL for your forum image', 'pagelines' ),
				'exp'		=> __( "Add the full url for your forum image.", 'pagelines' )
			),
			'forum_image_2' => array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'imagepreview'	=> 125,
				'inputlabel'=> __( 'Upload Forum Image', 'pagelines' ),
				'title'		=> __( 'Forum Sidebar Image #2', 'pagelines' ),
				'shortexp'	=> __( 'Add a 125px by 125px image to your forum sidebar', 'pagelines' ),
				'exp'		=> __( "Spice up your forum with a promotional image in the forum sidebar.", 'pagelines' )
			),
			'forum_image_link_2'	=> array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'=> __( 'Image Link URL', 'pagelines' ),
				'title'		=> __( 'Forum Image #2 Link', 'pagelines' ),
				'shortexp'	=> __( 'Full URL for your forum image', 'pagelines' ),
				'exp'		=> __( "Add the full url for your forum image.", 'pagelines' )
			),
			'forum_sidebar_link'	=> array(
				'default'	=> '#',
				'type'		=> 'text',
				'inputlabel'=> __( 'Forum Image Caption URL', 'pagelines' ),
				'title'		=> __( 'Forum Caption Link URL (Text Link)', 'pagelines' ),
				'shortexp'	=> __( 'Add the URL for your forum caption (optional)', 'pagelines' ),
				'exp'		=> __( "Text link underneath your forum images.", 'pagelines' )
			),
			'forum_sidebar_link_text' => array(
				'default'	=> 'About '.get_bloginfo('name'),
				'type'		=> 'text',
				'inputlabel'=> __( 'Forum Sidebar Link Text', 'pagelines' ),
				'title'		=> __( 'Forum Sidebar Link Text', 'pagelines' ),
				'shortexp'	=> __( 'The text of your image caption link', 'pagelines' ),
				'exp'		=> __( "Change the text of the caption placed under your forum images.", 'pagelines' )
			)
	
		);
		
		return apply_filters('pagelines_options_forum_options', $a);
		
	}
	
	/**
	 * Welcome Message
	 *
	 * @since 2.0.0
	 */
	function welcome(){
		
		$welcome = new PageLinesWelcome();
		
		$a = array(
			'icon'			=> PL_ADMIN_ICONS.'/book.png',
			'hide_pagelines_introduction'	=> array(
				'type'			=> 'text_content',
				'inputlabel'	=> 'Hide Introduction',
				'exp'			=> $welcome->get_welcome()
			),
		);
		
		return apply_filters('pagelines_options_welcome', $a);
		
	}

	
	/**
	 * Custom Options (Deprecated)
	 *
	 * @since 2.0.0
	 */
	function custom_options(){

		$a = array(	);
		
		return apply_filters('pagelines_custom_options', $a);
		
	}
	
	
}

/**
 *
 *  Returns Options Array
 *
 */
function get_option_array( $load_unavailable = true ){
	
	global $disabled_settings; 
	
	$default = new PageLinesOptionsArray();
	 
	$optionarray =  array_merge( $default->options, $default->last_options);
	if( isset($disabled_settings) && !empty($disabled_settings) ){
		foreach($disabled_settings as $key => $s){
			
			if( isset( $s['section'] ) && false != $s['option_id'] ) {
				if( isset($optionarray[$s['panel']][ $s['option_id'] ]) && ( !$load_unavailable || $s['keep'] == false ) )
					unset($optionarray[$s['panel']][ $s['option_id'] ]);
			} else {
				if( isset($optionarray[ $s['panel'] ]) && ( !$load_unavailable || $s['keep'] == false ) ) 
					unset($optionarray[ $s['panel'] ]);
			}	
		}
	}
	return apply_filters('pagelines_options_array', $optionarray); 
	
}

/**
 * 
 *  Returns Welcome
 *
 */
class PageLinesWelcome {
	
	
	function __contruct(){ }
	
	function get_welcome(){
		
		$count = 1; 
		
		$intro = '<div class="theme_intro"><div class="theme_intro_pad">';
		
		$intro .= $this->get_welcome_billboard();
		
		$intro .= '<ul class="welcome_feature_list">';
		foreach($this->get_welcome_features() as $k => $i){
			$endrow = ($count % 2 == 0) ? true : false;
			$intro .= sprintf('<li class="welcomef %s %s"><div class="welcomef-pad"><div class="feature_icon"></div><strong>%s</strong><p>%s</p></div></li>', $i['class'], ($endrow) ? 'rlast' : '', $i['name'], $i['desc']);
			if($endrow) $intro .= '<div class="clear"></div>';
			$count++; 
		}
		$intro .= '<div class="clear"></div></ul>';
		
		$intro .= $this->get_plugins_billboard();
		
		$intro .= sprintf( '<div class="finally"><h3>%s</h3></div>', __( "That's it for now! Have fun and good luck.", 'pagelines' ) );
		
		$intro .= '</div></div>';

		return apply_filters('pagelines_theme_intro', $intro);
	}
	
	function get_welcome_billboard(){
		
		$bill = '<div class="admin_billboard fix"><div class="admin_billboard_pad fix">';
		$bill .= '<div class="admin_theme_screenshot"><img class="" src="'.CHILD_URL.'/screenshot.png" alt="Screenshot" /></div>';
		$bill .= '<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">Congratulations!</h3></div>';
		$bill .= '<div class="admin_billboard_text">Welcome to your PageLines site.<br/> Here are a few tips to get you started...<br/><small>(Note: This intro can be removed below.)</small></div>';
		$bill .= '<div class="clear"></div></div></div></div>';
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	
	function get_welcome_features(){
		$f = array(
			'1strule'	=> array(
				'name'			=> __( 'The First Rule', 'pagelines' ),
				'desc'			=> __( "It's time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.", 'pagelines' ),
				'class'			=> 'feature_firstrule', 
				'icon'			=> '',
			),
			'support'	=> array(
				'name'			=> __( 'World Class Support', 'pagelines' ),
				'desc'			=> __( "For help getting started, we offer our customers tons of support including comprehensive <a href='http://www.pagelines.com/docs/' target='_blank'>docs</a>, and an active, moderated <a href='http://www.pagelines.com/forum/' target='_blank'>forum</a>.", 'pagelines' ),
				'class'			=> 'feature_support', 
				'icon'			=> '',
			),
			'dragdrop'	=> array(
				'name'			=> __( 'Drag &amp; Drop Templates', 'pagelines' ),
				'desc'			=> __( "Check out the Template Setup panel! This is how you will control site elements using drag &amp; drop on your site. Learn more in the <a href='http://docs.pagelines.com/'>docs</a>.", 'pagelines' ),
				'class'			=> 'feature_templates', 
				'icon'			=> '',
			),
			'settings'	=> array(
				'name'			=> __( 'Your Settings', 'pagelines' ),
				'desc'			=> __( 'This panel is where you will start the customization of your website. Any options applied through this interface will make changes site-wide.<br/> ', 'pagelines' ),
				'class'			=> 'feature_options', 
				'icon'			=> '',
			),
			'widgets'	=> array(
				'name'			=> __( 'Draggable Layout &amp; Widgets', 'pagelines' ),
				'desc'			=> __( 'Use the Layout Editor to control your content layout.  There are also several <strong>widgetized</strong> areas that are controlled through your widgets panel.', 'pagelines' ),
				'class'			=> 'feature_dynamic', 
				'icon'			=> '',
			),
			'metapanel'	=> array(
				'name'			=> __( 'MetaPanel', 'pagelines' ),
				'desc'			=> __( "You'll find the MetaPanel at the bottom of WordPress page/post creation pages.  It will allow you to set options specific to that page or post.", 'pagelines' ),
				'class'			=> 'feature_meta', 
				'icon'			=> '',
			),
		);
		
		return apply_filters('pagelines_welcome_features', $f);
	}
	
	function get_plugins_billboard(){
		
		$billboard = sprintf( '<div class="admin_billboard plugins_billboard"><div class="admin_billboard_content"><div class="feature_icon"></div><h3 class="admin_header_main">%s</h3>', __( 'Supported Extensions', 'pagelines' ) );
	
		$billboard .= $this->show_supported_elements( 'plugins' );
		$billboard .= $this->show_supported_elements( 'sections' );		
		
		$billboard .= sprintf( __( "<p>Although %s is universally plugin compatible, we have added <strong>advanced</strong> graphical/functional support for several WordPress plugins.</p><p> It's your responsibility to install each plugin, which can be done through <strong>Plugins</strong> &gt; <strong>Add New</strong> or through the <strong>developer's site</strong> where you can download them manually (e.g. CForms).</p>", 'pagelines' ), NICETHEMENAME );
			
		$billboard .= '<ul class="welcome_plugin_list">';
		foreach($this->get_welcome_plugins() as $k => $i){
			if(isset( $i['name2'] ))
				$billboard .= sprintf('<li><div class="li-pad"><a href="%s" target="_blank">%s</a> &amp; <a href="%s" target="_blank">%s</a> %s</div></li>', $i['url'], $i['name'], $i['url2'], $i['name2'], $i['desc']);
			else
				$billboard .= sprintf('<li><div class="li-pad"><a href="%s" target="_blank">%s</a> %s</div></li>', $i['url'], $i['name'], $i['desc']);
		
		}
		$billboard .= '</ul></div></div>';
		
		return $billboard;
	}
	
	function get_supported_elements( $type ) {
	
		global $supported_elements;
		$available = json_decode( get_option( 'pagelines_extend_' . $type, false ) );	

		if ( isset( $supported_elements[$type] ) && is_array( $supported_elements[$type] ) ) {
			$out = array();
			foreach( $supported_elements[$type] as $a ) {
				if ( ! $a['supported'] || ! isset( $available->$a['slug'] ) )
					continue;

				$out[ $a['slug'] ] = array(
					'name'	=> $available->$a['slug']->name,
					'url'	=> sprintf( 'http://www.pagelines.com/extend/%s/%s/', $type, $a['slug'] ),
					'desc'	=> $available->$a['slug']->text
					);
				}
			return $out;
			}

		return false;	
		}

	function show_supported_elements( $type ) {
		
		if ( false != ( is_child_theme() && $a = $this->get_supported_elements( $type ) ) ) {
			$out = '';
			$out .= sprintf( '<p>%s supports these additional %s:</p>', NICECHILDTHEMENAME, $type );
			$out .= '<ul class="welcome_plugin_list">';
			
			foreach ( $a as $i ) {

				$out .= sprintf('<li><div class="li-pad"><a href="%s" target="_blank">%s</a> %s</div></li>', $i['url'], $i['name'], $i['desc']);
							
			}
			$out .= '</ul>';
			return $out;
		}
		return '';
	}

	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'name'			=> __( 'Post Types Order', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'desc'			=> __( 'Allows you to re-order custom post types like features and boxes.', 'pagelines' ),
			),
			'disqus'	=> array(
				'name'			=> __( 'Disqus Comments', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/disqus-comment-system/', 
				'desc'			=> __( 'Improve your commenting system.', 'pagelines' ),
			),
			'cforms'	=> array(
				'name'			=> __( 'CForms', 'pagelines' ),
				'url'			=> 'http://www.deliciousdays.com/cforms-plugin/', 
				'desc'			=> __( 'Advanced contact forms that can be used for creating mailing lists, etc.', 'pagelines' ),
			),
			'wp125'	=> array(
				'name'			=> __( 'WP125', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/wp125/', 
				'desc'			=> __( 'Used to show 125px by 125px ads or images in your sidebar(Widget).', 'pagelines' ),
			),
			'flickrrss'	=> array(
				'name'			=> __( 'FlickrRSS Images', 'pagelines' ),
				'url'			=> 'http://eightface.com/wordpress/flickrrss/', 
				'desc'			=> __( 'Shows pictures from your Flickr Account (Widget &amp; Carousel Section).', 'pagelines' ),
			),
			'nextgen'	=> array(
				'name'			=> __( 'NextGen-Gallery', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/nextgen-gallery/', 
				'desc'			=> __( 'Allows you to create image galleries with special effects (Carousel Section).', 'pagelines' ),
			),
			'pagenavi'	=> array(
				'name'			=> __( 'Wp-PageNavi', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/wp-pagenavi/', 
				'desc'			=> __( 'Creates advanced <strong>paginated</strong> post navigation.', 'pagelines' ),
			),
			'breadcrumb'	=> array(
				'name'			=> __( 'Breadcrumb NavXT', 'pagelines' ),
				'url'			=> 'http://wordpress.org/extend/plugins/breadcrumb-navxt/', 
				'desc'			=> __( 'Displays a configurable breadcrumb nav on your site.', 'pagelines' ),
			)
		);
		
		return apply_filters('pagelines_welcome_plugins', $plugins);
	}
}
