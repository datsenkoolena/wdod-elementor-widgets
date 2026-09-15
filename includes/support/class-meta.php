<?php
/**
 * Post meta helper with ACF awareness.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Class Meta.
 *
 * Reads a value through ACF's `get_field()` when ACF is active (so return
 * formats, repeaters and image arrays are honoured) and falls back to plain
 * `get_post_meta()` otherwise.
 */
class Meta {

	/**
	 * Returns a meta value.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $key      Field / meta key.
	 * @param mixed  $fallback Value returned when the field is empty.
	 *
	 * @return mixed
	 */
	public static function get( $post_id, $key, $fallback = '' ) {
		$post_id = (int) $post_id;
		$value   = null;

		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $key, $post_id );
		}

		if ( self::is_empty( $value ) ) {
			$value = get_post_meta( $post_id, $key, true );
		}

		/**
		 * Filters a meta value read by the plugin.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed  $value   Resolved value (or default).
		 * @param int    $post_id Post ID.
		 * @param string $key     Field key.
		 */
		return apply_filters( 'wdod_ew_meta_value', self::is_empty( $value ) ? $fallback : $value, $post_id, $key );
	}

	/**
	 * Returns an image URL from an image field.
	 *
	 * Understands ACF image fields in any return format (array, ID, URL)
	 * and plain post meta holding an attachment ID or URL.
	 *
	 * @param int    $post_id            Post ID.
	 * @param string $key                Field / meta key.
	 * @param string $size               Registered image size.
	 * @param bool   $fallback_thumbnail Use the featured image when the field is empty.
	 *
	 * @return string URL or empty string.
	 */
	public static function image( $post_id, $key, $size = 'full', $fallback_thumbnail = false ) {
		$post_id = (int) $post_id;
		$value   = self::get( $post_id, $key, '' );
		$url     = self::image_url_from_value( $value, $size );

		if ( '' === $url && $fallback_thumbnail && function_exists( 'get_post_thumbnail_id' ) ) {
			$thumbnail_id = (int) get_post_thumbnail_id( $post_id );

			if ( $thumbnail_id > 0 ) {
				$url = (string) wp_get_attachment_image_url( $thumbnail_id, $size );
			}
		}

		return $url;
	}

	/**
	 * Resolves an image URL from a raw field value.
	 *
	 * @param mixed  $value Attachment ID, URL or ACF image array.
	 * @param string $size  Registered image size.
	 *
	 * @return string
	 */
	public static function image_url_from_value( $value, $size = 'full' ) {
		if ( self::is_empty( $value ) ) {
			return '';
		}

		if ( is_array( $value ) ) {
			if ( isset( $value['sizes'][ $size ] ) && is_string( $value['sizes'][ $size ] ) ) {
				return esc_url_raw( $value['sizes'][ $size ] );
			}

			foreach ( array( 'ID', 'id' ) as $id_key ) {
				if ( ! empty( $value[ $id_key ] ) ) {
					$url = wp_get_attachment_image_url( (int) $value[ $id_key ], $size );

					if ( $url ) {
						return $url;
					}
				}
			}

			return isset( $value['url'] ) ? esc_url_raw( (string) $value['url'] ) : '';
		}

		if ( is_numeric( $value ) ) {
			$url = wp_get_attachment_image_url( (int) $value, $size );

			return $url ? $url : '';
		}

		return esc_url_raw( (string) $value );
	}

	/**
	 * Whether a value should be treated as "not set".
	 *
	 * @param mixed $value Value.
	 *
	 * @return bool
	 */
	private static function is_empty( $value ) {
		return null === $value || false === $value || '' === $value || array() === $value;
	}
}
