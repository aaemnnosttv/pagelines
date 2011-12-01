<?php
/**
 * PageLine Inline Help System.
 *
 *
 * @author PageLines
 *
 * @since 2.0.b21
 */

class PageLines_Inline_Help {

	function __construct() {
		
		global $wp_version;
		if ( true == ( version_compare( $wp_version, '3.3-beta1', '>=' ) ) )	
			add_filter( 'contextual_help_list', array( &$this, 'get_help' ) ,9999);
	}
	
	function get_help() {
		
		global $current_screen;		
		$this->screen = $current_screen;

		switch( $this->screen->id ) {
			
			case 'pagelines_page_pagelines_extend':
				$this->extend_help( array(
					'sections'	=> __( 'Sections', 'pagelines' ),
					'themes'	=> __( 'Themes', 'pagelines' ),
					'plugins'	=> __( 'Plugins', 'pagelines' ),
					'integrations'	=> __( 'Integrations', 'pagelines' ),					
				));
			break;
					
			
			case 'pagelines_page_pagelines_special':
				$this->extend_help( array(
					'special-blog'		=> __( 'Blog Page', 'pagelines' ),
					'special-archive'	=> __( 'Archive Page', 'pagelines' ),
					'special-category'	=> __( 'Category Page', 'pagelines' ),
					'special-search'	=> __( 'Search Results', 'pagelines' ),
					'special-tags'		=> __( 'Tag Listing', 'pagelines' ),
					'special-author'	=> __( 'Author Posts', 'pagelines' ),
					'special-404'		=> __( '404 Page', 'pagelines' )
					));
			break;
			
			case 'pagelines_page_pagelines_templates':
				$this->extend_help( array(
					'templates'		=> __( 'Templates', 'pagelines' ) 
					));
			break;
			
			case 'toplevel_page_pagelines':
			$this->extend_help( array(
				'welcome'		=> __( 'Welcome', 'pagelines' ),
				'website_setup'	=>	__( 'Website Setup', 'pagelines' ),
				'layout'		=> __( 'Layout Editor', 'pagelines' ),
				'color'			=> __( 'Color Control', 'pagelines' ),
				'typography'	=> __( 'Typography', 'pagelines' ),
				'header-footer'	=> __( 'Header and Footer', 'pagelines' ),
				'blog-posts'	=> __( 'Blog and Posts', 'pagelines' ),
				'advanced'		=> __( 'Advanced', 'pagelines' ),
				'custom'		=> __( 'Custom Code', 'pagelines' )
				));
			break;

			case 'pagelines_page_pagelines_account':
				$this->extend_help( array(
					'your_account'	=> __( 'Your Account', 'pagelines' ),
					'import'	=> __( 'Import-Export', 'pagelines' ),
				));							
			default:
			break;
		}

	}
	
	function extend_help( $helps ) {
		
		foreach( $helps as $id => $help ) {
			
			$this->screen->add_help_tab( array(
				'id'      => $id,
				'title'   => $help,
				'content' => $this->help_markup( $id ),
			));	
			$this->screen->set_help_sidebar(
		        '<p><strong>' . __( 'For more information:', 'pagelines' ) . '</strong></p>' .
		        '<p>' . __( '<a href="http://www.pagelines.com/wiki/" target="_blank">Documentation</a>' ) . '</p>' .
		        '<p>' . __( '<a href="http://www.pagelines.com/forum/" target="_blank">Support Forums</a>' ) . '</p>'
		);
		}	
	}

	function help_markup( $help ) {
		
		$markup = array(
			
			'welcome'			=>	'<p>Welcome to the PageLines Help Section! Here you can find a brief overview of each tab, as well as a link to a more detailed help doc.</p>',
			
			'website_setup'		=>	'<p>Website Setup is generally the first thing people configure when they activate PageLines.<br />
									These are the options that get your logo, brand name, and other custom elements up on your site.<br />
									For more information, click on <a href="http://www.pagelines.com/wiki/index.php?title=
									How_to_Use_the_Website_Setup_Settings" target="_blank">How to Use the Website Setup Settings</a></p>',
			
			'layout'			=>	'<p>The Layout Editor is what changes the layout of your site. You can change the dimensions of your 
									content, the number & location of your sidebar(s), etc... <br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Layout_Editor_Settings" 
									target="_blank">How to Use the Layout Editor Settings</a></p>',
									
			'color'				=>	'<p>Color Control lets you choose the main colors that will be displayed on your website. 
									It will then decide the best colors for your site\'s secondary and tertiary elements. 
									You can always edit these manually by using CSS but Color Control chooses the best 
									complementary colors to your site design. <br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Color_Control_Settings" 
									target="_blank">How to Use the Color Control Settings</a></p>',
			
			'typography'		=>	'<p>Typography allows you to change the fonts that appear on your website. 
									No need for html or css to make changes to the most common place that you might want to change your fonts. <br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Typography_Settings" 
									target="_blank">How to Use the Typography Settings</a></p>',
			
			'header-footer'		=>	'<p>The Header & Footer  settings provide flexibility and ease in setting up important site content 
									such as Dropdown Navigation, Search capability, Social links, and Copyright statements.
									<br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Header_and_Footer_Settings" 
									target="_blank">How to Use the Header and Footer Settings</a></p>',
			
			'blog-posts'		=>	'<p>The Blog And Posts settings is where you can set up the general structure and appearance of your blog post content.
									<br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Blog_and_Posts_Settings" 
									target="_blank">How to Use the Blog and Posts Settings</a></p>',
			
			'advanced'			=>	'<p>The Advanced settings contain some additional options that can be useful to solve some 
									specific issues when developing your site. These include notorious browser compatibility issues 
									with JS, server issues with Ajax and some useful other options for helping troubleshoot your 
									site or connect with the affiliate program. <br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Advanced_Settings" 
									target="_blank">How to Use the Advanced Settings</a></p>',
			
			'custom'			=>	'<p>The Custom Code setting is where you can insert your Custom CSS styling. 
									If you have any Header, Footer, or Google Analytics script, all of that goes here as well. 
									<br /><br />For more information, click on 
									<a href="http://www.pagelines.com/wiki/index.php?title=How_to_Use_the_Custom_Code_Settings" 
									target="_blank">How to Use the Custom Code Settings</a></p>',
													
			'sections'			=>	'What is a section??',
			'plugins'			=>	'What is a plugin??',
			'themes'			=>	'What is a theme ( child theme info?)',
			'integrations'		=>	'A what???',
			
			
				
			'your_account'		=>	'account',	
			'import'			=>	'import',
			
			
			'special-blog'		=>	'blog stuff',
			'special-archive'	=>	'Archive stuff',
			'special-category'	=>	'category',
			'special-search'	=>	'search',
			'special-tags'		=>	'tags',
			'special-author'	=>	'author',
			'special-404'		=>	'404'		
		);
		
		return ( isset( $markup[$help] ) ) ? $markup[ $help ] : __( 'No help for this tab yet!', 'pagelines' );		
	}
	
} //end class