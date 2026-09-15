<?php
/**
 * Registers the widget category and the widgets with Elementor.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets;

defined( 'ABSPATH' ) || exit;

/**
 * Class Widgets_Manager.
 *
 * Widget classes extend `\Elementor\Widget_Base`, so they are only loaded
 * inside the `elementor/widgets/register` hook, never at file load time.
 */
class Widgets_Manager {

	/**
	 * Category slug used by all widgets.
	 *
	 * @var string
	 */
	const CATEGORY = 'wdod';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
	}

	/**
	 * Adds the "WDOD Widgets" category to the editor panel.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Elements manager.
	 *
	 * @return void
	 */
	public function register_category( $elements_manager ) {
		$elements_manager->add_category(
			self::CATEGORY,
			array(
				'title' => esc_html__( 'WDOD Widgets', 'wdod-elementor-widgets' ),
				'icon'  => 'fa fa-plug',
			)
		);
	}

	/**
	 * Returns the widget class names shipped with the plugin.
	 *
	 * @return string[]
	 */
	public function get_widget_classes() {
		$classes = array(
			Widgets\Pricing_Table::class,
			Widgets\Team_Member::class,
			Widgets\Testimonial_Slider::class,
		);

		/**
		 * Filters the list of widget classes to register.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $classes Fully qualified class names.
		 */
		return (array) apply_filters( 'wdod_ew_widget_classes', $classes );
	}

	/**
	 * Registers the widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 *
	 * @return void
	 */
	public function register_widgets( $widgets_manager ) {
		require_once WDOD_EW_DIR . 'includes/widgets/class-widget-base.php';
		require_once WDOD_EW_DIR . 'includes/widgets/class-pricing-table.php';
		require_once WDOD_EW_DIR . 'includes/widgets/class-team-member.php';
		require_once WDOD_EW_DIR . 'includes/widgets/class-testimonial-slider.php';

		foreach ( $this->get_widget_classes() as $class_name ) {
			if ( ! class_exists( $class_name ) ) {
				continue;
			}

			$widgets_manager->register( new $class_name() );
		}
	}
}
