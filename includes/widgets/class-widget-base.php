<?php
/**
 * Base class shared by all WDOD widgets.
 *
 * @package WDOD\ElementorWidgets
 */

namespace WDOD\ElementorWidgets\Widgets;

use Elementor\Icons_Manager;
use WDOD\ElementorWidgets\Support\Icons;
use WDOD\ElementorWidgets\Support\Template;

defined( 'ABSPATH' ) || exit;

/**
 * Class Widget_Base.
 *
 * Only loaded inside `elementor/widgets/register`, so `\Elementor\Widget_Base`
 * is guaranteed to exist.
 */
abstract class Widget_Base extends \Elementor\Widget_Base {

	/**
	 * Widget category.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'wdod' );
	}

	/**
	 * Stylesheets enqueued when the widget is rendered.
	 *
	 * @return string[]
	 */
	public function get_style_depends() {
		return array( 'wdod-ew-frontend' );
	}

	/**
	 * Keywords shared by every widget, merged with the widget's own.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'wdod' );
	}

	/**
	 * Help URL shown in the panel.
	 *
	 * @return string
	 */
	public function get_custom_help_url() {
		return 'https://github.com/datsenkoolena/wdod-elementor-widgets#readme';
	}

	/**
	 * Renders a plugin template.
	 *
	 * @param string $name Template name.
	 * @param array  $args Template arguments.
	 *
	 * @return void
	 */
	protected function render_template( $name, array $args ) {
		Template::output( $name, $args );
	}

	/**
	 * Renders an Elementor ICONS control value to sanitised markup.
	 *
	 * @param array|null $icon Icon control value (['value' => ..., 'library' => ...]).
	 *
	 * @return string Empty string when no icon is selected.
	 */
	protected function render_icon( $icon ) {
		if ( empty( $icon ) || ! is_array( $icon ) || empty( $icon['value'] ) ) {
			return '';
		}

		ob_start();
		Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );

		return Icons::kses( (string) ob_get_clean() );
	}

	/**
	 * Converts a URL control value into template arguments.
	 *
	 * @param array|null $link URL control value.
	 *
	 * @return array{url:string,target:bool,rel:string}
	 */
	protected function link_args( $link ) {
		$link = is_array( $link ) ? $link : array();

		$rel = array();

		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
		}

		return array(
			'url'    => isset( $link['url'] ) ? esc_url_raw( (string) $link['url'] ) : '',
			'target' => ! empty( $link['is_external'] ),
			'rel'    => trim( implode( ' ', array_filter( $rel ) ) ),
		);
	}

	/**
	 * Whether the widget is rendered inside the editor.
	 *
	 * @return bool
	 */
	protected function is_editor() {
		return \Elementor\Plugin::$instance->editor->is_edit_mode();
	}
}
