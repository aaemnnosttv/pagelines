<?php 
/*
	
	HEADER
	
	This file controls the HTML <head> and top graphical markup (including Navigation) for each page in your theme.
	You can control what shows up where using WordPress and PageLines PHP conditionals
	
	This theme copyright (C) 2008-2010 PageLines
	
*/ 	
	pagelines_register_hook('pagelines_before_html'); // Hook 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php 
		
		pagelines_register_hook('pagelines_head'); // Hook 
		
		wp_head(); // Hook (WordPress) 

?></head>
<body <?php body_class( pagelines_body_classes() ); ?>>
<?php 

	print_pagelines_option('asynch_analytics');  // Recommended Spot For Asynchronous Google Analytics
	pagelines_register_hook('pagelines_before_site'); // Hook 

?><div id="site" class="<?php echo pagelines_layout_mode();?>"> <!-- #site // Wraps #header, #page-main, #footer - closed in footer -->
<?php pagelines_register_hook('pagelines_before_page'); // Hook ?>
	<div id="page"> <!-- #page // Wraps #header, #page-main - closed in footer -->
		<div id="page-canvas">
			<?php pagelines_register_hook('pagelines_before_header');?>
			<header id="header" class="container-group fix">
				<div class="outline">
					<?php pagelines_template_area('pagelines_header', 'header'); // Hook ?>
				</div>
			</header>
			<?php pagelines_register_hook('pagelines_before_main'); // Hook ?>
			<div id="page-main" class="container-group fix"> <!-- #page-main // closed in footer -->
				<div id="dynamic-content" class="outline fix">
					<?php pagelines_integration_top();?>
					
				