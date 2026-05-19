<?php
/**
 * Header template
 *
 * @package Infobd_3D
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a1a">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="preloader" id="infobd-preloader" aria-hidden="true">
    <div class="preloader-cube">
        <div></div><div></div><div></div><div></div><div></div><div></div>
    </div>
</div>

<a class="screen-reader-text skip-link" href="#main-content"><?php esc_html_e( 'Skip to content', 'infobd-3d' ); ?></a>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container top-bar-inner">
        <div>
            <span class="top-date">
                &#128197; <?php echo esc_html( wp_date( get_option( 'date_format' ) . ' &middot; l' ) ); ?>
            </span>
        </div>
        <div class="top-social">
            <?php
            $socials = array(
                'facebook'  => get_theme_mod( 'infobd_facebook', '#' ),
                'twitter'   => get_theme_mod( 'infobd_twitter', '#' ),
                'youtube'   => get_theme_mod( 'infobd_youtube', '#' ),
                'instagram' => get_theme_mod( 'infobd_instagram', '#' ),
                'telegram'  => get_theme_mod( 'infobd_telegram', '#' ),
            );
            $icons = array(
                'facebook'  => 'F',
                'twitter'   => 'X',
                'youtube'   => 'Y',
                'instagram' => 'I',
                'telegram'  => 'T',
            );
            foreach ( $socials as $key => $url ) {
                if ( $url && '#' !== $url ) {
                    echo '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $key ) . '">' . esc_html( $icons[ $key ] ) . '</a>';
                }
            }
            ?>
        </div>
    </div>
</div>

<header class="site-header">
    <div class="container header-inner">
        <div class="site-branding">
            <?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo"><?php bloginfo( 'name' ); ?></a>
            <?php endif; ?>
        </div>

        <nav class="main-navigation" aria-label="<?php esc_attr_e( 'Primary', 'infobd-3d' ); ?>">
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">&#9776;</button>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'infobd_3d_fallback_menu',
            ) );
            ?>
        </nav>

        <div class="header-search">
            <button class="header-search-toggle" aria-label="<?php esc_attr_e( 'Search', 'infobd-3d' ); ?>">&#128270;</button>
            <form role="search" method="get" class="header-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="search" name="s" placeholder="<?php esc_attr_e( 'Search news, posts, products...', 'infobd-3d' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
            </form>
        </div>
    </div>
</header>

<?php $breaking = infobd_3d_breaking_news(); if ( $breaking ) : ?>
<div class="breaking-news">
    <div class="container breaking-news-inner">
        <span class="breaking-label">&#128226; <?php esc_html_e( 'Breaking', 'infobd-3d' ); ?></span>
        <div class="breaking-ticker"><span><?php echo wp_kses_post( $breaking ); ?></span></div>
    </div>
</div>
<?php endif; ?>

<main id="main-content" class="site-main">
