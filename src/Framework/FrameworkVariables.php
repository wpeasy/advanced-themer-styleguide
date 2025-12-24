<?php
/**
 * Framework Variables Helper.
 *
 * Provides CSS variable names mapped to the active framework.
 * This ensures the Style Guide uses the correct variable naming
 * convention for Advanced Themer or Automatic CSS.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Framework;

defined( 'ABSPATH' ) || exit;

/**
 * Framework Variables Helper class.
 */
class FrameworkVariables {

	/**
	 * Cached CSS variables string.
	 *
	 * @var string|null
	 */
	private static ?string $css_variables = null;

	/**
	 * Get the CSS variable for a given token.
	 *
	 * @param string $category The variable category (space, text, radius, shadow, color).
	 * @param string $name     The variable name/size (e.g., 'xl', 'm', 'primary').
	 * @param string $fallback Optional fallback value.
	 * @return string The CSS var() reference.
	 */
	public static function get( string $category, string $name, string $fallback = '' ): string {
		$var_name = self::get_variable_name( $category, $name );

		if ( $fallback ) {
			return "var({$var_name}, {$fallback})";
		}

		return "var({$var_name})";
	}

	/**
	 * Get just the CSS variable name (without var() wrapper).
	 *
	 * @param string $category The variable category.
	 * @param string $name     The variable name/size.
	 * @return string The CSS variable name (e.g., '--space-xl' or '--at-space--xl').
	 */
	public static function get_variable_name( string $category, string $name ): string {
		// Use the detected framework (respects priority: AT > ACSS).
		$framework = FrameworkDetector::detect();
		$is_acss   = $framework instanceof ACSSFrameworkProvider;

		// Map category and name to the correct variable format.
		switch ( $category ) {
			case 'space':
				return $is_acss ? "--space-{$name}" : "--at-space--{$name}";

			case 'text':
				return $is_acss ? "--text-{$name}" : "--at-text--{$name}";

			case 'radius':
				return $is_acss ? "--radius-{$name}" : "--at-radius--{$name}";

			case 'shadow':
				// ACSS: --box-shadow-m, AT: --at-shadow--m
				return $is_acss ? "--box-shadow-{$name}" : "--at-shadow--{$name}";

			case 'color':
				// For named colors like 'primary', 'neutral', etc.
				return $is_acss ? "--{$name}" : "--at-{$name}";

			case 'neutral':
				// Neutral shades: neutral-d-2 or neutral-semi-dark
				return $is_acss ? "--neutral-{$name}" : "--at-neutral-{$name}";

			case 'primary':
				return $is_acss ? "--primary-{$name}" : "--at-primary-{$name}";

			default:
				// Generic fallback - assume it's a direct variable name.
				return $is_acss ? "--{$name}" : "--at-{$name}";
		}
	}

	/**
	 * Get CSS custom properties block for use in inline styles.
	 *
	 * This generates a set of --bsg-* variables that map to the active framework's variables.
	 * Elements can then use --bsg-* variables and they'll resolve to the correct framework.
	 *
	 * @return string CSS custom properties block.
	 */
	public static function get_css_variables(): string {
		if ( null !== self::$css_variables ) {
			return self::$css_variables;
		}

		// Use the detected framework (respects priority: AT > ACSS).
		$framework = FrameworkDetector::detect();
		$is_acss   = $framework instanceof ACSSFrameworkProvider;

		if ( $is_acss ) {
			self::$css_variables = self::get_acss_variables();
		} else {
			self::$css_variables = self::get_at_variables();
		}

		return self::$css_variables;
	}

