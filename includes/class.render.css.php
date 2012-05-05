<?php


class PageLinesRenderCSS {
	
	var $lessfiles;
		
	function __construct() {
		
		$this->lessfiles = $this->get_core_lessfiles();
		self::actions();		
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
			'grid',
			'alerts',
			'close',
			'badges',
			'labels',
			'tooltip',
			'popovers',
			'buttons',
			'type',
			'dropdowns',
			'button-groups',
			'accordion',
			'carousel',
			'responsive',
			'navs',
			'modals',
			'component-animations',
			'color', // HAS TO BE LAST	
		);
		return $files;
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
		add_action( 'wp_print_styles', array( &$this, 'load_less_css' ), 11 );
		add_action('wp_head', array( &$this, 'draw_inline_sections_css' ), 8);
		add_action('wp_head', array( &$this, 'draw_inline_dynamic_css' ), 8);
		add_action( 'pagelines_head_last', array( &$this, 'draw_inline_custom_css' ) , 25 );
		add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ) );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );	
		add_filter( 'pagelines_insert_core_less', array( &$this, 'pagelines_insert_core_less_callback' ) );
	}

	/**
	 * 
	 * Get custom CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_custom_css() {

			$a = $this->get_compiled_custom();
			if ( '' != $a['custom'] )
				return inline_css_markup( 'pagelines-custom', rtrim( $this->minify( $a['custom'] ) ) );
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
		
		wp_register_style( 'pagelines-less',  $this->get_dynamic_url(), false, null, 'all' );
		wp_enqueue_style( 'pagelines-less' );
	}

	function get_dynamic_url() {
		
		if ( '' != get_option('permalink_structure') )
			return sprintf( '%s/pagelines-compiled-css-%s/',PARENT_URL, ploption( "pl_save_version" ) );
		else
			return sprintf( '%s?pageless=%s',site_url(), ploption( "pl_save_version" ) );
		
	}

	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_core() {
		
		if ( is_array(  $a = get_transient( 'pagelines_core_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$dynamic = $this->get_dynamic_css();

			$core_less = $this->get_core_lesscode();
			$pless = new PagelinesLess();			
			$core_less = $pless->raw_less( $core_less . $dynamic['type'] );

			$end_time = microtime(true);			
			$a = array(				
				'dynamic'	=> $dynamic['dynamic'],
				'core'		=> $core_less,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			set_transient( 'pagelines_core_css', $a, 604800 );
			return $a;			
		}		
	}
	
	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_custom() {
		
		if ( is_array(  $a = get_transient( 'pagelines_custom_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$custom = ploption( 'customcss' );

			$pless = new PagelinesLess();
			$custom =  $pless->raw_less( $custom, 'custom' );
			$end_time = microtime(true);			
			$a = array(				
				'custom'	=> $custom,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			set_transient( 'pagelines_custom_css', $a, 604800 );
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
			
			if ( ! $add_color ) {
				array_pop( $this->lessfiles );
			}
			return $this->load_core_cssfiles( $this->lessfiles );	
	}

	/**
	 * 
	 *  Helper for get_core_less_code()
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_core_cssfiles( $files ) {
	
		$code = '';
		foreach( $files as $less ) {
			
			$file = sprintf( '%s/%s.less', CORE_LESS, $less );
			$code .= pl_file_get_contents( $file );
		}
		return apply_filters( 'pagelines_insert_core_less', $code );
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
	        '(.*)pagelines-compiled-css' => '/?pageless=1'
	    );

	    $wp_rewrite->rules = $less_rule + $wp_rewrite->rules;
	}

	function pagelines_add_trigger( $vars ) {
	    $vars[] = 'pageless';
	    return $vars;
	}
	
	function pagelines_less_trigger() {
		if( intval( get_query_var( 'pageless' ) ) ) {
			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );
			
			$a = $this->get_compiled_core();
			echo $this->minify( $a['core'] );
			pl_debug( sprintf( 'CSS was compiled at %s and took %s seconds.', date( DATE_RFC822, $a['time'] ), $a['c_time'] ) );		
			die();
		}
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
		
		flush_rewrite_rules( true );
		plupop( 'pl_save_version', time() );
		delete_transient( 'pagelines_dynamic_css' );
	}
	
	function pagelines_insert_core_less_callback( $code ) {

		global $pagelines_raw_lesscode_external;	
		$out = '';
		if ( is_array( $pagelines_raw_lesscode_external ) && ! empty( $pagelines_raw_lesscode_external ) ) {

			foreach( $pagelines_raw_lesscode_external as $file ) {
				
				if( file_exists( $file ) )
					$out .= pl_file_get_contents( $file );
			}
			return $code . $out;
		}
		return $code;
	}
} //end of PageLinesRenderCSS

function pagelines_insert_core_less( $file ) {
	
	global $pagelines_raw_lesscode_external;
	
	if( !is_array( $pagelines_raw_lesscode_external ) )
		$pagelines_raw_lesscode_external = array();
	
	$pagelines_raw_lesscode_external[] = $file;
}