<?php
/**
 * Color Swatch Element for Bricks Builder.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Color Swatch Element.
 */
class Element_Color_Swatch extends \Bricks\Element {

	/**
	 * Element category.
	 *
	 * @var string
	 */
	public $category = 'advanced-themer';

	/**
	 * Element name.
	 *
	 * @var string
	 */
	public $name = 'at-color-swatch';

	/**
	 * Element icon.
	 *
	 * @var string
	 */
	public $icon = 'ti-palette';

	/**
	 * Element scripts.
	 *
	 * @var array
	 */
	public $scripts = [];

	/**
	 * Get element label.
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'AT Color Swatch', 'advanced-themer-style-guide' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords() {
		return [ 'color', 'swatch', 'palette', 'style guide', 'advanced themer' ];
	}

	/**
	 * Set control groups.
	 *
	 * @return void
	 */
	public function set_control_groups() {
		$this->control_groups['color'] = [
			'title' => esc_html__( 'Color', 'advanced-themer-style-guide' ),
		];

		$this->control_groups['display'] = [
			'title' => esc_html__( 'Display', 'advanced-themer-style-guide' ),
		];
	}

	/**
	 * Set controls.
	 *
	 * @return void
	 */
	public function set_controls() {
		// Color group controls.
		$this->controls['colorValue'] = [
			'group'       => 'color',
			'label'       => esc_html__( 'Color', 'advanced-themer-style-guide' ),
			'type'        => 'color',
			'default'     => [
				'hex' => '#3b82f6',
			],
			'css'         => [
				[
					'property' => 'background-color',
					'selector' => '.at-color-swatch__preview',
				],
			],
		];

		$this->controls['colorName'] = [
			'group'       => 'color',
			'label'       => esc_html__( 'Color Name', 'advanced-themer-style-guide' ),
			'type'        => 'text',
			'default'     => 'Primary Blue',
			'placeholder' => esc_html__( 'Enter color name', 'advanced-themer-style-guide' ),
		];

		$this->controls['colorVariable'] = [
			'group'       => 'color',
			'label'       => esc_html__( 'CSS Variable', 'advanced-themer-style-guide' ),
			'type'        => 'text',
			'default'     => '--color-primary',
			'placeholder' => '--color-name',
		];

		// Display group controls.
		$this->controls['showName'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Show Name', 'advanced-themer-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showValue'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Show Hex Value', 'advanced-themer-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showVariable'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Show CSS Variable', 'advanced-themer-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['swatchHeight'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Swatch Height', 'advanced-themer-style-guide' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '100px',
			'css'     => [
				[
					'property' => 'height',
					'selector' => '.at-color-swatch__preview',
				],
			],
		];

		$this->controls['swatchBorderRadius'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Border Radius', 'advanced-themer-style-guide' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '8px',
			'css'     => [
				[
					'property' => 'border-radius',
					'selector' => '.at-color-swatch__preview',
				],
			],
		];
	}

	/**
	 * Render element.
	 *
	 * @return void
	 */
	public function render() {
		$settings = $this->settings;

		$color_value    = $settings['colorValue']['hex'] ?? '#3b82f6';
		$color_name     = $settings['colorName'] ?? '';
		$color_variable = $settings['colorVariable'] ?? '';
		$show_name      = $settings['showName'] ?? true;
		$show_value     = $settings['showValue'] ?? true;
		$show_variable  = $settings['showVariable'] ?? true;

		$root_classes = [ 'at-color-swatch' ];

		$this->set_attribute( '_root', 'class', $root_classes );

		$output = "<div {$this->render_attributes( '_root' )}>";

		// Color preview.
		$output .= '<div class="at-color-swatch__preview"></div>';

		// Color info.
		$output .= '<div class="at-color-swatch__info">';

		if ( $show_name && $color_name ) {
			$output .= '<div class="at-color-swatch__name">' . esc_html( $color_name ) . '</div>';
		}

		if ( $show_value ) {
			$output .= '<div class="at-color-swatch__value">' . esc_html( $color_value ) . '</div>';
		}

		if ( $show_variable && $color_variable ) {
			$output .= '<div class="at-color-swatch__variable"><code>' . esc_html( $color_variable ) . '</code></div>';
		}

		$output .= '</div>'; // .at-color-swatch__info
		$output .= '</div>'; // .at-color-swatch

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get inline CSS for the element.
	 *
	 * @return string
	 */
	public static function get_element_css() {
		return '
			.at-color-swatch {
				display: flex;
				flex-direction: column;
				gap: 0.5rem;
			}
			.at-color-swatch__preview {
				width: 100%;
				height: 100px;
				border-radius: 8px;
				box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
			}
			.at-color-swatch__info {
				display: flex;
				flex-direction: column;
				gap: 0.25rem;
			}
			.at-color-swatch__name {
				font-weight: 600;
			}
			.at-color-swatch__value {
				font-family: monospace;
				font-size: 0.875rem;
				color: #666;
			}
			.at-color-swatch__variable code {
				font-size: 0.75rem;
				background: #f3f4f6;
				padding: 0.125rem 0.375rem;
				border-radius: 4px;
			}
		';
	}
}
