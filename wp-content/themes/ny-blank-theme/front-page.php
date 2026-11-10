<?php
/**
 * Home page (front page). Used whenever the site shows a static front page or the latest
 * posts on the home page.
 *
 * Like page.php, the content comes from the blocks of the page set as the front page. On
 * this page the acf/hero block outputs the home masthead (.masthead) and the intro section
 * instead of the usual page heading, see templates/blocks/hero/render.php.
 *
 * @package ny-blank-theme
 */

if ( 'page' !== get_option( 'show_on_front' ) ) {
	// The home page shows the latest posts, so use the post list template.
	get_template_part( 'index' );
	return;
}

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
