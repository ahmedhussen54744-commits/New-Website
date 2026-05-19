<?php
/**
 * Main template
 * @package Infobd_3D
 */
get_header(); ?>

<?php if ( is_home() && ! is_paged() ) : ?>
    <!-- HERO / FEATURED -->
    <?php
    $featured = new WP_Query( array(
        'posts_per_page'      => 3,
        'ignore_sticky_posts' => false,
        'meta_query'          => array(),
    ) );
    if ( $featured->have_posts() ) : ?>
        <section class="hero-section">
            <div class="container">
                <div class="hero-grid">
                    <?php $i = 0; while ( $featured->have_posts() ) : $featured->the_post(); $i++; ?>
                        <?php if ( 1 === $i ) : ?>
                            <article class="hero-main">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'infobd-hero' ); else echo '<div style="height:460px;background:var(--gradient-2);"></div>'; ?>
                                    <div class="hero-overlay">
                                        <?php $cat = get_the_category(); if ( $cat ) : ?>
                                            <span class="hero-category"><?php echo esc_html( $cat[0]->name ); ?></span>
                                        <?php endif; ?>
                                        <h2 class="hero-title"><?php the_title(); ?></h2>
                                        <div class="hero-meta">
                                            <span>&#128197; <?php echo esc_html( get_the_date() ); ?></span>
                                            <span>&#128100; <?php the_author(); ?></span>
                                            <span>&#128065; <?php echo esc_html( infobd_3d_get_views() ); ?></span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                            <div class="hero-side">
                        <?php else : ?>
                            <article>
                                <a href="<?php the_permalink(); ?>">
                                    <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'infobd-card' ); else echo '<div style="height:220px;background:var(--gradient-1);"></div>'; ?>
                                    <div class="hero-overlay">
                                        <?php $cat = get_the_category(); if ( $cat ) : ?>
                                            <span class="hero-category"><?php echo esc_html( $cat[0]->name ); ?></span>
                                        <?php endif; ?>
                                        <h3 class="hero-title"><?php echo esc_html( wp_trim_words( get_the_title(), 10 ) ); ?></h3>
                                    </div>
                                </a>
                            </article>
                        <?php endif; ?>
                    <?php endwhile; ?>
                            </div>
                </div>
            </div>
        </section>
    <?php wp_reset_postdata(); endif; ?>

    <!-- CATEGORIES STRIP -->
    <?php $cats = get_categories( array( 'number' => 6, 'orderby' => 'count', 'order' => 'DESC' ) ); if ( $cats ) : ?>
    <section class="cat-strip">
        <div class="container">
            <h2 class="section-title"><?php esc_html_e( 'Browse Categories', 'infobd-3d' ); ?></h2>
            <div class="cat-strip-grid">
                <?php
                $cat_icons = array( '&#128240;', '&#127918;', '&#128722;', '&#9917;', '&#127911;', '&#128187;', '&#128075;', '&#128396;' );
                foreach ( $cats as $idx => $c ) :
                    $icon = $cat_icons[ $idx % count( $cat_icons ) ];
                ?>
                    <a class="cat-card" href="<?php echo esc_url( get_category_link( $c->term_id ) ); ?>">
                        <span class="cat-card-icon"><?php echo $icon; ?></span>
                        <div class="cat-card-name"><?php echo esc_html( $c->name ); ?></div>
                        <div class="cat-card-count"><?php echo esc_html( sprintf( _n( '%d post', '%d posts', $c->count, 'infobd-3d' ), $c->count ) ); ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
<?php endif; ?>

<!-- MAIN CONTENT + SIDEBAR -->
<div class="container">
    <div class="main-area">
        <div class="content-area">
            <?php if ( is_home() && ! is_paged() ) : ?>
                <h2 class="section-title"><?php esc_html_e( 'Latest Posts', 'infobd-3d' ); ?></h2>
            <?php elseif ( is_search() ) : ?>
                <h2 class="section-title"><?php printf( esc_html__( 'Results for: %s', 'infobd-3d' ), '<em>' . esc_html( get_search_query() ) . '</em>' ); ?></h2>
            <?php elseif ( is_category() ) : ?>
                <h2 class="section-title"><?php single_cat_title(); ?></h2>
            <?php elseif ( is_tag() ) : ?>
                <h2 class="section-title">#<?php single_tag_title(); ?></h2>
            <?php elseif ( is_author() ) : ?>
                <h2 class="section-title"><?php the_post(); printf( esc_html__( 'Posts by %s', 'infobd-3d' ), esc_html( get_the_author() ) ); rewind_posts(); ?></h2>
            <?php endif; ?>

            <?php if ( have_posts() ) : ?>
                <div class="posts-grid">
                    <?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content' ); endwhile; ?>
                </div>

                <?php
                the_posts_pagination( array(
                    'mid_size'  => 2,
                    'prev_text' => '&laquo;',
                    'next_text' => '&raquo;',
                ) );
                ?>
            <?php else : ?>
                <div class="no-results">
                    <h2><?php esc_html_e( 'Nothing found', 'infobd-3d' ); ?></h2>
                    <p><?php esc_html_e( 'Try a different search or check back later.', 'infobd-3d' ); ?></p>
                    <?php get_search_form(); ?>
                </div>
            <?php endif; ?>
        </div>

        <aside class="site-sidebar">
            <?php if ( is_active_sidebar( 'sidebar-1' ) ) {
                dynamic_sidebar( 'sidebar-1' );
            } else {
                // Default popular posts widget
                $pop = new WP_Query( array(
                    'posts_per_page' => 5,
                    'meta_key'       => '_infobd_views',
                    'orderby'        => 'meta_value_num',
                    'order'          => 'DESC',
                ) );
                if ( $pop->have_posts() ) {
                    echo '<div class="widget"><h3 class="widget-title">' . esc_html__( 'Popular Posts', 'infobd-3d' ) . '</h3>';
                    while ( $pop->have_posts() ) : $pop->the_post(); ?>
                        <div class="popular-post">
                            <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'infobd-thumb' ); ?>
                            <div class="popular-post-content">
                                <h4><a href="<?php the_permalink(); ?>"><?php echo esc_html( wp_trim_words( get_the_title(), 9 ) ); ?></a></h4>
                                <time><?php echo esc_html( get_the_date() ); ?></time>
                            </div>
                        </div>
                    <?php endwhile;
                    echo '</div>';
                    wp_reset_postdata();
                }
                // Categories widget
                echo '<div class="widget"><h3 class="widget-title">' . esc_html__( 'Categories', 'infobd-3d' ) . '</h3><ul>';
                wp_list_categories( array( 'title_li' => '', 'show_count' => true ) );
                echo '</ul></div>';
                // Tag cloud
                echo '<div class="widget"><h3 class="widget-title">' . esc_html__( 'Tags', 'infobd-3d' ) . '</h3>';
                wp_tag_cloud( array( 'smallest' => 12, 'largest' => 16 ) );
                echo '</div>';
            } ?>
        </aside>
    </div>
</div>

<?php get_footer(); ?>
