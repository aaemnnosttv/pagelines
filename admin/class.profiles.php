<?php
/**
 * 
 *
 *  API for Working with WordPress User Profiles
 *
 *
 *  @package PageLines Core
 *  @since 2.0.b16
 *
 */
class ProfileEngine {

	public $tabs = array();
	public $current_tabs = array();

	function __construct( array $settings = array() ) { 
		
		
		add_action( 'edit_user_profile', array( &$this, 'admin_opts' ) );

		add_action( 'edit_user_profile', array( &$this, 'user_opts' ) );
		add_action( 'show_user_profile', array( &$this, 'user_opts' ) );
		
		add_action( 'edit_user_profile', array( &$this, 'do_panel' ) );
		add_action( 'show_user_profile', array( &$this, 'do_panel' ) );
		
	}
	
	function do_panel(){
	
		if( empty($this->current_tabs) )
			return;

		$set = array(
				'handle'	=> 'profiletabs',
				'title' 	=> 'PageLines Profile Options',
				'tag' 		=> false,
				'type'		=> 'profile',
				'stext' 	=> __("Save Profile Settings",'pagelines'),
				'tabs' 		=> $this->current_tabs
			);

		$panel = new PLPanel();

		$panel->the_panel( $set );
		
	}

	
	function admin_opts(  ){
	
		$this->current_tabs = array_merge($this->get_tabs('admin'), $this->current_tabs);

	}
	
	function user_opts(  ){
		
		$this->current_tabs = array_merge($this->current_tabs, $this->get_tabs('user'));

	}
	
	function get_tabs($role = 'user'){
	 
		$rtabs = array();
		foreach($this->tabs as $tid => $t){
			if($t->role == $role)
				$rtabs[$tid] = $t; 
		}
		
		return $rtabs;
	}
	
	/**
	 * Register a new tab for the meta panel
	 * This will look at Clone values and draw cloned tabs for cloned sections
	 *
	 * @since 2.0.b4
	 */
	function register_tab( $set, $location = 'bottom') {
		
		$d = array(
				'id' 		=> '',
				'opts'		=> array(),
				'name' 		=> '',
				'icon' 		=> '',
				'role'		=> 'user',
				'active'	=> true
			);

		$s = wp_parse_args($set, $d);
		
		$tab_id = $s['id'];
		
		if($location == 'top'){
			
			$top[$tab_id]->options = $s['opts'];
			$top[$tab_id]->icon = $s['icon'];
			$top[$tab_id]->active = $s['active'];
			$top[$tab_id]->name = $s['name'];
			$top[$tab_id]->role = $s['role'];
			
			$this->tabs = array_merge($top, $this->tabs);
			
		} else {
			$this->tabs[ $tab_id ]->options = $s['opts'];
			$this->tabs[ $tab_id ]->icon = $s['icon'];
			$this->tabs[ $tab_id ]->active = $s['active'];
			$this->tabs[ $tab_id ]->name = $s['name'];
			$this->tabs[ $tab_id ]->role = $s['role'];
		}

	}
	
}

function register_profile_tab( $set, $location = 'bottom' ){

	global $profile_panel_options;

	$profile_panel_options->register_tab($set, $location);

}