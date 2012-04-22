<?php
/*
	Section: Masthead
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width splash and text area. Great for getting big ideas across quickly.
	Class Name: PLMasthead	
	Workswith: templates, main, header, morefoot
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLMasthead extends PageLinesSection {
    
    var $tabID = 'masthead_meta';
    
	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		wp_register_style('jumbotron',$this->base_url.'/style.css',false);
		wp_enqueue_style('jumbotron');
	}
	
	function section_head($clone_id){
		
		
		?>
		
		<script>
		 
		</script>	
		
	<?php }

	function section_optionator( $settings ){
		
		$settings = wp_parse_args($settings, $this->optionator_default);
		
		$metatab_array = array(

				'pagelines_masthead_text' => array(
						'type' 				=> 'text_multi',
						'inputlabel' 		=> 'Enter text for your masthead banner section',
						'title' 			=> $this->name.' Text',	
						'selectvalues'	=> array(
							'pagelines_masthead_title'		=> array('inputlabel'=>'Title', 'default'=> ''),
							'pagelines_masthead_tagline'	=> array('inputlabel'=>'Tagline', 'default'=> '')
						),				
						'shortexp' 			=> 'The text for the masthead section',
						'exp' 				=> 'This text will be used as the title/text for the masthead section of the theme.'

				),
				'pagelines_masthead_button_text' => array(
					'type' 			=> 'text',
					'inputlabel' 	=> 'Enter Button Text',
					'title' 		=> 'Masthead Button Text',						
					'shortexp' 		=> 'Enter text for the masthead button',
					'exp' 			=> 'CSS and a button with *Start Here* will be used.'
				 ),
				'pagelines_masthead_link' => array(
					'type' => 'text',
					'inputlabel' => 'Enter the link destination (URL)',
					'title' => $this->name.' Button Link',						
					'shortexp' => 'The link destination of masthead banner section',
					'exp' => 'This URL will be used as the link for the button in the masthead.'

				),
				'pagelines_masthead_target' => array(
					'type'			=> 'check',
					'default'		=> false,
					'inputlabel'	=> 'Open link in new window.',
				),

			);
		
		$metatab_settings = array(
				'id' 		=> $this->tabID,
				'name' 		=> 'Masthead',
				'icon' 		=> $this->icon, 
				'clone_id'	=> $settings['clone_id'], 
				'active'	=> $settings['active']
			);
		
		register_metatab($metatab_settings, $metatab_array);
	}

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
   		$mast_title = ploption('pagelines_masthead_title', $this->oset);
		$mast_tag = ploption('pagelines_masthead_tagline', $this->oset);
		$butt_link = ploption('pagelines_masthead_link', $this->oset);
		$target = ( ploption( 'pagelines_masthead_target', $this->oset ) ) ? 'target="_blank"' : '';
		
		$butt_text = (ploption('pagelines_masthead_button_text', $this->oset)) ? ploption('pagelines_masthead_button_text', $this->oset) : __('Start Here', 'pagelines');

	if($mast_title){ ?>
	
	<header class="jumbotron masthead">
	  <div class="inner">
	  	<?php
	  		
	  		printf('<h1 class="masthead-title">%s</h1>',$mast_title);
	  		printf('<p class="masthead-tag">A Responsive, Drag &amp; Drop Platform for Beautiful Websites</p>',$mast_tag);

	  	?>
	    
	    <p class="download-info">

	    <?php
	    	printf('<a %s class="btn btn-primary btn-large" href="%s">%s</a>', $target, $butt_link, $butt_text);
	    ?> 
	    </p>
	  </div>
	</header>

		<?php 

		} else
			echo setup_section_notify($this, __('Set Masthead meta fields to activate.', 'pagelines') );

	}


}