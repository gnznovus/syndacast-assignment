<?php
/**
 * MUU Theme Controller bootstrap.
 *
 * @package MuuHotel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'MUU_EC_VERSION' ) ) {
    define( 'MUU_EC_VERSION', '0.2.0' );
}
if ( ! defined( 'MUU_EC_PATH' ) ) {
    define( 'MUU_EC_PATH', trailingslashit( __DIR__ ) );
}
if ( ! defined( 'MUU_EC_URL' ) ) {
    define( 'MUU_EC_URL', trailingslashit( get_theme_file_uri( 'inc/muu-element-controller' ) ) );
}

if ( ! class_exists( 'MUU_Element_Controller' ) ) {
    require_once MUU_EC_PATH . 'includes/class-muu-element-controller.php';
}

MUU_Element_Controller::instance();
