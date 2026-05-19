<?php
/**
 * Post Link / Slug Editor — quick edit slug + custom permalink override
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Link_Editor {

    public function __construct() {
        // Custom permalink override metabox
        add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
        add_action( 'save_post', array( $this, 'save_metabox' ) );
        // Filter post link to override
        add_filter( 'post_link', array( $this, 'override_link' ), 10, 2 );
        add_filter( 'page_link', array( $this, 'override_link' ), 10, 2 );
        add_filter( 'post_type_link', array( $this, 'override_link' ), 10, 2 );

        // Inline slug column in posts list
        add_filter( 'manage_posts_columns', array( $this, 'add_slug_column' ) );
        add_action( 'manage_posts_custom_column', array( $this, 'render_slug_column' ), 10, 2 );

        // Quick edit slug field
        add_action( 'quick_edit_custom_box', array( $this, 'quick_edit_slug' ), 10, 2 );

        // AJAX endpoint to update slug from frontend (admin only)
        add_action( 'wp_ajax_infobd_update_slug', array( $this, 'ajax_update_slug' ) );
    }

    public function add_metabox() {
        $types = get_post_types( array( 'public' => true ), 'names' );
        foreach ( $types as $t ) {
            if ( 'attachment' === $t ) continue;
            add_meta_box( 'infobd_link_editor', __( 'Custom Permalink', 'infobd-mega' ), array( $this, 'render_metabox' ), $t, 'side', 'high' );
        }
    }

    public function render_metabox( $post ) {
        wp_nonce_field( 'infobd_link_editor', 'infobd_link_editor_nonce' );
        $custom = get_post_meta( $post->ID, '_infobd_custom_url', true );
        $slug = $post->post_name;
        ?>
        <p>
            <label><strong><?php esc_html_e( 'Slug', 'infobd-mega' ); ?></strong></label>
            <input type="text" name="infobd_post_slug" value="<?php echo esc_attr( $slug ); ?>" style="width:100%;">
        </p>
        <p>
            <label><strong><?php esc_html_e( 'Custom URL (full)', 'infobd-mega' ); ?></strong></label>
            <input type="url" name="infobd_custom_url" value="<?php echo esc_url( $custom ); ?>" placeholder="https://example.com/..." style="width:100%;">
            <small><?php esc_html_e( 'If filled, this URL replaces the post permalink everywhere on the site.', 'infobd-mega' ); ?></small>
        </p>
        <?php
    }

    public function save_metabox( $post_id ) {
        if ( ! isset( $_POST['infobd_link_editor_nonce'] ) || ! wp_verify_nonce( $_POST['infobd_link_editor_nonce'], 'infobd_link_editor' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        if ( isset( $_POST['infobd_custom_url'] ) ) {
            $url = esc_url_raw( wp_unslash( $_POST['infobd_custom_url'] ) );
            if ( $url ) update_post_meta( $post_id, '_infobd_custom_url', $url );
            else delete_post_meta( $post_id, '_infobd_custom_url' );
        }
        if ( isset( $_POST['infobd_post_slug'] ) ) {
            $slug = sanitize_title( wp_unslash( $_POST['infobd_post_slug'] ) );
            $current = get_post_field( 'post_name', $post_id );
            if ( $slug && $slug !== $current ) {
                remove_action( 'save_post', array( $this, 'save_metabox' ) );
                wp_update_post( array( 'ID' => $post_id, 'post_name' => $slug ) );
                add_action( 'save_post', array( $this, 'save_metabox' ) );
            }
        }
    }

    public function override_link( $url, $post ) {
        $id = is_object( $post ) ? $post->ID : (int) $post;
        $custom = get_post_meta( $id, '_infobd_custom_url', true );
        return $custom ? esc_url( $custom ) : $url;
    }

    public function add_slug_column( $cols ) {
        $cols['infobd_slug'] = __( 'Slug / Link', 'infobd-mega' );
        return $cols;
    }

    public function render_slug_column( $col, $post_id ) {
        if ( 'infobd_slug' !== $col ) return;
        $slug = get_post_field( 'post_name', $post_id );
        $custom = get_post_meta( $post_id, '_infobd_custom_url', true );
        echo '<code>' . esc_html( $slug ) . '</code>';
        if ( $custom ) echo '<br><small style="color:#ff2d55;">' . esc_html__( 'Custom:', 'infobd-mega' ) . ' ' . esc_html( $custom ) . '</small>';
    }

    public function quick_edit_slug( $col, $post_type ) {
        if ( 'infobd_slug' !== $col ) return;
        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php esc_html_e( 'Slug', 'infobd-mega' ); ?></span>
                    <span class="input-text-wrap"><input type="text" name="post_name" class="text"></span>
                </label>
            </div>
        </fieldset>
        <?php
    }

    public function ajax_update_slug() {
        check_ajax_referer( 'infobd_mega_nonce', 'nonce' );
        $id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $slug = isset( $_POST['slug'] ) ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : '';
        if ( ! $id || ! $slug || ! current_user_can( 'edit_post', $id ) ) wp_send_json_error( 'forbidden' );
        wp_update_post( array( 'ID' => $id, 'post_name' => $slug ) );
        wp_send_json_success( array( 'slug' => $slug, 'url' => get_permalink( $id ) ) );
    }
}
