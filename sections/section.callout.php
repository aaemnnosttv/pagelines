<?php
/*

	Section: News Banner
	Author: Adam Munns
	Description: Shows a callout banner with optional graphic call to action
	Version: 1.0.0
	
*/

class PageLinesCallout extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Callout Section', 'pagelines');
		$id = 'callout';
	
		
		$default_settings = array(
			'description' 	=> 'Callout Section - A banner displaying a call to action or simple text.',
			'workswith' 	=> array('content'),
			'folder' 		=> '', 
			'init_file' 	=> 'callout.php', 
			'icon'			=> PL_ADMIN_ICONS . '/speaker.png'
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );
		
	   parent::__construct($name, $id, $settings);    
   }

		
 	function section_template() { ?>
	<div id="callout-area">
		<div class="callout_text">
			<div class="callout_text-pad">
			<h2 class="callout_head <?php if(!pagelines('pagelines_callout_image')):?>noimage<?php endif;?>">
				<?php print_pagelines_option('pagelines_callout_header', __('This is the "Callout Section"','pagelines') );?>
			</h2>
			<div class="callout_copy subhead">
				<?php print_pagelines_option('pagelines_callout_subheader', __('Perfect for a call to action or a special offer.','pagelines') );?>
			</div>
			</div>
		</div>
		
		<?php if(pagelines_option('pagelines_callout_image')):?>
			<div class="callout_image">
				<a href="<?php echo pagelines_option('pagelines_callout_link');?>" >
					<img src="<?php echo pagelines_option('pagelines_callout_image');?>" />
				</a>
			</div>
		<?php endif;?>
		
	</div>
<?php }

	function section_options($optionset = null, $location = null) {

		if($optionset == 'template_setup' && $location == 'bottom'){
			return array(
					
					'pagelines_callout_text' => array(
							'default' => '',
							'type' => 'text_multi',
							'inputlabel' => 'Enter text for your callout banner section',
							'title' => $this->name.' Text',	
							'selectvalues'=> array(
								'pagelines_callout_header'		=> array('inputlabel'=>'Callout Header', 'default'=> ''),
								'pagelines_callout_subheader'	=> array('inputlabel'=>'Callout Text', 'default'=> ''),
							),				
							'shortexp' => 'The text for the callout banner section',
							'exp' => 'This text will be used as the title/text for the callout section of the theme.'

					),				
					'pagelines_callout_image' => array(
						'default' 		=> PL_IMAGES.'/callout_default.png',
						'type' 			=> 'image_upload',
						'imagepreview' 	=> '270',
						'inputlabel' 	=> 'Upload custom image',
						'title' 		=> $this->name.' Image',						
						'shortexp' 		=> 'Input Full URL to your custom header or logo image.',
						'exp' 			=> 'Replaces the default callout image.'
						),
					'pagelines_callout_link' => array(
						'default' => 'http://pagelines.com',
						'type' => 'text',
						'inputlabel' => 'Enter the link destination (URL)',
						'title' => $this->name.' Image Link',						
						'shortexp' => 'The link destination of callout banner section',
						'exp' => 'This URL will be used as the link for the callout section of the theme.'

						),
				);

		}

	}

}
/*
	End of section class
*/