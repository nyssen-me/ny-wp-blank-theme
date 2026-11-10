<?php
/**
 * ACF settings: Local JSON, and a notice when ACF PRO is missing.
 *
 * Field groups are built in the ACF admin locally and saved to acf-json/ so they are
 * version-controlled. For staging and production they are exported to PHP files in
 * functions/acf/ and loaded from here.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Saves ACF field groups to the theme's acf-json/ folder.
 *
 * @return string
 */
function ny_acf_json_save_path() {
	return get_parent_theme_file_path( 'acf-json' );
}
add_filter( 'acf/settings/save_json', 'ny_acf_json_save_path' );

/**
 * Loads ACF field groups from the theme's acf-json/ folder.
 *
 * @return string[]
 */
function ny_acf_json_load_paths() {
	return [ get_parent_theme_file_path( 'acf-json' ) ];
}
add_filter( 'acf/settings/load_json', 'ny_acf_json_load_paths' );

/**
 * Makes field labels and instructions translatable with the theme text domain, including
 * in field groups exported to PHP.
 *
 * @return string
 */
function ny_acf_l10n_textdomain() {
	return 'ny-blank-theme';
}
add_filter( 'acf/settings/l10n_textdomain', 'ny_acf_l10n_textdomain' );

/**
 * Registers the Theme Settings options page (logo and footer details).
 */
function ny_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		[
			'page_title' => __( 'Theme General Settings', 'ny-blank-theme' ),
			'menu_title' => __( 'Theme Settings', 'ny-blank-theme' ),
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
		]
	);
}
add_action( 'acf/init', 'ny_acf_options_pages' );

/**
 * Warns administrators when ACF PRO is not active, as the theme's blocks need it.
 */
function ny_acf_missing_notice() {
	if ( function_exists( 'acf_register_block_type' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	wp_admin_notice(
		esc_html__( 'NY Blank Theme needs Advanced Custom Fields PRO 6.6 or later. The theme blocks are not available until it is active.', 'ny-blank-theme' ),
		[ 'type' => 'error' ]
	);
}
add_action( 'admin_notices', 'ny_acf_missing_notice' );
