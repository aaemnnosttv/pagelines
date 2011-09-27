<?php
/**
 * Includes functions for use in layouts
 * 
 **/


function grid( $query, $args = array() ){

	// The Query
	global $wp_query; 
	
	$wp_query = $query;
	
	$posts = $query->posts;
	if( !is_array( $posts ) )
		return;
	
	
	$defaults = array(
		'per_row'		=> 3, 
		'format'		=> 'img_grid', 
		'paged'			=> false, 
		'has_img'		=> true,
		'img_default'	=> null, 
		'img_width'		=> '100%', 
		'title'			=> '',
		'class'			=> 'pagelines-grid', 
		'row_class'		=> 'gridrow', 
		'content_len'	=> 10, 
		'callback'		=> false,
	);
	
	$a = wp_parse_args($args, $defaults);
	
	// Standard Variables
	$out = '';
	$total = count($posts);
	$count = 1;
	$default_img = ( isset($a['img_default']) ) ? sprintf('<img src="%s" alt="%s"/>', $a['img_default'], __('No Image', 'pagelines')) : '';
	
	
	// Grid loop
	foreach($posts as $pid => $p){
			setup_postdata($p); 
		// Grid Stuff
		$start = (grid_row_start( $count, $total, $a['per_row'])) ? '<div class="pprow grid-row fix">' : '';
		$end = (grid_row_end( $count, $total, $a['per_row'])) ? '</div>' : '';
		$last_class = (grid_row_end( $count, $total, $a['per_row'])) ? 'pplast' : '';
		

		// Content 
		$content = '';
		if($a['callback']){
			
			$content = call_user_func( $a['callback'], $p, $a );
			
		} else {
			
			// The Image
			$thumb = ( has_post_thumbnail( $p->ID ) ) ? get_the_post_thumbnail( $p->ID ) : $default_img;
			$image = sprintf( '<a href="%s" class="img grid-img" style="width: %s"><span class="grid-img-pad">%s</span></a>', get_permalink($p->ID), $a['img_width'], $thumb);
	
			$content .= $image;
	
			// Text
			$content .= ($a['format'] == 'media') ? sprintf('<div class="bd grid-content"><h4><a href="%s">%s</a></h4> <p>%s %s</p></div>', get_permalink($p->ID), $p->post_title, custom_trim_excerpt($p->post_content, $a['content_len']), pledit( $p->ID ) ) : '';
		
		}
		
		// Column Box Wrapper
		$out .= sprintf('%s<div class="grid-element pp%s %s %s"><div class="grid-element-pad">%s</div></div>%s', $start, $a['per_row'], $a['format'], $last_class, $content, $end);
	
		$count++;
	}
	
	if( $a['paged'] ){
		ob_start();
		pagelines_pagination();
		$pages = ob_get_clean();
	} else
		$pages = '';
	
	$title = ($a['title'] != '') ? sprintf('<h4 class="grid-title"><div class="grid-title-pad">%s</div></h4>', $a['title']) : '';
	
	$wrap = sprintf('<div class="plgrid %s"><div class="plgrid-pad">%s%s%s</div></div>', $a['class'], $title, $out, $pages);

	return $wrap;
	
}


/**
 *  Returns true on the first element in a row of elements
 **/
function grid_row_start( $count, $total_count, $perline){

	$row_count = $count + ( $perline - 1 );
	
	$grid_row_start = ( $row_count % $perline == 0 ) ? true : false;
	
	return $grid_row_start;

}

/**
 *  Returns false on the last element in a row of elements
 **/
function grid_row_end( $count, $total_count, $perline){

	
	$row_count = $count + ( $perline - 1 );
	
	$box_row_end = ( ( $row_count + 1 ) % $perline == 0 || $count == $total_count ) ? true : false;
	
	return $box_row_end;
}
