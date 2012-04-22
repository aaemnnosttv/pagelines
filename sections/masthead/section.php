<?php
/*
	Section: Masthead
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A responsive full width splash and text area. Great for getting big ideas across quickly.
	Class Name: PLMasthead	
	Workswith: templates, main, header, morefoot
*/

/**
 * Main section class
 *
 * @package PageLines Framework
 * @author PageLines
 */
class PLMasthead extends PageLinesSection {
 
	/**
	 * Load styles and scripts
	 */
	function section_styles(){
	}
	
	function section_head($clone_id){
		
		
		?>
		
		<script>
		 
		</script>	
		
	<?php }

	/**
	* Section template.
	*/
   function section_template( $clone_id ) { 
	?>
	
	<header class="jumbotron masthead">
	  <div class="inner">
	    <h1>PageLines Framework</h1>
	    <p>A Responsive, Drag &amp; Drop Platform for Beautiful Websites</p>
	    <p class="download-info">
	      <a href="http://www.pagelines.com/pricing/" class="pl-btn pl-btn-primary pl-btn-large">Signup and Download</a>
	    </p>
	  </div>

	  <div class="masthead-links">
	    <ul class="quick-links">
	      <li>
	        <iframe class="github-btn" src="http://markdotto.github.com/github-buttons/github-btn.html?user=twitter&repo=bootstrap&type=watch&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="112px" height="20px"></iframe>
	      </li>
	      <li>
	        <iframe class="github-btn" src="http://markdotto.github.com/github-buttons/github-btn.html?user=twitter&repo=bootstrap&type=fork&count=true" allowtransparency="true" frameborder="0" scrolling="0" width="98px" height="20px"></iframe>
	      </li>
	      <li class="follow-btn">
	        <a href="https://twitter.com/twbootstrap" class="twitter-follow-button" data-link-color="#0069D6" data-show-count="true">Follow @twbootstrap</a>
	      </li>
	      <li class="tweet-btn">
	        <a href="https://twitter.com/share" class="twitter-share-button" data-url="http://twitter.github.com/bootstrap/" data-count="horizontal" data-via="twbootstrap" data-related="mdo:Creator of Twitter Bootstrap">Tweet</a>
	      </li>
	    </ul>
	  </div>
	</header>

	
		<?php 
	}


}