<?php
/**
 * Page template
 * @package Infobd_3D
 */
get_header(); ?>

<div class="single-post-wrapper">
    <div class="container">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="single-post-hero">
                        <?php the_post_thumbnail( 'infobd-hero' ); ?>
                        <div class="single-post-hero-overlay">
                            <h1 class="single-post-title"><?php the_title(); ?></h1>
                        </div>
                    </div>
                <?php else : ?>
                    <h1 class="section-title"><?php the_title(); ?></h1>
                <?php endif; ?>
                <div class="single-post-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages(); ?>
                </div>
                <?php if ( comments_open() || get_comments_number() ) : ?>
                    <div class="comments-area"><?php comments_template(); ?></div>
                <?php endif; ?>
            </article>
        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>
