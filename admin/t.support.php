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
			'title'			=> __( 'PageLines Professional Support', 'pagelines' ), 
			'data'			=> $this->support_array(), 
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
	
	function support_array(){
		
		$data = array(
			'story3'	=> array(
				'title'		=> __( 'PageLines Live - Technical Community Chat (Plus Only)', 'pagelines' ),
				'text'		=> __( 'Talk to others in the PageLines community and get instant help from Live Moderators.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-live.png', 
				'link'		=> 'http://www.pagelines.com/live/', 
			),
			'story4'	=> array(
				'title'		=> __( 'PageLines Documentation', 'pagelines' ), 
				'text'		=> __( 'Docs for everything you want to do with PageLines.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-docs.png', 
				'link'		=> 'http://www.pagelines.com/wiki/', 
			),
			'story1'	=> array(
				'title'		=> __( 'PageLines Forum', 'pagelines' ), 
				'text'		=> __( 'Find answers to common technical issues. Post questions and get responses from PageLines experts.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-forum.png', 
				'link'		=> 'http://www.pagelines.com/forum/', 
			),
			'vids'	=> array(
				'title'		=> __( 'PageLines Videos and Training', 'pagelines' ), 
				'text'		=> __( 'Check out the latest videos on how to use PageLines fast and effectively via YouTube.', 'pagelines' ), 
				'img'		=> PL_ADMIN_ICONS . '/dash-video.png', 
				'link'		=> 'http://www.youtube.com/user/pagelines/videos?view=1', 
			),
		);
		
		return $data;
		
	}
	
	function get_live_bill(){
		
		$url = pagelines_check_credentials( 'vchat' );
		
		$iframe = ( $url ) ? sprintf( '<iframe class="live_chat_iframe" src="%s"></iframe>', $url ) : false;
		$rand = 
		ob_start();
		?>
		
		<div class="admin_billboard">
			<div class="admin_billboard_pad fix">
					<h3 class="admin_header_main">
					 <?php _e( 'PageLines Live Chat', 'pagelines'); ?>
					</h3>
					<div class='admin_billboard_text'>
					 <?php _e( 'A moderated live community chat room for discussing technical issues. (Plus Only)', 'pagelines' ); ?>
					</div>
			</div>
		</div>
		<div class="live_chat_wrap fix">
			
			<?php 
			
			if($iframe):
				echo $iframe; 
			else:?>
				
				<div class="live_chat_up_bill">
					<h3><?php _e( 'Live Chat Requires an active PageLines Plus account', 'pagelines' ); ?></h3>
					<?php
					if ( !pagelines_check_credentials() )
						printf( '<a class="button" href="%s">Login</a>', admin_url(PL_ACCOUNT_URL) );
						
					else
						if ( !VPLUS )
							printf( '<a class="button" href="%s">%s</a>', ADD_PLUS, __( 'Upgrade to PageLines Plus', 'pagelines' ) );?>			 
				</div>
			<?php endif;	?>
		</div>
		<?php 
		
		$bill = ob_get_clean();
	
		return apply_filters('pagelines_welcome_billboard', $bill);
	}
}