<?php
/**
 * 
 *
 *  PageLines Custom Post Type Class
 *
 *
 *  @package PageLines Core
 *  @subpackage Post Types
 *  @since 4.0
 *
 */
class PageLinesPostType {

	var $id;		// Root id for section.
	var $settings;	// Settings for this section
	
	
	/**
	 * PHP5 constructor
	 *
	 */
	function __construct($id, $settings, $taxonomies = array(), $columns = array(), $column_display_function = '') {
		$this->id = $id;
		$this->taxonomies = $taxonomies;
		$this->columns = $columns;
		$this->columns_display_function = $column_display_function;
		
		$defaults = array(
				
				'label' 			=> 'Posts',
				'singular_label' 	=> 'Post',
				'description' 		=> null,
				'public' 			=> false,  
				'show_ui' 			=> true,  
				'capability_type'	=> 'post',  
				'hierarchical' 		=> false,  
				'rewrite' 			=> false,  
				'supports' 			=> array('title', 'editor', 'thumbnail'), 
				'menu_icon' 		=> PL_ADMIN_IMAGES . "/favicon-pagelines.ico", 
				'taxonomies'		=> array(),
				'menu_position'		=> 20, 
				'featured_image'	=> false
			);
		
		$this->settings = wp_parse_args($settings, $defaults); // settings for post type
		
		$this->register_post_type();
		$this->register_taxonomies();
		$this->register_columns();
		
		$this->featured_image();
	
	}
	
	function featured_image(){	
		
		if( $this->settings['featured_image'] )
			add_filter('pl_support_featured_image', array(&$this, 'add_featured_image'));

	}
	
	function add_featured_image( $support_array ){
		
		$support_array[] = $this->id;
		return $support_array;
		
	}
	
	function register_post_type(){
		register_post_type( $this->id , array(  
				'labels' => array(
							'name' 			=> $this->settings['label'],
							'singular_name' => $this->settings['singular_label'],
							'add_new'		=> __('Add New ', 'pagelines') . $this->settings['singular_label'], 
							'add_new_item'	=> __('Add New ', 'pagelines') . $this->settings['singular_label'], 
							'edit'			=> __('Edit ', 'pagelines') . $this->settings['singular_label'],
							'edit_item'		=> __('Edit ', 'pagelines') . $this->settings['singular_label'], 
							'view'			=> __('View ', 'pagelines') . $this->settings['singular_label'],
							'view_item'		=> __('View ', 'pagelines') . $this->settings['singular_label'],
						),
			
	 			'label' 			=> $this->settings['label'],  
				'singular_label' 	=> $this->settings['singular_label'],
				'description' 		=> $this->settings['description'],
				'public' 			=> $this->settings['public'],  
				'show_ui' 			=> $this->settings['show_ui'],  
				'capability_type'	=> $this->settings['capability_type'],  
				'hierarchical' 		=> $this->settings['hierarchical'],  
				'rewrite' 			=> $this->settings['rewrite'],  
				'supports' 			=> $this->settings['supports'], 
				'menu_icon' 		=> $this->settings['menu_icon'], 
				'taxonomies'		=> $this->settings['taxonomies'],
				'menu_position'		=> $this->settings['menu_position'],
				'capabilities' => array(
			        'publish_posts' => 'manage_options',
			        'edit_posts' => 'manage_options',
			        'edit_others_posts' => 'manage_options',
			        'delete_posts' => 'manage_options',
			        'delete_others_posts' => 'manage_options',
			        'read_private_posts' => 'manage_options',
			        'edit_post' => 'manage_options',
			        'delete_post' => 'manage_options',
			        'read_post' => 'manage_options',
			    ),
				
			));
	}
	
	function register_taxonomies(){
		
		foreach($this->taxonomies as $tax_id => $tax_settings){
			
			register_taxonomy($tax_id, array($this->id), array(
					"hierarchical" => true, 
					"label" => $tax_settings['label'], 
					"singular_label" => $tax_settings['singular_label'], 
					"rewrite" => true
				)
			);
		}
		
	}
	
	function register_columns(){
		
		add_filter("manage_edit-{$this->id}_columns", array(&$this, 'set_columns'));
		
		add_action("manage_posts_custom_column",  array(&$this, 'set_column_values'));
	}
		
	function set_columns($columns){ 
		
		return $this->columns; 
	}
	
	function set_column_values($wp_column){
		
		call_user_func($this->columns_display_function, $wp_column);
						
	}
	
	function set_default_posts( $callback, $object = false){
	
		if(!get_posts('post_type='.$this->id)){

			if($object)
				call_user_func( array($object, $callback), $this->id);
			else
				call_user_func($callback, $this->id);
		}
						
	}
	
	
	

}
/////// END OF PostType CLASS ////////

