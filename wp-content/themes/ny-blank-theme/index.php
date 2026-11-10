<?php
/**
 * Lists of posts: blog page, archives (category, tag, author, date) and search results.
 *
 * @package ny-blank-theme
 */

global $wp_query;

if ( is_search() ) {
	$ny_title = sprintf(
		/* translators: 1: Number of results. 2: Search query. */
		_n( '%1$s search result for "%2$s"', '%1$s search results for "%2$s"', (int) $wp_query->found_posts, 'ny-blank-theme' ),
		number_format_i18n( (int) $wp_query->found_posts ),
		get_search_query( false )
	);
	$ny_text  = '';
} elseif ( is_archive() ) {
	$ny_title = wp_strip_all_tags( get_the_archive_title() );
	$ny_text  = wp_strip_all_tags( get_the_archive_description() );
} else {
	$ny_title = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'News', 'ny-blank-theme' );
	$ny_text  = '';
}

get_header();
?>
<main id="maincontent" class="wrapper">

	<?php
	get_template_part(
		'templates/partials/page-heading',
		null,
		[
			'title' => $ny_title,
			'text'  => $ny_text,
		]
	);
	?>

	<section>
		<div class="container container-x-wide">
			<div class="row">
				<div class="column">

					<?php if ( have_posts() ) : ?>
						<ul class="news-list">
							<?php while ( have_posts() ) : ?>
								<?php the_post(); ?>
								<?php get_template_part( 'templates/partials/card', null, [ 'show_image' => ! is_search() ] ); ?>
							<?php endwhile; ?>
						</ul>
					<?php else : ?>
						<p><?php esc_html_e( 'Nothing was found. Try a different search.', 'ny-blank-theme' ); ?></p>
						<?php get_search_form(); ?>
					<?php endif; ?>

				</div>
			</div>
		</div>

		<?php if ( $wp_query->max_num_pages > 1 ) : ?>
			<div class="container padding-long padding-top-none">
				<div class="row">
					<div class="column text-center">

						<nav class="pag-nav" aria-label="<?php esc_attr_e( 'Pagination', 'ny-blank-theme' ); ?>">
							<?php ny_numeric_posts_nav(); ?>
						</nav>

					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>

</main>
<?php
get_sidebar();
get_footer();
