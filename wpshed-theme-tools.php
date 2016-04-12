<?php
/*
	Plugin Name: WPshed Theme Tools
	Plugin URI: https://wpshed.com/
	Description: Theme zip generator.
	Author: Stefan I.
	Author URI: https://wpshed.com/
	Version: 0.1
	Text Domain: wpshed
	License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


/**
 * Constants.
 */
define( 'WTT_PLUGIN_DIR', 	trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'WTT_PLUGIN_URI', 	trailingslashit( plugins_url( '', __FILE__ ) ) );


/**
 * Functions.
 */
require WTT_PLUGIN_DIR . 'wpshed-functions.php';
require WTT_PLUGIN_DIR . 'wpshed-zipper.php';
require WTT_PLUGIN_DIR . 'wpshed-cloner.php';


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

	wpshed_clone_theme_form();

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

	<?php submit_button( __( 'Generate Zip', 'wpshed' ), 'primary' ); ?>

	</form>

	<?php

	printf( '<p>%s <a href="%s" target="_blank">%s</a>.</p>',
		__( 'You can now submit your theme to WordPress.org by', 'wpshed' ),
		esc_url( 'https://wordpress.org/themes/upload/' ),
		__( 'clicking here', 'wpshed' )
	);

}


/**
 * Child theme form.
 */
function wpshed_clone_theme_form() {
	
	$current_theme 	= ( isset( $_GET['theme'] ) ) ? trim( $_GET['theme'] ) : get_option( 'template' );
	$all_themes 	= wp_get_themes();

	?>
	<h3><?php _e( 'Create a clone from your starter theme', 'wpshed' ); ?></h3>

	<form method="get">

	<table class="form-table">

		<input type="hidden" name="page" value="theme-tools" />

		<tr valign="top">
			<th scope="row"><?php _e( 'Select Theme', 'wpshed' ); ?></th>
			<td>
				<select name="theme">
				<?php foreach ( $all_themes as $key => $value ) : ?>
					<option value="<?php echo $key; ?>" <?php selected( $current_theme, $key ); ?>><?php echo $key; ?></option>
				<?php endforeach; ?>
				</select>	
				<?php submit_button( __( 'Select Theme', 'wpshed' ), 'secondary', '', '' ); ?>			
			</td>
		</tr>

	</table>

	</form>

	<?php if ( isset( $_GET['theme'] ) && $_GET['theme'] != '' && isset( $_GET['page'] ) && $_GET['page'] == 'theme-tools' ) : ?>
	<?php $theme = wp_get_theme( trim( $_GET['theme'] ) ); ?>

	<form method="post">

	<table class="form-table">

		<input type="hidden" name="wpshed_generate_clone" value="1" />
		<input type="hidden" name="theme_dir" value="<?php echo trim( $_GET['theme'] ); ?>" />

		<tr valign="top">
			<th scope="row"><?php _e( 'Theme Name', 'wpshed' ); ?></th>
			<td>
				<input type="text" name="theme_name" value="<?php echo $theme->get( 'Name' ); ?> Clone" class="regular-text" />				
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Theme URI', 'wpshed' ); ?></th>
			<td>
				<input type="text" name="theme_uri" value="<?php echo $theme->get( 'ThemeURI' ); ?>" class="regular-text" />				
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Description', 'wpshed' ); ?></th>
			<td>
				<textarea name="theme_desc" rows="3" cols="60"><?php echo $theme->get( 'Description' ); ?></textarea>			
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Author', 'wpshed' ); ?></th>
			<td>	
				<input type="text" name="theme_author" value="<?php echo $theme->get( 'Author' ); ?>" class="regular-text" />		
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Author URI', 'wpshed' ); ?></th>
			<td>
				<input type="text" name="author_uri" value="<?php echo $theme->get( 'AuthorURI' ); ?>" class="regular-text" />		
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Version', 'wpshed' ); ?></th>
			<td>	
				<input type="text" name="theme_version" value="1.0.0" class="regular-text" />		
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Theme Slug', 'wpshed' ); ?></th>
			<td>
			<input type="text" name="theme_slug" value="<?php echo $current_theme; ?>_clone" class="regular-text" />
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Theme Tags', 'wpshed' ); ?></th>
			<td>
				<?php $xxx = array_map( 'trim', $theme->get( 'Tags' ) );?>
				<select name="theme_tags[]" multiple style="width: 350px;height:300px;">
				<?php foreach ( wpshed_allowed_tags() as $tag ) : ?>
				<?php $selected = in_array( trim( $tag ), $xxx ) ? 'selected="selected"' : ''; ?>
					<option value="<?php echo trim( $tag ); ?>" <?php echo $selected; ?>><?php echo esc_attr( $tag ); ?></option>
				<?php endforeach; ?>
				</select>	
				<br>
				<span class="description">
					<?php _e( 'Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'wpshed' ); ?>
				</span>			
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Template', 'wpshed' ); ?></th>
			<td>	
				<input type="text" name="theme_template" value="<?php echo $theme->get( 'Template' ); ?>" class="regular-text" />		
				<br>
				<span class="description">
					<?php _e( '(Optional - used in a child theme) The folder name of the parent theme ', 'wpshed' ); ?>
				</span>	
			</td>
		</tr>

		<tr valign="top">
			<th scope="row"><?php _e( 'Include SASS files', 'wpshed' ); ?></th>
			<td>
			<label>
			<input type="checkbox" name="inc_sass" value="1" checked="checked">
			<?php _e( '_sassify!', 'wpshed' ); ?></label>				
			</td>
		</tr>

	</table>

	<?php submit_button( __( 'Clone Theme', 'wpshed' ), 'primary' ); ?>

	</form>

	<?php endif; ?>

	<?php

}
