<?php
/**
 * 
 *
 *  PageLines Meta Panel Option Handling
 *
 *
 *  @package PageLines Core
 *  @subpackage Post Types
 *  @since 4.0
 *
 */
class PageLinesMetaPanel {

	var $tabs = array();	// Controller for drawing meta options

	
	var $blacklist = array( 'banners', 'feature', 'boxes', 'attachment', 'revision', 'nav_menu_item' );
	
	/**
	 * PHP5 constructor
	 */
	function __construct( $settings = array() ) {

		global $post; 
		global $pagenow;
		
	
			/**
			 * Single post pages have post as GET, not $post as object
			 */
			$post = (!isset($post) && isset($_GET['post'])) ? get_post($_GET['post'], 'object') : null;
	
			
			$this->ptype = $this->current_admin_post_type();
		
			$this->page_for_posts = ( isset($post) && get_option( 'page_for_posts' ) === $post->ID ) ? true : false;			
		

			$defaults = array(
					'id' 		=> 'pagelines-metapanel',
					'name' 		=> $this->get_the_title(),
					'posttype' 	=> $this->get_the_post_types(),
					'location' 	=> 'normal', 
					'priority' 	=> 'low', 
					'hide_tabs'	=> false
				);
	
			$this->settings = wp_parse_args($settings, $defaults); // settings for post type
	
			$this->register_actions();
	
			$this->hide_tabs = $this->settings['hide_tabs'];
			

	}
	

	
	function get_the_post_types(){
		$the_post_types = ( isset( $_GET['post'] ) && ! in_array( get_post_type( $_GET['post'] ), apply_filters( 'pagelines_meta_blacklist', $this->blacklist ) ) ) 
						? array( 'post', 'page', get_post_type( $_GET['post'] ) ) 
						: array( 'post', 'page' );
		
		return $the_post_types;
	}
	
	/**
	 * Get current post type, set as GET on 'add new' pages
	 */
	function current_admin_post_type(){
		global $pagenow;
		global $post;
		$current_post_type = ( !isset($post) && isset($_GET['post_type']) ) 
							? $_GET['post_type'] 
							: ( isset($post) && isset($post->post_type) ? $post->post_type : 
								($pagenow == 'post-new.php' ? 'post' : null));		
		
		return $current_post_type;
		
	}
	
	function get_the_title(){
		global $post;
		$this->base_name = 'PageLines Meta Settings';
		$name = $this->base_name;
		 
		if($this->ptype == 'post' || $this->ptype == 'page'){
			$current_template = (isset($post)) ? get_post_meta($post->ID, '_wp_page_template', true) : false;
	
			$this->page_templates = array_flip( PageLinesTemplate::get_page_templates() );
		
			if(  $this->ptype == 'page' && $current_template && $current_template != 'default') {

				if(isset($this->page_templates[$current_template]))
					$slug = $this->page_templates[$current_template];
		
			}elseif(  $this->ptype == 'page' )
				$slug = 'Default Page';
			elseif( $this->ptype == 'post' )
				$slug = 'Single Post';
			elseif( $this->page_for_posts )
				$slug = 'Blog Page';
			else 
				$slub = '';
		
			$this->edit_slug = $slug;
		
			$name .= sprintf(' <small style="font-style:italic">(%s)</small>', $slug);
		} 
		
		return $name;
	}
	
