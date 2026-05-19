<?php
/**
 * Comments template
 * @package Infobd_3D
 */
if ( post_password_required() ) return; ?>

<div id="comments" class="comments-area-inner">
    <?php if ( have_comments() ) : ?>
        <h3 class="section-title">
            <?php
            $count = get_comments_number();
            printf( esc_html( _n( '%s Comment', '%s Comments', $count, 'infobd-3d' ) ), number_format_i18n( $count ) );
            ?>
        </h3>
        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
                'avatar_size'=> 50,
            ) );
            ?>
        </ol>
        <?php the_comments_pagination(); ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'infobd-3d' ); ?></p>
    <?php endif; ?>

    <?php
    comment_form( array(
        'title_reply'         => __( 'Leave a comment', 'infobd-3d' ),
        'class_submit'        => 'submit',
        'comment_notes_before'=> '',
    ) );
    ?>
</div>
