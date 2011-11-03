<?php

class ExtensionIntegrations extends PageLinesExtensions {
	
	/**
	 * Integrations tab.
	 * 
	 */
	function extension_integrations( $tab = '' ) {
		
		$type = 'integration';
	
		$integrations = $this->get_latest_cached( 'integrations' );

		if ( !is_object($integrations) ) 
			return $integrations;

		$integrations = json_decode(json_encode($integrations), true); // convert objects to arrays	

		$list = $this->get_master_list( $integrations, $type, $tab );
		
		return $this->ui->extension_list( array( 'list' => $list, 'tab' => $tab, 'type' => 'integrations', 'mode' => 'download' ) );
	}
}