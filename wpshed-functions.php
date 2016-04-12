<?php
/**
 * Functions.
 *
 * @package WPshed Theme Tools
 * @author  Stefan I.
 * @license GPL-2.0+
 * @link    https://wpshed.com/
 */


/**
 * Change Theme name on theme switch.
 */
function wpshed_theme_name_changer() {
	$theme = wp_get_theme();
	if ( $theme->exists() ) {
		$blogname = $theme->Name . ' ' . __( 'Theme', 'wpshed' );
		$sanitized_blogname = wp_kses( $blogname, array() );
		update_option( 'blogname', $sanitized_blogname );
	}
}
add_action( 'switch_theme', 'wpshed_theme_name_changer' );


/**
 * Admin Notice.
 */
function wpshed_theme_clone_admin_notice() {

	$btn = '<button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button>';

    if ( isset( $_GET['page'] ) && $_GET['page'] == 'theme-tools' ) {

        // Settings saved message
        if ( isset( $_REQUEST['wpshed_generate_clone'] ) ) {
            printf( '<div class="notice is-dismissible"><p><strong>%1$s</strong></p>'. $btn .'</div>',
                __( 'Theme Clone Created!', 'wpshed' )
            );
        }

    }

}


/**
 * Copy a file, or recursively copy a folder and its contents.
 */
function xcopy( $source, $dest, $permissions = 0755 ) {
    
    // Check for symlinks
    if ( is_link( $source ) ) {
        return symlink( readlink( $source ), $dest );
    }

    // Simple copy for a file
    if ( is_file( $source ) ) {
        return copy( $source, $dest );
    }

    // Make destination directory
    if ( ! is_dir( $dest ) ) {
        mkdir( $dest, $permissions );
    }

    // Loop through the folder
    $dir = dir( $source );
    while ( false !== $entry = $dir->read() ) {
        // Skip pointers
        if ( $entry == '.' || $entry == '..' ) {
            continue;
        }

        // Deep copy directories
        xcopy( "$source/$entry", "$dest/$entry", $permissions );
    }

    // Clean up
    $dir->close();
    return true;
}


/**
 * WordPress.org Allowed Tags.
 */
function wpshed_allowed_tags() {

	$org_allowed_tags = array( 
		'black', 
		'blue', 
		'brown', 
		'gray', 
		'green', 
		'orange', 
		'pink', 
		'purple', 
		'red', 
		'silver', 
		'tan', 
		'white', 
		'yellow', 
		'dark', 
		'light', 
		'fixed-layout', 
		'fluid-layout', 
		'responsive-layout', 
		'one-column', 
		'two-columns', 
		'three-columns', 
		'four-columns', 
		'left-sidebar', 
		'right-sidebar', 
		'accessibility-ready', 
		'blavatar', 
		'buddypress', 
		'custom-background', 
		'custom-colors', 
		'custom-header', 
		'custom-menu', 
		'editor-style', 
		'featured-image-header',
		'featured-images', 
		'flexible-header', 
		'front-page-posting', 
		'full-width-template', 
		'microformats', 
		'post-formats', 
		'rtl-language-support', 
		'sticky-post', 
		'theme-options', 
		'threaded-comments', 
		'translation-ready', 
		'holiday', 
		'photoblogging', 
		'seasonal' 
	);

	return $org_allowed_tags;

}


/**
 * Get Directory Contents.
 */
function wpshed_get_dir_contents( $dir, &$results = array() ) {
    $files = scandir( $dir );

    foreach( $files as $key => $value ) {
        $path = realpath( $dir.DIRECTORY_SEPARATOR.$value );
        if( ! is_dir( $path ) ) {
            $results[] = $path;
        } else if( $value != "." && $value != ".." ) {
            wpshed_get_dir_contents( $path, $results );
            $results[] = $path;
        }
    }

    return $results;
}


/**
 * Exclude Files.
 */
function wpshed_exclude_files() {

	$exclude_files = array( 
		'.travis.yml', 
		'codesniffer.ruleset.xml', 
		'CONTRIBUTING.md', 
		'README.md', 
		'.git', 
		'.svn', 
		'.DS_Store', 
		'.gitignore',
		'customizer-examples.php', 
		'.', 
		'..',
		'.jscsrc',
		'.jshintignore',
	);

    return $exclude_files;
}


/**
 * Exclude Directories.
 */
function wpshed_exclude_directories() {

	$exclude_directories = array( '.git', '.svn', '.', '..' );

    return $exclude_directories;
}

