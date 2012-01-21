<?php


/* 
 * Admin Paths 
 */
class PLAdminPaths {
	
	
	
	static function account($vars = '', $hash = '#Your_Account'){
		
		return self::make_url('admin.php?page=pagelines_account', $vars, $hash);
		
	}
	
	function make_url( $string = '', $vars = '', $hash = '' ){
		
		return admin_url( $string.$vars.$hash );
		
	}	
}