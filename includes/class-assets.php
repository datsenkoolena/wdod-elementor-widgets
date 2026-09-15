<?php
/**
 * Registers and enqueues front-end and editor assets.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Assets.
 *
 * Assets are only *registered* on `wp_enqueue_scripts`; widgets enqueue them
 * through `get_style_depends()` / `get_script_depends()` and shortcodes call
 * `Assets::enqueue_frontend()` while rendering, so nothing loads on pages that
 * do not use the plugin.
 */
class Assets {

	/**
	 * Handle shared by the front-end stylesheet and script.
	 *
	 * @var string
	 */
	const HANDLE = 'wdod-ew-frontend';

	/**
	 * Editor stylesheet handle.
	 *
	 * @var string
	 */
	const EDITOR_HANDLE = 'wdod-ew-editor';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register' ) );
		add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_editor' ) );
	}

	/**
	 * Registers the front-end stylesheet and script.
	 *
	 * @return void
	 */
	public function register() {
		if ( wp_style_is( self::HANDLE, 'registered' ) ) {
			return;
		}

		wp_register_style(
			self::HANDLE,
			WDOD_EW_URL . 'assets/css/frontend.css',
			array(),
			WDOD_EW_VERSION
		);

		$deps = Plugin::is_elementor_loaded() ? array( 'elementor-frontend' ) : array();

		wp_register_script(
			self::HANDLE,
			WDOD_EW_URL . 'assets/js/frontend.js',
			$deps,
			WDOD_EW_VERSION,
			true
		);

		wp_localize_script(
			self::HANDLE,
			'wdodEwConfig',
			array(
				'breakpoints' => array(
					'tablet'  => 768,
					'desktop' => 1025,
				),
				'i18n'        => array(
					'prev' => esc_html__( 'Previous slide', 'wdod-elementor-widgets' ),
					'next' => esc_html__( 'Next slide', 'wdod-elementor-widgets' ),
					'goTo' => esc_html__( 'Go to slide', 'wdod-elementor-widgets' ),
				),
			)
		);
	}

	/**
	 * Enqueues the front-end assets. Safe to call from shortcode callbacks
	 * (late enqueue: styles are printed in the footer).
	 *
	 * @param bool $with_slider Whether the Swiper library should be loaded as
	 *                          well (only possible when Elementor ships it).
	 *
	 * @return void
	 */
	public static function enqueue_frontend( $with_slider = false ) {
		if ( ! wp_style_is( self::HANDLE, 'registered' ) ) {
			( new self() )->register();
		}

		wp_enqueue_style( self::HANDLE );
		wp_enqueue_script( self::HANDLE );

		if ( $with_slider && Plugin::is_elementor_loaded() ) {
			if ( wp_script_is( 'swiper', 'registered' ) ) {
				wp_enqueue_script( 'swiper' );
			}
			foreach ( array( 'swiper', 'e-swiper' ) as $style ) {
				if ( wp_style_is( $style, 'registered' ) ) {
					wp_enqueue_style( $style );
				}
			}
		}
	}

	/**
	 * Enqueues the small editor stylesheet (category icon colour, panel tweaks).
	 *
	 * @return void
	 */
	public function enqueue_editor() {
		wp_enqueue_style(
			self::EDITOR_HANDLE,
			WDOD_EW_URL . 'assets/css/editor.css',
			array(),
			WDOD_EW_VERSION
		);
	}
}
