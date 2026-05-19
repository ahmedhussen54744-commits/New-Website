<?php
/**
 * Plugin Name:       Infobd Mega Plugin
 * Plugin URI:        https://infobd.online/
 * Description:       Mega plugin for Infobd.online — 80+ features: security, copyright protection, news/gaming/eCommerce mixers, custom dashboard, post-link editor, social sharing, performance, SEO, ads, analytics, and more. Companion to the Infobd 3D theme.
 * Version:           1.0.0
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Infobd.online
 * Author URI:        https://infobd.online/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       infobd-mega
 * Domain Path:       /languages
 *
 * @package Infobd_Mega
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// ---------------- Constants ----------------
define( 'INFOBD_MEGA_VERSION', '1.0.0' );
define( 'INFOBD_MEGA_FILE', __FILE__ );
define( 'INFOBD_MEGA_DIR', plugin_dir_path( __FILE__ ) );
define( 'INFOBD_MEGA_URL', plugin_dir_url( __FILE__ ) );
define( 'INFOBD_MEGA_BASENAME', plugin_basename( __FILE__ ) );
define( 'INFOBD_MEGA_OPTION', 'infobd_mega_settings' );

// ---------------- Includes ----------------
require_once INFOBD_MEGA_DIR . 'includes/class-options.php';
require_once INFOBD_MEGA_DIR . 'includes/class-security.php';
require_once INFOBD_MEGA_DIR . 'includes/class-copyright.php';
require_once INFOBD_MEGA_DIR . 'includes/class-news.php';
require_once INFOBD_MEGA_DIR . 'includes/class-gaming.php';
require_once INFOBD_MEGA_DIR . 'includes/class-ecommerce.php';
require_once INFOBD_MEGA_DIR . 'includes/class-features.php';
require_once INFOBD_MEGA_DIR . 'includes/class-shortcodes.php';
require_once INFOBD_MEGA_DIR . 'includes/class-link-editor.php';
require_once INFOBD_MEGA_DIR . 'includes/class-seo.php';
require_once INFOBD_MEGA_DIR . 'includes/class-ads.php';
require_once INFOBD_MEGA_DIR . 'includes/class-admin.php';

// ---------------- Bootstrap ----------------
final class Infobd_Mega_Plugin {

    private static $instance = null;

    public $options;
    public $security;
    public $copyright;
    public $news;
    public $gaming;
    public $ecommerce;
    public $features;
    public $shortcodes;
    public $link_editor;
    public $seo;
    public $ads;
    public $admin;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
            self::$instance->boot();
        }
        return self::$instance;
    }

    private function boot() {
        // Init modules
        $this->options     = new Infobd_Mega_Options();
        $this->security    = new Infobd_Mega_Security();
        $this->copyright   = new Infobd_Mega_Copyright();
        $this->news        = new Infobd_Mega_News();
        $this->gaming      = new Infobd_Mega_Gaming();
        $this->ecommerce   = new Infobd_Mega_Ecommerce();
        $this->features    = new Infobd_Mega_Features();
        $this->shortcodes  = new Infobd_Mega_Shortcodes();
        $this->link_editor = new Infobd_Mega_Link_Editor();
        $this->seo         = new Infobd_Mega_SEO();
        $this->ads         = new Infobd_Mega_Ads();

        if ( is_admin() ) {
            $this->admin = new Infobd_Mega_Admin();
        }

        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend' ) );
        add_filter( 'plugin_action_links_' . INFOBD_MEGA_BASENAME, array( $this, 'plugin_links' ) );
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'infobd-mega', false, dirname( INFOBD_MEGA_BASENAME ) . '/languages' );
    }

    public function enqueue_frontend() {
        wp_enqueue_style( 'infobd-mega-frontend', INFOBD_MEGA_URL . 'assets/css/frontend.css', array(), INFOBD_MEGA_VERSION );
        wp_enqueue_script( 'infobd-mega-frontend', INFOBD_MEGA_URL . 'assets/js/frontend.js', array(), INFOBD_MEGA_VERSION, true );
        wp_localize_script( 'infobd-mega-frontend', 'InfobdMega', array(
            'ajax'  => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'infobd_mega_nonce' ),
            'opts'  => Infobd_Mega_Options::all(),
            'i18n'  => array(
                'protected' => __( 'This content is copyright protected.', 'infobd-mega' ),
                'copied'    => __( 'Link copied!', 'infobd-mega' ),
                'liked'     => __( 'Liked!', 'infobd-mega' ),
            ),
        ) );
    }

    public function plugin_links( $links ) {
        $settings = '<a href="' . admin_url( 'admin.php?page=infobd-mega' ) . '">' . __( 'Settings', 'infobd-mega' ) . '</a>';
        array_unshift( $links, $settings );
        return $links;
    }
}

// ---------------- Activation / Deactivation ----------------
register_activation_hook( __FILE__, function () {
    if ( false === get_option( INFOBD_MEGA_OPTION ) ) {
        add_option( INFOBD_MEGA_OPTION, Infobd_Mega_Options::defaults() );
    }
    // Custom post types need flushing
    Infobd_Mega_Plugin::instance();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );

// Boot
add_action( 'plugins_loaded', array( 'Infobd_Mega_Plugin', 'instance' ) );
