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
if ( ! class_exists( 'MUU_Footer_Controller' ) ) {
    require_once MUU_EC_PATH . 'includes/class-muu-footer-controller.php';
}

MUU_Element_Controller::instance();
MUU_Footer_Controller::instance();

add_action(
    'wp_enqueue_scripts',
    static function (): void {
        $footer_css = MUU_EC_PATH . 'assets/css/footer.css';
        if ( file_exists( $footer_css ) ) {
            wp_enqueue_style( 'muu-footer', MUU_EC_URL . 'assets/css/footer.css', array( 'muu-element-controller' ), (string) filemtime( $footer_css ) );
        }
    },
    20
);
