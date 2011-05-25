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
		
		$this->intro();
		$this->typography();
		$this->layout();
		$this->dynamic_grid();
		$this->options();
		$this->custom_css();
		
	}

	function intro(){
		if( $this->comments ) $this->css .= "/* PageLines - Copyright 2011 - Version ".CORE_VERSION." */".$this->nl2;
	}
	
	function typography(){
		
		foreach (get_option_array() as $mid){
			
			foreach($mid as $oid => $o){ 
				
				if($o['type'] == 'typography'){
					
					$type_foundry = new PageLinesFoundry;

					$type = pagelines_option($oid);
					
					$font_id = $type['font'];
					
					// Don't render if font isn't set.
					if(isset($font_id) && isset($type_foundry->foundry[$font_id]) ){
						
						if($type_foundry->foundry[$font_id]['google'])
							$google_fonts[] = $font_id;

						$type_selectors = $o['selectors']; 

						if( isset($type['selectors']) && !empty($type['selectors']) ) $type_selectors .=  ',' . trim(trim($type['selectors']), ",");

						$type_css = $type_foundry->get_type_css($type);
					
					
						$type_css_keys[] = $type_selectors . "{".$type_css."}".$this->nl;
					}
					
				}
				
			}
		}
		
		if(isset($google_fonts) && is_array($google_fonts )){
			
			if( $this->comments ) $this->css .= '/* Import Google Fonts --------------- */'.$this->nl2;
			
			$this->css .= $type_foundry->google_import($google_fonts) . $this->nl;
			
		}
		
		if( $this->comments ) $this->css .= '/* Set Type --------------- */'.$this->nl2;
		
		// Render the font CSS
		if(isset($type_css_keys) && is_array($type_css_keys)){
			foreach($type_css_keys as $typeface){
				$this->css .= $typeface .$this->nl;
			}
		}

	}

	function layout(){
		
		global $pagelines_layout; 
		global $post; 

		if( $this->comments ) $this->css .= '/* Dynamic Layout --------------- */'.$this->nl2;
		
		/* Fixed Width Page */
		$fixed_page = $pagelines_layout->content->width + 20;
		$this->css .= ".fixed_width #page, .fixed_width #footer, .canvas #page-canvas{width:".$fixed_page."px}".$this->nl;

		
		/* Content Width */
		$content_with_border = $pagelines_layout->content->width + 2;
		$this->css .= "#page-main .content{width:".$content_with_border."px}".$this->nl;
		$this->css .= "#site{min-width:".$content_with_border."px}".$this->nl; // Fix small horizontal scroll issue
		$this->css .= "#site .content, .wcontent, #primary-nav ul.main-nav.nosearch{width:".$pagelines_layout->content->width."px}".$this->nl;
		
		/* Navigation Width */
		$nav_width = $pagelines_layout->content->width - 220;
		$this->css .= "#primary-nav ul.main-nav{width:".$nav_width."px}".$this->nl;
		$this->css .= $this->nl;
		
		// For inline CSS in Multisite
		// TODO clean up layout variable handling
		$page_layout = $pagelines_layout->layout_mode;
		
		/* Layout Modes */
		foreach(get_the_layouts() as $layout_mode){
			$pagelines_layout->build_layout($layout_mode);
		
			//Setup for CSS
			$mode = '.'.$layout_mode.' ';
			$this->css .= $mode."#pagelines_content #column-main, ".$mode.".wmain, ".$mode."#buddypress-page #container{width:". $pagelines_layout->main_content->width."px}".$this->nl;
			$this->css .= $mode."#pagelines_content #sidebar1, ".$mode."#buddypress-page #sidebar1{width:". $pagelines_layout->sidebar1->width."px}".$this->nl;
			$this->css .= $mode."#pagelines_content #sidebar2, ".$mode."#buddypress-page #sidebar2{width:". $pagelines_layout->sidebar2->width."px}".$this->nl;
			$this->css .= $mode."#pagelines_content #column-wrap, ".$mode."#buddypress-page #container{width:". $pagelines_layout->column_wrap->width."px}".$this->nl;
			$this->css .= $mode."#pagelines_content #sidebar-wrap, ".$mode."#buddypress-page #sidebar-wrap{width:". $pagelines_layout->sidebar_wrap->width."px}".$this->nl2;
		}
		
		// Put back to original mode for page layouts in multisite
		$pagelines_layout->build_layout($page_layout);
		
	}
	
	function dynamic_grid(){
		global $pagelines_layout; 
		
		/*
			Generate Dynamic Column Widths & Padding
		*/
		if( $this->comments ) $this->css .= '/* Dynamic Grid --------------- */'.$this->nl2;
		for($i = 2; $i <= 5; $i++){
			$this->css .= '.dcol_container_'.$i.'{width: '.$pagelines_layout->dcol[$i]->container_width.'px; float: right;}'.$this->nl;
			$this->css .= '.dcol_'.$i.'{width: '.$pagelines_layout->dcol[$i]->width.'px; margin-left: '.$pagelines_layout->dcol[$i]->gutter_width.'px;}'.$this->nl2;
		}
		
	}
	
	function options(){
		/*
			Handle Color Select Options and output the required CSS for them...
		*/
		if( $this->comments ) $this->css .= '/* Options --------------- */'.$this->nl2;
		foreach (get_option_array() as $menuitem){

			foreach($menuitem as $optionid => $option_info){ 
				
				if($option_info['type'] == 'css_option' && pagelines_option($optionid)){
					if(isset($option_info['css_prop']) && isset($option_info['selectors'])){
						
						$css_units = (isset($option_info['css_units'])) ? $option_info['css_units'] : '';
						
						$this->css .= $option_info['selectors'].'{'.$option_info['css_prop'].':'.pagelines_option($optionid).$css_units.';}'.$this->nl;
					}

				}
				
				if( $option_info['type'] == 'background_image' && pagelines_option($optionid.'_url')){
					
					$bg_repeat = (pagelines_option($optionid.'_repeat')) ? pagelines_option($optionid.'_repeat'): 'no-repeat';
					$bg_pos_vert = (pagelines_option($optionid.'_pos_vert') || pagelines_option($optionid.'_pos_vert') == 0 ) ? (int) pagelines_option($optionid.'_pos_vert') : '0';
					$bg_pos_hor = (pagelines_option($optionid.'_pos_hor') || pagelines_option($optionid.'_pos_hor') == 0 ) ? (int) pagelines_option($optionid.'_pos_hor') : '50';
					$bg_selector = (pagelines_option($optionid.'_selector')) ? pagelines_option($optionid.'_selector') : $option_info['selectors'];
					$bg_url = pagelines_option($optionid.'_url');
					
					$this->css .= $bg_selector ."{background-image:url('".$bg_url."');}".$this->nl;
					$this->css .= $bg_selector ."{background-repeat:".$bg_repeat.";}".$this->nl;
					$this->css .= $bg_selector ."{background-position:".$bg_pos_hor."% ".$bg_pos_vert."%;}".$this->nl;
					
					
				}
	
				
				if($option_info['type'] == 'colorpicker'){
					
					$this->_css_colors($optionid, $option_info['selectors'], $option_info['css_prop']);

				}
				
				elseif($option_info['type'] == 'color_multi'){
					
					foreach($option_info['selectvalues'] as $moption_id => $m_option_info){
						
						$the_css_selectors = (isset($m_option_info['selectors'])) ? $m_option_info['selectors'] : null ;
						$the_css_property = (isset($m_option_info['css_prop'])) ? $m_option_info['css_prop'] : null ;
						
						$this->_css_colors($moption_id, $the_css_selectors, $the_css_property);
					}
					
				}
			} 
		}
		$this->css .= $this->nl2;
	}
	
	function _css_colors( $optionid, $selectors = null, $css_prop = null ){
		if( pagelines_option($optionid) ){
			
			if(isset($css_prop)){
			
				if(is_array($css_prop)){
				
					foreach( $css_prop as $css_property => $css_selectors ){

						if($css_property == 'text-shadow'){
							$this->css .= $css_selectors . '{ text-shadow:'.pagelines_option($optionid).' 0 1px 0;}'.$this->nl;		
						} elseif($css_property == 'text-shadow-top'){
							$this->css .= $css_selectors . '{ text-shadow:'.pagelines_option($optionid).' 0 -1px 0;}'.$this->nl;		
						}else {
							$this->css .= $css_selectors . '{'.$css_property.':'.pagelines_option($optionid).';}'.$this->nl;		
						}
						
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
 *
 *  Write Dynamic CSS to file
 *
 *  @package PageLines Core
 *  @subpackage Sections
 *  @since 1.2.0
 *
 */
function pagelines_build_dynamic_css( $trigger = 'N/A' ){

	// Create directories and folders for storing dynamic files
	if(!file_exists(PAGELINES_DCSS) ) {
		if ( false === pagelines_make_uploads() ); {
		pagelines_update_option( 'inline_dynamic_css', true );
		return;
		}	
	}
	// Write to dynamic files
	if ( is_writable(PAGELINES_DCSS) && !is_multisite() ){
		$pagelines_dynamic_css = new PageLinesCSS;
		$pagelines_dynamic_css->create('texturize');
		pagelines_make_uploads($pagelines_dynamic_css->css ."\n\n/* Trigger: ". $trigger . '*/');
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