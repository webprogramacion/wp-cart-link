<?php
/**
 * Admin functionality.
 *
 * @package Generador_Enlaces_Carrito
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Gencart_Admin {

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
     * @param string $version     Plugin version.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }

    /**
     * Registers metabox for product editor.
     */
    public function register_metabox() {
        add_meta_box(
            'gencart-link-box',
            esc_html__( 'Enlace al carrito', 'add-to-cart-links-generator' ),
            array( $this, 'render_metabox' ),
            'product',
            'side',
            'high'
        );
    }

    /**
     * Prints metabox content.
     *
     * @param WP_Post $post Current post.
     */
    public function render_metabox( $post ) {
        if ( ! function_exists( 'wc_get_product' ) ) {
            echo '<p>' . esc_html__( 'WooCommerce no está disponible.', 'add-to-cart-links-generator' ) . '</p>';
            return;
        }

        $product = wc_get_product( $post->ID );

        if ( ! $product ) {
            echo '<p>' . esc_html__( 'Producto no válido.', 'add-to-cart-links-generator' ) . '</p>';
            return;
        }

        if ( $product->is_type( 'variable' ) ) {
            // Para simplificar la generación del enlace, los productos variables no son compatibles.
            echo '<p>' . esc_html__( 'Los productos variables no son compatibles con los enlaces rápidos. Usa una variación específica.', 'add-to-cart-links-generator' ) . '</p>';
            return;
        }

        if ( ! $product->is_in_stock() ) {
            echo '<p>' . esc_html__( 'El producto está sin stock. No se genera enlace.', 'add-to-cart-links-generator' ) . '</p>';
            return;
        }

        $base_url      = get_permalink( $post->ID );
        $nonce         = wp_create_nonce( 'gencart_add_to_cart_' . $post->ID );
        $default_link  = add_query_arg(
            array(
                'add-cart'      => 1,
                'gencart_nonce' => $nonce,
            ),
            $base_url
        );
        $cart_link     = add_query_arg(
            array(
                'add-cart'      => 1,
                'dest'          => 'cart',
                'gencart_nonce' => $nonce,
            ),
            $base_url
        );
        $checkout_link = add_query_arg(
            array(
                'add-cart'      => 1,
                'dest'          => 'checkout',
                'gencart_nonce' => $nonce,
            ),
            $base_url
        );

        echo '<p>' . esc_html__( 'Copia estos enlaces para compartir con tus clientes. Al visitarlos se añadirá el producto al carrito.', 'add-to-cart-links-generator' ) . '</p>';
        echo '<p><label for="gencart-link-default"><strong>' . esc_html__( 'Enlace base', 'add-to-cart-links-generator' ) . '</strong></label></p>';
        echo '<input type="text" id="gencart-link-default" class="widefat" readonly value="' . esc_attr( $default_link ) . '" />';
        echo '<div class="gencart-button-group">';
        echo '<button type="button" class="button gencart-copy-link" data-gencart-target="' . esc_attr( $default_link ) . '" data-notice="' . esc_attr__( 'Enlace copiado (configuración por defecto).', 'add-to-cart-links-generator' ) . '">' . esc_html__( 'Copiar enlace (por defecto)', 'add-to-cart-links-generator' ) . '</button>';
        echo '<button type="button" class="button gencart-copy-link" data-gencart-target="' . esc_attr( $cart_link ) . '" data-notice="' . esc_attr__( 'Enlace al carrito copiado.', 'add-to-cart-links-generator' ) . '">' . esc_html__( 'Copiar enlace a carrito', 'add-to-cart-links-generator' ) . '</button>';
        echo '<button type="button" class="button gencart-copy-link" data-gencart-target="' . esc_attr( $checkout_link ) . '" data-notice="' . esc_attr__( 'Enlace al checkout copiado.', 'add-to-cart-links-generator' ) . '">' . esc_html__( 'Copiar enlace a checkout', 'add-to-cart-links-generator' ) . '</button>';
        echo '</div>';
        echo '<div class="gencart-copy-notice" aria-live="polite"></div>';
    }

    /**
     * Load admin scripts/styles where needed.
     */
    public function enqueue_admin_assets() {
        $screen = get_current_screen();
        if ( empty( $screen ) || 'product' !== $screen->id ) {
            return;
        }

        wp_enqueue_script(
            'gencart-admin',
            plugin_dir_url( __FILE__ ) . 'js/gencart-admin.js',
            array(),
            $this->version,
            true
        );

        wp_enqueue_style(
            'gencart-admin',
            plugin_dir_url( __FILE__ ) . 'css/gencart-admin.css',
            array(),
            $this->version
        );

        wp_localize_script(
            'gencart-admin',
            'gencartAdmin',
            array(
                'fallbackNotice' => esc_html__( 'El navegador no permite copiar automáticamente. Selecciona y copia manualmente.', 'add-to-cart-links-generator' ),
            )
        );
    }

    /**
     * Adds plugin tab to WooCommerce settings.
     *
     * @param array $tabs Settings tabs.
     *
     * @return array
     */
    public function add_settings_tab( $tabs ) {
        $tabs['gencart'] = esc_html__( 'Enlaces al carrito', 'add-to-cart-links-generator' );
        return $tabs;
    }

    /**
     * Render settings fields.
     */
    public function render_settings() {
        woocommerce_admin_fields( $this->get_settings() );
    }

    /**
     * Save settings values.
     */
    public function save_settings() {
        woocommerce_update_options( $this->get_settings() );
    }

    /**
     * Settings schema.
     *
     * @return array
     */
    private function get_settings() {
        return array(
            'section_title' => array(
                'name' => esc_html__( 'Generador de enlaces al carrito', 'add-to-cart-links-generator' ),
                'type' => 'title',
                'desc' => esc_html__( 'Configura el comportamiento general de los enlaces generados.', 'add-to-cart-links-generator' ),
                'id'   => 'gencart_settings_title',
            ),
            GENCART_OPTION_DEFAULT_DEST => array(
                'name'     => esc_html__( 'Destino por defecto', 'add-to-cart-links-generator' ),
                'type'     => 'select',
                'desc_tip' => true,
                'description' => esc_html__( 'Determina si los enlaces sin parámetro dest redirigen al carrito o al checkout.', 'add-to-cart-links-generator' ),
                'id'       => GENCART_OPTION_DEFAULT_DEST,
                'default'  => 'cart',
                'options'  => array(
                    'cart'     => esc_html__( 'Carrito', 'add-to-cart-links-generator' ),
                    'checkout' => esc_html__( 'Finalizar compra', 'add-to-cart-links-generator' ),
                ),
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id'   => 'gencart_settings_end',
            ),
        );
    }
}
