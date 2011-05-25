<?php 
/*
	
	SIDEBAR (SIDEBAR WRAP)
	
	This file controls the sidebar wrap template; which depending on the mode container one or both sidebars from layout.
	(It is used by BuddyPress as well and should play nice with child themes.)
	
	This theme copyright (C) 2008-2010 PageLines
	
*/ 
pagelines_register_hook('pagelines_before_sidebar_wrap'); // hook
global $pagelines_layout;
	
if($pagelines_layout->layout_mode != 'fullwidth'):?>

	<div id="sidebar-wrap" class="fix">
<?php 
			if(pagelines_option('sidebar_wrap_widgets') == 'top' || !pagelines_option('sidebar_wrap_widgets')){
				pagelines_template_area('pagelines_sidebar_wrap', 'sidebar_wrap'); // hook
			}
		
		if($pagelines_layout->layout_mode != 'two-sidebar-center'):?>
			<div id="sidebar1" class="scolumn fix" >
				<div class="scolumn-pad">
					<?php pagelines_template_area('pagelines_sidebar1', 'sidebar1'); // hook ?>	
				</div>
			</div>
		<?php endif;
		
		if($pagelines_layout->num_columns == 3): ?>
			<div id="sidebar2" class="scolumn fix">
				<div class="scolumn-pad">
					<?php pagelines_template_area('pagelines_sidebar2', 'sidebar2'); // hook ?>
				</div>
			</div>
<?php 	endif;

			if(pagelines_option('sidebar_wrap_widgets') == 'bottom'){
				pagelines_template_area('pagelines_sidebar_wrap', 'sidebar_wrap'); // hook
			}
			
		?>
	</div>		
<?php 
endif;

pagelines_register_hook('pagelines_after_sidebar_wrap'); // hook