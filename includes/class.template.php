<?php
/**
 * 
 *
 *  PageLines Template Class
 *
 *
 *  @package PageLines Core
 *  @subpackage Sections
 *  @since 4.0
 *
 */
class PageLinesTemplate {

	var $id;		// Root id for section.
	var $name;		// Name for this section.
	var $settings;	// Settings for this section
	
	var $layout;	// Layout type selected
	var $sections = array(); // HTML sections/effects for the page
	
	var $tsections = array(); 
	var $allsections = array();
	var $default_allsections = array();
	
	
	/**
	 * PHP5 constructor
	 *
	 */
	function __construct( $template_type = false ) {
		global $post;
		global $pl_section_factory;
	
		$post = (!isset($post) && isset($_GET['post'])) ? get_post($_GET['post'], 'object') : $post;
		
		$this->factory = $pl_section_factory->sections;
		
		// All section control settings
		
		$sc_set = (is_pagelines_special()) ? PAGELINES_SPECIAL : PAGELINES_SETTINGS;
		
		$this->scontrol = ploption('section-control', array('setting' => $sc_set));
		
		$this->map = $this->get_map();
		
		/**
		 * Template Type
		 * This is how we decide which sections belong on the page
		 */
		if( $template_type != false )
			$this->template_type = $template_type;
	
		else{
			if(is_admin())
				$this->template_type = $this->admin_page_type_breaker();
			else
				$this->template_type = $this->page_type_breaker();
		}
	
		if(!is_admin())
			$this->template_name = $this->page_type_name();
	
		$this->load_sections_on_hook_names();
	
	}

	function adjust_template_type($type){
		$this->template_type = $type; 
		$this->load_sections_on_hook_names();
	}

	/**
	 * Returns template type based on WordPress conditionals
	 */
	function page_type_breaker(){
		global $post;
		
		if(is_404())
			return '404';
		elseif(is_tag())
			return 'tag';
		elseif(is_search())
			return 'search';
		elseif(is_category())
			return 'category';
		elseif(is_author())
			return 'author';
		elseif(is_archive())
			return 'archive';
		elseif(is_home())
			return 'posts';
		elseif(is_page_template()){
			/*
				Strip the page. and .php from page.[template-name].php
			*/
			$page_filename = str_replace('.php', '', get_post_meta($post->ID,'_wp_page_template',true));
			$template_name = str_replace('page.', '', $page_filename);
			return $template_name;
		}elseif( get_post_type() && get_post_type() != 'post' && get_post_type() != 'page' )
			return get_post_type();
		elseif( is_single() )
			return 'single';
		else
			return 'default';
	}
	
	
	function page_type_name(){
		return $this->map['templates']['templates'][$this->template_type]['name'];
	}
	
	/**
	 * Returns template type based on elements in WP admin
	 */
	function admin_page_type_breaker(){
		global $post;
		
		
		if ( !is_object( $post ) ) 
			return 'default';
		
		if( isset($post) && $post->post_type == 'post' )
			return 'single';
		elseif( isset($_GET['page']) && $_GET['page'] == 'pagelines' )
			return 'posts';
		elseif( isset($post) && !empty($post->page_template) && $post->post_type == "page" ) {
			$page_filename = str_replace('.php', '', $post->page_template);
			$template_name = str_replace('page.', '', $page_filename);
			return $template_name;
		} elseif( isset($post) && get_post_meta($post->ID,'_wp_page_template',true) ){
			$page_filename = str_replace('.php', '', get_post_meta($post->ID,'_wp_page_template',true));
			$template_name = str_replace('page.', '', $page_filename);
			return $template_name;
		} elseif( isset($post) && isset($post->post_type) )
			return $post->post_type;
		else 
			return 'default';
		
	}
	
		
	/**
	 * Get current post type, set as GET on 'add new' pages
	 */
	function current_admin_post_type(){
		global $pagenow;
		global $post;
		$current_post_type = ( !isset($post) && isset($_GET['post_type']) ) 
							? $_GET['post_type'] 
							: ( isset($post) && isset($post->post_type) ? $post->post_type : 
								($pagenow == 'post-new.php' ? 'post' : null));		
		
		return $current_post_type;
		
	}
	

		
	
