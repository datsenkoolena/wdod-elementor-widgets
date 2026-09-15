<?php
/**
 * Testimonial slider template (Swiper-compatible markup).
 *
 * Override by copying to `<theme>/wdod-elementor-widgets/testimonial-slider.php`.
 *
 * @var array $args {
 *     @type string $id          Unique element ID.
 *     @type array  $items       List of ['quote', 'author', 'role', 'avatar_url', 'avatar_alt', 'rating'].
 *                               A rating below 0 hides the stars.
 *     @type array  $settings    Slider settings serialised into data-settings.
 *     @type bool   $show_arrows Render prev/next buttons.
 *     @type bool   $show_dots   Render the pagination container.
 *     @type string $extra_class Extra wrapper class.
 * }
 *
 * @package WDOD\ElementorWidgets
 */

use WDOD\ElementorWidgets\Support\Icons;
use WDOD\ElementorWidgets\Support\Template;

defined( 'ABSPATH' ) || exit;

$wdod_ew_id       = Template::arg( $args, 'id', 'wdod-ew-testimonials' );
$wdod_ew_items    = (array) Template::arg( $args, 'items', array() );
$wdod_ew_settings = (array) Template::arg( $args, 'settings', array() );
$wdod_ew_arrows   = (bool) Template::arg( $args, 'show_arrows', true );
$wdod_ew_dots     = (bool) Template::arg( $args, 'show_dots', true );
$wdod_ew_classes  = array( 'wdod-ew-testimonials', 'swiper', 'swiper-container' );

if ( '' !== Template::arg( $args, 'extra_class' ) ) {
	$wdod_ew_classes[] = Template::arg( $args, 'extra_class' );
}

if ( empty( $wdod_ew_items ) ) {
	return;
}
?>
<div id="<?php echo esc_attr( $wdod_ew_id ); ?>"
	class="<?php echo esc_attr( implode( ' ', $wdod_ew_classes ) ); ?>"
	data-settings="<?php echo esc_attr( wp_json_encode( $wdod_ew_settings ) ); ?>"
	role="region"
	aria-roledescription="carousel"
	aria-label="<?php esc_attr_e( 'Testimonials', 'wdod-elementor-widgets' ); ?>">
	<div class="swiper-wrapper wdod-ew-testimonials__wrapper">
		<?php foreach ( $wdod_ew_items as $wdod_ew_index => $wdod_ew_item ) : ?>
			<?php
			$wdod_ew_quote  = isset( $wdod_ew_item['quote'] ) ? $wdod_ew_item['quote'] : '';
			$wdod_ew_author = isset( $wdod_ew_item['author'] ) ? $wdod_ew_item['author'] : '';
			$wdod_ew_role   = isset( $wdod_ew_item['role'] ) ? $wdod_ew_item['role'] : '';
			$wdod_ew_avatar = isset( $wdod_ew_item['avatar_url'] ) ? $wdod_ew_item['avatar_url'] : '';
			$wdod_ew_alt    = isset( $wdod_ew_item['avatar_alt'] ) ? $wdod_ew_item['avatar_alt'] : $wdod_ew_author;
			$wdod_ew_rating = isset( $wdod_ew_item['rating'] ) ? (int) $wdod_ew_item['rating'] : -1;
			$wdod_ew_rating = min( 5, $wdod_ew_rating );
			?>
			<div class="swiper-slide wdod-ew-testimonials__slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%1$d / %2$d', $wdod_ew_index + 1, count( $wdod_ew_items ) ) ); ?>">
				<figure class="wdod-ew-testimonial">
					<?php if ( $wdod_ew_rating >= 0 ) : ?>
						<div class="wdod-ew-rating" aria-label="<?php echo esc_attr( sprintf( /* translators: %d: rating out of 5. */ __( 'Rated %d out of 5', 'wdod-elementor-widgets' ), $wdod_ew_rating ) ); ?>">
							<?php for ( $wdod_ew_star = 1; $wdod_ew_star <= 5; $wdod_ew_star++ ) : ?>
								<span class="wdod-ew-rating__star wdod-ew-rating__star--<?php echo $wdod_ew_star <= $wdod_ew_rating ? 'filled' : 'empty'; ?>"><?php echo Icons::kses( Icons::star() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?></span>
							<?php endfor; ?>
						</div>
					<?php endif; ?>

					<?php if ( '' !== $wdod_ew_quote ) : ?>
						<blockquote class="wdod-ew-testimonial__quote">
							<span class="wdod-ew-testimonial__quote-icon"><?php echo Icons::kses( Icons::quote() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?></span>
							<?php echo wp_kses_post( wpautop( $wdod_ew_quote ) ); ?>
						</blockquote>
					<?php endif; ?>

					<figcaption class="wdod-ew-testimonial__meta">
						<?php if ( '' !== $wdod_ew_avatar ) : ?>
							<img class="wdod-ew-testimonial__avatar" src="<?php echo esc_url( $wdod_ew_avatar ); ?>" alt="<?php echo esc_attr( $wdod_ew_alt ); ?>" loading="lazy" decoding="async" width="56" height="56" />
						<?php endif; ?>
						<span class="wdod-ew-testimonial__meta-text">
							<?php if ( '' !== $wdod_ew_author ) : ?>
								<span class="wdod-ew-testimonial__author"><?php echo esc_html( $wdod_ew_author ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $wdod_ew_role ) : ?>
								<span class="wdod-ew-testimonial__role"><?php echo esc_html( $wdod_ew_role ); ?></span>
							<?php endif; ?>
						</span>
					</figcaption>
				</figure>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( $wdod_ew_dots ) : ?>
		<div class="swiper-pagination wdod-ew-testimonials__pagination"></div>
	<?php endif; ?>

	<?php if ( $wdod_ew_arrows ) : ?>
		<button type="button" class="wdod-ew-testimonials__arrow wdod-ew-testimonials__arrow--prev" aria-label="<?php esc_attr_e( 'Previous slide', 'wdod-elementor-widgets' ); ?>">
			<?php echo Icons::kses( Icons::chevron_left() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?>
		</button>
		<button type="button" class="wdod-ew-testimonials__arrow wdod-ew-testimonials__arrow--next" aria-label="<?php esc_attr_e( 'Next slide', 'wdod-elementor-widgets' ); ?>">
			<?php echo Icons::kses( Icons::chevron_right() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?>
		</button>
	<?php endif; ?>
</div>
