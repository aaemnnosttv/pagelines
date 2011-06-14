<?php 
/**
 * Controls and Manages PageLines Extension
 *
 * 
 *
 * @author		Simon Prosser
 * @copyright	2011 PageLines
 */


class PageLinesExtension{
	
	function __contruct(){
	}


	/**
	 *  Scans THEMEDIR/sections recursively for section files and auto loads them.
	 *  Child section folder also scanned if found and dependencies resolved.
	 *
	 *  Section files MUST include a class header and optional depends header.
	 *
	 *  Example section header:
	 *
	 *	Section: BrandNav Section
	 *	Author: PageLines
	 *	Description: Branding and Nav Inline
	 *	Version: 1.0.0
	 *	Class Name: BrandNav
	 *	Depends: PageLinesNav
	 *
	 *  @package Platform
	 *  @subpackage Config
	 *  @since 2.0
	 *
	 */
	function pagelines_register_sections(){


		// Simplify compicated variables


		$section_dirs = array( 
			'child' => STYLESHEETPATH . '/sections/',	
			'parent' => PL_SECTIONS

			);

		// check for cached array	
		if ( !$sections = get_transient( 'pagelines_sections' ) ) {
			foreach ( apply_filters( 'pagelines_sections_dirs', $section_dirs) as $type => $dir ) {
				$sections[$type] = $this->pagelines_getsections( $dir, $type );
				set_transient( 'pagelines_sections', $sections, apply_filters( 'pagelines_section_cache_timeout', 120 ) );
			}
		}

		// main array containing child and parent sections
		$sections = apply_filters( 'pagelines_section_admin', $sections );

		foreach ( $sections as $type ) {

			foreach( $type as $section ) {
				if ($section['depends'] != '') { // do we have a dependency?
					if (isset( $sections['parent'][$section['depends']]['class']) && file_exists( $sections['parent'][$section['depends']]['filename'] ) ) {
						pagelines_register_section( $sections['parent'][$section['depends']]['class'], $sections['parent'][$section['depends']]['folder'], $sections['parent'][$section['depends']]['filename'] );	
					}
				} else {
					if ( $section['type'] == 'child') {
						pagelines_register_section( $section['class'], $section['filename'], null, array('child' => true ) );
					} else {
						pagelines_register_section( $section['class'], $section['folder'], $section['filename'] );
					}
				}
			}
		}
		pagelines_register_hook('pagelines_register_sections'); // Hook
	}		
	/**
	 * Helper function 
	 * Returns array of section files.
	 * @return array of php files
	 * @author Simon Prosser
	 **/
	function pagelines_getsections( $dir, $type ) {

		if ( is_child_theme() == false && $type == 'child' || ! is_dir($dir) ) return;

		$sections = array();
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $dir, RecursiveIteratorIterator::LEAVES_ONLY));

		foreach( $it as $fullFileName => $fileSPLObject ) {
			if (pathinfo($fileSPLObject->getFilename(), PATHINFO_EXTENSION ) == 'php') {
				$folder = ( preg_match( '/sections\/(.*)\//', $fullFileName, $match) ) ? $match[1] : '';
				$headers = get_file_data( $fullFileName, $default_headers = array( 'classname' => 'Class Name', 'depends' => 'Depends' ) );
				$filename = str_replace( '.php', '', str_replace( 'section.', '', $fileSPLObject->getFilename() ) );
				$sections[$headers['classname']] = array(
					'filename' => $filename,
					'folder' => $folder,
					'class' => $headers['classname'],
					'depends' => $headers['depends'],
					'type' => $type
				);	
			}
		}
		return $sections;	
	}	
}
/*
	TODO 
		- make more readable
			- add inline docs
			- add whitespace
			- consolidate complicated variable names into simple ones
*/

// =================================================
// = TODO - FUNCTIONS FOR CONSILIDATION INTO CLASS =
// =================================================







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