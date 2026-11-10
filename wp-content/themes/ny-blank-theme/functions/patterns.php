<?php
/**
 * Pattern categories, and removal of core and remote patterns.
 *
 * Pattern files in /patterns are registered by WordPress automatically. They are section
 * patterns, inserted from the Patterns tab. Pages start from the page template in
 * functions/post-templates.php, so there are no full-page patterns.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the theme's pattern categories.
 */
function ny_register_pattern_categories() {
	register_block_pattern_category(
		'ny-sections',
		[
			'label'       => __( 'Site sections', 'ny-blank-theme' ),
			'description' => __( 'Ready-made combinations of the site blocks.', 'ny-blank-theme' ),
		]
	);
}
add_action( 'init', 'ny_register_pattern_categories' );

// No patterns from WordPress.org or core.
add_filter( 'should_load_remote_block_patterns', '__return_false' );
add_action( 'after_setup_theme', fn() => remove_theme_support( 'core-block-patterns' ) );
