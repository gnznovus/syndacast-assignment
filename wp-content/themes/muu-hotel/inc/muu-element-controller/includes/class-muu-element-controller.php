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
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'wp_ajax_muu_controller_panel', array( $this, 'ajax_render_panel' ) );
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

    public function add_settings_page(): void {
        add_menu_page(
            __( 'MUU Theme Controller', 'muu-element-controller' ),
            __( 'MUU Controller', 'muu-element-controller' ),
            'manage_options',
            'muu-theme-controller',
            array( $this, 'render_settings_page' ),
            'dashicons-admin-customizer',
            58
        );
    }

    public function enqueue_admin_assets(): void {
        if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'muu-theme-controller' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        $css_path = MUU_EC_PATH . 'assets/css/admin.css';
        $js_path  = MUU_EC_PATH . 'assets/js/admin.js';

        wp_enqueue_style(
            'muu-element-controller-admin',
            MUU_EC_URL . 'assets/css/admin.css',
            array(),
            file_exists( $css_path ) ? (string) filemtime( $css_path ) : MUU_EC_VERSION
        );
        wp_enqueue_script(
            'muu-element-controller-admin',
            MUU_EC_URL . 'assets/js/admin.js',
            array(),
            file_exists( $js_path ) ? (string) filemtime( $js_path ) : MUU_EC_VERSION,
            true
        );
        wp_localize_script(
            'muu-element-controller-admin',
            'muuControllerAdmin',
            array(
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'muu_controller_panel' ),
            )
        );
    }

    private static function normalize_admin_panel( string $tab, string $section ): array {
        $tab = in_array( $tab, array( 'overview', 'shortcodes' ), true ) ? $tab : 'overview';
        if ( 'shortcodes' !== $tab ) {
            return array( $tab, '' );
        }

        $section = in_array( $section, array( 'navigation', 'footer' ), true ) ? $section : 'navigation';
        return array( $tab, $section );
    }

    public function ajax_render_panel(): void {
        check_ajax_referer( 'muu_controller_panel', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'You do not have permission to manage theme settings.', 'muu-element-controller' ) ), 403 );
        }

        $tab     = sanitize_key( wp_unslash( $_POST['tab'] ?? 'overview' ) );
        $section = sanitize_key( wp_unslash( $_POST['section'] ?? '' ) );
        list( $tab, $section ) = self::normalize_admin_panel( $tab, $section );

        ob_start();
        $this->render_admin_panel( $tab, $section );
        wp_send_json_success(
            array(
                'html'    => (string) ob_get_clean(),
                'tab'     => $tab,
                'section' => $section,
            )
        );
    }

    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tab     = sanitize_key( wp_unslash( $_GET['tab'] ?? 'overview' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $section = sanitize_key( wp_unslash( $_GET['section'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        list( $tab, $section ) = self::normalize_admin_panel( $tab, $section );
        ?>
        <div class="wrap muu-controller-admin">
            <div class="muu-controller-header">
                <div>
                    <h1><?php esc_html_e( 'MUU Theme Controller', 'muu-element-controller' ); ?></h1>
                    <p><?php esc_html_e( 'Manage reusable theme components from one place.', 'muu-element-controller' ); ?></p>
                </div>
            </div>

            <nav class="nav-tab-wrapper muu-controller-tabs" aria-label="<?php esc_attr_e( 'Theme controller sections', 'muu-element-controller' ); ?>">
                <a class="nav-tab <?php echo 'overview' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=overview' ) ); ?>" data-muu-tab="overview"><?php esc_html_e( 'Overview', 'muu-element-controller' ); ?></a>
                <a class="nav-tab <?php echo 'shortcodes' === $tab ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=navigation' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="navigation"><?php esc_html_e( 'Shortcodes', 'muu-element-controller' ); ?></a>
            </nav>

            <div id="muu-controller-panel" class="muu-controller-panel" aria-live="polite">
                <?php $this->render_admin_panel( $tab, $section ); ?>
            </div>
        </div>
        <?php
    }

    private function render_admin_panel( string $tab, string $section ): void {
        if ( 'shortcodes' === $tab ) {
            $this->render_shortcode_panel( $section );
            return;
        }

        $this->render_overview_panel();
    }

    private function render_overview_panel(): void {
        $theme      = wp_get_theme();
        $elementor  = did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
        $cf7        = shortcode_exists( 'contact-form-7' );
        $shortcodes = array( 'muu_nav_lefttab', 'muu_orange_shape', 'muu_contact_form', 'muu_footer' );
        ?>
        <section class="muu-admin-section">
            <div class="muu-admin-section__heading">
                <h2><?php esc_html_e( 'Overview', 'muu-element-controller' ); ?></h2>
                <p><?php esc_html_e( 'A quick health check for the custom theme layer and its reusable components.', 'muu-element-controller' ); ?></p>
            </div>

            <div class="muu-status-grid">
                <article class="muu-status-card">
                    <span class="muu-status-card__label"><?php esc_html_e( 'Theme', 'muu-element-controller' ); ?></span>
                    <strong><?php echo esc_html( $theme->get( 'Name' ) ?: 'MUU Hotel' ); ?></strong>
                    <span><?php echo esc_html( sprintf( __( 'Version %s', 'muu-element-controller' ), $theme->get( 'Version' ) ?: '—' ) ); ?></span>
                </article>
                <article class="muu-status-card">
                    <span class="muu-status-card__label"><?php esc_html_e( 'Elementor', 'muu-element-controller' ); ?></span>
                    <strong class="<?php echo $elementor ? 'is-ok' : 'is-warning'; ?>"><?php echo esc_html( $elementor ? __( 'Available', 'muu-element-controller' ) : __( 'Not detected', 'muu-element-controller' ) ); ?></strong>
                    <span><?php esc_html_e( 'Page composition dependency', 'muu-element-controller' ); ?></span>
                </article>
                <article class="muu-status-card">
                    <span class="muu-status-card__label"><?php esc_html_e( 'Contact Form 7', 'muu-element-controller' ); ?></span>
                    <strong class="<?php echo $cf7 ? 'is-ok' : 'is-warning'; ?>"><?php echo esc_html( $cf7 ? __( 'Available', 'muu-element-controller' ) : __( 'Not detected', 'muu-element-controller' ) ); ?></strong>
                    <span><?php esc_html_e( 'Contact form dependency', 'muu-element-controller' ); ?></span>
                </article>
                <article class="muu-status-card">
                    <span class="muu-status-card__label"><?php esc_html_e( 'MUU Shortcodes', 'muu-element-controller' ); ?></span>
                    <strong><?php echo esc_html( (string) count( $shortcodes ) ); ?></strong>
                    <span><?php esc_html_e( 'Registered reusable components', 'muu-element-controller' ); ?></span>
                </article>
            </div>

            <div class="muu-admin-card">
                <h3><?php esc_html_e( 'Registered shortcodes', 'muu-element-controller' ); ?></h3>
                <div class="muu-shortcode-list">
                    <?php foreach ( $shortcodes as $shortcode ) : ?>
                        <code>[<?php echo esc_html( $shortcode ); ?>]</code>
                    <?php endforeach; ?>
                </div>
                <div class="muu-quick-actions">
                    <a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=navigation' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="navigation"><?php esc_html_e( 'Edit Navigation', 'muu-element-controller' ); ?></a>
                    <a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=footer' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="footer"><?php esc_html_e( 'Edit Footer', 'muu-element-controller' ); ?></a>
                </div>
            </div>
        </section>
        <?php
    }

    private function render_shortcode_panel( string $section ): void {
        ?>
        <section class="muu-admin-section">
            <div class="muu-admin-section__heading">
                <h2><?php esc_html_e( 'Shortcodes', 'muu-element-controller' ); ?></h2>
                <p><?php esc_html_e( 'Global settings act as defaults. Shortcode attributes can still override individual instances.', 'muu-element-controller' ); ?></p>
            </div>

            <nav class="muu-subtabs" aria-label="<?php esc_attr_e( 'Shortcode components', 'muu-element-controller' ); ?>">
                <a class="<?php echo 'navigation' === $section ? 'is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=navigation' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="navigation"><?php esc_html_e( 'Navigation', 'muu-element-controller' ); ?></a>
                <a class="<?php echo 'footer' === $section ? 'is-active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=footer' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="footer"><?php esc_html_e( 'Footer', 'muu-element-controller' ); ?></a>
            </nav>

            <div class="muu-subtab-content">
                <?php
                if ( 'footer' === $section && class_exists( 'MUU_Footer_Controller' ) ) {
                    MUU_Footer_Controller::instance()->render_settings_panel();
                } else {
                    $this->render_navigation_settings_panel();
                }
                ?>
            </div>
        </section>
        <?php
    }

    private static function render_field_row( string $option_name, string $prefix, string $key, string $label, string $type, $value ): void {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr( $prefix . $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
            <td><input class="regular-text" id="<?php echo esc_attr( $prefix . $key ); ?>" name="<?php echo esc_attr( $option_name ); ?>[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $value ); ?>"></td>
        </tr>
        <?php
    }

    private function render_navigation_settings_panel(): void {
        $options = self::options();
        $groups  = array(
            __( 'Branding', 'muu-element-controller' ) => array(
                'logo_text' => array( 'Logo text', 'text' ),
                'logo_url'  => array( 'Logo URL', 'url' ),
            ),
            __( 'Utility navigation', 'muu-element-controller' ) => array(
                'offers_label'       => array( 'Offers label', 'text' ),
                'offers_url'         => array( 'Offers URL', 'url' ),
                'shop_label'         => array( 'Shop label', 'text' ),
                'shop_url'           => array( 'Shop URL', 'url' ),
                'availability_label' => array( 'Availability label', 'text' ),
                'availability_url'   => array( 'Availability URL', 'url' ),
            ),
            __( 'Expanded panel', 'muu-element-controller' ) => array(
                'side_label'     => array( 'Vertical side label', 'text' ),
                'language_label' => array( 'Expanded language label', 'text' ),
                'panel_eyebrow'  => array( 'Expanded eyebrow', 'text' ),
                'panel_headline' => array( 'Expanded headline', 'text' ),
                'booking_month'  => array( 'Booking month', 'text' ),
                'booking_day'    => array( 'Booking day', 'text' ),
                'booking_url'    => array( 'Booking URL', 'url' ),
            ),
            __( 'Social links', 'muu-element-controller' ) => array(
                'youtube_url'   => array( 'YouTube URL', 'url' ),
                'tiktok_url'    => array( 'TikTok URL', 'url' ),
                'instagram_url' => array( 'Instagram URL', 'url' ),
            ),
            __( 'Layout & colors', 'muu-element-controller' ) => array(
                'panel_width_percent' => array( 'Expanded panel width (%)', 'number' ),
                'header_height'       => array( 'Header height (px)', 'number' ),
                'rail_width'          => array( 'Rail width (px)', 'number' ),
                'rail_height_percent' => array( 'Rail height (% of hero)', 'number' ),
                'text_color'          => array( 'Text color', 'text' ),
                'line_color'          => array( 'Border color (CSS)', 'text' ),
                'rail_color'          => array( 'Rail color (CSS)', 'text' ),
            ),
            __( 'Advanced', 'muu-element-controller' ) => array(
                'target_selector' => array( 'Elementor target selector', 'text' ),
            ),
        );
        ?>
        <form action="options.php" method="post" class="muu-settings-form">
            <?php settings_fields( 'muu_ec_settings' ); ?>
            <?php foreach ( $groups as $heading => $fields ) : ?>
                <div class="muu-admin-card">
                    <h3><?php echo esc_html( $heading ); ?></h3>
                    <table class="form-table" role="presentation"><tbody>
                        <?php foreach ( $fields as $key => $field ) : ?>
                            <?php self::render_field_row( 'muu_ec_nav_lefttab', 'muu-ec-', $key, $field[0], $field[1], $options[ $key ] ); ?>
                        <?php endforeach; ?>
                    </tbody></table>
                </div>
            <?php endforeach; ?>

            <div class="muu-admin-card">
                <h3><?php esc_html_e( 'Custom CSS', 'muu-element-controller' ); ?></h3>
                <textarea class="large-text code" id="muu-ec-custom-css" name="muu_ec_nav_lefttab[custom_css]" rows="10"><?php echo esc_textarea( $options['custom_css'] ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Scoped to this component. Use .muu-ec-nav-lefttab as the root selector.', 'muu-element-controller' ); ?></p>
            </div>

            <?php submit_button( __( 'Save Navigation Settings', 'muu-element-controller' ) ); ?>
        </form>
        <?php
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
