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
		$css = '';
		
		foreach ( get_option_array() as $menu){

			foreach($menu as $oid => $o){ 
				
				$o['val'] = pagelines_option($oid);
				
				if(!empty($o['selectvalues']) && is_array($o['selectvalues'])){
					foreach( $o['selectvalues'] as $sid => $s)
						$o['selectvalues'][$sid]['val'] = pagelines_option( $sid );
				}
				
				if($o['type'] == 'css_option' && $o['val']){
					
					if(pagelines_option($oid) == $o['default']){
						// do nothing
					} elseif(isset($o['css_prop']) && isset($o['selectors'])){
						
						$css_units = (isset($o['css_units'])) ? $o['css_units'] : '';
						
						$css .= $o['selectors'].'{'.$o['css_prop'].':'.$o['val'].$css_units.';}';
						
					}

				}
				
				if( $o['type'] == 'background_image' && pagelines_option($oid.'_url')){
					
					$bg_repeat = (pagelines_option($oid.'_repeat')) ? pagelines_option($oid.'_repeat'): 'no-repeat';
					$bg_pos_vert = (pagelines_option($oid.'_pos_vert') || pagelines_option($oid.'_pos_vert') == 0 ) ? (int) pagelines_option($oid.'_pos_vert') : '0';
					$bg_pos_hor = (pagelines_option($oid.'_pos_hor') || pagelines_option($oid.'_pos_hor') == 0 ) ? (int) pagelines_option($oid.'_pos_hor') : '50';
					$bg_selector = (pagelines_option($oid.'_selector')) ? pagelines_option($oid.'_selector') : $o['selectors'];
					$bg_url = pagelines_option($oid.'_url');
					
					$css .= sprintf('%s{ background-image:url(%s);}', $bg_selector, $bg_url);
					$css .= sprintf('%s{ background-repeat: %s;}', $bg_selector, $bg_repeat);
					$css .= sprintf('%s{ background-position: %s%% %s%%;}', $bg_selector, $bg_pos_hor, $bg_pos_vert);
					
					
				}	
				
				if($o['type'] == 'colorpicker')
					$this->render_css_colors($oid, $o['cssgroup'], $o['css_prop']);
				
				elseif($o['type'] == 'color_multi'){
					
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
			
		if( !$v )
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