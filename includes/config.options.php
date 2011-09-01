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
		
		if(!pagelines_option('hide_introduction') && VPRO)
			$this->options['_welcome'] = $this->welcome();
		
		$this->options['website_setup'] = $this->website_setup();
		$this->options['template_setup'] = $this->template_setup();
		$this->options['layout_editor'] = $this->layout_editor();
		$this->options['color_control'] = $this->color_control();
		$this->options['typography'] = $this->typography();
		$this->options['header_and_footer'] = $this->header_footer();
		$this->options['blog_and_posts'] = $this->blog_posts();
		
		if( pagelines_option('forum_options') )
			$this->last_options['forum_settings'] = $this->forum_options();
		
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
				'inputlabel' 	=> 'Email Address',
				'title'			=> 'Email Updates',						
				'shortexp' 		=> 'Optionally sign up for email updates and notifications.',
				'exp' 			=> 'Adding your email here will allow us to send you email notifications about updates and new software from PageLines'
			),
			'pagelines_custom_logo' => array(
				'default' 		=> PL_IMAGES.'/logo.png',
				'default_free'	=> PL_IMAGES.'/logo-platform.png',
				'type' 			=> 'image_upload',
				'imagepreview' 	=> '270',
				'inputlabel' 	=> 'Upload custom logo',
				'title'			=> 'Custom Header Image',						
				'shortexp' 		=> 'Input Full URL to your custom header or logo image.',
				'exp' 			=> 'Optional way to replace "heading" and "description" text for your website ' . 
						    		'with an image.'
			),
			'pagelines_favicon'	=> array(
				'default' 	=> 	PL_ADMIN_IMAGES . "/favicon-pagelines.ico",
				'type' 		=> 	'image_upload',
				'imagepreview' 	=> 	'16',
				'title' 	=> 	'Favicon Image',						
				'shortexp' 	=> 	'Input Full URL to favicon image ("favicon.ico" image file)',
				'exp' 		=> 	'Enter the full URL location of your custom "favicon" which is visible in ' .
							'browser favorites and tabs.<br/> (<strong>Must be .png or .ico file - 16px by 16px</strong> ).'
			),		
			'twittername' => array(
				'default' 		=> '',
				'type' 			=> 'text',
				'inputlabel' 	=> 'Your Twitter Username',
				'title' 		=> 'Twitter Integration',
				'shortexp'	 	=> 'Places your Twitter feed in your site (<em>"Twitter for WordPress" plugin required</em>)',
				'exp' 			=> 'This places your Twitter feed on the site. Leave blank if you want to hide or not use.'
			),
			'pl_login_image'	=> array(
				'version' 		=> 'pro',
				'default' 		=> PL_ADMIN_IMAGES . "/login-pl.png",
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'60',
				'title' 		=> 'Login Page Image',						
				'shortexp' 		=> 'The image to use on your site\'s login page',
				'exp'			=> 'This image will be used on the login page to your admin. Use an image that is approximately <strong>80px</strong> in height.'
			),
			'pagelines_touchicon'	=> array(
				'version' 		=> 'pro',
				'default' 		=> '',
				'type' 			=> 	'image_upload',
				'imagepreview' 	=> 	'60',
				'title' 		=> 'Apple Touch Image',						
				'shortexp' 		=> 'Input Full URL to Apple touch image (.jpg, .gif, .png)',
				'exp'			=> 'Enter the full URL location of your Apple Touch Icon which is visible when ' .
						  'your users set your site as a <strong>webclip</strong> in Apple Iphone and ' . 
						  'Touch Products. It is an image approximately 57px by 57px in either .jpg, ' .
						  '.gif or .png format.'
			),
		
			'sidebar_no_default' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Hide Sidebars When Empty (no widgets)',
					'title'		=> 'Remove Default Sidebars When Empty',
					'shortexp'	=> 'Hide default sidebars when sidebars have no widgets in them',
					'exp'		=> 'This allows you to remove sidebars completely when they have no widgets in them.'
			),
			'sidebar_wrap_widgets' => array(
					'default' 	=> 'top',
					'version'	=> 'pro',
					'type' 		=> 'select',
					'selectvalues'	=> array(
						'top'		=> array("name" => 'On Top of Sidebar'),
						'bottom'	=> array("name" => 'On Bottom of Sidebar')
					),
					'inputlabel' 	=> 'Sidebar Wrap Widgets Position',
					'title' 	=> 'Sidebar Wrap Widgets',
					'shortexp' 	=> 'Choose whether to show the sidebar wrap widgets on the top or bottom of the sidebar.',
					'exp' 		=> 'You can select whether to show the widgets that you place in the sidebar wrap template in either the top or the bottom of the sidebar.'
			),
		
		);
		
		if ( get_option( 'pagelines_email_sent') ) 
			unset($a['email_capture']);
		
		return apply_filters('pagelines_options_website_setup', $a);
	}
	
	/**
	 * Get Template Setup - Drag & Drop Interface
	 *
	 * @since 2.0.0
	 */
	function template_setup(){

		 $a = array(
			'icon'			=> PL_ADMIN_ICONS.'/dragdrop.png',
			'templates'		=> array(
				'default'	=> '',
				'type'		=> 'templates',
				'layout'	=> 'interface',
				'title'		=> THEMENAME.' Template Setup',						
				'shortexp'	=> 'Drag and drop control over your website\'s templates.<br/> Note: Select "Hidden by Default" to hide the section by default; and activate with individual page/post options.',
				'docslink'	=> 'http://www.pagelines.com/docs/template-setup', 
				'vidtitle'	=> 'Template Setup Overview'
			),
			'resettemplates' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Template Section Order", 'pagelines'),
				'type'		=> 'reset',
				'callback'	=> 'reset_templates_to_default',
				'title'		=> 'Reset Section Order To Default',	
				'layout'	=> 'full',					
				'shortexp'	=> 'Changes your template sections back to their default order and layout (options settings are not affected)',
			)		
		);
		
		return apply_filters('pagelines_options_template_setup', $a);
		
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
				'inputlabel'	=> 'How should layout be handled?',
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-layout-modes.png',
				'height'		=> '88px', 
				'width'			=> '130px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'pixels'		=> array( 'name' => 'Responsive with Pixel Width', 'offset' => '0px 0px' ), 
					'percent'		=> array( 'name' => 'Responsive with Percent Width', 'offset' => '0px -88px' ), 
					'static'		=> array( 'name' => 'Static with Pixel Width', 'offset' => '0px -176px' )
				),
				'title'		=> 'Layout Handling',						
				'shortexp'	=> 'Select between responsive vs. static; pixel based or percentage based layout',
				'exp'		=> 'Responsive layout adjusts to the size of your user\'s browser window; static is fixed width. Use this option to switch between the pixel based site width and a percentage based one.'
			),
			'layout_default' => array(
				'default' 	=> "one-sidebar-right",
				'type' 		=> 'layout_select',
				'title' 	=> 'Default Layout Mode',
				'inputlabel'	=> 'Select Default Layout',	
				'layout' 	=> 'interface',						
				'shortexp' 	=> 'Select your default layout mode, this can be changed on individual pages.<br />Once selected, you can adjust the layout in the Layout Builder.',
				'exp' 		=> 'The default layout for pages and posts on your site. Dimensions can be changed using the Layout Dimension Editor.',
				'docslink'	=> 'http://www.pagelines.com/docs/editing-layout'
			),
			'layout' => array(
				'default'	=> 'one-sidebar-right',
				'type'		=> 'layout',
				'layout'	=> 'interface',
				'title'		=> 'Layout Dimension Editor',						
				'shortexp'	=> 'Configure the default layout for your site which is initially selected in the Default Layout Mode option in Global Options. <br/>This option allows you to adjust columns and margins for the default layout.',
			), 
			
			'resetlayout' => array(
				'default'	=> '',
				'inputlabel'	=> __("Reset Layout", 'pagelines'),
				'type' 		=> 'reset',
				'callback'	=> 'reset_layout_to_default',
				'title' 	=> 'Reset Layout To Default',	
				'layout'	=> 'full',					
				'shortexp'	=> 'Changes layout mode and dimensions back to default',
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
					'full_width'	=> array("name" => "Full-Width Sections", 'offset' => '0px 0px'),
					'fixed_width'	=> array("name" => "Content Width Page", "version" => "pro", 'offset' => '0px -88px')
				), 
				'inputlabel'	=> 'Site Design Mode',
				'title'		=> 'Site Design Mode',						
				'shortexp'	=> 'Choose between full width HTML or fixed width HTML',
				'exp'		=> 'There are three css design modes available. Each allows a different style of design.<ul><li><strong>Full-Width Mode With Canvas</strong> This design mode has a full-width page area, and a canvas area behind your content that can be controlled seperately.</li><li><strong>Full-Width Mode</strong> Full width design mode allows you to have aspects of your site that are the full-width of your screen; while others are the width of the content area.</li><li><strong>Fixed-Width Mode</strong> Fixed width design mode creates a fixed with "page" that can be used as the area for your design.  You can set a background to the page; and the content will have a seperate "fixed-width" background area (i.e. the width of the content).</li></ul>',
			),	
			'page_colors'		=> array(
				'title' 	=> 'Basic Layout Colors',						
				'shortexp' 	=> 'The Main Layout Colors For Your Site',
				'exp' 		=> 'Use these options to quickly setup the main layout colors for your site.  You can use these options to build custom sites very quickly, or to quickly prototype a design then refine through custom CSS.<br/><br/><strong>Notes:</strong> <ol><li>To make the background transparent, you can leave the options blank (delete text).</li>  <li>Further customize and refine colors through custom CSS or plugins
</li></ol>',
				'type' 		=> 'color_multi',
				'layout'	=> 'full',
				'selectvalues'	=> array(
					'bodybg'	=> array(				
						'default' 		=> '#EEEEEE',
						'css_prop'		=> 'background-color',
						'cssgroup'		=> 'bodybg',
						'inputlabel' 	=> 'Body Background',
						
					),
					'pagebg'		=> array(				
						'default' 	=> '',
						'cssgroup'	=>	'pagebg',
						'flag'		=> 'blank_default',
						'css_prop'	=> 'background-color',
						'inputlabel' 	=> 'Page Background',
						),
					'contentbg'	=> array(				
						'version'	=> 'pro',
						'default' 	=> '',
						'cssgroup'	=>	'contentbg',
						'flag'		=> 'blank_default',
						'css_prop'	=> 'background-color',
						'inputlabel' 	=> 'Content Background',
						'math'		=> array(
								array( 
									'mode' => 'contrast', 
									'cssgroup' => 'border_layout', 
									'css_prop' => 'border-color', 
									'diff' => '8%', 
									'depends' => pl_background_cascade()
									
								),
								array( 
									'mode' => 'contrast', 
									'cssgroup' => 'box_color_primary', 
									'css_prop' => 'background-color', 
									'diff' => '5%', 
									'depends' => pl_background_cascade(),
									'math'		=> array(
										array( 'mode' => 'contrast', 'cssgroup' => 'text_box', 'css_prop' => 'color', 'diff' => '65%', 'math' => array(
											array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_box') ),
										)),
										array( 'mode' => 'contrast', 'cssgroup' => 'border_primary', 'css_prop' => 'border-color', 'diff' => '8%', 'math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_primary_shadow', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '10%'),
											array( 'mode' => 'lighter', 'cssgroup' => 'border_primary_highlight', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '10%'),
										)),
										array( 'mode' => 'contrast', 'cssgroup' => 'box_color_secondary', 'css_prop' => array('background-color'), 'diff' => '3%', 'math' => array(
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-color'), 'diff' => '5%'),
											array( 'mode' => 'darker', 'cssgroup' => 'border_secondary', 'css_prop' => array('border-left-color', 'border-top-color'), 'diff' => '15%'),
											
										)),
										array( 'mode' => 'contrast', 'cssgroup' => 'box_color_tertiary', 'css_prop' => array('background-color'), 'diff' => '6%','math' => array(
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
				'title' 	=> 'Page Text Colors',						
				'shortexp' 	=> 'Control The Color Of Text Used Throughout Your Site',
				'exp' 		=> 'These options control the colors of the text throughout the page or content area of your site.<br/><br/>Certain text types are designed to contrast with different box elements and are meant to be used with hover effects.<br/><br/>Experiment to find exactly how colors are combined with text on your site.',
				'type' 		=> 'color_multi',
				'layout'	=> 'full',
				'selectvalues'	=> array(
					'headercolor'	=> array(		
						'default' 	=> '#000000',
						'cssgroup'	=> 'headercolor',
						'inputlabel' 	=> 'Text Headers',
						'math'		=> array(
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'headercolor'),
						)
					),
					'text_primary' => array(		
						'default' 	=> '#000000',
						'cssgroup'	=>	'text_primary',
						'inputlabel' 	=> 'Primary Text',
						'math'		=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'text_secondary', 'css_prop' => 'color', 'diff' => '65%'),
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => array('text_primary', 'text_secondary', 'text_tertiary') ),
						)
					),
					'linkcolor' => array(
						'default'		=> '#225E9B',
						'cssgroup'		=>	'linkcolor',
						'inputlabel' 	=> 'Primary Links',	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => pl_background_cascade(),  'cssgroup' => 'linkcolor_hover', 'css_prop' => 'color', 'diff' => '80%'),	
							array( 'mode' => 'shadow', 'mixwith' => pl_background_cascade(), 'cssgroup' => 'linkcolor'),
							)				
					),
					'footer_text' => array(
						'default'		=> '#AAAAAA',
						'cssgroup'		=>	'footer_highlight',
						'inputlabel' 	=> 'Footer Text',	
						'math'			=> array(
							array( 'mode' => 'mix', 'mixwith' => array(pagelines_option('bodybg')),  'cssgroup' => 'footer_text', 'css_prop' => 'color', 'diff' => '66%'),
							array( 'mode' => 'shadow', 'mixwith' => array(pagelines_option('bodybg')), 'cssgroup' => array('footer_text', 'footer_highlight') ),
						)					
					),
				),
			),
			'page_background_image' => array(
				'title' 	=> 'Site Background Image (Optional)',						
				'shortexp' 	=> 'Setup A Background Image For The Background Of Your Site',
				'exp' 		=> 'Use this option to apply a background image to your site. This option will be applied to different areas depending on the design mode you have set.<br/><br/><strong>Positioning</strong> Use percentages to position the images, 0% corresponds to the "top" or "left" side, 50% to center, etc..',
				'type' 		=> 'background_image',
				'selectors'	=> cssgroup('page_background_image')
			),
			


		);
		
		return apply_filters('pagelines_options_design_control', $a);
		
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
					'title' 	=> 'Typography - Text Headers',
					'shortexp' 	=> 'Select and Style Your Site\'s Header Tags (H1, H2, H3...)',
					'exp' 		=> 'Set typography for your h1, h2, etc.. tags. <br/><br/><strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts<br/><br/><strong>Note:</strong> These options make use of the <a href="http://code.google.com/webfonts" target="_blank">Google fonts API</a> to vastly increase the number of websafe fonts you can use.',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),

			'type_primary' => array(
					'default' 	=> array( 'font' => 'georgia' ),
					'type'		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_primary'),
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Primary Font',
					'shortexp' 	=> 'Select and Style The Standard Type Used In Your Site (body)',
					'exp' 		=> 'Set typography for your primary site text. This is assigned to your site\'s body tag. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),


			'type_secondary' => array(
					'default' 	=> array( 'font' => 'lucida_grande' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_secondary'),
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Secondary Font ',
					'shortexp' 	=> 'Select and Style Your Site\'s Secondary or Sub Title Text (Metabar, Sub Titles, etc..)',
					'exp' 		=> 'This options sets the typography for secondary text used throughout your site. This includes your navigation, subtitles, widget titles, etc.. <br/><br/> <strong>*</strong> Denotes Web Safe Fonts<br/><strong>G</strong> Denotes Google Fonts',
					'pro_note'	=> 'The Pro version of this framework has over 50 websafe and Google fonts.'
			),

			'type_inputs' => array(
					'version' 	=> 'pro',
					'default' 	=> array( 'font' => 'courier_new' ),
					'type' 		=> 'typography',
					'layout'	=> 'full',
					'selectors'	=> cssgroup('type_inputs'),
					'inputlabel' 	=> 'Select Font',
					'title' 	=> 'Typography - Inputs and Textareas',
					'shortexp' 	=> 'Select and Style Your Site\'s Text Inputs and Textareas.',
					'exp' 		=> 'This options sets the typography for general text inputs and textarea inputs. This includes default WordPress comment fields, etc.. <br/><br/> This option makes use of the <a href="http://code.google.com/webfonts">Google fonts API</a> to vastly increase the number of websafe fonts you can use.<br/><strong>*</strong> Denotes web safe fonts<br/><strong>G</strong> Denotes Google fonts<br/><br/><strong>Note:</strong> the "preview" pane represents the font in your current browser and OS. If developing locally, Google fonts require an internet connection.',
			),

			'typekit_script' => array(
					'default'	=> "",
					'type'		=> 'textarea',
					'inputlabel'	=> 'Typekit Header Script',
					'title'		=> 'Typekit Font Replacement',
					'shortexp'	=> 'Typekit is a service that allows you to use tons of new fonts on your site.',
					'exp'		=> 'Typekit is a new service and technique that allows you to use fonts outside of the 10 or so "web-safe" fonts. <br/><br/>' .
							 'Visit <a href="www.typekit.com" target="_blank">Typekit.com</a> to get the script for this option. Instructions for setting up Typekit are <a href="http://typekit.assistly.com/portal/article/6780-Adding-fonts-to-your-site" target="_blank">here</a>.'
			),
			'fontreplacement' => array(
					'version'	=> 'pro',
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'	=> 'Use Cufon font replacement?',
					'title'		=> 'Use Cufon Font Replacement',
					'shortexp'	=> 'Use a special font replacement technique for certain text',
					'exp'		=> 'Cufon is a special technique for allowing you to use fonts outside of the 10 or so "web-safe" fonts. <br/><br/>' .
							 THEMENAME.' is equipped to use it.  Select this option to enable it. Visit the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a>.'
			),
			'font_file'	=> array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> 'Cufon replacement font file URL',
					'title'		=> 'Cufon: Replacement Font File URL',
					'shortexp'	=> 'The font file used to replace text.',
					'exp'		=> 'Use the <a href="http://cufon.shoqolate.com/generate/">Cufon site</a> to generate a font file for use with this theme.  Place it in your theme folder and add the full URL to it here. The default font is Museo Sans.'
			),
			'replace_font' => array(
					'version'	=> 'pro',
					'default'	=> 'h1',
					'type'		=> 'text',
					'inputlabel'	=> 'CSS elements for font replacement',
					'title'		=> 'Cufon: CSS elements for font replacement',
					'shortexp'	=> 'Add selectors of elements you would like replaced.',
					'exp'		=> 'Use standard CSS selectors to replace them with your Cufon font. Font replacement must be enabled.'
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
			'icon_position' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'tiny',
					'selectvalues'	=> array(
						'icon_pos_bottom'	=> array('inputlabel'=>'Distance From Bottom (in pixels)', 'default'=> 12),
						'icon_pos_right'	=> array('inputlabel'=>'Distance From Right (in pixels)', 'default'=> 1),
					),
					'title'		=> 'Social Icon Position',
					'shortexp'	=> 'Control the location of the social icons in the branding section',
					'exp'		=> 'Set the position of your header icons with these options. They will be relative to the "branding" section of your site.'
			),
			'rsslink' => array(
					'default'	=> true,
					'type'		=> 'check',
					'inputlabel'	=> 'Display the Blog RSS icon and link?',
					'title'		=> 'News/Blog RSS Icon',
					'shortexp'	=> 'Places News/Blog RSS icon in your header',
					'exp'		=> ''
				),
			'icon_social' => array(
					'version'	=> 'pro',
					'type'		=> 'text_multi',
					'inputsize'	=> 'regular',
					'selectvalues'	=> array(
						'gpluslink'			=> array('inputlabel'=>'Your Google+ Profile URL', 'default'=> ''),
						'facebooklink'		=> array('inputlabel'=>'Your Facebook Profile URL', 'default'=> ''),
						'twitterlink'		=> array('inputlabel'=>'Your Twitter Profile URL', 'default'=> ''),
						'linkedinlink'		=> array('inputlabel'=>'Your LinkedIn Profile URL', 'default'=> ''),
						'youtubelink'		=> array('inputlabel'=>'Your YouTube Profile URL', 'default'=> ''),
					),
					'title'		=> 'Social Icons',
					'shortexp'	=> 'Add social network profile icons to your header',
					'exp'		=> 'Fill in the URLs of your social networking profiles. This option will create icons in the header/branding section of your site.'
			),
			'nav_use_hierarchy' => array(
					'default'	=> false,
					'type'		=> 'check',
					'inputlabel'	=> 'Use Child Pages For Secondary Nav?',
					'title'		=> 'Use Child Pages for Secondary Nav',
					'shortexp'	=> 'Use this options if you want child pages in secondary nav, instead of WP menus.',
					'exp'		=> ''
				),
			'footer_logo' => array(
					'version'	=> 'pro',
					'default'	=> PL_IMAGES.'/logo-small.png',
					'type'		=> 'image_upload',
					'imagepreview'	=> '100',
					'inputlabel'	=> 'Add Footer logo',
					'title'		=> 'Footer Logo',
					'shortexp'	=> 'Show a logo in the footer',
					'exp'		=> 'Add the full url of an image for use in the footer. Recommended size: 140px wide.'
			),
			'footer_more' => array(
					'default'	=> "Thanks for dropping by! Feel free to join the discussion by leaving " . 
							"comments, and stay updated by subscribing to the <a href='".get_bloginfo('rss2_url')."'>RSS feed</a>.",
					'type'		=> 'textarea',
					'inputlabel'	=> 'More Statement In Footer',
					'title'		=> 'More Statement',
					'shortexp'	=> 'Add a quick statement for users who want to know more...',
					'exp'		=> "This statement will show in the footer columns under the word more. It is for users who may want to know more about your company or service."
			),
			'footer_terms' => array(
					'default' 	=> '&copy; '.date('Y').' '.get_bloginfo('name'),
					'type' 		=> 'textarea',
					'inputlabel' 	=> 'Terms line in footer:',
					'title' 	=> 'Site Terms Statement',
					'shortexp' 	=> 'A line in your footer for "terms and conditions text" or similar',
					'exp' 		=> "It's sometimes a good idea to give your users a terms and conditions statement so they know how they should use your service or content."
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
						'magazine'	=> array("name" => "Magazine Layout Mode", "version" => "pro", 'offset' => '0px -90px'),
						'blog'		=> array("name" => "Blog Layout Mode", 'offset' => '0px 0px')
						), 
					'inputlabel'	=> 'Select Post Layout Mode',
					'title'			=> 'Blog Post Layout Mode',						
					'shortexp'		=> 'Choose between magazine style and blog style layout.',
					'exp'			=> 'Choose between two magazine or blog layout mode. <br/><br/> <strong>Magazine Layout Mode</strong><br/> Magazine layout mode makes use of post "clips". These are summarized excerpts shown at half the width of the main content column.<br/>  <strong>Note:</strong> There is an option for showing "full-width" posts on your main "posts" page.<br/><br/><strong>Blog Layout Mode</strong><br/> This is your classical blog layout. Posts span the entire width of the main content column.'
				), 
			'excerpt_mode_full' => array(
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> 'Select Excerpt Mode',
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => 'Left Justified', 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => 'On Top', 'offset' => '0px 0px' ), 
					'left-excerpt'	=> array( 'name' => 'Left, In Excerpt', 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => 'Right, In Excerpt', 'offset' => '0px -150px' ), 
					
				),
				'title'		=> 'Feature Post Excerpt Mode',						
				'shortexp'	=> 'Select how thumbs should be handled in full-width posts',
				'exp'		=> 'Use this option to configure how thumbs will be shown in full-width posts on your blog page.'
			),
			'metabar_standard' => array(
				'default'		=> 'By [post_author_posts_link] On [post_date] &middot; [post_comments] &middot; In [post_categories] [post_edit]',
				'type'			=> 'text',
				'inputlabel'	=> 'Configure Full Width Post Metabar',
				'title'			=> 'Full Width Post Meta',				
				'layout'		=> 'full',		
				'shortexp'		=> 'Additional information about a post such as Author, Date, etc...',
				'exp'			=> 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li></ul>'
			),
			'excerpt_mode_clip' => array(
				'default'		=> 'left',
				'type'			=> 'graphic_selector',
				'inputlabel'	=> 'Select Clip Excerpt Mode',
				'showname'		=> true,
				'sprite'		=> PL_ADMIN_IMAGES.'/sprite-excerpt-modes.png',
				'height'		=> '50px', 
				'width'			=> '62px',
				'layout'		=> 'interface',
				'selectvalues'	=> array(
					'left'			=> array( 'name' => 'Left Justified', 'offset' => '0px -50px' ), 
					'top'			=> array( 'name' => 'On Top', 'offset' => '0px 0px' ), 
					'left-excerpt'	=> array( 'name' => 'Left, In Excerpt', 'offset' => '0px -100px' ), 
					'right-excerpt'	=> array( 'name' => 'Right, In Excerpt', 'offset' => '0px -150px' ), 
					
				),
				'title'		=> 'Clip Excerpt Mode',						
				'shortexp'	=> 'Select how thumbs should be handled in clips',
				'exp'		=> 'Use this option to configure how thumbs will be shown in clips. These are the smaller "magazine" style excerpts on your blog page.'
			),
			'metabar_clip' => array(
				'default'		=> 'On [post_date] By [post_author_posts_link] [post_edit]',
				'type'			=> 'text',
				'layout'		=> 'full',
				'inputlabel'	=> 'Configure Clip Metabar',
				'title'			=> 'Clip Metabar',						
				'shortexp'		=> 'Additional information about a clip such as Author, Date, etc...',
				'exp'			=> 'Use shortcodes to control the dynamic information in your metabar. Example shortcodes you can use are: <ul><li><strong>[post_categories]</strong> - List of categories</li><li><strong>[post_edit]</strong> - Link for admins to edit the post</li><li><strong>[post_tags]</strong> - List of post tags</li><li><strong>[post_comments]</strong> - Link to post comments</li><li><strong>[post_author_posts_link]</strong> - Author and link to archive</li><li><strong>[post_author_link]</strong> - Link to author URL</li><li><strong>[post_author]</strong> - Post author with no link</li><li><strong>[post_time]</strong> - Time of post</li><li><strong>[post_date]</strong> - Date of post</li></ul>'
			),
			'full_column_posts'	=> array(
					'version'		=> 'pro',
					'default'		=> 2,
					'type'			=> 'count_select',
					'count_number'	=> get_option('posts_per_page'),
					'inputlabel'	=> 'Number of Full Width Posts?',
					'title'			=> 'Full Width Posts (Magazine Layout Mode Only)',						
					'shortexp'		=> 'When using magazine layout mode, select the number of "featured" or full-width posts.',
					'exp'			=> 'Select the number of posts you would like shown at the full width of the main content column in magazine layout mode (the rest will be half-width post "clips").'
				),

			'posts_page_layout' => array(
				'default' 	=> "one-sidebar-right",
				'type' 		=> 'layout_select',
				'title' 	=> 'Default Posts Page Layout',
				'inputlabel'	=> 'Select Default Posts Layout',	
				'layout' 	=> 'interface',						
				'shortexp' 	=> 'Select the layout that will be used on posts pages',
				'exp' 		=> 'This layout will be used on all non-meta posts pages. These include author pages, tags, categories, and most importantly your blog page. Set up the dimensions of these in the Layout Editor panel.',
			),
			'thumb_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'thumb_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> true),
						'thumb_single'		=> array('inputlabel'=>'Single Post Pages', 'default'=> false),
						'thumb_search' 		=> array('inputlabel'=>'Search Results', 'default'=> false),
						'thumb_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> true),
						'thumb_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> true),
						'thumb_clip' 		=> array('inputlabel'=>'In Post Clips (Magazine Mode)', 'default'=> true),
					),
					'title'		=> 'Post Thumbnail Placement',
					'shortexp'	=> 'Where should the theme use post thumbnails?',
					'exp'		=> 'Use this option to control where post "featured images" or thumbnails are used. Note: The post clips option only applies when magazine layout is selected.'
			),
			'excerpt_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
						'excerpt_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> true),
						'excerpt_single'	=> array('inputlabel'=>'Single Post Pages', 'default'=> false),
						'excerpt_search'	=> array('inputlabel'=>'Search Results', 'default'=> true),
						'excerpt_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> true),
						'excerpt_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> true),
					),
					'title'		=> 'Post Excerpt or Summary Handling',
					'shortexp'	=> 'Where should the theme use post excerpts when showing full column posts?',
					'exp'		=> 'This option helps you control where post excerpts are displayed.<br/><br/> <strong>About:</strong> Excerpts are small summaries of articles filled out when creating a post.'
			),
			'pagetitles' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Automatically show Page titles?',
					'title'		=> 'Page Titles',						
					'shortexp'	=> 'Show the title of pages above the page content.',
					'exp'		=> 'This option will automatically place page titles on all pages.'
			),
			'continue_reading_text' => array(
					'version'	=> 'pro',
					'default'	=> 'Read Full Article &rarr;',
					'type'		=> 'text',
					'inputlabel'	=> 'Continue Reading Link Text',
					'title'		=> '"Continue Reading" Link Text (When Using Excerpts)',						
					'shortexp'	=> 'The link at the end of your excerpt.',
					'exp' 		=> "This text will be used as the link to your full article when viewing articles on your posts page (when excerpts are turned on)."
			),
			'content_handling' => array(
					'type'		=> 'check_multi',
					'selectvalues'=> array(
						'content_blog'		=> array('inputlabel'=>'Posts/Blog Page', 'default'=> false),
						'content_search'	=> array('inputlabel'=>'Search Results', 'default'=> false),
						'content_category' 	=> array('inputlabel'=>'Category Lists', 'default'=> false),
						'content_archive' 	=> array('inputlabel'=>'Post Archives', 'default'=> false),
					),
					'title'		=> 'Full Post Content',
					'shortexp'	=> 'In addition to single post pages and page templates, where should the theme place the full content of posts?',
					'exp'		=> 'Choose where the full content of posts is displayed. Choose between all posts pages or just single post pages (i.e. posts pages can just show excerpts or titles).'
			),

			'post_footer_social_text' => array(
					'default'	=> 'If you enjoyed this article, please consider sharing it!',
					'type'		=> 'text',
					'inputlabel'	=> 'Post Footer Social Links Text',
					'title'		=> 'Post Footer Social Links Text',						
					'shortexp'	=> 'The text next to your social icons',
					'exp'		=> "Set the text next to your social links shown on single post pages or on all " . 
							 "posts pages if the post footer link is set to 'always sharing links'."
			),

			'post_footer_share_links' => array(
					'default'	=> '',
					'type'		=> 'check_multi',
					'selectvalues'	=> array(
					
						'share_facebook'	=> array('inputlabel'=>'Facebook Sharing Icon', 'default'=> true),
						'share_twitter'		=> array('inputlabel'=>'Twitter Sharing Icon', 'default'=> true),
						'share_twitter_cache'=>array('inputlabel'=>'Enable twitter short urls', 'default'=> false),
						'share_delicious'	=> array('inputlabel'=>'Del.icio.us Sharing Icon', 'default'=> true),
						'share_reddit'		=> array('inputlabel'=>'Reddit Sharing Icon', 'default'=> true),
						'share_digg'		=> array('inputlabel'=>'Digg Sharing Icon', 'default'=> false),
						'share_stumbleupon'	=> array('inputlabel'=>'StumbleUpon Sharing Icon', 'default'=> false)
					),
					'inputlabel'	=> 'Select Which Share Links To Show',
					'title'		=> 'Post Footer Sharing Icons',						
					'shortexp'	=> 'Select Which To Show',
					'exp'		=> "Select which icons you would like to show in your post footer when sharing " . 
							 "links are shown."
		    ), 
			'excerpt_len' => array(
					'version'	=> 'pro',
					'default' 	=> 55,
					'type' 		=> 'text',
					'inputlabel' 	=> 'Number of words.',
					'title' 	=> 'Excerpt Length',
					'shortexp' 	=> '',
					'exp' 		=> 'Excerpts are set to 55 words by default.'
			),
			'excerpt_tags' => array(
					'version'	=> 'pro',
					'default' 	=> '<p><br><a>',
					'type' 		=> 'text',
					'inputlabel' 	=> 'Allowed Tags',
					'title' 	=> 'Allow Tags in Excerpt',
					'shortexp' 	=> 'Control which tags are stripped from excerpts.',
					'exp' 		=> 'By default WordPress strips all HTML tags from excerpts. You can use this option to allow certain tags. Simply enter the allowed tags in this field. <br/>An example of allowed tags could be: <strong>&lt;p&gt;&lt;br&gt;&lt;a&gt;</strong>. <br/><br/> <strong>Note:</strong> Enter a period "<strong>.</strong>" to disallow all tags.'
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
					'inputlabel'	=> 'Include Google IE Compatibility Script?',
					'title'		=> 'Google IE Compatibility Fix',
					'shortexp'	=> 'Include a Google JS script that fixes problems with IE.',
					'exp'		=> 'More info on this can be found here: <strong>http://code.google.com/p/ie7-js/</strong>.'
			),
			'partner_link' 	=> array(
					'default'	=> '',
					'type'		=> 'text',
					'inputlabel'	=> 'Enter Partner Link',
					'title'		=> 'PageLines Partner Link',
					'shortexp'	=> 'Change your PageLines footer link to a partner link',
					'exp'		=> 'If you are a <a href="http://www.pagelines.com/partners">PageLines Partner</a> enter your link here and the footer link will become a partner or affiliate link.'
			),
