<?php
/*
	Section: Vanilla Forum
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: An Embedded Vanilla Forum
	Class Name: PageLinesVanilla
	Tags: forum, discussion, developer
*/

class PageLinesVanilla extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Vanilla Forum', 'pagelines');
		$id = 'vanillaforum';
		
		$default_settings = array(
			'description' 	=> 'An Embedded Vanilla Forum',
			'icon'			=> PL_ADMIN_ICONS . '/vanilla.png', 
			'workswith'		=> array('content'),
			'version'		=> 'pro',
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }

	function section_persistent(){

			add_filter('pagelines_options_array', array(&$this, 'the_options' ));
		
			add_filter('pagelines_settings_whitelist', array(&$this, 'whitelist_option'));
				
	}
	
	function section_head() {   

		if(pagelines_option('vanilla_layout') == 'full')
			printf('<style type="text/css" id="vanilla-css">#site %1$s .content{width: 100%%;max-width: none} #site  %1$s .content-pad{padding:0;}</style>', '#'.$this->id);
	}
	
	function section_template() {    
		if(pagelines_option('vanilla_embed'))
			echo pagelines_option('vanilla_embed');
	}
	
	function the_options( $array ){
		
		$array['vanilla_forum'] = array(
			
			'icon'			=> $this->icon,
			'vanilla_embed'		=> array(
				'default'		=> '',
				'type'			=> 'textarea',
				'inputlabel'	=> 'Your Vanilla Forum Embed Code',
				'title'			=> 'Vanilla Forum Embed',
				'shortexp'		=> 'Add your Vanilla Forum embed code',
				'exp'			=> 'Enter the <strong>&lt;embed&gt;</strong> code from your Vanilla Forum install.'
			),
			'vanilla_layout'		=> array(
				'default'		=> 'full',
				'type'			=> 'select',
				'selectvalues'		=> array(
					'content'	=> array('name' => 'Default Content Width'),
					'full'		=> array('name' => 'Full Width')
				),
				'inputlabel'	=> 'Width of Vanilla Forum Theme',
				'title'			=> 'Vanilla Forum Width Format',
				'shortexp'	=> 'Vanilla forum width can be independent of standard content width',
				'exp'		=> "Your Vanilla Forum theme is designed to be either full width or the width of your content. Use this option to have it be full width, as opposed to the width of the rest of your content."
			),
			
		);
		
		
		
		return $array; 
	}
	
	function whitelist_option($a){
		$a[] = 'vanilla_embed';
		return $a;
	}







	
	
// End of Section Class //
}
