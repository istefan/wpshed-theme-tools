<?php
/**
 * Theme Theme Clone Generator.
 *
 * @package WPshed Theme Tools
 * @author  Stefan I.
 * @license GPL-2.0+
 * @link    https://wpshed.com/
 */

add_action( 'init', 'wpshed_theme_clone_generator' );


function wpshed_theme_clone_generator() {

	if ( ! isset( $_REQUEST['wpshed_generate_clone'], $_REQUEST['theme_name'] ) )
		return;

	if ( empty( $_REQUEST['theme_name'] ) )
		wp_die( 'Please enter a theme name. Please go back and try again.', 'wpshed' );

	$theme 			= array();
	$theme_dir  	= trim( $_REQUEST['theme_dir'] );
	$theme_name  	= trim( $_REQUEST['theme_name'] );
	$theme_slug  	= sanitize_title_with_dashes( trim( $_REQUEST['theme_slug'] ) );
	$theme_uri  	= esc_url_raw( trim( $_REQUEST['theme_uri'] ) );
	$theme_desc  	= esc_html( trim( $_REQUEST['theme_desc'] ) );
	$author_uri  	= esc_url_raw( trim( $_REQUEST['author_uri'] ) );
	$theme_author  	= trim( $_REQUEST['theme_author'] );
	$theme_version  = trim( $_REQUEST['theme_version'] );
	$theme_tags  	= isset( $_REQUEST['theme_tags'] ) ? $_REQUEST['theme_tags'] : array();
	$theme_template = trim( $_REQUEST['theme_template'] );
	$theme_sass  	= (bool) isset( $_REQUEST['inc_sass'] );

	$slug 			= str_replace( '-', '_', $theme_slug );

	$permissions 	= 0755;
	$prototype_dir 	= WP_CONTENT_DIR . '/themes/' . $theme_dir . '/';
	$clone_dir 		= WP_CONTENT_DIR . '/themes/' . $slug;

	// create child theme directory if not exists
	if ( ! file_exists( $clone_dir ) ) {
	    mkdir( $clone_dir, $permissions, true );
	} else {
		wp_die( 'Please go back and change the theme slug. This Theme already exists.', 'wpshed' );
	}

	$exclude_files 			= wpshed_exclude_files();
	$exclude_directories 	= wpshed_exclude_directories();

	if ( ! $theme_sass ) {
		$exclude_directories[] = 'sass';
		$exclude_files[] = 'style.scss';
		$exclude_files[] = 'editor-style.scss';
	}

	// Create new theme directory structure
	$dir_iterator = new RecursiveIteratorIterator(
	    new RecursiveDirectoryIterator( $prototype_dir, RecursiveDirectoryIterator::SKIP_DOTS),
	    RecursiveIteratorIterator::SELF_FIRST,
	    RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
	);
	$paths = array( $prototype_dir );
	foreach ( $dir_iterator as $path => $dir ) {

		$local_dir = str_replace( trailingslashit( $prototype_dir ), '', $path );

		if ( in_array( $local_dir, $exclude_directories ) )
			continue;

		foreach ( $exclude_directories as $directory )
			if ( strstr( $path, "/{$directory}/" ) )
				continue 2; // continue the parent foreach loop

	    if ( $dir->isDir() )
	    	mkdir( trailingslashit( $clone_dir ) . $local_dir, $permissions, true );

	}

	// Copy theme files
	$iterator = new RecursiveDirectoryIterator( $prototype_dir );
	foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

		if ( in_array( basename( $filename ), $exclude_files ) )
			continue;

		foreach ( $exclude_directories as $directory )
			if ( strstr( $filename, "/{$directory}/" ) )
				continue 2; // continue the parent foreach loop

		$local_filename = str_replace( trailingslashit( $prototype_dir ), '', $filename );

		if ( 'languages/'. $theme_dir .'.pot' == $local_filename )
			$local_filename = sprintf( 'languages/%s.pot', $slug );

		xcopy( $filename, trailingslashit( $clone_dir )  . $local_filename, $permissions );

		// Replace text
		$theme = wp_get_theme( $theme_dir );
		$old_name = $theme->get( 'Name' );
		$old_slug = $theme_dir;

		$theme_file = trailingslashit( $clone_dir )  . $local_filename;
		$str = file_get_contents( $theme_file );
		// Theme Name
		$str = str_replace( $old_name, $theme_name, $str );

		// Theme Slug
		$str = str_replace( $old_slug, $slug, $str );

		// Theme URI
		$old_uri = '/^Theme URI:\s?([a-z\:\/\.]+)$/mi';
		$new_uri = 'Theme URI: ' . $theme_uri;
		$str = preg_replace( $old_uri, $new_uri, $str );

		// Theme Description
		$old_desc = '/^Description:\s?[a-z,\s-]+$/mi';
		$new_desc = 'Description: ' . $theme_desc;
		$str = preg_replace( $old_desc, $new_desc, $str );

		// Author
		$old_author = '/^Author:\s?([a-z\:\/\.]+)$/mi';
		$new_author = 'Author: ' . $theme_author;
		$str = preg_replace( $old_author, $new_author, $str );

		// Author URI
		$old_author_uri = '/^Author URI:\s?([a-z\:\/\.]+)$/mi';
		$new_author_uri = 'Author URI: ' . $author_uri;
		$str = preg_replace( $old_author_uri, $new_author_uri, $str );

		// Version
		$old_version = '/Version:.*/';
		$new_version = 'Version: ' . $theme_version;
		$str = preg_replace( $old_version, $new_version, $str );

		// Text Domain
		$old_slug = '/^Text Domain:\s?([a-z\:\/\.]+)$/mi';
		$new_slug = 'Text Domain: ' . $slug;
		$str = preg_replace( $old_slug, $new_slug, $str );

		// Template
		$old_template = '/^Template:\s?([a-z\:\/\.]+)$/mi';
		$new_template = 'Template: ' . $theme_template;
		$str = preg_replace( $old_template, $new_template, $str );

		// Tags
		$old_tags = '/^Tags:\s?[a-z,\s-]+$/mi';
		$new_tags = 'Tags: ' . implode( ', ', $theme_tags );
		$str = preg_replace( $old_tags, $new_tags, $str );

		file_put_contents( $theme_file, $str );
	}

	// Let me know if all is OK!
	add_action( 'admin_notices', 'wpshed_theme_clone_admin_notice' );
		
}
