<?php
/*
	
	SEARCH FORM
	
	This file is the template for the theme searchform.
	
	This theme copyright (C) 2008-2010 PageLines
	
*/
?>
<form method="get" class="searchform" action="<?php echo home_url(); ?>/" onsubmit="this.submit();return false;">
	<fieldset>
		<input type="text" value="<?php _e('Search','pagelines');?>" name="s" class="searchfield" onfocus="if (this.value == '<?php _e('Search','pagelines');?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search','pagelines');?>';}" />

		<input type="image" class="submit btn" name="submit" src="<?php echo PL_IMAGES;?>/search-btn.png" alt="Go" />
	</fieldset>
</form>
