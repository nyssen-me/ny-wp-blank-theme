<?php
/**
 * Site footer. Contact details come from Theme Settings, the legal links from the
 * "Footer menu" location.
 *
 * @package ny-blank-theme
 */

$ny_option = fn( $name ) => function_exists( 'get_field' ) ? get_field( $name, 'option' ) : null;

$ny_contact_button  = $ny_option( 'contact_button' );
$ny_address_heading = $ny_option( 'address_heading' );
$ny_address         = $ny_option( 'address' );
$ny_phone           = $ny_option( 'phone' );
$ny_email           = $ny_option( 'email' );
$ny_social_links    = $ny_option( 'social_links' ) ?: [];
$ny_legal_text      = $ny_option( 'legal_text' );
?>
	<footer class="footer wrapper">

		<div class="row row-bottom">
			<div class="column column-contact">
				<?php if ( ! empty( $ny_contact_button['url'] ) ) : ?>
					<a class="button-contact" href="<?php echo esc_url( $ny_contact_button['url'] ); ?>"<?php echo '_blank' === ( $ny_contact_button['target'] ?? '' ) ?' target="_blank" rel="noopener"' : ''; ?>><span><?php echo esc_html( $ny_contact_button['title'] ); ?></span></a>
				<?php endif; ?>
			</div>

			<div class="column">

				<div class="container-footer">
					<div class="row">
						<div class="column">
							<?php if ( $ny_address_heading ) : ?>
								<strong><?php echo esc_html( $ny_address_heading ); ?></strong>
							<?php endif; ?>
							<?php if ( $ny_address ) : ?>
								<address>
									<?php echo wp_kses( $ny_address, [ 'br' => [] ] ); ?>
								</address>
							<?php endif; ?>
						</div>

						<div class="column">
							<ul class="contact-media">
								<?php if ( $ny_phone ) : ?>
									<li><strong><?php esc_html_e( 'Telephone:', 'ny-blank-theme' ); ?></strong> <a class="phone" href="<?php echo esc_attr( ny_phone_href( $ny_phone ) ); ?>"><?php echo esc_html( $ny_phone ); ?></a></li>
								<?php endif; ?>
								<?php if ( $ny_email ) : ?>
									<li><strong><?php esc_html_e( 'Email:', 'ny-blank-theme' ); ?></strong> <a class="email" href="<?php echo esc_url( 'mailto:' . $ny_email ); ?>"><?php echo esc_html( antispambot( $ny_email ) ); ?></a></li>
								<?php endif; ?>
								<?php if ( $ny_social_links ) : ?>
									<li>
										<?php foreach ( $ny_social_links as $ny_i => $ny_social ) : ?>
											<?php echo $ny_i ? ' / ' : ''; ?><strong><?php ny_link( $ny_social['url'], $ny_social['label'], '', '_blank' ); ?></strong>
										<?php endforeach; ?>
									</li>
								<?php endif; ?>
							</ul>
						</div>

						<div class="column">
							<?php echo wp_kses_post( (string) $ny_legal_text ); ?>

							<div class="legal-links">
								<?php ny_inline_menu_links( 'footer' ); ?>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>

	</footer>

<?php wp_footer(); ?>
</body>
</html>
