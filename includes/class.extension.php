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
	
	function __contruct(){ }


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

		/*
			TODO 
				- this should be broken down into 2 functions
					- 1 $this->scan_directory();
					- 2 $this->register_files();
		*/
		global $pl_section_factory;
		$section_dirs = array( 
			'child' 	=> STYLESHEETPATH . '/sections/',	
			'parent' 	=> PL_SECTIONS
		);

		// check for cached array	
		if ( !$sections = get_transient( 'pagelines_sections' ) ) {
			foreach ( apply_filters( 'pagelines_sections_dirs', $section_dirs) as $type => $dir ) {
				
				// Recurse through directory, 
				/*
					TODO  define $type in documentation
				*/
				$sections[$type] = $this->pagelines_getsections( $dir, $type );
				
				// Set transient to prevent performance problems.
				// TODO switch this to activation/deactivation interface
				// TODO better idea, clear cached vars on settings save.
				set_transient( 'pagelines_sections', $sections, apply_filters( 'pagelines_section_cache_timeout', 1 ) );
				
			}
		}

		// main array containing child and parent sections
		$sections = apply_filters( 'pagelines_section_admin', $sections );

		/*
			TODO 
				- Simon review changes below for enhancements to readability (Pro Tip)
				- I got a non array warning on $type, fixed, but what's the issue?
		*/
		foreach ( $sections as $type ) {

			if(is_array($type)){
				foreach( $type as $section ) {
				
					// consolidate array vars
					$dep = ($section['depends'] != '') ? $section['depends'] : null;
					$parent_dep = (isset($sections['parent'][$section['depends']])) ? $sections['parent'][$section['depends']] : null;
				
					$dep_file = (isset($parent_dep['filename'])) ? $parent_dep['filename'] : null;
					$dep_class = (isset($parent_dep['class'])) ? $parent_dep['class'] : null;
					$dep_folder = (isset($parent_dep['folder'])) ? $parent_dep['folder'] : null;
					$args = array(
					'base_dir' => $section['base_dir'],
					'base_url' => $section['base_url'],
					'base_file' => $section['base_file']	
					);
					if (isset($dep)) { // do we have a dependency?
						if (isset( $dep_class ) && file_exists( $dep_file ) ) 
							pagelines_register_section( $dep_class, $dep_folder, $dep_file ); 
					
					} else {
							if ( !class_exists( $section['class'] ) ) {
								include( $section['base_file'] );
								$pl_section_factory->register($section['class'], $args);
							}
					}
				}
			}
		}
		pagelines_register_hook('pagelines_register_sections'); // Hook
	}		
	/**
	 * 
	 * Helper function 
	 * Returns array of section files.
	 * @return array of php files
	 * @author Simon Prosser
	 **/
	function pagelines_getsections( $dir, $type ) {

		if ( is_child_theme() == false && $type == 'child' || ! is_dir($dir) ) 
			return;

		$sections = array();
		$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator( $dir, RecursiveIteratorIterator::LEAVES_ONLY));

		foreach( $it as $fullFileName => $fileSPLObject ) {
			if (pathinfo($fileSPLObject->getFilename(), PATHINFO_EXTENSION ) == 'php') {
				$folder = ( preg_match( '/sections\/(.*)\//', $fullFileName, $match) ) ? '/' . $match[1] : '';
				$headers = get_file_data( $fullFileName, $default_headers = array( 'classname' => 'Class Name', 'depends' => 'Depends' ) );
				if ( !$headers['classname'] )
					break;
				$filename = str_replace( '.php', '', str_replace( 'section.', '', $fileSPLObject->getFilename() ) );
				$sections[$headers['classname']] = array(
					'class' => $headers['classname'],
					'depends' => $headers['depends'],
					'type' => $type,
					'base_url' => ( $type == 'child' ) ? CHILD_URL . '/sections/' . $folder : SECTION_ROOT . $folder,
					'base_dir' => ( $type == 'child' ) ? CHILD_DIR . '/sections' . $folder : PL_SECTIONS . $folder,
					'base_file' => $fullFileName
				);	
			}
		}
		return $sections;	
	}
		
} // end class
