<?php


class PageLinesRenderCSS {
	
	var $lessfiles;
		
	function __construct() {
		
		$this->lessfiles = $this->get_core_lessfiles();
		self::init();		
	}
	
	/**
	 * 
	 *  Load LESS files
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_core_lessfiles(){
		
		$files = array(

			'variables',
			'mixins',
			'alerts',
			'close',
			'badges',
			'labels',
			'tooltip',
			'popovers',
			'buttons',
			'button-groups',
			'accordion',
			'carousel',
			'tabs-pills',
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

	/**
	 * 
	 *  Leagacy mode, loads CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	private function legacy_actions() {
		
		global $pagelines_template;
		
		add_action('wp_head', array( &$this, 'draw_inline_core_css' ), 8);
		add_action('wp_head', array( &$this, 'draw_inline_sections_css' ), 8);
		add_action('wp_head', array( &$this, 'draw_inline_dynamic_css' ), 8);		
		add_action('wp_head', array( &$pagelines_template, 'print_template_section_head' ) );
		add_action( 'pagelines_head_last', array( &$this, 'get_custom_css' ) , 25 );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );
	}

	/**
	 * 
	 *  Dynamic mode, CSS is loaded to a file using wp_rewrite
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	private function actions() {

		global $pagelines_template;
				
		add_filter('query_vars', array( &$this, 'pagelines_add_trigger' ) );
		add_action('template_redirect', array( &$this, 'pagelines_less_trigger' ) );
		add_filter( 'generate_rewrite_rules', array( &$this, 'pagelines_less_rewrite' ) );
		add_action( 'wp_print_styles', array( &$this, 'load_less_css' ), 11 );
		add_action('wp_head', array( &$this, 'draw_inline_sections_css' ), 8);
		add_action('wp_head', array( &$this, 'draw_inline_dynamic_css' ), 8);
		add_action( 'pagelines_head_last', array( &$this, 'get_custom_css' ) , 25 );		
		add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ) );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );		
	}

	/**
	 * 
	 * Get custom CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_custom_css( $inline = true ) {
		
		if ( $inline ) {
			$a = $this->get_compiled_css();
			if ( '' != $a['custom'] )
				return inline_css_markup( 'pagelines-custom', $this->minify( $a['custom'] ) );
		} else {
			return ploption( 'customcss' );
		}
	}

	/**
	 * 
	 *  Draw Core CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_core_css(){

		$a = $this->get_compiled_css();

		if( ! empty( $a['core'] ) )
			inline_css_markup('core-css', $this->minify( $a['core'] ) );

		pl_debug( sprintf( 'CSS was compiled at %s and took %s seconds.', date( DATE_RFC822, $a['time'] ), $a['c_time'] ), "\n<!--", '-->' );		
	}

	/**
	 * 
	 *  Draw dynamic CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_dynamic_css() {

		if( has_filter( 'disable_dynamic_css' ) )
			return;

		$css = $this->get_dynamic_css();
		inline_css_markup('dynamic-css', $css['dynamic'] );
	}

	/**
	 * 
	 *  Draw sections CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_sections_css() {
		
		$template = new PageLinesTemplate;
		$sections = $template->print_template_section_css();
		
		if( ! empty( $sections ) ) {
			$pless = new PagelinesLess();
			inline_css_markup('sections-css', $this->minify( $pless->raw_less( $sections ) ) );
		}
	}

	/**
	 * 
	 *  Get Dynamic CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 *
	 */
	function get_dynamic_css(){
		
		$pagelines_dynamic_css = new PageLinesCSS;

		$pagelines_dynamic_css->typography();

		$typography = $pagelines_dynamic_css->css;

		unset( $pagelines_dynamic_css->css );
		$pagelines_dynamic_css->layout();
		$pagelines_dynamic_css->options();

		$out = array(
			'type'		=>	$typography,
			'dynamic'	=>	apply_filters('pl-dynamic-css', $pagelines_dynamic_css->css)	
		);
		return $out;
	}

	/**
	 * 
	 *  Enqueue the dynamic css file.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
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
			echo $this->minify( $a['core'] );
			pl_debug( sprintf( 'CSS was compiled at %s and took %s seconds.', date( DATE_RFC822, $a['time'] ), $a['c_time'] ) );		
			die();
		}
	}

	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_css() {
		
		if ( is_array(  $a = get_transient( 'pagelines_dynamic_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$dynamic = $this->get_dynamic_css();

			$custom = $this->get_custom_css( false );

			$core_less = $this->get_core_lesscode();

			$pless = new PagelinesLess();
			$core_less =  $pless->raw_less( $core_less );
			$end_time = microtime(true);			
			$a = array(				
				'dynamic'	=> $dynamic['dynamic'],
				'core'		=> $pless->raw_less( $core_less . $dynamic['type'] ),
				'custom'	=> $pless->raw_less( $custom ),
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			set_transient( 'pagelines_dynamic_css', $a, 604800 );
			return $a;			
		}
		
	}
	
	/**
	 * 
	 *  Get Core LESS code
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_core_lesscode() {
		
			global $disabled_settings;

			$add_color = (isset($disabled_settings['color_control'])) ? false : true;
			$color = ($add_color) ? $this->load_core_cssfiles() : '';			
			return $color;	
	}

	/**
	 * 
	 *  Helper for get_core_less_code()
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_core_cssfiles() {
	
		$code = '';
		foreach( $this->lessfiles as $less ) {
			
			$file = sprintf( '%s/%s.less', CORE_LESS, $less );
			$code .= pl_file_get_contents( $file );
		}
		return $code;
	}

	/**
	 * 
	 *  Add rewrite.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function pagelines_less_rewrite( $wp_rewrite ) {
	    $less_rule = array(
	        '(.*)/pagelines-dynamic-[0-9]+.css(.*)' => '/index.php?plless=1'
	    );

	    $wp_rewrite->rules = $less_rule + $wp_rewrite->rules;
	}

	/**
	 * 
	 *  Minify
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function minify( $css ) {
		if( is_pl_debug() )
			return $css;

		return preg_replace('@({)\s+|(\;)\s+|/\*.+?\*\/|\R@is', '$1$2 ', $css);
	}

	/**
	 * 
	 *  Flush rewrites/cached css
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function flush_version() {
		
		flush_rewrite_rules( false );
		plupop( 'pl_save_version', time() );
		delete_transient( 'pagelines_dynamic_css' );
	}
	
} //end of PageLinesRenderCSS



// LEGACY
function sspl_get_core_less() {
	
	$less = '';
		foreach( glob( CORE_LESS . '/*.less' ) as $file )
			$less .= pl_file_get_contents( $file );
	return $less;
}