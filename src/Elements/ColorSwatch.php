<?php
/**
 * Color Swatch Element for Bricks Builder.
 *
 * Displays a color from Advanced Themer or Automatic CSS with its variations.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Elements;

use AB\BricksSG\ATColors;
use AB\BricksSG\ATFrameworkDefaults;
use AB\BricksSG\Framework\FrameworkDetector;
use AB\BricksSG\Framework\FrameworkVariables;

defined( 'ABSPATH' ) || exit;

/**
 * Color Swatch Element.
 */
class ColorSwatch extends \Bricks\Element {

	/**
	 * Element category - set dynamically based on active framework.
	 *
	 * @var string
	 */
	public $category = 'bricks style guide';

	/**
	 * Constructor - set category based on active framework.
	 *
	 * @param \Bricks\Element|null $element The element.
	 */
	public function __construct( $element = null ) {
		$framework_name = FrameworkDetector::get_active_framework_name();
		if ( $framework_name ) {
			$this->category = $framework_name . ' Style Guide';
		}
		parent::__construct( $element );
	}

	/**
	 * Element name.
	 *
	 * @var string
	 */
	public $name = 'bsg-color-swatch';

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
	public function get_label(): string {
		return esc_html__( 'AT Color Swatch', 'bricks-style-guide' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'color', 'swatch', 'palette', 'style guide', 'advanced themer', 'variations' ];
	}

	/**
	 * Set control groups.
	 *
	 * @return void
	 */
	public function set_control_groups(): void {
		$this->control_groups['color'] = [
			'title' => esc_html__( 'Color Selection', 'bricks-style-guide' ),
		];

		$this->control_groups['variations'] = [
			'title' => esc_html__( 'Variations', 'bricks-style-guide' ),
		];

		$this->control_groups['display'] = [
			'title' => esc_html__( 'Display', 'bricks-style-guide' ),
		];

		$this->control_groups['labels'] = [
			'title' => esc_html__( 'Labels', 'bricks-style-guide' ),
		];
	}

