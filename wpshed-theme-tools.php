<?php
/*
	Plugin Name: WPshed Theme Tools
	Plugin URI: https://wpshed.com/
	Description: Theme zip generator.
	Author: Stefan I.
	Author URI: https://wpshed.com/
	Version: 1.0
	Text Domain: wpshed
	License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
 * Functions.
 */
require trailingslashit( plugin_dir_path( __FILE__ ) ) . 'wpshed-zipper.php';


/**
 * Admin menu.
 */
function wpshed_zipper_menu () {
	add_management_page( 
		__( 'Theme Tools', 'wpshed' ), 
		__( 'Theme Tools', 'wpshed' ), 
		'manage_options', 
		'theme-tools', 
		'wpshed_zipper_page'
	);
}
add_action( 'admin_menu', 'wpshed_zipper_menu' );


/**
 * Options page.
 */
function wpshed_zipper_page() {

	echo '<div class="wrap">';

	printf( '<h1>%s</h1>', __( 'WPshed Theme Tools', 'wpshed' ) );

	wpshed_zipper_form();

	echo '</div>';

}

 
/**
 * Zipper form.
 */
function wpshed_zipper_form() {

	$current_theme 	= get_option( 'template' );
	$all_themes 	= wp_get_themes();

	?>
	<h3><?php _e( 'Create a clean *.zip archive for any of the themes', 'wpshed' ); ?></h3>

	<form method="post">

	<table class="form-table">

		<input type="hidden" name="wpshed_generate_zip" value="1" />

		<tr valign="top">
			<th scope="row"><?php _e( 'Select Theme', 'wpshed' ); ?></th>
			<td>
				<select name="theme_name">
				<?php foreach ( $all_themes as $key => $value ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $current_theme, $key ); ?>><?php echo $key; ?></option>
				<?php endforeach; ?>
				</select>				
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Include SASS files', 'wpshed' ); ?></th>
			<td>
			<label>
			<input type="checkbox" name="inc_sass" value="1">
			<?php _e( '_sassify!', 'wpshed' ); ?></label>				
			</td>
		</tr>

	</table>

	<?php submit_button( __( 'Generate Zip File', 'wpshed' ), 'primary' ); ?>

	</form>

	<?php

	printf( '<p>%s <a href="%s" target="_blank">%s</a>.</p>',
		__( 'Submit your theme to WordPress.org by', 'wpshed' ),
		esc_url( 'https://wordpress.org/themes/upload/' ),
		__( 'clicking here', 'wpshed' )
	);

}
