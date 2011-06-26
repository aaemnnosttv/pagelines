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
		 * Only load on new post or post edit pages
		 * Makes sure there are no errors outside it, and improves processing
		 */
		if($pagenow == 'post.php' || $pagenow == 'post-new.php'){
	
			/**
			 * Single post pages have post as GET, not $post as object
			 */
			$post = (!isset($post) && isset($_GET['post'])) ? get_post($_GET['post'], 'object') : null;
	
			/**
			 * Get current post type, set as GET on 'add new' pages
			 */
			$this->ptype = ( !isset($post) && isset($_GET['post_type']) ) 
								? $_GET['post_type'] 
								: ( isset($post) && isset($post->post_type) ? $post->post_type : 
									($pagenow == 'post-new.php' ? 'post' : null));		
							
		
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
		
	}
	
	function get_the_post_types(){
		$the_post_types = ( isset( $_GET['post'] ) && ! in_array( get_post_type( $_GET['post'] ), apply_filters( 'pagelines_meta_blacklist', $this->blacklist ) ) ) 
						? array( 'post', 'page', get_post_type( $_GET['post'] ) ) 
						: array( 'post', 'page' );
		
		return $the_post_types;
	}
	
	function get_the_title(){
		global $post;
		$this->base_name = 'PageLines Meta Settings';
		$name = $this->base_name;
		
		if($this->ptype == 'post' || $this->ptype == 'page'){
			$current_template = (isset($post)) ? get_post_meta($post->ID, '_wp_page_template', true) : false;
	
			$this->page_templates = array_flip($this->get_page_templates());
		
			if(  $this->ptype == 'page' && $current_template && $current_template != 'default') {

				if(isset($this->page_templates[$current_template]))
					$slug = $this->page_templates[$current_template];
		
			}elseif(  $this->ptype == 'page' )
				$slug = 'Default';
			elseif( $this->ptype == 'post' )
				$slug = 'Single Post';
			elseif( $this->page_for_posts )
				$slug = 'Blog Page';
			else 
				$slub = '';
		
			$name .= sprintf(' <small style="font-style:italic">(%s)</small>', $slug);
		} 
		
		return $name;
	}
	
	function register_tab( $option_settings = array(), $option_array = array(), $location = 'bottom') {
		
		$key = $option_settings['id'];
		
		if($location == 'top'){
			
			$top[$key]->options = $option_array;
			$top[$key]->icon = $option_settings['icon'];
			$top[$key]->name = $option_settings['name'];

			$this->tabs = array_merge($top, $this->tabs);
			
		} else {
			$this->tabs[$key]->options = $option_array;
			$this->tabs[$key]->icon = $option_settings['icon'];
			$this->tabs[$key]->name = $option_settings['name'];
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

	function save_meta_options($postID){
	
		// Make sure we are saving on the correct post type...
	
		// Current post type is passed in $_POST
		$current_post_type = ( isset( $_POST['post_type'] ) ) ? $_POST['post_type'] : false;
		$post_type_save = ( is_array( $this->settings['posttype'] ) ) ? true : false;
		
		if((isset($_POST['update']) || isset($_POST['save']) || isset($_POST['publish'])) && $post_type_save){

			// Loop through tabs
			foreach($this->tabs as $tab => $t){
				// Loop through tab options
				foreach($t->options as $oid => $o){
				
					// Note: If the value is null, then test to see if the option is already set to something
					// create and overwrite the option to null in that case (i.e. it is being set to empty)
					$option_value =  isset($_POST[$oid]) ? $_POST[$oid] : null;

					if(!empty($option_value) || get_post_meta($postID, $oid)){
						update_post_meta($postID, $oid, $option_value );
					}
				}
			}
		}
	}
	
	function draw_meta_options(){ 
		global $post_ID;  
		
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
								<a class="<?php echo $tab;?>  metapanel-tab" href="#<?php echo $tab;?>">
									<span class="metatab_icon" style="background: url(<?php echo $t->icon; ?>) no-repeat 0 0;display: block;"><?php echo $t->name; ?></span>
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
											<div class="metatab_title" style="background: url(<?php echo $t->icon; ?>) no-repeat 10px 13px;" ><?php echo $t->name; ?></div>
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

	
	
	/**
	 * This was taken from core WP because the function hasn't loaded yet, and isn't accessible.
	 */
	function get_page_templates() {
		$themes = get_themes();
		$theme = get_current_theme();
		$templates = $themes[$theme]['Template Files'];
		$page_templates = array();

		if ( is_array( $templates ) ) {
			$base = array( trailingslashit(get_template_directory()), trailingslashit(get_stylesheet_directory()) );

			foreach ( $templates as $template ) {
				$basename = str_replace($base, '', $template);

				// don't allow template files in subdirectories
				if ( false !== strpos($basename, '/') )
					continue;

				if ( 'functions.php' == $basename )
					continue;

				$template_data = implode( '', file( $template ));

				$name = '';
				if ( preg_match( '|Template Name:(.*)$|mi', $template_data, $name ) )
					$name = _cleanup_header_comment($name[1]);

				if ( !empty( $name ) ) {
					$page_templates[trim( $name )] = $basename;
				}
			}
		}

		return $page_templates;
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


function add_global_meta_options( $meta_array = array()){
	global $global_meta_options;
	
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



