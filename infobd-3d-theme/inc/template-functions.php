<?php
/**
 * Template functions
 * @package Infobd_3D
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add a 3D parallax data attribute to body for JS
 */
function infobd_3d_add_data_attrs( $atts ) {
    $atts['data-3d'] = '1';
    return $atts;
}
add_filter( 'infobd_3d_body_attrs', 'infobd_3d_add_data_attrs' );

/**
 * Pingback header
 */
function infobd_3d_pingback() {
    if ( is_singular() && pings_open( get_queried_object() ) ) {
        printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
    }
}
add_action( 'wp_head', 'infobd_3d_pingback', 1 );

/**
 * Skip-link focus fix for older browsers
 */
function infobd_3d_skip_link_focus_fix() {
    ?>
    <script>
    (function(){var t,e,n,i=function(){"hidden"!==document.body.classList.contains("hidden")&&(e=document.activeElement,(t=window.location.hash.substring(1))&&(n=document.getElementById(t))&&(n.tabIndex||(n.tabIndex=-1),n.focus()))};window.addEventListener("hashchange",i,!1);})();
    </script>
    <?php
}
add_action( 'wp_print_footer_scripts', 'infobd_3d_skip_link_focus_fix' );
