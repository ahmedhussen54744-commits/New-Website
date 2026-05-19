<?php
/**
 * Options manager — central source of truth for all 80+ feature toggles.
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Options {

    public static function defaults() {
        return array(
            // Security (15 features)
            'sec_disable_xmlrpc'        => 1,
            'sec_disable_rest_anon'     => 0,
            'sec_remove_version'        => 1,
            'sec_disable_user_enum'     => 1,
            'sec_login_attempts'        => 1,
            'sec_max_attempts'          => 5,
            'sec_lockout_minutes'       => 30,
            'sec_disable_file_edit'     => 1,
            'sec_force_strong_pass'     => 1,
            'sec_block_bad_bots'        => 1,
            'sec_security_headers'      => 1,
            'sec_hide_login_errors'     => 1,
            'sec_disable_directory'     => 1,
            'sec_block_php_in_uploads'  => 1,
            'sec_change_login_url'      => 0,
            'sec_login_slug'            => 'infobd-login',
            // Copyright (10 features)
            'cp_disable_right_click'    => 1,
            'cp_disable_text_select'    => 0,
            'cp_disable_copy'           => 1,
            'cp_disable_drag'           => 1,
            'cp_disable_devtools'       => 1,
            'cp_disable_print_screen'   => 1,
            'cp_disable_view_source'    => 1,
            'cp_warning_text'           => __( 'Content protected by Infobd.online copyright.', 'infobd-mega' ),
            'cp_image_watermark'        => 0,
            'cp_append_source_on_copy'  => 1,
            // News portal (10 features)
            'news_breaking_enable'      => 1,
            'news_trending_enable'      => 1,
            'news_reading_progress'     => 1,
            'news_estimated_time'       => 1,
            'news_view_counter'         => 1,
            'news_latest_widget'        => 1,
            'news_category_color'       => 1,
            'news_author_box'           => 1,
            'news_related_posts'        => 1,
            'news_print_friendly'       => 1,
            // Gaming (8 features)
            'game_zone_enable'          => 1,
            'game_leaderboard'          => 1,
            'game_review_score'         => 1,
            'game_screenshot_gallery'   => 1,
            'game_system_req'           => 1,
            'game_release_countdown'    => 1,
            'game_user_rating'          => 1,
            'game_genre_filter'         => 1,
            // eCommerce (8 features)
            'shop_enable'               => 1,
            'shop_currency'             => 'BDT',
            'shop_currency_symbol'      => '৳',
            'shop_price_format'         => 'symbol_first',
            'shop_show_stock'           => 1,
            'shop_wishlist'             => 1,
            'shop_compare'              => 1,
            'shop_quick_view'           => 1,
            // Public features (15 features)
            'feat_dark_mode_toggle'     => 1,
            'feat_lang_toggle'          => 1,
            'feat_font_size_toggle'     => 1,
            'feat_back_to_top'          => 1,
            'feat_smooth_scroll'        => 1,
            'feat_reading_mode'         => 1,
            'feat_share_buttons'        => 1,
            'feat_floating_share'       => 1,
            'feat_post_like'            => 1,
            'feat_post_bookmark'        => 1,
            'feat_lazy_load'            => 1,
            'feat_image_zoom'           => 1,
            'feat_table_of_contents'    => 1,
            'feat_search_suggest'       => 1,
            'feat_user_dashboard'       => 1,
            // SEO (8 features)
            'seo_meta_description'      => 1,
            'seo_og_tags'               => 1,
            'seo_twitter_cards'         => 1,
            'seo_canonical'             => 1,
            'seo_json_ld'               => 1,
            'seo_sitemap_link'          => 1,
            'seo_robots_meta'           => 1,
            'seo_breadcrumbs'           => 1,
            // Performance (6 features)
            'perf_disable_emoji'        => 1,
            'perf_disable_embeds'       => 1,
            'perf_remove_query_strings' => 1,
            'perf_dns_prefetch'         => 1,
            'perf_preload_fonts'        => 1,
            'perf_minify_inline'        => 0,
            // Ads (4 features)
            'ads_header_enable'         => 0,
            'ads_header_code'           => '',
            'ads_inpost_enable'         => 0,
            'ads_inpost_code'           => '',
        );
    }

    public static function all() {
        $opts = get_option( INFOBD_MEGA_OPTION, array() );
        return wp_parse_args( $opts, self::defaults() );
    }

    public static function get( $key, $fallback = null ) {
        $opts = self::all();
        return isset( $opts[ $key ] ) ? $opts[ $key ] : $fallback;
    }

    public static function set( $key, $value ) {
        $opts = self::all();
        $opts[ $key ] = $value;
        update_option( INFOBD_MEGA_OPTION, $opts );
    }

    public static function update( $values ) {
        $opts = self::all();
        foreach ( $values as $k => $v ) $opts[ $k ] = $v;
        update_option( INFOBD_MEGA_OPTION, $opts );
    }

    public static function feature_groups() {
        return array(
            'security'  => __( 'Security', 'infobd-mega' ),
            'copyright' => __( 'Copyright Protection', 'infobd-mega' ),
            'news'      => __( 'News Portal', 'infobd-mega' ),
            'gaming'    => __( 'Gaming Zone', 'infobd-mega' ),
            'shop'      => __( 'eCommerce / Shop', 'infobd-mega' ),
            'feat'      => __( 'Public Features', 'infobd-mega' ),
            'seo'       => __( 'SEO', 'infobd-mega' ),
            'perf'      => __( 'Performance', 'infobd-mega' ),
            'ads'       => __( 'Ads & Monetization', 'infobd-mega' ),
        );
    }

    public function __construct() {}
}
