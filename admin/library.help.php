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
			
			'sections'			=> 'What is a section??',
			'plugins'			=> 'What is a plugin??',
			'themes'			=> 'What is a theme ( child theme info?)',
			'integrations'		=> 'A what???',		
			'your_account'			=> __( 'To be able to receive PageLines updates you have to setup your account credentials.' , 'pagelines' ),	
			'import'			=> __( 'Export and Import your settings.', 'pagelines' ),
			'special-blog'				=> 'blog stuff',
			'special-archive'			=> 'Archive stuff',			
		);
		
		return ( isset( $markup[$help] ) ) ? $markup[ $help ] : __( 'No help for this tab yet!', 'pagelines' );		
	}
	
} //end class