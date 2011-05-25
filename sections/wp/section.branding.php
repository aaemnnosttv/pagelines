<?php
/*

	Section: Branding
	Author: Andrew Powers
	Description: Shows the main site logo or the site title and description.
	Version: 1.0.0
	
*/

class PageLinesBranding extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Site Branding', 'pagelines');
		$id = 'branding';
	
		
		$settings = array(
			'type' 				=> 'header',
			'workswith'		 	=> array('header'),
			'description' 		=> 'Shows the main site logo or the site title and description.',
			'icon'				=> PL_ADMIN_ICONS . '/megaphone.png'
		);
		

	   parent::__construct($name, $id, $settings);    
   }

   function section_template() {
		// included in theme root for easy editing.
		//include( TEMPLATEPATH.'/template.branding.php' );
		get_template_part( 'template.branding' ); 
		
}

	function section_head(){}

	function section_scripts() {}

	function section_options() {
	
	}

}

/*
	End of section class
*/