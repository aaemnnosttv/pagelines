/**
 * Platform Javascript Functions
 * Copyright (c) PageLines 2008 - 2011
 *
 * Written By Andrew Powers
 */


/*
 * ###########################
 *   Typography
 * ###########################
 */

	function PageLinesStyleFont(element, property){
		
		var currentSelect = jQuery(element).attr("id");
		
		var selectedOption = '#'+currentSelect +' option:selected';
		
		
		
		if(jQuery(element).hasClass("fontselector")) {
			
			var previewProp = jQuery(selectedOption).attr("id");
			
			var gFontKey = jQuery('#'+currentSelect +' option:selected').attr("title");

			var gFontBase = 'http://fonts.googleapis.com/css?family=';
			
			var stylesheetId = '#' + currentSelect + '_style';
			
			jQuery(stylesheetId).attr("href", gFontBase + gFontKey);
		} else {
			
			var previewProp = jQuery(selectedOption).val();
			
		}
		
		jQuery(element).parent().parent().parent().find('.font_preview_pad').css(property, previewProp);
		
		
	}

/*
 * ###########################
 *   Sortable Sections
 * ###########################
 */
	function setSortable( selected_builder ){
		
		setEmpty(".selected_builder #sortable_template");
		setEmpty(".selected_builder #sortable_sections");
	
		jQuery(".selected_builder #sortable_template").sortable({ 
				connectWith: '.connectedSortable',
				cancel: '.required-section',
				
				items: 'li:not(.bank_title)',
				
				update: function() {
					
					var saveText = jQuery('.selected_builder .confirm_save_pad');
					
					setEmpty(".selected_builder #sortable_template");
					setEmpty(".selected_builder #sortable_sections");
					
		            var order = jQuery('.selected_builder #sortable_template').sortable('serialize');
		          
					var data = {
							action: 'pagelines_save_sortable',
							orderdata: order,
							template: selected_builder, 
							field: 'sections'
						};

					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					jQuery.ajax({
						type: 'GET',
						url: ajaxurl,
						data: data,
						beforeSend: function(){
						},
						success: function(response) {
							jQuery('.selected_builder .ttitle').effect("highlight", {color: "#ddd"}, 2000); 
							jQuery('.selected_builder .confirm_save').show().delay(1200).fadeOut(700); 
						}
					});
					
					
		        }                                         
		    }
		);
		
		jQuery(".selected_builder #sortable_sections").sortable({ 
				connectWith: '.connectedSortable',
				cancel: '.required-section',
				
				items: 'li:not(.bank_title)',
				
				update: function() { setEmpty(".selected_builder #sortable_sections"); }                                         
		});
		
		jQuery(".selected_builder #sortable_template, .selected_builder #sortable_sections").disableSelection();	
	}
	
	function setEmpty(sortablelist){
		if(!jQuery(sortablelist).has('.section-bar').length){
			jQuery(sortablelist).addClass('nosections');
		} else {
			jQuery(sortablelist).removeClass('nosections');
		}
	}

/*
 * ###########################
 *   Template Builder Select
 * ###########################
 */
jQuery(document).ready(function(){


	// Set the default template area... always the header
	/*
		TODO make default the default page, or posts page?
	*/
	var stemplate = 'header';
	jQuery('.'+stemplate).addClass('selected_builder');
	jQuery('.tg-header').addClass('builder_selected_area');
	setSortable(stemplate);
	
	// when a user clicks, highlight the area; slide up the sub selector panels (if they're open)
	jQuery('.tg-format').click(function() {
		// For select interface selection
		jQuery('.builder_selected_area').removeClass('builder_selected_area');
		jQuery(this).addClass('builder_selected_area');
		if(!jQuery(this).hasClass('tg-templates')) jQuery('.sel-templates-sub.sub-template-selector').slideUp();
		if(!jQuery(this).hasClass('tg-content-templates')) jQuery('.sel-content-sub.sub-template-selector').slideUp();
	});
	
	jQuery('.sss-button').click(function() {
		// For select interface selection
		jQuery('.sss-selected').removeClass('sss-selected');
		jQuery(this).addClass('sss-selected');
		var stemplate = jQuery(this).attr('id');
		viewAndSort(stemplate);
	});
	
	// Load the ID of the element if it has a load build class on it
	jQuery('.load-build').click(function() {
	
		var stemplate_id = jQuery(this).attr('id');
		var stemplate = stemplate_id.replace('ta-', '');
		viewAndSort(stemplate);
	
	});
	
	jQuery('.tg-templates').click(function() {
		var stemplate = 'templates-default';
		jQuery('.sss-selected').removeClass('sss-selected');
		jQuery('.sub-template-selector #'+stemplate).addClass('sss-selected');
		jQuery('.sel-templates-sub.sub-template-selector').slideDown();
	
		viewAndSort(stemplate);
	});
	
	jQuery('.tg-content-templates').click(function() {
		var stemplate = 'main-default';
		jQuery('.sss-selected').removeClass('sss-selected');
		jQuery('.sub-template-selector #'+stemplate).addClass('sss-selected');
		
		
		jQuery('.sel-content-sub.sub-template-selector').slideDown();
		
		viewAndSort(stemplate);
		
	});
});

