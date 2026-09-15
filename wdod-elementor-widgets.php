<?php
/**
 * Plugin Name:       WDOD Elementor Widgets
 * Plugin URI:        https://github.com/datsenkoolena/wdod-elementor-widgets
 * Description:       Three production-ready Elementor widgets (Pricing Table, Team Member, Testimonial Slider) with shortcode fallbacks that keep working when Elementor is not installed.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Tested up to:      7.1
 * Author:            Olena Datsenko
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wdod-elementor-widgets
 * Domain Path:       /languages
 *
 * Elementor is intentionally NOT declared in a "Requires Plugins" header:
 * the shortcodes shipped with this plugin work without Elementor, and the
 * widgets are only registered when a compatible Elementor version is active.
 *
 * @package WDOD\ElementorWidgets
 */

defined( 'ABSPATH' ) || exit;

define( 'WDOD_EW_VERSION', '1.0.0' );
define( 'WDOD_EW_FILE', __FILE__ );
define( 'WDOD_EW_DIR', plugin_dir_path( __FILE__ ) );
define( 'WDOD_EW_URL', plugin_dir_url( __FILE__ ) );
define( 'WDOD_EW_MIN_ELEMENTOR', '3.5.0' );
define( 'WDOD_EW_MIN_PHP', '7.4' );

require_once WDOD_EW_DIR . 'includes/class-autoloader.php';

WDOD\ElementorWidgets\Autoloader::register();

register_activation_hook( __FILE__, array( WDOD\ElementorWidgets\Plugin::class, 'activate' ) );

/**
 * Returns the main plugin instance.
 *
 * @since 1.0.0
 *
 * @return WDOD\ElementorWidgets\Plugin
 */
function wdod_ew() {
	return WDOD\ElementorWidgets\Plugin::instance();
}

/**
 * Boots the plugin once every plugin has been loaded, so Elementor (if
 * present) has already fired `elementor/loaded`.
 *
 * @since 1.0.0
 *
 * @return void
 */
function wdod_ew_boot() {
	wdod_ew()->init();
}
add_action( 'plugins_loaded', 'wdod_ew_boot' );
