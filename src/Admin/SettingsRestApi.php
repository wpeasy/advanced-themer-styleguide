<?php
/**
 * Settings REST API class.
 *
 * Provides REST API endpoints for persisting user framework settings.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Settings REST API class.
 */
final class SettingsRestApi {

	/**
	 * REST API namespace.
	 *
	 * @var string
	 */
	const REST_NAMESPACE = 'bricks-style-guide/v1';

	/**
	 * User meta key for settings.
	 *
	 * @var string
	 */
	const USER_META_KEY = 'bsg_framework_settings';

	/**
	 * Whether the class has been initialized.
	 *
	 * @var bool
	 */
	private static bool $initialized = false;

	/**
	 * Initialize the REST API.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( self::$initialized ) {
			return;
		}

		self::$initialized = true;

		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
	}

	/**
	 * Register REST API routes.
	 *
	 * @return void
	 */
	public static function register_routes(): void {
		register_rest_route(
			self::REST_NAMESPACE,
			'/settings',
			[
				[
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => [ __CLASS__, 'get_settings' ],
					'permission_callback' => [ __CLASS__, 'check_permission' ],
				],
				[
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => [ __CLASS__, 'save_settings' ],
					'permission_callback' => [ __CLASS__, 'check_permission' ],
				],
			]
		);
	}

	/**
	 * Check if the user has permission to access settings.
	 *
	 * @return bool|WP_Error
	 */
	public static function check_permission() {
		if ( ! is_user_logged_in() ) {
			return new \WP_Error(
				'rest_not_logged_in',
				__( 'You must be logged in to access settings.', 'bricks-style-guide' ),
				[ 'status' => 401 ]
			);
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You do not have permission to access settings.', 'bricks-style-guide' ),
				[ 'status' => 403 ]
			);
		}

		return true;
	}

	/**
	 * Get user settings.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 * @return \WP_REST_Response
	 */
	public static function get_settings( \WP_REST_Request $request ) {
		$user_id  = get_current_user_id();
		$settings = get_user_meta( $user_id, self::USER_META_KEY, true );

		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return new \WP_REST_Response( [], 200 );
		}

		return new \WP_REST_Response( $settings, 200 );
	}

	/**
	 * Save user settings.
	 *
	 * @param \WP_REST_Request $request The REST request.
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function save_settings( \WP_REST_Request $request ) {
		$user_id  = get_current_user_id();
		$settings = $request->get_json_params();

		if ( empty( $settings ) || ! is_array( $settings ) ) {
			return new \WP_Error(
				'rest_invalid_settings',
				__( 'Invalid settings data.', 'bricks-style-guide' ),
				[ 'status' => 400 ]
			);
		}

		// Sanitize settings.
		$sanitized = self::sanitize_settings( $settings );

		// Save to user meta (update_user_meta returns false if value is unchanged, not on error).
		update_user_meta( $user_id, self::USER_META_KEY, $sanitized );

		return new \WP_REST_Response(
			[
				'success'  => true,
				'settings' => $sanitized,
			],
			200
		);
	}

	/**
	 * Sanitize settings data.
	 *
	 * @param array $settings Raw settings data.
	 * @return array Sanitized settings.
	 */
	private static function sanitize_settings( array $settings ): array {
		$sanitized = [];

		// Boolean settings.
		$bool_keys = [ 'compact_mode' ];
		foreach ( $bool_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$sanitized[ $key ] = (bool) $settings[ $key ];
			}
		}

		// Numeric settings.
		$numeric_keys = [
			'compact_multiplier',
			'space_base',
			'space_scale',
			'font_base',
			'type_scale',
			'radius_base',
			'radius_scale',
		];
		foreach ( $numeric_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$sanitized[ $key ] = (float) $settings[ $key ];
			}
		}

		// String settings.
		$string_keys = [ 'theme_mode', 'app_max_width' ];
		foreach ( $string_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$sanitized[ $key ] = sanitize_text_field( $settings[ $key ] );
			}
		}

		// Color settings (hex values).
		$color_keys = [
			'primary_light',
			'primary_dark',
			'secondary_light',
			'secondary_dark',
			'neutral_light',
			'neutral_dark',
			'success_light',
			'success_dark',
			'warning_light',
			'warning_dark',
			'danger_light',
			'danger_dark',
			'info_light',
			'info_dark',
		];
		foreach ( $color_keys as $key ) {
			if ( isset( $settings[ $key ] ) ) {
				$sanitized[ $key ] = sanitize_hex_color( $settings[ $key ] ) ?: $settings[ $key ];
			}
		}

		return $sanitized;
	}
}
