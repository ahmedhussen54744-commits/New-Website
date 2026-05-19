<?php
/**
 * Public Features — dark mode, font size, like, bookmark, lazy load, image zoom, share, TOC, etc.
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Features {

    public function __construct() {
        add_action( 'wp_body_open', array( $this, 'reader_toolbar' ) );
        add_action( 'wp_footer', array( $this, 'floating_share' ) );
        add_filter( 'the_content', array( $this, 'maybe_table_of_contents' ), 9 );
        add_filter( 'the_content', array( $this, 'append_post_actions' ), 20 );

        // Performance optimizations
        $opts = Infobd_Mega_Options::all();
        if ( ! empty( $opts['perf_disable_emoji'] ) ) {
            remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
            remove_action( 'wp_print_styles', 'print_emoji_styles' );
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );
            remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
            remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
            remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        }
        if ( ! empty( $opts['perf_disable_embeds'] ) ) {
            remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
            remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        }
        if ( ! empty( $opts['perf_dns_prefetch'] ) ) {
            add_action( 'wp_head', array( $this, 'dns_prefetch' ), 1 );
        }

        // AJAX likes
        add_action( 'wp_ajax_infobd_like', array( $this, 'ajax_like' ) );
        add_action( 'wp_ajax_nopriv_infobd_like', array( $this, 'ajax_like' ) );
    }

    public function dns_prefetch() {
        echo '<link rel="dns-prefetch" href="//fonts.googleapis.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//fonts.gstatic.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    }

    public function reader_toolbar() {
        $opts = Infobd_Mega_Options::all();
        if ( empty( $opts['feat_dark_mode_toggle'] ) && empty( $opts['feat_font_size_toggle'] ) && empty( $opts['feat_reading_mode'] ) ) return;
        ?>
        <div class="infobd-reader-tools" id="infobd-reader-tools" aria-label="<?php esc_attr_e( 'Reader tools', 'infobd-mega' ); ?>">
            <button class="irt-toggle" aria-label="<?php esc_attr_e( 'Toggle reader tools', 'infobd-mega' ); ?>" title="<?php esc_attr_e( 'Reader tools', 'infobd-mega' ); ?>">&#9881;</button>
            <div class="irt-panel">
                <?php if ( ! empty( $opts['feat_dark_mode_toggle'] ) ) : ?>
                    <button data-action="dark"><span>&#9728;/&#9789;</span><?php esc_html_e( 'Dark mode', 'infobd-mega' ); ?></button>
                <?php endif; ?>
                <?php if ( ! empty( $opts['feat_font_size_toggle'] ) ) : ?>
                    <div class="irt-row">
                        <button data-action="font-down" aria-label="<?php esc_attr_e( 'Decrease font', 'infobd-mega' ); ?>">A-</button>
                        <button data-action="font-reset" aria-label="<?php esc_attr_e( 'Reset font', 'infobd-mega' ); ?>">A</button>
                        <button data-action="font-up" aria-label="<?php esc_attr_e( 'Increase font', 'infobd-mega' ); ?>">A+</button>
                    </div>
                <?php endif; ?>
                <?php if ( ! empty( $opts['feat_reading_mode'] ) ) : ?>
                    <button data-action="reading"><?php esc_html_e( 'Reading mode', 'infobd-mega' ); ?></button>
                <?php endif; ?>
                <button data-action="print">&#128424; <?php esc_html_e( 'Print', 'infobd-mega' ); ?></button>
            </div>
        </div>
        <?php
    }

    public function floating_share() {
        if ( ! is_singular( 'post' ) ) return;
        if ( ! Infobd_Mega_Options::get( 'feat_floating_share' ) ) return;
        $url = urlencode( get_permalink() );
        $title = urlencode( get_the_title() );
        ?>
        <aside class="infobd-floating-share" aria-label="<?php esc_attr_e( 'Share', 'infobd-mega' ); ?>">
            <a class="ifs fb" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" title="Facebook">F</a>
            <a class="ifs tw" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>" title="Twitter">X</a>
            <a class="ifs wa" target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo $title; ?>%20<?php echo $url; ?>" title="WhatsApp">W</a>
            <a class="ifs tg" target="_blank" rel="noopener" href="https://t.me/share/url?url=<?php echo $url; ?>" title="Telegram">T</a>
            <button class="ifs cp" data-copy="<?php echo esc_url( get_permalink() ); ?>" title="<?php esc_attr_e( 'Copy link', 'infobd-mega' ); ?>">&#128279;</button>
        </aside>
        <?php
    }

    public function maybe_table_of_contents( $content ) {
        if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
        if ( ! Infobd_Mega_Options::get( 'feat_table_of_contents' ) ) return $content;
        if ( ! preg_match_all( '/<h([2-3])[^>]*>(.*?)<\/h\1>/i', $content, $m ) ) return $content;
        if ( count( $m[0] ) < 3 ) return $content;
        $toc = '<div class="infobd-toc"><h3>' . esc_html__( 'Table of Contents', 'infobd-mega' ) . '</h3><ol>';
        foreach ( $m[2] as $i => $heading ) {
            $level = $m[1][ $i ];
            $text = wp_strip_all_tags( $heading );
            $id = 'toc-' . sanitize_title( $text ) . '-' . $i;
            // Inject id into the heading
            $original = $m[0][ $i ];
            $with_id = '<h' . $level . ' id="' . esc_attr( $id ) . '">' . $heading . '</h' . $level . '>';
            $content = str_replace( $original, $with_id, $content );
            $indent = ( $level === '3' ) ? ' style="margin-left:18px;"' : '';
            $toc .= '<li' . $indent . '><a href="#' . esc_attr( $id ) . '">' . esc_html( $text ) . '</a></li>';
        }
        $toc .= '</ol></div>';
        return $toc . $content;
    }

    public function append_post_actions( $content ) {
        if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
        $id = get_the_ID();
        $likes = (int) get_post_meta( $id, '_infobd_likes', true );
        ob_start(); ?>
        <div class="infobd-post-actions">
            <?php if ( Infobd_Mega_Options::get( 'feat_post_like' ) ) : ?>
                <button class="infobd-like-btn" data-id="<?php echo esc_attr( $id ); ?>">
                    &hearts; <span class="ilb-count"><?php echo esc_html( $likes ); ?></span> <?php esc_html_e( 'Likes', 'infobd-mega' ); ?>
                </button>
            <?php endif; ?>
            <?php if ( Infobd_Mega_Options::get( 'feat_post_bookmark' ) ) : ?>
                <button class="infobd-bookmark-btn" data-id="<?php echo esc_attr( $id ); ?>">
                    &#128278; <?php esc_html_e( 'Bookmark', 'infobd-mega' ); ?>
                </button>
            <?php endif; ?>
        </div>
        <?php
        return $content . ob_get_clean();
    }

    public function ajax_like() {
        check_ajax_referer( 'infobd_mega_nonce', 'nonce' );
        $id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        if ( ! $id ) wp_send_json_error( 'invalid' );
        $ck = 'infobd_l_' . $id;
        if ( isset( $_COOKIE[ $ck ] ) ) wp_send_json_error( 'already_liked' );
        $count = (int) get_post_meta( $id, '_infobd_likes', true );
        $count++;
        update_post_meta( $id, '_infobd_likes', $count );
        setcookie( $ck, '1', time() + YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
        wp_send_json_success( array( 'count' => $count ) );
    }
}
