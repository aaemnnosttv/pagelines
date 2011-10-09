
function blocks( css_class, match ){
	
	var matchDimension = 0,
		currentRowStart = 0,
		rowDivs = new Array(),
		el,
		topPosition = 0, 
		cssProp;

	jQuery( css_class ).each(function() {
		
		el = jQuery(this);
	  	topPosition = el.position().top;
		
		if(match == 'height'){
			cssProp = 'max-height';
		} else {
			cssProp = 'min-height';
		}

		
	  if (currentRowStart != topPosition) {

	    // we just came to a new row.  Set all the heights on the completed row
	    for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
	      rowDivs[currentDiv].css( cssProp, matchDimension );
	    }

	    // set the variables for the new row
	    rowDivs.length = 0; // empty the array
	    currentRowStart = topPosition;
	    matchDimension = el.height();
	    rowDivs.push(el);

	  } else {

	    // another div on the current row.  Add it to the list and check if it's taller
	    rowDivs.push(el);
	
		matchDimension = (matchDimension < el.height()) ? (el.height()) : (matchDimension);
	    

	 }

	 // do the last row
	  for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
	    rowDivs[currentDiv].css( cssProp, matchDimension );
	  }

	});

}
