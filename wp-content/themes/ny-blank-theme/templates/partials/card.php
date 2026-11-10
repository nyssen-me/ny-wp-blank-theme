<?php
/**
 * Post card for lists of posts (li.card inside ul.news-list). Uses the current post in the loop.
 *
 * @package ny-blank-theme
 *
 * @var array $args {
 *     Optional arguments passed via get_template_part().
 *
 *     @type bool $show_image Show the image and categories. Default true.
 * }
 */

$ny_show_image = $args['show_image'] ?? true;
$ny_categories = get_the_category();
?>
<li class="card">
	<?php if ( $ny_show_image ) : ?>
		<div class="card-image">
			<?php if ( $ny_categories ) : ?>
				<div class="entry-meta">
					<span class="post-category"><?php the_category( ' ' ); ?></span>
				</div>
			<?php endif; ?>

			<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'medium_large', [ 'class' => 'img-responsive' ] ); ?>
				<?php else : ?>
					<img class="img-responsive" src="<?php echo esc_url( get_theme_file_uri( 'assets/images/placeholder.jpg' ) ); ?>" alt="" width="800" height="600" loading="lazy">
				<?php endif; ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="card-header">
		<a href="<?php the_permalink(); ?>">
			<h2><?php the_title(); ?></h2>
		</a>
		<span class="post-date"><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></span>
	</div>

	<div class="card-body">
		<?php the_excerpt(); ?>
	</div>

	<div class="card-footer">
		<a class="button button-small" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read more', 'ny-blank-theme' ); ?><span class="visually-hidden">: <?php the_title(); ?></span>
		</a>
	</div>
</li>
