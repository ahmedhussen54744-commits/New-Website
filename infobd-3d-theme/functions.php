<?php
/**
 * Infobd 3D Theme functions
 *
 * @package Infobd_3D
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

define( 'INFOBD_3D_VERSION', '1.0.5' );
define( 'INFOBD_3D_DIR', get_template_directory() );
define( 'INFOBD_3D_URI', get_template_directory_uri() );

/* ---------------------------------------------------------------
 * THEME SETUP
 * ------------------------------------------------------------- */
function infobd_3d_setup() {
    load_theme_textdomain( 'infobd-3d', INFOBD_3D_DIR . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form','comment-form','comment-list','gallery','caption','style','script' ) );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
    add_theme_support( 'custom-background', array(
        'default-color' => '0a0a1a',
    ) );

    // Image sizes
    set_post_thumbnail_size( 1200, 720, true );
    add_image_size( 'infobd-card', 600, 360, true );
    add_image_size( 'infobd-thumb', 120, 120, true );
    add_image_size( 'infobd-hero', 1600, 900, true );

    // Menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'infobd-3d' ),
        'footer'  => __( 'Footer Menu', 'infobd-3d' ),
        'mobile'  => __( 'Mobile Menu', 'infobd-3d' ),
    ) );
}
add_action( 'after_setup_theme', 'infobd_3d_setup' );

/* ---------------------------------------------------------------
 * ENQUEUE ASSETS
 * ------------------------------------------------------------- */
function infobd_3d_enqueue() {
    // Bengali + Latin fonts
    wp_enqueue_style(
        'infobd-3d-fonts',
        'https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Inter:wght@400;600;700;800;900&display=swap',
        array(), null
    );

    wp_enqueue_style( 'infobd-3d-style', get_stylesheet_uri(), array( 'infobd-3d-fonts' ), INFOBD_3D_VERSION );
    wp_enqueue_style( 'infobd-3d-effects', INFOBD_3D_URI . '/assets/css/3d-effects.css', array( 'infobd-3d-style' ), INFOBD_3D_VERSION );

    wp_enqueue_script( 'infobd-3d-main', INFOBD_3D_URI . '/assets/js/main.js', array(), INFOBD_3D_VERSION, true );

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }

    // Localized strings for JS
    wp_localize_script( 'infobd-3d-main', 'Infobd3D', array(
        'ajax'    => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'infobd_3d_nonce' ),
        'home'    => esc_url( home_url( '/' ) ),
        'i18n'    => array(
            'copied' => __( 'Link Copied!', 'infobd-3d' ),
            'menu'   => __( 'Menu', 'infobd-3d' ),
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'infobd_3d_enqueue' );

/* ---------------------------------------------------------------
 * SIDEBARS
 * ------------------------------------------------------------- */
function infobd_3d_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Primary Sidebar', 'infobd-3d' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Main sidebar shown on posts and archives.', 'infobd-3d' ),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
    for ( $i = 1; $i <= 4; $i++ ) {
        register_sidebar( array(
            'name'          => sprintf( __( 'Footer Column %d', 'infobd-3d' ), $i ),
            'id'            => 'footer-' . $i,
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3>',
            'after_title'   => '</h3>',
        ) );
    }
}
add_action( 'widgets_init', 'infobd_3d_widgets_init' );

/* ---------------------------------------------------------------
 * CUSTOMIZER
 * ------------------------------------------------------------- */
require_once INFOBD_3D_DIR . '/inc/customizer.php';
require_once INFOBD_3D_DIR . '/inc/template-functions.php';

/* ---------------------------------------------------------------
 * EXCERPT
 * ------------------------------------------------------------- */
function infobd_3d_excerpt_length( $length ) { return 22; }
add_filter( 'excerpt_length', 'infobd_3d_excerpt_length' );

function infobd_3d_excerpt_more( $more ) { return '&hellip;'; }
add_filter( 'excerpt_more', 'infobd_3d_excerpt_more' );

/* ---------------------------------------------------------------
 * SECURITY HARDENING (theme-side)
 * ------------------------------------------------------------- */
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'xmlrpc_enabled', '__return_false' );

// Disable file editor in admin
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) define( 'DISALLOW_FILE_EDIT', true );

/* ---------------------------------------------------------------
 * BREAKING NEWS HELPER
 * ------------------------------------------------------------- */
function infobd_3d_breaking_news() {
    $latest = new WP_Query( array(
        'posts_per_page' => 6,
        'ignore_sticky_posts' => true,
        'no_found_rows'  => true,
    ) );
    if ( $latest->have_posts() ) {
        $items = array();
        while ( $latest->have_posts() ) {
            $latest->the_post();
            $items[] = '<a href="' . esc_url( get_permalink() ) . '" style="color:#fff">' . esc_html( get_the_title() ) . '</a>';
        }
        wp_reset_postdata();
        return implode( ' &nbsp;&bull;&nbsp; ', $items );
    }
    return '';
}

/* ---------------------------------------------------------------
 * READING TIME
 * ------------------------------------------------------------- */
function infobd_3d_reading_time( $post_id = null ) {
    $content = get_post_field( 'post_content', $post_id ?: get_the_ID() );
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes = max( 1, ceil( $word_count / 200 ) );
    /* translators: %d: minutes to read */
    return sprintf( _n( '%d min read', '%d min read', $minutes, 'infobd-3d' ), $minutes );
}

/* ---------------------------------------------------------------
 * VIEW COUNT (lightweight, theme-side fallback)
 * ------------------------------------------------------------- */
function infobd_3d_track_view() {
    if ( is_singular( 'post' ) && ! is_user_logged_in() ) {
        $id = get_the_ID();
        $views = (int) get_post_meta( $id, '_infobd_views', true );
        update_post_meta( $id, '_infobd_views', $views + 1 );
    }
}
add_action( 'wp_footer', 'infobd_3d_track_view' );

function infobd_3d_get_views( $id = null ) {
    $id = $id ?: get_the_ID();
    return (int) get_post_meta( $id, '_infobd_views', true );
}

/* ---------------------------------------------------------------
 * BODY CLASS
 * ------------------------------------------------------------- */
function infobd_3d_body_class( $classes ) {
    $classes[] = 'infobd-3d-theme';
    if ( is_singular() ) $classes[] = 'has-3d-effects';
    return $classes;
}
add_filter( 'body_class', 'infobd_3d_body_class' );

/* ---------------------------------------------------------------
 * FALLBACK MENU (when admin hasn't set one)
 * ------------------------------------------------------------- */
function infobd_3d_fallback_menu() {
    echo '<ul id="primary-menu" class="nav-menu">';
    echo '<li class="current-menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'infobd-3d' ) . '</a></li>';
    $cats = get_categories( array( 'number' => 6, 'orderby' => 'count', 'order' => 'DESC' ) );
    foreach ( $cats as $cat ) {
        echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
    }
    echo '</ul>';
}

/* ---------------------------------------------------------------
 * CUSTOM AVATAR FALLBACK
 * ------------------------------------------------------------- */
function infobd_3d_get_avatar( $user_id = 0, $size = 40 ) {
    return get_avatar( $user_id, $size );
}
