<?php
/**
 * PageLinesSection
 *
 * API for creating and using PageLines sections
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       4.0
 */
class PageLinesSection {

	var $id;		// Root id for section.
	var $name;		// Name for this section.
	var $settings;	// Settings for this section
	var $base_dir;  // Directory for section
	var $base_url;  // Directory for section
	var $builder;  	// Show in section builder

    /**
     * PHP5 constructor
     * @param   array $settings
     */
	function __construct( $settings = array() ) {
	

		/**
         * Assign default values for the section
         * @var $defaults string
         */
		$defaults = array(
				'markup'			=> null,
				'workswith'		 	=> array('content'),
				'description' 		=> null, 
				'required'			=> null,
				'version'			=> 'all', 
				'base_url'			=> SECTION_ROOT,
				'dependence'		=> '', 
				'posttype'			=> '',
				'failswith'			=> array(), 
				'cloning'			=> false,
				'tax_id'			=> ''
			);

		$this->settings = wp_parse_args( $settings, $defaults );
		
		$this->hook_get_view();
		
		$this->hook_get_post_type();

		$this->class_name = get_class($this);
	
		$this->set_section_info();
		
	}

	/**
     * Set Section Info
     *
     * Read information from the section header; assigns values found, or sets general default values if not
     *
     * @since   ...
     *
     * @uses    pageliens_register_sections
     * @uses    section_install_type
     * @uses    PL_ADMIN_ICONS
     * @uses    PL_ADMIN_IMAGES
     */
	function set_section_info(){
		
		global $load_sections;
		$available = $load_sections->pagelines_register_sections( false, true );
		
		$type = $this->section_install_type( $available );

		global $load_sections;
		$available = $load_sections->pagelines_register_sections( false, true );
		$this->sinfo = $available[$type][$this->class_name];

		// File location information
		$this->base_dir = $this->settings['base_dir'] = $this->sinfo['base_dir'];
		$this->base_file = $this->settings['base_file'] = $this->sinfo['base_file'];
		$this->base_url = $this->settings['base_url'] = $this->sinfo['base_url'];
		
		$this->images = $this->base_url . '/images';

		// Reference information
		$this->id = $this->settings['id'] = basename( $this->base_dir );
		
		$this->name = $this->settings['name'] = $this->sinfo['name'];
		$this->description = $this->settings['description'] = $this->sinfo['description'];

		$this->settings['cloning'] = ( !empty( $this->sinfo['cloning'] ) ) ? $this->sinfo['cloning'] : $this->settings['cloning'];
		$this->settings['workswith'] = ( !empty( $this->sinfo['workswith'] ) ) ? $this->sinfo['workswith'] : $this->settings['workswith'];
		$this->settings['version'] = ( !empty( $this->sinfo['edition'] ) ) ? $this->sinfo['edition'] : $this->settings['version'];
		$this->settings['failswith'] = ( !empty( $this->sinfo['failswith'] ) ) ? $this->sinfo['failswith'] : $this->settings['failswith'];
		$this->settings['tax_id'] = ( !empty( $this->sinfo['tax'] ) ) ? $this->sinfo['tax'] : $this->settings['tax_id'];
		$this->settings['p_ver'] = $this->sinfo['version'];

		$this->icon = $this->settings['icon'] = ( file_exists( sprintf( '%s/icon.png', $this->base_dir ) ) ) ? sprintf( '%s/icon.png', $this->base_url ) : PL_ADMIN_ICONS . '/leaf.png';
	
		$this->screenshot = $this->settings['screenshot'] = ( file_exists( sprintf( '%s/thumb.png', $this->base_dir ) ) ) ? sprintf( '%s/thumb.png', $this->base_url ) : PL_ADMIN_IMAGES . '/thumb-default.png';

		$this->optionator_default = array(
			'clone_id'	=> 1,
			'active'	=> true
		);
		load_plugin_textdomain($this->id, false, sprintf( 'pagelines-sections/%s/lang', $this->id ) );	
	}


