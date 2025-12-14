<?php
/**
 * AT Framework Defaults.
 *
 * Detects whether a supported CSS framework is active and provides access to the active provider.
 *
 * @package AB\BricksSG
 */

namespace AB\BricksSG;

use AB\BricksSG\Framework\FrameworkDetector;
use AB\BricksSG\Framework\FrameworkProviderInterface;

defined( 'ABSPATH' ) || exit;

/**
 * AT Framework Defaults class.
 *
 * This class now acts as a facade for the Framework system, providing
 * backward-compatible methods while delegating to the new provider architecture.
 */
class ATFrameworkDefaults {

	/**
	 * Cache for AT variables existence check.
	 *
	 * @var bool|null
	 */
	private static ?bool $has_at_variables = null;

	/**
	 * Get the active framework provider.
	 *
	 * @return FrameworkProviderInterface|null The active provider or null.
	 */
	public static function get_active_provider(): ?FrameworkProviderInterface {
		return FrameworkDetector::get_active_framework();
	}

	/**
	 * Get the name of the active framework.
	 *
	 * @return string Framework name (e.g., 'Advanced Themer', 'Automatic CSS') or empty string.
	 */
	public static function get_active_framework_name(): string {
		return FrameworkDetector::get_active_framework_name();
	}

	/**
	 * Check if any supported framework has variables configured.
	 *
	 * @return bool True if framework variables are found.
	 */
	public static function has_framework_variables(): bool {
		$provider = self::get_active_provider();
		return $provider && $provider::has_variables();
	}

	/**
	 * Check if AT Framework variables exist in Bricks Theme Styles.
	 *
	 * @deprecated Use has_framework_variables() for multi-framework support.
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
	 * Render a warning message when no framework is detected.
	 *
	 * @param string $element_name Optional element name for context.
	 * @return string HTML warning message.
	 */
	public static function render_warning( string $element_name = '' ): string {
		$message = esc_html__( 'No supported CSS framework detected. This Element requires Advanced Themer or Automatic CSS.', 'bricks-style-guide' );

		$html  = '<div class="bsg-warning" style="padding: 20px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; text-align: center;">';
		$html .= '<p style="margin: 0; font-size: 14px;">';
		$html .= '<strong>' . esc_html__( 'Warning:', 'bricks-style-guide' ) . '</strong> ';
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
		FrameworkDetector::clear_cache();
	}
}
