<?php
/**
 * 404 template
 * @package Infobd_3D
 */
get_header(); ?>

<div class="single-post-wrapper">
    <div class="container">
        <div class="error-404">
            <h1>404</h1>
            <h2><?php esc_html_e( 'Page not found', 'infobd-3d' ); ?></h2>
            <p><?php esc_html_e( 'The page you are looking for might have been moved, renamed, or temporarily unavailable.', 'infobd-3d' ); ?></p>
            <p style="margin-top:20px;">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn"><?php esc_html_e( 'Back to Home', 'infobd-3d' ); ?></a>
            </p>
            <div style="margin-top:30px; max-width:500px; margin-inline:auto;">
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
