<?php
/**
 * Upgrader skin
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
	
	function feedback($string) {}
	
	function error($error) {}
	
	function after() {}

	function before() {}
}