	/**
	 *
	 * Load sections on to class attributes the correspond w/ hook args
	 *
	 * TODO Account for different types of loads. e.g sidebar2 should only load if it is shown in the layout
	 *
	 */
	function load_sections_on_hook_names(){
		
		foreach( $this->map as $hook => $h ){
			
			$tsections = $this->sections_at_hook( $hook, $h );
			
			// Set All Sections As Defined By the Map
			if( is_array($tsections) ) 
				$this->default_allsections = array_merge($this->default_allsections, $tsections);
			
			// Remove sections deactivated by Section Control
			if(!is_admin())
				$tsections = $this->unset_hidden_sections($tsections, $hook);
			
			// Set Property after Template Hook Args
			$this->$hook = $tsections;
		
			// Create an array with all sections used on current page - 
			if(is_array($this->$hook)) 
				$this->allsections = array_merge($this->allsections, $this->$hook);
			
		}
		
	}
	
	
	/**
	 * For a given hook, see which sections are placed there and return them
	 */
	function sections_at_hook( $hook, $h ){
		
		if( $hook == 'main' ){
	
			if(isset($h['templates'][$this->template_type]['sections']))
				$tsections = $h['templates'][$this->template_type]['sections'];
			elseif(isset($h['templates']['default']['sections']))
				$tsections = $h['templates']['default']['sections'];
			
		} elseif( $hook == 'templates' ) {
			
			if(isset($h['templates'][$this->template_type]['sections']))
				$tsections = $h['templates'][$this->template_type]['sections'];
			elseif(isset($h['templates']['default']['sections']))
				$tsections = $h['templates']['default']['sections'];
			
		} elseif(isset($h['sections'])) { 
			
			// Get Sections assigned in map
			$tsections = $h['sections'];

		} else {
			
			$tsections = array();
			
		}
		
		return $tsections;
	}
	
	/**
	 * Unset sections based on section
	 */
	function unset_hidden_sections($ta_sections, $hook_id){
			
		global $post;
		
		// Non-meta page
		if ( !is_object( $post ) ) 
			return $ta_sections;
	
			
		if(is_array($ta_sections)){
			foreach($ta_sections as $key => $sid){
				
				$template_slug = $this->get_template_slug( $hook_id );	
				
				$sc = $this->sc_settings( $template_slug, $sid );
			
				if($this->unset_section($sid, $template_slug, $sc))
					unset($ta_sections[$key]);
			
			}
		}
		
		return $ta_sections;
		
	}
	
	/**
	 * Get Section Control Settings for Section
	 */
	function sc_settings( $template_slug, $sid ){
	
		$sc = (isset($this->scontrol[$template_slug][$sid])) ? $this->scontrol[$template_slug][$sid] : null;
	
		return $sc;	
		
	}
	
	function get_template_slug( $hook_id ){
		
		// Get template slug
		if($hook_id == 'templates')
			$template_slug = $hook_id.'-'.$this->template_type;
		elseif ($hook_id == 'main')
			$template_slug = $hook_id.'-'.$this->template_type;
		else
			$template_slug = $hook_id;
			
		return $template_slug;
	}
	
	/**
	 * Unset section based on Section Control
	 */
	function unset_section( $sid, $template_slug, $sc ){
		global $post;
		
		$post_id = ( isset($post) ) ? $post->ID : null;
		
		$oset = array('post_id' => $post_id);
		
		// Global Section Control Array
			$general_hide = (isset($sc['hide'])) ? true : false;
		
		// Meta Controls
		if(is_pagelines_special()){
			$special_type = $this->template_type;
			$meta_reverse = ( $sc[$special_type]['show'] ) ? true : false;
			$meta_hide = ( $sc[$special_type]['hide'] ) ? true : false;
		} else {
			$meta_reverse = ( plmeta( meta_option_name( array('show', $template_slug, $sid) ) , $oset ) ) ? true : false;
			$meta_hide = ( plmeta( meta_option_name( array('hide', $template_slug, $sid) ), $oset ) ) ? true : false;
		}
		
		return ( ($general_hide && !$meta_reverse) || (!$general_hide && $meta_hide) ) ? true : false;
		
		
	}
	

	/**
	 * Hook up sections to hooks throughout the theme
	 * Specifically, the hooks should link w/ template map slugs
	 */
	function hook_and_print_sections(){
		
		foreach( $this->map as $hook_id => $h ){

			if( isset($h['hook']) )
				add_action( $h['hook'], array(&$this, 'print_section_html') );

		}		
		
	}

