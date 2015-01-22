<?php


class PageLinesRenderCSS
{

	private static $instance;

	private static $types = array( 'core','sections','extended','custom' );

	const AGE_LIMIT_COMPILED = 86400;

	const AGE_LIMIT_BACKUP = 604800;

	private function __construct()
	{
		$this->actions();
	}

	/**
	 *
	 *  Load LESS files
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function get_core_lessfiles()
	{
		$files = array(
			'reset',
			'pl-core',
			'pl-wordpress',
			'pl-plugins',
			'grid',
			'alerts',
			'labels-badges',
			'tooltip-popover',
			'buttons',
			'type',
			'dropdowns',
			'accordion',
			'carousel',
			'navs',
			'modals',
			'thumbnails',
			'component-animations',
			'utilities',
			'pl-objects',
			'pl-tables',
			'wells',
			'forms',
			'breadcrumbs',
			'close',
			'pager',
			'pagination',
			'progress-bars',
			'icons',
			'responsive',
		);
		
		return apply_filters( 'pagelines_core_less_files', $files );
	}

	/**
	 *
	 *  Dynamic mode, CSS is loaded to a file using wp_rewrite
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	private function actions()
	{
		global $pagelines_template;

		add_filter( 'query_vars'                , array( $this, 'pagelines_add_trigger' ) );
		add_action( 'template_redirect'         , array( $this, 'pagelines_less_trigger' ) , 15);
		add_action( 'template_redirect'         , array( $this, 'less_file_mode' ) );
		add_action( 'extend_flush'              , array( $this, 'flush_version' ), 1 );
		add_action( 'admin_notices'             , array( $this, 'less_error_report') );
		add_action( 'wp_before_admin_bar_render', array( $this, 'less_css_bar' ) );

		add_action( 'wp_enqueue_scripts'        , array( $this, 'load_less_css' ) );
		add_action( 'wp_head'                   , array( $this, 'do_background_image' ), 13 );
		add_action( 'pagelines_head_last'       , array( $this, 'draw_inline_custom_css' ) , 25 );
		add_action( 'wp_head'                   , array( &$pagelines_template, 'print_template_section_head' ), 12 );
		add_action( 'pl_scripts_on_ready'       , array( &$pagelines_template, 'print_on_ready_scripts' ), 12 );

		if ( defined( 'PL_CSS_FLUSH' ) )
			do_action( 'extend_flush' );

		do_action( 'pagelines_max_mem' );
	}

	public static function get_compiled_filename( $type )
	{
		$stamp = get_theme_mod('pl_save_version');
		return "compiled-css-$stamp-$type.css";
	}

	function less_file_mode()
	{
		if ( ! get_theme_mod( 'pl_save_version' ) )
			return;

		if ( defined( 'LESS_FILE_MODE' ) && false == LESS_FILE_MODE )
			return;

		if ( defined( 'PL_NO_DYNAMIC_URL' ) && true == PL_NO_DYNAMIC_URL )
			return;

		$css_url  = self::get_css_dir('url');
		$css_path = self::get_css_dir('path');

		foreach ( self::$types as $type )
		{
			if ( 'custom' == $type )
				continue; // output inline in head

			$compiled_filename = self::get_compiled_filename( $type );

			if ( file_exists( trailingslashit( $css_path ) . $compiled_filename ) )
				return;

			if ( false == $this->check_posix() )
				return;

			$cached = $this->get_compiled( $type );
			$css = !empty( $cached['compiled'] ) ? $cached['compiled'] : '';

			if ( ! $css )
				continue;
			
			$css .= "\n/* ";
			$css .= sprintf( 'CSS was compiled at %s and took %s seconds using %sMB of unicorn dust.',
				date( DATE_RFC822, $cached['time'] ),
				$cached['c_time'],
				function_exists('memory_get_usage') ? round( memory_get_usage() / 1024 / 1024, 2 ) : 0
			);
			if ( is_multisite() )
				printf( ' on blog [%s]', get_current_blog_id() );
			$css .= "*/\n";

