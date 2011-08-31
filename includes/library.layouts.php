<?php
/**
 * Includes functions for use in layouts
 * 
 **/

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
