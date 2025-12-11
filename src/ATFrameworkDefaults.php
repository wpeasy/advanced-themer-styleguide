<?php
/**
 * AT Framework Defaults.
 *
 * Detects whether AT Framework variables are present in Bricks Theme Styles.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide;

defined( 'ABSPATH' ) || exit;

/**
 * AT Framework Defaults class.
 */
class ATFrameworkDefaults {

	/**
	 * Cache for AT variables existence check.
	 *
	 * @var bool|null
	 */
	private static ?bool $has_at_variables = null;

	/**
	 * Check if AT Framework variables exist in Bricks Theme Styles.
	 *
	 * Looks for variables with --at- prefix in spacing, typography, radius categories.
	 *
	 * @return bool True if AT variables are found.
	 */
	public static function has_at_variables(): bool {
		if ( null !== self::$has_at_variables ) {
			return self::$has_at_variables;
		}

		$theme_variables = ATVariables::get_theme_variables();

		if ( empty( $theme_variables ) ) {
			self::$has_at_variables = false;
			return false;
		}

		// Check for presence of core AT variables.
		$required_prefixes = [ 'at-space--', 'at-text--', 'at-radius--' ];
		$found_prefixes    = [];

		foreach ( $theme_variables as $variable ) {
			$name = $variable['name'] ?? '';
			foreach ( $required_prefixes as $prefix ) {
				if ( str_starts_with( $name, $prefix ) ) {
					$found_prefixes[ $prefix ] = true;
				}
			}
		}

		// Consider AT variables present if at least 2 of 3 core prefixes exist.
		self::$has_at_variables = count( $found_prefixes ) >= 2;

		return self::$has_at_variables;
	}

	/**
	 * Render a warning message when ATF is not detected.
	 *
	 * @param string $element_name Optional element name for context.
	 * @return string HTML warning message.
	 */
	public static function render_warning( string $element_name = '' ): string {
		$message = esc_html__( 'AT Framework not detected. This Element is for displaying ATF Styles.', 'advanced-themer-style-guide' );

		$html  = '<div class="atsg-warning" style="padding: var(--at-space--m, 20px); background: #fff3cd; border: 1px solid #ffc107; border-radius: var(--at-radius--s, 4px); color: #856404; text-align: center;">';
		$html .= '<p style="margin: 0; font-size: var(--at-text--s, 14px);">';
		$html .= '<strong>' . esc_html__( 'Warning:', 'advanced-themer-style-guide' ) . '</strong> ';
		$html .= $message;
		$html .= '</p>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Clear cached values.
	 *
	 * Call this if theme styles are updated.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$has_at_variables = null;
	}
}
