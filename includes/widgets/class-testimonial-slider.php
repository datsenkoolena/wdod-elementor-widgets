<?php
/**
 * Testimonial Slider widget.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Class Testimonial_Slider.
 *
 * Renders Swiper-compatible markup. On the front end the slider is
 * initialised through `elementorFrontend.utils.swiper` (Elementor's bundled
 * Swiper); when that is unavailable the stylesheet provides a scroll-snap
 * fallback and the script wires the arrows to it.
 */
class Testimonial_Slider extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wdod-testimonial-slider';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Testimonial Slider', 'wdod-elementor-widgets' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-testimonial-carousel';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'wdod', 'testimonial', 'review', 'slider', 'carousel', 'quote' );
	}

	/**
	 * Scripts enqueued with the widget. `swiper` is Elementor's own handle.
	 *
	 * @return string[]
	 */
	public function get_script_depends() {
		return array( 'swiper', 'wdod-ew-frontend' );
	}

	/**
	 * Stylesheets enqueued with the widget.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'swiper', 'e-swiper', 'wdod-ew-frontend' );
	}

	/**
	 * Registers controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_items_controls();
		$this->register_slider_controls();
		$this->register_card_style_controls();
		$this->register_text_style_controls();
		$this->register_navigation_style_controls();
	}

	/**
	 * Content > Testimonials.
	 *
	 * @return void
	 */
	private function register_items_controls() {
		$this->start_controls_section(
			'section_items',
			array(
				'label' => esc_html__( 'Testimonials', 'wdod-elementor-widgets' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'quote',
			array(
				'label'   => esc_html__( 'Quote', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'rows'    => 5,
				'default' => esc_html__( 'Working with this team was a pleasure: clear communication, on-time delivery and a site that finally loads fast.', 'wdod-elementor-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'author',
			array(
				'label'   => esc_html__( 'Author', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Alex Johnson', 'wdod-elementor-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'role',
			array(
				'label'   => esc_html__( 'Role / Company', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Marketing Director, Acme Inc.', 'wdod-elementor-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'avatar',
			array(
				'label'   => esc_html__( 'Avatar', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic' => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'rating',
			array(
				'label'   => esc_html__( 'Rating (0–5)', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 0,
				'max'     => 5,
				'step'    => 1,
				'default' => 5,
			)
		);

		$this->add_control(
			'items',
			array(
				'label'       => esc_html__( 'Items', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'author' => esc_html__( 'Alex Johnson', 'wdod-elementor-widgets' ),
						'role'   => esc_html__( 'Marketing Director, Acme Inc.', 'wdod-elementor-widgets' ),
						'rating' => 5,
					),
					array(
						'author' => esc_html__( 'Maria Garcia', 'wdod-elementor-widgets' ),
						'role'   => esc_html__( 'Founder, Bloom Studio', 'wdod-elementor-widgets' ),
						'quote'  => esc_html__( 'The new store increased our conversion rate within the first month. Highly recommended.', 'wdod-elementor-widgets' ),
						'rating' => 5,
					),
					array(
						'author' => esc_html__( 'Sam Lee', 'wdod-elementor-widgets' ),
						'role'   => esc_html__( 'CTO, Northwind', 'wdod-elementor-widgets' ),
						'quote'  => esc_html__( 'Clean code, documented hooks and no surprises during the handover.', 'wdod-elementor-widgets' ),
						'rating' => 4,
					),
				),
				'title_field' => '{{{ author }}}',
			)
		);

		$this->add_control(
			'show_rating',
			array(
				'label'        => esc_html__( 'Show rating', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Slider settings.
	 *
	 * @return void
	 */
	private function register_slider_controls() {
		$this->start_controls_section(
			'section_slider',
			array(
				'label' => esc_html__( 'Slider', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_responsive_control(
			'slides_per_view',
			array(
				'label'          => esc_html__( 'Slides per view', 'wdod-elementor-widgets' ),
				'type'           => Controls_Manager::SELECT,
				'options'        => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
				),
				'default'        => '2',
				'tablet_default' => '2',
				'mobile_default' => '1',
			)
		);

		$this->add_responsive_control(
			'space_between',
			array(
				'label'          => esc_html__( 'Space between (px)', 'wdod-elementor-widgets' ),
				'type'           => Controls_Manager::NUMBER,
				'min'            => 0,
				'max'            => 100,
				'default'        => 24,
				'tablet_default' => 20,
				'mobile_default' => 16,
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => esc_html__( 'Autoplay speed (ms)', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::NUMBER,
				'min'       => 1000,
				'max'       => 20000,
				'step'      => 500,
				'default'   => 5000,
				'condition' => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause on hover', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => array( 'autoplay' => 'yes' ),
			)
		);

		$this->add_control(
			'loop',
			array(
				'label'        => esc_html__( 'Infinite loop', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Transition speed (ms)', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'min'     => 100,
				'max'     => 3000,
				'step'    => 50,
				'default' => 500,
			)
		);

		$this->add_control(
			'show_arrows',
			array(
				'label'        => esc_html__( 'Arrows', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			)
		);

		$this->add_control(
			'show_dots',
			array(
				'label'        => esc_html__( 'Dots', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Card.
	 *
	 * @return void
	 */
	private function register_card_style_controls() {
		$this->start_controls_section(
			'section_card_style',
			array(
				'label' => esc_html__( 'Card', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'card_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonial' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'card_border',
				'selector' => '{{WRAPPER}} .wdod-ew-testimonial',
			)
		);

		$this->add_responsive_control(
			'card_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-testimonial' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'card_shadow',
				'selector' => '{{WRAPPER}} .wdod-ew-testimonial',
			)
		);

		$this->add_responsive_control(
			'card_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-testimonial' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Text.
	 *
	 * @return void
	 */
	private function register_text_style_controls() {
		$this->start_controls_section(
			'section_text_style',
			array(
				'label' => esc_html__( 'Text', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'quote_color',
			array(
				'label'     => esc_html__( 'Quote color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonial__quote' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'quote_typography',
				'label'    => esc_html__( 'Quote typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-testimonial__quote',
			)
		);

		$this->add_control(
			'author_color',
			array(
				'label'     => esc_html__( 'Author color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonial__author' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_typography',
				'label'    => esc_html__( 'Author typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-testimonial__author',
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => esc_html__( 'Role color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonial__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'label'    => esc_html__( 'Role typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-testimonial__role',
			)
		);

		$this->add_control(
			'star_color',
			array(
				'label'     => esc_html__( 'Star color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-rating__star--filled' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'star_empty_color',
			array(
				'label'     => esc_html__( 'Empty star color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-rating__star--empty' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'avatar_size',
			array(
				'label'     => esc_html__( 'Avatar size', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'separator' => 'before',
				'range'     => array(
					'px' => array(
						'min' => 24,
						'max' => 120,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonial__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Navigation.
	 *
	 * @return void
	 */
	private function register_navigation_style_controls() {
		$this->start_controls_section(
			'section_navigation_style',
			array(
				'label' => esc_html__( 'Navigation', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'arrow_color',
			array(
				'label'     => esc_html__( 'Arrow color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonials__arrow' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'arrow_background',
			array(
				'label'     => esc_html__( 'Arrow background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonials__arrow' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_responsive_control(
			'arrow_size',
			array(
				'label'     => esc_html__( 'Arrow size', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 24,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonials__arrow' => '--wdod-ew-arrow-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'dot_color',
			array(
				'label'     => esc_html__( 'Dot color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonials .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'dot_active_color',
			array(
				'label'     => esc_html__( 'Active dot color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-testimonials .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Reads a responsive control value with a fallback chain
	 * mobile -> tablet -> desktop.
	 *
	 * @param array  $settings Settings.
	 * @param string $key      Control name.
	 * @param string $device   '', 'tablet' or 'mobile'.
	 * @param mixed  $fallback Default value.
	 *
	 * @return mixed
	 */
	private function responsive_value( array $settings, $key, $device, $fallback ) {
		$chain = array( '' );

		if ( 'tablet' === $device ) {
			$chain = array( 'tablet', '' );
		} elseif ( 'mobile' === $device ) {
			$chain = array( 'mobile', 'tablet', '' );
		}

		foreach ( $chain as $suffix ) {
			$name = '' === $suffix ? $key : $key . '_' . $suffix;

			if ( isset( $settings[ $name ] ) && '' !== $settings[ $name ] && null !== $settings[ $name ] ) {
				return $settings[ $name ];
			}
		}

		return $fallback;
	}

	/**
	 * Front-end output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$items    = array();

		foreach ( (array) $settings['items'] as $item ) {
			$avatar_url = '';
			$avatar_alt = isset( $item['author'] ) ? $item['author'] : '';

			if ( ! empty( $item['avatar']['url'] ) ) {
				$avatar_url = $item['avatar']['url'];

				if ( ! empty( $item['avatar']['id'] ) ) {
					$thumb = wp_get_attachment_image_url( (int) $item['avatar']['id'], 'thumbnail' );

					if ( $thumb ) {
						$avatar_url = $thumb;
					}

					$alt = get_post_meta( (int) $item['avatar']['id'], '_wp_attachment_image_alt', true );

					if ( '' !== $alt ) {
						$avatar_alt = $alt;
					}
				}
			}

			$items[] = array(
				'quote'      => isset( $item['quote'] ) ? $item['quote'] : '',
				'author'     => isset( $item['author'] ) ? $item['author'] : '',
				'role'       => isset( $item['role'] ) ? $item['role'] : '',
				'avatar_url' => $avatar_url,
				'avatar_alt' => $avatar_alt,
				'rating'     => 'yes' === $settings['show_rating'] && isset( $item['rating'] ) ? (int) $item['rating'] : -1,
			);
		}

		if ( empty( $items ) ) {
			return;
		}

		$slider_settings = array(
			'autoplay'      => 'yes' === $settings['autoplay'],
			'autoplaySpeed' => max( 1000, (int) $settings['autoplay_speed'] ),
			'pauseOnHover'  => 'yes' === $settings['pause_on_hover'],
			'loop'          => 'yes' === $settings['loop'],
			'speed'         => max( 100, (int) $settings['speed'] ),
			'slidesPerView' => array(
				'desktop' => (int) $this->responsive_value( $settings, 'slides_per_view', '', 2 ),
				'tablet'  => (int) $this->responsive_value( $settings, 'slides_per_view', 'tablet', 2 ),
				'mobile'  => (int) $this->responsive_value( $settings, 'slides_per_view', 'mobile', 1 ),
			),
			'spaceBetween'  => array(
				'desktop' => (int) $this->responsive_value( $settings, 'space_between', '', 24 ),
				'tablet'  => (int) $this->responsive_value( $settings, 'space_between', 'tablet', 20 ),
				'mobile'  => (int) $this->responsive_value( $settings, 'space_between', 'mobile', 16 ),
			),
			'arrows'        => 'yes' === $settings['show_arrows'],
			'dots'          => 'yes' === $settings['show_dots'],
		);

		$this->render_template(
			'testimonial-slider',
			array(
				'id'          => 'wdod-ew-testimonials-' . $this->get_id(),
				'items'       => $items,
				'settings'    => $slider_settings,
				'show_arrows' => $slider_settings['arrows'],
				'show_dots'   => $slider_settings['dots'],
				'extra_class' => '',
			)
		);
	}
}