	/**
	 * Print section HTML from hooks.
	 */
	function print_section_html( $hook ){
	
		global $post;
		global $pagelines_post;		
		

		/**
		 * Sections assigned to array already in get_loaded_sections
		 */
		if( is_array( $this->$hook ) ){

			$markup_type = $this->map[$hook]['markup'];

			/**
			 * Parse through sections assigned to this hook
			 */
			foreach( $this->$hook as $sid ){

				$sc = $this->sc_settings( $hook, $sid );
				
				/**
				 * If this is a cloned element, remove the clone flag before instantiation here.
				 */
				$pieces = explode("ID", $sid);		
				$section = $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : null;
				
				if( $this->in_factory( $section ) ){
					$this->factory[ $section ]->before_section( $markup_type, $clone_id);
				
					$this->factory[ $section ]->section_template_load( $clone_id ); // If in child theme get that, if not load the class template function
					
					$this->factory[ $section ]->after_section( $markup_type );
				}
			
				$post = $pagelines_post; // Set the $post variable back to the default for the page (prevents sections from messing with others)
	
			}
		}
	}
	
	/**
	 * Tests if the section is in the factory singleton
	 * @since 1.0.0
	 */
	function in_factory( $section ){	
		return ( isset($this->factory[ $section ]) && is_object($this->factory[ $section ]) ) ? true : false;
	}
	
	/**
	 * Gets template map, sets option if not present
	 * @since 1.0.0
	 */
	function get_map(){
		
		// Get Section / Layout Map
		if(get_option('pagelines_template_map') && is_array(get_option('pagelines_template_map'))){
			$map = get_option('pagelines_template_map');
			return $this->update_template_config($map);
			
		}else{
		
			$config = $this->update_template_config( the_template_map() );
			update_option('pagelines_template_map', $config );
			return $config;
		}
	}
	
	/**
	 * When the default map is updated, we need to pull in the additional w/o the option val
	 * This will also be used for post types, that are added to add them to the map
	 *
	 * @since 1.0.0
	 * 
	 */
	function update_template_config( $map ){
		
		$map_setup = the_template_map();
		
		// Use the map config array, and parse against the option value
		foreach( $map_setup as $hook_id => $hook_info ){
			
			if( !isset( $map[$hook_id] ) || !is_array( $map[$hook_id] ) )
				$map[$hook_id] = $hook_info;
		
			// Use the names from the config instead
			$map[$hook_id]['name'] = $hook_info['name'];
			$map[$hook_id]['hook'] = $hook_info['hook'];
			$map[$hook_id]['markup'] = $hook_info['markup'];
			
			// Go through the sub-templates as well
			if(isset($hook_info['templates'])){
				
				foreach($hook_info['templates'] as $sub_template => $stemplate){
					
					if( !isset( $map[$hook_id]['templates'][$sub_template] ) )
						$map[$hook_id]['templates'][$sub_template] = $stemplate;
					
					$map[$hook_id]['templates'][$sub_template]['name'] = $stemplate['name'];
				}
				
			}
		}
	
		foreach( $map['templates']['templates'] as $hook => $h ){
			
			if(!isset($map_setup['templates']['templates'][$hook]))
				unset($map['templates']['templates'][$hook]);
				
			if(!isset($map_setup['main']['templates'][$hook]))
				unset($map['main']['templates'][$hook]);
			
		}

		
		return $map;
		
	}
	

	
	function reset_templates_to_default(){
		update_option('pagelines_template_map', the_template_map());
	}

	function print_template_section_headers(){

		if(is_array($this->allsections)){ 
			
			foreach($this->allsections as $sid){
				
				/**
				 * If this is a cloned element, remove the clone flag before instantiation here.
				 */
				$pieces = explode("ID", $sid);		
				$section = $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : null;
				
				if( $this->in_factory( $section ) )
					$this->factory[$section]->section_head( $clone_id );
					
			}
			
		}
		
	}
	
