<?php 
/*
	
	FOOTER
	
	This file controls the ending HTML </body></html> and common graphical elements in your site footer.
	You can control what shows up where using WordPress and PageLines PHP conditionals
	
	This theme copyright (C) 2008-2010 PageLines
	
*/


if(!has_action('override_pagelines_body_output')): ?>
			</div>
			<?php pagelines_register_hook('pagelines_after_main'); // Hook ?>
			<div id="morefoot_area" class="container-group"><?php pagelines_template_area('pagelines_morefoot', 'morefoot'); // Hook ?></div>
			<div class="clear"></div>
		</div>
	</div>
</div>
<?php pagelines_register_hook('pagelines_before_footer'); // Hook ?>
<footer id="footer" class="container-group">
	<div class="outline fix"><?php 
		pagelines_template_area('pagelines_footer', 'footer'); // Hook 
		pagelines_register_hook('pagelines_after_footer'); // Hook
		pagelines_cred(); 
	?></div>
</footer>
</div>
<?php 

endif;

	print_pagelines_option('footerscripts'); // Load footer scripts option 	
	wp_footer(); // Hook (WordPress) 
?>
</body>
</html>