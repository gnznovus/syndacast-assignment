<?php
/**
 * MUU Hotel theme setup.
 *
 * The theme provides a blank canvas and global visual tokens. Elementor owns
 * page composition; the built-in MUU Theme Controller owns custom components.
 *
 * @package MuuHotel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_theme_file_path( 'inc/muu-element-controller/bootstrap.php' );

function muu_hotel_setup(): void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/main.css' );
    add_theme_support(
        'html5',
        array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
    );

    register_nav_menus(
        array(
            'primary' => __( 'Primary Menu', 'muu-hotel' ),
            'footer'  => __( 'Footer Menu', 'muu-hotel' ),
        )
    );
}
add_action( 'after_setup_theme', 'muu_hotel_setup' );

function muu_hotel_enqueue_assets(): void {
    $css_path = get_theme_file_path( 'assets/css/main.css' );

    wp_enqueue_style( 'muu-hotel-main', get_theme_file_uri( 'assets/css/main.css' ), array(), (string) filemtime( $css_path ) );
}
add_action( 'wp_enqueue_scripts', 'muu_hotel_enqueue_assets' );

/** Keep Elementor's editing canvas free of the theme's page-width constraint. */
function muu_hotel_elementor_page_classes( array $classes ): array {
    if ( did_action( 'elementor/loaded' ) && is_singular( 'page' ) ) {
        $classes[] = 'muu-elementor-page';
    }
    return $classes;
}
add_filter( 'body_class', 'muu_hotel_elementor_page_classes' );