/*
 * TODO Do we leave this and reuse for vanilla?
*/

//			'forum_options' => array(
//					'default' 	=> '',
//					'type' 		=> 'check',
//					'inputlabel' 	=> 'Show bbPress Forum Addon Options',
//					'title' 	=> 'Activate Forum Options',
//					'shortexp'	=> 'If you have integrated a PageLines bbPress forum, activate its options here.',
//					'exp' 		=> 'This theme has some integrated options for its bbPress forum addon (if installed).'
//			),

			'disable_ajax_save' => array(
					'default'	=> '',
					'type'		=> 'check',
					'inputlabel'	=> 'Disable AJAX Saving?',
					'title'		=> 'Disable AJAX Saving',
					'shortexp'	=> 'Check to disable AJAX saving.',
					'exp'		=> "Check this option if you are having problems with AJAX saving. For example, if design control or typography options aren't working"
			),

			'enable_debug' => array(
					'default' => '',
					'version'	=> 'pro',
					'type' => 'check',
					'inputlabel' => 'Enable debug settings tab?',
					'title' => 'PageLines debug',
					'shortexp' => 'Show detailed settings information.',
					'exp' => "This information can be useful in the forums if you have a problem."
			),

			'hide_introduction' => array(
					'default' => '',
					'version'	=> 'pro',
					'type' => 'check',
					'inputlabel' => 'Hide the introduction?',
					'title' => 'Show Theme Introduction',
					'shortexp' => 'Uncheck this option to show theme introduction.',
					'exp' => ""
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
					'type' 		=> 'textarea',
					'layout' 	=> 'full',
					'inputlabel' 	=> 'CSS Rules',
					'title' 	=> 'Custom CSS',
					'shortexp' 	=> 'Insert custom CSS styling here. It will be stored in the DB and not overwritten. <br/>Note: The easiest way to customize your site is using "Base" the child theme for PlatformPro.',
					'exp' 		=> '<div class="theexample">Example:<br/> <strong>body{<br/> &nbsp;&nbsp;color:  #3399CC;<br/>&nbsp;&nbsp;line-height: 20px;<br/>&nbsp;&nbsp;font-size: 11px<br/>}</strong></div>Enter CSS Rules to change the style of your site.<br/><br/> A lot can be accomplished by simply changing the default styles of the "body" tag such as "line-height", "font-size", or "color" (as in text color).', 
					'docslink'	=> 'http://www.pagelines.com/docs/changing-colors-fonts', 
					'vidtitle'	=> 'View Customization Documentation'
				),

			'headerscripts' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Headerscripts Code',
					'title'		=> 'Header Scripts',
					'shortexp'	=> 'Scripts inserted directly before the end of the HTML &lt;head&gt; tag',
					'exp'		=> ''
				),
			'footerscripts' => array(
					'default'	=> '',						
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Footerscripts Code or Analytics',
					'title'		=> 'Footer Scripts &amp; Analytics',
					'shortexp'	=> 'Any footer scripts including Google Analytics',
					'exp'		=> ''
				), 
			'asynch_analytics' => array(
					'version'	=> 'pro',
					'default'	=> '',
					'type'		=> 'textarea',
					'layout'	=> 'full',
					'inputlabel'	=> 'Asynchronous Analytics',
					'title'		=> 'Asynchronous Analytics',
					'shortexp'	=> 'Placeholder for Google asynchronous analytics. Goes underneath "body" tag.',
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
				'inputlabel'	=> 'Show tags in sidebar?',
				'title'		=> 'Tag Cloud In Sidebar',
				'shortexp'	=> 'Including post tags on the forum sidebar.',
				'exp'		=> 'Tags are added by users and moderators on your forum and can help people locate posts.'
			),
			'forum_image_1'		=> array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'inputlabel'	=> 'Upload Forum Image',
				'imagepreview'	=> 125,
				'title'		=> 'Forum Sidebar Image #1',
				'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
				'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
			),
			'forum_image_link_1' => array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'	=> 'Image Link URL',
				'title'		=> 'Forum Image #1 Link',
				'shortexp'	=> 'Full URL for your forum image.',
				'exp'		=> "Add the full url for your forum image."
			),
			'forum_image_2' => array(
				'default'	=> '',
				'type'		=> 'image_upload',
				'imagepreview'	=> 125,
				'inputlabel'	=> 'Upload Forum Image',
				'title'		=> 'Forum Sidebar Image #2',
				'shortexp'	=> 'Add a 125px by 125px image to your forum sidebar',
				'exp'		=> "Spice up your forum with a promotional image in the forum sidebar."
			),
			'forum_image_link_2'	=> array(
				'default'	=> '',
				'type'		=> 'text',
				'inputlabel'	=> 'Image Link URL',
				'title'		=> 'Forum Image #2 Link',
				'shortexp'	=> 'Full URL for your forum image.',
				'exp'		=> "Add the full url for your forum image."
			),
			'forum_sidebar_link'	=> array(
				'default'	=> '#',
				'type'		=> 'text',
				'inputlabel'	=> 'Forum Image Caption URL',
				'title'		=> 'Forum Caption Link URL (Text Link)',
				'shortexp'	=> 'Add the URL for your forum caption (optional)',
				'exp'		=> "Text link underneath your forum images."
			),
			'forum_sidebar_link_text' => array(
				'default'	=> 'About '.get_bloginfo('name'),
				'type'		=> 'text',
				'inputlabel'	=> 'Forum Sidebar Link Text',
				'title'		=> 'Forum Sidebar Link Text',
				'shortexp'	=> 'The text of your image caption link',
				'exp'		=> "Change the text of the caption placed under your forum images."
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
			'theme_introduction'	=> array(
				'type'		=> 'text_content',
				'layout'	=> 'full',
				'exp'		=> $welcome->get_welcome()
			),
			'hide_introduction'	=> array(
				'default'	=> '',
				'type'		=> 'check',
				'inputlabel'	=> '',
				'inputlabel'	=> 'Hide the introduction',
				'title'		=> 'Remove This Theme Introduction',
				'shortexp'	=> 'Remove this introduction from the admin.',
				'exp'		=> "This introduction can be added back under the 'custom code' tab (once hidden)..."
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
 *
 *  Returns Options Array
 *
 *
 *
 */
function get_option_array( $load_unavailable = false ){
	
	
	$default = new PageLinesOptionsArray();
	 
	$optionarray =  array_merge(load_section_options('new', 'top', $load_unavailable), $default->options, load_section_options('new', 'bottom', $load_unavailable), $default->last_options);
	
	if(isset($custom_options['custom_options']) && !empty($custom_options['custom_options']))
		$optionarray = array_merge($optionarray, $custom_options);
	
	foreach($optionarray as $optionset => $options)
		$optionarray[$optionset] = array_merge( load_section_options($optionset, 'top', $load_unavailable), $options, load_section_options($optionset, 'bottom', $load_unavailable));
	
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
		
		$intro .= '<div class="finally"><h3>That\'s it for now! Have fun and good luck.</h3></div>';
		
		$intro .= '</div></div>';

		return apply_filters('pagelines_theme_intro', $intro);
	}
	
	function get_welcome_billboard(){
		
		$bill = '<div class="admin_billboard fix"><div class="admin_billboard_pad fix">';
		$bill .= '<div class="admin_theme_screenshot"><img class="" src="'.PARENT_URL.'/screenshot.png" alt="Screenshot" /></div>';
		$bill .= '<div class="admin_billboard_content"><div class="admin_header"><h3 class="admin_header_main">Congratulations!</h3></div>';
		$bill .= '<div class="admin_billboard_text">You\'re ready to build a professional website.<br/> Here are a few tips to get you started...<br/><small>(Note: This intro can be removed below.)</small></div>';
		$bill .= '<div class="clear"></div></div></div></div>';
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	
	function get_welcome_features(){
		$f = array(
			'1strule'	=> array(
				'name'			=> 'The First Rule',
				'desc'			=> 'It\'s time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.',
				'class'			=> 'feature_firstrule', 
				'icon'			=> '',
			),
			'support'	=> array(
				'name'			=> 'World Class Support',
				'desc'			=> 'For help getting started, we offer our customers tons of support including comprehensive <a href="http://www.pagelines.com/docs/" target="_blank">docs</a>, and an active, moderated <a href="http://www.pagelines.com/forum/" target="_blank">forum</a>.',
				'class'			=> 'feature_support', 
				'icon'			=> '',
			),
			'dragdrop'	=> array(
				'name'			=> 'Drag &amp; Drop Templates',
				'desc'			=> 'Check out the Template Setup panel! This is how you will control site elements using drag &amp; drop on your site. Learn more in the <a href="http://docs.pagelines.com/">docs</a>.',
				'class'			=> 'feature_templates', 
				'icon'			=> '',
			),
			'settings'	=> array(
				'name'			=> 'Your Settings',
				'desc'			=> 'This panel is where you will start the customization of your website. Any options applied through this interface will make changes site-wide.<br/> ',
				'class'			=> 'feature_options', 
				'icon'			=> '',
			),
			'widgets'	=> array(
				'name'			=> 'Draggable Layout &amp; Widgets',
				'desc'			=> 'Use the Layout Editor to control your content layout.  There are also several "widgetized" areas that are controlled through your widgets panel.',
				'class'			=> 'feature_dynamic', 
				'icon'			=> '',
			),
			'metapanel'	=> array(
				'name'			=> 'MetaPanel',
				'desc'			=> 'You\'ll find the MetaPanel at the bottom of WordPress page/post creation pages.  It will allow you to set options specific to that page or post.',
				'class'			=> 'feature_meta', 
				'icon'			=> '',
			),
		);
		
		return apply_filters('pagelines_welcome_features', $f);
	}
	
	function get_plugins_billboard(){
		
		$billboard = '<div class="admin_billboard plugins_billboard"><div class="admin_billboard_content"><div class="feature_icon"></div><h3 class="admin_header_main">Plugins</h3> <p>Although '.THEMENAME.' is universally plugin compatible, we have added "advanced" graphical/functional support for several WordPress plugins.</p><p> It\'s your responsibility to install each plugin, which can be done through "<strong>plugins</strong>" &gt; "<strong>Add New</strong>" or through the <strong>developer\'s site</strong> where you can download them manually (e.g. CForms).</p>';
			
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
	
	function get_welcome_plugins(){
		$plugins = array(
			'postorder'	=> array(
				'name'			=> 'Post Types Order',
				'url'			=> 'http://wordpress.org/extend/plugins/post-types-order/', 
				'desc'			=> 'Allows you to re-order custom post types like features and boxes.',
			),
			'disqus'	=> array(
				'name'			=> 'Disqus Comments',
				'url'			=> 'http://wordpress.org/extend/plugins/disqus-comment-system/', 
				'desc'			=> 'Improve your commenting system',
			),
			'cforms'	=> array(
				'name'			=> 'CForms',
				'url'			=> 'http://www.deliciousdays.com/cforms-plugin/', 
				'desc'			=> 'Advanced contact forms that can be used for creating mailing lists, etc..',
			),
			'wp125'	=> array(
				'name'			=> 'WP125',
				'url'			=> 'http://wordpress.org/extend/plugins/wp125/', 
				'desc'			=> 'Used to show 125px by 125px ads or images in your sidebar. (Widget)',
			),
			'flickrrss'	=> array(
				'name'			=> 'FlickrRSS Images',
				'url'			=> 'http://eightface.com/wordpress/flickrrss/', 
				'desc'			=> 'Shows pictures from your Flickr Account.  (Widget &amp; Carousel Section)',
			),
			'nextgen'	=> array(
				'name'			=> 'NextGen-Gallery',
				'url'			=> 'http://wordpress.org/extend/plugins/nextgen-gallery/', 
				'desc'			=> 'Allows you to create image galleries with special effects.  (Carousel Section)',
			),
			'pagenavi'	=> array(
				'name'			=> 'Wp-PageNavi',
				'url'			=> 'http://wordpress.org/extend/plugins/wp-pagenavi/', 
				'desc'			=> 'Creates advanced "paginated" post navigation..',
			),
			'breadcrumb'	=> array(
				'name'			=> 'Breadcrumb NavXT',
				'url'			=> 'http://wordpress.org/extend/plugins/breadcrumb-navxt/', 
				'desc'			=> 'Displays a configurable breadcrumb nav on your site',
			)
		);
		
		return apply_filters('pagelines_welcome_plugins', $plugins);
	}
}
