<?php
/**
 * NY Blank Theme functions and definitions.
 *
 * This file only loads the files in functions/. Add new functionality there.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

define( 'NY_THEME_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );

$ny_includes = [
	'setup',
	'cleanup',
	'admin',
	'navigation',
	'template-tags',
	'enqueue',
	'editor',
	'acf',
	'blocks',
	'post-templates',
	'patterns',
];

foreach ( $ny_includes as $ny_file ) {
	require_once get_parent_theme_file_path( "functions/{$ny_file}.php" );
}

unset( $ny_includes, $ny_file );
