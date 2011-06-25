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
		
		// Setup post, not auto-set for single post pages
		$post = (!isset($post) && isset($_GET['post'])) ? get_post($_GET['post'], 'object') : null;
		
		// Get current post type
		$posttype = ( isset( $_GET['post'] ) && ! in_array( get_post_type( $_GET['post'] ), apply_filters( 'pagelines_meta_blacklist', $this->blacklist ) ) ) 
						? array( 'post', 'page', get_post_type( $_GET['post'] ) ) 
						: array( 'post', 'page' );
		
		$this->non_meta = (!isset($post) || get_option( 'page_for_posts' ) === $post->ID ) ? true : false;
		
		// Only run the metapanel if it should be on this post type
		if(isset($post) && in_array($post->post_type, $posttype)){
		
			$this->ctemplate = ( isset($post) && 'page' == $post->post_type && 0 != count( get_page_templates() ) && !empty($post->page_template)) 
			 					? $post->page_template
								: ( $post->post_type == 'post' )
									? 'Single Post' 		
									: ( $this->non_meta ) 
										? 'Non Meta Page'
										: 'Default Template';
										
		}
		
		
			$defaults = array(
					'id' 		=> 'pagelines-metapanel',
					'name' 		=> 'PageLines Meta - '.$this->ctemplate,
					'posttype' 	=> $posttype,
					'location' 	=> 'normal', 
					'priority' 	=> 'low', 
					'hide_tabs'	=> false
				);
		
			$this->settings = wp_parse_args($settings, $defaults); // settings for post type
		
			$this->register_actions();
		
			$this->hide_tabs = $this->settings['hide_tabs'];
		
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
		if ( $this->non_meta )
			$this->non_meta_template(); return;
		
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

	function meta_option_engine($oid, $o){
		
		$defaults = array(
			'type'			=> '',
			'selectvalues' 	=> array(),
			'version'		=> '', 
			'where'			=> false, 
			'size'			=> 'reg',
			'title'			=> '',
			'desc'			=> null,
			'label'			=> '', 
			'exp'			=> null, 	
		);
		
		$o = wp_parse_args($o, $defaults);
		
		// Check if correct post type
		
			
			// Test Version
			if(VPRO || (!VPRO && $o['version'] != 'pro')){ ?>
				
				<div class="metapanel_option fix">
					<div class="metapanel_option_title">
						<div class="metatab_option_header"><?php echo $o['title']; ?></div>
						<?php if(isset($o['desc'])):?><div class="metatab_option_subheader"><?php echo $o['desc']; ?></div><?php endif;?>
					</div>
					<?php if( isset($o['exp']) ): ?>
						<div class="metapanel_option_exp">
							<div class="metapanel_option_exp_pad">
								<strong>More Info</strong><br/>
							<?php echo $o['exp']; ?>
							</div>
						</div>
					<?php endif;?>
					<div class="metapanel_option_input <?php if( isset($o['exp']) ) echo 'input_with_exp'; ?>">
						<div class="metapanel_option_input_pad" >
							<?php $this->option_breaker($oid, $o); ?>
						</div>
					</div>	
				</div>
		<?php }
					
	}
	
	function option_breaker($oid, $o){
		
		switch ( $o['type'] ){
		
			case 'select' :
				$this->_get_select_option($oid, $o);
				break;
			case 'select_count' :
				$this->_get_select_count_option($oid, $o);
				break;
			case 'select_taxonomy' :
				$this->_get_taxonomy_select($oid, $o);
				break;
			case 'select_menu' :
				$this->_get_menu_select($oid, $o);
				break;
			case 'textarea' :
				$this->_get_textarea_option($oid, $o);
				break;
			case 'text' :
				$this->_get_text_option($oid, $o);
				break;
			case 'check' :
				$this->_get_check_option($oid, $o);
				break;
			case 'image_upload' :
				$this->_get_image_upload_option($oid, $o);
				break;
			default :
				do_action( 'pagelines_metapanel_' . $o['type'] , $oid, $o);
				break;
		
		}
		
	}
	
	
	function _get_check_option($oid, $o){ global $post_ID;?>
			<?php if(isset($o['label'])):?><label class="metatext-label"><?php echo $o['label'];?></label><?php endif;?>
			<input class="admin_checkbox" type="checkbox" id="<?php echo $oid;?>" name="<?php echo $oid;?>" <?php checked((bool) m_pagelines($oid, $post_ID)); ?> />
<?php }
	
	function _get_text_option($oid, $o){ global $post_ID;?>
			<?php if(isset($o['label'])):?><label class="metatext-label"><?php echo $o['label'];?></label><?php endif;?>
			<input type="text" class="html-text <?php echo 'metatext-'.$o['size'];?>"  id="<?php echo $oid;?>" name="<?php echo $oid;?>" value="<?php em_pagelines($oid, $post_ID); ?>" />
<?php }

	function _get_textarea_option($oid, $o){ global $post_ID;?>
			<?php if(isset($o['label'])):?><label class="metatext-label"><?php echo $o['label'];?></label><?php endif;?>
			<textarea class="html-textarea <?php echo 'metatextarea-'.$o['size'];?>"  id="<?php echo $oid;?>" name="<?php echo $oid;?>" /><?php em_pagelines($oid, $post_ID); ?></textarea>
	<?php }
	
	function _get_select_option($oid, $o){  global $post_ID;?>
		<label class="context" for="<?php echo $oid;?>"><?php echo $o['label'];?></label>
			<select id="<?php echo $oid;?>" name="<?php echo $oid;?>">
				<option value="">&mdash;<?php _e("SELECT", 'pagelines');?>&mdash;</option>

				<?php foreach($o['selectvalues'] as $sval => $sset):?>
					<?php if($o['type']=='select_same'):?>
							<option value="<?php echo $sset;?>" <?php if(get_pagelines_meta($oid, $post_ID)==$sset) echo 'selected';?>><?php echo $sset;?></option>
					<?php elseif(is_array($sset)):
						$disabled_option = (isset($sset['version']) && $sset['version'] == 'pro' && !VPRO) ? true : false;
					?>
						<option <?php if($disabled_option) echo 'disabled="disabled" class="disabled_option"';?> value="<?php echo $sval;?>" <?php if(get_pagelines_meta($oid, $post_ID)==$sval) echo 'selected';?>><?php echo $sset['name']; if($disabled_option) echo ' (pro)';?></option>
					<?php else:?>
							<option value="<?php echo $sval;?>" <?php if(get_pagelines_meta($oid, $post_ID)==$sval) echo 'selected';?>><?php echo $sset;?></option>
					<?php endif;?>

				<?php endforeach;?>
			</select>

		
	<?php }
	
	function _get_select_count_option($oid, $o){  global $post_ID;?>
		
		<select id="<?php echo $oid;?>" name="<?php echo $oid;?>">
			<option value="">&mdash;SELECT&mdash;</option>
			<?php if(isset($o['count_start'])): $count_start = $o['count_start']; else: $count_start = 0; endif;?>
			<?php for($i = $count_start; $i <= $o['count_number']; $i++):?>
					<option value="<?php echo $i;?>" <?php selected($i, get_pagelines_meta($oid, $post_ID)); ?>><?php echo $i;?></option>
			<?php endfor;?>
		</select>
	<?php }
	
	
	function _get_taxonomy_select($oid, $o){ 
		
		global $post_ID;
		$terms_array = get_terms( $o['taxonomy_id']); ?> 

		<?php if(is_array($terms_array) && !empty($terms_array)):?>
		
				<select id="<?php echo $oid;?>" name="<?php echo $oid;?>">
					<option value="">&mdash;<?php _e("SELECT", 'pagelines');?>&mdash;</option>
					<?php foreach($terms_array as $term):?>
						<option value="<?php echo $term->slug;?>" <?php if(get_pagelines_meta($oid, $post_ID)==$term->slug) echo 'selected';?>><?php echo $term->name; ?></option>
					<?php endforeach;?>
				</select>
			
		<?php else:?>
			<div class="meta-message">No sets have been created and added to a post yet!</div>
		<?php endif;?>
		
	<?php }
	
	function _get_image_upload_option($oid, $o){ 
		
		global $post_ID;
		
		?>
		
			<p>
				<label class="context" for="<?php echo $oid;?>"><?php echo $o['label'];?></label>
				<input class="regular-text uploaded_url" type="text" name="<?php echo $oid;?>" value="<?php em_pagelines($oid, $post_ID); ?>" /><br/><br/>


				<span id="<?php echo $oid;?>" class="image_upload_button button">Upload Image</span>
				<span title="<?php echo $oid;?>" id="<?php echo $oid;?>" class="image_reset_button button">Remove</span>
				<input type="hidden" class="ajax_action_url" name="wp_ajax_action_url" value="<?php echo admin_url("admin-ajax.php"); ?>" />
				<input type="hidden" class="image_preview_size" name="img_size_<?php echo $oid;?>" value="100"/>
			</p>
			<?php if(m_pagelines($oid, $post_ID)):?>
				<img class="pagelines_image_preview" id="image_<?php echo $oid;?>" src="<?php em_pagelines($oid, $post_ID); ?>" style="max-width: 100px"/>
			<?php endif;?>

		
	<?php }
	
	function _get_menu_select($oid, $o){ global $post_ID; ?>
		
		<?php if(isset($o['label'])):?><label class="metatext-label"><?php echo $o['label'];?></label><?php endif;?>
		<select id="<?php echo $oid;?>" name="<?php echo $oid;?>">
			<option value="" >&mdash;SELECT&mdash;</option>
			<?php	$menus = wp_get_nav_menus( array('orderby' => 'name') );
					foreach ( $menus as $menu )
						printf( '<option value="%d" %s>%s</option>', $menu->term_id, selected($menu->term_id, get_pagelines_meta($oid, $post_ID)), esc_html( $menu->name ) );
			?>
		</select>
		
	
		
	<?php }
	
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



