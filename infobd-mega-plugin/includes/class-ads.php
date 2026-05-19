<?php
/**
 * Ads & Monetization
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_Ads {

    public function __construct() {
        add_action( 'wp_head', array( $this, 'header_ad' ), 5 );
        add_filter( 'the_content', array( $this, 'inpost_ad' ) );
    }

    public function header_ad() {
        if ( ! Infobd_Mega_Options::get( 'ads_header_enable' ) ) return;
        $code = Infobd_Mega_Options::get( 'ads_header_code', '' );
        if ( $code ) echo $code; // intentional, code may be ad scripts (admin-only setting)
    }

    public function inpost_ad( $content ) {
        if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
        if ( ! Infobd_Mega_Options::get( 'ads_inpost_enable' ) ) return $content;
        $code = Infobd_Mega_Options::get( 'ads_inpost_code', '' );
        if ( ! $code ) return $content;
        // Insert after 3rd paragraph
        $paras = explode( '</p>', $content );
        if ( count( $paras ) > 3 ) {
            $paras[2] .= '</p><div class="infobd-inpost-ad">' . $code . '</div>';
            return implode( '</p>', $paras );
        }
        return $content . '<div class="infobd-inpost-ad">' . $code . '</div>';
    }
}
