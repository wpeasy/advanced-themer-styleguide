<?php
/**
 * Framework Detector.
 *
 * Detects which CSS framework is active and returns the appropriate provider.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Framework Detector class.
 *
 * Handles detection of CSS frameworks (Advanced Themer, Automatic CSS)
 * and returns the appropriate provider instance.
 */
class FrameworkDetector {

	/**
	 * Cached active framework provider.
	 *
	 * @var FrameworkProviderInterface|null|false False means not yet checked.
	 */
	private static $active_framework = false;

	/**
	 * Detect and return the active framework provider.
	 *
	 * Priority: Advanced Themer > Automatic CSS
	 *
	 * @return FrameworkProviderInterface|null The active provider or null if none found.
	 */
	public static function detect(): ?FrameworkProviderInterface {
		if ( false !== self::$active_framework ) {
			return self::$active_framework;
		}

		// Priority 1: Advanced Themer.
		if ( ATFrameworkProvider::is_active() ) {
			self::$active_framework = new ATFrameworkProvider();
			return self::$active_framework;
		}

		// Priority 2: Automatic CSS.
		if ( ACSSFrameworkProvider::is_active() ) {
			self::$active_framework = new ACSSFrameworkProvider();
			return self::$active_framework;
		}

		// No framework found.
		self::$active_framework = null;
		return null;
	}

	/**
	 * Get the active framework provider (alias for detect).
	 *
	 * @return FrameworkProviderInterface|null The active provider or null.
	 */
	public static function get_active_framework(): ?FrameworkProviderInterface {
		return self::detect();
	}

	/**
	 * Check if Advanced Themer is active.
	 *
	 * @return bool True if AT is active.
	 */
	public static function is_at_active(): bool {
		return ATFrameworkProvider::is_active();
	}

	/**
	 * Check if Automatic CSS is active.
	 *
	 * @return bool True if ACSS is active.
	 */
	public static function is_acss_active(): bool {
		return ACSSFrameworkProvider::is_active();
	}

	/**
	 * Check if any supported framework is active.
	 *
	 * @return bool True if any framework is detected.
	 */
	public static function has_framework(): bool {
		return null !== self::detect();
	}

	/**
	 * Get the name of the active framework.
	 *
	 * @return string Framework name or empty string if none.
	 */
	public static function get_active_framework_name(): string {
		$framework = self::detect();
		return $framework ? $framework::get_name() : '';
	}

	/**
	 * Clear the cached framework detection.
	 *
	 * Call this if framework state may have changed.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$active_framework = false;
	}

	/**
	 * Get all available framework providers.
	 *
	 * @return array<string, class-string<FrameworkProviderInterface>> Provider classes keyed by identifier.
	 */
	public static function get_available_providers(): array {
		return [
			'at'   => ATFrameworkProvider::class,
			'acss' => ACSSFrameworkProvider::class,
		];
	}

	/**
	 * Get example variables from the active framework.
	 *
	 * Convenience method for use in Bricks control defaults/placeholders.
	 *
	 * @return array Example variables array, or AT defaults if no framework active.
	 */
	public static function get_example_variables(): array {
		$framework = self::detect();
		if ( $framework ) {
			return $framework::get_example_variables();
		}

		// Fallback to AT-style variables if no framework detected.
		return ATFrameworkProvider::get_example_variables();
	}
}
