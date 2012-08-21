<?php
/**
 * 
 *
 *  PageLines Front End Template Class
 *
 *
 *  @package PageLines Framework
 *  @subpackage Sections
 *  @since 3.0.0
 *  
 *
 */
class PageLinesTemplateHandler {

	var $section_list = array();

	function __construct( ) {
		
		// 1. Grab Option for Template Config on Page
		
		// 2. Deserialize and Treat Array, get in right format
		
		// 3. Create An Array of All Section on Page
		
		// 4. Parse And Render Section Areas

		global $pl_section_factory; 
		
		$this->factory = &$pl_section_factory->sections; // pass by reference
		
		$this->editor = new EditorInterface;
		
		$this->map = $this->dummy_config();

		$this->parse_config();
		
		$this->setup_processing();
		
	}
	
	function dummy_config(){
		$t = array();
		
		$t['template'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Template Area',
				'content'	=> array(
					'PLMasthead' => array( ), 
					'PageLinesBoxes' => array( ), 
					'PageLinesFeatures'=> array( ),
					'PageLinesBoxesID2'=> array(
						'clone'	=> 2, 
						'width'	=> .5,
 					), 
					'eColumn' => array( 
						'width' => .5,
						'content'	=> array( 
							'PageLinesHighlight' => array( )
						)
					), 
					'PageLinesContentBoxID3' => array('width' => '50%'),
					'PageLinesHighlight' => array( ), 
				)
			)
			
		);
		
		$t['header'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Header',
				'content'	=> array(
					'PageLinesBranding' => array( )
				)
			)
			
		);
		
		$t['page_footer'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Page Footer',
				'content'	=> array(
					'PageLinesTwitterBar' => array( )
				)
			)
			
		);
		$t['footer'] = array(
			'area-1'	=> array(
				'height'	=> 200,
				'name'		=> 'Body Footer',
				'content'	=> array(
					'SimpleNav' => array( )
				)
			)
			
		);
		
		return $t;
		
		
	}
	
	function meta_defaults($key){
		
		$p = splice_section_slug($key);
		
		$defaults = array(
			'id'		=> $p['section'],
			'clone'		=> $p['clone_id'],  
			'content'	=> array(),
			'width'		=> 1,
		);
		
		return $defaults;
	}
	
	function parse_config(){
		foreach($this->map as $group => &$g){
			foreach($g as $area => &$a){
				foreach($a['content'] as $key => &$meta){
				
					$meta = wp_parse_args($meta, $this->meta_defaults($key));
				
					if(!empty($meta['content'])){
						foreach($meta['content'] as $subkey => &$sub_meta){
							$sub_meta = wp_parse_args($sub_meta, $this->meta_defaults($subkey));
							$this->section_list[$subkey] = $sub_meta;
						}
						unset($sub_meta); // set by reference
					
						$this->section_list[$key] = $meta;
					}else		
						$this->section_list[$key] = $meta;
				}
				unset($meta); // set by reference
			}
			unset($a); // set by reference
		}
		
	}
	
	function setup_processing(){
		
		global $pl_section_factory;
		
		foreach($this->section_list as $key => $meta){
			
			if( $this->in_factory( $meta['id'] ) ){
				$this->factory[ $meta['id'] ]->meta = $meta;
			}else
				unset($this->section_list[$key]);
				
		}
				
	}
	
	function process_styles(){
		
		/*
			TODO add !has_action('override_pagelines_css_output')
		*/
		foreach($this->section_list as $key => $meta){

			if($this->in_factory( $meta['id'] )) {

				$s = $this->factory[ $meta['id'] ];

				$s->section_styles();
				
				// Auto load style.css for simplicity if its there.
				if( is_file( $s->base_dir . '/style.css' ) ){

					wp_register_style( $s->id, $s->base_url . '/style.css', array(), $s->settings['p_ver'], 'screen');
			 		wp_enqueue_style( $s->id );

				}
			}	
		}
	}
	
	function process_head(){
		
		foreach($this->section_list as $key => $meta){
		
			if( $this->in_factory( $meta['id'] ) ){

				$s = $this->factory[ $meta['id'] ];
				$s->setup_oset( $meta['clone'] ); // refactor

				ob_start();

					$s->section_head( $meta['clone'] );	

				$head = ob_get_clean();

				if($head != '')
					echo pl_source_comment($s->name.' | Section Head') . $head;
				

			}	
		}
	}
	
	function process_area( $area = 'template' ){
		
		if(!isset($this->map[ $area ]))
			return;
		
		foreach( $this->map[ $area ] as $area => $a ){
			
			$this->editor->area_start($a);
			
			foreach($a['content'] as $key => $meta){
			
				if( $this->in_factory( $meta['id'] ) ){
					
					$s = $this->factory[ $meta['id'] ];

					$s->setup_oset( $meta['clone'] ); // refactor

					ob_start();

						$s->section_template_load( $meta['clone'] ); // Check if in child theme, if not load section_template

					$output =  ob_get_clean(); // Load in buffer, so we can check if empty
				
					if(isset($output) && $output != ''){
						
						echo pl_source_comment($s->name . ' | Section Template', 2); // Add Comment 

						$s->before_section_template(  ); // refactor into before_section
						
						$s->before_section( 'editor', $meta['clone']);

						$this->editor->section_controls($meta['id'], $s);

						echo $output;

						$s->after_section( 'editor' );
						
						$s->after_section_template( );
						
					}
				
					wp_reset_postdata(); // Reset $post data
					wp_reset_query(); // Reset wp_query
					
				}
			}
			
			$this->editor->area_end($a);
			
		}
	}
	
	/**
	 * Tests if the section is in the factory singleton
	 */
	function in_factory( $section ){	
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}	
	
}


