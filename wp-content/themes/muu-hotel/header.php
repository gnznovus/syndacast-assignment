<?php
/**
 * Site header.
 *
 * @package MuuHotel
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'muu-hotel' ); ?></a>

    <header class="site-header" role="banner">
        <button class="site-header__menu-toggle" type="button" aria-expanded="false" aria-controls="primary-menu-panel">
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle navigation', 'muu-hotel' ); ?></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
            <span aria-hidden="true"></span>
        </button>

        <div class="site-header__brand">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="site-header__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">muu</a>
            <?php endif; ?>
        </div>

        <nav class="site-header__utility" aria-label="<?php esc_attr_e( 'Utility navigation', 'muu-hotel' ); ?>">
            <a href="#offers"><?php esc_html_e( 'Offers', 'muu-hotel' ); ?></a>
            <a href="#"><?php esc_html_e( 'Shop', 'muu-hotel' ); ?></a>
            <a class="site-header__availability" href="#"><?php esc_html_e( 'Check Availability', 'muu-hotel' ); ?></a>
        </nav>

        <div id="primary-menu-panel" class="site-header__panel" hidden>
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'site-header__menu',
                    'fallback_cb'    => false,
                )
            );
            ?>
        </div>
    </header>
