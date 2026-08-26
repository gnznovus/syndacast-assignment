<?php
/**
 * MUU Hotel theme setup.
 *
 * @package MuuHotel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function muu_hotel_setup(): void {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/main.css' );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        )
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
    $version = wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'muu-hotel-main',
        get_theme_file_uri( 'assets/css/main.css' ),
        array(),
        $version
    );

    wp_enqueue_script(
        'muu-hotel-main',
        get_theme_file_uri( 'assets/js/main.js' ),
        array(),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'muu_hotel_enqueue_assets' );
