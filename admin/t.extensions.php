<?php


class PageLinesCoreExtensions {

	/**
     * PHP5 Constructor
     */
	function __contruct(){ }
	
	function draw(){

		$dash = new PageLinesDashboard;
		
		// PageLines Plus
		$args = array(
			'title'			=> 'Available Plus Extensions', 
			'data'			=> $this->test_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/plusbtn.png', 
			'excerpt-trim'	=> false, 
			'format'		=> 'plus-extensions'
		); 
		
		$view = $this->get_welcome_billboard();

		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		return $view;
	}
	
	/**
     * Get Welcome Billboard
     *
     * Used to produce the content at the top of the theme Welcome page.
     *
     * @uses        CHILD_URL (constant)
     * @internal    uses 'pagelines_welcome_billboard' filter
     *
     * @return      mixed|void
     */
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 Free Extensions With Plus
					</h3>
					<div class='admin_billboard_text'>
						With PageLines Plus, you get all PageLines-built extensions and more every month!
					</div>
			</div>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
	
	function test_array(){
		
		$data = array(
			'story1'	=> array(
				'title'		=> 'Rockin Theme', 
				'text'		=> "It's time we introduce you to the first rule.  The first rule of PageLines is that you come first. We truly appreciate your business and support.", 
				'img'		=> PARENT_URL . '/screenshot.png', 
				'overview'	=> 'http://testlink.com/overview', 
				'download'	=> 'http://testlink.com/download'
			), 
			'story3'	=> array(
				'title'	=> 'BadAss Section', 
				'text'	=> "Check out the <a href='".admin_url(PL_TEMPLATE_SETUP_URL)."'>Template Setup panel</a>! Using drag and drop you can completely control the appearance of your templates. Learn more in the <a href='http://www.pagelines.com/wiki/'>docs</a>.", 
				'img'	=> PARENT_URL . '/screenshot.png', 
				'overview'	=> 'http://testlink.com/overview', 
				'download'	=> 'http://testlink.com/download'
			),
			'story4'	=> array(
				'title'	=> 'Super Cool Plugin', 
				'text'	=> "To maximize PageLines you're gonna need some extensions. Head over to the extensions page to get supported plugins and learn about extensions in the Store and Plus.", 
				'img'	=> PARENT_URL . '/screenshot.png',
				'overview'	=> 'http://testlink.com/overview', 
				'download'	=> 'http://testlink.com/download'
			),
			
		);
		
		return $data;
		
	}

}