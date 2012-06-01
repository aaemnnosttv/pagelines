<?php


class PageLinesSupportPanel {

	/**
     * PHP5 Constructor
     */
	function __contruct(){ }
	
	function draw(){

		$dash = new PageLinesDashboard;
		
		// PageLines Plus
		$args = array(
			'title'			=> 'PageLines Professional Support
			', 
			'data'			=> $this->test_array(), 
			'icon'			=> PL_ADMIN_ICONS . '/balloon-white.png', 
			'excerpt-trim'	=> false,
			'format'		=> 'button-links'
		); 
		
		$view = $this->get_welcome_billboard();

		$view .= $dash->wrap_dashboard_pane('tips', $args);
		
		$view .= $this->get_live_bill();
		
		return $view;
	}
	
	function get_welcome_billboard(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 PageLines Support
					</h3>
					<div class='admin_billboard_text'>
					 Tons of options for fast and professional support.
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
				'title'		=> 'PageLines Forum', 
				'text'		=> "", 
				'img'		=> PARENT_URL . '/screenshot.png', 
				'link'		=> 'http://testlink.com/overview', 
			), 
			'story3'	=> array(
				'title'		=> 'PageLines Live - Technical Community Chat (Plus Only)', 
				'text'		=> "", 
				'img'		=> PARENT_URL . '/screenshot.png', 
				'link'		=> 'http://testlink.com/overview', 
			),
			'story4'	=> array(
				'title'		=> 'PageLines Documentation', 
				'text'		=> "", 
				'img'		=> PARENT_URL . '/screenshot.png',
				'link'		=> 'http://testlink.com/overview', 
			),
			
		);
		
		return $data;
		
	}
	
	function get_live_bill(){
		
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 PageLines Live Chat
					</h3>
					<div class='admin_billboard_text'>
					 A moderated live community chat room for discussing technical issues. (Plus Only)
					</div>
			</div>
		</div>
		<div class="live_chat_wrap fix">
			<iframe class="live_chat_iframe" src="https://pagelines.campfirenow.com/6cd04"></iframe>
		</div>
		<?php 
		
		$bill = ob_get_clean();
		
		
		return apply_filters('pagelines_welcome_billboard', $bill);
	}

}