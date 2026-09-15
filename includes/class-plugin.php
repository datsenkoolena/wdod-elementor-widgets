<?php
/**
 * Main plugin class.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Plugin.
 *
 * Decides, once all plugins are loaded, whether the site can run the
 * Elementor widgets. Shortcodes are always registered so the templates keep
 * working with Elementor deactivated (or never installed).
 */
final class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Shortcode handler.
	 *
	 * @var Shortcodes|null
	 */
	public $shortcodes = null;

	/**
	 * Asset handler.
	 *
	 * @var Assets|null
	 */
	public $assets = null;

	/**
	 * Widget registrar, only set when Elementor is available.
	 *
	 * @var Widgets_Manager|null
	 */
	public $widgets = null;

	/**
	 * Admin notices, only set when requirements are not met.
	 *
	 * @var Admin_Notices|null
	 */
	public $notices = null;

	/**
	 * Whether init() already ran.
	 *
	 * @var bool
	 */
	private $booted = false;

	/**
	 * Returns the singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor: use instance().
	 */
	private function __construct() {}

	/**
	 * Activation callback. Stores the version so future upgrade routines can
	 * compare against it.
	 *
	 * @return void
	 */
	public static function activate() {
		update_option( 'wdod_ew_version', WDOD_EW_VERSION, false );
	}

	/**
	 * Boots the plugin. Hooked to `plugins_loaded`.
	 *
	 * @return void
	 */
	public function init() {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		add_action( 'init', array( $this, 'load_textdomain' ) );

		// Assets and shortcodes are always available.
		$this->assets     = new Assets();
		$this->shortcodes = new Shortcodes();

		$problem = $this->check_requirements();

		if ( '' === $problem ) {
			$this->widgets = new Widgets_Manager();
		} else {
			$this->notices = new Admin_Notices( $problem );
		}

		/**
		 * Fires after the plugin has finished booting.
		 *
		 * @since 1.0.0
		 *
		 * @param Plugin $plugin Plugin instance.
		 */
		do_action( 'wdod_ew_loaded', $this );
	}

	/**
	 * Loads the translation files.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'wdod-elementor-widgets',
			false,
			dirname( plugin_basename( WDOD_EW_FILE ) ) . '/languages'
		);
	}

	/**
	 * Checks whether the widgets can be registered.
	 *
	 * @return string Empty string when everything is fine, otherwise one of
	 *                'php', 'missing' or 'version'.
	 */
	public function check_requirements() {
		if ( version_compare( PHP_VERSION, WDOD_EW_MIN_PHP, '<' ) ) {
			return 'php';
		}

		if ( ! self::is_elementor_loaded() ) {
			return 'missing';
		}

		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, WDOD_EW_MIN_ELEMENTOR, '<' ) ) {
			return 'version';
		}

		return '';
	}

	/**
	 * Whether Elementor has loaded in this request.
	 *
	 * @return bool
	 */
	public static function is_elementor_loaded() {
		return (bool) did_action( 'elementor/loaded' );
	}

	/**
	 * Whether the current request is rendered inside the Elementor editor.
	 *
	 * @return bool
	 */
	public static function is_edit_mode() {
		if ( ! self::is_elementor_loaded() || ! class_exists( '\Elementor\Plugin' ) ) {
			return false;
		}

		$elementor = \Elementor\Plugin::$instance;

		return isset( $elementor->editor ) && $elementor->editor->is_edit_mode();
	}
}
