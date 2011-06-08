<?php
/**
 * 
 *
 *  PageLines Color Calculations and Handling
 *
 *
 *  @package PageLines Core
 *  @subpackage Post Types
 *  @since 1.3.0
 *
 */
class PageLinesColor {

	var $tabs = array();	// Controller for drawing meta options
	
	/**
	 * PHP5 constructor
	 *
	 */
	function __construct( $hex ) {
	
		$this->base_hex = $hex;
	
		$this->base_rgb = $this->hex_to_rgb( $this->base_hex  );
		
		$this->base_hsl = $this->rgb_to_hsl( $this->base_rgb  );
	
	}
	
	function contrast( $adjustment, $surrounding = ''){
		
		return $this->base_hsl['lightness'];
		
	}
	
	function adjust( $adjustment, $mode = 'lightness' ){
		
		
		$h = $this->base_hsl['hugh'];
		$s = $this->base_hsl['saturation'];
		$l = $this->base_hsl['lightness'];
		
		if(is_array($adjustment)){
			
			$l = $l + $adjustment['lightness']; 
			
			$h = $h + $adjustment['hugh']; 
			
			$s = $s + $adjustment['saturation']; 
			
			
		} else {
			if($mode == 'lightness'){

				$l = $l + $adjustment; 

			} elseif($mode == 'hugh') {

				$h = $h + $adjustment; 
				

			} elseif($mode == 'saturation') {

				$s = $s + $adjustment; 

			}
		}
	
	
		// Adjust for hue 180* scale
		if ($h > 1) $h -= 1;
		if ($s > 1) $s = 1;
		if ($l > 1) $l = 1;
		
		if ($h < 0) $h += 1;
		if ($s < 0) $s = 0;
		if ($l < 0) $l = 0;
		
		$new_hsl = array( 'hugh' => $h, 'saturation' => $s, 'lightness' => $l );
		
		
		return $this->hsl_to_hex( $new_hsl );
	}
	
	function adjust_hugh(){
		
		$h = $this->base_hsl['hugh'];
		$s = $this->base_hsl['saturation'];
		$l = $this->base_hsl['lightness'];
		
		$l = $l + $adjustment; 
		
		$new_hsl = array( 'hugh' => $h, 'saturation' => $s, 'lightness' => $l );
		
		
		return $this->hsl_to_hex( $new_hsl );
		
		$h2 = $h + 0.5;

		if ($h2 > 1)
		{
		$h2 -= 1;
		};
	}
	
	function hex_to_rgb( $hexcode ){
		
		$redhex  = substr( $hexcode, 0, 2 );
		$greenhex = substr( $hexcode, 2, 2 );
		$bluehex = substr( $hexcode, 4, 2 );

		// $var_r, $var_g and $var_b are the three decimal fractions to be input to our RGB-to-HSL conversion routine

		$r = hexdec($redhex);
		$g = hexdec($greenhex);
		$b = hexdec($bluehex);
		
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		
	}
	
	function rgb_to_hsl( $rgb ){
		
		$red = $this->base_rgb['red'];
		$green = $this->base_rgb['green'];
		$blue = $this->base_rgb['blue'];
		
		$var_red = $red / 255;
		$var_green = $green / 255;
		$var_blue = $blue / 255;
		
		$var_min = min( $var_red, $var_green, $var_blue );
		$var_max = max( $var_red, $var_green, $var_blue );
		
		$del_max = $var_max - $var_min;

		$l = ($var_max + $var_min) / 2;

		if ($del_max == 0){
			$h = 0;
			$s = 0;
		} else {
			
			if ($l < 0.5) {
				$s = $del_max / ($var_max + $var_min);
			} else {
				$s = $del_max / (2 - $var_max - $var_min);
			};

			$del_r = ((($var_max - $var_red) / 6) + ($del_max / 2)) / $del_max;
			$del_g = ((($var_max - $var_green) / 6) + ($del_max / 2)) / $del_max;
			$del_b = ((($var_max - $var_blue) / 6) + ($del_max / 2)) / $del_max;

			if ($var_red == $var_max) {
			        $h = $del_b - $del_g;
			} elseif ($var_green == $var_max) {
			        $h = (1 / 3) + $del_r - $del_b;
			} elseif ($var_blue == $var_max) {
			        $h = (2 / 3) + $del_g - $del_r;
			};

			if ($h < 0) {
			        $h += 1;
			};

			if ($h > 1) {
			        $h -= 1;
			};
		};
		
		return array( 'hugh' => $h, 'saturation' => $s, 'lightness' => $l );
	}
	
	function hsl_to_hex( $hsl ){
		
		$rgb = $this->hsl_to_rgb($hsl);

		$hex = $this->rgb_to_hex($rgb);
			
		return $hex;
	}
	
	function hsl_to_rgb( $hsl ){
		
		// Input is HSL value of complementary colour, held in $h2, $s, $l as fractions of 1
		// Output is RGB in normal 255 255 255 format, held in $r, $g, $b
		// Hue is converted using function hue_2_rgb, shown at the end of this code

		$h = $hsl['hugh'];
		$s = $hsl['saturation'];
		$l = $hsl['lightness'];

		if ($s == 0) {
			$r = $l * 255;
			$g = $l * 255;
			$b = $l * 255;
		} else {
			if ($l < 0.5) {
				$var_2 = $l * (1 + $s);
			} else {
				$var_2 = ($l + $s) - ($s * $l);
			};

			$var_1 = 2 * $l - $var_2;
			$r = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h + (1 / 3) );
			$g = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h );
			$b = 255 * $this->_hue_to_rgb( $var_1, $var_2, $h - (1 / 3) );
		};
		
		return array( 'red' => $r, 'green' => $g, 'blue' => $b );
		
	}

	function _hue_to_rgb( $v1, $v2, $vh ) {
		
		if ($vh < 0) {
			$vh += 1;
		};

		if ($vh > 1) {
			$vh -= 1;
		};

		if ((6 * $vh) < 1) {
			return ($v1 + ($v2 - $v1) * 6 * $vh);
		};

		if ((2 * $vh) < 1) {
			return ($v2);
		};

		if ((3 * $vh) < 2) {
			return ($v1 + ($v2 - $v1) * ((2 / 3 - $vh) * 6));
		};

		return ($v1);
	}
		
	function rgb_to_hex($rgb){
		
		$r = $rgb['red'];
		$g = $rgb['green'];
		$b = $rgb['blue'];
		
		$rhex = sprintf( "%02X", round($r) );
		$ghex = sprintf( "%02X", round($g) );
		$bhex = sprintf( "%02X", round($b) );

		$hex = $rhex.$ghex.$bhex;
		
		return $hex;
		
	}

}
//-------- END OF CLASS --------//



