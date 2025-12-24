<?php
/**
 * Automatic CSS Framework Provider.
 *
 * Provides CSS variables and colors from Automatic CSS (ACSS) framework.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Automatic CSS Framework Provider class.
 */
class ACSSFrameworkProvider implements FrameworkProviderInterface {

	/**
	 * Cache for ACSS settings.
	 *
	 * @var array|null
	 */
	private static ?array $settings = null;

	/**
	 * ACSS color keys (standard color names used by ACSS).
	 */
	public const COLOR_KEYS = [
		'primary',
		'secondary',
		'tertiary',
		'accent',
		'base',
		'neutral',
	];

	/**
	 * Light shade suffixes used by ACSS.
	 * Order: lightest to darkest.
	 * Note: ACSS provides both 'light' and descriptive names (ultra-light, semi-light).
	 */
	public const LIGHT_SHADES = [ 'ultra-light', 'light', 'semi-light' ];

	/**
	 * Dark shade suffixes used by ACSS.
	 * Order: lightest (hover) to darkest (ultra-dark).
	 * Note: ACSS provides both 'dark' and descriptive names (semi-dark, ultra-dark).
	 */
	public const DARK_SHADES = [ 'hover', 'semi-dark', 'dark', 'ultra-dark' ];

	/**
	 * Transparency shade suffixes used by ACSS.
	 * ACSS provides trans-10 through trans-100 in 10% increments.
	 */
	public const TRANSPARENCY_SHADES = [
		'trans-10',
		'trans-20',
		'trans-30',
		'trans-40',
		'trans-50',
		'trans-60',
		'trans-70',
		'trans-80',
		'trans-90',
	];

	/**
	 * Check if Automatic CSS is active.
	 *
	 * Only returns true if ACSS plugin is actually active/loaded,
	 * not just installed or having settings in the database.
	 *
	 * @return bool True if ACSS is active.
	 */
	public static function is_active(): bool {
		// Check for ACSS constant (defined when plugin is loaded).
		if ( defined( 'ACSS_PLUGIN_FILE' ) ) {
			return true;
		}

		// Check for ACSS main class (loaded when plugin is active).
		if ( class_exists( 'Automatic_CSS\\Plugin' ) ) {
			return true;
		}

		// Note: We intentionally do NOT check for database settings here,
		// as those persist even when the plugin is deactivated.

		return false;
	}

	/**
	 * Get the framework name.
	 *
	 * @return string The framework name.
	 */
	public static function get_name(): string {
		return 'Automatic CSS';
	}

	/**
	 * Get the CSS variable prefix.
	 *
	 * ACSS uses no prefix for its variables.
	 *
	 * @return string Empty string (no prefix).
	 */
	public static function get_prefix(): string {
		return '';
	}

	/**
	 * Get ACSS settings from database or API.
	 *
	 * @return array ACSS settings.
	 */
	public static function get_settings(): array {
		if ( null !== self::$settings ) {
			return self::$settings;
		}

		// Try the ACSS API first if available.
		if ( class_exists( 'Automatic_CSS\\API' ) && method_exists( 'Automatic_CSS\\API', 'get_settings' ) ) {
			self::$settings = \Automatic_CSS\API::get_settings() ?? [];
			return self::$settings;
		}

		// Fallback to database option.
		self::$settings = get_option( 'automatic_css_settings', [] );
		return self::$settings;
	}

	/**
	 * Get spacing variable definitions.
	 *
	 * @return array Array of spacing variables.
	 */
	public static function get_spacing_variables(): array {
		// ACSS provides: xs, s, m, l, xl, xxl (no 3xs, 2xs, 2xl, 3xl).
		$sizes = [ 'xs', 's', 'm', 'l', 'xl', 'xxl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--space-' . $size,
			];
		}