	/**
	 * Runs the options w/ cloning
	 *
	 * @package PageLines Core
	 * @subpackage Sections
	 * @since 2.0.b3
	 */
	function load_section_optionator(){
	
		foreach( $this->default_allsections as $section_slug ){
			
			$pieces = explode("ID", $section_slug);		
			$section = (string) $pieces[0];
			$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;
			
			if(isset($this->factory[$section]))
				$this->factory[$section]->section_optionator( array( 'clone_id' => $clone_id ) );
		
			
		}
	
		// Get inactive
		foreach( $this->factory as $key => $section ){
			
			$inactive = ( !in_array( $key, $this->default_allsections) ) ? true : false;
			
			if($inactive)
				$section->section_optionator( array('clone_id' => $clone_id, 'active' => false) );
		}

	}
	
	
	/**
	 * Print Section Styles (Hooked to wp_print_styles)
	 *
	 */
	function print_template_section_styles(){
	
		if(is_array($this->allsections)){
			foreach($this->allsections as $section){
				
				if($this->in_factory( $section )) 
					$this->factory[$section]->section_styles();
					
			}	
		}
	
	}
	

	function print_template_section_scripts(){


		foreach($this->allsections as $section){

			if($this->in_factory( $section )){
				
				$section_scripts = $this->factory[$section]->section_scripts();
				
				
				if(is_array( $section_scripts )){
					
					foreach( $section_scripts as $js_id => $js_atts){
						
						$defaults = array(
								'version' => '1.0',
								'dependancy' => null,
								'footer' => true
							);

						$parsed_js_atts = wp_parse_args($js_atts, $defaults);
						
						wp_register_script($js_id, $parsed_js_atts['file'], $parsed_js_atts['dependancy'], $parsed_js_atts['version'], true);

						wp_print_scripts($js_id);

					}

				}
			}

		}
	}
	
	/**
	 * This was taken from core WP because the function hasn't loaded yet, and isn't accessible.
	 */
	function get_page_templates() {
		$themes = get_themes();
		$theme = get_current_theme();
		$templates = $themes[$theme]['Template Files'];
		$page_templates = array();

		if ( is_array( $templates ) ) {
			$base = array( trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()) );

			foreach ( $templates as $template ) {
				$basename = str_replace($base, '', $template);

				// don't allow template files in subdirectories
				if ( false !== strpos($basename, '/') )
					continue;

				if ( 'functions.php' == $basename )
					continue;

				$template_data = implode( '', file( $template ));

				$name = '';
				if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) )
					$name = _cleanup_header_comment($name[1]);

				if ( !empty( $name ) ) {
					$page_templates[trim( $name )] = $basename;
				}
			}
		}

		return $page_templates;
	}

} /* ------ END CLASS ------ */


/**
 * PageLines Template Object 
 * @global object $pagelines_template
 * @since 1.0.0
 */
function build_pagelines_template(){	
	$GLOBALS['pagelines_template'] = new PageLinesTemplate;	
}

/**
 * Save Site Template Map
 *
 * @since 1.0.0
 */
function save_template_map($templatemap){	
	update_option('pagelines_template_map', $templatemap);
}


/**
 *  Used to parse section HTML for hooks
 *
 * @since 4.0.0
 */
function pagelines_ob_section_template($section){

	/*
		Start Output Buffering
	*/
	ob_start();
	
	/*
		Run The Section Template
	*/
	$section->section_template( true );

	/*
		Clean Up Buffered Output
	*/
	ob_end_clean();

	
}

function reset_templates_to_default(){	
	PageLinesTemplate::reset_templates_to_default();
}


/**
 *  Workaround for warning on WP login page when pagelines_template variable doesn't exist
 * Due to there being no "pagelines_before_html" hook present. Not ideal; but best solution for now.
 *
 * @since 4.0.0
 */
function workaround_pagelines_template_styles(){	
	global $pagelines_template; 
	if(is_object($pagelines_template)){
		$pagelines_template->print_template_section_styles();
	}
	else return;
}

