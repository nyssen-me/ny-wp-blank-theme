<?php
/**
 * Single page. The page heading comes from the acf/hero block, and every block outputs
 * its own section and .container inside main.wrapper.
 *
 * @package ny-blank-theme
 */

get_header();
?>
<main id="maincontent" class="wrapper">
	<?php while ( have_posts() ) : ?>
		<?php the_post(); ?>
		<?php the_content(); ?>
	<?php endwhile; ?>
</main>
<?php
get_footer();
