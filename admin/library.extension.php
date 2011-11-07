<?php
/**
 * Upgrader skin and other functions.
 *
 * 
 * @author PageLines
 *
 * @since 2.0.b10
 */
class PageLines_Upgrader_Skin extends WP_Upgrader_Skin {

	function __construct( $args = array() ) {
		parent::__construct($args);
	}
	
	function header() { }
	
	function footer(){ }
	
	function feedback($string) {
		
		$string = str_replace( 'downloading_package', '', $string );
		$string = str_replace( 'unpack_package', '', $string );
		$string = str_replace( 'installing_package', '', $string );
		$string = str_replace( 'process_failed', '', $string );	
		$string = str_replace( 'process_success', '', $string );
		
		// if anything left, must be a fatal error!
		
		if ( $string )	{			
			if ( strstr( $string, 'Download failed' ) ) {
				_e( 'Could not connect to download.<br/><a href="#">Reload Page</a>', 'pagelines' );
				exit();
			}
			if ( strstr( $string, 'Destination folder already exists' ) ) {
				$string = str_replace( 'Destination folder already exists.', '', $string );
				printf( __('Destination folder already exists %s', 'pagelines' ), $string );
				exit;
				
			}
			
			// unmapped error...
		//	echo $string;
		}
	}
	
//	function error($error) {}
	
	function after() {}

	function before() {}
}

function extend_delete_directory($dirname){
    // check whether $dirname is a directory
    if  (is_dir($dirname))
        // change its mode to 755 (rwx,rw,rw)
        chmod($dirname, 0755);

    // open the directory, the script cannot open the directory then stop
    $dir_handle  =  opendir($dirname);
    if  (!$dir_handle)
        return  false;

    // traversal for every entry in the directory
    while (($file = readdir($dir_handle)) !== false){
        // ignore '.' and '..' directory
        if  ($file  !=  "."  &&  $file  !=  "..")  {

            // if entry is directory then go recursive !
            if  (is_dir($dirname."/".$file)){
                      extend_delete_directory($dirname.'/'.$file);

            // if file then delete this entry
            } else {
                  unlink($dirname."/".$file);
            }
        }
    }
    // chose the directory
    closedir($dir_handle);

    // delete directory
    rmdir($dirname);
}