/*
	TEMPLATE MAP
	
	This array controls the default template map of section in the theme
	Each top level needs a hook; and the top-level template needs to be included 
	as an arg in said hook...
*/
function the_template_map() {
	
	$template_map = array();
	
	$page_templates = the_sub_templates('templates');
	$content_templates = the_sub_templates('main');
	
	$template_map['header'] = array(
		'hook' 			=> 'pagelines_header', 
		'name'			=> 'Site Header',
		'markup'		=> 'content', 
		'sections' 		=> array( 'PageLinesBranding' , 'PageLinesNav', 'PageLinesSecondNav' )
	);
	
	$template_map['footer'] = array(
		'hook' 			=> 'pagelines_footer', 
		'name'			=> 'Site Footer', 
		'markup'		=> 'content', 
		'sections' 		=> array('PageLinesFootCols')
	);
	
	$template_map['templates'] = array(
		'hook'			=> 'pagelines_template', 
		'name'			=> 'Page Templates', 
		'markup'		=> 'content', 
		'templates'		=> $page_templates,
	);
	
	$template_map['main'] = array(
		'hook'			=> 'pagelines_main', 
		'name'			=> 'Text Content Area',
		'markup'		=> 'copy', 
		'templates'		=> $content_templates,
	);
	
	$template_map['morefoot'] = array(
		'name'			=> 'Morefoot Area',
		'hook' 			=> 'pagelines_morefoot',
		'markup'		=> 'content', 
		'version'		=> 'pro',
		'sections' 		=> array('PageLinesMorefoot', 'PageLinesTwitterBar')
	);
	
	$template_map['sidebar1'] = array(
		'name'			=> 'Sidebar 1',
		'hook' 			=> 'pagelines_sidebar1',
		'markup'		=> 'copy', 
		'sections' 		=> array('PrimarySidebar')
	);
	
	$template_map['sidebar2'] = array(
		'name'			=> 'Sidebar 2',
		'hook' 			=> 'pagelines_sidebar2',
		'markup'		=> 'copy', 
		'sections' 		=> array('SecondarySidebar')
	);
	
	$template_map['sidebar_wrap'] = array(
		'name'			=> 'Sidebar Wrap',
		'hook' 			=> 'pagelines_sidebar_wrap',
		'markup'		=> 'copy', 
		'version'		=> 'pro',
		'sections' 		=> array()
	);
	
	return apply_filters('pagelines_template_map', $template_map); 
}


function the_sub_templates( $t = 'templates' ){
	
	$map = array(
		'default' => array(
				'name'			=> 'Default Page',
				'sections' 		=> ($t == 'main') ? array('PageLinesPostLoop', 'PageLinesComments') : array('PageLinesContent')
		),
		'posts' => array(
				'name'			=> 'Blog',
				'sections' 		=> ($t == 'main') ? array('PageLinesPostsInfo','PageLinesPostLoop', 'PageLinesPagination') : array('PageLinesContent')
			),
		'single' => array(
				'name'			=> 'Blog Post',
				'sections' 		=> ($t == 'main') ? array('PageLinesPostNav', 'PageLinesPostLoop', 'PageLinesShareBar', 'PageLinesComments', 'PageLinesPagination') : array('PageLinesContent')
			),
		'alpha' => array(
				'name'			=> 'Feature Page',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesFeatures', 'PageLinesBoxes', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'beta' => 	array(
				'name'			=> 'Carousel Page',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesCarousel', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'gamma' => 	array(
				'name'			=> 'Box Page',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesSoapbox', 'PageLinesBoxes' ),
				'version'		=> 'pro'
			),
		'delta' => 	array(
				'name'			=> 'Highlight Page',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		'epsilon' => 	array(
				'name'			=> 'Banner Page',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesBanners', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		'tag' => array(
				'name'			=> 'Tag',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'archive' => 	array(
				'name'			=> 'Archive',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'category' => 	array(
				'name'			=> 'Category',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'search' => 	array(
				'name'			=> 'Search',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'author' => 	array(
				'name'			=> 'Author',
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		
		
	);
	
	if($t == 'templates'){
		$map["404"] = array(
			'name'			=> '404 Error',
			'sections' 		=> array( 'PageLinesNoPosts' ),
		);
	}
	
	
	$pt = custom_post_type_handler( $t );

	$map = array_merge($map, $pt);

	return apply_filters('the_sub_templates', $map, $t);
	
}

/**
 * Handles custom post types, and adds panel if applicable
 */
function custom_post_type_handler( $area = 'main' ){
	global $post;
	
	// Get all 'public' post types
	$pts = get_post_types(array( 'publicly_queryable' => true ));

	
	if(isset($pts['page']))
		unset($pts['page']);
	
	if(isset($pts['post']))
		unset($pts['post']);

	$post_type_array = array();

	foreach($pts as $public_post_type){
		
		$sections = ($area == 'template') ? 'PageLinesContent' : 'PageLinesPostLoop';
	
		$post_type_array[$public_post_type] = array(
			'name'		=> ucfirst($public_post_type), 
			'sections'	=> array($sections)
		);
		
	}
	
	return $post_type_array;
	
}