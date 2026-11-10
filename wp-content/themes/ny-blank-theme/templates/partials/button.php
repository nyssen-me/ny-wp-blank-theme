<?php
/**
 * Button link from an ACF link field. Outputs nothing without a URL.
 *
 * @package ny-blank-theme
 *
 * @var array $args {
 *     Arguments passed via get_template_part().
 *
 *     @type array|null $link  ACF link field value (url, title, target).
 *     @type string     $class Class for the link. Default 'button'.
 * }
 */

$ny_link = $args['link'] ?? null;

if ( empty( $ny_link['url'] ) ) {
	return;
}

$ny_class   = $args['class'] ?? 'button';
$ny_title   = ! empty( $ny_link['title'] ) ? $ny_link['title'] : $ny_link['url'];
$ny_new_tab = '_blank' === ( $ny_link['target'] ?? '' );
?>
<a class="<?php echo esc_attr( $ny_class ); ?>" href="<?php echo esc_url( $ny_link['url'] ); ?>"<?php echo $ny_new_tab ? ' target="_blank" rel="noopener"' : ''; ?>>
	<?php echo esc_html( $ny_title ); ?>
	<?php if ( $ny_new_tab ) : ?>
		<span class="visually-hidden"><?php esc_html_e( '(opens in a new tab)', 'ny-blank-theme' ); ?></span>
	<?php endif; ?>
</a>
