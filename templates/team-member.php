<?php
/**
 * Team member template.
 *
 * Override by copying to `<theme>/wdod-elementor-widgets/team-member.php`.
 *
 * @var array $args {
 *     @type string $name        Member name.
 *     @type string $role        Role / job title.
 *     @type string $bio         Biography (limited HTML allowed).
 *     @type string $photo_html  Pre-rendered <img> markup (optional).
 *     @type string $photo_url   Photo URL used when $photo_html is empty.
 *     @type string $photo_alt   Photo alt text.
 *     @type array  $socials     List of ['network', 'label', 'url', 'icon_html', 'target', 'rel'].
 *     @type string $align       'left' | 'center' | 'right' | '' (controlled by CSS).
 *     @type string $shape       'circle' | 'rounded' | 'square'.
 *     @type string $extra_class Extra wrapper class.
 * }
 *
 * @package WDOD\ElementorWidgets
 */

use WDOD\ElementorWidgets\Support\Icons;
use WDOD\ElementorWidgets\Support\Template;

defined( 'ABSPATH' ) || exit;

$wdod_ew_name       = Template::arg( $args, 'name' );
$wdod_ew_role       = Template::arg( $args, 'role' );
$wdod_ew_bio        = Template::arg( $args, 'bio' );
$wdod_ew_photo_html = Template::arg( $args, 'photo_html' );
$wdod_ew_photo_url  = Template::arg( $args, 'photo_url' );
$wdod_ew_photo_alt  = Template::arg( $args, 'photo_alt', $wdod_ew_name );
$wdod_ew_socials    = (array) Template::arg( $args, 'socials', array() );
$wdod_ew_align      = Template::arg( $args, 'align' );
$wdod_ew_shape      = Template::arg( $args, 'shape', 'circle' );
$wdod_ew_classes    = array( 'wdod-ew-team', 'wdod-ew-team--' . sanitize_html_class( $wdod_ew_shape, 'circle' ) );

if ( in_array( $wdod_ew_align, array( 'left', 'center', 'right' ), true ) ) {
	$wdod_ew_classes[] = 'wdod-ew-team--align-' . $wdod_ew_align;
}

if ( '' !== Template::arg( $args, 'extra_class' ) ) {
	$wdod_ew_classes[] = Template::arg( $args, 'extra_class' );
}
?>
<div class="<?php echo esc_attr( implode( ' ', $wdod_ew_classes ) ); ?>">
	<?php if ( '' !== $wdod_ew_photo_html || '' !== $wdod_ew_photo_url ) : ?>
		<figure class="wdod-ew-team__photo">
			<?php if ( '' !== $wdod_ew_photo_html ) : ?>
				<?php echo wp_kses_post( $wdod_ew_photo_html ); ?>
			<?php else : ?>
				<img src="<?php echo esc_url( $wdod_ew_photo_url ); ?>" alt="<?php echo esc_attr( $wdod_ew_photo_alt ); ?>" loading="lazy" decoding="async" />
			<?php endif; ?>
		</figure>
	<?php endif; ?>

	<div class="wdod-ew-team__body">
		<?php if ( '' !== $wdod_ew_name ) : ?>
			<h3 class="wdod-ew-team__name"><?php echo esc_html( $wdod_ew_name ); ?></h3>
		<?php endif; ?>

		<?php if ( '' !== $wdod_ew_role ) : ?>
			<p class="wdod-ew-team__role"><?php echo esc_html( $wdod_ew_role ); ?></p>
		<?php endif; ?>

		<?php if ( '' !== $wdod_ew_bio ) : ?>
			<div class="wdod-ew-team__bio"><?php echo wp_kses_post( wpautop( $wdod_ew_bio ) ); ?></div>
		<?php endif; ?>

		<?php if ( ! empty( $wdod_ew_socials ) ) : ?>
			<ul class="wdod-ew-team__socials">
				<?php foreach ( $wdod_ew_socials as $wdod_ew_social ) : ?>
					<?php
					if ( empty( $wdod_ew_social['url'] ) ) {
						continue;
					}

					$wdod_ew_network = isset( $wdod_ew_social['network'] ) ? $wdod_ew_social['network'] : 'link';
					$wdod_ew_label   = ! empty( $wdod_ew_social['label'] ) ? $wdod_ew_social['label'] : ucfirst( $wdod_ew_network );
					$wdod_ew_icon    = ! empty( $wdod_ew_social['icon_html'] ) ? $wdod_ew_social['icon_html'] : Icons::social( $wdod_ew_network );
					$wdod_ew_new_tab = ! isset( $wdod_ew_social['target'] ) || ! empty( $wdod_ew_social['target'] );
					$wdod_ew_rel     = trim( ( isset( $wdod_ew_social['rel'] ) ? $wdod_ew_social['rel'] : '' ) . ( $wdod_ew_new_tab ? ' noopener' : '' ) );
					?>
					<li class="wdod-ew-team__social-item">
						<a class="wdod-ew-team__social wdod-ew-team__social--<?php echo esc_attr( $wdod_ew_network ); ?>"
							href="<?php echo esc_url( $wdod_ew_social['url'] ); ?>"
							target="<?php echo $wdod_ew_new_tab ? '_blank' : '_self'; ?>"
							rel="<?php echo esc_attr( $wdod_ew_rel ); ?>"
							aria-label="<?php echo esc_attr( $wdod_ew_label ); ?>">
							<?php echo Icons::kses( $wdod_ew_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Sanitised by Icons::kses(). ?>
							<span class="screen-reader-text"><?php echo esc_html( $wdod_ew_label ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
