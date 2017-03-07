<?php
/**
 * Theme Zip Generator.
 *
 * @package WPshed Theme Tools
 * @author  Stefan I.
 * @license GPL-2.0+
 * @link    https://wpshed.com/
 */

add_action( 'init', 'wpshed_zip_generator' );


function wpshed_zip_generator() {

	if ( ! isset( $_REQUEST['wpshed_generate_zip'], $_REQUEST['theme_name'] ) )
		return;

	if ( empty( $_REQUEST['theme_name'] ) )
		wp_die( 'Please enter a theme name. Please go back and try again.' );

	$theme 			= array();
	$theme_name  	= trim( $_REQUEST['theme_name'] );
	$theme_sass  	= (bool) isset( $_REQUEST['inc_sass'] );

	$zip 			= new ZipArchive;
	$zip_filename 	= sprintf( '/tmp/wpshed-%s.zip', md5( print_r( $theme, true ) ) );
	$res 			= $zip->open( $zip_filename, ZipArchive::CREATE && ZipArchive::OVERWRITE );

	$prototype_dir 	= WP_CONTENT_DIR . '/themes/' . $theme_name . '/';

	$exclude_files = wpshed_exclude_files();
	$exclude_directories = wpshed_exclude_directories();

	if ( ! $theme_sass ) {
		$exclude_directories[] = 'sass';
		$exclude_files[] = 'style.scss';
		$exclude_files[] = 'genericons.scss';
		$exclude_files[] = 'editor-style.scss';
	}

	$iterator = new RecursiveDirectoryIterator( $prototype_dir );
	foreach ( new RecursiveIteratorIterator( $iterator ) as $filename ) {

		if ( in_array( basename( $filename ), $exclude_files ) )
			continue;

		foreach ( $exclude_directories as $directory )
			if ( strstr( $filename, "/{$directory}/" ) )
				continue 2; // continue the parent foreach loop

		$local_filename = str_replace( trailingslashit( $prototype_dir ), '', $filename );

		if ( 'languages/_w.pot' == $local_filename )
			$local_filename = sprintf( 'languages/%s.pot', $theme_name );

		$contents = file_get_contents( $filename );
		$contents = apply_filters( 'wpshed_zip_generator_file_contents', $contents, $local_filename );
		$zip->addFromString( trailingslashit( $theme_name ) . $local_filename, $contents );
	}

	$zip->close();

	header( 'Content-type: application/zip' );
	header( sprintf( 'Content-Disposition: attachment; filename="%s.zip"', $theme_name ) );
	readfile( $zip_filename );
	unlink( $zip_filename );/**/
	die();
}