	/**
	 * Register a new tab for the meta panel
	 * This will look at Clone values and draw cloned tabs for cloned sections
	 *
	 * @since 2.0.b4
	 */
	function register_tab( $o = array(), $option_array = array(), $location = 'bottom') {
		
		$d = array(
				'id' 		=> '',
				'name' 		=> '',
				'icon' 		=> '',
				'clone_id' 	=> 1, 
				'active'	=> true
			);

		$o = wp_parse_args($o, $d);
		

			$tab_id = $o['id'].$o['clone_id'];
	
			plprint($tab_id);
		
		if( $o['clone_id'] != 1 ){
			
			$name = $o['name'].' (Clone #'.$o['clone_id'].')';
			
			/**
			 * For cloned tab, unset keys and change to new val w/ key
			 */
			foreach($option_array as $key => $opt){
				
				$newkey = join( '_', array($key, $o['clone_id']) );
				
				$opt['title'] = $opt['title']. ' ('.$o['clone_id'].')';
				$option_array[$newkey] = $opt;
				unset( $option_array[$key] );
				
			}
			
		} else 
			$name = $o['name'];
		
		
		if($location == 'top'){
			
			$top[$tab_id]->options = $option_array;
			$top[$tab_id]->icon = $o['icon'];
			$top[$tab_id]->active = $o['active'];
			$top[$tab_id]->name = $name;
			

			$this->tabs = array_merge($top, $this->tabs);
			
		} else {
			$this->tabs[$tab_id]->options = $option_array;
			$this->tabs[$tab_id]->icon = $o['icon'];
			$this->tabs[$tab_id]->active = $o['active'];
			$this->tabs[$tab_id]->name = $name;
		}
		
	}
	
	
	function register_actions(){
		
		// Adds the box
		add_action("admin_menu",  array(&$this, 'add_metapanel_box'));
		
		// Saves the options.
		add_action('save_post', array(&$this, 'save_meta_options'));
		
	}
	
	function add_metapanel_box(){
		
		foreach($this->settings['posttype'] as $post_type){
			add_meta_box($this->settings['id'], $this->settings['name'], "pagelines_metapanel_callback", $post_type, $this->settings['location'], $this->settings['priority'], array( $this ));
		}

		
	}

	/**
	 * Save Meta Options
	 * 
	 * Use tabs array to save options... 
	 * Need to identify if the option is being set to empty or has never been set
	 * *Section Control* gets its own saving schema
	 */
	function save_meta_options( $postID ){
	
		// Make sure we are saving on the correct post type...
	
		// Current post type is passed in $_POST
		$current_post_type = ( isset( $_POST['post_type'] ) ) ? $_POST['post_type'] : false;
		$post_type_save = ( is_array( $this->settings['posttype'] ) ) ? true : false;
		
		if((isset($_POST['update']) || isset($_POST['save']) || isset($_POST['publish'])) && $post_type_save){

			// Loop through tabs
			foreach($this->tabs as $tab => $t){
				// Loop through tab options
				foreach($t->options as $oid => $o){
				
					if($oid == 'section_control'){
						$this->save_sc( $postID );
					} else {
						
						// Note: If the value is null, then test to see if the option is already set to something
						// create and overwrite the option to null in that case (i.e. it is being set to empty)
						$option_value =  isset($_POST[$oid]) ? $_POST[$oid] : null;

						if(!empty($option_value) || get_post_meta($postID, $oid))
							update_post_meta($postID, $oid, $option_value );
						
					}
				}
			}
		}
	}
	
	
	function save_sc( $postID ){
		global $pagelines_template;

		global $post; 

		$save_template = new PageLinesTemplate();

	
		foreach( $save_template->map as $hook => $h ){

			if(isset($h['sections'])){
				foreach($h['sections'] as $key => $section_slug){
					$this->save_section_control($postID,  $section_slug, $hook );					
				}
			} elseif (isset($h['templates'])){
				foreach($h['templates'] as $template => $t){
					foreach($t['sections'] as $key => $section_slug){

						$template_slug = $hook.'-'.$template;
						$this->save_section_control($postID,  $section_slug, $template_slug );				
					}
				}
			}
			
		}
	
	}
	
	function save_section_control($postID,  $section_slug, $template_slug ){
		
		$pieces = explode("ID", $section_slug);		
		$section = (string) $pieces[0];
		$clone_id = (isset($pieces[1])) ? $pieces[1] : 1;
		
		$check_name_hide = PageLinesTemplateBuilder::sc_option_name( array('hide', $template_slug, $section, $clone_id) );

		$this->save_meta($postID, $check_name_hide);
		
		$check_name_show = PageLinesTemplateBuilder::sc_option_name( array('show', $template_slug, $section, $clone_id) );
	
		$this->save_meta($postID, $check_name_show);
		
	}
	
	function save_meta($postID, $name){
		
		$option_value =  isset($_POST[ $name ]) ? $_POST[ $name ] : null;
	
		if(!empty($option_value) || get_post_meta($postID, $name))
			update_post_meta($postID, $name, $option_value );
	}
	
