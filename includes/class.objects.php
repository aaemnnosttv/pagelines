<?php
/**
 * 
 *
 *  PageLines Color Calculations and Handling
 *
 *
 *  @package PageLines Core
 *  @subpackage Post Types
 *  @since 2.0.b6
 *
 */
class PLObs {

	
	function __contruct(){}
		
	function button( $text = '&nbsp;', $type = 'button', $color = 'grey', $args ){
		
		$defaults = array(
			'size'		=> 'normal',
			'align'		=> 'left', 
			'style'		=> '',
			'action'	=> '',
			'pid'		=> 0, 
			'class'		=> null, 
			'clear'		=> false,
		);
		
		$a = wp_parse_args( $args, $defaults );

		$color_class = 'bl-'.$color;
		$size_class = 'bl-size-'.$a['size'];
		$position = 'bl-align-'.$a['align'];

		$classes = join(' ', array($color_class, $size_class, $position));

		if($type == 'edit_post'){
			$element = 'a';
			$classes .= ' post-edit-link';
			$action = sprintf('href="%s"', get_edit_post_link( $a['pid']) );
		}elseif( $type = 'link'){
			$element = 'a';
			$action = sprintf('href="%s"', $a['action'] );
		}else{
			$element = 'span';
			$action = '';
		}
		
		$clear = ($a['clear']) ? '<div class="clear"></div>' : '';
		
		$button = sprintf( '<%1$s class="blink" %3$s><span class="blink-pad">%2$s</span></%1$s>', $element, $text, $action);

		$output = sprintf('%s<div class="%s blink-wrap">%s</div>', $clear, $classes, $button);

		return apply_filters('pagelines_button', $output, $a);
		
	}

}