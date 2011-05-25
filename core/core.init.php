<?php
// This file is deprecated, but still called from child themes.  Will remove completely in 2.0

// add_filter('pagelines_admin_notifications', 'get_functions_instead');
// 
// function get_functions_instead($notifications){
// 	
// 	$note = array();
// 
// 	$note['func']['title'] = 'Requiring a deprecated file!';
// 	$note['func']['text'] = "In your child theme file: functions.php, please replace your call to 'core/core.init.php' with a call to 'functions.php' in the parent";
// 	
// 	return array_merge($notifications, $note);
// }

// Used in Base < version 1.3.3
include( TEMPLATEPATH . '/functions.php');
