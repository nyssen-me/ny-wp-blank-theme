<?php
/**
 * Page not found.
 *
 * @package ny-blank-theme
 */

get_header();
?>
<main id="maincontent" class="wrapper">

	<?php
	get_template_part(
		'templates/partials/page-heading',
		null,
		[
			'title' => __( 'Page not found', 'ny-blank-theme' ),
			'text'  => __( 'Error 404', 'ny-blank-theme' ),
		]
	);
	?>

	<section class="container container-wide padding-short">

		<div class="row single-post-row">
			<div class="column">

				<div class="border-top"></div>

				<div class="row">
					<div class="column">

						<h2><?php esc_html_e( 'Sorry!', 'ny-blank-theme' ); ?></h2>
						<p><?php esc_html_e( 'We can\'t seem to find the page you are looking for.', 'ny-blank-theme' ); ?></p>

						<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return home', 'ny-blank-theme' ); ?></a>

					</div>

					<div class="column column-70 column-offset-4 border-left">
						<img src="<?php echo esc_url( get_theme_file_uri( 'assets/images/404.svg' ) ); ?>" alt="" width="860" height="571">
					</div>
				</div>

			</div>
		</div>

	</section>

</main>
<?php
get_footer();
