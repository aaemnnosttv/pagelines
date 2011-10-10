
// these are (ruh-roh) globals. You could wrap in an
                // immediately-Invoked Function Expression (IIFE) if you wanted to...
var currentTallest = 0,
    currentRowStart = 0,
    rowDivs = new Array();

function setConformingHeight(el, newHeight) {
	
        // set the height to something new, but remember the original height in case things change
        el.data("originalHeight", (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight")));
        el.css('min-height', newHeight);
}

function getOriginalHeight(el) {
        // if the height has changed, send the originalHeight
        return (el.data("originalHeight") == undefined) ? (el.height()) : (el.data("originalHeight"));
		//return el.height();
}

function columnConform() {

        // find the tallest DIV in the row, and set the heights of all of the DIVs to match it.
        jQuery('.blocks').each(function() {
        
                // "caching"
                var $el = jQuery(this);
                
                var topPosition = $el.position().top;

                if (currentRowStart != topPosition) {
				
                        // we just came to a new row.  Set all the heights on the completed row
                        for(currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
							
							setConformingHeight(rowDivs[currentDiv], currentTallest);
						}

                        // set the variables for the new row
                        rowDivs.length = 0; // empty the array
                        currentRowStart = topPosition;
                        currentTallest = getOriginalHeight($el);
                        rowDivs.push($el);

                } else {

                        // another div on the current row.  Add it to the list and check if it's taller
                        rowDivs.push($el);
                        currentTallest = (currentTallest < getOriginalHeight($el)) ? (getOriginalHeight($el)) : (currentTallest);

                }
                // do the last row
                for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) setConformingHeight(rowDivs[currentDiv], currentTallest);

        });

}


jQuery(window).resize(function() {
	columnConform();
});

// Dom Ready
// You might also want to wait until window.onload if images are the things that
// are unequalizing the blocks
jQuery(window).load(function() {
        columnConform();
});


// function blocks( css_class, match ){
// 	
// 	var matchDimension = 0,
// 		currentRowStart = 0,
// 		rowDivs = new Array(),
// 		el,
// 		topPosition = 0, 
// 		cssProp;
// 
// 	jQuery( css_class ).each(function() {
// 		
// 		el = jQuery(this);
// 	  	topPosition = el.position().top;
// 		
// 		if(match == 'height'){
// 			cssProp = 'max-height';
// 		} else {
// 			cssProp = 'min-height';
// 		}
// 
// 		
// 	  if (currentRowStart != topPosition) {
// 
// 	    // we just came to a new row.  Set all the heights on the completed row
// 	    for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
// 	      rowDivs[currentDiv].css( cssProp, matchDimension );
// 	    }
// 
// 	    // set the variables for the new row
// 	    rowDivs.length = 0; // empty the array
// 	    currentRowStart = topPosition;
// 	    matchDimension = el.height();
// 	    rowDivs.push(el);
// 
// 	  } else {
// 
// 	    // another div on the current row.  Add it to the list and check if it's taller
// 	    rowDivs.push(el);
// 	
// 		matchDimension = (matchDimension < el.height()) ? (el.height()) : (matchDimension);
// 	    
// 
// 	 }
// 
// 	 // do the last row
// 	  for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
// 	    rowDivs[currentDiv].css( cssProp, matchDimension );
// 	  }
// 
// 	});
// 
// }
