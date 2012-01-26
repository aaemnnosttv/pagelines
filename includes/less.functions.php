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
		
		global $less_vars;
		
		// The LESS Class
		$this->lparser = new lessc();
		
		$this->base_color = pl_hashify( pl_base_color() );
		
		/* Type */
		$fontsize = 15;
		$content_width = 600;
		
		// PageLines Variables
		$constants = array(
			'pl-base'			=> $this->base_color, 
			'pl-text'			=> pl_hashify( pl_text_color() ), 
			'pl-link'			=> pl_hashify( pl_link_color() ),
			'pl-header'   		=> pl_hashify( pl_header_color() ),
			'pl-footer'  	 	=> pl_hashify( pl_footer_color() ),
			'invert-dark'		=> $this->invert(),
			'invert-light'		=> $this->invert('light'),
			'font-size'			=> $fontsize.'px', 
			'line-height'		=> page_line_height($fontsize, $content_width).'px'
		);
		
		if(is_array($less_vars))
			$constants = array_merge($less_vars, $constants);
		
		// Make Filterable
		$this->constants = apply_filters('pless_vars', $constants);
		
	}
	
	public function draw_less( $lesscode ){
			
			printf(
				'%1$s<style type="text/css" id="pagelines-less-css" >%2$s</style>%1$s', 
				"\n",
				plstrip( $this->parse($lesscode) )
			);
	
	}
	
	/*
	 * Parse PLESS Input & return CSS 
	 */
	public function parse( $pless ) {
		
		$pless = $this->add_constants( $pless );
		$pless = $this->add_core_less( $pless );
		
		try{
			$css = $this->lparser->parse( $pless );
		} catch ( Exception $e){
			plprint($e->getMessage(), 'Problem Parsing Less');
		}
		 
		return $css;
		
	}
	

	private function add_core_less($pless){
	
		global $disabled_settings;
		
		$add_color = (isset($disabled_settings['color_control'])) ? false : true;
	
		$color = ($add_color) ? pl_file_get_contents(PARENT_DIR.'/css/color.less') : '';
			
		return $pless . $color;
		
	}
	
	private function add_constants( $pless ) {
		
		$prepend = '';
		
		foreach($this->constants as $key => $value)
			$prepend .= sprintf('@%s:%s;%s', $key, $value, "\n");
		
		return $prepend . $pless;
		
	}
	
	private function invert( $mode = 'dark', $delta = 5 ){
		
		if($mode == 'light'){
			
			if($this->color_detect() == -2)
				return 2*$delta;
			elseif($this->color_detect() == -1)
				return 1.5*$delta;
			elseif($this->color_detect() == 1)
				return -1.7*$delta;
			else
				return $delta;
			
		}else{
			if($this->color_detect() == -2)
				return -(2*$delta);
			elseif($this->color_detect() == -1)
				return -$delta;
			else
				return $delta;

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

/* 
 * Add Less Variables
 * 
 * Must be added before header.
 **************************/
function pagelines_less_var( $name, $value ){
	
	global $less_vars;
	
	$less_vars[$name] = $value;
	
}


/* 
 *  Color Fetch
 **************************/
function pl_base_color( $mode = '', $difference = '10%'){
	
	$base_color = PageLinesThemeSupport::BaseColor();

	if( !$base_color ){
	
		if(ploption('contentbg'))
			$base = pl_hash_strip( ploption('contentbg') );
		elseif(ploption('pagebg'))
			$base = pl_hash_strip( ploption('pagebg') );
		elseif(ploption('bodybg'))
			$base = pl_hash_strip( ploption('bodybg') );
		else
			$base = 'FFFFFF';
	
	} else
		$base = $base_color;
		
		
	if($mode != ''){
		
		$adjust_base = new PageLinesColor($base);
		
		$adjusted = $adjust_base->c($mode, $difference);
		
		return $adjusted;
		
	} else
		return $base;
	
}


function pl_bg_color(){
	
	if(get_set_color( 'the_bg' ))
		return get_set_color( 'the_bg' );
	else 
		return 'FFFFFF';
		
}

function pl_text_color(){
		
	$color = ( ploption( 'text_primary' ) ) ? pl_hash_strip( ploption( 'text_primary' ) ) : '000000';

	return $color;
}

function pl_link_color(){
	
	$color = ( ploption( 'linkcolor' ) ) ? pl_hash_strip( ploption( 'linkcolor' ) ) : '225E9B';
	
	return $color;
	
}

function pl_header_color(){
	
	$color = ( ploption( 'headercolor' ) ) ? pl_hash_strip( ploption( 'headercolor' ) ) : '000000';
	
	return $color;
	
}

function pl_footer_color(){
	
	$color = ( ploption( 'footer_text' ) ) ? pl_hash_strip( ploption( 'footer_text' ) ) : '999999';
	
	return $color;
	
}

/* 
 *  Helpers
 **************************/
function pl_hash_strip( $color ){
	
	return str_replace('#', '', $color);
	
}

function pl_hashify( $color ){
	
	$clean_hex = str_replace('#', '', $color);
	
	return sprintf('#%s', $clean_hex);
}