	/**
	 * Set controls.
	 *
	 * @return void
	 */
	public function set_controls(): void {
		// Color Selection group.
		$this->controls['atColor'] = [
			'group'       => 'color',
			'label'       => esc_html__( 'Select Color', 'bricks-style-guide' ),
			'type'        => 'select',
			'options'     => ATColors::get_root_colors_for_select(),
			'placeholder' => esc_html__( 'Select an AT color', 'bricks-style-guide' ),
		];

		$this->controls['colorInfo'] = [
			'group'    => 'color',
			'type'     => 'info',
			'content'  => esc_html__( 'Colors are loaded from Advanced Themer Color Manager. Only root colors (not variations) are shown.', 'bricks-style-guide' ),
			'required' => [ 'atColor', '=', '' ],
		];

		// Variations group.
		$this->controls['showVariations'] = [
			'group'   => 'variations',
			'label'   => esc_html__( 'Show Variations', 'bricks-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showLightShades'] = [
			'group'    => 'variations',
			'label'    => esc_html__( 'Show Light Shades (l-1 to l-6)', 'bricks-style-guide' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => [ 'showVariations', '!=', '' ],
		];

		$this->controls['showDarkShades'] = [
			'group'    => 'variations',
			'label'    => esc_html__( 'Show Dark Shades (d-1 to d-6)', 'bricks-style-guide' ),
			'type'     => 'checkbox',
			'default'  => true,
			'required' => [ 'showVariations', '!=', '' ],
		];

		// Display group.
		$this->controls['layout'] = [
			'group'   => 'display',
			'label'   => esc_html__( 'Layout', 'bricks-style-guide' ),
			'type'    => 'select',
			'options' => [
				'horizontal' => esc_html__( 'Horizontal', 'bricks-style-guide' ),
				'vertical'   => esc_html__( 'Vertical', 'bricks-style-guide' ),
			],
			'default' => 'horizontal',
			'inline'  => true,
		];

		$this->controls['swatchSize'] = [
			'group'       => 'display',
			'label'       => esc_html__( 'Swatch Size', 'bricks-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-space--xl)',
			'css'         => [
				[
					'property' => 'width',
					'selector' => '.bsg-swatch__item',
				],
				[
					'property' => 'height',
					'selector' => '.bsg-swatch__item',
				],
			],
		];

		$this->controls['baseSizeMultiplier'] = [
			'group'       => 'display',
			'label'       => esc_html__( 'Base Color Size Multiplier', 'bricks-style-guide' ),
			'type'        => 'number',
			'default'     => 1.5,
			'step'        => 0.1,
			'min'         => 1,
			'max'         => 3,
			'description' => esc_html__( 'Make the base color larger than variations', 'bricks-style-guide' ),
		];

		$this->controls['swatchBorderRadius'] = [
			'group'       => 'display',
			'label'       => esc_html__( 'Border Radius', 'bricks-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-radius--s)',
			'css'         => [
				[
					'property' => 'border-radius',
					'selector' => '.bsg-swatch__item',
				],
			],
		];

		$this->controls['gap'] = [
			'group'       => 'display',
			'label'       => esc_html__( 'Gap', 'bricks-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'placeholder' => 'var(--at-space--xs)',
			'css'         => [
				[
					'property' => 'gap',
					'selector' => '.bsg-swatch__row',
				],
			],
		];

		// Labels group.
		$this->controls['showColorName'] = [
			'group'   => 'labels',
			'label'   => esc_html__( 'Show Color Name', 'bricks-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showCssVariable'] = [
			'group'   => 'labels',
			'label'   => esc_html__( 'Show CSS Variable', 'bricks-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['showHexValue'] = [
			'group'   => 'labels',
			'label'   => esc_html__( 'Show Hex/Color Value', 'bricks-style-guide' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['showShadeLabel'] = [
			'group'   => 'labels',
			'label'   => esc_html__( 'Show Shade Labels', 'bricks-style-guide' ),
			'type'    => 'checkbox',
			'default' => true,
		];
	}

	/**
	 * Render element.
	 *
	 * @return void
	 */
	public function render(): void {
		// Check for framework variables (AT or ACSS).
		if ( ! ATFrameworkDefaults::has_framework_variables() ) {
			echo ATFrameworkDefaults::render_warning(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		$settings = $this->settings;

		$color_id = $settings['atColor'] ?? '';

		if ( empty( $color_id ) ) {
			if ( bricks_is_builder() ) {
				echo '<div class="bsg-swatch__placeholder">';
				echo esc_html__( 'Please select a color from Advanced Themer', 'bricks-style-guide' );
				echo '</div>';
			}
			return;
		}

		$root_color = ATColors::get_root_color( $color_id );

		if ( ! $root_color ) {
			if ( bricks_is_builder() ) {
				echo '<div class="bsg-swatch__placeholder">';
				echo esc_html__( 'Selected color not found. It may have been deleted from Advanced Themer.', 'bricks-style-guide' );
				echo '</div>';
			}
			return;
		}

		// Settings.
		$show_variations   = $settings['showVariations'] ?? true;
		$show_light_shades = $settings['showLightShades'] ?? true;
		$show_dark_shades  = $settings['showDarkShades'] ?? true;
		$layout            = $settings['layout'] ?? 'horizontal';
		$base_multiplier   = floatval( $settings['baseSizeMultiplier'] ?? 1.5 );
		$show_color_name   = $settings['showColorName'] ?? true;
		$show_css_variable = $settings['showCssVariable'] ?? true;
		$show_hex_value    = $settings['showHexValue'] ?? false;
		$show_shade_label  = $settings['showShadeLabel'] ?? true;

		// Get variations if enabled.
		$variations = [];
		if ( $show_variations && ! empty( $root_color['shadeChildren'] ) ) {
			$all_variations = ATColors::get_color_variations( $color_id );

			foreach ( $all_variations as $suffix => $variation ) {
				// Filter by light/dark preference.
				if ( 'base' === $suffix ) {
					$variations[ $suffix ] = $variation;
				} elseif ( str_starts_with( $suffix, 'l-' ) && $show_light_shades ) {
					$variations[ $suffix ] = $variation;
				} elseif ( str_starts_with( $suffix, 'd-' ) && $show_dark_shades ) {
					$variations[ $suffix ] = $variation;
				}
			}
		} else {
			// Just show base color.
			$variations['base'] = [
				'id'       => $root_color['id'],
				'name'     => $root_color['name'],
				'suffix'   => 'base',
				'raw'      => $root_color['raw'],
				'hex'      => $root_color['hex'],
				'rawValue' => $root_color['rawValue'],
			];
		}

		// Build output.
		$root_classes = [ 'bsg-swatch', 'bsg-swatch--' . $layout ];
		$this->set_attribute( '_root', 'class', $root_classes );

		$output = "<div {$this->render_attributes( '_root' )}>";

		// Color header.
		if ( $show_color_name || $show_css_variable ) {
			$output .= '<div class="bsg-swatch__header">';

			if ( $show_color_name ) {
				$output .= '<h4 class="bsg-swatch__title">' . esc_html( $root_color['label'] ) . '</h4>';
			}

			if ( $show_css_variable ) {
				$output .= '<code class="bsg-swatch__variable">' . esc_html( $root_color['raw'] ) . '</code>';
			}

			$output .= '</div>';
		}

		// Color swatches row.
		$output .= '<div class="bsg-swatch__row">';

		foreach ( $variations as $suffix => $variation ) {
			$is_base     = 'base' === $suffix;
			$item_class  = 'bsg-swatch__item';
			$item_class .= $is_base ? ' bsg-swatch__item--base' : '';

			// Use CSS variable for background.
			$css_var    = $variation['raw'];
			$item_style = "background-color: {$css_var};";

			// Apply base multiplier.
			if ( $is_base && $base_multiplier > 1 ) {
				$item_style .= " transform: scale({$base_multiplier}); z-index: 1;";
			}

			$output .= '<div class="' . esc_attr( $item_class ) . '" style="' . esc_attr( $item_style ) . '"';
			$output .= ' title="' . esc_attr( $css_var ) . '">';

			if ( $show_shade_label && ! $is_base ) {
				$output .= '<span class="bsg-swatch__shade-label">' . esc_html( $suffix ) . '</span>';
			}

			$output .= '</div>';
		}

		$output .= '</div>'; // .bsg-swatch__row

		// Hex value if enabled.
		if ( $show_hex_value ) {
			$hex_value = ATColors::get_color_value( $root_color, 'light' );
			if ( $hex_value ) {
				$output .= '<div class="bsg-swatch__hex">';
				$output .= '<span class="bsg-swatch__hex-label">' . esc_html__( 'Light:', 'bricks-style-guide' ) . '</span> ';
				$output .= '<code>' . esc_html( $hex_value ) . '</code>';

				$dark_value = ATColors::get_color_value( $root_color, 'dark' );
				if ( $dark_value && $dark_value !== $hex_value ) {
					$output .= ' <span class="bsg-swatch__hex-label">' . esc_html__( 'Dark:', 'bricks-style-guide' ) . '</span> ';
					$output .= '<code>' . esc_html( $dark_value ) . '</code>';
				}

				$output .= '</div>';
			}
		}

		$output .= '</div>'; // .bsg-swatch

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Enqueue element styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		$handle = 'bsg-color-swatch';

		// Only register and add inline styles once.
		if ( ! wp_style_is( $handle, 'registered' ) ) {
			wp_register_style( $handle, false, [], BRICKS_SG_VERSION );
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
		// Get framework-agnostic CSS variables that map to the active framework.
		$framework_vars = FrameworkVariables::get_css_variables();

		return '
			/* Framework Variable Mappings */
			.bsg-swatch {
				' . $framework_vars . '
			}

			/* Critical layout */
			.bsg-swatch {
				display: flex;
				flex-direction: column;
				gap: var(--bsg-space-s, 1rem);
			}

			.bsg-swatch__header {
				display: flex;
				flex-direction: column;
				gap: var(--bsg-space-3xs, 0.25rem);
			}

			.bsg-swatch__row {
				display: flex;
				flex-wrap: wrap;
				align-items: center;
				gap: var(--bsg-space-2xs, 0.5rem);
			}

			.bsg-swatch--vertical .bsg-swatch__row {
				flex-direction: column;
			}

			.bsg-swatch__item {
				position: relative;
				display: flex;
				align-items: flex-end;
				justify-content: center;
			}

			@layer bsg {
			.bsg-swatch__placeholder {
				padding: var(--bsg-space-l, 2rem);
				background: var(--bsg-neutral-light, #f3f4f6);
				border: var(--bsg-border-width, 0.125em) dashed var(--bsg-border-color, #d1d5db);
				border-radius: var(--bsg-radius-s, 0.5rem);
				text-align: center;
				color: var(--bsg-neutral-medium, #6b7280);
			}

			.bsg-swatch__title {
				margin: 0;
				font-size: var(--bsg-text-m, 1.125rem);
				font-weight: 600;
			}

			.bsg-swatch__variable {
				font-size: var(--bsg-text-2xs, 0.75rem);
				color: var(--bsg-neutral-medium, #6b7280);
				background: var(--bsg-neutral-light, #f3f4f6);
				padding: var(--bsg-space-3xs, 0.25rem) var(--bsg-space-xs, 0.5rem);
				border-radius: var(--bsg-radius-xs, 0.25rem);
				width: fit-content;
			}

			.bsg-swatch__item {
				width: var(--bsg-space-xl, 6rem);
				height: var(--bsg-space-xl, 6rem);
				border-radius: var(--bsg-radius-s, 0.5rem);
				border: 1px solid var(--bsg-border-color, #d1d5db);
				box-shadow: var(--bsg-shadow-m, 0 1px 3px var(--bsg-shadow-subtle, #e5e7eb));
				transition: transform 0.2s ease, box-shadow 0.2s ease;
			}

			.bsg-swatch__item:hover {
				box-shadow: var(--bsg-shadow-l, 0 4px 6px var(--bsg-shadow-subtle, #e5e7eb));
			}

			.bsg-swatch__item--base {
				box-shadow: var(--bsg-shadow-m, 0 2px 4px var(--bsg-shadow-subtle, #e5e7eb));
			}

			.bsg-swatch__shade-label {
				font-size: var(--bsg-text-2xs, 0.625rem);
				font-weight: 500;
				color: var(--bsg-white, #ffffff);
				text-shadow: 0 1px 2px var(--bsg-black, #000000);
				padding: var(--bsg-space-3xs, 0.125rem) var(--bsg-space-3xs, 0.25rem);
				background: var(--bsg-neutral-darker, #1f2937);
				border-radius: var(--bsg-radius-xs, 0.125rem);
				margin-bottom: var(--bsg-space-3xs, 0.25rem);
			}

			.bsg-swatch__hex {
				font-size: var(--bsg-text-2xs, 0.75rem);
				color: var(--bsg-neutral-medium, #6b7280);
			}

			.bsg-swatch__hex code {
				background: var(--bsg-neutral-light, #f3f4f6);
				padding: var(--bsg-space-3xs, 0.125rem) var(--bsg-space-2xs, 0.375rem);
				border-radius: var(--bsg-radius-xs, 0.25rem);
			}

			.bsg-swatch__hex-label {
				font-weight: 500;
			}
			} /* end @layer bsg */
		';
	}
}
