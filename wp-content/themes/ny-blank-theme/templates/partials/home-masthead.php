<?php
/**
 * Home page masthead (.masthead) followed by the intro section with the page h1.
 *
 * Output by the acf/hero block when it is on the page set as the front page. The masthead
 * text and SVG come from Theme Settings > Home, the rest from the hero block fields.
 *
 * @package ny-blank-theme
 *
 * @var array $args {
 *     Arguments passed via get_template_part().
 *
 *     @type string     $title       Heading text, output as the page h1.
 *     @type string     $text        Optional intro text under the heading.
 *     @type array|null $link        Optional ACF link field value, output as a button.
 *     @type int        $image       Optional attachment ID for the masthead background.
 *     @type string     $placeholder Optional text shown instead of an empty heading (editor only).
 *     @type bool       $is_preview  True in the editor.
 *     @type string     $attributes  Escaped attributes for the masthead element, for example
 *                                   from get_block_wrapper_attributes().
 * }
 */

$ny_title         = $args['title'] ?? '';
$ny_text          = $args['text'] ?? '';
$ny_image         = $args['image'] ?? 0;
$ny_placeholder   = $args['placeholder'] ?? '';
$ny_masthead_text = function_exists( 'get_field' ) ? get_field( 'masthead_text', 'option' ) : '';
$ny_masthead_svg  = function_exists( 'get_field' ) ? get_field( 'masthead_svg', 'option' ) : '';
?>
<div <?php echo $args['attributes'] ?? 'class="masthead"'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by the caller. ?>>
	<?php if ( $ny_image ) : ?>
		<picture>
			<?php echo wp_get_attachment_image( $ny_image, 'hero_image_large', false, [ 'fetchpriority' => 'high', 'loading' => 'eager' ] ); ?>
		</picture>
	<?php endif; ?>

	<div class="masthead-content">
		<?php if ( $ny_masthead_text ) : ?>
			<p><?php echo esc_html( $ny_masthead_text ); ?></p>
		<?php endif; ?>
	</div>

	<div class="masthead-logo">
		<?php echo ny_kses_svg( (string) $ny_masthead_svg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered by ny_kses_svg(). ?>
	</div>
</div>

<section<?php echo ! empty( $args['is_preview'] ) ? ' class="wrapper"' : ''; ?>>
	<div class="container padding-long home-intro">
		<div class="row">
			<div class="column">

				<?php if ( $ny_title ) : ?>
					<h1 class="home-title"><?php echo esc_html( $ny_title ); ?></h1>
				<?php elseif ( $ny_placeholder ) : ?>
					<p class="block__placeholder"><?php echo esc_html( $ny_placeholder ); ?></p>
				<?php endif; ?>

				<?php if ( $ny_text ) : ?>
					<p><?php echo esc_html( $ny_text ); ?></p>
				<?php endif; ?>

				<?php get_template_part( 'templates/partials/button', null, [ 'link' => $args['link'] ?? null ] ); ?>

			</div>
		</div>
	</div>
</section>
