<?php

class PageLinesUpgradePaths {
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct() {
		
		if ( ! VPRO && 'pagelines' == basename( get_bloginfo('url') ) ) {
			
			update_option( PAGELINES_SETTINGS, pagelines_settings_defaults() );
			update_option( PAGELINES_TEMPLATE_MAP, the_template_map() );
		}
			
		/**
		* Insure the correct default logo is being displayed after import.
		*/			
		if ( ! VPRO && 'logo-platform.png' == basename( ploption( 'pagelines_custom_logo' ) ) )
			plupop( 'pagelines_custom_logo',  PL_IMAGES . '/logo.png' );


		/**
		* Fix broken repeated excerpt problem on pagelines.com
		*/			
		if ( ! VPRO && 'pagelines' == basename( get_bloginfo('url') ) ) {
			
			if ( ! isset( $a['content_blog'] ) || true != $a['content_blog'] )
				plupop( 'content_blog', true );
			
			if ( ! isset( $a['content_blog'] ) || true == $a['excerpt_blog'] )
				plupop( 'excerpt_blog', false );
			
			/**
			* Fix broken templates
			*/		
			$t = ( array ) get_option( PAGELINES_TEMPLATE_MAP, the_template_map() );

			if ( 'PageLinesQuickSlider' != $t['main']['templates']['posts']['sections'][0] ) {
				array_unshift( $t['main']['templates']['posts']['sections'], 'PageLinesQuickSlider' );
				update_option( PAGELINES_TEMPLATE_MAP, $t );
			}
			
			plupop( 'pagelines_version', CORE_VERSION );
		}
		/**
		* Upgrade from Platform(pro) to PageLines and import settings.
		*/
		
		$pagelines = get_option( PAGELINES_SETTINGS );
		$platform = get_option( PAGELINES_SETTINGS_LEGACY );
		
		if ( is_array( $pagelines ) ) {
			
			if ( ! isset( $settings['pagelines_version'] ) )
				plupop( 'pagelines_version', CORE_VERSION );
			
			// were done.
			return;
		}
		
		if ( is_array( $platform ) ) {
			
			$this->upgrade( $platform );			
		}
	}

	/**
	*
	* @TODO document
	*
	*/
	function upgrade( $settings ) {

		
			// beta versions will all be using the old array...
			if ( isset( $settings['pl_login_image']) )
				$this->beta_upgrade( $settings );
			else 
				$this->full_upgrade( $settings );
	}

	/**
	*
	* @TODO document
	*
	*/
	function full_upgrade( $settings ) {
		
		// here we go, 1st were gonna set the defaults
		add_option( PAGELINES_SETTINGS, pagelines_settings_defaults() );
		add_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );
		
		$defaults = get_option( PAGELINES_SETTINGS );

		// copy the template-maps
		update_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );

		// now were gonna merge...
	
		foreach( $settings as $key => $data ) {
		
			if ( isset( $defaults[$key]) ) {
				if ( !empty( $data ) )
					plupop( $key, $data );
			}
		}
		plupop( 'pagelines_version', CORE_VERSION );
	}

	/**
	*
	* @TODO document
	*
	*/
	function beta_upgrade( $settings ) {
		
		update_option( PAGELINES_SETTINGS, $settings );
		update_option( PAGELINES_TEMPLATE_MAP, get_option( PAGELINES_TEMPLATE_MAP_LEGACY ) );		
	}		
}

new PageLinesUpgradePaths;