<?php global $pl_foundry;
	$pl_foundry->setup_google_loaders();
?>
<link rel="stylesheet" media="screen" type="text/css" href="<?php echo PL_ADMIN_JS;?>/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="<?php echo PL_ADMIN_JS;?>/colorpicker/js/colorpicker.js"></script>

<script type="text/javascript">/*<![CDATA[*/
jQuery(document).ready(function(){
<?php 

/*
	AJAX Saving of framework settings
*/

// Allow users to disable AJAX saving... 
if(!pagelines_option('disable_ajax_save')):

?>	
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
					
					jQuery.ajax({
						type: 'GET',
						url: ajaxAction, 
						data: { action: 'pagelines_ajax_create_dynamic_css' },
					});
				}
			});
			return false;
		}
	  
	});
	
<?php endif;?>

// Slide up notifications and messages
jQuery('#message.slideup_message').delay(5000).slideUp('fast');

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

/*
	Section Drag-Drop & Saving
*/
global $pagelines_template; 

?>

	var stemplate = jQuery('#tselect').val();

	jQuery('.'+stemplate).addClass('selected_template');

	setSortable(stemplate);

	jQuery('#tselect').change(function() {
	
		stemplate = jQuery(this).val();
		jQuery('.selected_template').removeClass('selected_template');
		jQuery('.'+stemplate).addClass('selected_template');
		setSortable(stemplate);
	
	});

/*
	Layout Builder Control	
*/
	// Default Layout Select
	jQuery(' .layout-select-default .layout-image-border').click(function(){
		LayoutSelectControl(this);
	});
	
	<?php 
		$the_last_edited = (pagelines_sub_option('layout', 'last_edit')) ? pagelines_sub_option('layout', 'last_edit') : null;
		
		// Set up layout object for loaded page
		$load_layout = new PageLinesLayout($the_last_edited);
		
		// Values for loading layout
		$layout_mode = $load_layout->layout_mode;
		$load_margin = $load_layout->margin->bwidth;
		$load_west = $load_layout->west->bwidth;
		$load_east = $load_layout->east->bwidth;
		$load_gutter = $load_layout->gutter->bwidth;
		
	?>
	setLayoutBuilder('<?php echo $layout_mode; ?>', <?php echo $load_margin;?>, <?php echo $load_east;?>, <?php echo $load_west;?>, 10);

	jQuery('.selected_template .layout-builder-select .layout-image-border').click(function(){
		var LayoutMode;
		var marginwidth;
		var innerwestwidth;
		var innereastwidth;
		var gtrwidth;
		

		// Get previous selected layout margin
		var mwidth = jQuery('.selectededitor .margin-west').width();
		
		// Control selector class & visualization
		LayoutSelectControl(this);
	
	
		// For Layout Builder mode e.g. 'one-sidebar-right'
		LayoutMode = jQuery(this).parent().find('.layoutinput').val();
	
		deactivateCurrentBuilder();
	
		// Display selected builder
		jQuery('.'+LayoutMode).addClass('selectededitor');

	<?php foreach(get_the_layouts() as $layout):
			$mylayout = new PageLinesLayout($layout);
			$default_margin = $mylayout->margin->bwidth;
			$ewidth = $mylayout->east->bwidth;
			$wwidth = $mylayout->west->bwidth;
		?>if (LayoutMode == '<?php echo $layout;?>') { 
				marginwidth = mwidth;
				innereastwidth = <?php echo $ewidth;?>;
				innerwestwidth = <?php echo $wwidth;?>; 
				gtrwidth = 10
			}
	<?php endforeach;?>

		setLayoutBuilder(LayoutMode, marginwidth, innereastwidth, innerwestwidth, gtrwidth);
	
	});	
	

});




/*]]>*/</script>