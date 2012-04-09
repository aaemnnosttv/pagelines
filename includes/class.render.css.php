<?php


class PageLinesRenderCSS {
	
	function __construct() {
		
		self::init();
		
	}
	
	function init() {
		
		global $pagelines_template;
		
		if ( ! ploption( 'less_css' ) ) {
			add_action('wp_head', 'do_dynamic_css', 8);
			add_action('wp_head', array(&$pagelines_template, 'print_template_section_headers_legacy'));
		} else {
			
			add_filter('query_vars', array( &$this, 'pagelines_add_trigger' ) );
			add_action('template_redirect', array( &$this, 'pagelines_less_trigger' ) );
			add_filter( 'generate_rewrite_rules', array( &$this, 'pagelines_less_rewrite' ) );
			add_action( 'wp_print_styles', array( &$this, 'load_less_css' ), 11 );
			add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ) );
			add_action( 'extend_flush', array( &$this, 'flush_version' ) );
		}	
	}

	function load_less_css() {

		$url = ( '' != get_option('permalink_structure') ) ? sprintf( '%s/pageless-%s.css/',PARENT_URL, ploption( "pl_save_version" ) ) : sprintf( '%s/?plless=1', site_url() );
		wp_register_style( 'pagelines-less',  $url, false, false, 'all' );
		wp_enqueue_style( 'pagelines-less' );
	}

	function pagelines_add_trigger( $vars ) {
	    $vars[] = 'plless';
	    return $vars;
	}

	
	function pagelines_less_trigger() {
		if( intval( get_query_var( 'plless' ) ) == 1) {
			build_pagelines_layout();
			$template = new PageLinesTemplate;
			header( 'Content-type: text/css' );
			header( 'Expires: ' );
			header( 'Cache-Control: max-age=604100, public' );
			$less = $template->print_template_section_css();
			$dynamic = get_dynamic_css();
			echo $this->minify( $dynamic . $less );
		die();
		}
	}

	function pagelines_less_rewrite( $wp_rewrite ) {
	    $feed_rules = array(
	        '(.*)/pageless-[0-9]+.css(.*)' => '/index.php?plless=1'
	    );

	    $wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
	}

	function minify( $css ) {
		if( defined( 'PL_DEV' ) && PL_DEV )
			return $css;

		return preg_replace('@({)\s+|(\;)\s+|/\*.+?\*\/|\R@is', '$1$2 ', $css);
	}
	
	function flush_version() {
		
		flush_rewrite_rules( false );
		plupop( 'pl_save_version', time() );
	}


} //end


// TODO this needs to be a list of files to include...
function pl_get_core_less() {
	
	$less = '';
		foreach( glob( CORE_LESS . '/*.less' ) as $file )
			$less .= pl_file_get_contents( $file );
	return $less;
}