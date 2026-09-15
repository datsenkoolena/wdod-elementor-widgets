<?php
/**
 * Shortcode fallbacks that render the same templates as the widgets.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

use WDOD\ElementorWidgets\Support\Meta;
use WDOD\ElementorWidgets\Support\Template;

defined( 'ABSPATH' ) || exit;

/**
 * Class Shortcodes.
 *
 * Available with or without Elementor:
 *
 *   [wdod_pricing_table title="Pro" price="29" currency="$" period="/mo"
 *       features="10 projects|Priority support|-White label" button_text="Buy"
 *       button_url="https://example.com" featured="yes" badge="Popular"]
 *
 *   [wdod_team_member post_id="12"]
 *   [wdod_team_member name="Jane Doe" role="CEO" photo="34"
 *       socials="linkedin:https://linkedin.com/in/jane|x:https://x.com/jane"]
 *
 *   [wdod_testimonials post_type="testimonial" count="6" autoplay="yes"
 *       loop="yes" slides="2" arrows="yes" dots="yes"]
 */
class Shortcodes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Registers the shortcodes.
	 *
	 * @return void
	 */
	public function register() {
		add_shortcode( 'wdod_pricing_table', array( $this, 'pricing_table' ) );
		add_shortcode( 'wdod_team_member', array( $this, 'team_member' ) );
		add_shortcode( 'wdod_testimonials', array( $this, 'testimonials' ) );
	}

	/**
	 * Converts a "yes"/"no"/"1"/"0"/"true"/"false" attribute to a boolean.
	 *
	 * @param mixed $value Raw attribute value.
	 *
	 * @return bool
	 */
	public static function to_bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}

		return in_array( strtolower( trim( (string) $value ) ), array( 'yes', 'true', '1', 'on' ), true );
	}

	/**
	 * Renders [wdod_pricing_table].
	 *
	 * Features are pipe-separated; prefix an item with "-" to mark it as not
	 * included (rendered with a cross instead of a check).
	 *
	 * @param array|string $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function pricing_table( $atts ) {
		$atts = shortcode_atts(
			array(
				'title'         => '',
				'badge'         => '',
				'price'         => '',
				'currency'      => '$',
				'period'        => '',
				'features'      => '',
				'button_text'   => '',
				'button_url'    => '',
				'button_target' => '',
				'featured'      => 'no',
				'class'         => '',
			),
			$atts,
			'wdod_pricing_table'
		);

		$features = array();

		foreach ( array_filter( array_map( 'trim', explode( '|', (string) $atts['features'] ) ) ) as $raw ) {
			$included = 0 !== strpos( $raw, '-' );

			$features[] = array(
				'text'      => sanitize_text_field( $included ? $raw : substr( $raw, 1 ) ),
				'included'  => $included,
				'icon_html' => '',
			);
		}

		$args = array(
			'title'         => sanitize_text_field( $atts['title'] ),
			'badge'         => sanitize_text_field( $atts['badge'] ),
			'price'         => sanitize_text_field( $atts['price'] ),
			'currency'      => sanitize_text_field( $atts['currency'] ),
			'period'        => sanitize_text_field( $atts['period'] ),
			'features'      => $features,
			'button_text'   => sanitize_text_field( $atts['button_text'] ),
			'button_url'    => esc_url_raw( $atts['button_url'] ),
			'button_target' => self::to_bool( $atts['button_target'] ),
			'button_rel'    => '',
			'featured'      => self::to_bool( $atts['featured'] ),
			'extra_class'   => sanitize_html_class( $atts['class'] ),
		);

		Assets::enqueue_frontend();

		return Template::render( 'pricing-table', $args );
	}

	/**
	 * Renders [wdod_team_member].
	 *
	 * @param array|string $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function team_member( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_id' => 0,
				'name'    => '',
				'role'    => '',
				'bio'     => '',
				'photo'   => '',
				'size'    => 'medium',
				'socials' => '',
				'align'   => 'center',
				'class'   => '',
			),
			$atts,
			'wdod_team_member'
		);

		$post_id = absint( $atts['post_id'] );
		$size    = sanitize_key( $atts['size'] );
		$size    = '' === $size ? 'medium' : $size;

		if ( $post_id > 0 ) {
			$args = self::team_member_args_from_post( $post_id, $size );
		} else {
			$args = array(
				'name'       => sanitize_text_field( $atts['name'] ),
				'role'       => sanitize_text_field( $atts['role'] ),
				'bio'        => wp_kses_post( $atts['bio'] ),
				'photo_html' => '',
				'photo_url'  => self::resolve_image( $atts['photo'], $size ),
				'photo_alt'  => sanitize_text_field( $atts['name'] ),
				'socials'    => self::parse_socials( $atts['socials'] ),
			);
		}

		$args['align']       = in_array( $atts['align'], array( 'left', 'center', 'right' ), true ) ? $atts['align'] : 'center';
		$args['extra_class'] = sanitize_html_class( $atts['class'] );

		Assets::enqueue_frontend();

		return Template::render( 'team-member', $args );
	}

	/**
	 * Renders [wdod_testimonials].
	 *
	 * @param array|string $atts Shortcode attributes.
	 *
	 * @return string
	 */
	public function testimonials( $atts ) {
		$atts = shortcode_atts(
			array(
				'post_type'      => 'testimonial',
				'count'          => 6,
				'ids'            => '',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'autoplay'       => 'yes',
				'autoplay_speed' => 5000,
				'loop'           => 'yes',
				'slides'         => 2,
				'slides_tablet'  => 2,
				'slides_mobile'  => 1,
				'space'          => 24,
				'arrows'         => 'yes',
				'dots'           => 'yes',
				'class'          => '',
			),
			$atts,
			'wdod_testimonials'
		);

		$post_type = sanitize_key( $atts['post_type'] );

		if ( '' === $post_type || ! post_type_exists( $post_type ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<p class="wdod-ew-notice-inline">' . sprintf(
					/* translators: %s: post type slug. */
					esc_html__( 'WDOD testimonials: the post type "%s" does not exist.', 'wdod-elementor-widgets' ),
					esc_html( $post_type )
				) . '</p>';
			}

			return '';
		}

		$query_args = array(
			'post_type'        => $post_type,
			'post_status'      => 'publish',
			'posts_per_page'   => min( 50, max( 1, absint( $atts['count'] ) ) ),
			'orderby'          => sanitize_key( $atts['orderby'] ),
			'order'            => 'ASC' === strtoupper( (string) $atts['order'] ) ? 'ASC' : 'DESC',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		);

		$ids = array_filter( array_map( 'absint', explode( ',', (string) $atts['ids'] ) ) );

		if ( ! empty( $ids ) ) {
			$query_args['post__in'] = $ids;
			$query_args['orderby']  = 'post__in';
		}

		/**
		 * Filters the query arguments used by [wdod_testimonials].
		 *
		 * @since 1.0.0
		 *
		 * @param array $query_args WP_Query arguments.
		 * @param array $atts       Shortcode attributes.
		 */
		$query_args = apply_filters( 'wdod_ew_testimonials_query_args', $query_args, $atts );

		$items = array();

		foreach ( get_posts( $query_args ) as $post ) {
			$items[] = array(
				'quote'      => wp_kses_post( $post->post_content ),
				'author'     => sanitize_text_field( Meta::get( $post->ID, 'author', get_the_title( $post ) ) ),
				'role'       => sanitize_text_field( Meta::get( $post->ID, 'role', '' ) ),
				'avatar_url' => Meta::image( $post->ID, 'avatar', 'thumbnail', true ),
				'avatar_alt' => sanitize_text_field( get_the_title( $post ) ),
				'rating'     => (int) Meta::get( $post->ID, 'rating', 5 ),
			);
		}

		if ( empty( $items ) ) {
			return '';
		}

		$args = array(
			'id'          => 'wdod-ew-testimonials-' . wp_unique_id(),
			'items'       => $items,
			'settings'    => array(
				'autoplay'      => self::to_bool( $atts['autoplay'] ),
				'autoplaySpeed' => max( 1000, absint( $atts['autoplay_speed'] ) ),
				'loop'          => self::to_bool( $atts['loop'] ),
				'pauseOnHover'  => true,
				'speed'         => 500,
				'slidesPerView' => array(
					'desktop' => max( 1, min( 4, absint( $atts['slides'] ) ) ),
					'tablet'  => max( 1, min( 4, absint( $atts['slides_tablet'] ) ) ),
					'mobile'  => max( 1, min( 4, absint( $atts['slides_mobile'] ) ) ),
				),
				'spaceBetween'  => array(
					'desktop' => absint( $atts['space'] ),
					'tablet'  => absint( $atts['space'] ),
					'mobile'  => absint( $atts['space'] ),
				),
				'arrows'        => self::to_bool( $atts['arrows'] ),
				'dots'          => self::to_bool( $atts['dots'] ),
			),
			'show_arrows' => self::to_bool( $atts['arrows'] ),
			'show_dots'   => self::to_bool( $atts['dots'] ),
			'extra_class' => sanitize_html_class( $atts['class'] ),
		);

		Assets::enqueue_frontend( true );

		return Template::render( 'testimonial-slider', $args );
	}

	/**
	 * Builds team member template arguments from a post.
	 *
	 * Reads `role`, `bio`, `photo` and `socials` through Meta (ACF or plain
	 * post meta) and falls back to the post title, excerpt and featured image.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $size    Image size.
	 *
	 * @return array
	 */
	public static function team_member_args_from_post( $post_id, $size = 'medium' ) {
		$post = get_post( $post_id );

		if ( ! $post ) {
			return array(
				'name'       => '',
				'role'       => '',
				'bio'        => '',
				'photo_html' => '',
				'photo_url'  => '',
				'photo_alt'  => '',
				'socials'    => array(),
			);
		}

		$bio = Meta::get( $post_id, 'bio', '' );

		if ( '' === $bio ) {
			$bio = has_excerpt( $post ) ? get_the_excerpt( $post ) : '';
		}

		$socials = Meta::get( $post_id, 'socials', array() );

		return array(
			'name'       => sanitize_text_field( get_the_title( $post ) ),
			'role'       => sanitize_text_field( Meta::get( $post_id, 'role', '' ) ),
			'bio'        => wp_kses_post( $bio ),
			'photo_html' => '',
			'photo_url'  => Meta::image( $post_id, 'photo', $size, true ),
			'photo_alt'  => sanitize_text_field( get_the_title( $post ) ),
			'socials'    => self::normalize_socials( $socials ),
		);
	}

	/**
	 * Parses the `socials` shortcode attribute ("network:url|network:url").
	 *
	 * @param string $raw Raw attribute value.
	 *
	 * @return array
	 */
	public static function parse_socials( $raw ) {
		$socials = array();

		foreach ( array_filter( array_map( 'trim', explode( '|', (string) $raw ) ) ) as $pair ) {
			$parts = explode( ':', $pair, 2 );

			if ( 2 !== count( $parts ) ) {
				continue;
			}

			$socials[] = array(
				'network' => sanitize_key( $parts[0] ),
				'url'     => trim( $parts[1] ),
			);
		}

		return self::normalize_socials( $socials );
	}

	/**
	 * Normalises social links coming from meta / ACF repeaters / shortcode
	 * attributes into the shape the template expects.
	 *
	 * Accepted item shapes: ['network' => 'x', 'url' => '...'],
	 * ['label' => 'X', 'url' => '...'], ['icon' => 'fab fa-x-twitter', 'url' => '...'].
	 *
	 * @param mixed $socials Raw value.
	 *
	 * @return array
	 */
	public static function normalize_socials( $socials ) {
		$result = array();

		if ( is_string( $socials ) ) {
			$decoded = json_decode( $socials, true );
			$socials = is_array( $decoded ) ? $decoded : array();
		}

		if ( ! is_array( $socials ) ) {
			return $result;
		}

		foreach ( $socials as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$url = isset( $item['url'] ) ? $item['url'] : ( isset( $item['link'] ) ? $item['link'] : '' );

			if ( is_array( $url ) ) {
				$url = isset( $url['url'] ) ? $url['url'] : '';
			}

			$url = esc_url_raw( (string) $url );

			if ( '' === $url ) {
				continue;
			}

			$network = isset( $item['network'] ) ? sanitize_key( $item['network'] ) : '';

			if ( '' === $network ) {
				$network = self::guess_network( $url );
			}

			$result[] = array(
				'network'   => $network,
				'label'     => isset( $item['label'] ) ? sanitize_text_field( $item['label'] ) : ucfirst( $network ),
				'url'       => $url,
				'icon_html' => isset( $item['icon_html'] ) ? (string) $item['icon_html'] : '',
			);
		}

		return $result;
	}

	/**
	 * Guesses the social network from a URL host.
	 *
	 * @param string $url URL.
	 *
	 * @return string
	 */
	public static function guess_network( $url ) {
		$host = (string) wp_parse_url( $url, PHP_URL_HOST );
		$host = preg_replace( '/^www\./', '', strtolower( $host ) );

		$map = array(
			'facebook.com'  => 'facebook',
			'fb.com'        => 'facebook',
			'x.com'         => 'x',
			'twitter.com'   => 'x',
			'linkedin.com'  => 'linkedin',
			'instagram.com' => 'instagram',
			'github.com'    => 'github',
			'youtube.com'   => 'youtube',
			'youtu.be'      => 'youtube',
		);

		if ( isset( $map[ $host ] ) ) {
			return $map[ $host ];
		}

		if ( 0 === strpos( $url, 'mailto:' ) ) {
			return 'email';
		}

		return 'link';
	}

	/**
	 * Resolves an attachment ID or URL to an image URL.
	 *
	 * @param mixed  $value Attachment ID or URL.
	 * @param string $size  Image size.
	 *
	 * @return string
	 */
	public static function resolve_image( $value, $size = 'medium' ) {
		$value = trim( (string) $value );

		if ( '' === $value ) {
			return '';
		}

		if ( is_numeric( $value ) ) {
			$url = wp_get_attachment_image_url( (int) $value, $size );

			return $url ? $url : '';
		}

		return esc_url_raw( $value );
	}
}
