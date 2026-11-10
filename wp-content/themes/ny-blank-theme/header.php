<?php
/**
 * Site header. The logo comes from Theme Settings (SVG code), with the site title as fallback.
 *
 * @package ny-blank-theme
 */

$ny_logo = function_exists( 'get_field' ) ? get_field( 'main_logo', 'option' ) : '';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<a href="#maincontent" class="skip-link"><?php esc_html_e( 'Skip to main content', 'ny-blank-theme' ); ?></a>

	<header class="main-header">

		<a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php if ( $ny_logo ) : ?>
				<?php echo ny_kses_svg( $ny_logo ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered by ny_kses_svg(). ?>
				<span class="visually-hidden"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<?php else : ?>
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			<?php endif; ?>
		</a>

		<div class="main-nav-panel" id="wrapper_nav">

			<nav class="main-nav" aria-label="<?php esc_attr_e( 'Main', 'ny-blank-theme' ); ?>">
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'primary',
						'container'      => false,
						'items_wrap'     => '<ul class="main-menu sm sm-clean" id="main_menu">%3$s</ul>',
						'fallback_cb'    => false,
					]
				);
				?>
			</nav>

			<div class="search-box">
				<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
					<input class="search-input" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'To search type and hit enter', 'ny-blank-theme' ); ?>" aria-label="<?php esc_attr_e( 'Search the site', 'ny-blank-theme' ); ?>">
					<button class="search-submit" type="submit">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="m21.7 19.2-3.8-3.8c-.2-.2-.4-.3-.7-.3h-.6c1.1-1.4 1.7-3.1 1.7-4.9 0-4.4-3.6-8-8-8s-8 3.6-8 8 3.6 8 8 8c1.8 0 3.5-.6 4.9-1.7v.6c0 .2.1.5.3.7l3.8 3.8c.4.4.9.4 1.3 0l1.1-1.1c.4-.4.4-1 0-1.3m-11.4-4.1c-2.7 0-4.9-2.2-4.9-4.9s2.2-4.9 4.9-4.9 4.9 2.2 4.9 4.9-2.2 4.9-4.9 4.9"/></svg>
						<span class="visually-hidden"><?php esc_html_e( 'Search', 'ny-blank-theme' ); ?></span>
					</button>
				</form>
			</div>

		</div>

		<div class="hamburger-btn">
			<button class="menu-button"
					aria-label="<?php esc_attr_e( 'Open navigation menu', 'ny-blank-theme' ); ?>"
					aria-expanded="false"
					aria-controls="wrapper_nav">
				<span class="menu-bar" aria-hidden="true"></span>
				<span class="menu-bar" aria-hidden="true"></span>
				<span class="menu-bar" aria-hidden="true"></span>
			</button>
		</div>

	</header>
