<?php
/**
 * PSR-4 style autoloader that maps the `WDOD\ElementorWidgets` namespace to
 * WordPress-style file names (`class-foo-bar.php`).
 *
 * The runtime never depends on Composer's `vendor/` directory; this
 * autoloader is enough to run the plugin from a plain zip.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Autoloader.
 *
 * Examples:
 *   WDOD\ElementorWidgets\Widgets\Pricing_Table -> includes/widgets/class-pricing-table.php
 *   WDOD\ElementorWidgets\Support\Meta          -> includes/support/class-meta.php
 *   WDOD\ElementorWidgets\Plugin                -> includes/class-plugin.php
 */
final class Autoloader {

	/**
	 * Namespace prefix handled by this autoloader.
	 *
	 * @var string
	 */
	const PREFIX = 'WDOD\\ElementorWidgets\\';

	/**
	 * Registers the autoloader with SPL.
	 *
	 * @return void
	 */
	public static function register() {
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Loads the file for a class, if it belongs to this plugin.
	 *
	 * @param string $class_name Fully qualified class name.
	 *
	 * @return void
	 */
	public static function autoload( $class_name ) {
		if ( 0 !== strpos( $class_name, self::PREFIX ) ) {
			return;
		}

		$file = self::class_to_file( $class_name );

		if ( $file && file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Converts a fully qualified class name to an absolute file path.
	 *
	 * @param string $class_name Fully qualified class name.
	 *
	 * @return string Absolute path (may not exist).
	 */
	public static function class_to_file( $class_name ) {
		$relative = substr( $class_name, strlen( self::PREFIX ) );
		$parts    = explode( '\\', $relative );
		$class    = array_pop( $parts );

		$path = WDOD_EW_DIR . 'includes/';

		foreach ( $parts as $part ) {
			$path .= strtolower( str_replace( '_', '-', $part ) ) . '/';
		}

		return $path . 'class-' . strtolower( str_replace( '_', '-', $class ) ) . '.php';
	}
}
