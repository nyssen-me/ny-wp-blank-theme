<?php
/**
 * ACF block loader and block category.
 *
 * Every folder in templates/blocks/ with a block.json is registered automatically.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers every block in templates/blocks/. Skipped when ACF PRO is not active.
 */
function ny_register_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	foreach ( glob( get_parent_theme_file_path( 'templates/blocks/*/block.json' ) ) ?: [] as $file ) {
		register_block_type( dirname( $file ) );
	}
}
add_action( 'init', 'ny_register_blocks' );

// Output InnerBlocks content directly in the block markup on the front end, without the
// extra div ACF adds by default, so the markup matches the old flexible content layouts.
add_filter( 'acf/blocks/wrap_frontend_innerblocks', '__return_false' );

/**
 * Adds the theme's block category at the top of the inserter.
 *
 * @param array[] $categories Registered block categories.
 * @return array[]
 */
function ny_block_categories( $categories ) {
	array_unshift(
		$categories,
		[
			'slug'  => 'ny-blocks',
			'title' => __( 'Site blocks', 'ny-blank-theme' ),
		]
	);

	return $categories;
}
add_filter( 'block_categories_all', 'ny_block_categories' );
