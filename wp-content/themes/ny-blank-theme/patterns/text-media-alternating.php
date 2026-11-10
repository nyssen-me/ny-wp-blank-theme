<?php
/**
 * Title: Text and media, alternating
 * Slug: ny-blank-theme/text-media-alternating
 * Categories: ny-sections
 * Description: Two text and media sections, the first with the image on the right and the second with the image on the left.
 * Keywords: image, columns, zigzag
 *
 * @package ny-blank-theme
 */

?>
<!-- wp:acf/text-media {"name":"acf/text-media","data":{"image_position":"right","_image_position":"field_ny_text_media_image_position"},"mode":"preview"} -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'First section heading', 'ny-blank-theme' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Describe the first point here. Choose an image in the block settings.', 'ny-blank-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:acf/text-media -->

<!-- wp:acf/text-media {"name":"acf/text-media","data":{"image_position":"left","_image_position":"field_ny_text_media_image_position"},"mode":"preview"} -->
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Second section heading', 'ny-blank-theme' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Describe the second point here. Choose an image in the block settings.', 'ny-blank-theme' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:acf/text-media -->
