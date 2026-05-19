<?php
/**
 * Gaming Zone — game CPT, review score, system requirements, leaderboard, etc.
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Gaming {

    public function __construct() {
        if ( ! Infobd_Mega_Options::get( 'game_zone_enable', 1 ) ) return;
        add_action( 'init', array( $this, 'register_cpt' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
        add_action( 'save_post_infobd_game', array( $this, 'save_meta' ) );
        add_filter( 'the_content', array( $this, 'append_game_box' ) );
    }

    public function register_cpt() {
        register_post_type( 'infobd_game', array(
            'labels' => array(
                'name'          => __( 'Games', 'infobd-mega' ),
                'singular_name' => __( 'Game', 'infobd-mega' ),
                'add_new_item'  => __( 'Add New Game', 'infobd-mega' ),
                'edit_item'     => __( 'Edit Game', 'infobd-mega' ),
                'menu_name'     => __( 'Games', 'infobd-mega' ),
            ),
            'public'        => true,
            'show_in_rest'  => true,
            'has_archive'   => true,
            'menu_icon'     => 'dashicons-games',
            'menu_position' => 22,
            'rewrite'       => array( 'slug' => 'games' ),
            'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        ) );
    }

    public function register_taxonomies() {
        register_taxonomy( 'game_genre', 'infobd_game', array(
            'label'        => __( 'Game Genre', 'infobd-mega' ),
            'public'       => true,
            'hierarchical' => true,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'game-genre' ),
        ) );
        register_taxonomy( 'game_platform', 'infobd_game', array(
            'label'        => __( 'Platform', 'infobd-mega' ),
            'public'       => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'game-platform' ),
        ) );
    }

    public function add_meta_box() {
        add_meta_box( 'infobd_game_details', __( 'Game Details', 'infobd-mega' ), array( $this, 'render_meta_box' ), 'infobd_game', 'normal', 'high' );
    }

    public function render_meta_box( $post ) {
        wp_nonce_field( 'infobd_game_meta', 'infobd_game_nonce' );
        $score = (float) get_post_meta( $post->ID, '_game_score', true );
        $req   = get_post_meta( $post->ID, '_game_sysreq', true );
        $rel   = get_post_meta( $post->ID, '_game_release', true );
        $dev   = get_post_meta( $post->ID, '_game_developer', true );
        ?>
        <p>
            <label><strong><?php esc_html_e( 'Review Score (0-10)', 'infobd-mega' ); ?></strong></label>
            <input type="number" step="0.1" min="0" max="10" name="_game_score" value="<?php echo esc_attr( $score ); ?>" class="regular-text">
        </p>
        <p>
            <label><strong><?php esc_html_e( 'Developer / Publisher', 'infobd-mega' ); ?></strong></label>
            <input type="text" name="_game_developer" value="<?php echo esc_attr( $dev ); ?>" class="regular-text">
        </p>
        <p>
            <label><strong><?php esc_html_e( 'Release Date', 'infobd-mega' ); ?></strong></label>
            <input type="date" name="_game_release" value="<?php echo esc_attr( $rel ); ?>">
        </p>
        <p>
            <label><strong><?php esc_html_e( 'System Requirements', 'infobd-mega' ); ?></strong></label>
            <textarea name="_game_sysreq" rows="6" class="large-text"><?php echo esc_textarea( $req ); ?></textarea>
        </p>
        <?php
    }

    public function save_meta( $post_id ) {
        if ( ! isset( $_POST['infobd_game_nonce'] ) || ! wp_verify_nonce( $_POST['infobd_game_nonce'], 'infobd_game_meta' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        foreach ( array( '_game_score', '_game_developer', '_game_release', '_game_sysreq' ) as $f ) {
            if ( isset( $_POST[ $f ] ) ) {
                update_post_meta( $post_id, $f, sanitize_textarea_field( wp_unslash( $_POST[ $f ] ) ) );
            }
        }
    }

    public function append_game_box( $content ) {
        if ( ! is_singular( 'infobd_game' ) || ! in_the_loop() || ! is_main_query() ) return $content;
        $id = get_the_ID();
        $score = (float) get_post_meta( $id, '_game_score', true );
        $dev   = get_post_meta( $id, '_game_developer', true );
        $rel   = get_post_meta( $id, '_game_release', true );
        $req   = get_post_meta( $id, '_game_sysreq', true );
        ob_start(); ?>
        <div class="infobd-game-box">
            <h3><?php esc_html_e( 'Game Info', 'infobd-mega' ); ?></h3>
            <div class="infobd-game-grid">
                <?php if ( $score > 0 ) : ?>
                    <div class="infobd-game-score">
                        <div class="score-circle" style="--score:<?php echo esc_attr( $score * 10 ); ?>">
                            <span><?php echo esc_html( number_format( $score, 1 ) ); ?></span>
                        </div>
                        <small><?php esc_html_e( 'Our Score', 'infobd-mega' ); ?></small>
                    </div>
                <?php endif; ?>
                <ul class="infobd-game-meta">
                    <?php if ( $dev ) : ?><li><strong><?php esc_html_e( 'Developer', 'infobd-mega' ); ?>:</strong> <?php echo esc_html( $dev ); ?></li><?php endif; ?>
                    <?php if ( $rel ) : ?><li><strong><?php esc_html_e( 'Release', 'infobd-mega' ); ?>:</strong> <?php echo esc_html( $rel ); ?></li><?php endif; ?>
                </ul>
            </div>
            <?php if ( $req ) : ?>
                <h4><?php esc_html_e( 'System Requirements', 'infobd-mega' ); ?></h4>
                <pre class="infobd-game-sysreq"><?php echo esc_html( $req ); ?></pre>
            <?php endif; ?>
        </div>
        <?php
        return $content . ob_get_clean();
    }
}
