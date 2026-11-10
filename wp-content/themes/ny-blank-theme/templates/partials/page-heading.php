<?php
/**
 * Page heading (.masthead-page). Shared by the acf/hero block and the PHP templates.
 *
 * @package ny-blank-theme
 *
 * @var array $args {
 *     Arguments passed via get_template_part().
 *
 *     @type string     $title       Heading text, output as the page h1.
 *     @type string     $text        Optional intro text under the heading.
 *     @type array|null $link        Optional ACF link field value, output as a button.
 *     @type int        $image       Optional attachment ID for the background image.
 *     @type string     $placeholder Optional text shown instead of an empty heading (editor only).
 *     @type string     $attributes  Optional escaped attributes for the outer element, for
 *                                   example from get_block_wrapper_attributes().
 * }
 */

$ny_title       = $args['title'] ?? '';
$ny_text        = $args['text'] ?? '';
$ny_image       = $args['image'] ?? 0;
$ny_placeholder = $args['placeholder'] ?? '';
$ny_attributes  = $args['attributes'] ?? 'class="masthead-page"';
?>
<div <?php echo $ny_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by the caller. ?>>
	<?php if ( $ny_image ) : ?>
		<picture>
			<?php echo wp_get_attachment_image( $ny_image, 'hero_image_large', false, [ 'fetchpriority' => 'high', 'loading' => 'eager' ] ); ?>
		</picture>
	<?php endif; ?>

	<div class="page-heading">
		<?php if ( $ny_title ) : ?>
			<h1 class="page-title"><?php echo esc_html( $ny_title ); ?></h1>
		<?php elseif ( $ny_placeholder ) : ?>
			<p class="block__placeholder"><?php echo esc_html( $ny_placeholder ); ?></p>
		<?php endif; ?>

		<?php if ( $ny_text ) : ?>
			<p><?php echo esc_html( $ny_text ); ?></p>
		<?php endif; ?>

		<?php get_template_part( 'templates/partials/button', null, [ 'link' => $args['link'] ?? null ] ); ?>
	</div>
</div>
