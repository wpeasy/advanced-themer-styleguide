<?php
/**
 * Typography Spread Element for Bricks Builder.
 *
 * Displays a rich text sample with all heading levels, lists, and blockquotes.
 * Includes optional read more functionality with smooth expand/collapse.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Elements;

use AB\BricksSG\ATFrameworkDefaults;
use AB\BricksSG\Framework\FrameworkDetector;
use AB\BricksSG\Framework\FrameworkVariables;

defined( 'ABSPATH' ) || exit;

/**
 * Typography Spread Element.
 */
class TypographySpread extends \Bricks\Element {

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
	public $name = 'bsg-typography-spread';

	/**
	 * Element icon.
	 *
	 * @var string
	 */
	public $icon = 'ti-text';

	/**
	 * Element scripts.
	 *
	 * @var array
	 */
	public $scripts = [ 'bsgTypographySpreadInit' ];

	/**
	 * Get element label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Typography Spread', 'bricks-style-guide' );
	}

	/**
	 * Get element keywords.
	 *
	 * @return array
	 */
	public function get_keywords(): array {
		return [ 'typography', 'text', 'heading', 'sample', 'lorem', 'ipsum', 'style guide' ];
	}

	/**
	 * Set control groups.
	 *
	 * @return void
	 */
	public function set_control_groups(): void {
		$this->control_groups['content'] = [
			'title' => esc_html__( 'Content', 'bricks-style-guide' ),
			'tab'   => 'content',
		];

		$this->control_groups['readMore'] = [
			'title' => esc_html__( 'Read More', 'bricks-style-guide' ),
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
		$this->controls['hideH1'] = [
			'group'       => 'content',
			'label'       => esc_html__( 'Hide H1', 'bricks-style-guide' ),
			'type'        => 'checkbox',
			'rerender'    => true,
			'description' => esc_html__( 'Hide the main H1 heading at the top.', 'bricks-style-guide' ),
		];

		$this->controls['h1Text'] = [
			'group'       => 'content',
			'label'       => esc_html__( 'H1 Text', 'bricks-style-guide' ),
			'type'        => 'text',
			'default'     => 'I am a H1 Heading',
			'placeholder' => esc_html__( 'Enter H1 text...', 'bricks-style-guide' ),
			'required'    => [ 'hideH1', '=', '' ],
		];

		// Read More controls.
		$this->controls['enableReadMore'] = [
			'group'    => 'readMore',
			'label'    => esc_html__( 'Enable Read More', 'bricks-style-guide' ),
			'type'     => 'checkbox',
			'rerender' => true,
		];

		$this->controls['collapsedHeight'] = [
			'group'       => 'readMore',
			'label'       => esc_html__( 'Collapsed Height', 'bricks-style-guide' ),
			'type'        => 'number',
			'units'       => true,
			'default'     => '300px',
			'placeholder' => '300px',
			'required'    => [ 'enableReadMore', '!=', '' ],
		];

		$this->controls['readMoreText'] = [
			'group'       => 'readMore',
			'label'       => esc_html__( 'Read More Text', 'bricks-style-guide' ),
			'type'        => 'text',
			'default'     => 'Read More',
			'placeholder' => esc_html__( 'Read More', 'bricks-style-guide' ),
			'required'    => [ 'enableReadMore', '!=', '' ],
		];

		$this->controls['readLessText'] = [
			'group'       => 'readMore',
			'label'       => esc_html__( 'Read Less Text', 'bricks-style-guide' ),
			'type'        => 'text',
			'default'     => 'Read Less',
			'placeholder' => esc_html__( 'Read Less', 'bricks-style-guide' ),
			'required'    => [ 'enableReadMore', '!=', '' ],
		];
	}

	/**
	 * Render element.
	 *
	 * @return void
	 */
	public function render(): void {
		$settings = $this->settings;

		$hide_h1          = isset( $settings['hideH1'] );
		$h1_text          = $settings['h1Text'] ?? 'I am a H1 Heading';
		$enable_read_more = isset( $settings['enableReadMore'] );
		$collapsed_height = $settings['collapsedHeight'] ?? '300px';
		$read_more_text   = $settings['readMoreText'] ?? 'Read More';
		$read_less_text   = $settings['readLessText'] ?? 'Read Less';

		$root_classes = [ 'bsg-typography-spread' ];
		if ( $enable_read_more ) {
			$root_classes[] = 'bsg-typography-spread--collapsible';
			$root_classes[] = 'bsg-typography-spread--collapsed';
		}

		$this->set_attribute( '_root', 'class', $root_classes );

		if ( $enable_read_more ) {
			$this->set_attribute( '_root', 'data-collapsed-height', esc_attr( $collapsed_height ) );
			$this->set_attribute( '_root', 'data-read-more-text', esc_attr( $read_more_text ) );
			$this->set_attribute( '_root', 'data-read-less-text', esc_attr( $read_less_text ) );
		}

		$output = "<div {$this->render_attributes( '_root' )}>";

		// H1 Heading (separate from content).
		if ( ! $hide_h1 ) {
			$output .= '<h1 class="bsg-typography-spread__h1">' . esc_html( $h1_text ) . '</h1>';
		}

		// Content wrapper.
		$output .= '<div class="bsg-typography-spread__content">';
		$output .= '<div class="bsg-typography-spread__inner">';

		// H2 Section.
		$output .= '<h2>This is a H2 Heading</h2>';
		$output .= '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>';

		// H3 Section.
		$output .= '<h3>This is a H3 Heading</h3>';
		$output .= '<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>';

		// Unordered List.
		$output .= '<h4>Unordered List Example</h4>';
		$output .= '<ul>';
		$output .= '<li>First item in the unordered list</li>';
		$output .= '<li>Second item with more detailed content</li>';
		$output .= '<li>Third item demonstrating list styling</li>';
		$output .= '<li>Fourth item to show proper spacing</li>';
		$output .= '</ul>';

		// Ordered List.
		$output .= '<h4>Ordered List Example</h4>';
		$output .= '<ol>';
		$output .= '<li>First step in the ordered process</li>';
		$output .= '<li>Second step with additional details</li>';
		$output .= '<li>Third step demonstrating numbered lists</li>';
		$output .= '<li>Fourth step to complete the example</li>';
		$output .= '</ol>';

		// Another paragraph.
		$output .= '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>';

		// Blockquote.
		$output .= '<blockquote>';
		$output .= '<p>"Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt."</p>';
		$output .= '</blockquote>';

		// Final paragraph.
		$output .= '<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem.</p>';

		$output .= '</div>'; // .bsg-typography-spread__inner
		$output .= '</div>'; // .bsg-typography-spread__content

		// Read More button.
		if ( $enable_read_more ) {
			$output .= '<div class="bsg-typography-spread__read-more-wrapper">';
			$output .= '<button type="button" class="bsg-typography-spread__read-more-btn">';
			$output .= '<span class="bsg-typography-spread__btn-text">' . esc_html( $read_more_text ) . '</span>';
			$output .= '</button>';
			$output .= '</div>';
		}

		$output .= '</div>';

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Enqueue element styles.
	 *
	 * @return void
	 */
	public function enqueue_scripts(): void {
		$handle = 'bsg-typography-spread';

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
			.bsg-typography-spread {
				' . $framework_vars . '
			}

			/* Base styles */
			.bsg-typography-spread {
				position: relative;
			}

			.bsg-typography-spread__content {
				position: relative;
				overflow: hidden;
				transition: max-height 0.5s ease-in-out;
			}

			.bsg-typography-spread__inner {
				padding-bottom: 1em;
			}

			/* Collapsible state */
			.bsg-typography-spread--collapsible .bsg-typography-spread__content {
				/* Height set via JS */
			}

			/* Collapsed state - fade mask */
			.bsg-typography-spread--collapsed .bsg-typography-spread__content {
				-webkit-mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
				mask-image: linear-gradient(to bottom, black 50%, transparent 100%);
			}

			/* Expanded state - no mask */
			.bsg-typography-spread--expanded .bsg-typography-spread__content {
				-webkit-mask-image: none;
				mask-image: none;
			}

			/* Read More wrapper */
			.bsg-typography-spread__read-more-wrapper {
				display: flex;
				justify-content: flex-end;
				margin-top: 0.5em;
			}

			/* Read More button */
			.bsg-typography-spread__read-more-btn {
				background: var(--bsg-primary, #3b82f6);
				color: var(--bsg-white, #ffffff);
				border: none;
				padding: 0.5em 1em;
				font-size: 0.875em;
				font-weight: 500;
				border-radius: var(--bsg-radius-s, 0.25em);
				cursor: pointer;
				transition: background-color 0.2s ease, transform 0.2s ease;
			}

			.bsg-typography-spread__read-more-btn:hover {
				background: var(--bsg-primary-dark, #2563eb);
				transform: translateY(-1px);
			}

			.bsg-typography-spread__read-more-btn:active {
				transform: translateY(0);
			}
		';
	}
}
