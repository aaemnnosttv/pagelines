<?php

class ExtensionThemes extends PageLinesExtensions {
	
	/**
	 * Themes tab.
	 * 
	 */
	function extension_themes( $tab = '' ) {

		$type = 'theme';
		$list = array();
		
		$themes = $this->get_latest_cached( 'themes' );

		if ( !is_object($themes) ) 
			return $themes;

		$themes = self::extension_scan_themes( $themes );

		foreach( $themes as $key => $ext ) {

			$check_file = sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, $key );
			
			if ( file_exists( $check_file ) )
				$exists = true;

			if( !$this->show_in_tab( $type, $key, $ext, $tab ) )
				continue;

			$list[$key] = $this->master_list( $type, $key, $ext, $tab );

		}
		return $this->ui->extension_list( array( 'list' => $list, 'tab' => $tab, 'type' => 'themes', 'mode' => 'graphic' ) );
	}
	
	/**
	 * Scan for themes and combine api with installed.
	 * 
	 */	
	function extension_scan_themes( $themes ) {
		
		$themes = json_decode(json_encode($themes), true);
		
		$get_themes = get_themes();

		foreach( $get_themes as $theme => $theme_data ) {
			$up = null;
			
			if ( $theme_data['Template'] != 'pagelines' )
				continue;
				
			if ( 'pagelines' == $theme_data['Stylesheet'] )
				continue;
			
			// check for an update...	
			if ( isset( $themes[ $theme_data['Stylesheet'] ]['version'] ) && $themes[ $theme_data['Stylesheet'] ]['version'] > $theme_data['Version']) 			
				$up = $themes[ $theme_data['Stylesheet'] ]['version'];
			else
				$up = '';
			
			if ( in_array( $theme, $themes ) )
				continue;
			// If we got this far, theme is a pagelines child theme not handled by the API
			// So we need to inject it into our themes array.
			
			$new_theme = array();
			$new_theme['name'] =		$theme_data['Name'];
			$new_theme['author'] =		$theme_data['Author Name'];
			$new_theme['author_url'] =	$theme_data['Author URI'];
			$new_theme['apiversion'] =	$up;			
			$new_theme['version'] =		$theme_data['Version'];
			$new_theme['text'] =		$theme_data['Description'];
			$new_theme['tags'] =		$theme_data['Tags'];
			$new_theme['featured']	=	( isset( $themes[$theme_data['Stylesheet']]['featured'] ) ) ? $themes[$theme_data['Stylesheet']]['featured'] : null;
			$new_theme['price']		= 	( isset( $themes[$theme_data['Stylesheet']]['price'] ) ) ? $themes[$theme_data['Stylesheet']]['price'] : null;
			$new_theme['productid'] = 	null;
			$new_theme['count'] = 		null;
			$themes[$theme_data['Stylesheet']] = $new_theme;		
		}
		return $themes;
	}
	
}