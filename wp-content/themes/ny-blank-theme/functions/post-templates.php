<?php
/**
 * Starting block templates and locking per post type.
 *
 * Templates only apply to new posts. For custom post types, pass `template` and
 * `template_lock` to register_post_type() instead.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sets the starting blocks for pages.
 *
 * Pages start with a hero that cannot be moved or removed, followed by a content block.
 * Posts have no template: they are text only and single.php outputs their title.
 */
function ny_post_type_templates() {
	$page = get_post_type_object( 'page' );
	if ( $page ) {
		$page->template      = [
			[
				'acf/hero',
				[
					'lock' => [
						'move'   => true,
						'remove' => true,
					],
				],
			],
			[ 'acf/content' ],
		];
		$page->template_lock = false;
	}
}
add_action( 'init', 'ny_post_type_templates' );
