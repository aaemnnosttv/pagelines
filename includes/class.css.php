<?php
/**
 * 
 *
 *  Write Dynamic CSS to file
 *
 *
 *  @package PageLines Core
 *  @subpackage Sections
 *  @since 4.0
 *
 */
class PageLinesCSS {
	
	function create( $format = 'inline') {
		
		if( $format == 'texturize' ){
			
			$this->nl = "\n";
			$this->nl2 = "\n\n";
			$this->comments = true;
			
		} else {
			
			$this->nl = "";
			$this->nl2 = "";
			$this->comments = false;
			
		}
		
		$this->typography();
		$this->layout();
		$this->options();
		$this->custom_css();
		
	}
	
	function typography(){
		
		$foundry = new PageLinesFoundry;
		$this->css .= $foundry->render_css();
	}
	
	function layout(){
		global $pagelines_layout;
		$this->css .= $pagelines_layout->get_layout_inline();
	}
	
	function options(){
		$engine = new OptEngine;
		$this->css .= $engine->render_css();
	}
	
	function _css_colors( $optionid, $o, $selectors = null, $css_prop = null ){
		if( pagelines_option($optionid)){
			
			if( isset($o['default']) && pagelines_option($optionid) == $o['default']){
				// do nothing
			}elseif(isset($css_prop)){
			
				if(is_array($css_prop)){
				
					foreach( $css_prop as $css_property => $css_selectors ){

						if($css_property == 'text-shadow')
							$this->css .= $css_selectors . '{ text-shadow:'.pagelines_option($optionid).' 0 1px 0;}'.$this->nl;		
						elseif($css_property == 'text-shadow-top')
							$this->css .= $css_selectors . '{ text-shadow:'.pagelines_option($optionid).' 0 -1px 0;}'.$this->nl;		
						else
							$this->css .= $css_selectors . '{'.$css_property.':'.pagelines_option($optionid).';}'.$this->nl;		
						
					}
				
				}else{
					$this->css .= $selectors.'{'.$css_prop.':'.pagelines_option($optionid).';}'.$this->nl;
				}
			
			} else {
				$this->css .= $selectors.'{color:'.pagelines_option($optionid).';}'.$this->nl;
			}
		}
	}
	
	function custom_css(){
		if( $this->comments )  $this->css .= '/* Custom CSS */'.$this->nl2;
		$this->css .= pagelines_option('customcss');
		$this->css .= $this->nl2;
	}

}


/**
 * 
 *  Load Dynamic CSS inline
 *
 *  @package Platform
 *  @since 1.2.0
 *
 */
function get_dynamic_css(){
	$pagelines_dynamic_css = new PageLinesCSS;
	$pagelines_dynamic_css->create();
	echo '<style type="text/css" id="dynamic-css">'."\n". $pagelines_dynamic_css->css . "\n".'</style>'. "\n";
}
/********** END OF CSS CLASS  **********/