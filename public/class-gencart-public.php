<?php
/**
 * Public functionality.
 *
 * @package Generador_Enlaces_Carrito
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Gencart_Public {

    /**
     * Plugin slug.
     *
     * @var string
     */
    private $plugin_name;

    /**
     * Plugin version.
     *
     * @var string
     */
    private $version;

    /**
     * Constructor.
     *
     * @param string $plugin_name Plugin slug.
     * @param string $version     Version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Detect custom parameter and add product to cart.
     */
    public function maybe_handle_custom_cart_link() {
        if ( ! function_exists( 'is_singular' ) || ! function_exists( 'WC' ) ) {
            return;
        }

        if ( ! is_singular( 'product' ) ) {
            return;
        }

        if ( empty( $_GET['add-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        $product_id = get_the_ID();
        if ( ! $product_id ) {
            return;
        }

        $product = wc_get_product( $product_id );
        if ( ! $product ) {
            return;
        }

        if ( $product->is_type( 'variable' ) ) {
            /*
             * Para evitar añadir variaciones incorrectas desde un enlace genérico,
             * no se procesa el parámetro en productos variables. Se podría extender
             * para usar variaciones concretas mediante parámetros adicionales.
             */
            return;
        }

        if ( ! $product->is_purchasable() || ! $product->is_in_stock() ) {
            wc_add_notice( esc_html__( 'El producto no está disponible actualmente.', 'cart-link' ), 'error' );
            return;
        }

        if ( null === WC()->cart ) {
            wc_load_cart();
        }

        $cart_item_key = WC()->cart->generate_cart_id( $product_id );
        $already_in_cart = WC()->cart->find_product_in_cart( $cart_item_key );

        if ( $already_in_cart ) {
            wc_add_notice( esc_html__( 'El producto ya estaba en tu carrito.', 'cart-link' ), 'notice' );
        } else {
            $added = WC()->cart->add_to_cart( $product_id );
            if ( ! $added ) {
                wc_add_notice( esc_html__( 'No se pudo añadir el producto al carrito.', 'cart-link' ), 'error' );
                return;
            }

            /* translators: %s: nombre del producto. */
            wc_add_notice( sprintf( esc_html__( '%s se añadió correctamente.', 'cart-link' ), $product->get_name() ), 'success' );
        }

        $destination = isset( $_GET['dest'] ) ? sanitize_key( wp_unslash( $_GET['dest'] ) ) : get_option( GENCART_OPTION_DEFAULT_DEST, 'cart' );
        $destination = in_array( $destination, array( 'cart', 'checkout' ), true ) ? $destination : 'cart';

        $redirect_url = ( 'checkout' === $destination ) ? wc_get_checkout_url() : wc_get_cart_url();

        wp_safe_redirect( $redirect_url );
        exit;
    }
}
