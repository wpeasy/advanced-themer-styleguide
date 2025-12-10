<?php
/**
 * Plugin Name: Advanced Themer Style Guide
 * Description: Adds Svelte based Elements to Bricks Builder for creating Style Guides based on Advanced Themer
 * Version: 0.0.1-beta
 * Author: WP Easy
 * Author URI: https://wpeasy.au
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: advanced-themer-style-guide
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

namespace AB\ATStyleGuide;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants.
define( 'AT_STYLE_GUIDE_VERSION', '0.0.1-beta' );
define( 'AT_STYLE_GUIDE_PLUGIN_FILE', __FILE__ );
define( 'AT_STYLE_GUIDE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AT_STYLE_GUIDE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Initialize the plugin.
 *
 * @return void
 */
function init() {
	// Check if Bricks Builder is active.
	if ( ! defined( 'BRICKS_VERSION' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\\missing_bricks_notice' );
		return;
	}

	// Load plugin classes.
	require_once AT_STYLE_GUIDE_PLUGIN_DIR . 'includes/class-plugin.php';

	// Initialize the plugin.
	Plugin::get_instance();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\init' );

/**
 * Admin notice for missing Bricks Builder.
 *
 * @return void
 */
function missing_bricks_notice() {
	?>
	<div class="notice notice-error">
		<p><?php esc_html_e( 'Advanced Themer Style Guide requires Bricks Builder to be installed and activated.', 'advanced-themer-style-guide' ); ?></p>
	</div>
	<?php
}
