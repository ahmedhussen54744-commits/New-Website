<?php
/**
 * Lightweight eCommerce — product CPT, price, stock, wishlist, quick view
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Ecommerce {

    public function __construct() {
        if ( ! Infobd_Mega_Options::get( 'shop_enable', 1 ) ) return;
        add_action( 'init', array( $this, 'register_cpt' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post_infobd_product', array( $this, 'save_meta' ) );
        add_filter( 'the_content', array( $this, 'append_product_box' ) );
        // Wishlist via cookies
        add_action( 'wp_ajax_infobd_wishlist', array( $this, 'wishlist_toggle' ) );
        add_action( 'wp_ajax_nopriv_infobd_wishlist', array( $this, 'wishlist_toggle' ) );
    }

    public function register_cpt() {
        register_post_type( 'infobd_product', array(
            'labels' => array(
                'name'          => __( 'Products', 'infobd-mega' ),
                'singular_name' => __( 'Product', 'infobd-mega' ),
                'add_new_item'  => __( 'Add New Product', 'infobd-mega' ),
                'menu_name'     => __( 'Shop', 'infobd-mega' ),
            ),
            'public'        => true,
            'show_in_rest'  => true,
            'has_archive'   => true,
            'menu_icon'     => 'dashicons-cart',
            'menu_position' => 23,
            'rewrite'       => array( 'slug' => 'shop' ),
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        ) );
    }

    public function register_taxonomies() {
        register_taxonomy( 'product_cat', 'infobd_product', array(
            'label'        => __( 'Product Category', 'infobd-mega' ),
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'product-category' ),
        ) );
    }

    public function add_meta_box() {
        add_meta_box( 'infobd_product_details', __( 'Product Details', 'infobd-mega' ), array( $this, 'render_meta_box' ), 'infobd_product', 'normal', 'high' );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'infobd_product_meta', 'infobd_product_nonce' );
        $price = get_post_meta( $post->ID, '_product_price', true );
        $sale  = get_post_meta( $post->ID, '_product_sale_price', true );
        $sku   = get_post_meta( $post->ID, '_product_sku', true );
        $stock = get_post_meta( $post->ID, '_product_stock', true );
        $url   = get_post_meta( $post->ID, '_product_buy_url', true );
        ?>
        <p><label><strong><?php esc_html_e( 'Price', 'infobd-mega' ); ?></strong></label> <input type="text" name="_product_price" value="<?php echo esc_attr( $price ); ?>" class="regular-text"></p>
        <p><label><strong><?php esc_html_e( 'Sale Price', 'infobd-mega' ); ?></strong></label> <input type="text" name="_product_sale_price" value="<?php echo esc_attr( $sale ); ?>" class="regular-text"></p>
        <p><label><strong>SKU</strong></label> <input type="text" name="_product_sku" value="<?php echo esc_attr( $sku ); ?>" class="regular-text"></p>
        <p><label><strong><?php esc_html_e( 'Stock', 'infobd-mega' ); ?></strong></label>
            <select name="_product_stock">
                <option value="in" <?php selected( $stock, 'in' ); ?>><?php esc_html_e( 'In Stock', 'infobd-mega' ); ?></option>
                <option value="out" <?php selected( $stock, 'out' ); ?>><?php esc_html_e( 'Out of Stock', 'infobd-mega' ); ?></option>
                <option value="pre" <?php selected( $stock, 'pre' ); ?>><?php esc_html_e( 'Pre-order', 'infobd-mega' ); ?></option>
            </select>
        </p>
        <p><label><strong><?php esc_html_e( 'Buy URL (external)', 'infobd-mega' ); ?></strong></label> <input type="url" name="_product_buy_url" value="<?php echo esc_url( $url ); ?>" class="regular-text"></p>
        <?php
    }

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['infobd_product_nonce'] ) || ! wp_verify_nonce( $_POST['infobd_product_nonce'], 'infobd_product_meta' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        $fields = array(
            '_product_price' => 'sanitize_text_field',
            '_product_sale_price' => 'sanitize_text_field',
            '_product_sku' => 'sanitize_text_field',
            '_product_stock' => 'sanitize_key',
            '_product_buy_url' => 'esc_url_raw',
        );
        foreach ( $fields as $f => $san ) {
            if ( isset( $_POST[ $f ] ) ) update_post_meta( $post_id, $f, $san( wp_unslash( $_POST[ $f ] ) ) );
        }
    }

    public static function format_price( $amount ) {
        $sym = Infobd_Mega_Options::get( 'shop_currency_symbol', '৳' );
        $fmt = Infobd_Mega_Options::get( 'shop_price_format', 'symbol_first' );
        $num = number_format( (float) $amount, 2 );
        return 'symbol_first' === $fmt ? $sym . $num : $num . ' ' . $sym;
    }

    public function append_product_box( $content ) {
        if ( ! is_singular( 'infobd_product' ) || ! in_the_loop() || ! is_main_query() ) return $content;
        $id = get_the_ID();
        $price = get_post_meta( $id, '_product_price', true );
        $sale  = get_post_meta( $id, '_product_sale_price', true );
        $sku   = get_post_meta( $id, '_product_sku', true );
        $stock = get_post_meta( $id, '_product_stock', true );
        $url   = get_post_meta( $id, '_product_buy_url', true );
        ob_start(); ?>
        <div class="infobd-product-box">
            <div class="infobd-product-price">
                <?php if ( $sale ) : ?>
                    <span class="old-price"><?php echo wp_kses_post( self::format_price( $price ) ); ?></span>
                    <span class="new-price"><?php echo wp_kses_post( self::format_price( $sale ) ); ?></span>
                <?php else : ?>
                    <span class="new-price"><?php echo wp_kses_post( self::format_price( $price ) ); ?></span>
                <?php endif; ?>
            </div>
            <ul class="infobd-product-meta">
                <?php if ( $sku ) : ?><li><strong>SKU:</strong> <?php echo esc_html( $sku ); ?></li><?php endif; ?>
                <li><strong><?php esc_html_e( 'Stock', 'infobd-mega' ); ?>:</strong>
                    <?php
                    $labels = array( 'in' => __( '✓ In Stock', 'infobd-mega' ), 'out' => __( '✗ Out of Stock', 'infobd-mega' ), 'pre' => __( '◔ Pre-order', 'infobd-mega' ) );
                    echo esc_html( $labels[ $stock ] ?? $labels['in'] );
                    ?>
                </li>
            </ul>
            <div class="infobd-product-actions">
                <?php if ( $url ) : ?>
                    <a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener" class="infobd-buy-btn"><?php esc_html_e( 'Buy Now', 'infobd-mega' ); ?> &rarr;</a>
                <?php endif; ?>
                <button type="button" class="infobd-wishlist-btn" data-id="<?php echo esc_attr( $id ); ?>">&hearts; <?php esc_html_e( 'Wishlist', 'infobd-mega' ); ?></button>
            </div>
        </div>
        <?php
        return $content . ob_get_clean();
    }

    public function wishlist_toggle() {
        check_ajax_referer( 'infobd_mega_nonce', 'nonce' );
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        if ( ! $id ) wp_send_json_error( 'invalid' );
        $cookie = isset( $_COOKIE['infobd_wishlist'] ) ? array_filter( array_map( 'absint', explode( ',', $_COOKIE['infobd_wishlist'] ) ) ) : array();
        if ( in_array( $id, $cookie, true ) ) {
            $cookie = array_diff( $cookie, array( $id ) );
            $action = 'removed';
        } else {
            $cookie[] = $id;
            $action = 'added';
        }
        setcookie( 'infobd_wishlist', implode( ',', $cookie ), time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        wp_send_json_success( array( 'action' => $action, 'count' => count( $cookie ) ) );
    }
}
