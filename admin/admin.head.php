<?php 
	
	global $pl_foundry;
	$pl_foundry->setup_google_loaders();
	
	pagelines_register_hook( 'pagelines_admin_head' ); // Hook
?>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo PL_ADMIN_JS;?>/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="<?php echo PL_ADMIN_JS;?>/colorpicker/js/colorpicker.js"></script>
<script type="text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){ 
<?php 

/**
 * AJAX Saving of framework settings
 * 
 * @package AJAX
 * @since 1.2.0
 */
// Allow users to disable AJAX saving... 
if(!pagelines_option('disable_ajax_save')): ?>	
jQuery("#pagelines-settings-form").submit(function() {
	
	var ajaxAction = "<?php echo admin_url("admin-ajax.php"); ?>";
	
	formData = jQuery("#pagelines-settings-form");
	serializedData = jQuery(formData).serialize();
	
	if(jQuery("#input-full-submit").val() == 1){
		return true;
	} else {
		jQuery('.ajax-saved').center('#pagelines-settings-form');
		url = 'options.php';
		var saveText = jQuery('.ajax-saved .ajax-saved-pad .ajax-saved-icon');
		jQuery.ajax({
			type: 'POST',
			url: url,
			data: serializedData,
			beforeSend: function(){
				
				jQuery('.ajax-saved').removeClass('success').show().addClass('uploading');

				saveText.text('Saving'); // text while saving
				
				// add some dots while saving.
				interval = window.setInterval(function(){
					var text = saveText.text();
					if (text.length < 10){	saveText.text(text + '.'); }
					else { saveText.text('Saving'); } 
				}, 400);
				
			},
		  	success: function(data){
				window.clearInterval(interval); // clear dots...
				jQuery('.ajax-saved').removeClass('uploading').addClass('success');
				saveText.text('Settings Saved!'); // change button text, when user selects file	
				
				jQuery('.ajax-saved').show().delay(800).fadeOut('slow');
				
			}
		});
		return false;
	}
  
});

<?php endif;?>



<?php
/*
	Color Picker
*/
	foreach (get_option_array() as $menuitem):
		foreach($menuitem as $oid => $o):
			if($o['type'] == 'colorpicker'):
			?>setColorPicker('<?php echo $oid;?>', '<?php echo pagelines_option($oid);?>');<?php 
			echo "\n";
			elseif($o['type'] == 'color_multi'):				
				foreach($o['selectvalues'] as $sid => $s):
				?>setColorPicker('<?php echo $sid;?>', '<?php echo pagelines_option($sid);?>');<?php 
				echo "\n";
				endforeach;?>
<?php 		endif;
		endforeach;
	endforeach;

?> 
}); 
/*]]>*/</script>