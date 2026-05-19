<?php
/**
 * News portal features — 10 features
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_News {

    public function __construct() {
        $opts = Infobd_Mega_Options::all();

        // Add reading progress bar
        if ( ! empty( $opts['news_reading_progress'] ) ) {
            add_action( 'wp_body_open', array( $this, 'render_progress_bar' ) );
        }

        // View counter
        if ( ! empty( $opts['news_view_counter'] ) ) {
            add_action( 'wp_head', array( $this, 'maybe_track_view' ) );
        }

        // Custom Post Type for news (optional, supplement to posts)
        add_action( 'init', array( $this, 'register_breaking_meta' ) );

        // Trending shortcode handled via shortcodes class
    }

    public function render_progress_bar() {
        if ( ! is_singular( 'post' ) ) return;
        echo '<div id="infobd-progress" style="position:fixed;top:0;left:0;height:4px;width:0;background:linear-gradient(90deg,#ff2d55,#ffd60a,#00d4ff);z-index:9999;transition:width .1s linear;box-shadow:0 0 10px #ff2d55;"></div>';
    }

    public function maybe_track_view() {
        if ( ! is_singular( 'post' ) ) return;
        if ( is_user_logged_in() && current_user_can( 'edit_posts' ) ) return;
        $id = get_queried_object_id();
        if ( ! $id ) return;
        // Cookie-based deduplication (1 per 4 hours per visitor)
        $key = 'infobd_v_' . $id;
        if ( isset( $_COOKIE[ $key ] ) ) return;
        $views = (int) get_post_meta( $id, '_infobd_views', true );
        update_post_meta( $id, '_infobd_views', $views + 1 );
        setcookie( $key, '1', time() + 4 * HOUR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    }

    public function register_breaking_meta() {
        register_post_meta( 'post', '_infobd_breaking', array(
            'show_in_rest' => true, 'single' => true, 'type' => 'boolean',
            'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
        ) );
        register_post_meta( 'post', '_infobd_trending', array(
            'show_in_rest' => true, 'single' => true, 'type' => 'boolean',
            'auth_callback' => function () { return current_user_can( 'edit_posts' ); },
        ) );
    }

    public static function get_views( $post_id = null ) {
        $id = $post_id ?: get_the_ID();
        return (int) get_post_meta( $id, '_infobd_views', true );
    }

    public static function get_trending( $count = 5 ) {
        return new WP_Query( array(
            'posts_per_page' => $count,
            'meta_key'       => '_infobd_views',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'date_query'     => array( array( 'after' => '30 days ago' ) ),
            'no_found_rows'  => true,
        ) );
    }
}
