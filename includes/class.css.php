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
		
		$this->css .= $this->render_css();
			
	}
	
	
	function custom_css(){
		$this->css .= pagelines_option('customcss');
	}
	
	/**
	 *  CSS Rendering In <head>
	 */
	function render_css(){
		global $pagelines_ID;
		
		$css = '';
		
		if(has_action('override_pagelines_css_output'))
			return;
		
		foreach ( get_option_array() as $menu){

			foreach($menu as $oid => $o){ 
				
				$oset = array( 'post_id' => $pagelines_ID );
				$o['val'] = ploption($oid, $oset);
				
				if(!empty($o['selectvalues']) && is_array($o['selectvalues'])){
					foreach( $o['selectvalues'] as $sid => $s)
						$o['selectvalues'][$sid]['val'] = ploption( $sid, $oset);
				}
				
				if( !ploption('supersize_bg', $oset) && $o['type'] == 'background_image' && ploption($oid.'_url', $oset)){
					
					$bg_repeat = (ploption($oid.'_repeat', $oset)) ? ploption($oid.'_repeat', $oset) : 'no-repeat';
					$bg_attach = (ploption($oid.'_attach', $oset)) ? ploption($oid.'_attach', $oset): 'scroll';
					$bg_pos_vert = (ploption($oid.'_pos_vert', $oset) || ploption($oid.'_pos_vert', $oset) == 0 ) ? (int) ploption($oid.'_pos_vert', $oset) : '0';
					$bg_pos_hor = (ploption($oid.'_pos_hor', $oset) || ploption($oid.'_pos_hor', $oset) == 0 ) ? (int) ploption($oid.'_pos_hor', $oset) : '50';
					$bg_selector = (ploption($oid.'_selector', $oset)) ? ploption($oid.'_selector', $oset) : $o['selectors'];
					$bg_url = ploption($oid.'_url', $oset);
					
					$css .= sprintf('%s{ background-image:url(%s);}', $bg_selector, $bg_url);
					$css .= sprintf('%s{ background-repeat: %s;}', $bg_selector, $bg_repeat);
					$css .= sprintf('%s{ background-attachment: %s;}', $bg_selector, $bg_attach);
					$css .= sprintf('%s{ background-position: %s%% %s%%;}', $bg_selector, $bg_pos_hor, $bg_pos_vert);
					
					
				}	
				
				elseif( $o['type'] == 'colorpicker')
					$this->render_css_colors($oid, $o['cssgroup'], $o['css_prop']);
				
				elseif( $o['type'] == 'color_multi'){
					
					foreach($o['selectvalues'] as $mid => $m){			
						
						$cgroup = (isset($m['cssgroup'])) ? $m['cssgroup'] : null;
						$cprop = (isset($m['css_prop'])) ? $m['css_prop'] : null;
						$this->render_css_colors($mid, $m, $cgroup, $cprop );
					}
				}
				
			} 
		}
		$css .= $this->parse_css_factory();
		return $css;

	}
	
	function render_css_colors( $oid, $o, $cssgroup = null, $css_prop = null ){
		
		$v = $o['val'];
			
		if( !$v && isset($o['flag']))
			$v = ($o['flag'] == 'blank_default') ? false : $o['default'];
	
		do_color_math($oid, $o, $v, 'css');
		
		if( $v && isset($css_prop) )
			$this->set_factory_key($cssgroup, $this->load_the_props( $css_prop, $v ));
		elseif( $v )
			$this->set_factory_key($cssgroup, $this->get_the_rule( 'color', $v ));
		
	}
	
	function load_the_props( $props, $val ){
		
		$output = '';
		
		if( is_array($props) ){
			
			foreach( $props as $p => $s )
				$output .= ( gettype($p) == 'string' ) ? $this->get_the_rule( $p, $val ) : $this->get_the_rule( $s, $val );
	
		} else
			$output .= $this->get_the_rule( $props, $val);
		
		return $output;
		
	}
	
	function get_the_rule( $prop, $val ){

		if( $prop == 'text-shadow' )	
			$rule = sprintf('%s:%s;', 'text-shadow', $val.' 0 1px 0');	
		elseif( $prop == 'text-shadow-top' )
			$rule = sprintf('%s:%s;', 'text-shadow', $val.' 0 -1px 0');
		else
			$rule = sprintf('%s:%s;', $prop, $val);
	
		return $rule;
	} 
	
	function set_factory_key($cssgroup, $props){
		
		global $css_factory;
		
		if(isset($css_factory[ $cssgroup ]))
			$css_factory[ $cssgroup ] .= $props;
		else 
			$css_factory[ $cssgroup ] = $props;
		
	}

	function parse_css_factory(){
		
		global $css_factory;
		
		$output = '';
		foreach( $css_factory as $cssgroup => $props)
			$output .= sprintf('%s{%s}', cssgroup($cssgroup), $props);
	
		return $output;
	}

}

/**
 * 
 *  Load Dynamic CSS inline
 *
 *  @package PageLines Framework
 *  @since 1.2.0
 *
 */
function get_dynamic_css(){
	$pagelines_dynamic_css = new PageLinesCSS;
	$pagelines_dynamic_css->create();
	
	$css = apply_filters('pl-dynamic-css', $pagelines_dynamic_css->css);
	inline_css_markup('dynamic-css', $css);
}



function inline_css_markup($id, $css, $echo = true){
	$mark = sprintf('<style type="text/css" id="%3$s">%2$s %1$s %2$s</style>%2$s', $css, "\n", $id);
	
	if($echo) 
		echo $mark;
	else
		return $mark;
	
}

