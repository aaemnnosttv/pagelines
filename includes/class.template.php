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
		
		$sc_set = (is_pagelines_special()) ? PAGELINES_SPECIAL : PAGELINES_TEMPLATES;
		
		$this->scontrol = ploption('section-control', array('setting' => $sc_set));
		
		$this->sc_default = ploption('section-control', array('setting' => PAGELINES_TEMPLATES));
		
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
			$type = '404_page';
		elseif( pl_is_cpt('archive') )
			$type = get_post_type_plural();
		elseif( pl_is_cpt() )
			$type = get_post_type();
		elseif(is_tag())
			$type = 'tag';
		elseif(is_search())
			$type = 'search';
		elseif(is_category())
			$type = 'category';
		elseif(is_author())
			$type = 'author';
		elseif(is_archive())
			$type = 'archive';
		elseif(is_home())
			$type = 'posts';
		elseif(is_page_template()){
			/*
				Strip the page. and .php from page.[template-name].php
			*/
			$page_filename = str_replace('.php', '', get_post_meta($post->ID,'_wp_page_template',true));
			$template_name = str_replace('page.', '', $page_filename);
			$type = $template_name;
		}elseif( is_single() )
			$type = 'single';
		else
			$type = 'default';
			
		return apply_filters('pagelines_page_type', $type, $post);
	}
	
	
	function page_type_name(){
		
		if(isset($this->map['templates']['templates'][$this->template_type]['name']))
			return $this->map['templates']['templates'][$this->template_type]['name'];
		else
			return ui_key( $this->template_type );
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
		
		/* Load in sections at hook in map, for this template type, allow for overriding */
		if( $hook == 'main' || $hook == 'templates' ){
			
			$sections = $this->section_cascade( $hook, $h );
			
			return apply_filters( 'pl_template_sections', $sections, $this->template_type, $hook );
			
		}
			
		elseif(isset($h['sections']))
			return $h['sections']; // Get Sections assigned in map

		else
			return array();
		
	}

	/**
	 * Run down the map, if not at hook in map, then load default
	 * 	if default doesn't load, load a blank array
	 */
	function section_cascade( $hook, $h ){
		
		if( isset($h['templates'][$this->template_type]['sections']) )
			return $h['templates'][$this->template_type]['sections'];
			
		elseif( isset($h['templates']['default']['sections']) )
			return $h['templates']['default']['sections'];
			
		else
			return array();
			
	}
	
	/**
	 * Unset sections based on section
	 */
	function unset_hidden_sections($ta_sections, $hook_id){
			
		global $post;
		
			
		if(is_array($ta_sections)){
			foreach($ta_sections as $key => $sid){
				
				$template_slug = $this->get_template_slug( $hook_id );	
				
				$sc = $this->sc_settings( $this->scontrol, $template_slug, $sid );
				$dsc = $this->sc_settings( $this->sc_default, $template_slug, $sid );
				
				if($this->unset_section($sid, $template_slug, $sc, $dsc))
					unset($ta_sections[$key]);
			
			}
		}
		
		return $ta_sections;
		
	}
	
	/**
	 * Get Section Control Settings for Section
	 */
	function sc_settings( $set, $tid, $sid ){
	
		return (isset($set[$tid][$sid])) ? $set[$tid][$sid] : null;

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
	function unset_section( $sid, $template_slug, $sc, $dsc){
		global $post;
	
		$post_id = ( isset($post) ) ? $post->ID : null;
		
		$oset = array('post_id' => $post_id);
		
		// Global Section Control Array
			$general_hide = (isset($dsc['hide'])) ? true : false;
	
		// Meta Controls
		if(is_pagelines_special()){
			$special_type = $this->template_type;
			
			$meta_reverse = ( isset($sc[$special_type]['show']) && $sc[$special_type]['show'] ) ? true : false;
			$meta_hide = ( isset($sc[$special_type]['hide']) && $sc[$special_type]['hide'] ) ? true : false;
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
		global $wp_query;
		global $pagelines_post;	
		
		// Save Handling Globals
		// Prevents sections from screwing them up.
		$save_query = $wp_query;
		$save_post = $post;
		
		/**
		 * Sections assigned to array already in get_loaded_sections
		 */
		if( is_array( $this->$hook ) ){

			$markup_type = $this->map[$hook]['markup'];

			/**
			 * Parse through sections assigned to this hook
			 */
			foreach( $this->$hook as $key => $sid ){	
				
				/**
				 * If this is a cloned element, remove the clone flag before instantiation here.
				 */
				$p = splice_section_slug($sid);
				$section = $p['section'];
				$clone_id = $p['clone_id'];
				
				if( $this->in_factory( $section ) ){
					
					$s = $this->factory[ $section ];
					
					$in_area = $this->$hook;
					
					$conjugation = $this->conjugation($hook, $key, $sid, $s);
				
		
					/**
					 * Load Template
					 * Get Template in Buffer 
					 */
					ob_start();
				
					// If in child theme get that, if not load the class template function
					$s->section_template_load( $clone_id );
			
					$template_output = plstrip( ob_get_clean() );
					
					if($template_output != ''){
				
						$s->before_section_template( $clone_id );
					
						$s->before_section( $markup_type, $clone_id, $conjugation);
				
						echo $template_output;
					
						$s->after_section( $markup_type );
					
						$s->after_section_template( $clone_id );
					
						
					}
				}
			
				$wp_query = $save_query;
				$post = $save_post;
	
			}
		}
	}
	
	/**
	 * For buffering, NOT BEING USED YET
	 */
	function next_up($in_area, $hook, $s){
		global $post;
		global $wp_query;
		global $template_output;
		global $last_drawn;	
		global $next_drawn;
		
		/**
		 * Load Next Template
		 * Attempt to load next template. Useful in conjugations.
		 ***************************************************************/
	
		$next_up = ( isset($in_area[$key+1]) ) ? $in_area[$key+1] : $this->conjugation_adjacent_area($hook, 'next');
		
		if($next_up != 'bottom'){
			$p = splice_section_slug($next_up);
			$psection = $p['section'];
		} 
		
		if( $next_up != 'bottom' && $this->in_factory( $psection ) ){
			
			$next_section = $this->factory[ $psection ];
			
			ob_start();
		
			// If in child theme get that, if not load the class template function
			$next_section->section_template_load( $clone_id );
		
			$template_output[ $next_section->id ] = plstrip( ob_get_clean() );
			
			if($template_output[$next_section->id] != '')
				$next_drawn = $next_section->id;
			
			$wp_query = $save_query;
			$post = $save_post;
				
		} elseif($next_up == 'bottom')
			$next_drawn = 'bottom';
		
		
		$content_hooks = array('main', 'sidebar1', 'sidebar2', 'sidebar_wrap');
		
		if(in_array($hook, $content_hooks)){
		
			if(!isset($in_area[$key-1]))
				$last_drawn = 'top';
				
			
		}
		
	}
	
	/**
	 * Load in the next and previous area as classes
	 * Useful for styling based on relationships between sections
	 */
	function conjugation( $hook, $key, $sid, $current_section ){
		/**
		 * Conjugation
		 */
		$in_area = $this->$hook;
		
		
		$pre = (isset($in_area[$key-1])) ? $in_area[$key-1] : $this->conjugation_adjacent_area($hook, 'prev');
		$next = (isset($in_area[$key+1])) ? $in_area[$key+1] : $this->conjugation_adjacent_area($hook, 'next');

		$pieces = explode("ID", $pre);		
		$pre_section = $pieces[0];

		if($pre == 'top') 
			$pre_class = 'top';
		elseif($pre && $this->in_factory( $pre_section ) )
			$pre_class = $this->factory[ $pre_section ]->id;
		else 
			$pre_class = 'top';
			
		$pieces = explode("ID", $next);		
		$post_section = $pieces[0];	
			
		if($next == 'bottom') 
			$post_class = 'bottom';
		elseif($next && $this->in_factory( $post_section ))
			$post_class = $this->factory[ $post_section ]->id;
		else
			$post_class = 'bottom';
			

		$conj = sprintf('%s-%s %s-%s', $pre_class, $current_section->id, $current_section->id, $post_class);
		
		return $conj;
	}
	
	/**
	 * Return sections from different areas
	 */
	function conjugation_adjacent_area( $hook, $relation = 'next' ){
		
		$order = array('header', 'templates', 'morefoot', 'footer');
		
		foreach($order as $key => $area){
			
			if( $hook == $area ){
				
				for($i = 1; $i <= 4; $i++) {
				    
					$adjust = ($relation == 'prev') ? $key-$i : $key+$i;

					$area = (isset($order[ $adjust ])) ? $order[ $adjust ] : false;

					if( $area && is_array($this->$area) && !empty($this->$area) ){

						$rel = ($relation == 'next') ? reset($this->$area) : end($this->$area);
						break;
						
					}
					
				}

				if(isset($rel))
					return $rel;
				elseif($relation == 'prev')
					return 'top';
				else
					return 'bottom';
				
			}
		}
		
		return ($relation == 'prev') ? 'top' : 'bottom';
	
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
		if(get_option( PAGELINES_TEMPLATE_MAP ) && is_array(get_option( PAGELINES_TEMPLATE_MAP ))){
			$map = get_option( PAGELINES_TEMPLATE_MAP );
			return $this->update_template_config($map);
			
		}else{
		
			$config = $this->update_template_config( the_template_map() );
			update_option( PAGELINES_TEMPLATE_MAP, $config );
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
	
	/**
	 * Callback for resetting the options to default
	 */
	function reset_templates_to_default(){
		
		update_option(PAGELINES_TEMPLATES, array());
		update_option(PAGELINES_TEMPLATE_MAP, the_template_map());
		
	}

	function run_before_page(){

		if(is_array($this->allsections)){ 
			
			foreach($this->allsections as $sid){
				
				/**
				 * If this is a cloned element, remove the clone flag before instantiation here.
				 */
				$pieces = explode("ID", $sid);		
				$section = $pieces[0];
				$clone_id = (isset($pieces[1])) ? $pieces[1] : null;
				
				if( $this->in_factory( $section ) ){
					
					$this->factory[$section]->section_before_page( $clone_id );
					
				}
			}
			
		}
		
	}
	
	function print_template_section_headers(){

		$lesscode = '';

		if(is_array($this->allsections)){ 
			
			foreach($this->allsections as $sid){
				
				/**
				 * If this is a cloned element, remove the clone flag before instantiation here.
				 */
				$p = splice_section_slug($sid);
				$section = $p['section'];
				$clone_id = $p['clone_id'];
				
				if( $this->in_factory( $section ) ){
					
					$s = $this->factory[$section];
					
					$s->section_head( $clone_id );
					
					global $supported_elements;
					
					$support = (isset($supported_elements['sections'][ $section ])) ? $supported_elements['sections'][ $section ] : false;
					
					if( $support && $support['disable_color'] )
						continue;	
							
					echo plstrip( $s->dynamic_style( $clone_id ) );
					
					/*
					 * Less CSS
					 */
					if( file_exists( $s->base_dir . '/color.less' ) )
						$lesscode .= file_get_contents( $s->base_dir.'/color.less' );
				
					
				}
				
				
			}
			
			if($lesscode != ''){
				
				$pless = new PagelinesLess();
				
				printf(
					'<style id="less-pagelines" rel="stylesheet" type="text/css">%s</style>', 
					plstrip( $pless->parse($lesscode) )
				);
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
	function load_section_optionator( $defaults = false ){
	
		if($defaults){
			
			foreach( $this->factory as $key => $section )
				$section->section_optionator( array() );

			
		} else {
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
			

	}
	
	
	/**
	 * Print Section Styles (Hooked to wp_print_styles)
	 *
	 */
	function print_template_section_styles(){
	
		if(is_array($this->allsections)){
			foreach($this->allsections as $section_slug){
				
				$p = splice_section_slug( $section_slug );
				
				if($this->in_factory( $p['section'] )) {
					
					$s = $this->factory[$p['section']];
					
					$s->section_styles();
				
					// Auto load style.css for simplicity if its there.
					if( file_exists( $s->base_dir . '/style.css' ) ){
						
						wp_register_style( $s->id, $s->base_url . '/style.css', array(), $s->settings['p_ver'], 'screen');
				 		wp_enqueue_style( $s->id );
				
					}
					
					
				}	
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
	update_option( PAGELINES_TEMPLATE_MAP, $templatemap);
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
		'name'			=> __( 'Site Header', 'pagelines' ),
		'markup'		=> 'content', 
		'sections' 		=> array( 'PageLinesBranding' , 'PageLinesNav', 'PageLinesSecondNav' )
	);
	
	$template_map['footer'] = array(
		'hook' 			=> 'pagelines_footer', 
		'name'			=> __( 'Site Footer', 'pagelines' ), 
		'markup'		=> 'content', 
		'sections' 		=> array('SimpleNav')
	);
	
	$template_map['templates'] = array(
		'hook'			=> 'pagelines_template', 
		'name'			=> __( 'Page Templates', 'pagelines' ), 
		'markup'		=> 'content', 
		'templates'		=> $page_templates,
	);
	
	$template_map['main'] = array(
		'hook'			=> 'pagelines_main', 
		'name'			=> __( 'Text Content Area', 'pagelines' ),
		'markup'		=> 'copy', 
		'templates'		=> $content_templates,
	);
	
	$template_map['morefoot'] = array(
		'name'			=> __( 'Morefoot Area', 'pagelines' ),
		'hook' 			=> 'pagelines_morefoot',
		'markup'		=> 'content', 
		'version'		=> 'pro',
		'sections' 		=> array('PageLinesMorefoot', 'PageLinesTwitterBar')
	);
	
	$template_map['sidebar1'] = array(
		'name'			=> __( 'Sidebar 1', 'pagelines' ),
		'hook' 			=> 'pagelines_sidebar1',
		'markup'		=> 'copy', 
		'sections' 		=> array('PrimarySidebar')
	);
	
	$template_map['sidebar2'] = array(
		'name'			=> __( 'Sidebar 2', 'pagelines' ),
		'hook' 			=> 'pagelines_sidebar2',
		'markup'		=> 'copy', 
		'sections' 		=> array('SecondarySidebar')
	);
	
	$template_map['sidebar_wrap'] = array(
		'name'			=> __( 'Sidebar Wrap', 'pagelines' ),
		'hook' 			=> 'pagelines_sidebar_wrap',
		'markup'		=> 'copy', 
		'version'		=> 'pro',
		'sections' 		=> array()
	);
	
	return apply_filters( PAGELINES_TEMPLATE_MAP, $template_map); 
}


function the_sub_templates( $t = 'templates' ){
	
	$map = array(
		'default' => array(
				'name'			=> __( 'Default Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array('PageLinesPostLoop', 'PageLinesComments') : array('PageLinesContent')
		),
		'alpha' => array(
				'name'			=> __( 'Feature Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesFeatures', 'PageLinesBoxes', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'beta' => 	array(
				'name'			=> __( 'Carousel Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array('PageLinesCarousel', 'PageLinesContent'),
				'version'		=> 'pro'
			),
		'gamma' => 	array(
				'name'			=> __( 'Box Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesSoapbox', 'PageLinesBoxes' ),
				'version'		=> 'pro'
			),
		'delta' => 	array(
				'name'			=> __( 'Highlight Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		'epsilon' => 	array(
				'name'			=> __( 'Banner Page', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostLoop' ) : array( 'PageLinesHighlight', 'PageLinesBanners', 'PageLinesContent' ),
				'version'		=> 'pro'
			),
		'posts' => array(
				'name'			=> __( 'Blog', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array('PageLinesPostsInfo','PageLinesPostLoop', 'PageLinesPagination') : array('PageLinesContent')
			),
		'single' => array(
				'name'			=> __( 'Blog Post', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array('PageLinesPostNav', 'PageLinesPostLoop', 'PageLinesShareBar', 'PageLinesComments', 'PageLinesPagination') : array('PageLinesContent')
			),
		'tag' => array(
				'name'			=> __( 'Tag', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostsInfo', 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'archive' => 	array(
				'name'			=> __( 'Archive', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostsInfo', 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'category' => 	array(
				'name'			=> __( 'Category', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostsInfo', 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'search' => 	array(
				'name'			=> __( 'Search', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostsInfo', 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'author' => 	array(
				'name'			=> __( 'Author', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( 'PageLinesPostsInfo', 'PageLinesPostLoop' ) : array('PageLinesContent'),
				'version'		=> 'pro'
			),
		'404_page' => 	array(
				'name'			=> __( '404 Error', 'pagelines' ),
				'sections' 		=> ($t == 'main') ? array( ) : array('PageLinesNoPosts'),
				'version'		=> 'pro'
			),
		
		
	);
	
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
	$pts = get_post_types( array( 'publicly_queryable' => true ) );

	
	if(isset($pts['page']))
		unset($pts['page']);
	
	if(isset($pts['post']))
		unset($pts['post']);
	
	if(isset($pts['attachment']))
		unset($pts['attachment']);
	

	$post_type_array = array();
	
	foreach( $pts as $public_post_type ){
		
		$dragdrop = apply_filters('pl_cpt_dragdrop', true, $public_post_type, $area);
		
		if( $dragdrop ){
		
			$post_type_data = get_post_type_object( $public_post_type );

			$sections = ( $area == 'templates' ) ? 'PageLinesContent' : 'PageLinesPostLoop';
	
			$sections_array = apply_filters( 'pl_default_sections', array( $sections ), $area, $public_post_type );
	
			$cpt_plural = strtolower(get_post_type_plural( $public_post_type ));
	
			$post_type_array[ $cpt_plural ] = array(
				'name'		=> ui_key($cpt_plural), 
				'sections'	=> $sections_array
			);
		
			$cpt_single = strtolower($public_post_type);
			$post_type_array[ $cpt_single ] = array(
				'name'		=> ui_key($cpt_single), 
				'sections'	=> $sections_array
			);
			
		}
		
		
	}
	
	return $post_type_array;
	
}