    /**
     * Section Install Type
     *
     * @since   ...
     *
     * @param   $available string
     *
     * @return  string
     */
	function section_install_type( $available ){
		
		if ( isset( $available['custom'][$this->class_name] ) )
			return 'custom';		
		elseif ( isset( $available['child'][$this->class_name] ) )
			return 'child';
		elseif ( isset( $available['parent'][$this->class_name] ) )
			return 'parent';
		else {
			
			/** 
			 * We dont know the type, could be a 3rd party plugin.
			 */
			$results = array_search_ext($available, $this->class_name, true);
			if ( is_array( $results ) && isset( $results[0]['keys']))
				return $results[0]['keys'][0];
		}
			
	}

    /**
     * Section Template
     *
     * The 'section_template()' function is the most important section function.
     * Use this function to output all the HTML for the section on pages/locations where it's placed.
     *
     * Subclasses should over-ride this function to generate their section code.
     *
     * @since   ...
     */
	function section_template() {
		die('function PageLinesSection::section_template() must be over-ridden in a sub-class.');
	}

    /**
     * Before Section Template
     *
     * For template code that should show before the standard section markup
     *
     * @since   ...
     *
     * @param   null $clone_id
     */
	function before_section_template( $clone_id = null ){}

    /**
     * After Section Template
     *
     * For template code that should show after the standard section markup
     *
     * @since   ...
     *
     * @param   null $clone_id
     */
	function after_section_template( $clone_id = null ){}

