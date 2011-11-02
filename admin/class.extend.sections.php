<?php

class ExtensionSections extends PageLinesExtensions {
	
	/**
	 * Section install tab.
	 * 
	 */
 	function extension_sections_install( $tab = '' ) {
 
		if ( !$this->has_extend_plugin() )
			return $this->ui->get_extend_plugin( $this->has_extend_plugin('status'), $tab );
		
 		$sections = $this->get_latest_cached( 'sections' );

		if ( !is_object( $sections ) ) 
			return $sections;

		$type = 'section';
		
		foreach( $sections as $key => $ext ) {
			
			$ext = (array) $ext;
			
			if ( !isset( $ext['type']) )
				$ext['type'] = 'internal';
			
			if( !$this->show_in_tab( 'section', $key, $ext, $tab ) )
				continue; 
		
			$list[$key] = $this->master_list( $type, $key, $ext, $tab );
			
		}
		
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'Available %1$s sections will appear here.', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}

	
	/*
	 * Installed sections tab.
	 */
 	function extension_sections( $tab = '' ) {

 		global $load_sections;

		if($tab == 'child' && !is_child_theme())
			return $this->ui->extension_banner( __( 'A PageLines child theme is not currently activated', 'pagelines' ) );

		// Get sections
 		$available = $load_sections->pagelines_register_sections( true, true );

 		$disabled = get_option( 'pagelines_sections_disabled', array() );

		$upgradable = $this->get_latest_cached( 'sections' );
		
		$type = 'section';
 		foreach( $available as $section ) {
	
			foreach( $section as $key => $ext)
				$section[$key]['status'] = ( isset( $disabled[ $ext['type'] ][ $ext['class'] ] ) ) ? 'disabled' : 'enabled';

			$section = pagelines_array_sort( $section, 'name' ); // Sort Alphabetically

 			foreach( $section as $key => $ext ) { // main loop


				if ( isset( $ext['base_dir'] ) ) {
					$upgrade = basename( $ext['base_dir'] );
					$ext['upgrade'] = ( isset( $upgradable->$upgrade->version ) ) ? $upgradable->$upgrade->version : '';
				}
				
				$ext['class_exists'] = ( isset( $available['child'][ $ext['class'] ] ) || isset( $available['custom'][ $ext['class'] ] ) ) ? true : false;
				
				if( !$this->show_in_tab( 'section', $key, $ext, $tab ) )
					continue;

					$list[$key] = $this->master_list( $type, $key, $ext, $tab );
			}
 		} 	
		if(empty($list))
			return $this->ui->extension_banner( sprintf ( __( 'Installed %1$s sections will appear here.', 'pagelines' ), $tab ) );
		else
			return $this->ui->extension_list( $list );
 	}
	
}