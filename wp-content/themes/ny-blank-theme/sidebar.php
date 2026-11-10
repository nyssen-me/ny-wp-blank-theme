<?php
/**
 * Sidebar: search form and two widget areas. Outputs nothing when both are empty.
 *
 * @package ny-blank-theme
 */

if ( ! is_active_sidebar( 'widget-area-1' ) && ! is_active_sidebar( 'widget-area-2' ) ) {
	return;
}
?>
<aside class="sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'ny-blank-theme' ); ?>">

	<?php get_search_form(); ?>

	<?php foreach ( [ 'widget-area-1', 'widget-area-2' ] as $ny_area ) : ?>
		<?php if ( is_active_sidebar( $ny_area ) ) : ?>
			<div class="sidebar-widget">
				<?php dynamic_sidebar( $ny_area ); ?>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>

</aside>
