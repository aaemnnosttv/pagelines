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
		$this->init();
	}
	
	function init(){
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
					'import'	=> __( 'Import-Export', 'pagelines' ),
					'account'	=> __( 'Account', 'pagelines' )
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
				'custom'		=> __( 'Custom Code')
				));
			break;
							
			default:
			break;
		}

	}
	
	function extend_help( $helps ) {

		if ( !method_exists( $this->screen, 'add_help_tab' ) )
			return;
		
		foreach( $helps as $id => $help ) {
			
			$this->screen->add_help_tab( array(
				'id'      => $id,
				'title'   => $help,
				'content' => $this->help_markup( $id ),
			));	
		
		}		
	}

	function help_markup( $help ) {
		
		$markup = array(
			
			'sections'			=> 'This is the sections markup',
			'plugins'			=> 'This is the plugins markup',
			'themes'			=> 'This is the themes markup',			
			'import'			=> 'This is the import markup',
			'ccount'			=> 'This is the account markup',	
			
			'special-blog'				=> 'blog stuff',
			'special-archive'			=> 'Archive stuff',			
		);
		
		return ( isset( $markup[$help] ) ) ? $markup[ $help ] : __( 'No help for this tab yet!', 'pagelines' );		
	}
	
} //end class