<?php
/*
	Section: Highlight Section
	Author: Andrew Powers
	Author URI: http://www.pagelines.com
	Description: Adds a highlight sections with a splash image and 2-big lines of text.
	Class Name: PageLinesHighlight
	Tags: internal
*/

class PageLinesHighlight extends PageLinesSection {

   function __construct( $registered_settings = array() ) {
	
		$name = __('Highlight', 'pagelines');
		$id = 'highlight';
	
		
		$default_settings = array(
			'description' 	=> 'Adds a highlight section with a splash image, and optional header/subheader text. Set up on individual pages/posts.',
			'workswith' 	=> array('templates', 'main', 'header', 'morefoot'),
			'failswith'		=> array('posts', '404'),
			'icon'			=> PL_ADMIN_ICONS . '/highlight.png', 
			'version'		=> 'pro', 
			'cloning'		=> true
		);
		
		$settings = wp_parse_args( $registered_settings, $default_settings );

	   parent::__construct($name, $id, $settings);    
   }

	
	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(

				'_highlight_head' => array(
					'version' 		=> 'pro',
					'type' 			=> 'text',
					'size'			=> 'big',		
					'title' 		=> 'Highlight Header Text (Optional)',
					'shortexp' 		=> 'Add the main header text for the highlight section.'
				),
				'_highlight_subhead' => array(
					'version' 		=> 'pro',
					'type' 			=> 'text',
					'size'			=> 'big',		
					'title' 		=> 'Highlight Subheader Text (Optional)',
					'shortexp' 		=> 'Add the main subheader text for the highlight section.'
				),
				
				'_highlight_splash' => array(
					'version' 		=> 'pro',
					'type' 			=> 'image_upload',	
					'inputlabel'	=> 'Upload Splash Image',	
					'title' 		=> 'Highlight Splash Image (Optional)',
					'shortexp' 		=> 'Upload an image to use in the highlight section (if activated)'
				),
				'_highlight_splash_position' => array(
					'version' 		=> 'pro',
					'type' 			=> 'select',		
					'title' 		=> 'Highlight Image Position',
					'shortexp' 		=> 'Select the position of the highlight image.',
					'selectvalues'=> array(
						'top'			=> array( 'name' => 'Top' ),
						'bottom'	 	=> array( 'name' => 'Bottom' )
					),
				),
			);
		
		$metatab_settings = array(
				'id' 		=> 'highlight_meta',
				'name' 		=> "Highlight Section",
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $metatab_array);
	}

	function section_template() { 
		global $pagelines_ID;   
 		
		$h_head = get_pagelines_meta('_highlight_head', $pagelines_ID);
		$h_subhead = get_pagelines_meta('_highlight_subhead', $pagelines_ID);
		$h_splash = get_pagelines_meta('_highlight_splash', $pagelines_ID);
		$h_splash_position = get_pagelines_meta('_highlight_splash_position', $pagelines_ID);
		
		?>
	<?php if($h_head || $h_subhead || $h_splash):?>
		<div class="highlight-area">
			
			<?php if($h_splash_position == 'top' && $h_splash):?> 
				<div class="highlight-splash hl-image-top">
					<img src="<?php echo $h_splash; ?>" alt="" />
				</div>
			<?php 	endif;	?>
			
			<?php if($h_head):?> 
				<h1 class="highlight-head">
					<?php echo $h_head; ?>
				</h1>
			<?php endif;?>
	
			<?php if($h_subhead):?> 
				<h3 class="highlight-subhead subhead">
					<?php echo $h_subhead; ?>
				</h3>
			<?php endif;?>
	
			<?php if( $h_splash_position != 'top' && $h_splash):?> 
				<div class="highlight-splash hl-image-bottom">
					<img src="<?php echo $h_splash; ?>" alt="" />
				</div>
			<?php 	endif;	?>
		</div>
	<?php endif;?>
<?php 
 
	}

} /* End of section class */