<?php
/**
 * Hero block: the page heading, with the page h1.
 *
 * On the page set as the front page it outputs the home masthead and intro section
 * (templates/partials/home-masthead.php). Everywhere else it outputs the page heading
 * (templates/partials/page-heading.php), shared with the PHP templates.
 *
 * @package ny-blank-theme
 *
 * @var array  $block      Block settings and attributes.
 * @var string $content    Inner block content (not used).
 * @var bool   $is_preview True in the editor.
 * @var int    $post_id    Current post ID.
 */

$ny_is_front_page = 'page' === get_option( 'show_on_front' ) && (int) $post_id === (int) get_option( 'page_on_front' );

get_template_part(
	$ny_is_front_page ? 'templates/partials/home-masthead' : 'templates/partials/page-heading',
	null,
	[
		'title'       => get_field( 'heading' ),
		'text'        => get_field( 'text' ),
		'link'        => get_field( 'cta' ),
		'image'       => get_field( 'image' ),
		'placeholder' => $is_preview ? __( 'Add a heading in the block settings.', 'ny-blank-theme' ) : '',
		'is_preview'  => $is_preview,
		'attributes'  => get_block_wrapper_attributes( [ 'class' => $ny_is_front_page ? 'masthead' : 'masthead-page' ] ),
	]
);
