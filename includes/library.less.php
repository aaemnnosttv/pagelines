<?php
/**
 *
 *  PageLines Less Language Parser
 *
 *  @package Core
 *  @since 2.0.b22
 *
 */
class PageLinesLess {
	
	private $lparser = null;
	private $constants = '';
	
	function __construct() {
		
		// The LESS Class
		$this->lparser = new lessc();
		
		
		$this->base_color = pl_bg_color();
		
		// PageLines Variables
		$constants = array(
			'pl-base'		=> '#'.$this->base_color, 
			'pl-text'		=> '#'.pl_text_color(), 
			'pl-link'		=> '#'.pl_link_color(),
			'invert-dark'	=> $this->invert(),
			'invert-light'	=> $this->invert('light')
		);
		
		// Make Filterable
		$this->constants = apply_filters('pless_vars', $constants);
		
	}
	
	/*
	 * Parse PLESS Input & return CSS 
	 */
	public function parse( $pless ) {
		
		$pless = $this->add_constants( $pless );
		
		// echo $pless;
		
		return $this->lparser->parse( $pless );
		
	}
	
	private function add_constants( $pless ) {
		
		$prepend = '';
		
		foreach($this->constants as $key => $value)
			$prepend .= sprintf('@%s:%s;%s', $key, $value, "\n");
		
		return $prepend . $pless;
		
	}
	
	private function invert( $mode = 'dark', $delta = 6 ){
		
		if($mode == 'light'){
			
			if($this->color_detect() == -2){
				return 2*$delta;
			}elseif($this->color_detect() == -1){
				return 1.5*$delta;
			}elseif($this->color_detect() == 1){
				return -1.7*$delta;
			}else {
				return $delta;
			}
			
		}else{
			if($this->color_detect() == -2){
				return -(2*$delta);
			}elseif($this->color_detect() == -1){
				return -$delta;
			} else {
				return $delta;
			}
		}
		
		
	}
	
	
	function color_detect(){
		
		$hex = str_replace('#', '', $this->base_color); 

		$r = hexdec(substr($hex,0,2));
		$g = hexdec(substr($hex,2,2));
		$b = hexdec(substr($hex,4,2));
		
		if($r + $g + $b > 750){
			
			// Light
		    return 1;
		
		}elseif($r + $g + $b < 120){

			// Really Dark
			return -2;

		}
		elseif($r + $g + $b < 300){
		
			// Dark
			return -1;
		
		}else{
			
			// Meh
		    return false;
		
		}
		
		
		
	}
	
	
}