	/**
	 * Get ACSS variable mappings.
	 *
	 * @return string CSS custom properties.
	 */
	private static function get_acss_variables(): string {
		return '
			/* BSG Framework Variables - ACSS */
			/* ACSS provides: xs, s, m, l, xl, xxl (no 3xs, 2xs, 2xl, 3xl) */
			--bsg-space-3xs: var(--space-xs, 0.25rem);
			--bsg-space-2xs: var(--space-xs, 0.5rem);
			--bsg-space-xs: var(--space-xs, 0.75rem);
			--bsg-space-s: var(--space-s, 1rem);
			--bsg-space-m: var(--space-m, 1.5rem);
			--bsg-space-l: var(--space-l, 2rem);
			--bsg-space-xl: var(--space-xl, 3rem);
			--bsg-space-2xl: var(--space-xxl, 4rem);
			--bsg-space-3xl: var(--space-xxl, 6rem);

			/* ACSS text: xs, s, m, l, xl, xxl */
			--bsg-text-2xs: var(--text-xs, 0.75rem);
			--bsg-text-xs: var(--text-xs, 0.875rem);
			--bsg-text-s: var(--text-s, 1rem);
			--bsg-text-m: var(--text-m, 1.125rem);
			--bsg-text-l: var(--text-l, 1.25rem);

			/* ACSS radius: xs, s, m, l, xl, xxl */
			--bsg-radius-xs: var(--radius-xs, 2px);
			--bsg-radius-s: var(--radius-s, 4px);
			--bsg-radius-m: var(--radius-m, 5px);
			--bsg-radius-l: var(--radius-l, 8px);
			--bsg-radius-xl: var(--radius-xl, 12px);
			--bsg-radius-xxl: var(--radius-xxl, 16px);

			/* ACSS uses --box-shadow- prefix: m, l, xl */
			--bsg-shadow-m: var(--box-shadow-m, 0 0 40px rgba(0, 0, 0, 0.1));
			--bsg-shadow-l: var(--box-shadow-l, 0 0 60px rgba(0, 0, 0, 0.2));
			--bsg-shadow-xl: var(--box-shadow-xl, 0 0 80px rgba(0, 0, 0, 0.3));

			--bsg-primary: var(--primary, #3b82f6);
			--bsg-primary-hover: var(--primary-hover, #2563eb);
			--bsg-primary-light: var(--primary-light, #dbeafe);
			--bsg-primary-dark: var(--primary-dark, #1d4ed8);
			--bsg-primary-trans-4: var(--primary-trans, rgba(59, 130, 246, 0.4));
			--bsg-secondary: var(--secondary, #8b5cf6);
			--bsg-white: var(--white, #ffffff);
			--bsg-black: var(--black, #000000);

			--bsg-neutral-light: var(--neutral-light, #f3f4f6);
			--bsg-neutral-medium: var(--neutral, #9ca3af);
			--bsg-neutral-dark: var(--neutral-dark, #374151);
			--bsg-neutral-darker: var(--neutral-ultra-dark, #1f2937);

			--bsg-success: var(--success, #10b981);
			--bsg-warning: var(--warning, #f59e0b);
			--bsg-error: var(--danger, #ef4444);

			--bsg-border-color: var(--neutral-light, #d1d5db);
			--bsg-border-width: 2px;

			--bsg-focus-ring-color: var(--primary-trans, var(--primary, #3b82f6));
			--bsg-shadow-subtle: var(--neutral-ultra-light, #e5e7eb);
		';
	}

	/**
	 * Get AT variable mappings.
	 *
	 * @return string CSS custom properties.
	 */
	private static function get_at_variables(): string {
		return '
			/* BSG Framework Variables - Advanced Themer */
			--bsg-space-3xs: var(--at-space--3xs, 0.25rem);
			--bsg-space-2xs: var(--at-space--2xs, 0.5rem);
			--bsg-space-xs: var(--at-space--xs, 0.75rem);
			--bsg-space-s: var(--at-space--s, 1rem);
			--bsg-space-m: var(--at-space--m, 1.5rem);
			--bsg-space-l: var(--at-space--l, 2rem);
			--bsg-space-xl: var(--at-space--xl, 3rem);
			--bsg-space-2xl: var(--at-space--2xl, 4rem);
			--bsg-space-3xl: var(--at-space--3xl, 6rem);

			--bsg-text-2xs: var(--at-text--2xs, 0.75rem);
			--bsg-text-xs: var(--at-text--xs, 0.875rem);
			--bsg-text-s: var(--at-text--s, 1rem);
			--bsg-text-m: var(--at-text--m, 1.125rem);
			--bsg-text-l: var(--at-text--l, 1.25rem);

			--bsg-radius-xs: var(--at-radius--xs, 4px);
			--bsg-radius-s: var(--at-radius--s, 8px);
			--bsg-radius-m: var(--at-radius--m, 8px);
			--bsg-radius-l: var(--at-radius--l, 12px);

			--bsg-shadow-l: var(--at-shadow--l, 0 10px 25px rgba(0, 0, 0, 0.15));

			--bsg-primary: var(--at-primary, #3b82f6);
			--bsg-primary-hover: var(--at-primary-d-1, #2563eb);
			--bsg-primary-light: var(--at-primary-l-4, #dbeafe);
			--bsg-primary-dark: var(--at-primary-d-2, #1d4ed8);
			--bsg-primary-trans-4: var(--at-primary-t-4, rgba(59, 130, 246, 0.4));
			--bsg-white: var(--at-white, #ffffff);
			--bsg-black: var(--at-black, #000000);

			--bsg-neutral-light: var(--at-neutral-t-6, #f3f4f6);
			--bsg-neutral-medium: var(--at-neutral-d-1, #9ca3af);
			--bsg-neutral-dark: var(--at-neutral-d-3, #374151);
			--bsg-neutral-darker: var(--at-neutral-d-4, #1f2937);

			--bsg-success: var(--at-success, #10b981);
			--bsg-warning: var(--at-warning, #f59e0b);
			--bsg-error: var(--at-error, #ef4444);

			--bsg-border-color: var(--at-neutral-t-4, #d1d5db);
			--bsg-border-width: var(--at-border-width, 2px);

			--bsg-focus-ring-color: var(--at-primary-t-4, var(--at-primary, #3b82f6));
			--bsg-shadow-subtle: var(--at-neutral-t-2, #e5e7eb);
		';
	}

	/**
	 * Clear cached values.
	 *
	 * @return void
	 */
	public static function clear_cache(): void {
		self::$css_variables = null;
	}
}
