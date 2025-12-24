<?php
/**
 * Advanced Themer Framework Provider.
 *
 * Provides CSS variables and colors from Advanced Themer framework.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Framework;

use AB\BricksSG\ATColors;
use AB\BricksSG\ATVariables;

defined( 'ABSPATH' ) || exit;

/**
 * Advanced Themer Framework Provider class.
 */
class ATFrameworkProvider implements FrameworkProviderInterface {

	/**
	 * Cache for variables existence check.
	 *
	 * @var bool|null
	 */
	private static ?bool $has_variables = null;

	/**
	 * Check if Advanced Themer is active.
	 *
	 * @return bool True if AT is active.
	 */
	public static function is_active(): bool {
		// Check for AT constant.
		if ( defined( 'BRICKS_ADVANCED_THEMER_PATH' ) ) {
			return true;
		}

		// Fallback: Check for AT classes.
		if ( class_exists( 'AT__Global_Colors' ) || class_exists( 'AT__Admin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the framework name.
	 *
	 * @return string The framework name.
	 */
	public static function get_name(): string {
		return 'Advanced Themer';
	}

	/**
	 * Get the CSS variable prefix.
	 *
	 * @return string The prefix ('at-').
	 */
	public static function get_prefix(): string {
		return 'at-';
	}

	/**
	 * Get spacing variable definitions.
	 *
	 * @return array Array of spacing variables.
	 */
	public static function get_spacing_variables(): array {
		$sizes = [ '3xs', '2xs', 'xs', 's', 'm', 'l', 'xl', '2xl', '3xl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--at-space--' . $size,
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
		$sizes = [ '3xs', '2xs', 'xs', 's', 'm', 'l', 'xl', '2xl', '3xl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--at-text--' . $size,
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
		$sizes = [ 's', 'm', 'l', 'xl', 'full' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--at-radius--' . $size,
			];
		}

		return $vars;
	}

	/**
	 * Get box shadow variable definitions.
	 *
	 * @return array Array of shadow variables.
	 */
	public static function get_shadow_variables(): array {
		$sizes = [ 's', 'm', 'l', 'xl', '2xl' ];
		$vars  = [];

		foreach ( $sizes as $size ) {
			$vars[] = [
				'name'     => $size,
				'variable' => '--at-shadow--' . $size,
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
		return ATColors::get_root_colors();
	}

	/**
	 * Get color shades for a specific color.
	 *
	 * @param string $color_id The root color ID.
	 * @return array Array of shade variations.
	 */
	public static function get_color_shades( string $color_id ): array {
		return ATColors::get_color_variations( $color_id );
	}

	/**
	 * Get light shade suffixes.
	 *
	 * @return array Light shade suffixes.
	 */
	public static function get_light_shades(): array {
		return ATColors::LIGHT_SHADES;
	}

	/**
	 * Get dark shade suffixes.
	 *
	 * @return array Dark shade suffixes.
	 */
	public static function get_dark_shades(): array {
		return ATColors::DARK_SHADES;
	}

	/**
	 * Get transparency shade suffixes.
	 *
	 * @return array Transparency shade suffixes.
	 */
	public static function get_transparency_shades(): array {
		return ATColors::TRANSPARENCY_SHADES;
	}

	/**
	 * Check if AT variables exist.
	 *
	 * @return bool True if AT variables are found.
	 */
	public static function has_variables(): bool {
		if ( null !== self::$has_variables ) {
			return self::$has_variables;
		}

		$theme_variables = ATVariables::get_theme_variables();

		if ( empty( $theme_variables ) ) {
			self::$has_variables = false;
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
		self::$has_variables = count( $found_prefixes ) >= 2;

		return self::$has_variables;
	}

	/**
	 * Clear cached values.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$has_variables = null;
	}

	/**
	 * Get example variable strings for control defaults/placeholders.
	 *
	 * @return array Associative array with example variables.
	 */
	public static function get_example_variables(): array {
		return [
			// Typography.
			'text_s'       => 'var(--at-text--s)',
			'text_m'       => 'var(--at-text--m)',
			'text_l'       => 'var(--at-text--l)',
			// Spacing.
			'space_3xs'    => 'var(--at-space--3xs)',
			'space_xs'     => 'var(--at-space--xs)',
			'space_s'      => 'var(--at-space--s)',
			'space_m'      => 'var(--at-space--m)',
			'space_l'      => 'var(--at-space--l)',
			'space_xl'     => 'var(--at-space--xl)',
			'space_3xl'    => 'var(--at-space--3xl)',
			// Raw variable names (without var()).
			'space_m_raw'  => '--at-space--m',
			// Radius.
			'radius_s'     => 'var(--at-radius--s)',
			'radius_m'     => 'var(--at-radius--m)',
			'radius_l'     => 'var(--at-radius--l)',
			'radius_m_raw' => '--at-radius--m',
			// Shadows.
			'shadow_m'     => 'var(--at-shadow--m)',
			'shadow_m_raw' => '--at-shadow--m',
		];
	}
}
