<?php
/**
 * Single post template
 * @package Infobd_3D
 */
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="single-post-wrapper">
    <div class="container">

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <!-- HERO -->
            <div class="single-post-hero">
                <?php if ( has_post_thumbnail() ) : ?>
                    <?php the_post_thumbnail( 'infobd-hero', array( 'loading' => 'eager' ) ); ?>
                <?php else : ?>
                    <div style="height:380px;background:var(--gradient-1);"></div>
                <?php endif; ?>
                <div class="single-post-hero-overlay">
                    <?php $cat = get_the_category(); if ( $cat ) : ?>
                        <a href="<?php echo esc_url( get_category_link( $cat[0]->term_id ) ); ?>" class="single-post-cat"><?php echo esc_html( $cat[0]->name ); ?></a>
                    <?php endif; ?>
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                    <div class="single-post-meta">
                        <span class="single-post-meta-date">&#128197; <?php echo esc_html( get_the_date() ); ?></span>
                        <span>&#128100; <?php the_author(); ?></span>
                        <span>&#128337; <?php echo esc_html( infobd_3d_reading_time() ); ?></span>
                        <span>&#128065; <?php echo esc_html( infobd_3d_get_views() ); ?> <?php esc_html_e( 'views', 'infobd-3d' ); ?></span>
                        <?php if ( comments_open() ) : ?>
                            <span>&#128172; <?php echo esc_html( get_comments_number() ); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CONTENT -->
            <div class="single-post-content">
                <?php
                the_content();
                wp_link_pages( array(
                    'before' => '<div class="page-links"><span>' . esc_html__( 'Pages:', 'infobd-3d' ) . '</span>',
                    'after'  => '</div>',
                ) );
                ?>

                <!-- Tags -->
                <?php if ( has_tag() ) : ?>
                    <div class="post-tags">
                        <strong><?php esc_html_e( 'Tags:', 'infobd-3d' ); ?></strong>
                        <?php
                        $tags = get_the_tags();
                        foreach ( $tags as $tag ) {
                            echo '<a href="' . esc_url( get_tag_link( $tag->term_id ) ) . '" class="post-tag">#' . esc_html( $tag->name ) . '</a>';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Share buttons -->
                <div class="share-buttons">
                    <strong style="align-self:center;margin-right:6px;"><?php esc_html_e( 'Share:', 'infobd-3d' ); ?></strong>
                    <?php
                    $url = urlencode( get_permalink() );
                    $title = urlencode( get_the_title() );
                    ?>
                    <a class="share-btn fb" target="_blank" rel="noopener" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>">Facebook</a>
                    <a class="share-btn tw" target="_blank" rel="noopener" href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>">Twitter/X</a>
                    <a class="share-btn wa" target="_blank" rel="noopener" href="https://wa.me/?text=<?php echo $title; ?>%20<?php echo $url; ?>">WhatsApp</a>
                    <a class="share-btn tg" target="_blank" rel="noopener" href="https://t.me/share/url?url=<?php echo $url; ?>&text=<?php echo $title; ?>">Telegram</a>
                    <a class="share-btn ln" target="_blank" rel="noopener" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>">LinkedIn</a>
                    <button type="button" class="share-btn cp" data-copy="<?php echo esc_url( get_permalink() ); ?>"><?php esc_html_e( 'Copy Link', 'infobd-3d' ); ?></button>
                </div>
            </div>

            <!-- Author box -->
            <?php if ( get_the_author_meta( 'description' ) ) : ?>
                <div class="author-box">
                    <?php echo get_avatar( get_the_author_meta( 'ID' ), 80 ); ?>
                    <div>
                        <h4><?php the_author(); ?></h4>
                        <p><?php echo esc_html( get_the_author_meta( 'description' ) ); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related posts -->
            <?php
            $cats = wp_get_post_categories( get_the_ID() );
            if ( $cats ) :
                $related = new WP_Query( array(
                    'category__in'   => $cats,
                    'post__not_in'   => array( get_the_ID() ),
                    'posts_per_page' => 3,
                    'ignore_sticky_posts' => true,
                ) );
                if ( $related->have_posts() ) : ?>
                    <div class="related-posts">
                        <h2 class="section-title"><?php esc_html_e( 'You may also like', 'infobd-3d' ); ?></h2>
                        <div class="posts-grid">
                            <?php while ( $related->have_posts() ) : $related->the_post(); get_template_part( 'template-parts/content' ); endwhile; ?>
                        </div>
                    </div>
                <?php endif;
                wp_reset_postdata();
            endif;
            ?>

            <!-- Comments -->
            <?php if ( comments_open() || get_comments_number() ) : ?>
                <div class="comments-area">
                    <?php comments_template(); ?>
                </div>
            <?php endif; ?>
        </article>
    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
