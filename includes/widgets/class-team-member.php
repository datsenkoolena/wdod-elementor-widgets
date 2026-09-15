<?php
/**
 * Team Member widget.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Utils;
use WDOD\ElementorWidgets\Shortcodes;

defined( 'ABSPATH' ) || exit;

/**
 * Class Team_Member.
 *
 * Content can be entered manually or pulled from a post (team CPT, post,
 * or any type allowed through the `wdod_ew_team_post_types` filter). In post
 * mode the role, bio, photo and social links are read through Meta, so ACF
 * fields named `role`, `bio`, `photo`, `socials` work out of the box.
 */
class Team_Member extends Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'wdod-team-member';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return esc_html__( 'Team Member', 'wdod-elementor-widgets' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-person';
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'wdod', 'team', 'member', 'person', 'staff', 'profile' );
	}

	/**
	 * Post types selectable in "post" mode.
	 *
	 * @return string[]
	 */
	public static function get_post_types() {
		/**
		 * Filters the post types offered by the Team Member widget.
		 *
		 * @since 1.0.0
		 *
		 * @param string[] $post_types Post type slugs.
		 */
		$post_types = (array) apply_filters( 'wdod_ew_team_post_types', array( 'team', 'post' ) );

		return array_values( array_filter( $post_types, 'post_type_exists' ) );
	}

	/**
	 * Options for the post select control (max. 50 most recent posts).
	 *
	 * @return array<int,string>
	 */
	private function get_post_options() {
		$post_types = self::get_post_types();
		$options    = array( 0 => esc_html__( '— Select —', 'wdod-elementor-widgets' ) );

		if ( empty( $post_types ) ) {
			return $options;
		}

		$posts = get_posts(
			array(
				'post_type'        => $post_types,
				'post_status'      => 'publish',
				'posts_per_page'   => 50,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);

		foreach ( $posts as $post ) {
			$label = get_the_title( $post );

			if ( count( $post_types ) > 1 ) {
				$label .= ' (' . $post->post_type . ')';
			}

			$options[ $post->ID ] = $label;
		}

		return $options;
	}

	/**
	 * Registers controls.
	 *
	 * @return void
	 */
	protected function register_controls() {
		$this->register_content_controls();
		$this->register_socials_controls();
		$this->register_box_style_controls();
		$this->register_photo_style_controls();
		$this->register_text_style_controls();
		$this->register_socials_style_controls();
	}

	/**
	 * Content > Member.
	 *
	 * @return void
	 */
	private function register_content_controls() {
		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Member', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'label'   => esc_html__( 'Source', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => array(
					'manual' => esc_html__( 'Manual', 'wdod-elementor-widgets' ),
					'post'   => esc_html__( 'Post', 'wdod-elementor-widgets' ),
				),
			)
		);

		$this->add_control(
			'post_id',
			array(
				'label'       => esc_html__( 'Post', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => $this->get_post_options(),
				'default'     => 0,
				'label_block' => true,
				'description' => esc_html__( 'Reads the title, featured image and the "role", "bio", "photo" and "socials" fields.', 'wdod-elementor-widgets' ),
				'condition'   => array( 'source' => 'post' ),
			)
		);

		$this->add_control(
			'name',
			array(
				'label'       => esc_html__( 'Name', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Jane Doe', 'wdod-elementor-widgets' ),
				'label_block' => true,
				'dynamic'     => array( 'active' => true ),
				'condition'   => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'role',
			array(
				'label'     => esc_html__( 'Role', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Lead Developer', 'wdod-elementor-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'source' => 'manual' ),
			)
		);

		$this->add_control(
			'photo',
			array(
				'label'     => esc_html__( 'Photo', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::MEDIA,
				'default'   => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'source' => 'manual' ),
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			array(
				'name'    => 'photo',
				'default' => 'medium',
			)
		);

		$this->add_control(
			'bio',
			array(
				'label'     => esc_html__( 'Bio', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::TEXTAREA,
				'rows'      => 5,
				'default'   => esc_html__( 'Short biography that introduces the team member and their responsibilities.', 'wdod-elementor-widgets' ),
				'dynamic'   => array( 'active' => true ),
				'condition' => array( 'source' => 'manual' ),
			)
		);

		$this->add_responsive_control(
			'align',
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
					'{{WRAPPER}} .wdod-ew-team' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Content > Social links.
	 *
	 * @return void
	 */
	private function register_socials_controls() {
		$this->start_controls_section(
			'section_socials',
			array(
				'label'     => esc_html__( 'Social links', 'wdod-elementor-widgets' ),
				'condition' => array( 'source' => 'manual' ),
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'icon',
			array(
				'label'   => esc_html__( 'Icon', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::ICONS,
				'default' => array(
					'value'   => 'fab fa-linkedin-in',
					'library' => 'fa-brands',
				),
			)
		);

		$repeater->add_control(
			'label',
			array(
				'label'   => esc_html__( 'Label', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'LinkedIn',
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => esc_html__( 'Link', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://',
				'default'     => array(
					'url'         => '#',
					'is_external' => true,
					'nofollow'    => true,
				),
				'dynamic'     => array( 'active' => true ),
			)
		);

		$this->add_control(
			'socials',
			array(
				'label'       => esc_html__( 'Links', 'wdod-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => array(
					array(
						'label' => 'LinkedIn',
						'icon'  => array(
							'value'   => 'fab fa-linkedin-in',
							'library' => 'fa-brands',
						),
					),
					array(
						'label' => 'X',
						'icon'  => array(
							'value'   => 'fab fa-x-twitter',
							'library' => 'fa-brands',
						),
					),
				),
				'title_field' => '{{{ label }}}',
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
					'{{WRAPPER}} .wdod-ew-team' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'box_border',
				'selector' => '{{WRAPPER}} .wdod-ew-team',
			)
		);

		$this->add_responsive_control(
			'box_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-team' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .wdod-ew-team',
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-team' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Photo.
	 *
	 * @return void
	 */
	private function register_photo_style_controls() {
		$this->start_controls_section(
			'section_photo_style',
			array(
				'label' => esc_html__( 'Photo', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'photo_width',
			array(
				'label'      => esc_html__( 'Width', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%' ),
				'range'      => array(
					'px' => array(
						'min' => 40,
						'max' => 400,
					),
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => 'px',
					'size' => 160,
				),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-team__photo' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'photo_shape',
			array(
				'label'   => esc_html__( 'Shape', 'wdod-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => array(
					'circle'  => esc_html__( 'Circle', 'wdod-elementor-widgets' ),
					'rounded' => esc_html__( 'Rounded', 'wdod-elementor-widgets' ),
					'square'  => esc_html__( 'Square', 'wdod-elementor-widgets' ),
				),
			)
		);

		$this->add_responsive_control(
			'photo_radius',
			array(
				'label'      => esc_html__( 'Border radius', 'wdod-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'condition'  => array( 'photo_shape' => 'rounded' ),
				'selectors'  => array(
					'{{WRAPPER}} .wdod-ew-team__photo img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'photo_border',
				'selector' => '{{WRAPPER}} .wdod-ew-team__photo img',
			)
		);

		$this->add_responsive_control(
			'photo_spacing',
			array(
				'label'     => esc_html__( 'Spacing below', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__photo' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
			'name_color',
			array(
				'label'     => esc_html__( 'Name color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__name' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'name_typography',
				'label'    => esc_html__( 'Name typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-team__name',
			)
		);

		$this->add_control(
			'role_color',
			array(
				'label'     => esc_html__( 'Role color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__role' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'role_typography',
				'label'    => esc_html__( 'Role typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-team__role',
			)
		);

		$this->add_control(
			'bio_color',
			array(
				'label'     => esc_html__( 'Bio color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__bio' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'bio_typography',
				'label'    => esc_html__( 'Bio typography', 'wdod-elementor-widgets' ),
				'selector' => '{{WRAPPER}} .wdod-ew-team__bio',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Style > Social icons.
	 *
	 * @return void
	 */
	private function register_socials_style_controls() {
		$this->start_controls_section(
			'section_socials_style',
			array(
				'label' => esc_html__( 'Social icons', 'wdod-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'social_size',
			array(
				'label'     => esc_html__( 'Icon size', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 10,
						'max' => 48,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__social' => '--wdod-ew-social-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'social_gap',
			array(
				'label'     => esc_html__( 'Gap', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__socials' => 'gap: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'social_tabs' );

		$this->start_controls_tab(
			'social_tab_normal',
			array(
				'label' => esc_html__( 'Normal', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'social_color',
			array(
				'label'     => esc_html__( 'Icon color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__social' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__social' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'social_tab_hover',
			array(
				'label' => esc_html__( 'Hover', 'wdod-elementor-widgets' ),
			)
		);

		$this->add_control(
			'social_hover_color',
			array(
				'label'     => esc_html__( 'Icon color', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__social:hover, {{WRAPPER}} .wdod-ew-team__social:focus' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'social_hover_background',
			array(
				'label'     => esc_html__( 'Background', 'wdod-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .wdod-ew-team__social:hover, {{WRAPPER}} .wdod-ew-team__social:focus' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Front-end output.
	 *
	 * @return void
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$size     = ! empty( $settings['photo_size'] ) ? $settings['photo_size'] : 'medium';

		if ( 'post' === $settings['source'] ) {
			$post_id = absint( $settings['post_id'] );

			if ( $post_id <= 0 ) {
				if ( $this->is_editor() ) {
					echo '<p class="wdod-ew-placeholder">' . esc_html__( 'Select a post to display the team member.', 'wdod-elementor-widgets' ) . '</p>';
				}

				return;
			}

			$args = Shortcodes::team_member_args_from_post( $post_id, $size );

			if ( 'custom' === $size && ! empty( $settings['photo_custom_dimension'] ) && '' !== $args['photo_url'] ) {
				// Custom dimensions require the attachment ID; fall back to the URL when unknown.
				$attachment_id = attachment_url_to_postid( $args['photo_url'] );

				if ( $attachment_id ) {
					$args['photo_html'] = Group_Control_Image_Size::get_attachment_image_html(
						array_merge( $settings, array( 'photo' => array( 'id' => $attachment_id ) ) ),
						'photo',
						'photo'
					);
				}
			}
		} else {
			$socials = array();

			foreach ( (array) $settings['socials'] as $item ) {
				$link = $this->link_args( isset( $item['link'] ) ? $item['link'] : null );

				if ( '' === $link['url'] ) {
					continue;
				}

				$socials[] = array(
					'network'   => Shortcodes::guess_network( $link['url'] ),
					'label'     => isset( $item['label'] ) ? $item['label'] : '',
					'url'       => $link['url'],
					'icon_html' => $this->render_icon( isset( $item['icon'] ) ? $item['icon'] : null ),
					'target'    => $link['target'],
					'rel'       => $link['rel'],
				);
			}

			$photo_html = '';
			$photo_url  = '';

			if ( ! empty( $settings['photo']['url'] ) ) {
				$photo_html = Group_Control_Image_Size::get_attachment_image_html( $settings, 'photo', 'photo' );
				$photo_url  = $settings['photo']['url'];
			}

			$args = array(
				'name'       => $settings['name'],
				'role'       => $settings['role'],
				'bio'        => $settings['bio'],
				'photo_html' => $photo_html,
				'photo_url'  => $photo_url,
				'photo_alt'  => $settings['name'],
				'socials'    => $socials,
			);
		}

		$args['align']       = '';
		$args['shape']       = $settings['photo_shape'];
		$args['extra_class'] = '';

		$this->render_template( 'team-member', $args );
	}
}