function viewAndSort( stemplate ){
	jQuery('.selected_builder').removeClass('selected_builder');
	jQuery('.'+stemplate).addClass('selected_builder');

	setSortable(stemplate);
}

/*
 * ###########################
 *   Layout Control
 * ###########################
 */

		function LayoutSelectControl (ClickedLayout){
			jQuery(ClickedLayout).parent().parent().find('.layout-image-border').removeClass('selectedlayout');
			jQuery(ClickedLayout).addClass('selectedlayout');
			jQuery(ClickedLayout).parent().find('.layoutinput').attr("checked", "checked");
		}

		function deactivateCurrentBuilder() {
			// Deactivate old builder
			jQuery('.layout_controls').find('.layouteditor').removeClass('selectededitor');
			if ( window['OuterLayout'] ) window['OuterLayout'].destroy();
			if ( window['InnerLayout'] ) window['InnerLayout'].destroy();
		}

		function updateDimensions( LayoutMode, Source ) {
			var contentwidth = jQuery("."+LayoutMode+"  #contentwidth").width() * 2 - 24;
			var innereastwidth = jQuery("."+LayoutMode+"  .innereast").width() * 2;
			var innerwestwidth = jQuery("."+LayoutMode+"  .innerwest").width() * 2;
			var gutterwidth = (jQuery("."+LayoutMode+" #innerlayout .gutter").width()+2) * 2;
			
			// Don't trigger if content is 0px wide. This means the function was triggered in error or by a browser quirk. (e.g. dragging a tab in Firefox)
			if( contentwidth > 0 ){
				
				if(LayoutMode == 'one-sidebar-right' || LayoutMode == 'one-sidebar-left'){var ngutters = 1;}
				else if (LayoutMode == 'two-sidebar-right' || LayoutMode == 'two-sidebar-left' || LayoutMode == 'two-sidebar-center'){var ngutters = 2;}
				else if (LayoutMode == 'fullwidth'){var ngutters = 0;gutterwidth = 0}

				var innercenterwidth = contentwidth - innereastwidth - innerwestwidth;

				jQuery("."+LayoutMode+" #contentwidth .loelement-pad .width span").html(contentwidth);
				jQuery("."+LayoutMode+" .innercenter .loelement-pad .width span").html(innercenterwidth);
				jQuery("."+LayoutMode+" .innereast .loelement-pad .width span").html(innereastwidth);
				jQuery("."+LayoutMode+"  .innerwest .loelement-pad .width span").html(innerwestwidth);

				var primarysidebar = jQuery("."+LayoutMode+" #layout-sidebar-1 .loelement-pad .width span").html();
				var maincontent = jQuery("."+LayoutMode+" #layout-main-content .loelement-pad .width span").html();
				var wcontent = jQuery("."+LayoutMode+" #contentwidth .loelement-pad span").html();



				jQuery(".layout_controls").find("#input-content-width").val(wcontent);

				jQuery("."+LayoutMode+" #input-primarysidebar-width").val(primarysidebar);

				jQuery("."+LayoutMode+" #input-maincolumn-width").val(maincontent);
				
			} 
			


		}

	///// LAYOUT BUILDER //////
	function setLayoutBuilder(LayoutMode, margin, innereast, innerwest, gutter){

		
		var MainLayoutBuilder, InnerLayoutBuilder;
	
		window['OuterLayout'] = jQuery("."+LayoutMode+" .layout-main-content").layout({ 

						center__paneSelector:	".layout-inner-content"
					,	east__paneSelector:		".margin-east"
					,	west__paneSelector: 	".margin-west"
					,	closable:				false	// pane can open & close
					,	resizable:				true	// when open, pane can be resized 
					,	slidable:				false
					,	resizeWhileDragging:	true
					,	west__resizable:		true	// Set to TRUE to activate dynamic margin
					,	east__resizable:		true	// Set to TRUE to activate dynamic margin
					,	east__resizerClass: 	'pagelines-resizer-east'
					,	west__resizerClass: 	'pagelines-resizer-west'
					,	east__size:				margin
					,	west__size:				margin
					,	east__minSize:			10
					,	west__minSize:			10
					, 	east__maxSize:  		113
					, 	west__maxSize:  		113
					, 	west__onresize: function (pane, $Pane, paneState) {
					    var width  = paneState.innerWidth;
						var realwidth = width * 2;
						var currentElement = jQuery("."+LayoutMode+" .margin-east");
						
						// This will fire in Firefox in strange times, make sure it's visible before doing anything
						if(currentElement.is(':visible')){
							currentElement.width(width);
							var position = jQuery("."+LayoutMode+" .pagelines-resizer-west").position();
							jQuery("."+LayoutMode+" .pagelines-resizer-east").css('right', position.left);
							updateDimensions(LayoutMode, 'Margin West Resize');
						}
						
					} 
					, 	east__onresize: function (pane, $Pane, paneState) {
					    var width  = paneState.innerWidth;
						var realwidth = width * 2;
						var currentElement = jQuery("."+LayoutMode+" .margin-west");
						
						// This will fire in Firefox in strange times, make sure it's visible before doing anything
						if(currentElement.is(':visible')){
							currentElement.width(width);
							var position = jQuery("."+LayoutMode+" .pagelines-resizer-east").css('right');
							jQuery("."+LayoutMode+" .pagelines-resizer-west").css('left', position);
							updateDimensions(LayoutMode, 'Margin East Resize');
						}
					}
		});
		window['InnerLayout'] = jQuery("."+LayoutMode+" .layout-inner-content").layout({ 

						closable:				false
					,	resizable:				true
					,	slidable:				false	
					, 	north__resizable: 		false
					, 	south__resizable: 		false
					,	resizeWhileDragging:	true
					,	east__resizerClass: 	'gutter'
					,	west__resizerClass: 	'gutter'
					,	east__minSize: 			60
					,	west__minSize: 			60
					,	center__minWidth: 		60
					,   east__spacing_open:     gutter
					, 	west__spacing_open: 	gutter
					,	east__size: 			innereast
					,	west__size: 			innerwest
					, 	west__onresize: function (pane, $Pane, paneState) { updateDimensions(LayoutMode, 'Inner West'); } 
					, 	east__onresize: function (pane, $Pane, paneState) {	updateDimensions(LayoutMode, 'Inner East'); }
		});
		
		updateDimensions(LayoutMode, 'Layout Builder');
	}




