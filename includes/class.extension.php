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

		global $pl_section_factory;

		/**
		* Load our two main section folders
		* @filter pagelines_section_dirs
		*/
		$section_dirs = apply_filters( 'pagelines_sections_dirs', array( 
			'child' 	=> STYLESHEETPATH . '/sections/',	
			'parent' 	=> PL_SECTIONS
		) );

		/**
		* If cache exists load into $sections array
		* If not populate array and prime cache
		*/
		if ( !$sections = get_option( 'pagelines_sections_cache' ) ) {
			foreach ( $section_dirs as $type => $dir ) {
				$sections[$type] = $this->pagelines_getsections( $dir, $type );
			}

			/**
			* TODO switch this to activation/deactivation interface
			* TODO better idea, clear cached vars on settings save.
			*/
			update_option( 'pagelines_sections_cache', $sections );	
		}

		// filter main array containing child and parent and any custom sections
		$sections = apply_filters( 'pagelines_section_admin', $sections );
		$disabled = get_option( 'pagelines_sections_disabled' );
		foreach ( $sections as $type ) {

			if(is_array($type)){
				foreach( $type as $section ) {
					
					/**
					* using pagelines_section_admin filter we can disable sections from loading
					* by setting the disabled bit.
					*/

					if (isset( $disabled[$section['type']][$section['class']] ) )
						continue;
					
					// consolidate array vars
					$dep = ($section['depends'] != '') ? $section['depends'] : null;
					$parent_dep = (isset($sections['parent'][$section['depends']])) ? $sections['parent'][$section['depends']] : null;

					$dep_data = array(
						'base_dir'  => (isset($parent_dep['base_dir'])) ? $parent_dep['base_dir'] : null,
						'base_url'  => (isset($parent_dep['base_url'])) ? $parent_dep['base_url'] : null,
						'base_file' => (isset($parent_dep['base_file'])) ? $parent_dep['base_file'] : null
					);
					$section_data = array(
						'base_dir'  => $section['base_dir'],
						'base_url'  => $section['base_url'],
						'base_file' => $section['base_file']	
					);
					if ( isset( $dep ) ) { // do we have a dependency?
						if ( isset( $dep_class ) && !class_exists( $dep_class ) && file_exists( $dep_file ) ) {
							include( $section['dep_file'] );
							$pl_section_factory->register( $dep_class, $dep_data );
						}
					} else {
							if ( !class_exists( $section['class'] ) && file_exists( $section['base_file'] ) ) {
								include( $section['base_file'] );
								$pl_section_factory->register( $section['class'], $section_data );
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
				$headers = get_file_data( $fullFileName, $default_headers = array( 'author' => 'Author', 'authoruri' => 'Author URI', 'section' => 'Section', 'description' => 'Description', 'classname' => 'Class Name', 'depends' => 'Depends' ) );

				// If no pagelines class headers ignore this file.
				if ( !$headers['classname'] )
					break;

				$filename = str_replace( '.php', '', str_replace( 'section.', '', $fileSPLObject->getFilename() ) );
				$sections[$headers['classname']] = array(
					'class' => $headers['classname'],
					'depends' => $headers['depends'],
					'type' => $type,
					'author' => $headers['author'],
					'authoruri' => ( isset( $headers['authoruri'] ) ) ? $headers['authoruri'] : '',
					'description' => $headers['description'],
					'name' => $headers['section'],
					'base_url' => ( $type == 'child' ) ? CHILD_URL . '/sections/' . $folder : SECTION_ROOT . $folder,
					'base_dir' => ( $type == 'child' ) ? CHILD_DIR . '/sections' . $folder : PL_SECTIONS . $folder,
					'base_file' => $fullFileName
				);	
			}
		}
		return $sections;	
	}
		
} // end class