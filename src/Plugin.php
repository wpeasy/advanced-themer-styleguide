<?php
/**
 * Main Plugin class.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin main class.
 */
final class Plugin {

	/**
	 * Whether the plugin has been initialized.
	 *
	 * @var bool
	 */
	private static bool $initialized = false;

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( self::$initialized ) {
			return;
		}

		self::$initialized = true;

		// Register Bricks elements.
		add_action( 'init', [ __CLASS__, 'register_bricks_elements' ], 11 );

		// Enqueue assets.
		add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_frontend_assets' ] );
	}

	/**
	 * Register Bricks Builder elements.
	 *
	 * @return void
	 */
	public static function register_bricks_elements(): void {
		if ( ! class_exists( '\Bricks\Elements' ) ) {
			return;
		}

		$elements = [
			'color-swatch' => Elements\ColorSwatch::class,
		];

		foreach ( $elements as $name => $class ) {
			$file = AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/' . str_replace( '_', '', ucwords( str_replace( '-', '_', $name ), '_' ) ) . '.php';

			if ( class_exists( $class ) ) {
				\Bricks\Elements::register_element( $file, $name, $class );
			}
		}
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_assets(): void {
		if ( ! function_exists( 'bricks_is_builder_preview' ) ) {
			return;
		}

		$dist_path = AT_STYLE_GUIDE_PLUGIN_PATH . 'assets/svelte/dist/';
		$dist_url  = AT_STYLE_GUIDE_PLUGIN_URL . 'assets/svelte/dist/';

		// Enqueue main Svelte app if it exists.
		if ( file_exists( $dist_path . 'main.js' ) ) {
			wp_enqueue_script(
				'at-style-guide-svelte',
				$dist_url . 'main.js',
				[],
				AT_STYLE_GUIDE_VERSION,
				true
			);

			wp_localize_script(
				'at-style-guide-svelte',
				'atStyleGuideData',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'at_style_guide_nonce' ),
				]
			);
		}

		// Enqueue CSS if it exists.
		if ( file_exists( $dist_path . 'main.css' ) ) {
			wp_enqueue_style(
				'at-style-guide-styles',
				$dist_url . 'main.css',
				[],
				AT_STYLE_GUIDE_VERSION
			);
		}
	}
}
