<?php
/**
 * Text and media block: inline text next to an image, image on the left or right.
 *
 * The text column comes first in the markup, so it is read first. The CSS reverses the
 * row when the image is on the left (assets/css/src/blocks.css).
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

$ny_image    = get_field( 'image' );
$ny_position = 'left' === get_field( 'image_position' ) ? 'left' : 'right';

$ny_classes = [ "text-media--image-{$ny_position}" ];
if ( $is_preview ) {
	$ny_classes[] = 'wrapper';
}

$ny_wrapper = get_block_wrapper_attributes( [ 'class' => implode( ' ', $ny_classes ) ] );

$ny_allowed_blocks = [ 'core/heading', 'core/paragraph', 'core/list' ];
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
		<div class="row row-center">
			<div class="column">
				<InnerBlocks
					allowedBlocks="<?php echo esc_attr( wp_json_encode( $ny_allowed_blocks ) ); ?>"
					template="<?php echo esc_attr( wp_json_encode( $ny_template ) ); ?>"
				/>
			</div>

			<?php if ( $ny_image ) : ?>
				<div class="column">
					<?php echo wp_get_attachment_image( $ny_image, 'large', false, [ 'class' => 'img-responsive' ] ); ?>
				</div>
			<?php elseif ( $is_preview ) : ?>
				<div class="column">
					<p class="block__placeholder"><?php esc_html_e( 'Choose an image in the block settings.', 'ny-blank-theme' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