		return $vars;
	}

	/**
	 * Get typography variable definitions.
	 *
	 * @return array Array of typography variables.
	 */
	public static function get_typography_variables(): array {
		// ACSS provides: xs, s, m, l, xl, xxl (no 3xs, 2xs, 2xl, 3xl).
		$sizes = [ 'xs', 's', 'm', 'l', 'xl', 'xxl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--text-' . $size,
			];
		}

		return $vars;
	}

	/**
	 * Get border radius variable definitions.
	 *
	 * @return array Array of radius variables.
	 */
	public static function get_radius_variables(): array {
		// ACSS provides: xs, s, m, l, xl, xxl plus special values (50, circle, none).
		$sizes = [ 'xs', 's', 'm', 'l', 'xl', 'xxl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--radius-' . $size,
			];
		}

		// Add special ACSS radius values.
		$vars[] = [
			'name'     => '50%',
			'variable' => '--radius-50',
		];
		$vars[] = [
			'name'     => 'circle',
			'variable' => '--radius-circle',
		];
		$vars[] = [
			'name'     => 'none',
			'variable' => '--radius-none',
		];

		return $vars;
	}

	/**
	 * Get box shadow variable definitions.
	 *
	 * @return array Array of shadow variables.
	 */
	public static function get_shadow_variables(): array {
		// ACSS uses --box-shadow- prefix with m, l, xl (and numbered 1, 2, 3).
		$sizes = [ 'm', 'l', 'xl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--box-shadow-' . $size,
			];
		}

		return $vars;
	}

	/**
	 * Get all available colors.
	 *
	 * @return array Array of colors.
	 */
	public static function get_colors(): array {
		$settings = self::get_settings();
		$colors   = [];

		foreach ( self::COLOR_KEYS as $color_key ) {
			// ACSS stores colors with 'color-' prefix in settings.
			$setting_key = 'color-' . $color_key;

			// Check if this color is configured in ACSS settings.
			$color_value = $settings[ $setting_key ] ?? '';

			// Even if no value in settings, the variable may exist from ACSS defaults.
			$colors[ $color_key ] = [
				'id'             => $color_key,
				'name'           => $color_key,
				'label'          => ucfirst( $color_key ),
				'palette_name'   => 'ACSS',
				'palette_prefix' => '',
				'raw'            => 'var(--' . $color_key . ')',
				'hex'            => $color_value ?: '',
				'rawValue'       => [],
				'shadeChildren'  => true, // ACSS always generates shades.
			];
		}

		return $colors;
	}

	/**
	 * Get color shades for a specific color.
	 *
	 * @param string $color_id The root color ID (e.g., 'primary').
	 * @return array Array of shade variations.
	 */
	public static function get_color_shades( string $color_id ): array {
		$shades = [];

		// Light shades (in reverse order so lightest is first).
		$light_reversed = array_reverse( self::LIGHT_SHADES );
		foreach ( $light_reversed as $shade ) {
			$var_name           = '--' . $color_id . '-' . $shade;
			$shades[ $shade ] = [
				'id'       => $color_id . '-' . $shade,
				'name'     => $color_id . '-' . $shade,
				'suffix'   => $shade,
				'raw'      => 'var(' . $var_name . ')',
				'hex'      => '',
				'rawValue' => [],
			];
		}

		// Base color.
		$shades['base'] = [
			'id'       => $color_id,
			'name'     => $color_id,
			'suffix'   => 'base',
			'raw'      => 'var(--' . $color_id . ')',
			'hex'      => '',
			'rawValue' => [],
		];

		// Dark shades.
		foreach ( self::DARK_SHADES as $shade ) {
			$var_name           = '--' . $color_id . '-' . $shade;
			$shades[ $shade ] = [
				'id'       => $color_id . '-' . $shade,
				'name'     => $color_id . '-' . $shade,
				'suffix'   => $shade,
				'raw'      => 'var(' . $var_name . ')',
				'hex'      => '',
				'rawValue' => [],
			];
		}

		return $shades;
	}

	/**
	 * Get light shade suffixes.
	 *
	 * @return array Light shade suffixes.
	 */
	public static function get_light_shades(): array {
		return self::LIGHT_SHADES;
	}

	/**
	 * Get dark shade suffixes.
	 *
	 * @return array Dark shade suffixes.
	 */
	public static function get_dark_shades(): array {
		return self::DARK_SHADES;
	}

	/**
	 * Get transparency shade suffixes.
	 *
	 * @return array Transparency shade suffixes.
	 */
	public static function get_transparency_shades(): array {
		return self::TRANSPARENCY_SHADES;
	}

	/**
	 * Check if ACSS variables exist.
	 *
	 * For ACSS, we check if the plugin is active and has settings configured.
	 *
	 * @return bool True if ACSS is active and configured.
	 */
	public static function has_variables(): bool {
		if ( ! self::is_active() ) {
			return false;
		}

		// ACSS generates variables by default, so if it's active, variables exist.
		return true;
	}

	/**
	 * Clear cached values.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$settings = null;
	}

	/**
	 * Get example variable strings for control defaults/placeholders.
	 *
	 * @return array Associative array with example variables.
	 */
	public static function get_example_variables(): array {
		return [
			// Typography (ACSS: xs, s, m, l, xl, xxl).
			'text_s'       => 'var(--text-s)',
			'text_m'       => 'var(--text-m)',
			'text_l'       => 'var(--text-l)',
			// Spacing (ACSS: xs, s, m, l, xl, xxl - no 3xs, 2xs, 2xl, 3xl).
			'space_3xs'    => 'var(--space-xs)', // Fallback - ACSS has no 3xs.
			'space_xs'     => 'var(--space-xs)',
			'space_s'      => 'var(--space-s)',
			'space_m'      => 'var(--space-m)',
			'space_l'      => 'var(--space-l)',
			'space_xl'     => 'var(--space-xl)',
			'space_3xl'    => 'var(--space-xxl)', // Fallback to xxl since ACSS has no 3xl.
			// Raw variable names (without var()).
			'space_m_raw'  => '--space-m',
			// Radius (ACSS: xs, s, m, l, xl, xxl).
			'radius_s'     => 'var(--radius-s)',
			'radius_m'     => 'var(--radius-m)',
			'radius_l'     => 'var(--radius-l)',
			'radius_m_raw' => '--radius-m',
			// Shadows (ACSS uses --box-shadow- prefix: m, l, xl).
			'shadow_m'     => 'var(--box-shadow-m)',
			'shadow_m_raw' => '--box-shadow-m',
		];
	}
}
