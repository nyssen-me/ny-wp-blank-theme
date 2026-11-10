<?php
/**
 * Front-end clean-up: head output, emoji, body classes, admin bar and comments.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/*
 * Head output. The main feed links are not output because the theme does not declare
 * automatic-feed-links support. The extra feeds (comments, category, search) are output
 * regardless, so they are removed here. The canonical link is kept, as search engines
 * rely on it.
 */
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );

/*
 * Emoji: no detection script, styles or DNS prefetch. Browsers display emoji natively.
 * wp_enqueue_emoji_styles() normally unhooks the deprecated print_emoji_styles(), so that
 * has to be removed as well, or it runs and triggers a deprecation notice.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );

/*
 * Core CSS on the front end. All front-end CSS comes from style-min.css, so WordPress's
 * block CSS is not loaded there: the general block library, the theme.json global styles
 * (preset variables, layout and alignment rules) and each core block's own stylesheet.
 * They mostly style features the editor lockdown switches off. The editor is not
 * affected: it loads its own copy of these styles.
 */
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

/**
 * Dequeues the general block library CSS on the front end.
 */
function ny_dequeue_block_library() {
	wp_dequeue_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'ny_dequeue_block_library', 100 );

/**
 * Stops core blocks (paragraph, heading, list and so on) loading their own stylesheets
 * when they appear on a page.
 *
 * @param array  $args       Block type arguments.
 * @param string $block_name Block name.
 * @return array
 */
function ny_remove_core_block_styles( $args, $block_name ) {
	if ( str_starts_with( $block_name, 'core/' ) ) {
		$args['style_handles'] = [];
		unset( $args['style'] );
	}

	return $args;
}
add_filter( 'register_block_type_args', 'ny_remove_core_block_styles', 10, 2 );

/**
 * Replaces rel="category tag" with rel="tag" in category links, as "category" is not a
 * valid rel value.
 *
 * @param string $list Category list HTML.
 * @return string
 */
function ny_category_rel( $list ) {
	return str_replace( 'rel="category tag"', 'rel="tag"', $list );
}
add_filter( 'the_category', 'ny_category_rel' );

/**
 * Adds the page or post slug to the body classes, and removes "blog" from the blog page.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function ny_body_classes( $classes ) {
	if ( is_home() ) {
		$classes = array_values( array_diff( $classes, [ 'blog' ] ) );
	} elseif ( is_singular() ) {
		$classes[] = sanitize_html_class( get_post_field( 'post_name', get_queried_object_id() ) );
	}

	return $classes;
}
add_filter( 'body_class', 'ny_body_classes' );

// No admin bar on the front end.
add_filter( 'show_admin_bar', '__return_false' );

/*
 * Comments are switched off everywhere: front end, admin menu, dashboard, admin bar and
 * the editor's Discussion panel.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );

/**
 * Removes comment and trackback support from every post type.
 */
function ny_remove_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'ny_remove_comment_support', 100 );

/**
 * Removes the comments screens from the admin and redirects anyone who opens them.
 */
function ny_remove_comments_admin() {
	global $pagenow;

	if ( in_array( $pagenow, [ 'edit-comments.php', 'options-discussion.php' ], true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'admin_init', 'ny_remove_comments_admin' );

/**
 * Removes Comments and Settings > Discussion from the admin menu.
 */
function ny_remove_comments_menu() {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'ny_remove_comments_menu' );

/**
 * Removes the comments item from the admin bar.
 *
 * @param WP_Admin_Bar $admin_bar Admin bar.
 */
function ny_remove_comments_admin_bar( $admin_bar ) {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'ny_remove_comments_admin_bar', 999 );
