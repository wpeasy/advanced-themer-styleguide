<?php
/**
 * Framework Provider Interface.
 *
 * Contract for CSS framework providers (Advanced Themer, Automatic CSS, etc.)
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Interface for framework providers.
 */
interface FrameworkProviderInterface {

	/**
	 * Check if this framework is active/installed.
	 *
	 * @return bool True if the framework is active.
	 */
	public static function is_active(): bool;

	/**
	 * Get the framework name for display purposes.
	 *
	 * @return string The framework name.
	 */
	public static function get_name(): string;

	/**
	 * Get the CSS variable prefix used by this framework.
	 *
	 * @return string The prefix (e.g., 'at-' for AT, '' for ACSS).
	 */
	public static function get_prefix(): string;

	/**
	 * Get spacing variable definitions.
	 *
	 * @return array Array of spacing variables with name and CSS variable.
	 */
	public static function get_spacing_variables(): array;

	/**
	 * Get typography variable definitions.
	 *
	 * @return array Array of typography variables with name and CSS variable.
	 */
	public static function get_typography_variables(): array;

	/**
	 * Get border radius variable definitions.
	 *
	 * @return array Array of radius variables with name and CSS variable.
	 */
	public static function get_radius_variables(): array;

	/**
	 * Get box shadow variable definitions.
	 *
	 * @return array Array of shadow variables with name and CSS variable.
	 */
	public static function get_shadow_variables(): array;

	/**
	 * Get all available colors from this framework.
	 *
	 * @return array Array of colors with id, name, label, raw (CSS var), hex, etc.
	 */
	public static function get_colors(): array;

	/**
	 * Get color variations/shades for a specific color.
	 *
	 * @param string $color_id The root color identifier.
	 * @return array Array of shade variations for the color.
	 */
	public static function get_color_shades( string $color_id ): array;

	/**
	 * Get light shade suffixes used by this framework.
	 *
	 * @return array Array of light shade suffixes.
	 */
	public static function get_light_shades(): array;

	/**
	 * Get dark shade suffixes used by this framework.
	 *
	 * @return array Array of dark shade suffixes.
	 */
	public static function get_dark_shades(): array;

	/**
	 * Get transparency shade suffixes used by this framework.
	 *
	 * @return array Array of transparency shade suffixes.
	 */
	public static function get_transparency_shades(): array;

	/**
	 * Check if the framework has variables configured.
	 *
	 * @return bool True if framework variables exist.
	 */
	public static function has_variables(): bool;

	/**
	 * Get example variable strings for control defaults/placeholders.
	 *
	 * Returns framework-specific example variables for use in Bricks controls.
	 *
	 * @return array Associative array with keys like 'text_s', 'space_m', 'radius_m', 'shadow_m', etc.
	 */
	public static function get_example_variables(): array;
}
