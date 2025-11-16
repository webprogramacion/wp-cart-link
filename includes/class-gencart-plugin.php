<?php
/**
 * Core plugin class
 *
 * @package Generador_Enlaces_Carrito
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class responsible for orchestrating hooks.
 */
class Gencart_Plugin {

    /**
     * Plugin version.
     *
     * @var string
     */
    protected $version = GENCART_VERSION;

    /**
     * Plugin slug.
     *
     * @var string
     */
    protected $plugin_name = 'cart-link';

    /**
     * Admin handler.
     *
     * @var Gencart_Admin
     */
    protected $admin;

    /**
     * Public handler.
     *
     * @var Gencart_Public
     */
    protected $public;

    /**
     * Execute plugin bootstrap.
     */
    public function run() {
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load required files.
     */
    private function load_dependencies() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-gencart-admin.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-gencart-public.php';
    }

    /**
     * Define hooks for admin area.
     */
    private function define_admin_hooks() {
        $this->admin = new Gencart_Admin( $this->plugin_name, $this->version );

        add_action( 'add_meta_boxes', array( $this->admin, 'register_metabox' ) );
        add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_admin_assets' ) );
        add_filter( 'woocommerce_settings_tabs_array', array( $this->admin, 'add_settings_tab' ), 99 );
        add_action( 'woocommerce_settings_gencart', array( $this->admin, 'render_settings' ) );
        add_action( 'woocommerce_update_options_gencart', array( $this->admin, 'save_settings' ) );
    }

    /**
     * Define hooks for public area.
     */
    private function define_public_hooks() {
        $this->public = new Gencart_Public( $this->plugin_name, $this->version );
        add_action( 'template_redirect', array( $this->public, 'maybe_handle_custom_cart_link' ) );
    }
}