/*
 * ###########################
 *   AJAX Uploading
 * ###########################
 */
jQuery(document).ready(function(){
	jQuery('.image_upload_button').each(function(){

		var clickedObject = jQuery(this);
		var clickedID = jQuery(this).attr('id');
		var actionURL = jQuery(this).parent().find('.ajax_action_url').val();

		new AjaxUpload(clickedID, {
			  action: actionURL,
			  name: clickedID, // File upload name
			  data: { // Additional data to send
					action: 'pagelines_ajax_post_action',
					type: 'upload',
					data: clickedID },
			  autoSubmit: true, // Submit file after selection
			  responseType: false,
			  onChange: function(file, extension){},
			  onSubmit: function(file, extension){
					clickedObject.text('Uploading'); // change button text, when user selects file	
					this.disable(); // If you want to allow uploading only 1 file at time, you can disable upload button
					interval = window.setInterval(function(){
						var text = clickedObject.text();
						if (text.length < 13){	clickedObject.text(text + '.'); }
						else { clickedObject.text('Uploading'); } 
					}, 200);
			  },
			  onComplete: function(file, response) {

				window.clearInterval(interval);
				clickedObject.text('Upload Image');	
				this.enable(); // enable upload button

				// If there was an error
				if(response.search('Upload Error') > -1){
					var buildReturn = '<span class="upload-error">' + response + '</span>';
					jQuery(".upload-error").remove();
					clickedObject.parent().after(buildReturn);

				}
				else{

					var previewSize = clickedObject.parent().find('.image_preview_size').attr('value');

					var buildReturn = '<img style="max-width:'+previewSize+'px;" class="pagelines_image_preview" id="image_'+clickedID+'" src="'+response+'" alt="" />';

					jQuery(".upload-error").remove();
					jQuery("#image_" + clickedID).remove();	
					clickedObject.parent().after(buildReturn);
					jQuery('img#image_'+clickedID).fadeIn();
					clickedObject.next('span').fadeIn();
					clickedObject.parent().find('.uploaded_url').val(response);
				}
			  }
			});

		});

		//AJAX Remove (clear option value)
		jQuery('.image_reset_button').click(function(){

			var clickedObject = jQuery(this);
			var clickedID = jQuery(this).attr('id');
			var theID = jQuery(this).attr('title');	
			var actionURL = jQuery(this).parent().find('.ajax_action_url').val();
			
			var ajax_url = actionURL;

			var data = {
				action: 'pagelines_ajax_post_action',
				type: 'image_reset',
				data: theID
			};

			jQuery.post(ajax_url, data, function(response) {
				var image_to_remove = jQuery('#image_' + theID);
				var button_to_hide = jQuery('#reset_' + theID);
				image_to_remove.fadeOut(500,function(){ jQuery(this).remove(); });
				button_to_hide.fadeOut();
				clickedObject.parent().find('.uploaded_url').val('');				
			});

			return false; 

		});


});
// End AJAX Uploading

