<?php
/**
 * Title: Content with a list
 * Slug: ny-blank-theme/content-with-list
 * Categories: ny-sections
 * Description: A content section with a heading, an introduction and a bulleted list.
 * Keywords: text, list, features
 *
 * @package ny-blank-theme
 */

?>
<!-- wp:acf/content {"name":"acf/content","mode":"preview"} -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Section heading', 'ny-blank-theme' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'A short introduction to the list below.', 'ny-blank-theme' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><!-- wp:list-item -->
<li><?php echo esc_html__( 'First point', 'ny-blank-theme' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html__( 'Second point', 'ny-blank-theme' ); ?></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><?php echo esc_html__( 'Third point', 'ny-blank-theme' ); ?></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list -->
<!-- /wp:acf/content -->
