<?php
/**
 * Spacing Item Element for Bricks Builder.
 *
 * Individual spacing sample row for use within Spacing (Nestable) element.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide\Elements;

defined( 'ABSPATH' ) || exit;

/**
 * Spacing Item Element.
 */
class SpacingItem extends \Bricks\Element {

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
	public $name = 'at-spacing-item';

	/**
	 * Element icon.
	 *
	 * @var string
	 */
	public $icon = 'ti-layout-width-default';

	/**
	 * Element scripts.
	 *
	 * @var array
	 */
	public $scripts = [ 'atSpacingItemInit' ];

	/**
	 * Get element label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Spacing Item', 'advanced-themer-style-guide' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'spacing', 'gap', 'margin', 'padding', 'item' ];
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

		$this->control_groups['barStyle'] = [
			'title' => esc_html__( 'Bar Style', 'advanced-themer-style-guide' ),
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
			'default'     => '--at-space--m',
			'placeholder' => esc_html__( 'e.g. --at-space--m', 'advanced-themer-style-guide' ),
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
			'label'    => esc_html__( 'Hide Computed Value', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		$this->controls['hideValueLabel'] = [
			'group'    => 'display',
			'label'    => esc_html__( 'Hide Value Label', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		// Bar style controls.
		$this->controls['barColor'] = [
			'group' => 'barStyle',
			'label' => esc_html__( 'Bar Color', 'advanced-themer-style-guide' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => '.atsg-spacing-item__bar',
				],
			],
		];

		$this->controls['barHeight'] = [
			'group'   => 'barStyle',
			'label'   => esc_html__( 'Bar Height', 'advanced-themer-style-guide' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '24px',
			'css'     => [
				[
					'property' => 'height',
					'selector' => '.atsg-spacing-item__bar',
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
		$variable = $settings['variable'] ?? '--at-space--m';

		// Bricks checkbox: key exists = checked (true), key missing = unchecked (false).
		// Using "Hide" checkboxes: isset = hide, !isset = show (default).
		$show_label       = ! isset( $settings['hideLabel'] );
		$show_variable    = ! isset( $settings['hideVariable'] );
		$show_value       = ! isset( $settings['hideValue'] );
		$show_value_label = ! isset( $settings['hideValueLabel'] );

		$this->set_attribute( '_root', 'class', [ 'atsg-spacing-item' ] );
		$this->set_attribute( '_root', 'data-var', esc_attr( $variable ) );

		$output = "<div {$this->render_attributes( '_root' )}>";

		// Info section (label + variable).
		$output .= '<div class="atsg-spacing-item__info">';
		if ( $show_label ) {
			$output .= '<span class="atsg-spacing-item__label">' . esc_html( $label ) . '</span>';
		}
		if ( $show_variable ) {
			$output .= '<code class="atsg-spacing-item__variable">' . esc_html( $variable ) . '</code>';
		}
		$output .= '</div>';

		// Bar container - set both width and height to the spacing value, CSS controls which one applies.
		$output .= '<div class="atsg-spacing-item__bar-container">';
		$output .= '<div class="atsg-spacing-item__bar" style="width: var(' . esc_attr( $variable ) . '); height: var(' . esc_attr( $variable ) . ');"></div>';
		if ( $show_value ) {
			$output .= '<span class="atsg-spacing-item__value">';
			if ( $show_value_label ) {
				$output .= '<span class="atsg-spacing-item__value-label">' . esc_html__( 'Value:', 'advanced-themer-style-guide' ) . '</span> ';
			}
			$output .= '<span class="atsg-spacing-item__computed"></span>';
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
			'at-spacing-item',
			false,
			[],
			AT_STYLE_GUIDE_VERSION
		);
		wp_add_inline_style( 'at-spacing-item', $this->get_element_css() );
		wp_enqueue_style( 'at-spacing-item' );
	}

	/**
	 * Get inline CSS for the element.
	 *
	 * @return string
	 */
	private function get_element_css(): string {
		return '
			.atsg-spacing-item {
				display: flex;
				align-items: center;
				gap: var(--at-space--s, 1rem);
			}

			.atsg-spacing-item__info {
				display: flex;
				flex-direction: column;
				gap: var(--at-space--3xs, 0.25rem);
				min-width: 120px;
			}

			.atsg-spacing-item__label {
				font-weight: 600;
				font-size: var(--at-text--xs, 0.875rem);
			}

			.atsg-spacing-item__variable {
				font-size: var(--at-text--2xs, 0.75rem);
				color: var(--at-neutral-d-2, #6b7280);
				background: var(--at-neutral-t-6, #f3f4f6);
				padding: var(--at-space--3xs, 0.125rem) var(--at-space--2xs, 0.375rem);
				border-radius: var(--at-radius--xs, 4px);
				width: fit-content;
			}

			.atsg-spacing-item__bar-container {
				display: flex;
				align-items: center;
				gap: var(--at-space--xs, 0.75rem);
				flex: 1;
			}

			.atsg-spacing-item__bar {
				background-color: var(--at-primary, #3b82f6);
				border-radius: var(--at-radius--xs, 4px);
				min-width: 4px;
				min-height: 4px;
				transition: width 0.2s ease, height 0.2s ease;
			}

			/* Horizontal mode: fixed height, variable width */
			.atsg-spacing:not(.atsg-spacing--vertical) .atsg-spacing-item__bar {
				height: 24px !important;
			}

			.atsg-spacing-item__value {
				font-size: var(--at-text--2xs, 0.75rem);
				font-weight: 500;
				color: var(--at-neutral-d-3, #374151);
				min-width: 50px;
			}

			.atsg-spacing-item__value-label {
				font-weight: 600;
				color: var(--at-neutral-d-2, #6b7280);
			}
		';
	}
}
