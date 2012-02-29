<?php

$up = new PageLinesUpgradePaths;

class PageLinesUpgradePaths {
	

	/**
	*
	* @TODO document
	*
	*/
	function __construct() {
		
		/**
		* Upgrade from Platform(pro) to PageLines and import settings.
		*/
		if ( !is_array( $a = get_option( PAGELINES_SETTINGS ) ) )
			$this->upgrade();
			
		/**
		* Insure the correct default logo is being displayed after import.
		*/			
		if ( ! VPRO && 'logo-platform.png' == basename( $a['pagelines_custom_logo'] ) )
			plupop( 'pagelines_custom_logo',  PL_IMAGES . '/logo.png' );
	}

	/**
	*
	* @TODO document
	*
	*/
	function upgrade() {

		if ( is_array( $settings = get_option( PAGELINES_SETTINGS_LEGACY ) ) ) {
		
			// beta versions will all be using the old array...
			if ( isset( $settings['pl_login_image']) )
				$this->beta_upgrade( $settings );
			else 
				$this->full_upgrade( $settings );
		}
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