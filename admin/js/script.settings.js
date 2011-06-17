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
					saveSectionOrder( selected_builder );				
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
	
	function cloneSection( sectionId ){
		
		var selected_builder = jQuery('.selected_builder').attr('title');
		
		$new_clone_id = false;
		$i = 2;
		while(!$new_clone_id){
			
			if(jQuery('#'+ sectionId + 'ID' + $i).exists()){
				$i++;
		
			} else {
				$new_clone_id = true;
			}
			
		}
		
		var newID = sectionId+ 'ID' + $i;
		
		jQuery('#'+sectionId).clone().hide().insertAfter('#'+sectionId).attr('id', newID);
		
		jQuery('#'+newID).find('.the_clone_id').html($i);
		
		jQuery('#'+newID).find('.section-controls').hide();
		
		jQuery('#'+newID).slideDown();
		
		saveSectionOrder( selected_builder );	
		
	}
	
	function saveSectionOrder( selected_builder ){
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
			beforeSend: function(){ },
			success: function(response) {
				jQuery('.selected_builder .ttitle').effect("highlight", {color: "#ddd"}, 2000); 
				jQuery('.selected_builder .confirm_save').show().delay(1200).fadeOut(700); 
				//alert(response);
			}
		});
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
			, 	east__maxSize:  		188
			, 	west__maxSize:  		188
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

				    	closable: 				true 
					,   togglerLength_open: 	0 
					,	resizable:				true
					,	slidable:				false	
					, 	north__resizable: 		false
					, 	south__resizable: 		false
					,	resizeWhileDragging:	true
					,	east__resizerClass: 	'gutter'
					,	west__resizerClass: 	'gutter'
					,	east__minSize: 			30
					,	west__minSize: 			30
					,	center__minWidth: 		20
					, 	east__closable:  		false
					, 	west__closable:  		false
					,   east__spacing_open:     gutter
					, 	west__spacing_open: 	gutter
					,	east__size: 			innereast
					,	west__size: 			innerwest
					, 	west__onresize: function (pane, $Pane, paneState) { updateDimensions(LayoutMode, 'Inner West'); } 
					, 	east__onresize: function (pane, $Pane, paneState) {	updateDimensions(LayoutMode, 'Inner East'); }
		});
		
		updateDimensions(LayoutMode, 'Layout Builder');
	}