/*
 * ###########################
 *   Color Picker
 * ###########################
 */
function setColorPicker(optionid, color){
	jQuery('#'+optionid+'_picker').children('div').css('backgroundColor', color);    
	jQuery('#'+optionid+'_picker').ColorPicker({
		color: color,
		onShow: function (colpkr) {
			jQuery(colpkr).fadeIn(500);
			return false;
		},
		onHide: function (colpkr) {
			jQuery(colpkr).fadeOut(500);
			return false;
		},
		onChange: function (hsb, hex, rgb) {
			jQuery('#'+optionid+'_picker').children('div').css('backgroundColor', '#' + hex);
			jQuery('#'+optionid+'_picker').next('input').attr('value','#' + hex);
		}
	});
}

function PageLinesSimpleToggle(showElement, hideElement){
	
	jQuery(hideElement).hide();
	jQuery(hideElement+'_button').removeClass('active_button');
	
	if( jQuery(showElement).is(':visible')) {
		jQuery(showElement).fadeOut();
		jQuery(showElement+'_button').removeClass('active_button');
	} else {
		jQuery(showElement+'_button').addClass('active_button');
		jQuery(showElement).fadeIn();
		
	}
	
}


function PageLinesSlideToggle(toggle_element, toggle_input, text_element, show_text, hide_text, option){
	var opt_value; 
	var input_flag;
	
	if(jQuery(toggle_input).val() == 'show'){
		input_flag = 'hide';
		jQuery(toggle_input).val(input_flag);
		jQuery(toggle_element).fadeOut();
		
		opt_value = input_flag;
		
		jQuery(text_element).html(hide_text);
		
		jQuery(toggle_element).css('display', 'none');
	} else {
		input_flag = 'show';
		
		jQuery(toggle_input).val(input_flag);
		jQuery(toggle_element).fadeIn();
		
		opt_value = input_flag;
		jQuery(text_element).html(show_text);
		
		jQuery(toggle_element).css('display', 'block');
	}
	
	var data = {
		action: 'pagelines_ajax_save_option',
		option_name: option,
		option_value: opt_value
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	jQuery.post(ajaxurl, data, function(response) { });
	
}

/*
 * ###########################
 *   Email Capture
 * ###########################
 */

function sendEmailToMothership( email, input_id ){
	// validate that shit
	
	jQuery('.the_email_response').html('');
	jQuery(".the_email_response").hide();
	var hasError = false;
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

	if( email == '') {
	    jQuery(".the_email_response").html('<span class="email_error">You\'re silly... The email field is blank!</span>').show().delay(2000).slideUp();
	    hasError = true;
	}
	
	else if(!emailReg.test(email)) {
	    jQuery(".the_email_response").html('<span class="email_error">Hmm... doesn\'t seem like a valid email!</span>').show().delay(2000).slideUp();
	    hasError = true;
	}
	
	if(hasError == true) { return false; }
	
	var data = {
		email: email
	};
	
	
	var option_name = 'pagelines_email_sent';
	
	jQuery.ajax({
		type: 'GET',
		url: "http://api.pagelines.com/subscribe/index.php?",
		dataType: "json",
		data: data,
		success: function(response) {
			if(response == 1){
				jQuery(".the_email_response").html('Email Sent!').show().delay(2000).slideUp();
				
				var data = {
						action: 'pagelines_ajax_save_option',
						option_name: option_name,
						option_value: email
					};

				// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
				jQuery.ajax({
					type: 'POST',
					url: ajaxurl,
					data: data,
					success: function(response) {
					}
				});
				
			} else if(response == 0){
				jQuery(".the_email_response").html('Email Already Submitted!').show().delay(2000).slideUp();
			}else if(response == -1){
				jQuery(".the_email_response").html('There was an error on our side. Sorry about that...').show().delay(2000).slideUp();
			}			
			
		
		}
	});
	

}

/*
 * ###########################
 *   jQuery Extension
 * ###########################
 */

jQuery.fn.center = function ( relative_element ) {
	
    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 4+jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(relative_element).width() - this.width() ) / 2+jQuery(relative_element).scrollLeft() + "px");
    return this;
}

