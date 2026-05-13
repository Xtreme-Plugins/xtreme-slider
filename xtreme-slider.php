<?php
/**
 * Plugin Name: Xtreme Slider (3D Image Slider)
 * Plugin URI: https://xtremeplugins.com/plugins/xtreme-slider/
 * Description: Beautiful image slider with Simple and 3D layouts. Upload photos, generate shortcodes, and embed anywhere in WordPress or Elementor.
 * Version: 1.2.1
 * Author: XtremePlugins
 * Author URI: https://xtremeplugins.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires PHP: 7.4
 * Requires at least: 6.0
 * Text Domain: xtreme-slider
 * Domain Path: /languages
 */

defined( 'ABSPATH' ) || exit;

define( 'XTRSL_VERSION',         '1.2.1' );
define( 'XTRSL_DB_VERSION',      '1' );
define( 'XTRSL_PLUGIN_FILE',     __FILE__ );
define( 'XTRSL_PLUGIN_PATH',     plugin_dir_path( __FILE__ ) );
define( 'XTRSL_PLUGIN_URL',      plugin_dir_url( __FILE__ ) );
define( 'XTRSL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Activation / deactivation hooks must be called at file scope.
require_once XTRSL_PLUGIN_PATH . 'inc/class-activator.php';
require_once XTRSL_PLUGIN_PATH . 'inc/class-deactivator.php';

register_activation_hook( __FILE__,   array( 'Xtrsl_Activator',   'activate' ) );
register_deactivation_hook( __FILE__, array( 'Xtrsl_Deactivator', 'deactivate' ) );

add_action( 'plugins_loaded', 'xtrsl_init_plugin' );

function xtrsl_init_plugin() {
	require_once XTRSL_PLUGIN_PATH . 'inc/class-plugin.php';
	Xtrsl_Plugin::instance();
}
