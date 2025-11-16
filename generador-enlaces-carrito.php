<?php
/**
 * Plugin Name:       Generador de enlaces al carrito
 * Plugin URI:        https://example.com/
 * Description:       Genera enlaces personalizados para añadir productos al carrito utilizando un parámetro propio y redirigir a carrito o checkout.
 * Version:           1.0.0
 * Author:            OpenAI Assistant
 * Author URI:        https://openai.com/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       generador-enlaces-carrito
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Currently plugin version.
 */
define( 'GENCART_VERSION', '1.0.0' );
define( 'GENCART_OPTION_DEFAULT_DEST', 'gencart_default_destination' );

/**
 * The code that runs during plugin activation.
 */
function gencart_activate() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( esc_html__( 'Este plugin requiere WooCommerce activo.', 'generador-enlaces-carrito' ) );
    }
}
register_activation_hook( __FILE__, 'gencart_activate' );

/**
 * Load the core plugin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-gencart-plugin.php';

/**
 * Begins execution of the plugin.
 */
function gencart_run_plugin() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    $plugin = new Gencart_Plugin();
    $plugin->run();
}
add_action( 'plugins_loaded', 'gencart_run_plugin', 20 );