	function draw_meta_options(){ 
		global $post_ID;  
		global $pagelines_template;
		
		
		// if page doesn't support settings
		if ( $this->page_for_posts ){
			$this->non_meta_template(); 
			return;
		}
		
		$option_engine = new OptEngine( 'meta' );
		
		$this->tabs_setup(); ?>
		
			<div id="metatabs" class="pagelines_metapanel fix">
				<div class="pagelines_metapanel_pad fix">
					<?php if(!$this->hide_tabs):?>
					<ul id="tabsnav" class="mp_tabs">
					
						<?php foreach($this->tabs as $tab => $t):?>
							<li>
								<a class="<?php echo $tab;?>  metapanel-tabn <?php if(!$t->active) echo 'inactive-tab';?>" href="#<?php echo $tab;?>">
									<span class="metatab_icon" style="background: url(<?php echo $t->icon; ?>) no-repeat 0 0;display: block;">
										<?php 
											echo $t->name;
											if(!$t->active) 
												printf('<span class="tab_inactive">inactive</span>');
										
										 ?>
									</span>
								</a>
							</li>
						<?php endforeach;?>
					</ul>
					<?php endif;?>
					<div class="mp_panel fix <?php if($this->hide_tabs) echo 'hide_tabs';?>">
						<div class="mp_panel_pad fix">
						
							<div class="pagelines_metapanel_options">
						
								<div class="pagelines_metapanel_options_pad">
									<?php foreach($this->tabs as $tab => $t):?>
										<div id="<?php echo $tab;?>" class="pagelines_metatab">
											<div class="metatab_title" style="background: url(<?php echo $t->icon; ?>) no-repeat 10px 13px;" >
												<?php 
												
													echo $t->name;
												
													if(!$t->active) 
														printf('<span class="tab_inactive">(inactive on template)</span>');
												 ?>
											</div>
											<?php 
											foreach($t->options as $oid => $o)
												$option_engine->option_engine($oid, $o, $post_ID);
											?>
									
										</div>
									<?php endforeach;?>
								</div>
							</div>
						</div>
					
					</div>
				</div>
				
			</div>
			<div class="ohead mp_footer ">
				<div class="mp_footer_pad fix ">
					<input type="hidden" name="_posttype" value="<?php echo $this->settings['posttype'];?>" />
				
				
					<div class="superlink-wrap osave-wrap">
						<input id="update" class="superlink osave" type="submit" value="<?php _e("Save Meta Settings",'pagelines'); ?>"  name="update" />
					</div>
				</div>
			</div>
			
		<?php 
	
	}
	
	function non_meta_template(){?>
		<div class="metapanel_banner">
			<p>
				<strong>Note:</strong> Individual page settings do not work on the blog page (<em>use the settings panel</em>).
			</p>
		</div>
		
	<?php }
		
	function tabs_setup(){
		if(!$this->hide_tabs):

			if(isset($_COOKIE['PageLinesMetaTabCookie']))
				$selected_tab = (int) $_COOKIE['PageLinesMetaTabCookie'];
			else
				$selected_tab = 0;
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {						
					var $myTabs = jQuery("#metatabs").tabs({ fx: { opacity: "toggle", duration: "fast" }, selected: <?php echo $selected_tab; ?>});

					jQuery('#metatabs').bind('tabsshow', function(event, ui) {
						var selectedTab = jQuery('#metatabs').tabs('option', 'selected');

						jQuery.cookie('PageLinesMetaTabCookie', selectedTab);
					});

				});
			</script>
		<?php endif;
	}


	
}
/////// END OF MetaOptions CLASS ////////


function pagelines_metapanel_callback($post, $object){

	$object['args'][0]->draw_meta_options();
	
}


function register_metatab($settings, $option_array, $location = 'bottom'){
	
	global $metapanel_options;
	
	$metapanel_options->register_tab($settings, $option_array, $location);
	
}


function add_global_meta_options( $meta_array = array(), $location = 'bottom'){
	global $global_meta_options;

	if($location == 'top')
		$global_meta_options = array_merge($meta_array, $global_meta_options);
	else
		$global_meta_options = array_merge($global_meta_options, $meta_array);
	
}

function do_global_meta_options(){
	global $global_meta_options;
	
	$metatab_settings = array(
			'id' => 'general_page_meta',
			'name' => "General Page Setup",
			'icon' =>  PL_ADMIN_ICONS . '/ileaf.png'
		);

	register_metatab($metatab_settings,  $global_meta_options, 'top');
}



