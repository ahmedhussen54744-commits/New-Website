<?php
/**
 * Admin Dashboard — main settings page with all 80+ feature toggles
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'register_dashboard_widget' ) );
    }

    public function enqueue( $hook ) {
        wp_enqueue_style( 'infobd-mega-admin', INFOBD_MEGA_URL . 'assets/css/admin.css', array(), INFOBD_MEGA_VERSION );
        if ( strpos( $hook, 'infobd-mega' ) !== false ) {
            wp_enqueue_script( 'infobd-mega-admin', INFOBD_MEGA_URL . 'assets/js/admin.js', array( 'jquery' ), INFOBD_MEGA_VERSION, true );
        }
    }

    public function menu() {
        add_menu_page(
            __( 'Infobd Mega', 'infobd-mega' ),
            __( 'Infobd Mega', 'infobd-mega' ),
            'manage_options',
            'infobd-mega',
            array( $this, 'render_page' ),
            'dashicons-admin-site-alt3',
            2
        );
        add_submenu_page( 'infobd-mega', __( 'Dashboard', 'infobd-mega' ), __( 'Dashboard', 'infobd-mega' ), 'manage_options', 'infobd-mega', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Security', 'infobd-mega' ), __( 'Security', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=security', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Copyright', 'infobd-mega' ), __( 'Copyright', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=copyright', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'News', 'infobd-mega' ), __( 'News', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=news', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Gaming', 'infobd-mega' ), __( 'Gaming', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=gaming', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Shop', 'infobd-mega' ), __( 'Shop', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=shop', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Public Features', 'infobd-mega' ), __( 'Public Features', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=feat', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'SEO', 'infobd-mega' ), __( 'SEO', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=seo', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Performance', 'infobd-mega' ), __( 'Performance', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=perf', array( $this, 'render_page' ) );
        add_submenu_page( 'infobd-mega', __( 'Ads', 'infobd-mega' ), __( 'Ads', 'infobd-mega' ), 'manage_options', 'infobd-mega&tab=ads', array( $this, 'render_page' ) );
    }

    public function register_settings() {
        register_setting( 'infobd_mega_group', INFOBD_MEGA_OPTION, array(
            'type' => 'array',
            'sanitize_callback' => array( $this, 'sanitize' ),
            'default' => Infobd_Mega_Options::defaults(),
        ) );
    }

    public function sanitize( $input ) {
        $defaults = Infobd_Mega_Options::defaults();
        $clean = Infobd_Mega_Options::all();

        foreach ( $defaults as $key => $val ) {
            if ( is_int( $val ) ) {
                $clean[ $key ] = isset( $input[ $key ] ) ? (int) $input[ $key ] : 0;
            } elseif ( in_array( $key, array( 'ads_header_code', 'ads_inpost_code' ), true ) ) {
                $clean[ $key ] = isset( $input[ $key ] ) ? wp_unslash( $input[ $key ] ) : '';
            } else {
                $clean[ $key ] = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : $val;
            }
        }
        return $clean;
    }

    public function field_definitions() {
        return array(
            // Security
            'sec_disable_xmlrpc'        => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Disable XML-RPC', 'infobd-mega' ), 'desc' => __( 'Prevent brute-force via xmlrpc.php', 'infobd-mega' ) ),
            'sec_disable_rest_anon'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Require login for REST API', 'infobd-mega' ) ),
            'sec_remove_version'        => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Hide WordPress version', 'infobd-mega' ) ),
            'sec_disable_user_enum'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Block user enumeration', 'infobd-mega' ) ),
            'sec_login_attempts'        => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Limit login attempts', 'infobd-mega' ) ),
            'sec_max_attempts'          => array( 'tab' => 'security', 'type' => 'number', 'label' => __( 'Max login attempts', 'infobd-mega' ) ),
            'sec_lockout_minutes'       => array( 'tab' => 'security', 'type' => 'number', 'label' => __( 'Lockout duration (minutes)', 'infobd-mega' ) ),
            'sec_disable_file_edit'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Disable theme/plugin file editor', 'infobd-mega' ) ),
            'sec_force_strong_pass'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Force strong passwords', 'infobd-mega' ) ),
            'sec_block_bad_bots'        => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Block bad bots', 'infobd-mega' ) ),
            'sec_security_headers'      => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Send security headers (X-Frame-Options, HSTS, etc.)', 'infobd-mega' ) ),
            'sec_hide_login_errors'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Hide login error details', 'infobd-mega' ) ),
            'sec_disable_directory'     => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Disable directory listing', 'infobd-mega' ), 'desc' => __( 'Add a .htaccess rule on your server: Options -Indexes', 'infobd-mega' ) ),
            'sec_block_php_in_uploads'  => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Block PHP execution inside uploads', 'infobd-mega' ) ),
            'sec_change_login_url'      => array( 'tab' => 'security', 'type' => 'toggle', 'label' => __( 'Change login URL (custom slug)', 'infobd-mega' ) ),
            'sec_login_slug'            => array( 'tab' => 'security', 'type' => 'text', 'label' => __( 'Custom login slug', 'infobd-mega' ) ),

            // Copyright
            'cp_disable_right_click'    => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Disable right-click', 'infobd-mega' ) ),
            'cp_disable_text_select'    => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Disable text selection', 'infobd-mega' ) ),
            'cp_disable_copy'           => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Disable copy', 'infobd-mega' ) ),
            'cp_disable_drag'           => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Disable image drag', 'infobd-mega' ) ),
            'cp_disable_devtools'       => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Block DevTools shortcuts (F12, Ctrl+Shift+I/J/C)', 'infobd-mega' ) ),
            'cp_disable_print_screen'   => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Block Print / Screenshot shortcut', 'infobd-mega' ) ),
            'cp_disable_view_source'    => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Block View Source / Save (Ctrl+U / Ctrl+S)', 'infobd-mega' ) ),
            'cp_warning_text'           => array( 'tab' => 'copyright', 'type' => 'text', 'label' => __( 'Copyright warning message', 'infobd-mega' ) ),
            'cp_image_watermark'        => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Auto-watermark uploaded images (server support required)', 'infobd-mega' ) ),
            'cp_append_source_on_copy'  => array( 'tab' => 'copyright', 'type' => 'toggle', 'label' => __( 'Append source URL when copying long text', 'infobd-mega' ) ),

            // News
            'news_breaking_enable'      => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Breaking news ticker', 'infobd-mega' ) ),
            'news_trending_enable'      => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Trending posts widget', 'infobd-mega' ) ),
            'news_reading_progress'     => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Reading progress bar', 'infobd-mega' ) ),
            'news_estimated_time'       => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Show estimated reading time', 'infobd-mega' ) ),
            'news_view_counter'         => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Track post views', 'infobd-mega' ) ),
            'news_latest_widget'        => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Latest posts widget', 'infobd-mega' ) ),
            'news_category_color'       => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Color-coded categories', 'infobd-mega' ) ),
            'news_author_box'           => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Show author box', 'infobd-mega' ) ),
            'news_related_posts'        => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Show related posts', 'infobd-mega' ) ),
            'news_print_friendly'       => array( 'tab' => 'news', 'type' => 'toggle', 'label' => __( 'Print-friendly page', 'infobd-mega' ) ),

            // Gaming
            'game_zone_enable'          => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Enable Gaming Zone (CPT)', 'infobd-mega' ) ),
            'game_leaderboard'          => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Game leaderboard', 'infobd-mega' ) ),
            'game_review_score'         => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Review score', 'infobd-mega' ) ),
            'game_screenshot_gallery'   => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Screenshot gallery', 'infobd-mega' ) ),
            'game_system_req'           => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'System requirements box', 'infobd-mega' ) ),
            'game_release_countdown'    => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Release date countdown', 'infobd-mega' ) ),
            'game_user_rating'          => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'User rating', 'infobd-mega' ) ),
            'game_genre_filter'         => array( 'tab' => 'gaming', 'type' => 'toggle', 'label' => __( 'Genre filter', 'infobd-mega' ) ),

            // Shop
            'shop_enable'               => array( 'tab' => 'shop', 'type' => 'toggle', 'label' => __( 'Enable Shop (Product CPT)', 'infobd-mega' ) ),
            'shop_currency'             => array( 'tab' => 'shop', 'type' => 'text',   'label' => __( 'Currency code', 'infobd-mega' ) ),
            'shop_currency_symbol'      => array( 'tab' => 'shop', 'type' => 'text',   'label' => __( 'Currency symbol', 'infobd-mega' ) ),
            'shop_price_format'         => array( 'tab' => 'shop', 'type' => 'select', 'label' => __( 'Price format', 'infobd-mega' ), 'options' => array( 'symbol_first' => 'Symbol Amount (৳100)', 'amount_first' => 'Amount Symbol (100 ৳)' ) ),
            'shop_show_stock'           => array( 'tab' => 'shop', 'type' => 'toggle', 'label' => __( 'Show stock status', 'infobd-mega' ) ),
            'shop_wishlist'             => array( 'tab' => 'shop', 'type' => 'toggle', 'label' => __( 'Wishlist button', 'infobd-mega' ) ),
            'shop_compare'              => array( 'tab' => 'shop', 'type' => 'toggle', 'label' => __( 'Compare button', 'infobd-mega' ) ),
            'shop_quick_view'           => array( 'tab' => 'shop', 'type' => 'toggle', 'label' => __( 'Quick view modal', 'infobd-mega' ) ),

            // Public Features
            'feat_dark_mode_toggle'     => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Dark mode toggle', 'infobd-mega' ) ),
            'feat_lang_toggle'          => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Language toggle (BN/EN UI)', 'infobd-mega' ) ),
            'feat_font_size_toggle'     => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Font size adjustment (A-/A+)', 'infobd-mega' ) ),
            'feat_back_to_top'          => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Back-to-top button', 'infobd-mega' ) ),
            'feat_smooth_scroll'        => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Smooth scrolling', 'infobd-mega' ) ),
            'feat_reading_mode'         => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Reading mode (distraction-free)', 'infobd-mega' ) ),
            'feat_share_buttons'        => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Social share buttons', 'infobd-mega' ) ),
            'feat_floating_share'       => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Floating share sidebar', 'infobd-mega' ) ),
            'feat_post_like'            => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Post like button', 'infobd-mega' ) ),
            'feat_post_bookmark'        => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Post bookmark button', 'infobd-mega' ) ),
            'feat_lazy_load'            => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Lazy-load images', 'infobd-mega' ) ),
            'feat_image_zoom'           => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Image zoom on click', 'infobd-mega' ) ),
            'feat_table_of_contents'    => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Auto Table of Contents', 'infobd-mega' ) ),
            'feat_search_suggest'       => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'Live search suggestions', 'infobd-mega' ) ),
            'feat_user_dashboard'       => array( 'tab' => 'feat', 'type' => 'toggle', 'label' => __( 'User front-end dashboard', 'infobd-mega' ) ),

            // SEO
            'seo_meta_description'      => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Meta description', 'infobd-mega' ) ),
            'seo_og_tags'               => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Open Graph tags', 'infobd-mega' ) ),
            'seo_twitter_cards'         => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Twitter Cards', 'infobd-mega' ) ),
            'seo_canonical'             => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Canonical URL', 'infobd-mega' ) ),
            'seo_json_ld'               => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'JSON-LD structured data', 'infobd-mega' ) ),
            'seo_sitemap_link'          => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'XML sitemap link in head', 'infobd-mega' ) ),
            'seo_robots_meta'           => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Robots meta tag', 'infobd-mega' ) ),
            'seo_breadcrumbs'           => array( 'tab' => 'seo', 'type' => 'toggle', 'label' => __( 'Breadcrumbs (use [infobd_breadcrumbs])', 'infobd-mega' ) ),

            // Performance
            'perf_disable_emoji'        => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'Disable emoji scripts', 'infobd-mega' ) ),
            'perf_disable_embeds'       => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'Disable embeds', 'infobd-mega' ) ),
            'perf_remove_query_strings' => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'Remove asset version query strings', 'infobd-mega' ) ),
            'perf_dns_prefetch'         => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'DNS prefetch / preconnect', 'infobd-mega' ) ),
            'perf_preload_fonts'        => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'Preload primary font', 'infobd-mega' ) ),
            'perf_minify_inline'        => array( 'tab' => 'perf', 'type' => 'toggle', 'label' => __( 'Minify inline CSS/JS (basic)', 'infobd-mega' ) ),

            // Ads
            'ads_header_enable'         => array( 'tab' => 'ads', 'type' => 'toggle', 'label' => __( 'Header ad', 'infobd-mega' ) ),
            'ads_header_code'           => array( 'tab' => 'ads', 'type' => 'textarea', 'label' => __( 'Header ad code', 'infobd-mega' ) ),
            'ads_inpost_enable'         => array( 'tab' => 'ads', 'type' => 'toggle', 'label' => __( 'In-post ad after 3rd paragraph', 'infobd-mega' ) ),
            'ads_inpost_code'           => array( 'tab' => 'ads', 'type' => 'textarea', 'label' => __( 'In-post ad code', 'infobd-mega' ) ),
        );
    }

    public function render_page() {
        $current_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
        $tabs = array(
            'dashboard' => __( 'Dashboard', 'infobd-mega' ),
            'security'  => __( '🔒 Security', 'infobd-mega' ),
            'copyright' => __( '©  Copyright', 'infobd-mega' ),
            'news'      => __( '📰 News', 'infobd-mega' ),
            'gaming'    => __( '🎮 Gaming', 'infobd-mega' ),
            'shop'      => __( '🛒 Shop', 'infobd-mega' ),
            'feat'      => __( '✨ Public Features', 'infobd-mega' ),
            'seo'       => __( '📈 SEO', 'infobd-mega' ),
            'perf'      => __( '⚡ Performance', 'infobd-mega' ),
            'ads'       => __( '💰 Ads', 'infobd-mega' ),
        );
        ?>
        <div class="wrap infobd-mega-wrap">
            <h1 class="infobd-title">⚡ Infobd Mega Plugin <small>v<?php echo esc_html( INFOBD_MEGA_VERSION ); ?></small></h1>
            <p class="infobd-tagline"><?php esc_html_e( '80+ features for your premium 3D news, gaming, and shop site.', 'infobd-mega' ); ?></p>

            <h2 class="nav-tab-wrapper infobd-tabs">
                <?php foreach ( $tabs as $tab => $label ) : ?>
                    <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'infobd-mega', 'tab' => $tab ), admin_url( 'admin.php' ) ) ); ?>" class="nav-tab <?php echo $current_tab === $tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html( $label ); ?></a>
                <?php endforeach; ?>
            </h2>

            <?php if ( 'dashboard' === $current_tab ) : ?>
                <?php $this->render_dashboard(); ?>
            <?php else : ?>
                <form method="post" action="options.php" class="infobd-form">
                    <?php settings_fields( 'infobd_mega_group' ); ?>
                    <?php $this->render_fields_for_tab( $current_tab ); ?>
                    <?php submit_button( __( 'Save Settings', 'infobd-mega' ), 'primary infobd-save' ); ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    private function render_dashboard() {
        $opts = Infobd_Mega_Options::all();
        $defs = $this->field_definitions();
        $stats = array(
            'features_total' => count( $defs ),
            'features_on'    => count( array_filter( array_intersect_key( $opts, array_filter( $defs, function($f){ return $f['type']==='toggle'; } ) ) ) ),
            'posts'          => wp_count_posts( 'post' )->publish,
            'pages'          => wp_count_posts( 'page' )->publish,
            'games'          => post_type_exists( 'infobd_game' ) ? wp_count_posts( 'infobd_game' )->publish : 0,
            'products'       => post_type_exists( 'infobd_product' ) ? wp_count_posts( 'infobd_product' )->publish : 0,
            'comments'       => wp_count_comments()->approved,
            'users'          => count_users()['total_users'],
        );
        ?>
        <div class="infobd-dash">
            <div class="infobd-stats-grid">
                <div class="stat-card sc-1"><strong><?php echo esc_html( $stats['features_on'] ); ?>/<?php echo esc_html( $stats['features_total'] ); ?></strong><span><?php esc_html_e( 'Features Active', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-2"><strong><?php echo esc_html( $stats['posts'] ); ?></strong><span><?php esc_html_e( 'Posts', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-3"><strong><?php echo esc_html( $stats['pages'] ); ?></strong><span><?php esc_html_e( 'Pages', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-4"><strong><?php echo esc_html( $stats['games'] ); ?></strong><span><?php esc_html_e( 'Games', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-5"><strong><?php echo esc_html( $stats['products'] ); ?></strong><span><?php esc_html_e( 'Products', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-6"><strong><?php echo esc_html( $stats['comments'] ); ?></strong><span><?php esc_html_e( 'Comments', 'infobd-mega' ); ?></span></div>
                <div class="stat-card sc-7"><strong><?php echo esc_html( $stats['users'] ); ?></strong><span><?php esc_html_e( 'Users', 'infobd-mega' ); ?></span></div>
            </div>

            <div class="infobd-dash-grid">
                <div class="infobd-card">
                    <h2>🚀 <?php esc_html_e( 'Quick Actions', 'infobd-mega' ); ?></h2>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="button button-primary">+ <?php esc_html_e( 'New Post', 'infobd-mega' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=infobd_game' ) ); ?>" class="button">🎮 <?php esc_html_e( 'New Game', 'infobd-mega' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=infobd_product' ) ); ?>" class="button">🛒 <?php esc_html_e( 'New Product', 'infobd-mega' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" class="button">🎨 <?php esc_html_e( 'Customize Theme', 'infobd-mega' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>" class="button">📋 <?php esc_html_e( 'Edit Menus', 'infobd-mega' ); ?></a>
                    <a href="<?php echo esc_url( admin_url( 'widgets.php' ) ); ?>" class="button">🧩 <?php esc_html_e( 'Manage Widgets', 'infobd-mega' ); ?></a>
                </div>

                <div class="infobd-card">
                    <h2>📋 <?php esc_html_e( 'Useful Shortcodes', 'infobd-mega' ); ?></h2>
                    <ul class="infobd-shortcodes">
                        <li><code>[infobd_trending count="6"]</code> — <?php esc_html_e( 'Trending posts', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_latest count="6" category="news"]</code> — <?php esc_html_e( 'Latest posts', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_products count="8"]</code> — <?php esc_html_e( 'Product grid', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_games count="8"]</code> — <?php esc_html_e( 'Games grid', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_categories]</code> — <?php esc_html_e( 'All categories', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_breadcrumbs]</code> — <?php esc_html_e( 'Breadcrumb nav', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_subscribe]</code> — <?php esc_html_e( 'Newsletter form', 'infobd-mega' ); ?></li>
                        <li><code>[infobd_social]</code> — <?php esc_html_e( 'Social links row', 'infobd-mega' ); ?></li>
                    </ul>
                </div>

                <div class="infobd-card">
                    <h2>🔒 <?php esc_html_e( 'Security Status', 'infobd-mega' ); ?></h2>
                    <ul class="infobd-checks">
                        <?php
                        $checks = array(
                            'sec_disable_xmlrpc'   => __( 'XML-RPC disabled', 'infobd-mega' ),
                            'sec_remove_version'   => __( 'WP version hidden', 'infobd-mega' ),
                            'sec_disable_user_enum'=> __( 'User enum blocked', 'infobd-mega' ),
                            'sec_login_attempts'   => __( 'Login attempts limited', 'infobd-mega' ),
                            'sec_security_headers' => __( 'Security headers sent', 'infobd-mega' ),
                            'sec_block_php_in_uploads' => __( 'PHP blocked in uploads', 'infobd-mega' ),
                        );
                        foreach ( $checks as $k => $label ) {
                            $on = ! empty( $opts[ $k ] );
                            echo '<li class="' . ( $on ? 'check-on' : 'check-off' ) . '">' . ( $on ? '✓' : '✗' ) . ' ' . esc_html( $label ) . '</li>';
                        }
                        ?>
                    </ul>
                </div>

                <div class="infobd-card">
                    <h2>©  <?php esc_html_e( 'Copyright Status', 'infobd-mega' ); ?></h2>
                    <ul class="infobd-checks">
                        <?php
                        $checks = array(
                            'cp_disable_right_click' => __( 'Right-click blocked', 'infobd-mega' ),
                            'cp_disable_copy'        => __( 'Copy blocked', 'infobd-mega' ),
                            'cp_disable_drag'        => __( 'Image drag blocked', 'infobd-mega' ),
                            'cp_disable_devtools'    => __( 'DevTools shortcut blocked', 'infobd-mega' ),
                            'cp_disable_view_source' => __( 'View Source blocked', 'infobd-mega' ),
                        );
                        foreach ( $checks as $k => $label ) {
                            $on = ! empty( $opts[ $k ] );
                            echo '<li class="' . ( $on ? 'check-on' : 'check-off' ) . '">' . ( $on ? '✓' : '✗' ) . ' ' . esc_html( $label ) . '</li>';
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_fields_for_tab( $tab ) {
        $defs = $this->field_definitions();
        $opts = Infobd_Mega_Options::all();
        echo '<div class="infobd-fields">';
        foreach ( $defs as $key => $def ) {
            if ( $def['tab'] !== $tab ) continue;
            $val = $opts[ $key ] ?? '';
            $name = INFOBD_MEGA_OPTION . '[' . $key . ']';
            echo '<div class="infobd-field infobd-field--' . esc_attr( $def['type'] ) . '">';
            echo '<div class="if-info"><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $def['label'] ) . '</strong></label>';
            if ( ! empty( $def['desc'] ) ) echo '<p class="if-desc">' . esc_html( $def['desc'] ) . '</p>';
            echo '</div><div class="if-control">';
            switch ( $def['type'] ) {
                case 'toggle':
                    echo '<label class="if-switch"><input type="hidden" name="' . esc_attr( $name ) . '" value="0"><input id="' . esc_attr( $key ) . '" type="checkbox" name="' . esc_attr( $name ) . '" value="1" ' . checked( $val, 1, false ) . '><span class="if-slider"></span></label>';
                    break;
                case 'number':
                    echo '<input type="number" id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" class="small-text">';
                    break;
                case 'select':
                    echo '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '">';
                    foreach ( $def['options'] as $ov => $ol ) {
                        echo '<option value="' . esc_attr( $ov ) . '" ' . selected( $val, $ov, false ) . '>' . esc_html( $ol ) . '</option>';
                    }
                    echo '</select>';
                    break;
                case 'textarea':
                    echo '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" rows="5" class="large-text code">' . esc_textarea( $val ) . '</textarea>';
                    break;
                case 'text':
                default:
                    echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $val ) . '" class="regular-text">';
                    break;
            }
            echo '</div></div>';
        }
        echo '</div>';
    }

    public function register_dashboard_widget() {
        wp_add_dashboard_widget( 'infobd_mega_dashboard', __( '⚡ Infobd Mega — Quick Stats', 'infobd-mega' ), array( $this, 'dashboard_widget' ) );
    }

    public function dashboard_widget() {
        $opts = Infobd_Mega_Options::all();
        $defs = $this->field_definitions();
        $on = count( array_filter( array_intersect_key( $opts, array_filter( $defs, function($f){ return $f['type']==='toggle'; } ) ) ) );
        $total = count( array_filter( $defs, function($f){ return $f['type']==='toggle'; } ) );
        ?>
        <p>
            <strong><?php echo esc_html( $on ); ?>/<?php echo esc_html( $total ); ?></strong>
            <?php esc_html_e( 'features active.', 'infobd-mega' ); ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=infobd-mega' ) ); ?>"><?php esc_html_e( 'Go to dashboard →', 'infobd-mega' ); ?></a>
        </p>
        <?php
    }
}
