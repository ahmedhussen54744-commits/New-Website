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
    <!-- INLINE CSS - New simple dropdown menu style (cannot be cached) -->
    <style id="infobd-menu-styles">
        /* Hamburger toggle button - mobile only */
        .menu-toggle {
            display: none;
            background: linear-gradient(135deg, #ff2d55, #c70039);
            color: #fff;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            font-size: 22px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(255,45,85,0.4);
            transition: transform .25s ease;
            align-items: center;
            justify-content: center;
            padding: 0;
        }
        .menu-toggle:active { transform: scale(0.92); }
        .menu-toggle[aria-expanded="true"] {
            background: linear-gradient(135deg, #c70039, #900c3f);
        }

        /* Hide overlay on desktop */
        .menu-overlay { display: none; }

        /* ===== MOBILE STYLES (max 768px) ===== */
        @media (max-width: 768px) {
            .menu-toggle {
                display: inline-flex !important;
            }

            /* Mobile menu - DROPDOWN below header (NOT fullscreen panel) */
            #primary-menu,
            ul.nav-menu,
            .nav-menu {
                display: none !important;
                position: absolute !important;
                top: calc(100% + 8px) !important;
                right: 12px !important;
                left: 12px !important;
                width: auto !important;
                max-width: none !important;
                height: auto !important;
                max-height: 70vh !important;
                background: linear-gradient(180deg, #1a1a35, #131329) !important;
                flex-direction: column !important;
                gap: 4px !important;
                padding: 12px !important;
                border-radius: 16px !important;
                border: 1px solid rgba(255,45,85,0.3) !important;
                box-shadow:
                    0 20px 60px rgba(0,0,0,0.6),
                    0 0 0 1px rgba(255,255,255,0.05) inset,
                    0 0 30px rgba(255,45,85,0.2) !important;
                z-index: 9999 !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                list-style: none !important;
                margin: 0 !important;
                visibility: hidden;
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
                transform-origin: top center;
                transition: opacity .25s ease, transform .3s cubic-bezier(.2,.9,.3,1.4), visibility 0s .3s;
            }

            #primary-menu.open,
            ul.nav-menu.open,
            .nav-menu.open {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                transform: translateY(0) scale(1) !important;
                transition: opacity .25s ease, transform .3s cubic-bezier(.2,.9,.3,1.4), visibility 0s 0s !important;
            }

            /* Menu items */
            .nav-menu li {
                width: 100%;
                list-style: none;
                margin: 0;
                padding: 0;
                border-bottom: 1px solid rgba(255,255,255,0.06);
            }
            .nav-menu li:last-child { border-bottom: none; }

            .nav-menu li a {
                display: flex !important;
                align-items: center;
                width: 100%;
                padding: 14px 16px !important;
                color: #f0f0ff !important;
                font-weight: 600 !important;
                font-size: 16px !important;
                text-decoration: none !important;
                border-radius: 10px;
                background: transparent !important;
                transition: all .2s ease;
                transform: none !important;
            }
            .nav-menu li a::before {
                content: '\203A';
                margin-right: 12px;
                color: #ff2d55;
                font-size: 22px;
                font-weight: bold;
                background: none !important;
                position: static !important;
                opacity: 1 !important;
                transform: none !important;
                box-shadow: none !important;
                inset: auto !important;
                border-radius: 0 !important;
                z-index: auto !important;
            }
            .nav-menu li a:active {
                background: linear-gradient(135deg, #ff2d55, #c70039) !important;
                color: #fff !important;
            }
            .nav-menu li.current-menu-item > a,
            .nav-menu li.current_page_item > a {
                background: linear-gradient(135deg, #ff2d55, #c70039) !important;
                color: #fff !important;
                box-shadow: 0 4px 12px rgba(255,45,85,0.3);
            }
            .nav-menu li.current-menu-item > a::before,
            .nav-menu li.current_page_item > a::before {
                color: #fff !important;
            }

            /* Submenu items */
            .nav-menu .sub-menu {
                position: static !important;
                opacity: 1 !important;
                visibility: visible !important;
                transform: none !important;
                background: rgba(0,0,0,0.2) !important;
                border: none !important;
                border-radius: 8px !important;
                margin: 4px 0 4px 20px !important;
                padding: 4px !important;
                box-shadow: none !important;
            }
            .nav-menu .sub-menu li a {
                font-size: 14px !important;
                padding: 10px 14px !important;
            }

            /* Light backdrop overlay (click to close) */
            .menu-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.35);
                z-index: 9998;
                cursor: pointer;
                animation: infobdFadeIn .25s ease;
            }
            .menu-overlay.active { display: block; }
            @keyframes infobdFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
        }

        @media (min-width: 769px) {
            .menu-toggle { display: none !important; }
        }

        /* Make header position relative for dropdown */
        .main-navigation { position: relative; }
    </style>
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
            <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false" type="button">&#9776;</button>
            <?php
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'fallback_cb'    => 'infobd_3d_fallback_menu',
            ) );
            ?>
            <div class="menu-overlay" id="menu-overlay"></div>
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

<!-- INLINE MENU JS - Cannot be cached, ensures menu always works -->
<script>
(function(){
    function initMenu(){
        var toggle = document.querySelector('.menu-toggle');
        var menu = document.getElementById('primary-menu');
        var overlay = document.getElementById('menu-overlay');
        if (!toggle || !menu) return;

        // Force closed on every page load
        menu.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
        if (overlay) overlay.classList.remove('active');

        function openM(){
            menu.classList.add('open');
            toggle.setAttribute('aria-expanded', 'true');
            if (overlay) overlay.classList.add('active');
        }
        function closeM(){
            menu.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
            if (overlay) overlay.classList.remove('active');
        }

        toggle.addEventListener('click', function(e){
            e.preventDefault();
            e.stopPropagation();
            if (menu.classList.contains('open')) closeM(); else openM();
        });

        if (overlay){
            overlay.addEventListener('click', function(e){
                e.preventDefault();
                closeM();
            });
        }

        // Click outside menu closes it
        document.addEventListener('click', function(e){
            if (!menu.classList.contains('open')) return;
            if (menu.contains(e.target) || toggle.contains(e.target)) return;
            closeM();
        });

        // ESC key closes menu
        document.addEventListener('keydown', function(e){
            if ((e.key === 'Escape' || e.keyCode === 27) && menu.classList.contains('open')){
                closeM();
            }
        });

        // Click on any menu link closes menu
        var links = menu.getElementsByTagName('a');
        for (var i = 0; i < links.length; i++){
            (function(link){
                link.addEventListener('click', function(){
                    setTimeout(closeM, 100);
                });
            })(links[i]);
        }

        window.infobdMenu = { open: openM, close: closeM };
    }

    if (document.readyState === 'loading'){
        document.addEventListener('DOMContentLoaded', initMenu);
    } else {
        initMenu();
    }
})();
</script>
