<?php
/**
 * Admin and login screen: login page branding and SVG uploads.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Brands the login page: the logo from Theme Settings replaces the WordPress logo, and
 * assets/css/login.css (if it exists) adds the rest of the styling.
 */
function ny_login_styles() {
	$path = 'assets/css/login.css';

	if ( file_exists( get_theme_file_path( $path ) ) ) {
		wp_enqueue_style( 'ny-login', get_theme_file_uri( $path ), [ 'login' ], ny_asset_version( $path ) );
	}

	$logo = function_exists( 'get_field' ) ? ny_kses_svg( (string) get_field( 'main_logo', 'option' ) ) : '';

	if ( $logo ) {
		wp_add_inline_style(
			'login',
			sprintf(
				'.login h1 a { background-image: url("data:image/svg+xml;base64,%s"); background-size: contain; background-position: center; width: 100%%; height: 8rem; }',
				base64_encode( $logo ) // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Encodes the logo as a data URI.
			)
		);
	}
}
add_action( 'login_enqueue_scripts', 'ny_login_styles' );

// The login logo links to the site, with the site name as its text.
add_filter( 'login_headerurl', fn() => home_url( '/' ) );
add_filter( 'login_headertext', fn() => get_bloginfo( 'name' ) );

// No language switcher on the login page.
add_filter( 'login_display_language_dropdown', '__return_false' );

/**
 * Allows SVG uploads for administrators only.
 *
 * WordPress does not sanitise SVG files, which can contain scripts, so the upload is not
 * offered to other roles (on a single site Editors also have unfiltered_html, so that
 * capability is not enough of a check).
 *
 * @param array $mimes Allowed mime types.
 * @return array
 */
function ny_svg_upload_mimes( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'ny_svg_upload_mimes' );
