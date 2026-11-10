<?php
/**
 * Navigation menu output tweaks.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Removes the div WordPress wraps around every menu, to keep the markup clean.
 *
 * @param array $args wp_nav_menu() arguments.
 * @return array
 */
function ny_nav_menu_args( $args ) {
	$args['container'] = false;

	return $args;
}
add_filter( 'wp_nav_menu_args', 'ny_nav_menu_args' );

/**
 * Shows menu item descriptions (Appearance > Menus > Screen Options > Description) in the
 * top level of the primary menu, as span.description inside the link.
 *
 * @param string   $item_output Menu item HTML.
 * @param WP_Post  $item        Menu item.
 * @param int      $depth       Depth of the item.
 * @param stdClass $args        wp_nav_menu() arguments.
 * @return string
 */
function ny_nav_menu_descriptions( $item_output, $item, $depth, $args ) {
	if ( 'primary' !== $args->theme_location || $depth || empty( $item->description ) ) {
		return $item_output;
	}

	return str_replace( '</a>', '<span class="description">' . esc_html( $item->description ) . '</span></a>', $item_output );
}
add_filter( 'walker_nav_menu_start_el', 'ny_nav_menu_descriptions', 10, 4 );
