<?php
/**
 * Pricing Table widget.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined( 'ABSPATH' ) || exit;

/**
 * Class Pricing_Table.
 */
class Pricing_Table extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wdod-pricing-table';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Pricing Table', 'wdod-elementor-widgets' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'wdod', 'pricing', 'price', 'table', 'plan', 'subscription' );
	}

	/**
	 * Registers controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_header_controls();
		$this->register_price_controls();
		$this->register_features_controls();
		$this->register_button_controls();
		$this->register_box_style_controls();
		$this->register_header_style_controls();
		$this->register_features_style_controls();
		$this->register_button_style_controls();
	}

	/**
	 * Content > Header.
	 *
	 * @return void
	 */
	private function register_header_controls() {
		$this->start_controls_section(
			'section_header',
			array(
				'label' => esc_html__( 'Header', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'title',
			array(
				'label'       => esc_html__( 'Title', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Professional', 'wdod-elementor-widgets' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'featured',
			array(
				'label'        => esc_html__( 'Featured plan', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'wdod-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'wdod-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'badge_text',
			array(
				'label'       => esc_html__( 'Badge text', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Most popular', 'wdod-elementor-widgets' ),
				'placeholder' => esc_html__( 'Leave empty to hide', 'wdod-elementor-widgets' ),
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'featured' => 'yes' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Price.
	 *
	 * @return void
	 */
	private function register_price_controls() {
		$this->start_controls_section(
			'section_price',
			array(
				'label' => esc_html__( 'Price', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'currency',
			array(
				'label'   => esc_html__( 'Currency symbol', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '$',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'price',
			array(
				'label'   => esc_html__( 'Price', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '29',
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'period',
			array(
				'label'   => esc_html__( 'Period', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '/ month', 'wdod-elementor-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Features.
	 *
	 * @return void
	 */
	private function register_features_controls() {
		$this->start_controls_section(
			'section_features',
			array(
				'label' => esc_html__( 'Features', 'wdod-elementor-widgets' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => esc_html__( 'Feature', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Feature description', 'wdod-elementor-widgets' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
			)
		);

		$repeater->add_control(
			'included',
			array(
				'label'        => esc_html__( 'Included', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'       => esc_html__( 'Custom icon', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::ICONS,
				'description' => esc_html__( 'Optional. Defaults to a check or cross mark.', 'wdod-elementor-widgets' ),
				'skin'        => 'inline',
				'label_block' => false,
			)
		);

		$this->add_control(
			'features',
			array(
				'label'       => esc_html__( 'Features', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'text'     => esc_html__( '10 projects', 'wdod-elementor-widgets' ),
						'included' => 'yes',
					),
					array(
						'text'     => esc_html__( 'Unlimited storage', 'wdod-elementor-widgets' ),
						'included' => 'yes',
					),
					array(
						'text'     => esc_html__( 'Priority support', 'wdod-elementor-widgets' ),
						'included' => 'yes',
					),
					array(
						'text'     => esc_html__( 'White label', 'wdod-elementor-widgets' ),
						'included' => '',
					),
				),
				'title_field' => '{{{ text }}}',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Button.
	 *
	 * @return void
	 */
	private function register_button_controls() {
		$this->start_controls_section(
			'section_button',
			array(
				'label' => esc_html__( 'Button', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'button_text',
			array(
				'label'   => esc_html__( 'Text', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Get started', 'wdod-elementor-widgets' ),
				'dynamic' => array( 'active' => true ),
			)
		);

		$this->add_control(
			'button_link',
			array(
				'label'       => esc_html__( 'Link', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://example.com/checkout',
				'default'     => array(
					'url'         => '#',
					'is_external' => false,
					'nofollow'    => false,
				),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Box.
	 *
	 * @return void
	 */
	private function register_box_style_controls() {
		$this->start_controls_section(
			'section_box_style',
			array(
				'label' => esc_html__( 'Box', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'box_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'featured_color',
			array(
				'label'     => esc_html__( 'Featured accent', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing' => '--wdod-ew-accent: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .wdod-ew-pricing',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-pricing' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .wdod-ew-pricing',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-pricing' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'box_align',
			array(
				'label'     => esc_html__( 'Alignment', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'wdod-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'wdod-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'wdod-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Header (title, price).
	 *
	 * @return void
	 */
	private function register_header_style_controls() {
		$this->start_controls_section(
			'section_header_style',
			array(
				'label' => esc_html__( 'Title & Price', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Title color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__title' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => esc_html__( 'Title typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-pricing__title',
			)
		);

		$this->add_control(
			'price_color',
			array(
				'label'     => esc_html__( 'Price color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__amount, {{WRAPPER}} .wdod-ew-pricing__currency' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'price_typography',
				'label'    => esc_html__( 'Price typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-pricing__amount',
			)
		);

		$this->add_control(
			'period_color',
			array(
				'label'     => esc_html__( 'Period color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__period' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'period_typography',
				'label'    => esc_html__( 'Period typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-pricing__period',
			)
		);

		$this->add_control(
			'badge_color',
			array(
				'label'     => esc_html__( 'Badge text color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__badge' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'badge_background',
			array(
				'label'     => esc_html__( 'Badge background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__badge' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Features.
	 *
	 * @return void
	 */
	private function register_features_style_controls() {
		$this->start_controls_section(
			'section_features_style',
			array(
				'label' => esc_html__( 'Features', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'features_color',
			array(
				'label'     => esc_html__( 'Text color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__feature' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'features_typography',
				'selector' => '{{WRAPPER}} .wdod-ew-pricing__feature',
			)
		);

		$this->add_control(
			'included_icon_color',
			array(
				'label'     => esc_html__( 'Included icon color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__feature--included .wdod-ew-pricing__feature-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'excluded_icon_color',
			array(
				'label'     => esc_html__( 'Excluded icon color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__feature--excluded .wdod-ew-pricing__feature-icon' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'excluded_opacity',
			array(
				'label'     => esc_html__( 'Excluded opacity', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min'  => 0.1,
						'max'  => 1,
						'step' => 0.05,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__feature--excluded' => 'opacity: {{SIZE}};',
				),
			)
		);

		$this->add_responsive_control(
			'features_gap',
			array(
				'label'     => esc_html__( 'Row gap', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__features' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Button.
	 *
	 * @return void
	 */
	private function register_button_style_controls() {
		$this->start_controls_section(
			'section_button_style',
			array(
				'label' => esc_html__( 'Button', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'button_typography',
				'selector' => '{{WRAPPER}} .wdod-ew-pricing__button',
			)
		);

		$this->start_controls_tabs( 'button_tabs' );

		$this->start_controls_tab(
			'button_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'button_color',
			array(
				'label'     => esc_html__( 'Text color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__button' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__button' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_tab_hover',
			array(
				'label' => esc_html__( 'Hover', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'button_hover_color',
			array(
				'label'     => esc_html__( 'Text color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__button:hover, {{WRAPPER}} .wdod-ew-pricing__button:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'button_hover_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-pricing__button:hover, {{WRAPPER}} .wdod-ew-pricing__button:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'      => 'button_border',
				'selector'  => '{{WRAPPER}} .wdod-ew-pricing__button',
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'button_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-pricing__button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-pricing__button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'button_full_width',
			array(
				'label'        => esc_html__( 'Full width', 'wdod-elementor-widgets' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'selectors'    => array(
					'{{WRAPPER}} .wdod-ew-pricing__button' => 'display: block; width: 100%;',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Front-end output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$features = array();

		foreach ( (array) $settings['features'] as $item ) {
			$features[] = array(
				'text'      => isset( $item['text'] ) ? $item['text'] : '',
				'included'  => ! empty( $item['included'] ) && 'yes' === $item['included'],
				'icon_html' => $this->render_icon( isset( $item['icon'] ) ? $item['icon'] : null ),
			);
		}

		$link = $this->link_args( $settings['button_link'] );

		$this->render_template(
			'pricing-table',
			array(
				'title'         => $settings['title'],
				'badge'         => 'yes' === $settings['featured'] ? $settings['badge_text'] : '',
				'price'         => $settings['price'],
				'currency'      => $settings['currency'],
				'period'        => $settings['period'],
				'features'      => $features,
				'button_text'   => $settings['button_text'],
				'button_url'    => $link['url'],
				'button_target' => $link['target'],
				'button_rel'    => $link['rel'],
				'featured'      => 'yes' === $settings['featured'],
				'extra_class'   => '',
			)
		);
	}
}
