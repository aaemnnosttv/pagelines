<?php

class ExtensionIntegrations extends PageLinesExtensions {
	
	/**
	 * Integrations tab.
	 * 
	 */
	function extension_integrations( $tab = '' ) {

		$integrations = $this->get_latest_cached( 'integrations' );

		if ( !is_object($integrations) ) 
			return $integrations;
		$integrations = json_decode(json_encode($integrations), true); // convert objects to arrays	

		$type = 'integration';

		foreach( $integrations as $key => $ext ) {
		
			$list[$key] = $this->master_list( $type, $key, $ext, $tab );		
		}
		return $this->ui->extension_list( $list, 'download' );
	}
}