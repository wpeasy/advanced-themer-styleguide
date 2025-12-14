<?php
/**
 * Main Plugin class.
 *
 * @package AB\BricksSG
 */

namespace AB\BricksSG;

use AB\BricksSG\Admin\AdminMenu;

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

		// Initialize admin menu.
		AdminMenu::init();

		// Register Bricks elements.
		add_action( 'init', [ __CLASS__, 'register_bricks_elements' ], 11 );

		// Add element category translations.
		add_filter( 'bricks/builder/i18n', [ __CLASS__, 'add_i18n_strings' ] );

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

		// Register elements with explicit file, name, and fully-qualified class name.
		$elements = [
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/Typography.php',
				'name'  => 'bsg-typography',
				'class' => 'AB\\BricksSG\\Elements\\Typography',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/TypographyItem.php',
				'name'  => 'bsg-typography-item',
				'class' => 'AB\\BricksSG\\Elements\\TypographyItem',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/Spacing.php',
				'name'  => 'bsg-spacing',
				'class' => 'AB\\BricksSG\\Elements\\Spacing',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/SpacingItem.php',
				'name'  => 'bsg-spacing-item',
				'class' => 'AB\\BricksSG\\Elements\\SpacingItem',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/Radii.php',
				'name'  => 'bsg-radii',
				'class' => 'AB\\BricksSG\\Elements\\Radii',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/RadiiItem.php',
				'name'  => 'bsg-radii-item',
				'class' => 'AB\\BricksSG\\Elements\\RadiiItem',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/BoxShadows.php',
				'name'  => 'bsg-box-shadows',
				'class' => 'AB\\BricksSG\\Elements\\BoxShadows',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/BoxShadowsItem.php',
				'name'  => 'bsg-box-shadows-item',
				'class' => 'AB\\BricksSG\\Elements\\BoxShadowsItem',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/ColorSwatch.php',
				'name'  => 'bsg-color-swatch',
				'class' => 'AB\\BricksSG\\Elements\\ColorSwatch',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/Buttons.php',
				'name'  => 'bsg-buttons',
				'class' => 'AB\\BricksSG\\Elements\\Buttons',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/ButtonsItem.php',
				'name'  => 'bsg-buttons-item',
				'class' => 'AB\\BricksSG\\Elements\\ButtonsItem',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/Colors.php',
				'name'  => 'bsg-colors',
				'class' => 'AB\\BricksSG\\Elements\\Colors',
			],
			[
				'file'  => BRICKS_SG_PLUGIN_PATH . 'src/Elements/ColorsItem.php',
				'name'  => 'bsg-colors-item',
				'class' => 'AB\\BricksSG\\Elements\\ColorsItem',
			],
		];

		foreach ( $elements as $element ) {
			\Bricks\Elements::register_element( $element['file'], $element['name'], $element['class'] );
		}
	}

	/**
	 * Add i18n strings for element categories.
	 *
	 * @param array $i18n Existing i18n strings.
	 * @return array Modified i18n strings.
	 */
	public static function add_i18n_strings( array $i18n ): array {
		// Add category label for our elements.
		$i18n['bricks style guide'] = esc_html__( 'Bricks Style Guide', 'bricks-style-guide' );

		return $i18n;
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public static function enqueue_frontend_assets(): void {
		// Register element scripts (Bricks will call these via $scripts property).
		wp_register_script(
			'bricks-style-guide-elements',
			BRICKS_SG_PLUGIN_URL . 'assets/js/elements.js',
			[],
			BRICKS_SG_VERSION,
			true
		);
		wp_enqueue_script( 'bricks-style-guide-elements' );

		// Enqueue Svelte assets if they exist.
		$dist_path = BRICKS_SG_PLUGIN_PATH . 'assets/svelte/dist/';
		$dist_url  = BRICKS_SG_PLUGIN_URL . 'assets/svelte/dist/';

		if ( file_exists( $dist_path . 'main.js' ) ) {
			wp_enqueue_script(
				'bricks-style-guide-svelte',
				$dist_url . 'main.js',
				[],
				BRICKS_SG_VERSION,
				true
			);

			wp_localize_script(
				'bricks-style-guide-svelte',
				'bricksStyleGuideData',
				[
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'bricks_style_guide_nonce' ),
				]
			);
		}

		if ( file_exists( $dist_path . 'main.css' ) ) {
			wp_enqueue_style(
				'bricks-style-guide-styles',
				$dist_url . 'main.css',
				[],
				BRICKS_SG_VERSION
			);
		}
	}
}
