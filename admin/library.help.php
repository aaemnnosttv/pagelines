<?php
/**
 * PageLine Inline Help System.
 *
 * 
 * @author Simon Prosser
 *
 * @since 2.0.b6
 */

	function pagelines_inline_help( $contextual_help, $screen_id , $screen ) {

		switch( $screen_id ) {
			
			case 'pagelines_page_pagelines_extend':
				$contextual_help = 'This is the AWESOME extensions part, sit back, and be amazed!<br /><strong>YES</strong> html works here! <p><FONT SIZE="4" FACE="Comic Sans MS" COLOR=blue><MARQUEE BEHAVIOR=SCROLL>We need more Comic Sans!</MARQUEE></FONT></p>
				';
				break;
			
			default:
				break;
			
		}	
		
		return $contextual_help;
	}