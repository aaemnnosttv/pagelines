<?php
/*
	Section: Scroll Spy
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A special section with auto scroll content.
	Class Name: ScrollSpy
	Workswith: templates
	Cloning: false
	Failswith: pagelines_special_pages()
*/

/**
 * Content Section
 *
 * @package PageLines Framework
 * @author PageLines
 */
class ScrollSpy extends PageLinesSection {

	/**
	 * Load styles and scripts
	 */
	function section_styles(){
		
		wp_enqueue_script( 'pagelines-bootstrap-all' );
		wp_enqueue_script( 'jquery' );
	}


	function section_head($clone_id){ ?>
		
		<script>
		jQuery(document).ready(function() {
			
			var spyCounter = 1
			, mainOffset = jQuery('.spynav').offset().top
			
			jQuery('body').find('.page-header').each(function() {
				
				var headerID = 'spyID'+spyCounter++;
				var headerText;
				
				// Set ID of page header
				jQuery(this).attr('id', headerID)
				
				// Get text for nav link
				if (jQuery(this).attr('title')) {
					headerText = jQuery(this).attr('title');
				} else {
					headerText = 'Set Title Attr.';
				}
				
			
			
				
				jQuery('.spynav .nav').append('<li><a class="spyanchor" href="#'+headerID+'">'+headerText+'</a></li>');
				
			});
			
			jQuery('body').attr('data-spy', 'scroll');
			jQuery('body').scrollspy({offset: 100-mainOffset});
		
			
			jQuery(".spyanchor").click( function(event){		
					event.preventDefault();
					var offTop = jQuery(this.hash).offset().top - 140;
					jQuery('html,body').animate({scrollTop:offTop}, 500);
				});
		
			var $win = jQuery(window)
					, $nav = jQuery('.spynav')
					, navbarHeight = jQuery('.navbar-full-width').length && jQuery('.navbar-full-width').outerHeight()
					, navbarOffset = jQuery('.navbar-full-width').length && jQuery('.navbar-full-width').offset().top
					, navOffset = navbarHeight + navbarOffset
					, navTop = jQuery('.spynav').length && jQuery('.spynav').offset().top - navbarOffset
					, isFixed = 0				
		
	
	    	processScroll()
	    		    
  		    // hack sad times - holdover until rewrite for 2.1
  		    $nav.on('click', function () {
  		      if (!isFixed) setTimeout(function () {  $win.scrollTop($win.scrollTop() - 47) }, 10)
  		    })
  
  		    $win.on('scroll', processScroll)
  		
  				   
  			function processScroll() {
  				var i, scrollTop = $win.scrollTop()
  		
  				if (scrollTop >= navTop && !isFixed) {
					var contWidth = jQuery('.section-scrollspy .content-pad').width();
					jQuery('.spynav .nav').width(contWidth);
  					isFixed = 1
  					$nav.css('top', navOffset).addClass('spynav-fixed')
  				} else if (scrollTop <= navTop && isFixed) {
  					isFixed = 0
					jQuery('.spynav .nav').width('auto');
  					$nav.removeClass('spynav-fixed')
  				}
  			}

		});
		
		 
		</script>	

	<?php }	


	/**
	* Section template.
	*/
	function section_template() {  
	
		global $post;
			
		printf('
			<div id="spynav" class="spynav">
	          <ul class="nav nav-pills">
	          </ul>
	        </div>');
	 
	}

}