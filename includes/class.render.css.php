<?php


class PageLinesRenderCSS {
	
	var $lessfiles;
	var $types;
	var $ctimeout;
	var $btimeout;
	
	function __construct() {
		
		$this->ctimout = 86400;
		$this->btimeout = 604800;
		$this->types = array( 'sections', 'core', 'custom' );
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
			'pagelines-special',
			'code',
			'pl-objects',
			'pl-tables',
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
		add_action( 'pagelines_head_last', array( &$this, 'draw_inline_custom_css' ) , 25 );
		add_action( 'wp_head', array(&$pagelines_template, 'print_template_section_head' ), 12 );
		add_action( 'extend_flush', array( &$this, 'flush_version' ) );	
		add_filter( 'pagelines_insert_core_less', array( &$this, 'pagelines_insert_core_less_callback' ) );
		add_action('admin_notices', array(&$this,'less_error_report') );
	}

	function less_error_report() {
		
		$default = '<div class="updated fade update-nag"><div style="text-align:left"><h4>PageLines %s LESS/CSS error.</h4>%s</div></div>';

		foreach ( $this->types as $t ) {		
			if ( ploption( "pl_less_error_{$t}" ) ) 
				printf( $default, ucfirst( $t ), ploption( "pl_less_error_{$t}" ) );
		}					
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

			$core_less = $pless->raw_less( $core_less  );

			$end_time = microtime(true);			
			$a = array(				
				'dynamic'	=> $dynamic['dynamic'],
				'type'		=> $dynamic['type'],
				'core'		=> $core_less,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			if ( strpos( $core_less, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_core_css', $a, $this->ctimeout );
				set_transient( 'pagelines_core_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_core_css_backup' );
			}			
		}		
	}

	/**
	 * 
	 *  Get compiled/cached CSS
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_compiled_sections() {
		
		if ( is_array(  $a = get_transient( 'pagelines_sections_css' ) ) ) {
			return $a;
		} else {
			
			$start_time = microtime(true);
			build_pagelines_layout();

			$sections = $this->get_all_active_sections();

			$pless = new PagelinesLess();
			$sections =  $pless->raw_less( $sections, 'sections' );
			$end_time = microtime(true);			
			$a = array(				
				'sections'	=> $sections,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()		
			);
			if ( strpos( $sections, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_sections_css', $a, $this->ctimeout );
				set_transient( 'pagelines_sections_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_sections_css_backup' );
			}		
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
			if ( strpos( $custom, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_custom_css', $a, $this->ctimeout );
				set_transient( 'pagelines_custom_css_backup', $a, $this->btimeout );
				return $a;
			} else {
				return get_transient( 'pagelines_custom_css_backup' );
			}			
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
			return $this->load_core_cssfiles( apply_filters( 'pagelines_core_less_files', $this->lessfiles ) );	
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
			$b = $this->get_compiled_sections();
			$gfonts = preg_match( '#(@import[^;]*;)#', $a['type'], $g ); 
			
			if ( $gfonts ) {
				echo $g[1];
				$a['type'] = str_replace( $g[1], '', $a['type'] );
			}
			echo $this->minify( $a['core'] );
			echo $this->minify( $b['sections'] );
			echo $this->minify( $a['type'] );
			echo $this->minify( $a['dynamic'] );
			$mem = round( bcdiv( memory_get_peak_usage(), 1048576, 3 ), 2 );
			pl_debug( sprintf( 'CSS was compiled at %s and took %s seconds using %sMB of unicorn dust.', date( DATE_RFC822, $a['time'] ), $a['c_time'],  $mem ) );		
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
	function flush_version( $rules = true ) {
		
		$types = array( 'sections', 'core', 'custom' );
		if( $rules )
			flush_rewrite_rules( true );
		plupop( 'pl_save_version', time() );

		$types = array( 'sections', 'core', 'custom' );
		
		foreach( $types as $t ) {
			
			$compiled = get_transient( "pagelines_{$t}_css" );
			$backup = get_transient( "pagelines_{$t}_css_backup" );
			
			if ( ! is_array( $backup ) && is_array( $compiled ) && strpos( $compiled[$t], 'PARSE ERROR' ) === false )
				set_transient( "pagelines_{$t}_css_backup", $compiled, 604800 );
		
			delete_transient( "pagelines_{$t}_css" );	
		}	
	}
	
	function pagelines_insert_core_less_callback( $code ) {

		global $pagelines_raw_lesscode_external;	
		$out = '';
		if ( is_array( $pagelines_raw_lesscode_external ) && ! empty( $pagelines_raw_lesscode_external ) ) {

			foreach( $pagelines_raw_lesscode_external as $file ) {
				
				if( is_file( $file ) )
					$out .= pl_file_get_contents( $file );
			}
			return $code . $out;
		}
		return $code;
	}
	
	function get_all_active_sections() {
		
		$out = '';
		global $load_sections;
		$available = $load_sections->pagelines_register_sections( true, true );

		$disabled = get_option( 'pagelines_sections_disabled', array() );
		foreach( $disabled as $type ) {
			foreach( $type as $disable )
				if( isset( $avaliable[$type][$disable] ) )
					unset( $avalable[$type][$disable] );
			}	
		foreach( $available as $t ) {		
			foreach( $t as $key => $data ) {
				if ( $data['less'] ) {
					if ( is_file( $data['base_dir'] . '/style.less' ) )
						$out .= pl_file_get_contents( $data['base_dir'] . '/style.less' );
					elseif( is_file( $data['base_dir'] . '/color.less' ))
						$out .= pl_file_get_contents( $data['base_dir'] . '/color.less' );	
				}
			}	
		}
		return apply_filters('pagelines_lesscode', $out);
	}
	
} //end of PageLinesRenderCSS

function pagelines_insert_core_less( $file ) {
	
	global $pagelines_raw_lesscode_external;
	
	if( !is_array( $pagelines_raw_lesscode_external ) )
		$pagelines_raw_lesscode_external = array();
	
	$pagelines_raw_lesscode_external[] = $file;
}