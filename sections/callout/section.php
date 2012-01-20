<?php
/*
	Section: Callout
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: Shows a callout banner with optional graphic call to action
	Class Name: PageLinesCallout
	Cloning: true
	Workswith: templates, main, header, morefoot
*/

class PageLinesCallout extends PageLinesSection {

	var $tabID = 'callout_meta';

	function section_optionator( $settings ){
		$settings = wp_parse_args($settings, $this->optionator_default);
		
			$page_metatab_array = array(
				'pagelines_callout_text' => array(
						'type' 				=> 'text_multi',
						'inputlabel' 		=> 'Enter text for your callout banner section',
						'title' 			=> $this->name.' Text',	
						'selectvalues'	=> array(
							'pagelines_callout_header'		=> array('inputlabel'=>'Callout Header', 'default'=> ''),
							'pagelines_callout_subheader'	=> array('inputlabel'=>'Callout Text', 'default'=> ''),
						),				
						'shortexp' 			=> 'The text for the callout banner section',
						'exp' 				=> 'This text will be used as the title/text for the callout section of the theme.'

				),				
				'pagelines_callout_image' => array(
					'type' 			=> 'image_upload',
					'imagepreview' 	=> '270',
					'inputlabel' 	=> 'Upload custom image',
					'title' 		=> $this->name.' Image',						
					'shortexp' 		=> 'Input Full URL to your custom header or logo image.',
					'exp' 			=> 'Replaces the default callout image.'
					),
				'pagelines_callout_link' => array(
					'type' => 'text',
					'inputlabel' => 'Enter the link destination (URL)',
					'title' => $this->name.' Image Link',						
					'shortexp' => 'The link destination of callout banner section',
					'exp' => 'This URL will be used as the link for the callout section of the theme.'

					)
			);

			$metatab_settings = array(
					'id' 		=> $this->tabID,
					'name' 		=> 'Callout Meta',
					'icon' 		=> $this->icon, 
					'clone_id'	=> $settings['clone_id'], 
					'active'	=> $settings['active']
				);

			register_metatab($metatab_settings, $page_metatab_array);

	}

		
 	function section_template() {
		$call_title = ploption('pagelines_callout_header', $this->oset);
		$call_sub = ploption('pagelines_callout_subheader', $this->oset);
		$call_img = ploption('pagelines_callout_image', $this->oset);
		$call_link = ploption('pagelines_callout_link', $this->oset);
		
		
		if($call_title || $call_img){ ?>
	<div class="callout-area fix">
		<div class="callout_text">
			<div class="callout_text-pad">
				<?php 
				
					printf( '<h2 class="callout_head %s">%s</h2>', (!$call_img) ? 'noimage' : '', $call_title);
					printf( '<div class="callout_copy subhead">%s</div>', $call_sub);
				
				?>
			</div>
		</div>
		
		<?php if( $call_img )
				printf('<div class="callout_image"><a href="%s" ><img src="%s" /></a></div>', $call_link, $call_img);
		?>
	</div>
<?php

		} else
			echo setup_section_notify($this, __('Set Callout meta fields to activate.', 'pagelines') );
	}
}
/*
	End of section class
*/