<?php 
/**
 *
 *  PageLines Integration Functions
 *
 */

class PageLinesIntegration {
	
	public $lesscode = '';
	
	function __construct( $integration ){  
		
		// All of WordPress Initialization is done by this time
		
		
		
		$this->integration = $integration;
		
		global $pl_integration;
		$pl_integration = $this->integration;
		
		add_filter('pagelines_lesscode', array(&$this, 'load_less'));
		
	}
	
	public function add_less( $path){
	
		if(file_exists($path))
			$this->lesscode .= file_get_contents($path);	

	}
	
	function load_less( $lesscode ){
		
		return $lesscode . $this->lesscode;
		
	}
	
	public function parse_header(){
		
		ob_start();
			get_header();
		$raw = ob_get_clean();

		$css 	= $this->regex_parse( array( 'buffer' => $raw, 'type' => 'css' ) );
		$js 	= $this->regex_parse( array( 'buffer' => $raw, 'type' => 'js' ) );
		$divs 	= $this->regex_parse( array( 'buffer' => $raw, 'type' => 'divs' ) );
		
		return array('css' => $css, 'js' => $js, 'divs' => $divs);
		
	}
	
	public function parse_footer(){
		
		ob_start();
			get_footer();
		$raw = ob_get_clean();
		
		return array('raw' => $raw);
		
	}
	
	public function regex_parse( $args ){
		
		$defaults = array(

			'buffer'=>	'',
			'area'	=>	'head',
			'type'	=>	'css'
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['area'] == 'head' && $args['buffer'] ) {

			switch( $args['type'] ) {

				case 'css':
					preg_match_all( '#<link rel=[\'|"]stylesheet[\'|"].*\/>#', $args['buffer'], $styles );
					preg_match_all( '#<style type=[\'|"]text\/css[\'|"].*<\/style>#ms', $args['buffer'], $xtra_styles );
					$styles = array_merge( $styles[0], $xtra_styles[0] );
					if ( is_array( $styles ) ) {
						$css = '';
						foreach( $styles as $style )
							$css .= $style . "\n";
						return $css;
					}
				break;

				case 'js':
					preg_match_all( '#<script type=[\'|"]text\/javascript[\'|"].*<\/script>#', $args['buffer'], $js );
					if( is_array( $js[0] ) ) {
						$js_out = '';
						foreach( $js[0] as $j )
							$js_out .= $j . "\n";
					return $js_out;
					}
				break;

				case 'divs':
					preg_match( '/<div.*>/ms',$args['buffer'], $divs );
					return ( isset( $divs[0] ) ) ? $divs[0] : '';
				break;

				default:
					return false;
				break;
			}

		}

	}
	
	
}

function pl_is_integration(){
	global $pl_integration;
	
	return (isset($pl_integration) && !$pl_integration) ? true : false;
}

function pl_get_integration(){
	global $in_integration;
	
	return (isset($pl_integration) && !$pl_integration) ? $pl_integration : false;
}