<?php
/**
 * Pricing table template.
 *
 * Override by copying to `<theme>/wdod-elementor-widgets/pricing-table.php`.
 *
 * @var array $args {
 *     @type string $title         Plan title.
 *     @type string $badge         Badge text (empty to hide).
 *     @type string $price         Price.
 *     @type string $currency      Currency symbol.
 *     @type string $period        Billing period.
 *     @type array  $features      List of ['text' => string, 'included' => bool, 'icon_html' => string].
 *     @type string $button_text   Button label.
 *     @type string $button_url    Button URL.
 *     @type bool   $button_target Open in a new tab.
 *     @type string $button_rel    Extra rel values.
 *     @type bool   $featured      Highlight the plan.
 *     @type string $extra_class   Extra wrapper class.
 * }
 *
 * @package WDOD\ElementorWidgets
 */

use WDOD\ElementorWidgets\Support\Icons;
use WDOD\ElementorWidgets\Support\Template;

defined( 'ABSPATH' ) || exit;

$wdod_ew_title    = Template::arg( $args, 'title' );
$wdod_ew_badge    = Template::arg( $args, 'badge' );
$wdod_ew_price    = Template::arg( $args, 'price' );
$wdod_ew_currency = Template::arg( $args, 'currency' );
$wdod_ew_period   = Template::arg( $args, 'period' );
$wdod_ew_features = (array) Template::arg( $args, 'features', array() );
$wdod_ew_btn_text = Template::arg( $args, 'button_text' );
$wdod_ew_btn_url  = Template::arg( $args, 'button_url' );
$wdod_ew_btn_new  = (bool) Template::arg( $args, 'button_target', false );
$wdod_ew_btn_rel  = trim( Template::arg( $args, 'button_rel' ) . ( $wdod_ew_btn_new ? ' noopener' : '' ) );
$wdod_ew_featured = (bool) Template::arg( $args, 'featured', false );
$wdod_ew_classes  = array( 'wdod-ew-pricing' );

if ( $wdod_ew_featured ) {
	$wdod_ew_classes[] = 'wdod-ew-pricing--featured';
}

if ( '' !== Template::arg( $args, 'extra_class' ) ) {
	$wdod_ew_classes[] = Template::arg( $args, 'extra_class' );
}
?>
<div class="<?php echo esc_attr( implode( ' ', $wdod_ew_classes ) ); ?>">
	<?php if ( '' !== $wdod_ew_badge ) : ?>
		<span class="wdod-ew-pricing__badge"><?php echo esc_html( $wdod_ew_badge ); ?></span>
	<?php endif; ?>

	<div class="wdod-ew-pricing__header">
		<?php if ( '' !== $wdod_ew_title ) : ?>
			<h3 class="wdod-ew-pricing__title"><?php echo esc_html( $wdod_ew_title ); ?></h3>
		<?php endif; ?>

		<?php if ( '' !== $wdod_ew_price ) : ?>
			<p class="wdod-ew-pricing__price">
				<?php if ( '' !== $wdod_ew_currency ) : ?>
					<span class="wdod-ew-pricing__currency"><?php echo esc_html( $wdod_ew_currency ); ?></span>
				<?php endif; ?>
				<span class="wdod-ew-pricing__amount"><?php echo esc_html( $wdod_ew_price ); ?></span>
				<?php if ( '' !== $wdod_ew_period ) : ?>
					<span class="wdod-ew-pricing__period"><?php echo esc_html( $wdod_ew_period ); ?></span>
				<?php endif; ?>
			</p>
		<?php endif; ?>
	</div>

	<?php if ( ! empty( $wdod_ew_features ) ) : ?>
		<ul class="wdod-ew-pricing__features">
			<?php foreach ( $wdod_ew_features as $wdod_ew_feature ) : ?>
				<?php
				$wdod_ew_included = ! empty( $wdod_ew_feature['included'] );
				$wdod_ew_icon     = ! empty( $wdod_ew_feature['icon_html'] ) ? $wdod_ew_feature['icon_html'] : ( $wdod_ew_included ? Icons::check() : Icons::cross() );
				?>
				<li class="wdod-ew-pricing__feature wdod-ew-pricing__feature--<?php echo $wdod_ew_included ? 'included' : 'excluded'; ?>">
					<span class="wdod-ew-pricing__feature-icon"><?php echo Icons::kses( $wdod_ew_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?></span>
					<span class="wdod-ew-pricing__feature-text"><?php echo esc_html( isset( $wdod_ew_feature['text'] ) ? $wdod_ew_feature['text'] : '' ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

	<?php if ( '' !== $wdod_ew_btn_text && '' !== $wdod_ew_btn_url ) : ?>
		<div class="wdod-ew-pricing__footer">
			<a class="wdod-ew-pricing__button wdod-ew-button"
				href="<?php echo esc_url( $wdod_ew_btn_url ); ?>"
				target="<?php echo $wdod_ew_btn_new ? '_blank' : '_self'; ?>"
				rel="<?php echo esc_attr( $wdod_ew_btn_rel ); ?>">
				<?php echo esc_html( $wdod_ew_btn_text ); ?>
			</a>
		</div>
	<?php endif; ?>
</div>