    /**
     * Section Template Load
     *
     * Checks for overrides and loads section template function
     *
     * @since   ...
     *
     * @param   $clone_id
     *
     * @uses    section_template
     */
	function section_template_load( $clone_id ) {
		
		// Variables for override
		$override_template = 'template.' . $this->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false) require( $override );
		else{
			$this->section_template( $clone_id );
		}
		
	}


    /**
     * Before Section
     *
     * Starts general section wrapper classes content and content-pad; adds class to uniquely identify clones
     * Dynamically creates unique hooks for section: pagelines_before_, pagelines_outer_, and pagelines_inside_top_
     *
     * @since       ...
     *
     * @param       string $markup
     * @param       null $clone_id
     * @param       string $classes
     *
     * @internal    param string $conjugation
     *
     * @uses        pagelines_register_hook
     */
	function before_section( $markup = 'content', $clone_id = null, $classes = ''){
		
		$classes .= (isset($clone_id)) ? ' clone_'.$clone_id : '';
		
		if(isset($this->settings['markup']))
			$set_markup = $this->settings['markup'];
		else 
			$set_markup = $markup;	
		
		pagelines_register_hook('pagelines_before_'.$this->id, $this->id); // hook
		
		if ( 'comments' == $this->id )
			$section_id = 'wp-comments';
		if ( 'content' == $this->id )
			$section_id = 'content-area';
		else
			$section_id = $this->id;
		
		if( $set_markup == 'copy' ) 
			printf('<section id="%s" class="copy %s %s"><div class="copy-pad">', $section_id, 's-'.$section_id, $classes);
		elseif( $set_markup == 'content' ){
			printf('<section id="%s" class="container %s %s fix"><div class="texture">', $this->id, 's-'.$section_id, $classes);
			pagelines_register_hook('pagelines_outer_'.$this->id, $this->id); // hook
			printf('<div class="content"><div class="content-pad">');
		}
		
		pagelines_register_hook('pagelines_inside_top_'.$this->id, $this->id); // hook 
 	}


    /**
     * After Section
     *
     * Closes CSS containers opened by before_section()
     * Dynamically creates unique hooks: pagelines_inside_bottom_, and pagelines_after_ with matching ids to the dynamically created hooks made in before_section()
     *
     * @since   ...
     *
     * @param   string $markup
     *
     * @uses    pagelines_register_hook
     */
	function after_section( $markup = 'content' ){
		if(isset($this->settings['markup']))
			$set_markup = $this->settings['markup'];
		else
			$set_markup = $markup;	
		
		pagelines_register_hook('pagelines_inside_bottom_'.$this->id, $this->id);
	 	
		if( $set_markup == 'copy' )
			printf('<div class="clear"></div></div></section>');
		elseif( $set_markup == 'content' )
			printf('<div class="clear"></div></div></div></div></section>');
			
		pagelines_register_hook('pagelines_after_'.$this->id, $this->id);
	}


    /**
     * Section Persistent
     *
     * Use this function to add code that will run on every page in your site & admin
     * Code here will run ALL the time, and is useful for adding post types, options etc.
     *
     * @since   ...
     */
	function section_persistent(){}
	

    /**
     * Section Init
     *
     * @since   ...
     *
     * @TODO document
     */
	function section_init(){}
	

    /**
     * Section Admin
     *
     * @since   ...
     * @TODO document
     */
	function section_admin(){}
	

    /**
     * Section Head
     *
     * Code added in this function will be run during the <head> element of the
     * site's 'front-end' pages. Use this to add custom Javascript, or manually
     * add scripts and meta information. It will *only* be loaded if the section
     * is present on the page template.
     *
     * @since   ...
     */
	function section_head(){}
	

    /**
     * Section Styles
     *
     * @since   ...
     * @TODO document
     */
	function section_styles(){}
	

    /**
     * Section Options
     *
     * @since   ...
     * @TODO document
     */
	function section_options(){}

    /**
     * Section Optionator
     *
     * Handles section options
     *
     * @since   ...
     *
     * @param   $settings
     */
	function section_optionator( $settings ){}
	

    /**
     * Section Scripts
     *
     * @since   ...
     * @TODO document
     */
    function section_scripts(){}


    /**
     * Getting Started
     *
     * @since   ...
     * @TODO document
     */
	function getting_started(){}


    /**
     * Add Guide
     *
     * Use to add a user's guide for the section
     *
     * @since   ...
     *
     * @param   $options
     *
     * @uses    ploption
     * @uses    plupop
     * @uses    PAGELINES_SPECIAL
     *
     * @return  array
     * @TODO document
     */
	function add_guide( $options ){
		
		
		if( file_exists( $this->base_dir . '/guide.php' ) ){
			
			ob_start();
				include( $this->base_dir . '/guide.php' );
			$guide = ob_get_clean();
			
			$key = sprintf('hide_guide_%s', $this->id);
			
			$opt = array(
				$key => array(
					'type' 			=> 'text_content',		
					'title'	 		=> __( 'Getting Started', 'pagelines' ),
					'shortexp' 		=> __( 'How to use this section', 'pagelines' ),
					'exp'			=> $guide, 
					'inputlabel'	=> __( 'Hide This Overview', 'pagelines')
				)
			);
			
			
			// Has this been hidden?
				
		
				$special_oset = array('setting' => PAGELINES_SPECIAL);
		
				$global_option = (bool) ploption( $key );
				$special_option = (bool) ploption($key, $special_oset );
			
			//	var_dump( $special_option );
					
				if( $global_option && $special_option ){
					$hide = true;
					
				}elseif( $special_option && !$global_option){
			
					plupop($key, true);
	
					$hide = true;
			
				}elseif( !$special_option && $global_option) {
					
					plupop($key, false);
	
					$hide = false;
					
				}else 
					$hide = false;

			if( !$hide )
				$options = array_merge($opt, $options);
			else {
			
				$opt = array(
					$key => array(
						'type' 			=> 'text_content_reverse',
						'inputlabel'	=> __( 'Hide Section Guide', 'pagelines' )
					)
				);
				
				$options = array_merge( $options, $opt);
			}
		
		}
		
		return $options;
		
		
	}	
	
	// Deprecated

    /**
     * Add Getting Started
     *
     * @since   ...
     *
     * @param   $tab_array
     *
     * @return  array
     * @TODO document
     */
	function add_getting_started( $tab_array ){
		
		return $this->add_guide($tab_array);
		
	}


    /**
     * Hook Get View
     *
     * @since   ...
     *
     * @TODO document
     */
	function hook_get_view(){

		add_action('wp_head', array(&$this, 'get_view'), 10);
	}

	/**
     * Get View
     *
     * @since   ...
     * @TODO document
     */
	function get_view(){
		
		if(is_single())
			$view = 'single';
		elseif(is_archive())
			$view = 'archive';
		elseif( is_page_template() )
			$view = 'page';
		else
			$view = 'default';
		
		$this->view = $view;
	}
	

    /**
     * Hook Get Post Type
     *
     * @since   ...
     * @TODO document
     */
	function hook_get_post_type(){
		
		add_action('wp_head', array(&$this, 'get_post_type'), 10);
	}
	

    /**
     * Get Post Type
     *
     * @since   ...
     * @TODO document
     */
	function get_post_type(){
		global $pagelines_template;
	
		$this->template_type = $pagelines_template->template_type;
		
	}


    /**
     * Runs before any html loads, but in the page.
     *
     * @package     PageLines Framework
     * @subpackage  Sections
     * @since       1.0.0
     *
     * @param       $clone_id
     */
	function setup_oset( $clone_id ){
		
		global $pagelines_ID;
		
		
		// Setup common option configuration, considering clones and page ids
		$this->oset = array('post_id' => $pagelines_ID, 'clone_id' => $clone_id);

	}



}
/********** END OF SECTION CLASS  **********/

