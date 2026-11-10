<?php
/**
 * Search form, used by get_search_form().
 *
 * @package ny-blank-theme
 */

$ny_field_id = wp_unique_id( 'search-' );
?>
<section class="container">
	<div class="row">
		<div class="column">
			<form class="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
				<label class="visually-hidden" for="<?php echo esc_attr( $ny_field_id ); ?>"><?php esc_html_e( 'Search the site for:', 'ny-blank-theme' ); ?></label>
				<input class="search-input" id="<?php echo esc_attr( $ny_field_id ); ?>" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'To search, type and hit enter.', 'ny-blank-theme' ); ?>">
				<button class="search-submit" type="submit"><?php esc_html_e( 'Search', 'ny-blank-theme' ); ?></button>
			</form>
		</div>
	</div>
</section>
