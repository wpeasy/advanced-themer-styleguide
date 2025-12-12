<?php
/**
 * Main Plugin class.
 *
 * @package AB\ATStyleGuide
 */

namespace AB\ATStyleGuide;

use AB\ATStyleGuide\Admin\AdminMenu;

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
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/Typography.php',
				'name'  => 'at-typography',
				'class' => 'AB\\ATStyleGuide\\Elements\\Typography',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/TypographyItem.php',
				'name'  => 'at-typography-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\TypographyItem',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/Spacing.php',
				'name'  => 'at-spacing',
				'class' => 'AB\\ATStyleGuide\\Elements\\Spacing',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/SpacingItem.php',
				'name'  => 'at-spacing-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\SpacingItem',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/Radii.php',
				'name'  => 'at-radii',
				'class' => 'AB\\ATStyleGuide\\Elements\\Radii',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/RadiiItem.php',
				'name'  => 'at-radii-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\RadiiItem',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/BoxShadows.php',
				'name'  => 'at-box-shadows',
				'class' => 'AB\\ATStyleGuide\\Elements\\BoxShadows',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/BoxShadowsItem.php',
				'name'  => 'at-box-shadows-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\BoxShadowsItem',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/ColorSwatch.php',
				'name'  => 'at-color-swatch',
				'class' => 'AB\\ATStyleGuide\\Elements\\ColorSwatch',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/Buttons.php',
				'name'  => 'at-buttons',
				'class' => 'AB\\ATStyleGuide\\Elements\\Buttons',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/ButtonsItem.php',
				'name'  => 'at-buttons-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\ButtonsItem',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/Colors.php',
				'name'  => 'at-colors',
				'class' => 'AB\\ATStyleGuide\\Elements\\Colors',
			],
			[
				'file'  => AT_STYLE_GUIDE_PLUGIN_PATH . 'src/Elements/ColorsItem.php',
				'name'  => 'at-colors-item',
				'class' => 'AB\\ATStyleGuide\\Elements\\ColorsItem',
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
		$i18n['at style guide'] = esc_html__( 'AT Style Guide', 'advanced-themer-style-guide' );

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
			'at-style-guide-elements',
			AT_STYLE_GUIDE_PLUGIN_URL . 'assets/js/elements.js',
			[],
			AT_STYLE_GUIDE_VERSION,
			true
		);
		wp_enqueue_script( 'at-style-guide-elements' );

		// Enqueue Svelte assets if they exist.
		$dist_path = AT_STYLE_GUIDE_PLUGIN_PATH . 'assets/svelte/dist/';
		$dist_url  = AT_STYLE_GUIDE_PLUGIN_URL . 'assets/svelte/dist/';

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
