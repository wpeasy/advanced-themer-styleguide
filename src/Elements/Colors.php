<?php
/**
 * Colors Element (Nestable) for Bricks Builder.
 *
 * Container element for color swatches from Advanced Themer.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide\Elements;

use AB\ATStyleGuide\ATColors;
use AB\ATStyleGuide\ATFrameworkDefaults;

defined( 'ABSPATH' ) || exit;

/**
 * Colors Element (Nestable).
 */
class Colors extends \Bricks\Element {

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
	public $name = 'at-colors';

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
	public $scripts = [ 'atColorsInit' ];

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
		return esc_html__( 'Colors', 'advanced-themer-style-guide' ) . ' (' . esc_html__( 'Nestable', 'advanced-themer-style-guide' ) . ')';
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'color', 'swatch', 'palette', 'style guide', 'nestable', 'advanced themer' ];
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

		$this->control_groups['variationsOverride'] = [
			'title' => esc_html__( 'Variations Override', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['styleOverride'] = [
			'title' => esc_html__( 'Style Override', 'advanced-themer-style-guide' ),
			'tab'   => 'content',
		];
	}

	/**
	 * Set controls.
	 *
	 * @return void
	 */
	public function set_controls(): void {
		// Base font size control.
		$this->controls['baseFontSize'] = [
			'group'       => 'layout',
			'label'       => esc_html__( 'Base Font Size', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'default'     => 'var(--at-text--s)',
			'css'         => [
				[
					'property' => 'font-size',
					'selector' => '',
				],
			],
			'description' => esc_html__( 'Base font size for the element. Sub-components use em units relative to this.', 'advanced-themer-style-guide' ),
		];

		// Layout mode control.
		$this->controls['layoutMode'] = [
			'group'       => 'layout',
			'label'       => esc_html__( 'Layout Mode', 'advanced-themer-style-guide' ),
			'type'        => 'select',
			'options'     => [
				'default'          => esc_html__( 'Default (Grid)', 'advanced-themer-style-guide' ),
				'stacked'          => esc_html__( 'Stacked (Vertical)', 'advanced-themer-style-guide' ),
				'compact'          => esc_html__( 'Compact', 'advanced-themer-style-guide' ),
				'compact-vertical' => esc_html__( 'Compact Vertical', 'advanced-themer-style-guide' ),
			],
			'default'     => 'default',
			'placeholder' => esc_html__( 'Default (Grid)', 'advanced-themer-style-guide' ),
			'rerender'    => true,
		];

		// Layout controls - Flex options using Bricks dedicated control types.
		$this->controls['flexDirection'] = [
			'group'   => 'layout',
			'label'   => esc_html__( 'Direction', 'advanced-themer-style-guide' ),
			'type'    => 'direction',
			'inline'  => true,
			'css'     => [
				[
					'property' => 'flex-direction',
					'selector' => '',
				],
			],
		];

		$this->controls['justifyContent'] = [
			'group'   => 'layout',
			'label'   => esc_html__( 'Justify', 'advanced-themer-style-guide' ),
			'type'    => 'justify-content',
			'inline'  => true,
			'css'     => [
				[
					'property' => 'justify-content',
					'selector' => '',
				],
			],
		];

		$this->controls['alignItems'] = [
			'group'   => 'layout',
			'label'   => esc_html__( 'Align', 'advanced-themer-style-guide' ),
			'type'    => 'align-items',
			'inline'  => true,
			'css'     => [
				[
					'property' => 'align-items',
					'selector' => '',
				],
			],
		];

		$this->controls['gap'] = [
			'group'       => 'layout',
			'label'       => esc_html__( 'Gap', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => '2em',
			'css'         => [
				[
					'property' => 'gap',
					'selector' => '',
				],
			],
		];

		$this->controls['showA11yGlossary'] = [
			'group'       => 'layout',
			'label'       => esc_html__( 'Show A11Y Glossary', 'advanced-themer-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Show the WCAG contrast standards glossary when A11Y badges are enabled.', 'advanced-themer-style-guide' ),
		];

		// Display Override controls.
		$this->controls['overrideChildDisplay'] = [
			'group'       => 'displayOverride',
			'label'       => esc_html__( 'Override Child Display Settings', 'advanced-themer-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Enable to control display settings for all child items from here.', 'advanced-themer-style-guide' ),
		];

		$this->controls['hideColorName'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Color Name', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideCssVariable'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide CSS Variable', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideHexValue'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Hex/Color Value', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		$this->controls['hideShadeLabels'] = [
			'group'    => 'displayOverride',
			'label'    => esc_html__( 'Hide Shade Labels', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildDisplay', '!=', '' ],
		];

		// Variations Override controls.
		$this->controls['overrideChildVariations'] = [
			'group'       => 'variationsOverride',
			'label'       => esc_html__( 'Override Child Variation Settings', 'advanced-themer-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Enable to control variation settings for all child items from here.', 'advanced-themer-style-guide' ),
		];

		$this->controls['hideVariations'] = [
			'group'    => 'variationsOverride',
			'label'    => esc_html__( 'Hide All Variations', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildVariations', '!=', '' ],
		];

		$this->controls['hideLightShades'] = [
			'group'    => 'variationsOverride',
			'label'    => esc_html__( 'Hide Light Shades', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildVariations', '!=', '' ],
		];

		$this->controls['hideDarkShades'] = [
			'group'    => 'variationsOverride',
			'label'    => esc_html__( 'Hide Dark Shades', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildVariations', '!=', '' ],
		];

		$this->controls['hideTransparencyShades'] = [
			'group'    => 'variationsOverride',
			'label'    => esc_html__( 'Hide Transparency Shades', 'advanced-themer-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
			'required' => [ 'overrideChildVariations', '!=', '' ],
		];

		// Style Override controls.
		$this->controls['overrideChildStyle'] = [
			'group'       => 'styleOverride',
			'label'       => esc_html__( 'Override Child Style Settings', 'advanced-themer-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Enable to control swatch styling for all child items from here.', 'advanced-themer-style-guide' ),
		];

		$this->controls['parentSwatchSize'] = [
			'group'       => 'styleOverride',
			'label'       => esc_html__( 'Variant Swatch Size', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-space--xl)',
			'required'    => [ 'overrideChildStyle', '!=', '' ],
			'css'         => [
				[
					'property' => 'width',
					'selector' => '.atsg-colors-item__swatch',
				],
				[
					'property' => 'height',
					'selector' => '.atsg-colors-item__swatch',
				],
				[
					'property'  => 'min-width',
					'selector'  => '.atsg-colors-item__swatch',
					'important' => true,
				],
			],
		];

		$this->controls['parentBaseSize'] = [
			'group'       => 'styleOverride',
			'label'       => esc_html__( 'Base Swatch Width', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-space--xl)',
			'required'    => [ 'overrideChildStyle', '!=', '' ],
			'css'         => [
				[
					'property'  => 'width',
					'selector'  => '.atsg-colors-item__base-column',
					'important' => true,
				],
				[
					'property'  => 'width',
					'selector'  => '.atsg-colors-item__base',
					'important' => true,
				],
			],
		];

		$this->controls['parentSwatchGap'] = [
			'group'       => 'styleOverride',
			'label'       => esc_html__( 'Swatch Gap', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-space--3xs)',
			'required'    => [ 'overrideChildStyle', '!=', '' ],
			'css'         => [
				[
					'property' => 'gap',
					'selector' => '.atsg-colors-item__grid',
				],
				[
					'property' => 'gap',
					'selector' => '.atsg-colors-item__column',
				],
			],
		];

		$this->controls['parentSwatchBorderRadius'] = [
			'group'       => 'styleOverride',
			'label'       => esc_html__( 'Swatch Border Radius', 'advanced-themer-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-radius--m)',
			'required'    => [ 'overrideChildStyle', '!=', '' ],
			'css'         => [
				[
					'property' => 'border-radius',
					'selector' => '.atsg-colors-item__swatch',
				],
				[
					'property' => 'border-radius',
					'selector' => '.atsg-colors-item__base',
				],
			],
		];

		$this->controls['labelTypography'] = [
			'group'    => 'styleOverride',
			'label'    => esc_html__( 'Label Typography', 'advanced-themer-style-guide' ),
			'type'     => 'typography',
			'required' => [ 'overrideChildStyle', '!=', '' ],
			'css'      => [
				[
					'property' => 'font',
					'selector' => '.atsg-colors-item__label',
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
			'name'     => 'at-colors-item',
			'label'    => esc_html__( 'Color Item', 'advanced-themer-style-guide' ),
			'settings' => [
				'atColor' => '{item_color_id}',
			],
		];
	}

	/**
	 * Get nestable children.
	 *
	 * Creates a color item for each defined AT color.
	 *
	 * @return array
	 */
	public function get_nestable_children(): array {
		$root_colors = ATColors::get_root_colors();
		$children    = [];

		foreach ( $root_colors as $color_id => $color ) {
			$child = $this->get_nestable_item();

			// Replace placeholders.
			$child = wp_json_encode( $child );
			$child = str_replace( '{item_color_id}', $color_id, $child );
			$child = json_decode( $child, true );

			$children[] = $child;
		}

		// If no colors found, return empty array.
		if ( empty( $children ) ) {
			return [];
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

		$layout_mode  = $settings['layoutMode'] ?? 'default';
		$root_classes = [ 'atsg-colors' ];

		if ( 'default' !== $layout_mode ) {
			$root_classes[] = 'atsg-colors--' . $layout_mode;
		}

		$this->set_attribute( '_root', 'class', $root_classes );
		$this->set_attribute( '_root', 'data-layout', esc_attr( $layout_mode ) );

		// Pass display override settings as data attributes.
		if ( isset( $settings['overrideChildDisplay'] ) ) {
			$this->set_attribute( '_root', 'data-override-display', 'true' );

			$display_overrides = [
				'hideColorName',
				'hideCssVariable',
				'hideHexValue',
				'hideShadeLabels',
			];

			foreach ( $display_overrides as $setting_key ) {
				if ( isset( $settings[ $setting_key ] ) ) {
					$data_key = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $setting_key ) );
					$this->set_attribute( '_root', 'data-' . $data_key, 'true' );
				}
			}
		}

		// Pass variations override settings as data attributes.
		if ( isset( $settings['overrideChildVariations'] ) ) {
			$this->set_attribute( '_root', 'data-override-variations', 'true' );

			$variation_overrides = [
				'hideVariations',
				'hideLightShades',
				'hideDarkShades',
				'hideTransparencyShades',
			];

			foreach ( $variation_overrides as $setting_key ) {
				if ( isset( $settings[ $setting_key ] ) ) {
					$data_key = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $setting_key ) );
					$this->set_attribute( '_root', 'data-' . $data_key, 'true' );
				}
			}
		}

		// Check if glossary should be shown.
		// Bricks checkbox: key exists = checked, key missing = unchecked.
		// We want glossary shown by default, so show if key exists OR if never set.
		$show_glossary = isset( $settings['showA11yGlossary'] );

		// Pass glossary setting as data attribute.
		if ( $show_glossary ) {
			$this->set_attribute( '_root', 'data-show-glossary', 'true' );
		}

		$output = "<div {$this->render_attributes( '_root' )}>";

		// A11Y badges toggle switch.
		$output .= '<div class="atsg-colors__toolbar">';
		$output .= '<label class="atsg-colors__toggle">';
		$output .= '<input type="checkbox" class="atsg-colors__toggle-input" data-toggle="a11y-badges">';
		$output .= '<span class="atsg-colors__toggle-switch"></span>';
		$output .= '<span class="atsg-colors__toggle-label">' . esc_html__( 'A11Y Badges', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '</label>';
		$output .= '</div>';

		// A11Y Glossary - reveals when toggle is enabled (if glossary is enabled in settings).
		$output .= '<div class="atsg-colors__glossary">';
		$output .= '<div class="atsg-colors__glossary-inner">';

		// WCAG Standard explanation.
		$output .= '<div class="atsg-colors__glossary-section">';
		$output .= '<h4 class="atsg-colors__glossary-title">' . esc_html__( 'WCAG Contrast Standards', 'advanced-themer-style-guide' ) . '</h4>';
		$output .= '<p class="atsg-colors__glossary-text">' . esc_html__( 'The Web Content Accessibility Guidelines (WCAG) define minimum contrast ratios between text and background colors to ensure readability for users with visual impairments.', 'advanced-themer-style-guide' ) . '</p>';
		$output .= '</div>';

		// Contrast ratios.
		$output .= '<div class="atsg-colors__glossary-section">';
		$output .= '<h4 class="atsg-colors__glossary-title">' . esc_html__( 'Contrast Ratios', 'advanced-themer-style-guide' ) . '</h4>';
		$output .= '<ul class="atsg-colors__glossary-list">';
		$output .= '<li><strong>AAA</strong> ' . esc_html__( '(7:1+) - Enhanced contrast, best for body text', 'advanced-themer-style-guide' ) . '</li>';
		$output .= '<li><strong>AA</strong> ' . esc_html__( '(4.5:1+) - Minimum for normal text', 'advanced-themer-style-guide' ) . '</li>';
		$output .= '<li><strong>AA Large</strong> ' . esc_html__( '(3:1+) - Minimum for large text (24px+ or 19px+ bold)*', 'advanced-themer-style-guide' ) . '</li>';
		$output .= '</ul>';
		$output .= '<p class="atsg-colors__glossary-note">' . esc_html__( '*Approximate px values at 96dpi', 'advanced-themer-style-guide' ) . '</p>';
		$output .= '</div>';

		// Badge legend.
		$output .= '<div class="atsg-colors__glossary-section">';
		$output .= '<h4 class="atsg-colors__glossary-title">' . esc_html__( 'Badge Legend', 'advanced-themer-style-guide' ) . '</h4>';
		$output .= '<div class="atsg-colors__glossary-badges">';
		$output .= '<div class="atsg-colors__glossary-badge-item">';
		$output .= '<span class="atsg-colors__glossary-badge atsg-colors__glossary-badge--pass">' . esc_html__( 'AAA / AA', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '<span class="atsg-colors__glossary-badge-desc">' . esc_html__( 'Pass - Good contrast', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '</div>';
		$output .= '<div class="atsg-colors__glossary-badge-item">';
		$output .= '<span class="atsg-colors__glossary-badge atsg-colors__glossary-badge--large">' . esc_html__( 'AA Large', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '<span class="atsg-colors__glossary-badge-desc">' . esc_html__( 'Large text only', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '</div>';
		$output .= '<div class="atsg-colors__glossary-badge-item">';
		$output .= '<span class="atsg-colors__glossary-badge atsg-colors__glossary-badge--fail">' . esc_html__( 'Fail', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '<span class="atsg-colors__glossary-badge-desc">' . esc_html__( 'Insufficient contrast', 'advanced-themer-style-guide' ) . '</span>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		// W/B badges explanation.
		$output .= '<div class="atsg-colors__glossary-section">';
		$output .= '<p class="atsg-colors__glossary-text atsg-colors__glossary-text--small">';
		$output .= '<strong>W</strong> = ' . esc_html__( 'contrast against white text', 'advanced-themer-style-guide' ) . '<br>';
		$output .= '<strong>B</strong> = ' . esc_html__( 'contrast against black text', 'advanced-themer-style-guide' );
		$output .= '</p>';
		$output .= '</div>';

		$output .= '</div>'; // .atsg-colors__glossary-inner
		$output .= '</div>'; // .atsg-colors__glossary

		// Render children elements (individual color items).
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
		$handle = 'at-colors';

		// Only register and add inline styles once.
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, false, [], AT_STYLE_GUIDE_VERSION );
			wp_add_inline_style( $handle, $this->get_element_css() );
		}

		wp_enqueue_style( $handle );
	}

	/**
	 * Get inline CSS for the element.
	 *
	 * @return string
	 */
	private function get_element_css(): string {
		return '
			/* Critical layout */
			.atsg-colors {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				gap: 2em;
			}

			/* Parent display override styles */
			.atsg-colors[data-override-display="true"][data-hide-color-name="true"] .atsg-colors-item__label {
				display: none;
			}

			/* Parent variations override styles */
			/* Hide all variation columns (keep only base) */
			.atsg-colors[data-override-variations="true"][data-hide-variations="true"] .atsg-colors-item__column {
				display: none;
			}

			/* Hide dark shades column */
			.atsg-colors[data-override-variations="true"][data-hide-dark-shades="true"] .atsg-colors-item__column[data-variant="dark"] {
				display: none;
			}

			/* Hide light shades column */
			.atsg-colors[data-override-variations="true"][data-hide-light-shades="true"] .atsg-colors-item__column[data-variant="light"] {
				display: none;
			}

			/* Hide transparency shades column */
			.atsg-colors[data-override-variations="true"][data-hide-transparency-shades="true"] .atsg-colors-item__column[data-variant="transparency"] {
				display: none;
			}

			/* Toolbar with toggle */
			.atsg-colors__toolbar {
				width: 100%;
				display: flex;
				justify-content: flex-start;
				margin-bottom: 0.5em;
			}

			.atsg-colors__toggle {
				display: flex;
				align-items: center;
				gap: 0.375em;
				cursor: pointer;
				user-select: none;
			}

			.atsg-colors__toggle-input {
				position: absolute;
				opacity: 0;
				width: 0;
				height: 0;
			}

			/* A11Y badges - hidden by default */
			.atsg-colors .atsg-colors-item__contrast-badges {
				display: none;
			}

			/* Show A11Y badges when toggle is checked */
			.atsg-colors[data-show-a11y-badges="true"] .atsg-colors-item__contrast-badges {
				display: flex;
			}

			/* A11Y Glossary - completely hidden by default with smooth reveal */
			.atsg-colors__glossary {
				width: 100%;
				display: grid;
				grid-template-rows: 0fr;
				transition: grid-template-rows 0.3s ease-out;
			}

			.atsg-colors__glossary-inner {
				min-height: 0;
				overflow: hidden;
				display: flex;
				flex-wrap: wrap;
				gap: 1.5em;
				padding: 0;
				border: 1px solid transparent;
				transition: padding 0.3s ease-out, border-color 0.3s ease-out;
			}

			/* Show glossary when A11Y toggle is enabled AND glossary setting is on */
			.atsg-colors[data-show-a11y-badges="true"][data-show-glossary="true"] .atsg-colors__glossary {
				grid-template-rows: 1fr;
			}

			.atsg-colors[data-show-a11y-badges="true"][data-show-glossary="true"] .atsg-colors__glossary-inner {
				padding: 1em;
				border-color: var(--at-neutral-t-4, #d1d5db);
			}

			.atsg-colors__glossary-section {
				flex: 1 1 200px;
				min-width: 180px;
			}

			.atsg-colors__glossary-badges {
				display: flex;
				flex-direction: column;
				gap: 0.5em;
			}

			.atsg-colors__glossary-badge-item {
				display: flex;
				align-items: center;
				gap: 0.5em;
			}

			@layer atsg {
			.atsg-colors__toggle-switch {
				position: relative;
				width: 1.5em;
				height: 0.875em;
				background: var(--at-neutral-t-4, #d1d5db);
				border-radius: 0.4375em;
				transition: background 0.2s ease;
			}

			.atsg-colors__toggle-switch::after {
				content: "";
				position: absolute;
				top: 0.125em;
				left: 0.125em;
				width: 0.625em;
				height: 0.625em;
				background: var(--at-white, #ffffff);
				border-radius: 50%;
				transition: transform 0.2s ease;
			}

			.atsg-colors__toggle-input:checked + .atsg-colors__toggle-switch {
				background: var(--at-primary, #3b82f6);
			}

			.atsg-colors__toggle-input:checked + .atsg-colors__toggle-switch::after {
				transform: translateX(0.625em);
			}

			.atsg-colors__toggle-label {
				font-size: 0.6875em;
				color: var(--at-neutral-d-2, #6b7280);
			}

			.atsg-colors__glossary-inner {
				background: var(--at-neutral-t-6, #f3f4f6);
				border-radius: var(--at-radius--s, 0.5em);
			}

			.atsg-colors__glossary-title {
				margin: 0 0 0.5em 0;
				font-size: 0.8125em;
				font-weight: 600;
				color: var(--at-neutral-d-4, #1f2937);
			}

			.atsg-colors__glossary-text {
				margin: 0;
				font-size: 0.75em;
				line-height: 1.5;
				color: var(--at-neutral-d-2, #6b7280);
			}

			.atsg-colors__glossary-text--small {
				font-size: 0.6875em;
			}

			.atsg-colors__glossary-list {
				margin: 0;
				padding-left: 1em;
				font-size: 0.75em;
				line-height: 1.6;
				color: var(--at-neutral-d-2, #6b7280);
			}

			.atsg-colors__glossary-list li {
				margin-bottom: 0.25em;
			}

			.atsg-colors__glossary-note {
				margin: 0.5em 0 0 0;
				font-size: 0.625em;
				font-style: italic;
				color: var(--at-neutral-d-1, #9ca3af);
			}

			.atsg-colors__glossary-badge {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				padding: 0.125em 0.5em;
				font-size: 0.625em;
				font-weight: 600;
				border-radius: 0.25em;
				min-width: 3.5em;
				text-align: center;
			}

			.atsg-colors__glossary-badge--pass {
				background: var(--at-success, #10b981);
				color: #ffffff;
			}

			.atsg-colors__glossary-badge--large {
				background: var(--at-warning, #f59e0b);
				color: #ffffff;
			}

			.atsg-colors__glossary-badge--fail {
				background: var(--at-error, #ef4444);
				color: #ffffff;
			}

			.atsg-colors__glossary-badge-desc {
				font-size: 0.6875em;
				color: var(--at-neutral-d-2, #6b7280);
			}
			} /* end @layer atsg */
		';
	}
}
