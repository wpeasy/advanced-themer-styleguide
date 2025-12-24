<?php
/**
 * Admin Menu class.
 *
 * Registers the WordPress admin menu for the Style Guide Instructions.
 *
 * @package AB\BricksSG
 */

declare(strict_types=1);

namespace AB\BricksSG\Admin;

use AB\BricksSG\Framework\FrameworkDetector;

defined( 'ABSPATH' ) || exit;

/**
 * Admin Menu class.
 */
final class AdminMenu {

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'bricks-style-guide';

	/**
	 * Whether the class has been initialized.
	 *
	 * @var bool
	 */
	private static bool $initialized = false;

	/**
	 * Initialize the admin menu.
	 *
	 * @return void
	 */
	public static function init(): void {
		if ( self::$initialized ) {
			return;
		}

		self::$initialized = true;

		add_action( 'admin_menu', [ __CLASS__, 'register_menu' ] );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_admin_assets' ] );
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'Bricks Style Guide', 'bricks-style-guide' ),
			__( 'Bricks Style Guide', 'bricks-style-guide' ),
			'manage_options',
			self::MENU_SLUG,
			[ __CLASS__, 'render_page' ],
			self::get_menu_icon(),
			81 // Position after Settings
		);
	}

	/**
	 * Get the SVG menu icon as base64 data URI.
	 *
	 * @return string
	 */
	private static function get_menu_icon(): string {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">'
			. '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>'
			. '<path d="M3 9h18"/>'
			. '<path d="M9 21V9"/>'
			. '<circle cx="16" cy="15" r="2"/>'
			. '<path d="M6 6h.01"/>'
			. '<path d="M9 6h.01"/>'
			. '<path d="M12 6h.01"/>'
			. '</svg>';

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public static function render_page(): void {
		?>
		<div class="wpea wpea-full" id="bricks-style-guide-admin">
			<div data-bricks-style-guide-instructions></div>
		</div>
		<?php
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_assets( string $hook ): void {
		// Only load on our admin page.
		if ( 'toplevel_page_' . self::MENU_SLUG !== $hook ) {
			return;
		}

		// Enqueue WPEA framework CSS.
		wp_enqueue_style(
			'wpea-framework',
			BRICKS_SG_PLUGIN_URL . 'lib/wpea/wpea-framework.css',
			[],
			BRICKS_SG_VERSION
		);

		// Enqueue WPEA WP resets.
		wp_enqueue_style(
			'wpea-wp-resets',
			BRICKS_SG_PLUGIN_URL . 'lib/wpea/wpea-wp-resets.css',
			[ 'wpea-framework' ],
			BRICKS_SG_VERSION
		);

		// Enqueue admin Svelte app if built.
		$dist_path = BRICKS_SG_PLUGIN_PATH . 'assets/admin/dist/';
		$dist_url  = BRICKS_SG_PLUGIN_URL . 'assets/admin/dist/';

		if ( file_exists( $dist_path . 'main.js' ) ) {
			wp_enqueue_script(
				'bricks-style-guide-admin',
				$dist_url . 'main.js',
				[],
				BRICKS_SG_VERSION,
				true
			);

			wp_localize_script(
				'bricks-style-guide-admin',
				'bricksStyleGuideAdminData',
				[
					'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
					'nonce'           => wp_create_nonce( 'bricks_style_guide_admin_nonce' ),
					'pluginUrl'       => BRICKS_SG_PLUGIN_URL,
					'activeFramework' => FrameworkDetector::is_acss_active() ? 'acss' : 'at',
					'frameworkName'   => FrameworkDetector::get_active_framework_name(),
					'restUrl'         => esc_url_raw( rest_url() ),
					'restNonce'       => wp_create_nonce( 'wp_rest' ),
				]
			);
		}

		if ( file_exists( $dist_path . 'main.css' ) ) {
			wp_enqueue_style(
				'bricks-style-guide-admin',
				$dist_url . 'main.css',
				[ 'wpea-wp-resets' ],
				BRICKS_SG_VERSION
			);
		}
	}
}
