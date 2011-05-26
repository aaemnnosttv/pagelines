<?php 
/*
	
	THE LOOP (Posts, Single Post Content, and Page Content)
	
	This file contains the WordPress "loop" which controls the content in your pages & posts. 
	You can control what shows up where using WordPress and PageLines PHP conditionals
	
	This theme copyright (C) 2008-2010 PageLines
	
*/

$theposts = new PageLinesPosts();
$theposts->load_loop();