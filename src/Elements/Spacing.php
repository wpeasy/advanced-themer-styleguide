<?php
/**
 * Spacing Element (Nestable) for Bricks Builder.
 *
 * Container element for spacing samples with computed pixel values.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide\Elements;

use AB\ATStyleGuide\ATFrameworkDefaults;

defined( 'ABSPATH' ) || exit;

/**
 * Spacing Element (Nestable).
 */
class Spacing extends \Bricks\Element {

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
	public $name = 'at-spacing';

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
	public $scripts = [ 'atSpacingInit' ];

	/**
	 * Nestable element.
	 *
	 * @var bool
	 */
	public $nestable = true;

	/**
	 * Get element label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Spacing', 'advanced-themer-style-guide' ) . ' (' . esc_html__( 'Nestable', 'advanced-themer-style-guide' ) . ')';
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'spacing', 'gap', 'margin', 'padding', 'scale', 'style guide', 'nestable' ];
	}

	/**
	 * Set control groups.
	 *
	 * @return void
	 */
	public function set_control_groups(): void {
		$this->control_groups['layout'] = [
			'title' => esc_html__( 'Layout', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['displayOverride'] = [
			'title' => esc_html__( 'Display Override', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['style'] = [
			'title' => esc_html__( 'Item Styling', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];
	}

	/**
	 * Set controls.
	 *
	 * @return void
	 */
	public function set_controls(): void {
		// Layout controls.
		$this->controls['layout'] = [
			'group'    => 'layout',
			'label'    => esc_html__( 'Layout', 'advanced-themer-style-guide' ),
			'type'     => 'select',
			'options'  => [
				'horizontal' => esc_html__( 'Horizontal Bars', 'advanced-themer-style-guide' ),
				'vertical'   => esc_html__( 'Vertical Bars', 'advanced-themer-style-guide' ),
			],
			'default'  => 'horizontal',
			'inline'   => true,
			'rerender' => true,
		];

		$this->controls['gap'] = [
			'group'   => 'layout',
			'label'   => esc_html__( 'Gap', 'advanced-themer-style-guide' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '1rem',
			'css'     => [
				[
					'property' => 'gap',
					'selector' => '',
				],
			],
		];

		// Style preset.
		$this->controls['stylePreset'] = [
			'group'    => 'layout',
			'label'    => esc_html__( 'Style Preset', 'advanced-themer-style-guide' ),
			'type'     => 'select',
			'options'  => [
				'default'   => esc_html__( 'Default', 'advanced-themer-style-guide' ),
				'minimal'   => esc_html__( 'Minimal', 'advanced-themer-style-guide' ),
				'bold'      => esc_html__( 'Bold', 'advanced-themer-style-guide' ),
				'colourful' => esc_html__( 'Colourful', 'advanced-themer-style-guide' ),
				'compact'   => esc_html__( 'Compact', 'advanced-themer-style-guide' ),
			],
			'default'  => 'default',
			'inline'   => true,
			'rerender' => true,
		];

		// Display Override controls - apply to all child items.
		$this->controls['overrideChildDisplay'] = [
			'group'       => 'displayOverride',
			'label'       => esc_html__( 'Override Child Display Settings', 'advanced-themer-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Enable to control display settings for all child items from here.', 'advanced-themer-style-guide' ),
		];

		$this->controls['hideLabel'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Label', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideVariable'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Variable Name', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideValue'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Computed Value', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideValueLabel'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Value Label', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		// Item styling controls.
		$this->controls['barColor'] = [
			'group' => 'style',
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
			'group'       => 'style',
			'label'       => esc_html__( 'Bar Height', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => '24px',
			'css'         => [
				[
					'property' => 'height',
					'selector' => '&:not(.atsg-spacing--vertical) .atsg-spacing-item__bar',
				],
			],
			'description' => esc_html__( 'Only applies to horizontal layout.', 'advanced-themer-style-guide' ),
		];

		$this->controls['labelTypography'] = [
			'group' => 'style',
			'label' => esc_html__( 'Label Typography', 'advanced-themer-style-guide' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.atsg-spacing-item__label',
				],
			],
		];

		$this->controls['infoMinWidth'] = [
			'group'   => 'style',
			'label'   => esc_html__( 'Info Min Width', 'advanced-themer-style-guide' ),
			'type'    => 'number',
			'units'   => true,
			'default' => '120px',
			'css'     => [
				[
					'property' => 'min-width',
					'selector' => '.atsg-spacing-item__info',
				],
			],
		];
	}

	/**
	 * Get nestable item structure.
	 *
	 * @return array
	 */
	public function get_nestable_item(): array {
		return [
			'name'     => 'at-spacing-item',
			'label'    => esc_html__( 'Spacing Item', 'advanced-themer-style-guide' ),
			'settings' => [
				'label'    => '{item_label}',
				'variable' => '{item_variable}',
			],
		];
	}

	/**
	 * Get nestable children.
	 *
	 * @return array
	 */
	public function get_nestable_children(): array {
		$default_items = [
			[ 'label' => '3XS', 'variable' => '--at-space--3xs' ],
			[ 'label' => '2XS', 'variable' => '--at-space--2xs' ],
			[ 'label' => 'XS', 'variable' => '--at-space--xs' ],
			[ 'label' => 'S', 'variable' => '--at-space--s' ],
			[ 'label' => 'M', 'variable' => '--at-space--m' ],
			[ 'label' => 'L', 'variable' => '--at-space--l' ],
			[ 'label' => 'XL', 'variable' => '--at-space--xl' ],
			[ 'label' => '2XL', 'variable' => '--at-space--2xl' ],
			[ 'label' => '3XL', 'variable' => '--at-space--3xl' ],
		];

		$children = [];

		foreach ( $default_items as $item ) {
			$child = $this->get_nestable_item();

			// Replace placeholders.
			$child      = wp_json_encode( $child );
			$child      = str_replace( '{item_label}', $item['label'], $child );
			$child      = str_replace( '{item_variable}', $item['variable'], $child );
			$child      = json_decode( $child, true );
			$children[] = $child;
		}

		return $children;
	}

	/**
	 * Render element.
	 *
	 * @return void
	 */
	public function render(): void {
		// Check for ATF variables.
		if ( ! ATFrameworkDefaults::has_at_variables() ) {
			echo ATFrameworkDefaults::render_warning(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$settings = $this->settings;

		$layout       = $settings['layout'] ?? 'horizontal';
		$style_preset = $settings['stylePreset'] ?? 'default';

		$root_classes = [ 'atsg-spacing', 'atsg-spacing--' . $layout ];
		if ( 'default' !== $style_preset ) {
			$root_classes[] = 'atsg-spacing--' . $style_preset;
		}

		$this->set_attribute( '_root', 'class', $root_classes );

		// Pass override settings as data attributes for child elements to read.
		if ( isset( $settings['overrideChildDisplay'] ) ) {
			$this->set_attribute( '_root', 'data-override', 'true' );

			$override_settings = [
				'hideLabel',
				'hideVariable',
				'hideValue',
				'hideValueLabel',
			];

			foreach ( $override_settings as $setting_key ) {
				if ( isset( $settings[ $setting_key ] ) ) {
					// Convert camelCase to kebab-case for data attribute.
					$data_key = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $setting_key ) );
					$this->set_attribute( '_root', 'data-' . $data_key, 'true' );
				}
			}
		}

		$output = "<div {$this->render_attributes( '_root' )}>";

		// Render children elements (individual spacing items).
		$output .= \Bricks\Frontend::render_children( $this );

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
			'at-spacing',
			false,
			[],
			AT_STYLE_GUIDE_VERSION
		);
		wp_add_inline_style( 'at-spacing', $this->get_element_css() );
		wp_enqueue_style( 'at-spacing' );
	}

	/**
	 * Get inline CSS for the element.
	 *
	 * @return string
	 */
	private function get_element_css(): string {
		return '
			.atsg-spacing {
				display: flex;
				flex-direction: column;
				gap: var(--at-space--s, 1rem);
			}

			.atsg-spacing__placeholder {
				padding: var(--at-space--l, 2rem);
				background: var(--at-neutral-t-6, #f3f4f6);
				border: var(--at-border-width, 2px) dashed var(--at-border-color, #d1d5db);
				border-radius: var(--at-radius--s, 8px);
				text-align: center;
				color: var(--at-neutral-d-2, #6b7280);
			}

			/* Vertical layout */
			.atsg-spacing--vertical {
				flex-direction: row;
				flex-wrap: wrap;
				align-items: flex-end;
				gap: var(--at-space--m, 1.5rem);
			}

			.atsg-spacing--vertical .atsg-spacing-item {
				flex-direction: column;
				align-items: center;
			}

			.atsg-spacing--vertical .atsg-spacing-item__info {
				min-width: auto;
				align-items: center;
				order: 2;
			}

			.atsg-spacing--vertical .atsg-spacing-item__bar-container {
				flex-direction: column;
				justify-content: flex-end;
				height: 200px;
				order: 1;
			}

			.atsg-spacing--vertical .atsg-spacing-item__bar {
				width: 24px !important;
				/* height comes from inline style */
			}

			/* Style: Minimal */
			.atsg-spacing--minimal .atsg-spacing-item__label {
				font-weight: 400;
			}

			.atsg-spacing--minimal .atsg-spacing-item__variable {
				display: none;
			}

			/* Style: Bold */
			.atsg-spacing--bold .atsg-spacing-item__label {
				font-size: var(--at-text--s, 0.875rem);
				font-weight: 700;
			}

			.atsg-spacing--bold .atsg-spacing-item__bar {
				height: 32px;
			}

			/* Style: Colourful */
			.atsg-spacing--colourful .atsg-spacing-item__bar {
				background: linear-gradient(90deg, var(--at-primary, #3b82f6), var(--at-secondary, #8b5cf6));
			}

			.atsg-spacing--colourful .atsg-spacing-item__label {
				color: var(--at-primary, #3b82f6);
			}

			.atsg-spacing--colourful .atsg-spacing-item__variable {
				background: var(--at-primary-l-5, #dbeafe);
				color: var(--at-primary-d-2, #1d4ed8);
			}

			/* Style: Compact */
			.atsg-spacing--compact {
				gap: var(--at-space--3xs, 0.25rem);
			}

			.atsg-spacing--compact .atsg-spacing-item {
				gap: var(--at-space--2xs, 0.5rem);
			}

			.atsg-spacing--compact .atsg-spacing-item__info {
				min-width: 80px;
				gap: var(--at-space--3xs, 0.125rem);
			}

			.atsg-spacing--compact .atsg-spacing-item__label {
				font-size: var(--at-text--xs, 0.75rem);
			}

			.atsg-spacing--compact .atsg-spacing-item__variable {
				font-size: var(--at-text--xs, 0.75rem);
			}

			.atsg-spacing--compact .atsg-spacing-item__bar {
				height: 16px;
			}

			/* Parent override styles - hide elements based on parent data attributes */
			.atsg-spacing[data-override="true"][data-hide-label="true"] .atsg-spacing-item__label {
				display: none;
			}

			.atsg-spacing[data-override="true"][data-hide-variable="true"] .atsg-spacing-item__variable {
				display: none;
			}

			.atsg-spacing[data-override="true"][data-hide-value="true"] .atsg-spacing-item__value {
				display: none;
			}

			.atsg-spacing[data-override="true"][data-hide-value-label="true"] .atsg-spacing-item__value-label {
				display: none;
			}
		';
	}
}
