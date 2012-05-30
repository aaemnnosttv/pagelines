<?php


class PageLinesDashboard {
	
	
	function __contruct(){
		
		
		
	}
	
	function draw(){
		
		// Updates Dashboard
		
		// --> $this->get_updates(); 
		
		$args = array(
			'title'	=> 'Your Available Updates', 
			'data'	=> $this->updates_test_array(), 
			'icon'	=> PL_ADMIN_ICONS . '/download.png'
		); 
		
		$dashboards = $this->dashboard_pane('updates', $args); 
		
		// PageLines Blog Dashboard
		
		$args = array(
			'title'	=> 'News from the PageLines Blog', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half pl-dash-space', 
			'icon'	=> PL_ADMIN_ICONS . '/welcome.png'
		); 
		
		$dashboards .= $this->dashboard_pane('news', $args);
		
		// PageLines Store Latest Dash
		
		$args = array(
			'title'	=> 'Latest on PageLines Store', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half', 
			'icon'	=> PL_ADMIN_ICONS . '/store.png'
		); 
		
		$dashboards .= $this->dashboard_pane('store', $args);
		
		// Latest from the Community
		$args = array(
			'title'	=> 'From the Community', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half pl-dash-space', 
			'icon'	=> PL_ADMIN_ICONS . '/users.png'
		); 
		
		$dashboards .= $this->dashboard_pane('community', $args);
		
		// PageLines Plus
		$args = array(
			'title'	=> 'PageLines Extensions', 
			'data'	=> $this->test_array(), 
			'classes'	=> 'pl-dash-half', 
			'icon'	=> PL_ADMIN_ICONS . '/plusbtn.png'
		); 
		
		$dashboards .= $this->dashboard_pane('extensions', $args);
		
		
		return $this->dashboard_wrap($dashboards); 
		
	}
	
	function dashboard_wrap( $dashboards ){
		
		return sprintf('<div class="pl-dashboards fix">%s</div>', $dashboards);
		
	}
	
	
	function dashboard_pane( $id, $args = array() ){
		
		$defaults = array(
			'title' 		=> 'Dashboard',
			'icon'			=> PL_ADMIN_ICONS.'/pin.png',  
			'classes'		=> '', 
			'data'			=> array(), 
			'data-format'	=> 'array', 
			
		);
		
		$a = wp_parse_args($args, $defaults); 
		
		ob_start()
		?>
		<div id="<?php echo 'pl-dash-'.$id;?>" class="pl-dash <?php echo $a['classes'];?>">
			<div class="pl-dash-pad">
				<h2 class="dash-title"><?php printf('<img src="%s"/> %s', $a['icon'], $a['title']); ?></h2>
				<?php echo $this->dashboard_stories( $a ); ?>
			</div>
		</div>
		<?php 
		
		return ob_get_clean();
		
	}
	
	function dashboard_stories( $args = array() ){
		
		if($args['data-format'] == 'array')
			return $this->stories_array_format($args); 
		
		
	}
	
	function stories_rss_format(){
		
	}
	
	function stories_array_format($args){
		
		ob_start();
		
		$count = 1;
		foreach($args['data'] as $id => $story){
			
			$image = (isset($story['img'])) ? $story['img'] : false; 
			$tag = (isset($story['tag'])) ? $story['tag'] : false; 
			$tag_class = (isset($story['tag-class'])) ? $story['tag-class'] : ''; 
			
			$alt = ($count % 2 == 0) ? 'alt-story' : '';
		?>
		<div class="pl-dashboard-story media <?php echo $alt;?> dashpane">
			<div class="dashpane-pad fix">
				<?php
					if($tag)
						printf('<div class="img"><div class="extend_button %s">%s</div></div>', $tag_class, 'Update'); 
					elseif($image)
						printf('<div class="img img-frame"><img src="%s" /></div>', $image);
				
				?>
				<div class="bd">
					<h3><?php echo $story['title'];?></h3>
					<p><?php echo custom_trim_excerpt($story['text'], 18);?></p>
				</div>
			</div>
		</div>
		
		<?php
		$count++;
		}
		
		return ob_get_clean();
	}
	
	function stories_remote_url_format(){
		
	}
	
	function test_array(){
		
		$data = array(
			'story1'	=> array(
				'title'	=> 'Test Story 1', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about'
			), 
			'story2'	=> array(
				'title'	=> 'Test Story 3', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about', 
				'img'	=> PL_ADMIN_IMAGES . '/pagelines-icon.jpg'
			),
			'story3'	=> array(
				'title'	=> 'Test Story 3', 
				'text'	=> 'Here is a bunch of text for the first text story that will go in the admin. Simon is gonna have to work with this to get the rest figured out. That is all i have to say.', 
				'link'	=> 'http://www.pagelines.com/about'
			)
		);
		
		return $data;
		
	}
	
	function updates_test_array(){
		
		$data = array(
			'story1'	=> array(
				'title'	=> 'Cool Plugin - Version 2.1', 
				'text'	=> 'Changelog...', 
				'tag'	=> 'update'
			), 
			'story2'	=> array(
				'title'	=> 'Cool Theme - Version 1.1', 
				'text'	=> 'Changelog...', 
				'tag'	=> 'section'
			),
			'story3'	=> array(
				'title'	=> 'Rockin Section - Version 1.3', 
				'text'	=> 'Changelog...', 
				'tag'	=> 'section'
			),
		);
		
		return $data;
		
	}
		
	
	
}


