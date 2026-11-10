<?php
/**
 * Content block: headings, paragraphs and lists edited inline.
 *
 * In the editor the section also gets the "wrapper" class, which main.wrapper provides on
 * the front end, so the .wrapper .container styles apply to the preview too.
 *
 * @package ny-blank-theme
 *
 * @var array  $block      Block settings and attributes.
 * @var string $content    Inner block content.
 * @var bool   $is_preview True in the editor.
 * @var int    $post_id    Current post ID.
 */

$ny_wrapper = get_block_wrapper_attributes( $is_preview ? [ 'class' => 'wrapper' ] : [] );

$ny_allowed_blocks = [ 'core/heading', 'core/paragraph', 'core/list', 'core/quote' ];
$ny_template       = [
	[
		'core/heading',
		[
			'level'       => 2,
			'placeholder' => __( 'Add a heading', 'ny-blank-theme' ),
		],
	],
	[ 'core/paragraph', [ 'placeholder' => __( 'Add some text', 'ny-blank-theme' ) ] ],
];
?>
<section <?php echo $ny_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by get_block_wrapper_attributes(). ?>>
	<div class="container">
		<div class="row">
			<div class="column">
				<InnerBlocks
					allowedBlocks="<?php echo esc_attr( wp_json_encode( $ny_allowed_blocks ) ); ?>"
					template="<?php echo esc_attr( wp_json_encode( $ny_template ) ); ?>"
				/>
			</div>
		</div>
	</div>
</section>
