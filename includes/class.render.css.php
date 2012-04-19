<?php


class PageLinesRenderCSS {
	
	var $lessfiles;
		
	function __construct() {
		
		$this->lessfiles = $this->get_core_lessfiles();
		self::init();		
	}
	
	function get_core_lessfiles(){
		
		$files = array(

			'variables',
//			'mixins',
			'buttons',
			'color',		
		);
		return $files;
	}

	private function init() {
				
		if ( ! ploption( 'less_css' ) || '' == get_option('permalink_structure') )
			$this->legacy_actions();
		else
			$this->actions();	
	}

	private function legacy_actions() {
		
		global $pagelines_template;
		
		add_action('wp_head', array( &$this, 'get_inline_css' ), 8);
		add_action('wp_head', array( &$pagelines_template, 'print_template_section_head' ) );
		add_action( 'pagelines_head_last', array( &$this, 'get_custom_css' ) , 25 );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );
	}

	private function actions() {

		global $pagelines_template;
				
		add_filter('query_vars', array( &$this, 'pagelines_add_trigger' ) );
		add_action('template_redirect', array( &$this, 'pagelines_less_trigger' ) );
		add_filter( 'generate_rewrite_rules', array( &$this, 'pagelines_less_rewrite' ) );
		add_action( 'wp_print_styles', array( &$this, 'load_less_css' ), 11 );
		add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ) );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );		
	}

	function get_custom_css( $inline = true ) {
		
		if ( $inline )
			return inline_css_markup( 'pagelines-custom', $this->minify( ploption( 'customcss' ) ) );
		else
			return plstrip( ploption( 'customcss' ) );
	}

	/**
	 * 
	 *  Load Dynamic CSS inline
	 *
	 *  @package PageLines Framework
	 *  @since 1.2.0
	 *
	 */
	function get_inline_css(){

		$a = $this->get_compiled_css();
		
		if( ! empty( $a['core'] ) )
			inline_css_markup('core-css', $this->minify( $a['core'] ) );
		
		if ( ! empty( $a['sections'] ) )
		inline_css_markup('sections-css', $this->minify( $a['sections'] ) );

		if( ! has_filter( 'disable_dynamic_css' ) && ! empty( $a['dynamic'] ) )
			inline_css_markup('dynamic-css', $a['dynamic']);
		pl_debug( sprintf( 'CSS was cached and compiled at %s.', date( DATE_RFC822, $a['time'] ) ) );
	}

	/**
	 * 
	 *  Load Dynamic CSS
	 *
	 *  @package PageLines Framework
	 *  @since 1.2.0
	 *
	 */
	function get_dynamic_css(){

		if( has_filter( 'disable_dynamic_css' ) )
			return;
		
		$pagelines_dynamic_css = new PageLinesCSS;
		$pagelines_dynamic_css->create();

		$css = apply_filters('pl-dynamic-css', $pagelines_dynamic_css->css);
		return $css;
	}

	function load_less_css() {

		$url = sprintf( '%s/pagelines-dynamic-%s.css/',PARENT_URL, ploption( "pl_save_version" ) );
		wp_register_style( 'pagelines-less',  $url, false, false, 'all' );
		wp_enqueue_style( 'pagelines-less' );
	}

	function pagelines_add_trigger( $vars ) {
	    $vars[] = 'plless';
	    return $vars;
	}

	
	function pagelines_less_trigger() {
		if( intval( get_query_var( 'plless' ) ) == 1) {
			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );
			
			$a = $this->get_compiled_css();

			echo $this->minify( $a['core'] . $a['sections'] . $a['dynamic'] . $a['custom'] );
			pl_debug( sprintf( 'CSS was cached at %s.', date( DATE_RFC822, $a['time'] ) ) );
			die();
		}
	}


	function get_compiled_css() {
		
		if ( is_array(  $a = get_transient( 'pagelines_dynamic_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();
			$template = new PageLinesTemplate;

			$sections = $template->print_template_section_css();

			$dynamic = $this->get_dynamic_css();

			$custom = $this->get_custom_css( false );

			$core_less = $this->get_core_lesscode();

			$pless = new PagelinesLess();
			$core_less =  $pless->raw_less( $core_less );
			$a = array(				
				'sections'	=> $pless->raw_less( $sections ),
				'dynamic'	=> $dynamic,
				'core'		=> $pless->raw_less( $core_less ),
				'custom'	=> $pless->raw_less( $custom ),
				'time'		=> time()		
			);
			set_transient( 'pagelines_dynamic_css', $a, 604800 );
			$end_time = microtime(true);
			pl_debug( sprintf( 'LESS css was compiled in %s seconds.', round(($end_time - $start_time),5) ) );
			return $a;			
		}
		
	}

	

	function get_core_lesscode() {
		
			global $disabled_settings;

			$add_color = (isset($disabled_settings['color_control'])) ? false : true;
			$color = ($add_color) ? $this->load_core_cssfiles() : '';			
			return $color;	
	}


	function load_core_cssfiles() {
	
		$code = '';
		foreach( $this->lessfiles as $less ) {
			
			$file = sprintf( '%s/%s.less', CORE_LESS, $less );
			$code .= pl_file_get_contents( $file );
		}
		return $code;
	}


	function pagelines_less_rewrite( $wp_rewrite ) {
	    $less_rule = array(
	        '(.*)/pagelines-dynamic-[0-9]+.css(.*)' => '/index.php?plless=1'
	    );

	    $wp_rewrite->rules = $less_rule + $wp_rewrite->rules;
	}

	function minify( $css ) {
		if( is_pl_debug() )
			return $css;

		return preg_replace('@({)\s+|(\;)\s+|/\*.+?\*\/|\R@is', '$1$2 ', $css);
	}
	
	function flush_version() {
		
		flush_rewrite_rules( false );
		plupop( 'pl_save_version', time() );
		delete_transient( 'pagelines_dynamic_css' );
	}


} //end

/**
*
* @TODO do
*
*/
function inline_css_markup($id, $css, $echo = true){
	$mark = sprintf('%2$s<style type="text/css" id="%3$s">%2$s %1$s %2$s</style>%2$s', $css, "\n", $id);
	
	if($echo) 
		echo $mark;
	else
		return $mark;	
}

// LEGACY
function pl_get_core_less() {
	
	$less = '';
		foreach( glob( CORE_LESS . '/*.less' ) as $file )
			$less .= pl_file_get_contents( $file );
	return $less;
}