<?php
/**
 * Theme Customizer
 * @package Infobd_3D
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function infobd_3d_customize_register( $wp_customize ) {
    // Section: Branding
    $wp_customize->add_section( 'infobd_branding', array(
        'title'    => __( 'Infobd Branding', 'infobd-3d' ),
        'priority' => 30,
    ) );

    $wp_customize->add_setting( 'infobd_about', array(
        'default'           => __( 'Infobd.online — Your premium 3D news destination.', 'infobd-3d' ),
        'sanitize_callback' => 'sanitize_textarea_field',
    ) );
    $wp_customize->add_control( 'infobd_about', array(
        'label'   => __( 'About text (footer)', 'infobd-3d' ),
        'section' => 'infobd_branding',
        'type'    => 'textarea',
    ) );

    // Section: Social
    $wp_customize->add_section( 'infobd_social', array(
        'title'    => __( 'Social Links', 'infobd-3d' ),
        'priority' => 35,
    ) );
    $networks = array( 'facebook' => 'Facebook', 'twitter' => 'Twitter/X', 'youtube' => 'YouTube', 'instagram' => 'Instagram', 'telegram' => 'Telegram' );
    foreach ( $networks as $key => $label ) {
        $wp_customize->add_setting( 'infobd_' . $key, array( 'sanitize_callback' => 'esc_url_raw', 'default' => '' ) );
        $wp_customize->add_control( 'infobd_' . $key, array(
            'label'   => $label,
            'section' => 'infobd_social',
            'type'    => 'url',
        ) );
    }

    // Section: Colors
    $wp_customize->add_setting( 'infobd_primary_color', array(
        'default'           => '#ff2d55',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'infobd_primary_color', array(
        'label'   => __( 'Primary color', 'infobd-3d' ),
        'section' => 'colors',
    ) ) );
}
add_action( 'customize_register', 'infobd_3d_customize_register' );

// Output dynamic CSS based on customizer settings
function infobd_3d_customizer_css() {
    $primary = get_theme_mod( 'infobd_primary_color', '#ff2d55' );
    if ( $primary && '#ff2d55' !== $primary ) {
        echo '<style id="infobd-3d-customizer">:root{--primary:' . esc_attr( $primary ) . ';}</style>';
    }
}
add_action( 'wp_head', 'infobd_3d_customizer_css' );
