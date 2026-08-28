<?php
/** MUU footer shortcode and admin settings. */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class MUU_Footer_Controller {
    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_shortcode( 'muu_footer', array( $this, 'render_footer' ) );
    }

    public static function defaults(): array {
        return array(
            'background_image_id'  => 0,
            'slh_image_id'         => 0,
            'logo_text'            => 'muu',
            'logo_url'             => home_url( '/' ),
            'gallery_label'        => 'Gallery',
            'gallery_url'          => '#',
            'health_label'         => 'Health & Safety',
            'health_url'           => '#',
            'sustainability_label' => 'Sustainability',
            'sustainability_url'   => '#',
            'privacy_label'        => 'Privacy Policy',
            'privacy_url'          => '#',
            'contact_label'        => 'Contact',
            'contact_url'          => home_url( '/contact/' ),
            'follow_label'         => 'FOLLOW US',
            'youtube_url'          => '#',
            'tiktok_url'           => '#',
            'instagram_url'        => '#',
            'reservations_label'   => 'RESERVATIONS',
            'reservations_phone'   => '+66 (0) 2 090 9000',
            'reservations_fax'     => '+66 (0) 2 090 9090',
            'reservations_email'   => 'rsvn.akt8@theakyra.com',
            'inquiries_label'      => 'OTHER INQUIRIES',
            'inquiries_phone'      => '+66 (0) 2 090 9000',
            'inquiries_fax'        => '+66 (0) 2 090 9090',
            'inquiries_email'      => 'fd.akt8@theakyra.com',
            'address'              => "88/333 Sukhumvit 55, North Klongton,\nWattana District, Bangkok 10110",
            'copyright'            => 'Copyright © 2022 MUU Hotel. All Rights Reserved.',
        );
    }

    public static function options(): array {
        return wp_parse_args( (array) get_option( 'muu_footer_settings', array() ), self::defaults() );
    }

    public function register_settings(): void {
        register_setting(
            'muu_footer_settings_group',
            'muu_footer_settings',
            array(
                'type'              => 'array',
                'sanitize_callback' => array( $this, 'sanitize_settings' ),
                'default'           => self::defaults(),
            )
        );
    }

    private static function sanitize_link_value( $value ): string {
        $value = trim( (string) $value );

        if ( '' === $value || '#' === $value ) {
            return $value;
        }

        if ( str_starts_with( $value, '#' ) || str_starts_with( $value, '/' ) ) {
            return sanitize_text_field( $value );
        }

        return esc_url_raw( $value );
    }

    public function sanitize_settings( $input ): array {
        $input    = is_array( $input ) ? $input : array();
        $defaults = self::defaults();
        $output   = array();

        foreach ( array( 'background_image_id', 'slh_image_id' ) as $key ) {
            $output[ $key ] = absint( $input[ $key ] ?? 0 );
        }

        foreach ( array(
            'logo_text', 'gallery_label', 'health_label', 'sustainability_label', 'privacy_label', 'contact_label',
            'follow_label', 'reservations_label', 'reservations_phone', 'reservations_fax', 'inquiries_label',
            'inquiries_phone', 'inquiries_fax', 'copyright'
        ) as $key ) {
            $output[ $key ] = sanitize_text_field( $input[ $key ] ?? $defaults[ $key ] );
        }

        foreach ( array(
            'logo_url', 'gallery_url', 'health_url', 'sustainability_url', 'privacy_url', 'contact_url',
            'youtube_url', 'tiktok_url', 'instagram_url'
        ) as $key ) {
            $output[ $key ] = self::sanitize_link_value( $input[ $key ] ?? $defaults[ $key ] );
        }

        foreach ( array( 'reservations_email', 'inquiries_email' ) as $key ) {
            $output[ $key ] = sanitize_email( $input[ $key ] ?? $defaults[ $key ] );
        }

        $output['address'] = sanitize_textarea_field( $input['address'] ?? $defaults['address'] );
        return $output;
    }

    private static function phone_href( string $phone ): string {
        return 'tel:' . preg_replace( '/[^0-9+]/', '', $phone );
    }

    private static function icon( string $type ): string {
        $paths = array(
            'phone' => '<path d="M6.6 2.5 9 2l1.1 4-2.2 1.1a13.1 13.1 0 0 0 5 5L14 9.9l4 1.1-.5 2.4c-.3 1.6-1.8 2.7-3.4 2.4C8 14.8 3.2 10 2.2 3.9 1.9 2.3 3 .8 4.6.5Z"/>',
            'fax'   => '<path d="M5 3h10v5H5zM3 8h14a2 2 0 0 1 2 2v6h-4v3H5v-3H1v-6a2 2 0 0 1 2-2Zm4 6v3h6v-3H7Z"/>',
            'mail'  => '<path d="M2 4h16v12H2V4Zm2 2v.3l6 4.3 6-4.3V6H4Zm12 8V8.8l-6 4.2-6-4.2V14h12Z"/>',
            'pin'   => '<path d="M10 1a6 6 0 0 1 6 6c0 4.3-6 11-6 11S4 11.3 4 7a6 6 0 0 1 6-6Zm0 3a3 3 0 1 0 0 6 3 3 0 0 0 0-6Z"/>',
        );
        return '<svg viewBox="0 0 20 20" aria-hidden="true" focusable="false">' . ( $paths[ $type ] ?? '' ) . '</svg>';
    }

    public function render_footer( $atts = array() ): string {
        $options = self::options();
        $atts = shortcode_atts(
            array( 'class' => '' ),
            is_array( $atts ) ? $atts : array(),
            'muu_footer'
        );

        $background_url   = $options['background_image_id'] ? wp_get_attachment_image_url( absint( $options['background_image_id'] ), 'full' ) : '';
        $background_style = $background_url ? '--muu-footer-background:url(' . esc_url( $background_url ) . ');' : '';
        $slh              = $options['slh_image_id'] ? wp_get_attachment_image( absint( $options['slh_image_id'] ), 'medium', false, array( 'class' => 'muu-footer__slh', 'loading' => 'lazy' ) ) : '';

        wp_enqueue_style( 'muu-footer', MUU_EC_URL . 'assets/css/footer.css', array(), (string) filemtime( MUU_EC_PATH . 'assets/css/footer.css' ) );

        $nav_items = array(
            array( $options['gallery_label'], $options['gallery_url'] ),
            array( $options['health_label'], $options['health_url'] ),
            array( $options['sustainability_label'], $options['sustainability_url'] ),
            array( $options['privacy_label'], $options['privacy_url'] ),
            array( $options['contact_label'], $options['contact_url'] ),
        );

        ob_start();
        ?>
        <footer class="muu-footer <?php echo esc_attr( $atts['class'] ); ?>" style="<?php echo esc_attr( $background_style ); ?>">
            <div class="muu-footer__inner">
                <nav class="muu-footer__nav" aria-label="<?php esc_attr_e( 'Footer navigation', 'muu-element-controller' ); ?>">
                    <?php foreach ( $nav_items as $item ) : ?>
                        <a href="<?php echo esc_url( $item[1] ); ?>"><?php echo esc_html( $item[0] ); ?></a>
                    <?php endforeach; ?>
                </nav>

                <div class="muu-footer__brand">
                    <a class="muu-footer__logo" href="<?php echo esc_url( $options['logo_url'] ); ?>"><?php echo esc_html( $options['logo_text'] ); ?></a>
                    <p class="muu-footer__follow-title"><?php echo esc_html( $options['follow_label'] ); ?></p>
                    <nav class="muu-footer__socials" aria-label="<?php esc_attr_e( 'Social media', 'muu-element-controller' ); ?>">
                        <a href="<?php echo esc_url( $options['youtube_url'] ); ?>" aria-label="YouTube"><img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/social-1.svg' ); ?>" alt=""></a>
                        <a href="<?php echo esc_url( $options['tiktok_url'] ); ?>" aria-label="TikTok"><img src="<?php echo esc_url( MUU_EC_URL . 'assets/images/social-2.svg' ); ?>" alt=""></a>
                        <a class="muu-footer__instagram" href="<?php echo esc_url( $options['instagram_url'] ); ?>" aria-label="Instagram"><span></span></a>
                    </nav>
                    <?php echo wp_kses_post( $slh ); ?>
                </div>

                <div class="muu-footer__contacts">
                    <section>
                        <h2><?php echo esc_html( $options['reservations_label'] ); ?></h2>
                        <p><?php echo self::icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( self::phone_href( $options['reservations_phone'] ) ); ?>"><?php echo esc_html( $options['reservations_phone'] ); ?></a></p>
                        <p><?php echo self::icon( 'fax' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $options['reservations_fax'] ); ?></span></p>
                        <p><?php echo self::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( antispambot( $options['reservations_email'] ) ); ?>"><?php echo esc_html( antispambot( $options['reservations_email'] ) ); ?></a></p>
                    </section>

                    <section>
                        <h2><?php echo esc_html( $options['inquiries_label'] ); ?></h2>
                        <p><?php echo self::icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( self::phone_href( $options['inquiries_phone'] ) ); ?>"><?php echo esc_html( $options['inquiries_phone'] ); ?></a></p>
                        <p><?php echo self::icon( 'fax' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $options['inquiries_fax'] ); ?></span></p>
                        <p><?php echo self::icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="mailto:<?php echo esc_attr( antispambot( $options['inquiries_email'] ) ); ?>"><?php echo esc_html( antispambot( $options['inquiries_email'] ) ); ?></a></p>
                        <div class="muu-footer__address-row"><?php echo self::icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><address><?php echo nl2br( esc_html( $options['address'] ) ); ?></address></div>
                    </section>
                </div>
            </div>

            <div class="muu-footer__copyright"><?php echo esc_html( $options['copyright'] ); ?></div>
        </footer>
        <?php
        return (string) ob_get_clean();
    }
}
