<?php
/**
 * Front-end and editor assets.
 *
 * Styles for block content must load via block.json or add_editor_style(), never via
 * enqueue_block_editor_assets, which loads outside the iframed editor canvas.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Returns a version string for a theme asset, based on when the file last changed.
 *
 * @param string $path Path relative to the theme folder.
 * @return string|false Version string, or false when the file does not exist.
 */
function ny_asset_version( $path ) {
	$file = get_theme_file_path( $path );

	return file_exists( $file ) ? (string) filemtime( $file ) : false;
}

/**
 * Enqueues the main stylesheet and script on the front end. Both are compiled by Prepros.
 *
 * Block styles are not included here: each block loads its own style.css via block.json.
 */
function ny_enqueue_assets() {
	$style         = 'assets/css/style-min.css';
	$style_version = ny_asset_version( $style );

	if ( false !== $style_version ) {
		wp_enqueue_style( 'ny-main', get_theme_file_uri( $style ), [], $style_version );
	}

	$script         = 'assets/js/main-min.js';
	$script_version = ny_asset_version( $script );

	if ( false !== $script_version ) {
		wp_enqueue_script(
			'ny-main',
			get_theme_file_uri( $script ),
			[],
			$script_version,
			[
				'in_footer' => true,
				'strategy'  => 'defer',
			]
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ny_enqueue_assets' );

/**
 * Enqueues editor JavaScript. It runs in the editor window, not in the canvas iframe.
 */
function ny_enqueue_editor_scripts() {
	$path = 'assets/js/editor.js';

	wp_enqueue_script(
		'ny-editor',
		get_theme_file_uri( $path ),
		[ 'wp-data', 'wp-dom-ready', 'wp-format-library', 'wp-rich-text' ],
		ny_asset_version( $path ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'ny_enqueue_editor_scripts' );
