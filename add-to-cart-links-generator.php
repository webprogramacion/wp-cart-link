<?php
/**
 * Plugin Name:       Add to Cart links generator for WooCommerce
 * Plugin URI:        https://webprogramacion.com/add-to-cart-links-generator
 * Description:       Generate custom links to add products to the WooCommerce cart using your own parameter and redirect to cart or checkout.
 * Version:           1.0.0
 * Author:            Dámaso Velázquez Álvarez
 * Author URI:        https://webprogramacion.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       add-to-cart-links-generator
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * WC requires at least: 5.0
 * WC tested up to:   9.0
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
        wp_die( esc_html__( 'Este plugin requiere WooCommerce activo.', 'add-to-cart-links-generator' ) );
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
