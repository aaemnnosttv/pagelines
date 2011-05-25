<?php
/**
 * 
 *
 *  Class for managing content layout
 *
 *
 *  @package PageLines Core
 *  @subpackage Layout
 *  @since 4.0
 *
 */
class PageLinesLayout {

	// BUILD THE PAGELINES OBJECT
		function __construct($layout_mode = null) {
			
			/*
				Get the layout map from DB, or use default
			*/
			$this->get_layout_map();
			
			/*
				If layout mode isn't set, then use the saved default mode.
			*/
			if( isset($layout_mode) ) {
				
				$this->layout_mode = $layout_mode;
				
			} elseif ( isset($this->layout_map['saved_layout'])  && !empty($this->layout_map['saved_layout']) ) {

				$layout_mode = $this->layout_map['saved_layout'];	
							
			} else {
				$layout_mode = 'one-sidebar-right';
			}
		
		
			$this->build_layout($layout_mode);
			
		}
		
		function build_layout($layout_mode){
			
			/*
				Set the current pages layout
			*/
			$this->layout_mode = $layout_mode;
			
			/*
				Get number of columns
			*/
			$this->set_columns();
			
			/*
				Set layout dimensions
			*/
			$this->set_layout_data();
			
			/*
				Set wrap dimensions for use on page
			*/
			$this->set_wrap_dimensions();
			
			/*
				Set scaled dimensions and convert for use in the JS builder
			*/
			$this->set_builder_dimensions();
			
			/*
				Generate dynamic column layout
			*/
			$this->generate_dynamic_columns();
			
		}
		
		function set_columns(){
			if($this->layout_mode == 'two-sidebar-center' || $this->layout_mode == 'two-sidebar-left' || $this->layout_mode == 'two-sidebar-right'){
				$this->num_columns = 3;
			}elseif($this->layout_mode == 'one-sidebar-left' || $this->layout_mode == 'one-sidebar-right'){
				$this->num_columns = 2;
			}else $this->num_columns = 1;
		}
		

		function get_layout_map(){
			if(pagelines_option('layout')){
				
				$this->layout_map = pagelines_option('layout');

				
			}else{
				
				$this->layout_map = $this->default_layout_setup();
				
			}
		}
		

		
		function default_layout_setup(){
			$this->content->width = 960;
			
			$this->gutter->width = 20;
			
			$def_main_two = 640;
			$def_sb_two = 320;
			
			$def_main_three = 480;
			$def_sb_three = 240;
			
			$default_map = array(
					'saved_layout' 			=> 'one-sidebar-right',
					'last_edit' 			=> 'one-sidebar-right',
					'content_width' 		=> $this->content->width,
					'one-sidebar-right' 	=> array(	
							'maincolumn_width' 		=> $def_main_two,
							'primarysidebar_width'	=> $def_sb_two,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						), 
					'one-sidebar-left' 	=> array(	
							'maincolumn_width' 		=> $def_main_two,
							'primarysidebar_width'	=> $def_sb_two,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'two-sidebar-right' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width 
						),
					'two-sidebar-left' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'two-sidebar-center' 	=> array(	
							'maincolumn_width' 		=> $def_main_three,
							'primarysidebar_width'	=> $def_sb_three,
							'gutter_width' 			=> $this->gutter->width, 
							'content_width'			=> $this->content->width
						),
					'fullwidth' 	=> array(	
							'maincolumn_width' 		=> $this->content->width,
							'primarysidebar_width'	=> 0, 
							'gutter_width' 			=> 0, 
							'content_width'			=> 0
						)
				);
				
		
			return $default_map;
		}

		

		
		function set_layout_data(){
			
			// Text & IDs
				$this->hidden->text = '';
				$this->hidden->id = 'hidden';

				$this->main_content->text = 'Main Column';
				$this->main_content->id = 'layout-main-content';

				$this->sidebar1->text = 'SB1';
				$this->sidebar1->id = 'layout-sidebar-1';

				$this->sidebar2->text = 'SB2';
				$this->sidebar2->id = 'layout-sidebar-2';
			
			$this->content->width = 960;

			$this->gutter->width = 30;
			
			$this->builder->width = 1300;
			$this->fudgefactor = 24;
		
			$this->hidden->width = 0;
			
			$this->content->width = $this->layout_map['content_width'];
			
			foreach($this->layout_map as $layoutmode => $settings){
				if($this->layout_mode == $layoutmode && ($layoutmode == 'one-sidebar-right' || $layoutmode == 'one-sidebar-left')){
					
					//Account for javascript saving of other layout type
					$this->main_content->width = $settings['maincolumn_width'];
					$this->sidebar1->width = $this->content->width - $settings['maincolumn_width'];
					
				} elseif($this->layout_mode == $layoutmode) {
				
					$this->main_content->width = $settings['maincolumn_width'];
					$this->sidebar1->width = $settings['primarysidebar_width'];
				
				}
			}
				
			$this->margin->width = ($this->builder->width - $this->content->width)/2 - ($this->fudgefactor - 1);
						
			$this->sidebar2->width = $this->content->width - $this->main_content->width - $this->sidebar1->width;
		
			$this->dynamic_grid->width = $this->content->width/12;
			
		
		}
		
		
		function set_wrap_dimensions(){
			if($this->layout_mode == "two-sidebar-center"){
				$this->column_wrap->width = $this->main_content->width + $this->sidebar1->width;
				$this->sidebar_wrap->width = $this->sidebar2->width;
				
				$this->clip->width = ($this->main_content->width - (3 * $this->gutter->width))/2 ;
				
			}elseif($this->layout_mode == "two-sidebar-right" || $this->layout_mode == "two-sidebar-left"){
				$this->column_wrap->width = $this->main_content->width;
				$this->sidebar_wrap->width = $this->sidebar1->width + $this->sidebar2->width;
				$this->clip->width = ($this->main_content->width - (2 * $this->gutter->width))/2 ;
			}elseif($this->layout_mode == "one-sidebar-right" || $this->layout_mode == "one-sidebar-left"){
				$this->column_wrap->width = $this->main_content->width;
				$this->sidebar_wrap->width = $this->sidebar1->width;
				
				$this->clip->width = ($this->main_content->width - (2 * $this->gutter->width))/2 ;
			}else{
				$this->sidebar_wrap->width = 0;
				$this->column_wrap->width = $this->main_content->width;
				$this->clip->width = ($this->main_content->width - (1 * $this->gutter->width))/2 ;
			}
		}
		