/**
 * PageLines Section Factory (class)
 *
 * Singleton that registers and instantiates PageLinesSection classes.
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       1.0.0
 */
class PageLinesSectionFactory {
	var $sections  = array();
	var $unavailable_sections  = array();


	/**
     * Constructor
     *
     * @TODO document
     */
	function __contruct() { }


    /**
     * Register
     *
     * @since   ...
     *
     * @param   $section_class
     * @param   $args
     *
     * @TODO document
     */
	function register($section_class, $args) {
		
		if(class_exists($section_class))
			$this->sections[$section_class] = new $section_class( $args );
		
		/** Unregisters version-controlled sections */
		if(!VPRO && $this->sections[$section_class]->settings['version'] == 'pro') {
			$this->unavailable_sections[] = $this->sections[$section_class];	
			$this->unregister($section_class);	
		}
	}


    /**
     * Unregister
     *
     * @since   ...
     *
     * @param   $section_class
     * @TODO document
     */
	function unregister($section_class) {
		if ( isset($this->sections[$section_class]) )
			unset($this->sections[$section_class]);
	}

}

/**
 * Load Section Persistent
 *
 * Runs the persistent PHP for sections.
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       1.0.0
 *
 * @uses        section_persistent
 */
function load_section_persistent(){
	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section)
		$section->section_persistent();
			

}

/**
 * Load Section Admin
 *
 * Runs the admin PHP for sections.
 *
 * @package     PageLines Framework
 * @subpackage  Sections
 * @since       1.0.0
 *
 * @uses        section_admin
 */
function load_section_admin(){

	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section)
		$section->section_admin();

}

/**
 * Get Unavailable Section Areas
 *
 * @since   ...
 *
 * @return array
 * @TODO document
 */
function get_unavailable_section_areas(){
	
	$unavailable_section_areas = array();
	
	foreach(the_template_map() as $top_section_area){
		
		if(isset($top_section_area['version']) && $top_section_area['version'] == 'pro') $unavailable_section_areas[] = $top_section_area['name'];
		
		if(isset($top_section_area['templates'])){
			foreach ($top_section_area['templates'] as $section_area_template){
				if(isset($section_area_template['version']) && $section_area_template['version'] == 'pro') $unavailable_section_areas[] = $section_area_template['name'];
			}
		}
		
	}
	
	return $unavailable_section_areas;
	
}

/**
 * Setup Section Notify
 *
 * @since   ...
 *
 * @param   $section
 * @param   $text
 * @param   null $url
 * @param   null $ltext
 * @param   null $tab
 *
 * @return  string
 */
function setup_section_notify( $section, $text, $url = null, $ltext = null, $tab = null){
	
	
	if(current_user_can('edit_themes')){
	
		$banner_title = sprintf('<h3 class="banner_title wicon" style="background-image: url(%s);">%s</h3>', $section->icon, $section->name);
		
		$tab = ( !isset( $tab) && isset($section->tabID)) ? $section->tabID : $tab;
		
		$url = (isset($url)) ? $url : pl_meta_set_url( $tab );
		
		$link_text = (isset($ltext)) ? $ltext : __('Set Meta', 'pagelines');
		
		$link = sprintf('<a href="%s">%s</a>', $url, $link_text . ' &rarr;');
		
		return sprintf('<div class="banner setup_area"><div class="banner_pad">%s <div class="banner_text subhead">%s<br/> %s</div></div></div>', $banner_title, $text, $link);
	}
	
}

/**
 * Splice Section Slug
 *
 * @param   $slug
 *
 * @return  array
 * @TODO document
 */
function splice_section_slug( $slug ){
	
	$pieces = explode('ID', $slug);		
	$section = (string) $pieces[0];
	$clone_id = (isset($pieces[1])) ? $pieces[1] : null;
	
	return array('section' => $section, 'clone_id' => $clone_id);
}