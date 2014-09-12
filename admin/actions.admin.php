<?php
/**
 * actions.admin.php
 */

/**
 * Show Options Panel after theme activation
 *
 * @package PageLines Framework
 * @subpackage Redirects
 * @since 1.0.0
 */
if( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" )
	wp_redirect( admin_url( PL_DASH_URL.'&activated=true&pageaction=activated' ) );

/**
 * Add Javascript for Layout Controls from the Layout UI class
 *
 * @package PageLines Framework
 * @subpackage LayoutUI
 * @since 2.0.b3
 */
$layout_control_js = new PageLinesLayoutControl();
add_action( 'pagelines_admin_head', array(&$layout_control_js, 'layout_control_javascript' ) );


/**
 * Admin Body Class
 *
 * Adds the 'pagelines_ui' class
 *
 * @package PageLines Framework
 * @since   ...
 *
 * @param   $class
 *
 * @return  string
 */
function pagelines_admin_body_class( $class )
{
	return "{$class}pagelines_ui";
}


/**
 * Inline Help
 *
 * Load Inline help system.
 *
 * @package PageLines Framework
 * @since   ...
 *
 * @uses    PageLines_Inline_Help
 */
function pagelines_inline_help()
{
	global $pl_help;
	if ( !($pl_help instanceof PageLines_Inline_Help) )
		$pl_help = new PageLines_Inline_Help;

	return $pl_help;
}
add_action( 'admin_init', 'pagelines_inline_help' );

/**
 * Page Columns
 *
 * Add custom columns to page/post views.
 *
 * @package PageLines Framework
 * @since   2.1.3
 *
 * @param   $columns
 * @return  array
 */
function pl_page_columns( $columns )
{
	$columns['template'] = 'PageLines Template';
	return $columns;
}
add_filter( 'manage_edit-page_columns', 'pl_page_columns' );

/**
 * Post Columns
 *
 * @package PageLines Framework
 * @since   2.1.3
 *
 * @param   $columns
 * @return  array
 */
function pl_post_columns( $columns )
{
	$columns['feature'] = 'Featured Image';
	return $columns;
}
add_filter( 'manage_edit-post_columns', 'pl_post_columns' );

/**
 * Posts Show Columns
 *
 * @package PageLines Framework
 * @since   ...
 *
 * @param   $name
 *
 */
function pl_posts_show_columns( $name )
{
    switch ( $name )
    {
		case 'feature':
			if ( has_post_thumbnail( $post->ID ) )
				the_post_thumbnail( array(48,48) );

			break;
    }
}
add_action( 'manage_posts_custom_column', 'pl_posts_show_columns' );

/**
 * Page Show Columns
 *
 * @package PageLines Framework
 * @since   2.1.3
 *
 * @param   $name
 *
 * @uses    pl_file_get_contents
 */
function pl_page_show_columns($name)
{
    global $post;
    switch ( $name )
    {
        case 'template':
            $template = get_post_meta( $post->ID, '_wp_page_template', true );

			if ( 'default' == $template ) {
				_e( 'Default', 'pagelines' );
				break;
			}

			if ( !$file = locate_template( array( $template ) ) )
			{
				printf( '<a href="%s">%s</a>',
					get_edit_post_link( $post->ID ),
					__( 'No Template Assigned', 'pagelines' )
				);
				break;
			}

			$data = get_file_data( $file, array( 'name' => 'Template Name' ) );

			if ( isset( $data['name'] ) )
				$template = $data['name'];
			else
				$template = __( 'Default', 'pagelines' );

			echo $template;
			break;

		case 'feature':
			if ( has_post_thumbnail( $post->ID ) )
				the_post_thumbnail( array(48,48) );

			break;
    }
}
add_action( 'manage_pages_custom_column', 'pl_page_show_columns' );

/**
 * Setup Versions and flush caches.
 *
 * @package PageLines Framework
 * @since   2.2
 */
function pagelines_set_versions()
{
	if ( !pl_validate_section_cache() )
		pl_purge_section_cache();

	if ( current_user_can( 'edit_theme_options' ) && pl_less_dev() )
		pl_purge_css();
}
add_action( 'admin_init', 'pagelines_set_versions' );

// make sure we're running out of 'pagelines' folder.
function pagelines_check_folders()
{
	$folder = basename( get_template_directory() );

	if ( 'pagelines' == $folder )
		return;

	echo '<div class="updated">';
	printf( "<p><h3>Install Error!</h3><br />PageLines Framework must be installed in a folder called 'pagelines' to work with child themes and extensions.<br /><br />Current path: %s<br /></p>", get_template_directory() );
	echo '</div>';
}
add_action( 'admin_notices', 'pagelines_check_folders' );