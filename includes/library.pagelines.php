<?php


if ( !defined('VPRO' ) )
	define( 'VPRO', true );


add_action( 'pagelines_max_mem', create_function('',"@ini_set('memory_limit',WP_MAX_MEMORY_LIMIT);") );


function pl_include( $file )
{
	$filepath = PL_INCLUDES . "/$file.php";

	if ( defined('PL_DEV') && PL_DEV && !is_readable( $filepath ) )
		wp_die( "File not readable: $filepath", 'Missing/Unreadable File' );

	require_once( $filepath );
}

function pl_admin_include( $file )
{
	require_once( PL_ADMIN . "/$file.php" );
}

function pl_get_theme_data( $stylesheet = null, $header = 'Version')
{
	if ( function_exists( 'wp_get_theme' ) ) {
		return wp_get_theme( basename( $stylesheet ) )->get( $header );
	} else {
		$data = get_theme_data( sprintf( '%s/themes/%s/style.css', WP_CONTENT_DIR, basename( $stylesheet ) ) );
		return $data[ $header ];
	}
}

function pl_get_themes()
{
	if ( ! class_exists( 'WP_Theme' ) )
		return get_themes();

	$themes = wp_get_themes();

	foreach ( $themes as $key => $theme ) {
		$theme_data[$key] = array(
			'Name'			=> $theme->get('Name'),
			'URI'			=> $theme->display('ThemeURI', true, false),
			'Description'	=> $theme->display('Description', true, false),
			'Author'		=> $theme->display('Author', true, false),
			'Author Name'	=> $theme->display('Author', false),
			'Author URI'	=> $theme->display('AuthorURI', true, false),
			'Version'		=> $theme->get('Version'),
			'Template'		=> $theme->get('Template'),
			'Status'		=> $theme->get('Status'),
			'Tags'			=> $theme->get('Tags'),
			'Title'			=> $theme->get('Name'),
			'Template'		=> ( '' != $theme->display('Template', false, false) ) ? $theme->display('Template', false, false) : $key,
			'Stylesheet'	=> $key,
			'Stylesheet Files'	=> array(
				0 => sprintf( '%s/style.css' , $theme->get_stylesheet_directory() )
			)
		);
	}

	return $theme_data;
}


