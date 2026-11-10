<?php
/**
 * Single post: date, title and content, with the featured image beside it.
 *
 * Posts use core text blocks only (see functions/editor.php), so the_content() outputs
 * plain paragraphs, headings and lists inside .grid-content, as the Classic Editor did.
 *
 * @package ny-blank-theme
 */

get_header();
?>
<main id="maincontent" class="wrapper">

	<div class="masthead-page"></div>

	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<section>
			<div class="container container-narrow">

				<div class="grid-wrapper">

					<div class="grid-content">

						<div class="event-meta-single">
							<span>
								<?php
								printf(
									/* translators: %s: Publication date. */
									esc_html__( 'Published on %s', 'ny-blank-theme' ),
									'<time datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>'
								);
								?>
							</span>
						</div>

						<h1 class="post-heading"><?php the_title(); ?></h1>

						<?php the_content(); ?>

					</div>

					<div class="grid-sidebar">
						<picture>
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', [ 'class' => 'img-responsive' ] ); ?>
							<?php else : ?>
								<img class="img-responsive" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="" width="800" height="600" loading="lazy">
							<?php endif; ?>
						</picture>
					</div>

				</div>

			</div>
		</section>
	<?php endwhile; ?>

</main>
<?php
get_footer();
