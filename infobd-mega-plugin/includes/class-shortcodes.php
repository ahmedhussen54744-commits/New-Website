<?php
/**
 * Shortcodes — trending posts, latest, products, games, social, breadcrumbs
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Shortcodes {

    public function __construct() {
        add_shortcode( 'infobd_trending', array( $this, 'trending' ) );
        add_shortcode( 'infobd_latest', array( $this, 'latest' ) );
        add_shortcode( 'infobd_products', array( $this, 'products' ) );
        add_shortcode( 'infobd_games', array( $this, 'games' ) );
        add_shortcode( 'infobd_categories', array( $this, 'categories' ) );
        add_shortcode( 'infobd_social', array( $this, 'social' ) );
        add_shortcode( 'infobd_subscribe', array( $this, 'subscribe' ) );
    }

    private function loop_grid( $query ) {
        if ( ! $query->have_posts() ) return '<p>' . esc_html__( 'No posts found.', 'infobd-mega' ) . '</p>';
        ob_start();
        echo '<div class="infobd-shortcode-grid">';
        while ( $query->have_posts() ) {
            $query->the_post();
            $thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'medium' ) : '';
            ?>
            <article class="infobd-sc-card">
                <a href="<?php the_permalink(); ?>">
                    <?php if ( $thumb ) : ?>
                        <div class="isc-thumb" style="background-image:url('<?php echo esc_url( $thumb ); ?>')"></div>
                    <?php else : ?>
                        <div class="isc-thumb" style="background:linear-gradient(135deg,#ff2d55,#7400b8)"></div>
                    <?php endif; ?>
                    <div class="isc-body">
                        <h3><?php echo esc_html( wp_trim_words( get_the_title(), 12 ) ); ?></h3>
                        <time><?php echo esc_html( get_the_date() ); ?></time>
                    </div>
                </a>
            </article>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();
        return ob_get_clean();
    }

    public function trending( $atts ) {
        $a = shortcode_atts( array( 'count' => 6, 'days' => 30 ), $atts );
        $q = new WP_Query( array(
            'posts_per_page' => (int) $a['count'],
            'meta_key'       => '_infobd_views',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
            'date_query'     => array( array( 'after' => (int) $a['days'] . ' days ago' ) ),
            'no_found_rows'  => true,
        ) );
        return $this->loop_grid( $q );
    }

    public function latest( $atts ) {
        $a = shortcode_atts( array( 'count' => 6, 'category' => '' ), $atts );
        $args = array( 'posts_per_page' => (int) $a['count'], 'no_found_rows' => true );
        if ( $a['category'] ) $args['category_name'] = sanitize_title( $a['category'] );
        return $this->loop_grid( new WP_Query( $args ) );
    }

    public function products( $atts ) {
        $a = shortcode_atts( array( 'count' => 8 ), $atts );
        return $this->loop_grid( new WP_Query( array(
            'post_type' => 'infobd_product',
            'posts_per_page' => (int) $a['count'],
            'no_found_rows' => true,
        ) ) );
    }

    public function games( $atts ) {
        $a = shortcode_atts( array( 'count' => 8 ), $atts );
        return $this->loop_grid( new WP_Query( array(
            'post_type' => 'infobd_game',
            'posts_per_page' => (int) $a['count'],
            'no_found_rows' => true,
        ) ) );
    }

    public function categories() {
        $cats = get_categories( array( 'number' => 12 ) );
        if ( ! $cats ) return '';
        $out = '<div class="infobd-cat-grid">';
        $icons = array( '&#128240;', '&#127918;', '&#128722;', '&#9917;', '&#127911;', '&#128187;' );
        foreach ( $cats as $i => $c ) {
            $icon = $icons[ $i % count( $icons ) ];
            $out .= '<a href="' . esc_url( get_category_link( $c->term_id ) ) . '" class="infobd-cat-tile"><span>' . $icon . '</span><strong>' . esc_html( $c->name ) . '</strong><small>' . sprintf( __( '%d posts', 'infobd-mega' ), $c->count ) . '</small></a>';
        }
        $out .= '</div>';
        return $out;
    }

    public function social() {
        $out = '<div class="infobd-social-row">';
        $networks = array(
            'facebook' => 'Facebook', 'twitter' => 'Twitter/X', 'youtube' => 'YouTube',
            'instagram' => 'Instagram', 'telegram' => 'Telegram',
        );
        foreach ( $networks as $key => $label ) {
            $url = get_theme_mod( 'infobd_' . $key, '' );
            if ( $url && '#' !== $url ) {
                $out .= '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" class="infobd-social-link" aria-label="' . esc_attr( $label ) . '">' . esc_html( $label ) . '</a>';
            }
        }
        $out .= '</div>';
        return $out;
    }

    public function subscribe() {
        ob_start(); ?>
        <form class="infobd-subscribe" onsubmit="event.preventDefault();this.querySelector('button').innerText='✓ Subscribed';this.querySelector('input').value='';">
            <input type="email" required placeholder="<?php esc_attr_e( 'Your email address', 'infobd-mega' ); ?>">
            <button type="submit"><?php esc_html_e( 'Subscribe', 'infobd-mega' ); ?></button>
        </form>
        <?php
        return ob_get_clean();
    }
}
