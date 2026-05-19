<?php
/**
 * SEO — meta description, OG, Twitter Cards, JSON-LD, canonical, breadcrumbs
 *
 * @package Infobd_Mega
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class Infobd_Mega_SEO {

    public function __construct() {
        add_action( 'wp_head', array( $this, 'output_meta' ), 1 );
        add_shortcode( 'infobd_breadcrumbs', array( $this, 'breadcrumbs_shortcode' ) );
    }

    public function output_meta() {
        $opts = Infobd_Mega_Options::all();
        $title = wp_get_document_title();
        $url = ( is_singular() ? get_permalink() : home_url( add_query_arg( null, null ) ) );
        $desc = '';
        $img = '';

        if ( is_singular() ) {
            global $post;
            $desc = has_excerpt( $post->ID ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28 );
            if ( has_post_thumbnail( $post ) ) {
                $img = get_the_post_thumbnail_url( $post, 'large' );
            }
        } elseif ( is_home() || is_front_page() ) {
            $desc = get_bloginfo( 'description' );
        } elseif ( is_category() || is_tag() || is_tax() ) {
            $term = get_queried_object();
            if ( $term && ! empty( $term->description ) ) $desc = $term->description;
        } elseif ( is_search() ) {
            $desc = sprintf( __( 'Search results for "%s"', 'infobd-mega' ), get_search_query() );
        }

        $desc = wp_trim_words( wp_strip_all_tags( $desc ), 32 );

        echo "\n<!-- Infobd SEO -->\n";

        if ( ! empty( $opts['seo_meta_description'] ) && $desc ) {
            echo '<meta name="description" content="' . esc_attr( $desc ) . "\">\n";
        }
        if ( ! empty( $opts['seo_canonical'] ) ) {
            echo '<link rel="canonical" href="' . esc_url( $url ) . "\">\n";
        }
        if ( ! empty( $opts['seo_robots_meta'] ) ) {
            $robots = is_search() || is_404() ? 'noindex,follow' : 'index,follow,max-image-preview:large';
            echo '<meta name="robots" content="' . esc_attr( $robots ) . "\">\n";
        }
        if ( ! empty( $opts['seo_og_tags'] ) ) {
            echo '<meta property="og:locale" content="' . esc_attr( get_locale() ) . "\">\n";
            echo '<meta property="og:type" content="' . ( is_singular() ? 'article' : 'website' ) . "\">\n";
            echo '<meta property="og:title" content="' . esc_attr( $title ) . "\">\n";
            if ( $desc ) echo '<meta property="og:description" content="' . esc_attr( $desc ) . "\">\n";
            echo '<meta property="og:url" content="' . esc_url( $url ) . "\">\n";
            echo '<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . "\">\n";
            if ( $img ) echo '<meta property="og:image" content="' . esc_url( $img ) . "\">\n";
        }
        if ( ! empty( $opts['seo_twitter_cards'] ) ) {
            echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr( $title ) . "\">\n";
            if ( $desc ) echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . "\">\n";
            if ( $img ) echo '<meta name="twitter:image" content="' . esc_url( $img ) . "\">\n";
        }
        if ( ! empty( $opts['seo_json_ld'] ) && is_singular( 'post' ) ) {
            global $post;
            $author = get_userdata( $post->post_author );
            $ld = array(
                '@context' => 'https://schema.org',
                '@type'    => 'NewsArticle',
                'headline' => get_the_title(),
                'description' => $desc,
                'datePublished' => get_the_date( DATE_W3C ),
                'dateModified'  => get_the_modified_date( DATE_W3C ),
                'author'   => array( '@type' => 'Person', 'name' => $author ? $author->display_name : '' ),
                'publisher' => array(
                    '@type' => 'Organization',
                    'name'  => get_bloginfo( 'name' ),
                ),
                'mainEntityOfPage' => $url,
            );
            if ( $img ) $ld['image'] = $img;
            echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
        }
    }

    public function breadcrumbs_shortcode() {
        if ( is_home() || is_front_page() ) return '';
        $sep = ' <span style="color:#ff2d55;">/</span> ';
        $out = '<nav class="infobd-breadcrumbs" aria-label="breadcrumbs"><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'infobd-mega' ) . '</a>' . $sep;
        if ( is_category() || is_single() ) {
            if ( is_single() ) {
                $cat = get_the_category();
                if ( $cat ) $out .= '<a href="' . esc_url( get_category_link( $cat[0]->term_id ) ) . '">' . esc_html( $cat[0]->name ) . '</a>' . $sep;
                $out .= '<span>' . esc_html( get_the_title() ) . '</span>';
            } else {
                $out .= '<span>' . single_cat_title( '', false ) . '</span>';
            }
        } elseif ( is_page() ) {
            $out .= '<span>' . esc_html( get_the_title() ) . '</span>';
        } elseif ( is_search() ) {
            $out .= '<span>' . sprintf( esc_html__( 'Search: %s', 'infobd-mega' ), esc_html( get_search_query() ) ) . '</span>';
        } elseif ( is_tag() ) {
            $out .= '<span>#' . single_tag_title( '', false ) . '</span>';
        } elseif ( is_author() ) {
            $out .= '<span>' . esc_html( get_the_author() ) . '</span>';
        } elseif ( is_404() ) {
            $out .= '<span>' . esc_html__( '404', 'infobd-mega' ) . '</span>';
        }
        $out .= '</nav>';
        return $out;
    }
}