			$this->write_css_file( $css, $type );
			$this->write_css_file( $this->_minify($css), "$type.min" );
		} // each type
	}

	function check_posix() {

		if ( true == apply_filters( 'render_css_posix_', false ) )
			return true;

		if ( ! function_exists( 'posix_geteuid') || ! function_exists( 'posix_getpwuid' ) )
			return false;

		$User = posix_getpwuid( posix_geteuid() );
		$File = posix_getpwuid( fileowner( __FILE__ ) );
		if( $User['name'] !== $File['name'] )
			return false;

		return true;
	}

	public static function get_css_dir( $type = '' )
	{
		$folder = apply_filters( 'pagelines_css_upload_dir', wp_upload_dir() );

		if ( 'path' == $type )
			return trailingslashit( $folder['basedir'] ) . 'pagelines';
		else
			return trailingslashit( $folder['baseurl'] ) . 'pagelines';
	}

	function write_css_file( $contents, $type )
	{
		add_filter('request_filesystem_credentials', '__return_true' );

		$method      = '';
		$url         = 'themes.php?page=pagelines';
		$folder      = self::get_css_dir( 'path' );
		$filename    = self::get_compiled_filename( $type );

		if ( ! is_dir( $folder ) )
			wp_mkdir_p( $folder );

		include_once( ABSPATH . 'wp-admin/includes/file.php' );

		if ( is_writable( $folder ) )
		{
			$creds = request_filesystem_credentials($url, $method, false, false, null);
			if ( ! WP_Filesystem($creds) )
				return false;
		}

		global $wp_filesystem;
		
		if ( is_object( $wp_filesystem ) )
		{
			$wp_filesystem->put_contents( trailingslashit( $folder ) . $filename, $contents, FS_CHMOD_FILE);
		}
		else
			return false;
	}

	function do_background_image() {

		global $pagelines_ID;
		if ( is_archive() || is_home() )
			$pagelines_ID = null;
		$oset = array( 'post_id' => $pagelines_ID );
		$oid = 'page_background_image';
		$sel = cssgroup('page_background_image');
		if( !ploption('supersize_bg', $oset) && ploption( $oid . '_url', $oset )){

			$bg_repeat = (ploption($oid.'_repeat', $oset)) ? ploption($oid.'_repeat', $oset) : 'no-repeat';
			$bg_attach = (ploption($oid.'_attach', $oset)) ? ploption($oid.'_attach', $oset): 'scroll';
			$bg_pos_vert = (ploption($oid.'_pos_vert', $oset) || ploption($oid.'_pos_vert', $oset) == 0 ) ? (int) ploption($oid.'_pos_vert', $oset) : '0';
			$bg_pos_hor = (ploption($oid.'_pos_hor', $oset) || ploption($oid.'_pos_hor', $oset) == 0 ) ? (int) ploption($oid.'_pos_hor', $oset) : '50';
			$bg_selector = (ploption($oid.'_selector', $oset)) ? ploption($oid.'_selector', $oset) : $sel;
			$bg_url = ploption($oid.'_url', $oset);

			$css = sprintf('%s{ background-image:url(%s);', $bg_selector, $bg_url);
			$css .= sprintf('background-repeat: %s;', $bg_repeat);
			$css .= sprintf('background-attachment: %s;', $bg_attach);
			$css .= sprintf('background-position: %s%% %s%%;}', $bg_pos_hor, $bg_pos_vert);
			echo inline_css_markup( 'pagelines-page-bg', $css );

		}
	}


	function less_css_bar() {
		foreach ( self::$types as $t ) {
			if ( ploption( "pl_less_error_{$t}" ) ) {

				global $wp_admin_bar;
				$wp_admin_bar->add_menu( array(
					'parent' => false,
					'id' => 'less_error',
					'title' => sprintf( '<span class="label label-warning pl-admin-bar-label">%s</span>', __( 'LESS Compile error!', 'pagelines' ) ),
					'href' => admin_url( PL_SETTINGS_URL ),
					'meta' => false
				));
				$wp_admin_bar->add_menu( array(
					'parent' => 'less_error',
					'id' => 'less_message',
					'title' => sprintf( __( 'Error in %s Less code: %s', 'pagelines' ), $t, ploption( "pl_less_error_{$t}" ) ),
					'href' => admin_url( PL_SETTINGS_URL ),
					'meta' => false
				));
			}
		}
	}

	function less_error_report() {

		$default = '<div class="updated fade update-nag"><div style="text-align:left"><h4>PageLines %s LESS/CSS error.</h4>%s</div></div>';

		foreach ( self::$types as $t ) {
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
	function draw_inline_custom_css()
	{
		$a = $this->get_compiled('custom');
		
		if ( $a['compiled'] )
			return inline_css_markup( 'pl-custom', rtrim( $this->minify($a['compiled']) ) );
	}

	/**
	 *
	 *  Draw dynamic CSS inline.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function draw_inline_dynamic_css()
	{
		if ( apply_filters( 'disable_dynamic_css', false ) )
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
	function get_dynamic_css()
	{
		$pagelines_dynamic_css = new PageLinesCSS;
		$pagelines_dynamic_css->typography();

		$typography = $pagelines_dynamic_css->css;

		$pagelines_dynamic_css->css = '';
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
	 *  Enqueue the dynamic css files.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_less_css()
	{
		$css_url = $this->get_css_dir('url');
		$css_path = $this->get_css_dir('path');

		foreach ( self::$types as $type )
		{
			$load = ( is_pl_debug() || ! ploption('pl_minify') ) ? $type : "$type.min";
			$filename = self::get_compiled_filename( $load );
			
			if ( file_exists( path_join($css_path,$filename) ) )
			{
				wp_enqueue_style("pl-$type", "$css_url/$filename", false, null);
			}
		}

	}

	function get_dynamic_url() {

		global $blog_id;
		$version = get_theme_mod( "pl_save_version" );

		if ( ! $version )
			$version = '1';

		if ( is_multisite() )
			$id = $blog_id;
		else
			$id = '1';

		$version = sprintf( '%s_%s', $id, $version );

		$parent = apply_filters( 'pl_parent_css_url', PL_PARENT_URL );

		if ( '' != get_option('permalink_structure') && ! $this->check_compat() )
			$url = sprintf( '%s/pagelines-compiled-css-%s/', $parent, $version );
		else {
			$url = add_query_arg(array('pageless' => $version), $this->get_base_url());
		}
		if ( defined( 'DYNAMIC_FILE_URL' ) )
			$url = DYNAMIC_FILE_URL;

		if ( has_action( 'pl_force_ssl' ) )
			$url = str_replace( 'http://', 'https://', $url );

		return apply_filters( 'pl_dynamic_css_url', $url );
	}

	function get_base_url()
	{
		if ( function_exists('icl_get_home_url') )
			return icl_get_home_url();

		return get_home_url();
	}

	function check_compat()
	{
		if ( defined( 'LESS_FILE_MODE' ) && false == LESS_FILE_MODE && is_multisite() )
			return true;

		if ( function_exists( 'icl_get_home_url' ) )
			return true;

		if ( defined( 'PL_NO_DYNAMIC_URL' ) )
			return true;

		if ( site_url() !== get_home_url() )
			return true;

		if ( 'nginx' == substr($_SERVER['SERVER_SOFTWARE'], 0, 5) )
			return false;

		if ( empty($GLOBALS['is_apache']) )
			return true;
	}

	function get_compiled( $type )
	{
		$cache_key  = "pagelines_{$type}_css";
		$backup_key = "{$cache_key}_backup";

		if ( is_array( $cached = get_transient( $cache_key ) ) )
		{
			return $cached;
		}
		else
		{
			$start_time = microtime(true);
			build_pagelines_layout();

			$compiler = new PagelinesLess();
			$less     = $this->get_less( $type );
			$css      = $compiler->raw_less( $less, $type );
			$end_time = microtime(true);

			$cached = array(
				'compiled'  => $css,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()
			);

			if ( strpos( $css, 'PARSE ERROR' ) === false )
			{
				set_transient( $cache_key, $cached, self::AGE_LIMIT_COMPILED );
				set_transient( $backup_key, $cached, self::AGE_LIMIT_BACKUP );
				return $cached;
			}
			else
			{
				return get_transient( $backup_key );
			}
		}
	}

	function get_less( $type )
	{
		switch( $type )
		{
			case 'core' :
				return $this->get_core_lesscode();

			case 'sections' :
				return $this->get_all_active_sections();

			case 'extended' :
				$code = $this->get_dynamic_css(); // type, dynamic
				$code[] = $this->pagelines_insert_core_less_callback(''); // plugin-added
				return join("\n", $code);

			case 'custom' :
				return stripslashes( ploption('customcss') );
		}
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
				set_transient( 'pagelines_core_css', $a, self::AGE_LIMIT_COMPILED );
				set_transient( 'pagelines_core_css_backup', $a, self::AGE_LIMIT_BACKUP );
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
				set_transient( 'pagelines_sections_css', $a, self::AGE_LIMIT_COMPILED );
				set_transient( 'pagelines_sections_css_backup', $a, self::AGE_LIMIT_BACKUP );
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

			$custom = stripslashes( ploption( 'customcss' ) );

			$pless = new PagelinesLess();
			$custom =  $pless->raw_less( $custom, 'custom' );
			$end_time = microtime(true);
			$a = array(
				'custom'	=> $custom,
				'c_time'	=> round(($end_time - $start_time),5),
				'time'		=> time()
			);
			if ( strpos( $custom, 'PARSE ERROR' ) === false ) {
				set_transient( 'pagelines_custom_css', $a, self::AGE_LIMIT_COMPILED );
				set_transient( 'pagelines_custom_css_backup', $a, self::AGE_LIMIT_BACKUP );
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
	function get_core_lesscode()
	{
		return $this->load_core_cssfiles( $this->get_core_lessfiles() );
	}

	/**
	 *
	 *  Helper for get_core_less_code()
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function load_core_cssfiles( $files )
	{
		$less = pl_load_less_files( $files );
		
		/**
		 * filter 'pagelines_insert_core_less'
		 * @param	$less	string	
		 */
		return apply_filters( 'pagelines_insert_core_less', $less );
	}

	/**
	 *
	 *  Add rewrite.
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	public static function pagelines_less_rewrite( $wp_rewrite )
	{
	    $less_rule = array(
	        self::LESS_REWRITE_REGEX => self::LESS_REWRITE_REQUEST
	    );
	    $wp_rewrite->rules = $less_rule + $wp_rewrite->rules;
	}

	// flush_rules() if our rules are not yet included
	public static function check_rules()
	{
		$rules = get_option( 'rewrite_rules' );

		if ( empty( $rules[ self::LESS_REWRITE_REGEX ] ) )
			flush_rewrite_rules();
	}

	function pagelines_add_trigger( $vars )
	{
	    $vars[] = 'pageless';
	    return $vars;
	}

	function pagelines_less_trigger()
	{
		if ( ! $type = get_query_var('pageless') )
			return;

		header( 'Content-type: text/css' );
		header( 'Expires: ' );
		header( 'Cache-Control: max-age=604100, public' );

		$c = $this->get_compiled( $type );
		echo $this->minify($c['compiled']);

		exit;
	}

	/**
	 * Whether or not uncompressed styles should be served.
	 * @return [type] [description]
	 */
	public static function serve_pristine()
	{
		return (
			(defined('SCRIPT_DEBUG') && SCRIPT_DEBUG)
			|| is_pl_debug()
			|| ! ploption('pl_minify')
			);
	}

	/**
	 *
	 *  Minify
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	function minify( $css )
	{
		if ( self::serve_pristine() )
			return $css;

		return $this->_minify( $css );
	}

	function _minify( $css )
	{
	    $data = preg_replace( '#/\*.*?\*/#s', '', $css );
	    // remove new lines \\n, tabs and \\r
	    $data = preg_replace('/(\t|\r|\n)/', '', $data);
	    // replace multi spaces with singles
	    $data = preg_replace('/(\s+)/', ' ', $data);
	    //Remove empty rules
	    $data = preg_replace('/[^}{]+{\s?}/', '', $data);
	    // Remove whitespace around selectors and braces
	    $data = preg_replace('/\s*{\s*/', '{', $data);
	    // Remove whitespace at end of rule
	    $data = preg_replace('/\s*}\s*/', '}', $data);
	    // Just for clarity, make every rules 1 line tall
	    $data = preg_replace('/}/', "}\n", $data);
	    $data = str_replace( ';}', '}', $data );
	    $data = str_replace( ', ', ',', $data );
	    $data = str_replace( '; ', ';', $data );
	    $data = str_replace( ': ', ':', $data );
	    $data = preg_replace( '#\s+#', ' ', $data );

		if ( ! preg_last_error() )
			return $data;
		else
			return $css;
	}

	/**
	 *
	 *  Flush rewrites/cached css
	 *
	 *  @package PageLines Framework
	 *  @since 2.2
	 */
	public static function flush_version( $rules = true )
	{
		// Attempt to flush super-cache.
		if ( function_exists( 'prune_super_cache' ) ) {
			global $cache_path;
			$GLOBALS["super_cache_enabled"] = 1;
        	prune_super_cache( $cache_path . 'supercache/', true );
        	prune_super_cache( $cache_path, true );
		}
		// Attempt to flush w3 cache.
		if ( class_exists('W3_Plugin_TotalCacheAdmin') ) {
		    $plugin_totalcacheadmin = & w3_instance('W3_Plugin_TotalCacheAdmin');
		    $plugin_totalcacheadmin->flush_all();
		}

		if ( $rules )
			flush_rewrite_rules( true );

		foreach ( self::$types as $t )
		{
			$css_path = self::get_css_dir('path');

			foreach ( array($t,"$t.min") as $type )
			{
				$filename = self::get_compiled_filename( $type );
				$filepath = path_join($css_path, $filename);

				if ( is_file( $filepath ) )
					@unlink( $filepath );
			}

			$compiled = get_transient( "pagelines_{$t}_css" );
			$backup   = get_transient( "pagelines_{$t}_css_backup" );

			if ( ! is_array( $backup ) && is_array( $compiled ) && strpos( $compiled[$t], 'PARSE ERROR' ) === false )
				set_transient( "pagelines_{$t}_css_backup", $compiled, 604800 );

			delete_transient( "pagelines_{$t}_css" );
		}

		set_theme_mod( 'pl_save_version', time() );
	}

	function pagelines_insert_core_less_callback( $code )
	{
		global $pagelines_raw_lesscode_external;
	
		if (
			! empty( $pagelines_raw_lesscode_external )
			&& is_array( $pagelines_raw_lesscode_external )
			)
		{
			$less = pl_load_less_files( $pagelines_raw_lesscode_external );
			$code .= "\n" . $less;
		}

		return $code;
	}

	function get_all_active_sections()
	{
		global $load_sections;
		// refresh sections cache
		$available = $load_sections->pagelines_register_sections( true, true );

		$less = array();

		foreach ( pl_get_sections() as $s )
		{
			$data = $s->sinfo;
			if ( $data['less'] && $data['loadme'] )
			{
				if ( is_file( "{$s->base_dir}/style.less" ) )
					$less[] = pl_file_get_contents( "{$s->base_dir}/style.less" );
				elseif ( is_file( "{$s->base_dir}/color.less" ) )
					$less[] = pl_file_get_contents( "{$s->base_dir}/color.less" );
			}
		}

		$less = join( "\n", $less );

		return apply_filters('pagelines_lesscode', $less);
	}

	public static function instance()
	{
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

} // PageLinesRenderCSS

function pagelines_insert_core_less( $file )
{
	global $pagelines_raw_lesscode_external;

	if ( !is_array( $pagelines_raw_lesscode_external ) )
		$pagelines_raw_lesscode_external = array();

	$pagelines_raw_lesscode_external[] = $file;
}
