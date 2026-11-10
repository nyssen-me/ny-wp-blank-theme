<?php
/**
 * Template tags: small output helpers used by the PHP templates and partials.
 *
 * @package ny-blank-theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Filters pasted SVG code (such as the logo from Theme Settings) down to safe SVG markup.
 *
 * @param string $svg SVG code.
 * @return string
 */
function ny_kses_svg( $svg ) {
	$presentation = [
		'class'             => true,
		'id'                => true,
		'fill'              => true,
		'fill-rule'         => true,
		'clip-rule'         => true,
		'stroke'            => true,
		'stroke-width'      => true,
		'stroke-linecap'    => true,
		'stroke-linejoin'   => true,
		'opacity'           => true,
		'transform'         => true,
		'style'             => true,
		'clip-path'         => true,
		'mask'              => true,
		'aria-hidden'       => true,
		'enable-background' => true,
	];

	$allowed = [
		'svg'            => $presentation + [
			'xmlns'               => true,
			'xmlns:xlink'         => true,
			'xml:space'           => true,
			'version'             => true,
			'viewbox'             => true,
			'width'               => true,
			'height'              => true,
			'x'                   => true,
			'y'                   => true,
			'role'                => true,
			'focusable'           => true,
			'aria-label'          => true,
			'aria-labelledby'     => true,
			'preserveaspectratio' => true,
		],
		'title'          => [ 'id' => true ],
		'desc'           => [ 'id' => true ],
		'g'              => $presentation,
		'defs'           => [],
		'clippath'       => [ 'id' => true ],
		'mask'           => [ 'id' => true ],
		'path'           => $presentation + [ 'd' => true ],
		'circle'         => $presentation + [ 'cx' => true, 'cy' => true, 'r' => true ],
		'ellipse'        => $presentation + [ 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ],
		'rect'           => $presentation + [ 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ],
		'line'           => $presentation + [ 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ],
		'polygon'        => $presentation + [ 'points' => true ],
		'polyline'       => $presentation + [ 'points' => true ],
		'lineargradient' => [ 'id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true, 'gradienttransform' => true ],
		'radialgradient' => [ 'id' => true, 'cx' => true, 'cy' => true, 'r' => true, 'fx' => true, 'fy' => true, 'gradientunits' => true, 'gradienttransform' => true ],
		'stop'           => [ 'offset' => true, 'stop-color' => true, 'stop-opacity' => true, 'style' => true ],
		'use'            => $presentation + [ 'href' => true, 'xlink:href' => true, 'x' => true, 'y' => true, 'width' => true, 'height' => true ],
	];

	return wp_kses( (string) $svg, $allowed );
}

/**
 * Turns a phone number as displayed into a tel: link target, e.g. "+44 (0)131 240 5000"
 * becomes "+4401312405000".
 *
 * @param string $phone Phone number as displayed.
 * @return string
 */
function ny_phone_href( $phone ) {
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * Outputs a link, adding target, rel and a hidden "(opens in a new tab)" note when needed.
 *
 * @param string $url   Link URL.
 * @param string $title Link text.
 * @param string $class Optional class for the link.
 * @param string $target Optional target, such as '_blank'.
 */
function ny_link( $url, $title, $class = '', $target = '' ) {
	$new_tab = '_blank' === $target;

	printf(
		'<a%1$s href="%2$s"%3$s>%4$s%5$s</a>',
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_url( $url ),
		$new_tab ? ' target="_blank" rel="noopener"' : '',
		esc_html( $title ),
		$new_tab ? ' <span class="visually-hidden">' . esc_html__( '(opens in a new tab)', 'ny-blank-theme' ) . '</span>' : ''
	);
}

/**
 * Outputs the items of a menu location as plain links separated by " / ", as used for the
 * legal links in the footer.
 *
 * @param string $location Menu location.
 */
function ny_inline_menu_links( $location ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return;
	}

	$items = wp_get_nav_menu_items( $locations[ $location ] ) ?: [];
	$items = array_filter( $items, fn( $item ) => 0 === (int) $item->menu_item_parent );

	foreach ( array_values( $items ) as $i => $item ) {
		echo $i ? ' / ' : '';
		ny_link( $item->url, $item->title, '', $item->target );
	}
}

/**
 * Outputs numeric pagination for post lists as ul.pagination, showing the first and last
 * pages and two pages either side of the current one.
 */
function ny_numeric_posts_nav() {
	global $wp_query;

	if ( is_singular() || $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$paged = max( 1, absint( get_query_var( 'paged' ) ) );
	$max   = (int) $wp_query->max_num_pages;
	$pages = array_unique( array_merge( [ 1, $max ], range( max( 1, $paged - 2 ), min( $max, $paged + 2 ) ) ) );
	sort( $pages );

	echo '<ul class="pagination">';

	if ( $paged > 1 ) {
		printf(
			'<li><a href="%1$s"><span aria-hidden="true">&laquo;</span><span class="visually-hidden">%2$s</span></a></li>',
			esc_url( get_pagenum_link( $paged - 1 ) ),
			esc_html__( 'Previous page', 'ny-blank-theme' )
		);
	}

	$previous = 0;
	foreach ( $pages as $page ) {
		if ( $previous && $page - $previous > 1 ) {
			echo '<li aria-hidden="true">&hellip;</li>';
		}

		printf(
			'<li%1$s><a href="%2$s" class="page"%3$s>%4$s</a></li>',
			$page === $paged ? ' class="active"' : '',
			esc_url( get_pagenum_link( $page ) ),
			$page === $paged ? ' aria-current="page"' : '',
			esc_html( number_format_i18n( $page ) )
		);

		$previous = $page;
	}

	if ( $paged < $max ) {
		printf(
			'<li><a href="%1$s"><span aria-hidden="true">&raquo;</span><span class="visually-hidden">%2$s</span></a></li>',
			esc_url( get_pagenum_link( $paged + 1 ) ),
			esc_html__( 'Next page', 'ny-blank-theme' )
		);
	}

	echo '</ul>';
}
