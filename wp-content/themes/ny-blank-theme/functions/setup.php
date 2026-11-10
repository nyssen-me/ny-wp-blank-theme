<?php
/**
 * Theme setup: theme supports, menus, image sizes, sidebars and editor styles.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers theme supports, menus and image sizes.
 */
function ny_setup() {
	load_theme_textdomain( 'ny-blank-theme', get_parent_theme_file_path( 'languages' ) );

	// No automatic-feed-links, so WordPress does not output feed links in the head.
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'gallery', 'caption', 'navigation-widgets' ] );

	// The front-end stylesheet also loads inside the iframed editor canvas, so block previews
	// match the front end. WordPress scopes it to the canvas and fixes relative url() paths.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/style-min.css' );

	register_nav_menus(
		[
			'primary' => __( 'Primary menu', 'ny-blank-theme' ),
			'footer'  => __( 'Footer menu', 'ny-blank-theme' ),
		]
	);

	/*
	 * Image sizes. "large", "medium_large" and "medium" replace the sizes set in
	 * Settings > Media, which then no longer apply.
	 */
	add_image_size( 'large', 900, 600, true );
	add_image_size( 'medium_large', 800, 0 );
	add_image_size( 'medium', 600, 400, true );
	add_image_size( 'small', 300, 0 );
	add_image_size( 'large_square', 900, 900, true );
	add_image_size( 'medium_square', 600, 600, true );
	add_image_size( 'small_square', 300, 300, true );
	add_image_size( 'hero_image_large', 2048, 1366, true );
	add_image_size( 'hero_image_tablet', 1366, 1366, true );

	// Excerpts on pages.
	add_post_type_support( 'page', 'excerpt' );
}
add_action( 'after_setup_theme', 'ny_setup' );

/**
 * Stops WordPress creating its extra 1536px and 2048px copies of every upload.
 *
 * @param array $sizes Image sizes to generate.
 * @return array
 */
function ny_remove_default_image_sizes( $sizes ) {
	unset( $sizes['1536x1536'], $sizes['2048x2048'] );

	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'ny_remove_default_image_sizes' );

/**
 * Registers widget areas.
 */
function ny_widgets_init() {
	foreach ( [ 1, 2 ] as $ny_number ) {
		register_sidebar(
			[
				/* translators: %d: Widget area number. */
				'name'          => sprintf( __( 'Widget Area %d', 'ny-blank-theme' ), $ny_number ),
				'id'            => "widget-area-{$ny_number}",
				'description'   => __( 'Widgets shown in the sidebar on post lists.', 'ny-blank-theme' ),
				'before_widget' => '<div id="%1$s" class="%2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			]
		);
	}
}
add_action( 'widgets_init', 'ny_widgets_init' );

/**
 * Removes the presets WordPress adds by default (colours, gradients, duotone, font sizes,
 * spacing, shadows, aspect ratios and dimension sizes), at the top level and per block.
 *
 * WordPress outputs default presets as CSS custom properties even when theme.json hides
 * them from the editor. Removing them here means the only --wp--preset--* variables are
 * the ones defined in this theme's theme.json.
 *
 * @param WP_Theme_JSON_Data $theme_json Core (default origin) theme.json data.
 * @return WP_Theme_JSON_Data
 */
function ny_remove_default_presets( $theme_json ) {
	$preset_paths = [
		[ 'color', 'palette' ],
		[ 'color', 'gradients' ],
		[ 'color', 'duotone' ],
		[ 'typography', 'fontSizes' ],
		[ 'typography', 'fontFamilies' ],
		[ 'spacing', 'spacingSizes' ],
		[ 'spacing', 'spacingScale' ],
		[ 'shadow', 'presets' ],
		[ 'dimensions', 'aspectRatios' ],
		[ 'dimensions', 'dimensionSizes' ],
	];

	$strip = function ( array $settings ) use ( $preset_paths ) {
		foreach ( $preset_paths as [ $section, $key ] ) {
			unset( $settings[ $section ][ $key ] );
		}
		return $settings;
	};

	$data             = $theme_json->get_data();
	$data['settings'] = $strip( $data['settings'] ?? [] );

	foreach ( $data['settings']['blocks'] ?? [] as $block_name => $block_settings ) {
		$data['settings']['blocks'][ $block_name ] = $strip( $block_settings );
	}

	return new WP_Theme_JSON_Data( $data, 'default' );
}
add_filter( 'wp_theme_json_data_default', 'ny_remove_default_presets' );
