<?php
/*

	Section: Breadcrumb
	Author: Adam Munns
	Description: Displays Breadcrumb Navigation on your site.
	Version: 1.0.0
	
*/

class PageLinesBreadcrumb extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Breadcrumb', 'pagelines');
		$id = 'breadcrumb';
	

		$default_settings = array(
			'description' 	=> 'Displays a breadcrumb navigation section',
			'workswith' 	=> array('main','header'),
			'icon'			=> PL_ADMIN_ICONS . '/ui-breadcrumb.png', 
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
		parent::__construct($name, $id, $settings);    
   }

   function section_template() { 
		?>
			<div class="breadcrumb subtext">
<?php
				if(function_exists('bcn_display')){
					if(pagelines_option('breadcrumb_no_link')){
						//Make new breadcrumb object
						$breadcrumb_trail = new bcn_breadcrumb_trail;
						//Setup options here if needed
						//Fill the breadcrumb trail
						$breadcrumb_trail->fill();
						//Display the trail, but don't link the breadcrumbs
						bcn_display(false,false);
					}else{
						bcn_display();
					}
				}else{
					echo '<p style=text-align:center;>';
					_e('Please activate the <strong>Breadcrumb-NavXT</strong> plug-in to use the section.', 'pagelines');
					echo '</p>';
				}
					
				
				?>
			</div>
<?php
	}
	
	
	function section_options($optionset = null, $location = null) {
	
		if($optionset == 'header_and_nav' && $location == 'bottom'){
			return array(
					'breadcrumb_no_link' => array(
							'version' => 'pro',
							'default' => false,
							'type' => 'check',
							'inputlabel' => 'Disable Breadcrumb Links?',
							'title' => 'Breadcrumb Links',						
							'shortexp' => 'Removes the links that are included in the breadcrumb nav.',
							'exp' => 'This option removes links from the breadcrumb navigation.'
						),
				);

		}
	
	}
	

}

/*
	End of section class
*/