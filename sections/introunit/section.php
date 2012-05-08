<?php
/*
	Section: Intro Unit
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width image and text area with button.
	Class Name: PLintroUnit	
	Workswith: templates, main, header, morefoot
*/

/*
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLintroUnit extends PageLinesSection {
    
    var $tabID = 'introunit_meta';
    

	function section_styles(){

	}
	
	function section_head($clone_id){
		
		
		?>
		
		<script>
		 
		</script>	
		
	<?php }

	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$option_array = array(

				'pagelines_introunit_text' => array(
						'type' 				=> 'text_multi',
						'inputlabel' 		=> 'Enter text for your Intro Unit section',
						'title' 			=> $this->name.' Text',	
						'selectvalues'	=> array(
							'pagelines_introunit_title'		=> array('inputlabel'=>'Title', 'default'=> ''),
							'pagelines_introunit_tagline'	=> array('inputlabel'=>'Tagline', 'default'=> '')
						),				
						'shortexp' 			=> 'The text for the Intro Unit section',
						'exp' 				=> 'This text will be used as the title/tagline for the Intro Unit section.'

				),
				'pagelines_introunit_image' => array(
					'type' 			=> 'image_upload',
					'imagepreview' 	=> '270',
					'inputlabel' 	=> 'Upload custom image',
					'title' 		=> $this->name.' Image',						
					'shortexp' 		=> 'Input Full URL to your custom Intro Unit image.',
					'exp' 			=> 'Places a custom image to the right of the call to action and text.'
				),
				'pagelines_introunit_cta' => array(
					'type'		=> 'multi_option', 
					'title'		=> __('Intro Unit Action Button', 'pagelines'), 
					'shortexp'	=> __('Enter the options for the Intro Unit button', 'pagelines'),
					'selectvalues'	=> array(
						'introunit_button_link' => array(
							'type' => 'text',
							'inputlabel' => 'Button link destination (URL - Required)',
						),
						'introunit_button_text' => array(
							'type' 			=> 'text',
							'inputlabel' 	=> 'Intro Unit Button Text',					
						),		
						'introunit_button_target' => array(
							'type'			=> 'check',
							'default'		=> false,
							'inputlabel'	=> 'Open link in new window.',
						),
						'introunit_button_theme' => array(
							'type'			=> 'select',
							'default'		=> false,
							'inputlabel'	=> 'Select Button Color',
							'selectvalues'	=> array(
								'primary'	=> array('name' => 'Blue'), 
								'warning'	=> array('name' => 'Orange'), 
								'danger'	=> array('name' => 'Red'), 
								'success'	=> array('name' => 'Green'), 
								'info'		=> array('name' => 'Light Blue'), 
								'reverse'	=> array('name' => 'Grey'), 
							),
						),
					),
				),			
		);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Intro Unit',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $option_array);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 

   		$intro_title = ploption( 'pagelines_introunit_title', $this->tset );
		$intro_tag = ploption( 'pagelines_introunit_tagline', $this->tset );
		$intro_img = ploption( 'pagelines_introunit_image', $this->tset );
		$intro_butt_link = ploption( 'introunit_button_link', $this->oset );
		$intro_butt_text = ploption( 'introunit_button_text', $this->oset );
		$intro_butt_target = ploption( 'introunit_button_target', $this->oset );
		$intro_butt_theme = ploption( 'introunit_button_theme', $this->oset );

   		if($intro_title){ ?>

	   	<div class="pl-introunit-wrap row">

			<div class="pl-introunit span6">
				<?php

					if($intro_title)
						printf('<h1>%s</h1>',$intro_title);
					
					if($intro_tag)
		  				printf('<p>%s</p>',$intro_tag);
	  			
	  			    if($intro_butt_link)
					printf('<a %s class="btn btn-%s btn-large" href="%s">%s</a> ', $intro_butt_target, $intro_butt_theme, $intro_butt_link, $intro_butt_text);
	  			?>
			</div>

			<div class="pl-introunit-image span6">
				<?php 
				    
					if($intro_img)
						printf('<div class="introunit_image"><img class="pl-imageframe" src="%s" /></div>', $intro_img);
					
				?>
			</div>

		</div>

		<?php

		} else
			echo setup_section_notify($this, __('Set intro Unit meta fields to activate.', 'pagelines') );
	}

}