<?php
/**
 * Block editor lockdown: block allow-list, core block supports and editor settings.
 *
 * Colour, typography and spacing controls are switched off in theme.json. This file
 * handles what theme.json cannot reach.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Core blocks editors may use alongside the ACF blocks.
 *
 * @return string[]
 */
function ny_allowed_core_blocks() {
	return [ 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/quote' ];
}

/**
 * Names of the theme's registered ACF blocks.
 *
 * @return string[]
 */
function ny_acf_block_names() {
	return array_values(
		array_filter(
			array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ),
			fn( $name ) => str_starts_with( $name, 'acf/' )
		)
	);
}

/**
 * Limits the post editor to the theme's ACF blocks plus the allowed core text blocks.
 *
 * Other editors (such as the widgets editor) keep the default list.
 *
 * @param bool|string[]           $allowed Allowed block names, or true for all.
 * @param WP_Block_Editor_Context $context Current editor context.
 * @return bool|string[]
 */
function ny_allowed_block_types( $allowed, $context ) {
	if ( 'core/edit-post' !== $context->name ) {
		return $allowed;
	}

	// Vary by post type here. Posts are text only: single.php outputs their content inside
	// its own grid, where section blocks (each with a .container) do not fit.
	if ( 'post' === ( $context->post->post_type ?? '' ) ) {
		return ny_allowed_core_blocks();
	}

	return array_merge( ny_acf_block_names(), ny_allowed_core_blocks() );
}
add_filter( 'allowed_block_types_all', 'ny_allowed_block_types', 10, 2 );

/**
 * Keeps the core text blocks inside the section blocks on pages.
 *
 * On pages every section is an ACF block that outputs its own section > .container >
 * .row > .column, so a paragraph, heading, list or quote at the top level would sit
 * outside the grid. Setting `ancestor` means they can only be inserted inside an ACF
 * block, and each block's InnerBlocks `allowedBlocks` decides which ones actually accept
 * them (currently acf/content and acf/text-media). New ACF blocks with text InnerBlocks
 * are covered automatically.
 *
 * Posts keep the blocks at the top level, as single.php wraps their content itself.
 *
 * This runs on current_screen, which fires before the editor sends the block definitions
 * to the browser, so it only affects the block editor for that request. Existing loose
 * text blocks still display and can be edited, but no new ones can be added.
 *
 * @param WP_Screen $screen Current admin screen.
 */
function ny_restrict_top_level_text_blocks( $screen ) {
	// Only the post editor (base "post" covers every post type), not the widgets editor.
	if ( ! $screen->is_block_editor() || 'post' !== $screen->base || 'post' === $screen->post_type ) {
		return;
	}

	$registry = WP_Block_Type_Registry::get_instance();
	$sections = ny_acf_block_names();

	// core/list-item is left out: it already has core/list as its parent.
	foreach ( [ 'core/paragraph', 'core/heading', 'core/list', 'core/quote' ] as $name ) {
		$block_type = $registry->get_registered( $name );
		if ( $block_type ) {
			$block_type->ancestor = $sections;
		}
	}
}
add_action( 'current_screen', 'ny_restrict_top_level_text_blocks' );

/**
 * Removes styling supports from the allowed core text blocks.
 *
 * These blocks support border, wide and full alignment, text indent and "fit text",
 * which theme.json does not switch off. Removing the supports here also removes the
 * matching controls, per-block custom CSS, and the Additional CSS class and Edit as HTML
 * options.
 *
 * Headings also lose the H1 option, as the page heading comes from acf/hero.
 *
 * Quotes also support a background image, minimum height and layout, and come with a
 * "Plain" block style, which are all removed. Their text alignment button is built into
 * the block and cannot be removed here, so blocks.css cancels its effect instead.
 *
 * @param array  $args       Block type arguments, including supports from block.json.
 * @param string $block_name Block name.
 * @return array
 */
function ny_restrict_core_block_supports( $args, $block_name ) {
	if ( ! in_array( $block_name, ny_allowed_core_blocks(), true ) ) {
		return $args;
	}

	$args['supports'] = array_merge(
		$args['supports'] ?? [],
		[
			'align'                => false,
			'color'                => false,
			'customClassName'      => false,
			'customCSS'            => false,
			'html'                 => false,
			'spacing'              => false,
			'typography'           => false,
			'__experimentalBorder' => false,
			'background'           => false,
			'dimensions'           => false,
			'layout'               => false,
		]
	);

	if ( 'core/heading' === $block_name ) {
		$args['attributes']['levelOptions']['default'] = [ 2, 3, 4, 5, 6 ];
	}

	if ( 'core/quote' === $block_name ) {
		$args['styles'] = [];
	}

	return $args;
}
add_filter( 'register_block_type_args', 'ny_restrict_core_block_supports', 10, 2 );

/**
 * Restricts code editing and block locking to administrators and switches off
 * Openverse and the font library.
 *
 * @param array $settings Block editor settings.
 * @return array
 */
function ny_block_editor_settings( $settings ) {
	$is_admin = current_user_can( 'manage_options' );

	$settings['codeEditingEnabled']           = $is_admin;
	$settings['canLockBlocks']                = $is_admin;
	$settings['enableOpenverseMediaCategory'] = false;
	$settings['fontLibraryEnabled']           = false;

	return $settings;
}
add_filter( 'block_editor_settings_all', 'ny_block_editor_settings' );

// No block directory, so blocks cannot be installed from the inserter.
remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );

// Core and remote patterns are removed in functions/patterns.php.

// Load core block CSS per block, only when the block is on the page.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
