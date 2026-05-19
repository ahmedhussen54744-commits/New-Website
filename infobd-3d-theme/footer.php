<?php
/**
 * Footer template
 * @package Infobd_3D
 */
?>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-widget">
                <h3><?php bloginfo( 'name' ); ?></h3>
                <p style="color:var(--text-muted); margin-bottom:14px;">
                    <?php echo esc_html( get_theme_mod( 'infobd_about', __( 'Infobd.online — Your premium 3D news, gaming, and shopping destination. Stay informed, entertained, and inspired every day.', 'infobd-3d' ) ) ); ?>
                </p>
                <form class="newsletter-form" onsubmit="event.preventDefault(); this.querySelector('button').innerText='&#10003; Subscribed';">
                    <input type="email" placeholder="<?php esc_attr_e( 'Your email', 'infobd-3d' ); ?>" required>
                    <button type="submit"><?php esc_html_e( 'Subscribe', 'infobd-3d' ); ?></button>
                </form>
            </div>

            <?php if ( is_active_sidebar( 'footer-2' ) ) : dynamic_sidebar( 'footer-2' ); else : ?>
                <div class="footer-widget">
                    <h3><?php esc_html_e( 'Categories', 'infobd-3d' ); ?></h3>
                    <ul>
                        <?php
                        $cats = get_categories( array( 'number' => 6, 'orderby' => 'count', 'order' => 'DESC' ) );
                        foreach ( $cats as $c ) {
                            echo '<li><a href="' . esc_url( get_category_link( $c->term_id ) ) . '">' . esc_html( $c->name ) . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'footer-3' ) ) : dynamic_sidebar( 'footer-3' ); else : ?>
                <div class="footer-widget">
                    <h3><?php esc_html_e( 'Quick Links', 'infobd-3d' ); ?></h3>
                    <?php
                    if ( has_nav_menu( 'footer' ) ) {
                        wp_nav_menu( array( 'theme_location' => 'footer', 'container' => false, 'depth' => 1 ) );
                    } else {
                        echo '<ul>';
                        echo '<li><a href="' . esc_url( home_url( '/about' ) ) . '">' . esc_html__( 'About', 'infobd-3d' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/contact' ) ) . '">' . esc_html__( 'Contact', 'infobd-3d' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/privacy' ) ) . '">' . esc_html__( 'Privacy', 'infobd-3d' ) . '</a></li>';
                        echo '<li><a href="' . esc_url( home_url( '/terms' ) ) . '">' . esc_html__( 'Terms', 'infobd-3d' ) . '</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>
            <?php endif; ?>

            <?php if ( is_active_sidebar( 'footer-4' ) ) : dynamic_sidebar( 'footer-4' ); else : ?>
                <div class="footer-widget">
                    <h3><?php esc_html_e( 'Latest Posts', 'infobd-3d' ); ?></h3>
                    <ul>
                        <?php
                        $latest = get_posts( array( 'numberposts' => 4 ) );
                        foreach ( $latest as $p ) {
                            echo '<li><a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . esc_html( wp_trim_words( $p->post_title, 8 ) ) . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-bottom">
            <p>
                &copy; <?php echo esc_html( date_i18n( 'Y' ) ); ?>
                <strong><?php bloginfo( 'name' ); ?></strong>.
                <?php esc_html_e( 'All rights reserved.', 'infobd-3d' ); ?>
                <?php esc_html_e( 'Content protected by copyright law.', 'infobd-3d' ); ?>
            </p>
        </div>
    </div>
</footer>

<button class="back-to-top" id="back-to-top" aria-label="<?php esc_attr_e( 'Back to top', 'infobd-3d' ); ?>">&uarr;</button>

<?php wp_footer(); ?>
</body>
</html>
