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
	function __construct( $name = null, $id = null, $settings = array(), $base = null ) {
		
		$defaults = array(
				'markup'			=> null,
				'workswith'		 	=> array('content'),
				'description' 		=> null, 
				'required'			=> null,
				'version'			=> 'all', 
				'icon'				=> PL_ADMIN_ICONS . '/leaf.png',
				'base_dir'			=> PL_SECTIONS,
				'base_file'			=> PL_SECTIONS.'/section.'.$id.'.php',
				'base_url'			=> SECTION_ROOT,
				'dependence'		=> '', 
				'posttype'			=> '',
				'failswith'			=> array()
			);
		
		$this->settings = wp_parse_args( $settings, $defaults );
		
		// Reference information
		$this->id = empty($id) ? strtolower(get_class($this)) : strtolower($id);
		$this->name = $name;
		
		// File location information
		$this->base_dir = $this->settings['base_dir'];
		$this->base_file = $this->settings['base_file'];
		$this->base_url = $this->settings['base_url'];
		
		$this->icon = $this->settings['icon'];
	}

	/** Echo the section content.
	 *
	 * Subclasses should over-ride this function to generate their section code.
	 *
	 */
	function section_template() {
		die('function PageLinesSection::section_template() must be over-ridden in a sub-class.');
	}
	
	/** Checks for overrides and loads section template function
	 *
	 *
	 */
	function section_template_load() {
		// Variables for override
		$override_template = 'template.' . $this->id .'.php';
		$override = ( '' != locate_template(array( $override_template), false, false)) ? locate_template(array( $override_template )) : false;

		if( $override != false) require( $override );
		else{
			$this->section_template();
		}
		
	}

	function before_section( $markup = 'content' ){
		if(isset($this->settings['markup'])){
			$set_markup = $this->settings['markup'];
		} else {
			$set_markup = $markup;	
		}
		pagelines_register_hook('pagelines_before_'.$this->id, $this->id);
		
		if( $set_markup == 'copy' ):?>
<div id="<?php echo $this->id;?>" class="copy fix">
	<div class="copy-pad">
<?php 	elseif( $set_markup == 'content' ):?>
<div id="<?php echo $this->id;?>" class="container fix">
	<div class="texture">
		<div class="content">
			<div class="content-pad">
<?php 	endif;
		pagelines_register_hook('pagelines_inside_top_'.$this->id, $this->id);
 	}

	function after_section( $markup = 'content' ){
		if(isset($this->settings['markup'])){
			$set_markup = $this->settings['markup'];
		} else {
			$set_markup = $markup;	
		}
		pagelines_register_hook('pagelines_inside_bottom_'.$this->id, $this->id);
	 	
		if( $set_markup == 'copy' ):?>
	<div class="clear"></div>
	</div>
</div>
<?php 	elseif( $set_markup == 'content' ):?>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
<?php 	endif;
		pagelines_register_hook('pagelines_after_'.$this->id, $this->id);
	}

	function section_persistent(){}
	
	function section_admin(){}
	
	function section_head(){}
	
	function section_styles(){}
	
	function section_options(){}
	
	function section_scripts(){}


}
/********** END OF SECTION CLASS  **********/

/**
 * Singleton that registers and instantiates PageLinesSection classes.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 4.0
 */
class PageLinesSectionFactory {
	var $sections  = array();
	var $unavailable_sections  = array();

	function __contruct() { }

	function register($section_class, $args) {
		if(class_exists($section_class)){
			$this->sections[$section_class] = new $section_class( $args );
		}
		
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
 * Registers and loads the section files
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 4.0
 */
function pagelines_register_section($section_class, $section_folder, $init_file = null, $args = array()){
	

	global $pl_section_factory;

	if(isset($args['child']) && $args['child'] == true) $register_child_section = true; 
	else $register_child_section = false; 
	
	// Don't register class twice.
	if( class_exists ( $section_class )) return;
	
	// If the section depends on other sections
	if(isset($args['deps'])){
		
		if(is_array($args['deps'])){
			// Check to make sure it is registered
			foreach($args['deps'] as $parent_section){
				if(!isset($pl_section_factory->sections[$parent_section])) return;
			}
		} else {
			
			if(!isset($pl_section_factory->sections[ $args['deps'] ])) return;

		}
		
	}

	/*
		Refine & modify filename
	*/
	if(!isset($init_file) && !strpos($init_file, '.php')) $init_file = $section_folder.'.php';
	elseif(!strpos($init_file, '.php')) $init_file = $init_file.'.php';


	if($register_child_section){
		
		
		
		/*
		 	Set up possible paths to section
		 */
		$section_init_file_section = CHILD_DIR.'/sections/section.'.$init_file;
		$section_init_folder_section = CHILD_DIR.'/sections/'.$section_folder.'/section.'.$init_file;

		/*
			Include and set directory/location information
		*/
		if(file_exists($section_init_file_section)){

			include($section_init_file_section);
			$base_dir = CHILD_DIR.'/sections';
			$base_url = CHILD_URL.'/sections/'.$section_folder;
			$base_file = $section_init_file_section;

		}elseif(file_exists($section_init_folder_section)){

			include($section_init_folder_section);
			$base_dir = CHILD_DIR.'/sections/'.$section_folder;
			$base_url = CHILD_URL.'/sections/'.$section_folder;
			$base_file = $section_init_folder_section;

		}
	}else{
		 /*
		 	Set up possible paths to section
		 */
		$section_init_file_section = PL_SECTIONS.'/section.'.$init_file;
		$section_init_folder_section = PL_SECTIONS.'/'.$section_folder.'/section.'.$init_file;


		/*
			Include and set directory/location information
		*/
		if(file_exists($section_init_file_section)){

			include($section_init_file_section);
			$base_dir = PL_SECTIONS;
			$base_url = SECTION_ROOT.'/'.$section_folder;
			$base_file = $section_init_file_section;

		}elseif(file_exists($section_init_folder_section)){

			include($section_init_folder_section);
			$base_dir = PL_SECTIONS.'/'.$section_folder;
			$base_url = SECTION_ROOT.'/'.$section_folder;
			$base_file = $section_init_folder_section;

		}
	}

	
	if( isset($base_file) ){
		$args['base_dir'] = $base_dir;  	
		$args['base_url'] = $base_url;
		$args['base_file'] = $base_file;
	}

	/*
		Add to the section factory singleton for use as global
	*/
	$pl_section_factory->register($section_class, $args);	
}



/**
 * Runs the persistent PHP for sections.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 4.0
 */
function load_section_persistent(){
	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section){
		$section->section_persistent();
	}

}

/**
 * Runs the admin PHP for sections.
 *
 * @package PageLines Core
 * @subpackage Sections
 * @since 4.0
 */
function load_section_admin(){
	global $pl_section_factory;
	
	foreach($pl_section_factory->sections as $section){
		$section->section_admin();
	}

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