		function set_builder_dimensions(){
			
			$this->builder->bwidth 		= $this->downscale($this->builder->width);
			$this->content->bwidth 		= $this->downscale($this->content->width);
			$this->gutter->bwidth 		= $this->downscale($this->gutter->width);
			$this->margin->bwidth 		= $this->downscale($this->margin->width);
			$this->main_content->bwidth = $this->downscale($this->main_content->width);
			$this->sidebar1->bwidth		= $this->downscale($this->sidebar1->width);
			$this->sidebar2->bwidth 	= $this->downscale($this->sidebar2->width);
				
			$this->hidden->bwidth = 0;
			
			/*
				Convert builder dimensions to dimensions the plugin understands
			*/
			$this->builder_inner_directions();
		}

		function builder_inner_directions(){
			if($this->layout_mode == 'two-sidebar-right'){
				
				$this->west = $this->main_content;
				$this->center = $this->sidebar1;
				$this->east = $this->sidebar2;
			}elseif($this->layout_mode == 'two-sidebar-left'){

				$this->east = $this->main_content;
				$this->west = $this->sidebar1;
				$this->center = $this->sidebar2;
			}elseif($this->layout_mode == 'two-sidebar-center'){

				$this->east = $this->sidebar2;
				$this->west = $this->sidebar1;
				$this->center = $this->main_content;
			}elseif($this->layout_mode == 'one-sidebar-right'){
				$this->east = $this->sidebar1;
				$this->west = $this->hidden;
				$this->center = $this->main_content;
			}
			elseif($this->layout_mode == 'one-sidebar-left'){
				$this->east = $this->hidden;
				$this->west = $this->sidebar1;
				$this->center = $this->main_content;
			}elseif($this->layout_mode == 'fullwidth'){
				$this->east = $this->hidden;
				$this->west = $this->hidden;
				$this->center = $this->main_content;
			}else{
				echo 'Issue setting layout ' . $this->layout_mode;
			}
		}
		
	
		function generate_dynamic_grid(){
			
			
			
		}
		
		function generate_dynamic_columns(){
			
			$config_dynamic_layout = array(
				
					2 => array(
							'gutter' => 20
						),
					3 => array(
							'gutter' => 20 
						),
					4 => array(
							'gutter' => 20
						), 
					5 => array(
							'gutter' => 20
						)
					
				);
			foreach($config_dynamic_layout as $no_cols => $col_settings){
					$column_gutter = $col_settings['gutter'];
					$number_of_columns = $no_cols;
					
					
					$round_amount = fmod($this->content->width / $number_of_columns, 1) * $number_of_columns ;
					/*
						Set Container width (content + gutter as margin prevents the wider area from being seen)
					*/
					$this->dcol[ $number_of_columns ]->container_width = $this->content->width + $column_gutter - $round_amount;
				
					$column_space = floor( $this->dcol[ $number_of_columns ]->container_width / $number_of_columns );
		
					/*
						Base Column Width
					*/
					$this->dcol[ $number_of_columns ]->width = $column_space - $column_gutter;
					
					// Set Gutter Width
					$this->dcol[ $number_of_columns ]->gutter_width = $column_gutter;
					
					/*
						Generate Column Spans
					*/
					$this->dcol[ $number_of_columns ]->span2 = 2 * $this->dcol[ $number_of_columns ]->width + $column_gutter;
					$this->dcol[ $number_of_columns ]->span3 = 3 * $this->dcol[ $number_of_columns ]->width + 2 * $column_gutter;
					$this->dcol[ $number_of_columns ]->span4 = 4 * $this->dcol[ $number_of_columns ]->width + 3 * $column_gutter;
				
			}
		
			
		
		}
		
		
		function downscale($actual_pixels, $ratio = 2){
			return floor($actual_pixels / $ratio);
		}



}

//********* END OF LAYOUT CLASS *********//


function get_layout_mode(){
	$load_layout = new PageLinesLayout();
	$layoutmap = $load_layout->get_layout_map();
	$layout_mode = $layoutmap['layout_mode'];
	return $layout_mode;
}

	
/*
	The main content layouts available in this theme
*/
function get_the_layouts(){
	return array(
		'fullwidth', 
		'one-sidebar-right', 
		'one-sidebar-left', 
		'two-sidebar-right', 
		'two-sidebar-left', 
		'two-sidebar-center'
	);
}

function reset_layout_to_default(){
	
	$dlayout = new PageLinesLayout;
	
	$layout_map = $dlayout->default_layout_setup();

	pagelines_update_option('layout', $layout_map);
}
