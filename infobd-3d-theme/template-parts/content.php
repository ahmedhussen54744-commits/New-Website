<?php
/**
 * Post card template part
 * @package Infobd_3D
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <div class="post-card-thumb">
        <a href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'infobd-card', array( 'loading' => 'lazy' ) ); ?>
            <?php else : ?>
                <div style="width:100%;height:100%;background:var(--gradient-2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;font-weight:900;"><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></div>
            <?php endif; ?>
        </a>

        <?php $cat = get_the_category(); if ( $cat ) : ?>
            <a href="<?php echo esc_url( get_category_link( $cat[0]->term_id ) ); ?>" class="post-card-cat"><?php echo esc_html( $cat[0]->name ); ?></a>
        <?php endif; ?>

        <div class="post-card-date">
            <strong><?php echo esc_html( get_the_date( 'd' ) ); ?></strong>
            <?php echo esc_html( get_the_date( 'M' ) ); ?>
        </div>
    </div>

    <div class="post-card-body">
        <h2 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>
        <p class="post-card-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <div class="post-card-meta">
            <span>&#128100; <?php the_author(); ?> &middot; <?php echo esc_html( infobd_3d_reading_time() ); ?></span>
            <a href="<?php the_permalink(); ?>" class="post-card-readmore"><?php esc_html_e( 'Read', 'infobd-3d' ); ?> &rarr;</a>
        </div>
    </div>
</article>
