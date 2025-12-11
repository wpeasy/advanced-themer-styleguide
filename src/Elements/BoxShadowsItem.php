<?php
/**
 * Box Shadows Item Element for Bricks Builder.
 *
 * Individual box shadow sample for use within Box Shadows (Nestable) element.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide\Elements;

defined( 'ABSPATH' ) || exit;

/**
 * Box Shadows Item Element.
 */
class BoxShadowsItem extends \Bricks\Element {

	/**
	 * Element category.
	 *
	 * @var string
	 */
	public $category = 'at style guide';

	/**
	 * Element name.
	 *
	 * @var string
	 */
	public $name = 'at-box-shadows-item';

	/**
	 * Element icon.
	 *
	 * @var string
	 */
	public $icon = 'ti-layers';

	/**
	 * Element scripts.
	 *
	 * @var array
	 */
	public $scripts = [ 'atBoxShadowsItemInit' ];

	/**
	 * Get element label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Box Shadow Item', 'advanced-themer-style-guide' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'shadow', 'elevation', 'depth', 'box-shadow', 'item' ];
	}

	/**
	 * Set control groups.
	 *
	 * @return void
	 */
	public function set_control_groups(): void {
		$this->control_groups['content'] = [
			'title' => esc_html__( 'Content', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['display'] = [
			'title' => esc_html__( 'Display', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['boxStyle'] = [
			'title' => esc_html__( 'Box Style', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];
	}

	/**
	 * Set controls.
	 *
	 * @return void
	 */
	public function set_controls(): void {
		// Content controls.
		$this->controls['label'] = [
			'group'       => 'content',
			'label'       => esc_html__( 'Label', 'advanced-themer-style-guide' ),
			'type'        => 'text',
			'default'     => 'Medium',
			'placeholder' => esc_html__( 'e.g. Small, Medium, Large...', 'advanced-themer-style-guide' ),
		];

		$this->controls['variable'] = [
			'group'       => 'content',
			'label'       => esc_html__( 'CSS Variable', 'advanced-themer-style-guide' ),
			'type'        => 'text',
			'default'     => '--at-shadow--m',
			'placeholder' => esc_html__( 'e.g. --at-shadow--m', 'advanced-themer-style-guide' ),
		];

		// Display controls - all "Hide X" for consistency.
		$this->controls['hideLabel'] = [
			'group'    => 'display',
			'label'    => esc_html__( 'Hide Label', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		$this->controls['hideVariable'] = [
			'group'    => 'display',
			'label'    => esc_html__( 'Hide Variable Name', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		$this->controls['hideValue'] = [
			'group'    => 'display',
			'label'    => esc_html__( 'Hide Shadow Value', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		$this->controls['hideValueLabel'] = [
			'group'    => 'display',
			'label'    => esc_html__( 'Hide Value Label', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		// Box style controls.
		$this->controls['boxSize'] = [
			'group'       => 'boxStyle',
			'label'       => esc_html__( 'Box Size', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => '120px',
			'css'         => [
				[
					'property' => 'width',
					'selector' => '.atsg-shadows-item__box',
				],
				[
					'property' => 'height',
					'selector' => '.atsg-shadows-item__box',
				],
			],
		];

		$this->controls['boxBackground'] = [
			'group' => 'boxStyle',
			'label' => esc_html__( 'Box Background', 'advanced-themer-style-guide' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.atsg-shadows-item__box',
				],
			],
		];

		$this->controls['boxRadius'] = [
			'group'       => 'boxStyle',
			'label'       => esc_html__( 'Box Border Radius', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => '8px',
			'css'         => [
				[
					'property' => 'border-radius',
					'selector' => '.atsg-shadows-item__box',
				],
			],
		];
	}

	/**
	 * Render element.
	 *
	 * @return void
	 */
	public function render(): void {
		$settings = $this->settings;

		$label    = $settings['label'] ?? 'Medium';
		$variable = $settings['variable'] ?? '--at-shadow--m';

		// Bricks checkbox: key exists = checked (true), key missing = unchecked (false).
		// Using "Hide" checkboxes: isset = hide, !isset = show (default).
		$show_label       = ! isset( $settings['hideLabel'] );
		$show_variable    = ! isset( $settings['hideVariable'] );
		$show_value       = ! isset( $settings['hideValue'] );
		$show_value_label = ! isset( $settings['hideValueLabel'] );

		$this->set_attribute( '_root', 'class', [ 'atsg-shadows-item' ] );
		$this->set_attribute( '_root', 'data-var', esc_attr( $variable ) );

		$output = "<div {$this->render_attributes( '_root' )}>";

		// Box with box-shadow applied.
		$output .= '<div class="atsg-shadows-item__box" style="box-shadow: var(' . esc_attr( $variable ) . ');"></div>';

		// Info section.
		$output .= '<div class="atsg-shadows-item__info">';
		if ( $show_label ) {
			$output .= '<span class="atsg-shadows-item__label">' . esc_html( $label ) . '</span>';
		}
		if ( $show_variable ) {
			$output .= '<code class="atsg-shadows-item__variable">' . esc_html( $variable ) . '</code>';
		}
		if ( $show_value ) {
			$output .= '<span class="atsg-shadows-item__value">';
			if ( $show_value_label ) {
				$output .= '<span class="atsg-shadows-item__value-label">' . esc_html__( 'Value:', 'advanced-themer-style-guide' ) . '</span> ';
			}
			$output .= '<span class="atsg-shadows-item__computed"></span>';
			$output .= '</span>';
		}
		$output .= '</div>';

		$output .= '</div>';

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Enqueue element styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		wp_register_style(
			'at-box-shadows-item',
			false,
			[],
			AT_STYLE_GUIDE_VERSION
		);
		wp_add_inline_style( 'at-box-shadows-item', $this->get_element_css() );
		wp_enqueue_style( 'at-box-shadows-item' );
	}

	/**
	 * Get inline CSS for the element.
	 *
	 * @return string
	 */
	private function get_element_css(): string {
		return '
			.atsg-shadows-item {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: var(--at-space--s, 1rem);
			}

			.atsg-shadows-item__box {
				width: 120px;
				height: 120px;
				background-color: var(--at-white, #ffffff);
				border-radius: var(--at-radius--s, 8px);
			}

			.atsg-shadows-item__info {
				display: flex;
				flex-direction: column;
				align-items: center;
				gap: var(--at-space--3xs, 0.25rem);
				text-align: center;
			}

			.atsg-shadows-item__label {
				font-weight: 600;
				font-size: var(--at-text--s, 0.875rem);
				color: var(--at-neutral-d-3, #374151);
			}

			.atsg-shadows-item__variable {
				font-size: var(--at-text--xs, 0.75rem);
				color: var(--at-neutral-d-2, #6b7280);
				background: var(--at-neutral-t-6, #f3f4f6);
				padding: var(--at-space--3xs, 0.125rem) var(--at-space--2xs, 0.375rem);
				border-radius: var(--at-radius--xs, 4px);
			}

			.atsg-shadows-item__value {
				font-size: var(--at-text--xs, 0.75rem);
				color: var(--at-neutral-d-2, #6b7280);
				max-width: 200px;
				word-break: break-all;
				text-align: center;
			}

			.atsg-shadows-item__value-label {
				font-weight: 600;
				color: var(--at-neutral-d-3, #374151);
			}
		';
	}
}
