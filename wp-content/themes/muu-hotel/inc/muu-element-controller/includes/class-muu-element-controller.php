<?php
/** MUU theme controller. */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class MUU_Element_Controller {
    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_fonts' ), 5 );
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'enqueue_fonts' ) );
        add_filter( 'elementor/fonts/groups', array( $this, 'register_elementor_font_group' ) );
        add_filter( 'elementor/fonts/additional_fonts', array( $this, 'register_elementor_fonts' ) );
        add_shortcode( 'muu_nav_lefttab', array( $this, 'render_nav_lefttab' ) );
        add_shortcode( 'muu_orange_shape', array( $this, 'render_orange_shape' ) );
        add_shortcode( 'muu_contact_form', array( $this, 'render_contact_form' ) );
    }

    public static function defaults(): array {
        return array(
            'logo_text'          => 'muu',
            'logo_url'           => home_url( '/' ),
            'offers_label'       => 'OFFERS',
            'offers_url'         => '#offers',
            'shop_label'         => 'SHOP',
            'shop_url'           => '#',
            'availability_label' => 'CHECK AVAILABILITY',
            'availability_url'   => '#',
            'side_label'         => 'LOREM IPSUM DO',
            'language_label'     => 'EN | TH',
            'panel_eyebrow'      => 'Lorem ipsum',
            'panel_headline'     => '- ONSECTETUR ADIPISCING ELIT PELLENTESQUE.',
            'booking_month'      => 'SEP',
            'booking_day'        => '22',
            'booking_url'        => '#',
            'youtube_url'        => '#',
            'tiktok_url'         => '#',
            'instagram_url'      => '#',
            'panel_width_percent'=> 29,
            'target_selector'    => '.muu-hero-host',
            'header_height'      => 83,
            'rail_width'         => 74,
            'rail_height_percent'=> 100,
            'text_color'         => '#ffffff',
            'line_color'         => 'rgba(255,255,255,0.35)',
            'rail_color'         => 'rgba(0,0,0,0.50)',
            'custom_css'         => '',
        );
    }

    public static function options(): array {
        return wp_parse_args( (array) get_option( 'muu_ec_nav_lefttab', array() ), self::defaults() );
    }

    public function enqueue_assets(): void {
        wp_enqueue_style( 'muu-element-controller', MUU_EC_URL . 'assets/css/frontend.css', array(), (string) filemtime( MUU_EC_PATH . 'assets/css/frontend.css' ) );
        wp_enqueue_script( 'muu-element-controller', MUU_EC_URL . 'assets/js/frontend.js', array(), (string) filemtime( MUU_EC_PATH . 'assets/js/frontend.js' ), true );
    }

    public function register_elementor_font_group( array $groups ): array {
        $groups['muu_fonts'] = __( 'MUU Fonts', 'muu-element-controller' );
        return $groups;
    }

    public function register_elementor_fonts( array $fonts ): array {
        $fonts['Butler'] = 'muu_fonts';
        $fonts['HK Grotesk'] = 'muu_fonts';
        return $fonts;
    }

    public function enqueue_fonts(): void {
        $handle = 'muu-element-controller-fonts';
        wp_register_style( $handle, false, array(), MUU_EC_VERSION );
        wp_enqueue_style( $handle );

        $butler = trailingslashit( get_theme_file_uri( 'assets/fonts/Butler' ) );
        $hk     = trailingslashit( get_theme_file_uri( 'assets/fonts/HKGrotesk' ) );
        $faces  = array(
            array( 'Butler', $butler . 'Butler.woff2', 400 ),
            array( 'Butler', $butler . 'Butler-Medium.woff2', 500 ),
            array( 'Butler', $butler . 'Butler-Bold.woff2', 700 ),
            array( 'Butler', $butler . 'Butler-ExtraBold.woff2', 800 ),
            array( 'Butler', $butler . 'Butler-Black.woff2', 900 ),
            array( 'HK Grotesk', $hk . 'HKGrotesk-Regular.woff2', 400 ),
            array( 'HK Grotesk', $hk . 'HKGrotesk-Medium.woff2', 500 ),
            array( 'HK Grotesk', $hk . 'HKGrotesk-SemiBold.woff2', 600 ),
            array( 'HK Grotesk', $hk . 'HKGrotesk-Bold.woff2', 700 ),
        );
        $css = '';
        foreach ( $faces as $face ) {
            $css .= sprintf(
                '@font-face{font-family:"%1$s";src:url("%2$s") format("woff2");font-style:normal;font-weight:%3$d;font-display:swap;}',
                esc_attr( $face[0] ), esc_url( $face[1] ), absint( $face[2] )
            );
        }
        $css .= ':root{--muu-font-display:"Butler",Georgia,serif;--muu-font-body:"HK Grotesk",Arial,sans-serif;}';
        wp_add_inline_style( $handle, $css );
    }

    public function register_settings(): void {
        register_setting(
            'muu_ec_settings',
            'muu_ec_nav_lefttab',
            array( 'type' => 'array', 'sanitize_callback' => array( $this, 'sanitize_settings' ), 'default' => self::defaults() )
        );
    }

    public function sanitize_settings( $input ): array {
        $input    = is_array( $input ) ? $input : array();
        $defaults = self::defaults();
        $output   = array();

        foreach ( array( 'logo_text', 'offers_label', 'shop_label', 'availability_label', 'side_label', 'language_label', 'panel_eyebrow', 'panel_headline', 'booking_month', 'booking_day', 'target_selector' ) as $key ) {
            $output[ $key ] = sanitize_text_field( $input[ $key ] ?? $defaults[ $key ] );
        }
        foreach ( array( 'logo_url', 'offers_url', 'shop_url', 'availability_url', 'booking_url', 'youtube_url', 'tiktok_url', 'instagram_url' ) as $key ) {
            $output[ $key ] = esc_url_raw( $input[ $key ] ?? $defaults[ $key ] );
        }
        foreach ( array( 'header_height', 'rail_width' ) as $key ) {
            $output[ $key ] = max( 1, min( 3000, absint( $input[ $key ] ?? $defaults[ $key ] ) ) );
        }
        $output['rail_height_percent'] = max( 1, min( 100, absint( $input['rail_height_percent'] ?? 100 ) ) );
        $output['panel_width_percent'] = max( 20, min( 100, absint( $input['panel_width_percent'] ?? 29 ) ) );
        foreach ( array( 'text_color', 'line_color', 'rail_color' ) as $key ) {
            $output[ $key ] = sanitize_text_field( $input[ $key ] ?? $defaults[ $key ] );
        }
        $output['custom_css'] = wp_strip_all_tags( $input['custom_css'] ?? '' );
        return $output;
    }

    public function render_nav_lefttab( $atts = array() ): string {
        $options = self::options();
        $raw_atts = is_array( $atts ) ? $atts : array();
        $render_lefttab = true;
        if ( array_key_exists( 'left-tab', $raw_atts ) ) {
            $render_lefttab = filter_var( $raw_atts['left-tab'], FILTER_VALIDATE_BOOLEAN );
        } elseif ( array_key_exists( 'left_tab', $raw_atts ) ) {
            $render_lefttab = filter_var( $raw_atts['left_tab'], FILTER_VALIDATE_BOOLEAN );
        }
        $atts    = shortcode_atts( array(
            'class' => '', 'target' => '', 'left-tab' => 'true', 'left_tab' => '',
            'logo-color' => '', 'logo_color' => '', 'nav-color' => '', 'nav_color' => '',
            'divider-color' => '', 'divider_color' => '',
            'mobile-logo-color' => '', 'mobile_logo_color' => '',
            'mobile-nav-color' => '', 'mobile_nav_color' => '',
            'mobile-divider-color' => '', 'mobile_divider_color' => '',
        ), $raw_atts, 'muu_nav_lefttab' );
        $target  = '' !== trim( (string) $atts['target'] ) ? sanitize_text_field( $atts['target'] ) : $options['target_selector'];
        $logo_color_value = '' !== trim( (string) $atts['logo-color'] ) ? $atts['logo-color'] : $atts['logo_color'];
        $nav_color_value = '' !== trim( (string) $atts['nav-color'] ) ? $atts['nav-color'] : $atts['nav_color'];
        $divider_color_value = '' !== trim( (string) $atts['divider-color'] ) ? $atts['divider-color'] : $atts['divider_color'];
        $logo_color = sanitize_hex_color( $logo_color_value ) ?: $options['text_color'];
        $nav_color = sanitize_hex_color( $nav_color_value ) ?: $options['text_color'];
        $divider_color = sanitize_hex_color( $divider_color_value ) ?: $options['line_color'];

        $mobile_logo_color_value = '' !== trim( (string) $atts['mobile-logo-color'] ) ? $atts['mobile-logo-color'] : $atts['mobile_logo_color'];
        $mobile_nav_color_value = '' !== trim( (string) $atts['mobile-nav-color'] ) ? $atts['mobile-nav-color'] : $atts['mobile_nav_color'];
        $mobile_divider_color_value = '' !== trim( (string) $atts['mobile-divider-color'] ) ? $atts['mobile-divider-color'] : $atts['mobile_divider_color'];
        $mobile_logo_color = sanitize_hex_color( $mobile_logo_color_value ) ?: $logo_color;
        $mobile_nav_color = sanitize_hex_color( $mobile_nav_color_value ) ?: $nav_color;
        $mobile_divider_color = sanitize_hex_color( $mobile_divider_color_value ) ?: $divider_color;

        $style = sprintf(
            '--muu-ec-header-max:%1$dpx;--muu-ec-rail-max:%2$dpx;--muu-ec-rail-height:%3$d%%;--muu-ec-text:%4$s;--muu-ec-line:%5$s;--muu-ec-rail:%6$s;--muu-ec-panel-percent:%7$d%%;--muu-ec-logo-color:%8$s;--muu-ec-nav-color:%9$s;',
            absint( $options['header_height'] ), absint( $options['rail_width'] ), absint( $options['rail_height_percent'] ),
            esc_attr( $options['text_color'] ), esc_attr( $divider_color ), esc_attr( $options['rail_color'] ), absint( $options['panel_width_percent'] ),
            esc_attr( $logo_color ), esc_attr( $nav_color )
        );
        $style .= sprintf(
            '--muu-ec-mobile-logo-color:%1$s;--muu-ec-mobile-nav-color:%2$s;--muu-ec-mobile-line:%3$s;',
            esc_attr( $mobile_logo_color ), esc_attr( $mobile_nav_color ), esc_attr( $mobile_divider_color )
        );

        $panel_id = wp_unique_id( 'muu-ec-panel-' );
        $language_parts = array_map( 'trim', explode( '|', (string) $options['language_label'], 2 ) );
        $active_language = $language_parts[0] ?? '';
        $inactive_language = $language_parts[1] ?? '';

        ob_start();
        ?>
        <div class="muu-ec-nav-lefttab <?php echo esc_attr( $render_lefttab ? 'muu-ec-with-lefttab' : 'muu-ec-without-lefttab' ); ?> <?php echo esc_attr( $atts['class'] ); ?>" data-target-selector="<?php echo esc_attr( $target ); ?>" style="<?php echo esc_attr( $style ); ?>">
            <?php if ( $render_lefttab ) : ?>
            <aside class="muu-ec-lefttab" id="<?php echo esc_attr( $panel_id ); ?>" aria-hidden="true" inert>
                <div class="muu-ec-panel-content">
                    <p class="muu-ec-language"><span class="muu-ec-language-active"><?php echo esc_html( $active_language ); ?></span><?php if ( '' !== $inactive_language ) : ?> | <?php echo esc_html( $inactive_language ); ?><?php endif; ?></p>
                    <p class="muu-ec-panel-eyebrow"><?php echo esc_html( $options['panel_eyebrow'] ); ?></p>
                    <h2 class="muu-ec-panel-headline"><?php echo esc_html( $options['panel_headline'] ); ?></h2>
                    <a class="muu-ec-booking" href="<?php echo esc_url( $options['booking_url'] ); ?>">
                        <span class="muu-ec-booking-month"><?php echo esc_html( $options['booking_month'] ); ?></span>
                        <span class="muu-ec-booking-day"><?php echo esc_html( $options['booking_day'] ); ?></span>
                    </a>
                    <button class="muu-ec-panel-close" type="button" aria-label="<?php esc_attr_e( 'Collapse panel', 'muu-element-controller' ); ?>">
                        <img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/click-hide.svg' ); ?>" alt="">
                    </button>
                </div>
                <nav class="muu-ec-socials" aria-label="<?php esc_attr_e( 'Social media', 'muu-element-controller' ); ?>">
                    <a href="<?php echo esc_url( $options['youtube_url'] ); ?>" aria-label="YouTube"><img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/social-1.svg' ); ?>" alt=""></a>
                    <a href="<?php echo esc_url( $options['tiktok_url'] ); ?>" aria-label="TikTok"><img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/social-2.svg' ); ?>" alt=""></a>
                    <a class="muu-ec-instagram" href="<?php echo esc_url( $options['instagram_url'] ); ?>" aria-label="Instagram"><span></span></a>
                </nav>
            </aside>
            <?php endif; ?>
            <header class="muu-ec-navbar">
                <?php if ( $render_lefttab ) : ?>
                <button class="muu-ec-menu-toggle" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $panel_id ); ?>" aria-label="<?php esc_attr_e( 'Open menu', 'muu-element-controller' ); ?>">
                    <img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/burger.svg' ); ?>" alt="" width="42" height="27">
                </button>
                <?php endif; ?>
                <a class="muu-ec-logo" href="<?php echo esc_url( $options['logo_url'] ); ?>"><?php echo esc_html( $options['logo_text'] ); ?></a>
                <nav class="muu-ec-utility" aria-label="<?php esc_attr_e( 'Utility navigation', 'muu-element-controller' ); ?>">
                    <a href="<?php echo esc_url( $options['offers_url'] ); ?>"><?php echo esc_html( $options['offers_label'] ); ?></a>
                    <a href="<?php echo esc_url( $options['shop_url'] ); ?>"><?php echo esc_html( $options['shop_label'] ); ?></a>
                    <a class="muu-ec-availability" href="<?php echo esc_url( $options['availability_url'] ); ?>"><?php echo esc_html( $options['availability_label'] ); ?></a>
                </nav>
            </header>
            <?php if ( $render_lefttab ) : ?>
            <p class="muu-ec-side-label"><?php echo esc_html( $options['side_label'] ); ?></p>
            <?php endif; ?>
        </div>
        <?php if ( '' !== trim( $options['custom_css'] ) ) : ?><style><?php echo wp_strip_all_tags( $options['custom_css'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></style><?php endif; ?>
        <?php
        return (string) ob_get_clean();
    }

    private static function css_length( $value, string $fallback ): string {
        $value = trim( (string) $value );
        return preg_match( '/^-?(?:\d+|\d*\.\d+)(?:%|px|rem|em|vw|vh|cqw|cqh)$/', $value ) ? $value : $fallback;
    }

    public function render_orange_shape( $atts = array() ): string {
        $options = self::options();
        $atts = shortcode_atts( array(
            'target' => $options['target_selector'], 'left' => '20%', 'top' => '12%',
            'width' => '28%', 'height' => 'auto', 'rotate' => '0', 'flip_x' => 'true',
            'flip_y' => 'false', 'color' => '#ff6a00', 'opacity' => '0.85', 'class' => '',
        ), $atts, 'muu_orange_shape' );

        $left    = self::css_length( $atts['left'], '20%' );
        $top     = self::css_length( $atts['top'], '12%' );
        $width   = self::css_length( $atts['width'], '28%' );
        $height  = 'auto' === strtolower( trim( (string) $atts['height'] ) ) ? 'auto' : self::css_length( $atts['height'], 'auto' );
        $rotate  = max( -360, min( 360, (float) $atts['rotate'] ) );
        $flip_x  = filter_var( $atts['flip_x'], FILTER_VALIDATE_BOOLEAN ) ? -1 : 1;
        $flip_y  = filter_var( $atts['flip_y'], FILTER_VALIDATE_BOOLEAN ) ? -1 : 1;
        $opacity = max( 0, min( 1, (float) $atts['opacity'] ) );
        $color   = sanitize_hex_color( $atts['color'] ) ?: '#fe5000';
        $style   = sprintf(
            '--muu-shape-left:%1$s;--muu-shape-top:%2$s;--muu-shape-width:%3$s;--muu-shape-height:%4$s;--muu-shape-rotate:%5$sdeg;--muu-shape-flip-x:%6$d;--muu-shape-flip-y:%7$d;--muu-shape-color:%8$s;--muu-shape-opacity:%9$s;',
            $left, $top, $width, $height, $rotate, $flip_x, $flip_y, $color, $opacity
        );

        return sprintf(
            '<div class="muu-ec-orange-shape %1$s" data-target-selector="%2$s" style="%3$s" aria-hidden="true"><span></span></div>',
            esc_attr( $atts['class'] ), esc_attr( sanitize_text_field( $atts['target'] ) ), esc_attr( $style )
        );
    }

    public function render_contact_form( $atts = array() ): string {
        $atts = shortcode_atts( array(
            'id' => 0,
            'class' => '',
        ), $atts, 'muu_contact_form' );
        $form_id = absint( $atts['id'] );
        if ( ! $form_id || ! shortcode_exists( 'contact-form-7' ) ) {
            return current_user_can( 'edit_pages' )
                ? '<p class="muu-ec-contact-config-error">' . esc_html__( 'Set a valid Contact Form 7 ID, for example: [muu_contact_form id="254"]', 'muu-element-controller' ) . '</p>'
                : '';
        }

        return sprintf(
            '<div class="muu-ec-contact-form-wrap %1$s">%2$s</div>',
            esc_attr( $atts['class'] ),
            do_shortcode( '[contact-form-7 id="' . $form_id . '"]' )
        );
    }
}
