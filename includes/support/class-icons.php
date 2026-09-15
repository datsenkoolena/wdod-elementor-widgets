<?php
/**
 * Inline SVG icons used by the templates when no Elementor icon is chosen.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Class Icons.
 *
 * All methods return markup that only contains the tags listed in
 * `allowed_html()`, so it can be passed through `wp_kses()` safely.
 */
class Icons {

	/**
	 * Allowed HTML for icon markup (inline SVG or icon-font element).
	 *
	 * @return array
	 */
	public static function allowed_html() {
		return array(
			'svg'     => array(
				'class'       => true,
				'xmlns'       => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'fill'        => true,
				'stroke'      => true,
				'aria-hidden' => true,
				'focusable'   => true,
				'role'        => true,
			),
			'path'    => array(
				'd'               => true,
				'fill'            => true,
				'stroke'          => true,
				'stroke-width'    => true,
				'stroke-linecap'  => true,
				'stroke-linejoin' => true,
			),
			'circle'  => array(
				'cx'   => true,
				'cy'   => true,
				'r'    => true,
				'fill' => true,
			),
			'polygon' => array(
				'points' => true,
				'fill'   => true,
			),
			'i'       => array(
				'class'       => true,
				'aria-hidden' => true,
			),
			'span'    => array(
				'class'       => true,
				'aria-hidden' => true,
			),
		);
	}

	/**
	 * Sanitises icon markup.
	 *
	 * @param string $html Icon markup.
	 *
	 * @return string
	 */
	public static function kses( $html ) {
		return wp_kses( (string) $html, self::allowed_html() );
	}

	/**
	 * Wraps an SVG path in a 24x24 SVG element.
	 *
	 * @param string $path      Path data.
	 * @param string $css_class Extra CSS class.
	 * @param string $fill      Fill attribute.
	 *
	 * @return string
	 */
	private static function svg( $path, $css_class = '', $fill = 'currentColor' ) {
		return sprintf(
			'<svg class="wdod-ew-icon %1$s" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="%2$s" aria-hidden="true" focusable="false"><path d="%3$s"/></svg>',
			esc_attr( $css_class ),
			esc_attr( $fill ),
			esc_attr( $path )
		);
	}

	/**
	 * Check mark.
	 *
	 * @return string
	 */
	public static function check() {
		return self::svg( 'M9 16.2 4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4z', 'wdod-ew-icon--check' );
	}

	/**
	 * Cross mark.
	 *
	 * @return string
	 */
	public static function cross() {
		return self::svg( 'M19 6.4 17.6 5 12 10.6 6.4 5 5 6.4l5.6 5.6L5 17.6 6.4 19l5.6-5.6 5.6 5.6 1.4-1.4-5.6-5.6z', 'wdod-ew-icon--cross' );
	}

	/**
	 * Star (used for ratings).
	 *
	 * @return string
	 */
	public static function star() {
		return self::svg( 'M12 17.27 18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z', 'wdod-ew-icon--star' );
	}

	/**
	 * Chevron pointing left.
	 *
	 * @return string
	 */
	public static function chevron_left() {
		return self::svg( 'M15.4 7.4 14 6l-6 6 6 6 1.4-1.4L10.8 12z', 'wdod-ew-icon--chevron' );
	}

	/**
	 * Chevron pointing right.
	 *
	 * @return string
	 */
	public static function chevron_right() {
		return self::svg( 'M8.6 16.6 10 18l6-6-6-6-1.4 1.4 4.6 4.6z', 'wdod-ew-icon--chevron' );
	}

	/**
	 * Opening quote mark.
	 *
	 * @return string
	 */
	public static function quote() {
		return self::svg( 'M6.5 17.5c-1.7 0-3-1.3-3-3 0-3.4 2.3-6.6 5.7-8l.8 1.4c-2.2 1-3.6 2.7-3.9 4.4.2 0 .3-.1.5-.1 1.7 0 3 1.3 3 3s-1.4 3.3-3.1 3.3zm9 0c-1.7 0-3-1.3-3-3 0-3.4 2.3-6.6 5.7-8l.8 1.4c-2.2 1-3.6 2.7-3.9 4.4.2 0 .3-.1.5-.1 1.7 0 3 1.3 3 3s-1.4 3.3-3.1 3.3z', 'wdod-ew-icon--quote' );
	}

	/**
	 * Brand / generic icon for a social network.
	 *
	 * @param string $network Network slug (facebook, x, linkedin, instagram, github, youtube, email, link).
	 *
	 * @return string
	 */
	public static function social( $network ) {
		$paths = array(
			'facebook'  => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z',
			'x'         => 'M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z',
			'linkedin'  => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z',
			'instagram' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324zM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881z',
			'github'    => 'M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12',
			'youtube'   => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z',
			'email'     => 'M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z',
			'link'      => 'M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z',
		);

		$network = sanitize_key( $network );

		if ( 'twitter' === $network ) {
			$network = 'x';
		}

		$path = isset( $paths[ $network ] ) ? $paths[ $network ] : $paths['link'];

		return self::svg( $path, 'wdod-ew-icon--social wdod-ew-icon--' . $network );
	}
}
