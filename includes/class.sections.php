<?php
/**
 * 
 *
 *  API for creating and using PageLines sections
 *
 *
 *  @package PageLines Core
 *  @subpackage Sections
 *  @since 4.0
 *
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
	 *
	 */
	function __construct( $settings = array() ) {
	
		global $load_sections;
		
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
		
		$this->available = $load_sections->pagelines_register_sections( false, true );
		
		$this->class_name = get_class($this);
	
		$this->set_section_info();
		
	}
	
	function set_section_info(){
		
		$type = $this->section_install_type();

		$this->sinfo = $this->available[$type][$this->class_name];

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
	
		$this->screenshot = $this->settings['screenshot'] = ( file_exists( sprintf( '%s/screenshot.png', $this->base_dir ) ) ) ? sprintf( '%s/screenshot.png', $this->base_url ) : PL_ADMIN_IMAGES . '/screenshot-default.png';

		$this->optionator_default = array(
			'clone_id'	=> 1,
			'active'	=> true
		);
		
	}
	
	function section_install_type(){
		
		if ( isset( $this->available['custom'][$this->class_name] ) )
			return 'custom';		
		
		if ( isset( $this->available['child'][$this->class_name] ) )
			return 'child';

		if ( isset( $this->available['parent'][$this->class_name] ) )
			return 'parent';
	}

	/** 
	 * Echo the section content.
	 * Subclasses should over-ride this function to generate their section code.
	 */
	function section_template() {
		die('function PageLinesSection::section_template() must be over-ridden in a sub-class.');
	}
	
	/** 
	 * For template code that should show before the standard section markup
	 */
	function before_section_template( $clone_id = null ){}
	
	/** 
	 * For template code that should show after the standard section markup
	 */
	function after_section_template( $clone_id = null ){}
	
	/** 
	 * Checks for overrides and loads section template function
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

	function before_section( $markup = 'content', $clone_id = null){
		
		$clone_class = (isset($clone_id)) ? 'clone_'.$clone_id : '';
		
		if(isset($this->settings['markup']))
			$set_markup = $this->settings['markup'];
		else 
			$set_markup = $markup;	
		
		pagelines_register_hook('pagelines_before_'.$this->id, $this->id);
		
		if( $set_markup == 'copy' ) 
			printf('<section id="%s" class="copy fix %s"><div class="copy-pad">', $this->id, $clone_class);
		elseif( $set_markup == 'content' )
			printf('<section id="%s" class="container fix %s"><div class="texture"><div class="content"><div class="content-pad">', $this->id, $clone_class);

		pagelines_register_hook('pagelines_inside_top_'.$this->id, $this->id);
 	}

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

	function section_persistent(){}
	
	function section_admin(){}
	
	function section_head(){}
	
	function section_styles(){}
		
	function dynamic_style(){}
	
	function section_options(){}
		
	function section_optionator( $settings ){}
	
	function section_scripts(){}

	function getting_started(){}
		
	function add_getting_started( $tab_array ){
		
		$tab = array(
			'hide_getting_started_'.$this->id => array(
				'type' 			=> 'text_content',		
				'title'	 		=> 'Getting Started',
				'shortexp' 		=> 'How to use this section',
				'exp'			=> $this->getting_started(), 
				'inputlabel'	=> 'Hide This Overview'
			)
		);
		
		global $post_ID;
		
		if( !plmeta('hide_getting_started_'.$this->id, array('post_id' => $post_ID)) )
			$tab_array = array_merge($tab, $tab_array);
			
		return $tab_array;
		
	}

	function hook_get_view(){

		add_action('wp_head', array(&$this, 'get_view'), 10);
	}
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
	
	function hook_get_post_type(){
		
		add_action('wp_head', array(&$this, 'get_post_type'), 10);
	}
	
	function get_post_type(){
		global $pagelines_template;
	
		$this->template_type = $pagelines_template->template_type;
		
	}

}
/********** END OF SECTION CLASS  **********/

/**
 * Singleton that registers and instantiates PageLinesSection classes.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 1.0.0
 */
class PageLinesSectionFactory {
	var $sections  = array();
	var $unavailable_sections  = array();

	function __contruct() { }

	function register($section_class, $args) {
		
		if(class_exists($section_class))
			$this->sections[$section_class] = new $section_class( $args );
		
		/*
			Unregisters version-controlled sections
		*/
		if(!VPRO && $this->sections[$section_class]->settings['version'] == 'pro') {
			$this->unavailable_sections[] = $this->sections[$section_class];	
			$this->unregister($section_class);	
		}
	}

	function unregister($section_class) {
		if ( isset($this->sections[$section_class]) )
			unset($this->sections[$section_class]);
	}

}





/**
 * Runs the persistent PHP for sections.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 1.0.0
 */
function load_section_persistent(){
	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section)
		$section->section_persistent();

}

/**
 * Runs the admin PHP for sections.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 1.0.0
 */
function load_section_admin(){
	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section)
		$section->section_admin();

}


function load_section_options($optionset = null, $location = 'bottom', $load_unavailable_options = false){
	global $pl_section_factory;
	
	$load_options = array();
	
	foreach($pl_section_factory->sections as $section){
		$section_options = $section->section_options($optionset, $location);
		if(is_array($section_options)){
			$load_options = array_merge($load_options, $section_options);
		}
	}
	
	/*
		For Free Version
	*/
	if( $load_unavailable_options && is_array($pl_section_factory->unavailable_sections) ){
		foreach($pl_section_factory->unavailable_sections as $section){
			$section_options = $section->section_options($optionset, $location);
			if(is_array($section_options)){
				$load_options = array_merge($load_options, $section_options);
			}
		}
	}

	return $load_options;
}

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

function setup_section_notify( $section, $text, $url = null, $ltext = null){
	
	
	if(current_user_can('edit_themes')){
	
		$banner_title = sprintf('<h3 class="banner_title wicon" style="background-image: url(%s);">%s</h3>', $section->icon, $section->name);
		
		$url = (isset($url)) ? $url : pl_meta_set_url();
		$link_text = (isset($ltext)) ? $ltext : __('Set Meta', 'pagelines');
		
		$link = sprintf('<a href="%s">%s</a>', $url, $link_text . ' &rarr;');
		
		return sprintf('<div class="banner setup_area"><div class="banner_pad">%s <div class="banner_text subhead">%s<br/> %s</div></div></div>', $banner_title, $text, $link);
	}
	
}





