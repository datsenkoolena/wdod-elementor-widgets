<?php
/**
 * Template loader shared by widgets and shortcodes.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Class Template.
 *
 * Templates are plain PHP views reading a single `$args` array. A theme can
 * override any template by copying it to
 * `<theme>/wdod-elementor-widgets/<name>.php`, and developers can point to a
 * completely different file with the `wdod_ew_template_path` filter.
 */
class Template {

	/**
	 * Theme sub-directory searched for overrides.
	 *
	 * @var string
	 */
	const THEME_DIR = 'wdod-elementor-widgets';

	/**
	 * Renders a template and returns the output.
	 *
	 * @param string $name Template name without extension (e.g. 'pricing-table').
	 * @param array  $args Data made available to the template as `$args`.
	 *
	 * @return string
	 */
	public static function render( $name, array $args = array() ) {
		$file = self::locate( $name, $args );

		if ( '' === $file ) {
			return '';
		}

		/**
		 * Filters the arguments passed to a template.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $args Template arguments.
		 * @param string $name Template name.
		 */
		$args = (array) apply_filters( 'wdod_ew_template_args', $args, $name );

		ob_start();
		include $file;

		return (string) ob_get_clean();
	}

	/**
	 * Prints a template.
	 *
	 * @param string $name Template name.
	 * @param array  $args Template arguments.
	 *
	 * @return void
	 */
	public static function output( $name, array $args = array() ) {
		// Templates escape every value they print.
		echo self::render( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Finds the template file: theme override first, then the plugin default.
	 *
	 * @param string $name Template name.
	 * @param array  $args Template arguments (passed to the filter for context).
	 *
	 * @return string Absolute path or empty string when nothing was found.
	 */
	public static function locate( $name, array $args = array() ) {
		$name = preg_replace( '/[^a-z0-9\-_]/', '', strtolower( (string) $name ) );

		if ( '' === $name ) {
			return '';
		}

		$relative = self::THEME_DIR . '/' . $name . '.php';
		$file     = locate_template( $relative );

		if ( ! $file ) {
			$file = WDOD_EW_DIR . 'templates/' . $name . '.php';
		}

		/**
		 * Filters the absolute path of a template file.
		 *
		 * @since 1.0.0
		 *
		 * @param string $file Absolute path.
		 * @param string $name Template name.
		 * @param array  $args Template arguments.
		 */
		$file = (string) apply_filters( 'wdod_ew_template_path', $file, $name, $args );

		return file_exists( $file ) ? $file : '';
	}

	/**
	 * Returns a value from the arguments array with a default.
	 *
	 * @param array  $args     Arguments.
	 * @param string $key      Key.
	 * @param mixed  $fallback Default.
	 *
	 * @return mixed
	 */
	public static function arg( array $args, $key, $fallback = '' ) {
		return isset( $args[ $key ] ) ? $args[ $key ] : $fallback;
	}
}
