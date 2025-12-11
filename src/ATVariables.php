<?php
/**
 * Advanced Themer Variables Helper.
 *
 * Provides access to CSS variables defined in Advanced Themer.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide;

defined( 'ABSPATH' ) || exit;

/**
 * AT Variables Helper class.
 */
class ATVariables {

	/**
	 * Get all CSS variables from Bricks Global Variables.
	 *
	 * Bricks stores global variables in the 'bricks_global_variables' option.
	 * These include variables imported via Advanced Themer.
	 *
	 * @return array Array of variables with 'name' and 'value' keys.
	 */
	public static function get_theme_variables(): array {
		$variables = [];

		// Primary method: Check Bricks Global Variables option.
		// This is where AT Framework variables are stored when imported.
		$global_variables = get_option( 'bricks_global_variables', [] );

		if ( ! empty( $global_variables ) && is_array( $global_variables ) ) {
			foreach ( $global_variables as $var ) {
				if ( is_array( $var ) && isset( $var['name'] ) ) {
					$variables[] = $var;
				}
			}
		}

		return $variables;
	}

	/**
	 * Get CSS variables filtered by prefix.
	 *
	 * @param string $prefix The prefix to filter by (e.g., 'at-space', 'at-radius').
	 * @return array Filtered array of variables.
	 */
	public static function get_variables_by_prefix( string $prefix ): array {
		$all_variables = self::get_theme_variables();
		$filtered      = [];

		foreach ( $all_variables as $variable ) {
			$name = $variable['name'] ?? '';
			if ( str_starts_with( $name, $prefix ) ) {
				$filtered[] = $variable;
			}
		}

		return $filtered;
	}

	/**
	 * Get spacing variables (--at-space--*).
	 *
	 * @return array Array of spacing variables.
	 */
	public static function get_spacing_variables(): array {
		return self::get_variables_by_prefix( 'at-space' );
	}

	/**
	 * Get radius variables (--at-radius--*).
	 *
	 * @return array Array of radius variables.
	 */
	public static function get_radius_variables(): array {
		return self::get_variables_by_prefix( 'at-radius' );
	}

	/**
	 * Get shadow variables (--at-shadow--*).
	 *
	 * @return array Array of shadow variables.
	 */
	public static function get_shadow_variables(): array {
		return self::get_variables_by_prefix( 'at-shadow' );
	}

	/**
	 * Get typography variables (--at-font--*, --at-text--*).
	 *
	 * @return array Array of typography variables.
	 */
	public static function get_typography_variables(): array {
		$font_vars = self::get_variables_by_prefix( 'at-font' );
		$text_vars = self::get_variables_by_prefix( 'at-text' );
		return array_merge( $font_vars, $text_vars );
	}

	/**
	 * Format variables for Bricks select control.
	 *
	 * @param array $variables Array of variables.
	 * @return array Associative array of variable_name => label.
	 */
	public static function format_for_select( array $variables ): array {
		$options = [];

		foreach ( $variables as $variable ) {
			$name  = $variable['name'] ?? '';
			$value = $variable['value'] ?? '';

			if ( empty( $name ) ) {
				continue;
			}

			// Create readable label from variable name.
			$label = str_replace( [ 'at-', '--' ], [ '', ' ' ], $name );
			$label = ucwords( trim( $label ) );

			$options[ '--' . $name ] = $label;
		}

		return $options;
	}

	/**
	 * Format variables for repeater default values.
	 *
	 * @param array $variables Array of variables.
	 * @return array Array formatted for Bricks repeater defaults.
	 */
	public static function format_for_repeater( array $variables ): array {
		$items = [];

		foreach ( $variables as $variable ) {
			$name = $variable['name'] ?? '';

			if ( empty( $name ) ) {
				continue;
			}

			// Create readable label from variable name.
			$label = self::get_label_from_name( $name );

			$items[] = [
				'name'  => '--' . $name,
				'label' => $label,
			];
		}

		return $items;
	}

	/**
	 * Get a readable label from a variable name.
	 *
	 * @param string $name The variable name (without --).
	 * @return string The formatted label.
	 */
	public static function get_label_from_name( string $name ): string {
		// Remove common prefixes.
		$label = preg_replace( '/^at-(space|radius|shadow|font|text)--/', '', $name );

		// Convert remaining dashes to spaces and uppercase.
		$label = str_replace( [ '-', '_' ], ' ', $label );
		$label = ucwords( trim( $label ) );

		return $label;
	}

	/**
	 * Get all variables grouped by type.
	 *
	 * @return array Associative array with 'spacing', 'radius', 'shadow', 'typography' keys.
	 */
	public static function get_all_grouped(): array {
		return [
			'spacing'    => self::get_spacing_variables(),
			'radius'     => self::get_radius_variables(),
			'shadow'     => self::get_shadow_variables(),
			'typography' => self::get_typography_variables(),
		];
	}
}
