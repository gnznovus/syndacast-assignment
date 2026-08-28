<?php
/** MUU Theme Controller admin experience. */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Owns the WordPress admin UI for MUU reusable components.
 *
 * Frontend controllers retain settings registration, sanitization, shortcode
 * rendering, and frontend assets. This class only coordinates administration.
 */
final class MUU_Admin_Controller {
    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_media' ) );
        add_action( 'wp_ajax_muu_controller_panel', array( $this, 'ajax_render_panel' ) );
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
                <p class="description"><?php esc_html_e( 'Expand a shortcode to see supported arguments and usage examples.', 'muu-element-controller' ); ?></p>

                <div class="muu-shortcode-docs">
                    <details>
                        <summary><code>[muu_nav_lefttab]</code><span><?php esc_html_e( 'Navigation + expandable left panel', 'muu-element-controller' ); ?></span></summary>
                        <div class="muu-shortcode-docs__body">
                            <p><strong><?php esc_html_e( 'Example', 'muu-element-controller' ); ?></strong></p>
                            <div class="muu-shortcode-example">
                                <code>[muu_nav_lefttab left-tab="false" logo-color="#000" nav-color="#000" mobile-logo-color="#fff" mobile-nav-color="#fff"]</code>
                                <button type="button" class="button" data-muu-copy-shortcode="[muu_nav_lefttab left-tab=&quot;false&quot; logo-color=&quot;#000&quot; nav-color=&quot;#000&quot; mobile-logo-color=&quot;#fff&quot; mobile-nav-color=&quot;#fff&quot;]"><?php esc_html_e( 'Copy', 'muu-element-controller' ); ?></button>
                            </div>
                            <table class="widefat striped muu-shortcode-args"><tbody>
                                <tr><td><code>class</code></td><td><?php esc_html_e( 'Extra CSS class on the component root.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>target</code></td><td><?php esc_html_e( 'Override the configured Elementor host selector.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>left-tab</code></td><td><?php esc_html_e( 'Show or hide the expandable left rail. Default: true.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>logo-color</code></td><td><?php esc_html_e( 'Logo color override.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>nav-color</code></td><td><?php esc_html_e( 'Utility navigation color override.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>divider-color</code></td><td><?php esc_html_e( 'Header divider color override.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>mobile-logo-color</code></td><td><?php esc_html_e( 'Mobile logo color override.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>mobile-nav-color</code></td><td><?php esc_html_e( 'Mobile utility navigation color override.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>mobile-divider-color</code></td><td><?php esc_html_e( 'Mobile divider color override.', 'muu-element-controller' ); ?></td></tr>
                            </tbody></table>
                            <a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=navigation' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="navigation"><?php esc_html_e( 'Edit Navigation', 'muu-element-controller' ); ?></a>
                        </div>
                    </details>

                    <details>
                        <summary><code>[muu_orange_shape]</code><span><?php esc_html_e( 'Decorative orange hero artwork', 'muu-element-controller' ); ?></span></summary>
                        <div class="muu-shortcode-docs__body">
                            <p><strong><?php esc_html_e( 'Example', 'muu-element-controller' ); ?></strong></p>
                            <div class="muu-shortcode-example">
                                <code>[muu_orange_shape left="20%" top="12%" width="28%" rotate="0" color="#fe5000" opacity="0.85"]</code>
                                <button type="button" class="button" data-muu-copy-shortcode="[muu_orange_shape left=&quot;20%&quot; top=&quot;12%&quot; width=&quot;28%&quot; rotate=&quot;0&quot; color=&quot;#fe5000&quot; opacity=&quot;0.85&quot;]"><?php esc_html_e( 'Copy', 'muu-element-controller' ); ?></button>
                            </div>
                            <table class="widefat striped muu-shortcode-args"><tbody>
                                <tr><td><code>target</code></td><td><?php esc_html_e( 'Elementor host selector.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>left</code></td><td><?php esc_html_e( 'Horizontal position. Default: 20%.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>top</code></td><td><?php esc_html_e( 'Vertical position. Default: 12%.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>width</code></td><td><?php esc_html_e( 'Artwork width. Default: 28%.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>height</code></td><td><?php esc_html_e( 'Artwork height. Default: auto.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>rotate</code></td><td><?php esc_html_e( 'Rotation in degrees.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>flip_x</code></td><td><?php esc_html_e( 'Flip horizontally. Default: true.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>flip_y</code></td><td><?php esc_html_e( 'Flip vertically. Default: false.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>color</code></td><td><?php esc_html_e( 'Artwork color. Default: #fe5000.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>opacity</code></td><td><?php esc_html_e( 'Opacity from 0 to 1. Default: 0.85.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>class</code></td><td><?php esc_html_e( 'Extra CSS class.', 'muu-element-controller' ); ?></td></tr>
                            </tbody></table>
                        </div>
                    </details>

                    <details>
                        <summary><code>[muu_contact_form]</code><span><?php esc_html_e( 'Styled Contact Form 7 wrapper', 'muu-element-controller' ); ?></span></summary>
                        <div class="muu-shortcode-docs__body">
                            <p><strong><?php esc_html_e( 'Example', 'muu-element-controller' ); ?></strong></p>
                            <div class="muu-shortcode-example">
                                <code>[muu_contact_form id="254"]</code>
                                <button type="button" class="button" data-muu-copy-shortcode="[muu_contact_form id=&quot;254&quot;]"><?php esc_html_e( 'Copy', 'muu-element-controller' ); ?></button>
                            </div>
                            <table class="widefat striped muu-shortcode-args"><tbody>
                                <tr><td><code>id</code></td><td><?php esc_html_e( 'Required Contact Form 7 form ID.', 'muu-element-controller' ); ?></td></tr>
                                <tr><td><code>class</code></td><td><?php esc_html_e( 'Extra CSS class on the wrapper.', 'muu-element-controller' ); ?></td></tr>
                            </tbody></table>
                        </div>
                    </details>

                    <details>
                        <summary><code>[muu_footer]</code><span><?php esc_html_e( 'Theme-owned footer component', 'muu-element-controller' ); ?></span></summary>
                        <div class="muu-shortcode-docs__body">
                            <p><strong><?php esc_html_e( 'Example', 'muu-element-controller' ); ?></strong></p>
                            <div class="muu-shortcode-example">
                                <code>[muu_footer]</code>
                                <button type="button" class="button" data-muu-copy-shortcode="[muu_footer]"><?php esc_html_e( 'Copy', 'muu-element-controller' ); ?></button>
                            </div>
                            <table class="widefat striped muu-shortcode-args"><tbody>
                                <tr><td><code>class</code></td><td><?php esc_html_e( 'Optional extra CSS class on the footer root.', 'muu-element-controller' ); ?></td></tr>
                            </tbody></table>
                            <a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=muu-theme-controller&tab=shortcodes&section=footer' ) ); ?>" data-muu-tab="shortcodes" data-muu-section="footer"><?php esc_html_e( 'Edit Footer', 'muu-element-controller' ); ?></a>
                        </div>
                    </details>
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
        $options = MUU_Element_Controller::options();
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


    public function enqueue_admin_media(): void {
        if ( ! is_admin() || ( $_GET['page'] ?? '' ) !== 'muu-theme-controller' ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script( 'jquery' );
        wp_add_inline_script(
            'jquery',
            <<<'JS'
(function ($) {
    function setMedia($field, attachment) {
        var $row = $field.closest('.muu-media-field');
        $field.val(attachment ? attachment.id : '');
        $row.find('.muu-media-preview').html(attachment ? '<img src="' + attachment.url + '" alt="">' : '');
        $row.find('.muu-media-remove').prop('hidden', !attachment);
    }

    $(document).on('click', '.muu-media-select', function (event) {
        event.preventDefault();
        var $field = $('#' + $(this).data('target'));
        var frame = wp.media({
            title: 'Select footer image',
            button: { text: 'Use this image' },
            library: { type: 'image' },
            multiple: false
        });
        frame.on('select', function () {
            setMedia($field, frame.state().get('selection').first().toJSON());
        });
        frame.open();
    });

    $(document).on('click', '.muu-media-remove', function (event) {
        event.preventDefault();
        setMedia($('#' + $(this).data('target')), null);
    });
})(jQuery);
JS
        );
    }

    private static function media_field( string $key, string $label, int $attachment_id ): void {
        $preview = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'medium' ) : '';
        ?>
        <tr>
            <th scope="row"><label for="muu-footer-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th>
            <td>
                <div class="muu-media-field">
                    <input id="muu-footer-<?php echo esc_attr( $key ); ?>" name="muu_footer_settings[<?php echo esc_attr( $key ); ?>]" type="hidden" value="<?php echo esc_attr( $attachment_id ); ?>">
                    <div class="muu-media-preview">
                        <?php if ( $preview ) : ?><img src="<?php echo esc_url( $preview ); ?>" alt=""><?php endif; ?>
                    </div>
                    <button class="button muu-media-select" type="button" data-target="muu-footer-<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Select / Upload Image', 'muu-element-controller' ); ?></button>
                    <button class="button muu-media-remove" type="button" data-target="muu-footer-<?php echo esc_attr( $key ); ?>"<?php echo $attachment_id ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove', 'muu-element-controller' ); ?></button>
                </div>
            </td>
        </tr>
        <?php
    }

    public function render_settings_panel(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $options = MUU_Footer_Controller::options();
        $groups  = array(
            __( 'Media & branding', 'muu-element-controller' ) => array(
                'logo_text' => array( 'Logo text', 'text' ),
                'logo_url'  => array( 'Logo URL', 'link' ),
            ),
            __( 'Navigation links', 'muu-element-controller' ) => array(
                'gallery_label'        => array( 'Gallery label', 'text' ),
                'gallery_url'          => array( 'Gallery URL', 'link' ),
                'health_label'         => array( 'Health & Safety label', 'text' ),
                'health_url'           => array( 'Health & Safety URL', 'link' ),
                'sustainability_label' => array( 'Sustainability label', 'text' ),
                'sustainability_url'   => array( 'Sustainability URL', 'link' ),
                'privacy_label'        => array( 'Privacy Policy label', 'text' ),
                'privacy_url'          => array( 'Privacy Policy URL', 'link' ),
                'contact_label'        => array( 'Contact label', 'text' ),
                'contact_url'          => array( 'Contact URL', 'link' ),
            ),
            __( 'Social', 'muu-element-controller' ) => array(
                'follow_label'  => array( 'Follow us label', 'text' ),
                'youtube_url'   => array( 'YouTube URL', 'link' ),
                'tiktok_url'    => array( 'TikTok URL', 'link' ),
                'instagram_url' => array( 'Instagram URL', 'link' ),
            ),
            __( 'Reservations', 'muu-element-controller' ) => array(
                'reservations_label' => array( 'Reservations heading', 'text' ),
                'reservations_phone' => array( 'Reservations phone', 'text' ),
                'reservations_fax'   => array( 'Reservations fax', 'text' ),
                'reservations_email' => array( 'Reservations email', 'email' ),
            ),
            __( 'Other inquiries', 'muu-element-controller' ) => array(
                'inquiries_label' => array( 'Other inquiries heading', 'text' ),
                'inquiries_phone' => array( 'Other inquiries phone', 'text' ),
                'inquiries_fax'   => array( 'Other inquiries fax', 'text' ),
                'inquiries_email' => array( 'Other inquiries email', 'email' ),
            ),
            __( 'Legal', 'muu-element-controller' ) => array(
                'copyright' => array( 'Copyright', 'text' ),
            ),
        );
        ?>
        <form action="options.php" method="post" class="muu-settings-form">
            <?php settings_fields( 'muu_footer_settings_group' ); ?>

            <div class="muu-admin-card">
                <h3><?php esc_html_e( 'Media & branding', 'muu-element-controller' ); ?></h3>
                <table class="form-table" role="presentation"><tbody>
                    <?php self::media_field( 'background_image_id', 'Footer background image', absint( $options['background_image_id'] ) ); ?>
                    <?php self::media_field( 'slh_image_id', 'Small Luxury Hotels logo', absint( $options['slh_image_id'] ) ); ?>
                    <?php foreach ( $groups[ __( 'Media & branding', 'muu-element-controller' ) ] as $key => $field ) : ?>
                        <?php $input_type = 'link' === $field[1] ? 'text' : $field[1]; ?>
                        <tr>
                            <th scope="row"><label for="muu-footer-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
                            <td><input class="regular-text" id="muu-footer-<?php echo esc_attr( $key ); ?>" name="muu_footer_settings[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $input_type ); ?>" value="<?php echo esc_attr( $options[ $key ] ); ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody></table>
            </div>

            <?php foreach ( $groups as $heading => $fields ) : ?>
                <?php if ( __( 'Media & branding', 'muu-element-controller' ) === $heading ) { continue; } ?>
                <div class="muu-admin-card">
                    <h3><?php echo esc_html( $heading ); ?></h3>
                    <table class="form-table" role="presentation"><tbody>
                        <?php foreach ( $fields as $key => $field ) : ?>
                            <?php $input_type = 'link' === $field[1] ? 'text' : $field[1]; ?>
                            <tr>
                                <th scope="row"><label for="muu-footer-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label></th>
                                <td><input class="regular-text" id="muu-footer-<?php echo esc_attr( $key ); ?>" name="muu_footer_settings[<?php echo esc_attr( $key ); ?>]" type="<?php echo esc_attr( $input_type ); ?>" value="<?php echo esc_attr( $options[ $key ] ); ?>"></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if ( __( 'Other inquiries', 'muu-element-controller' ) === $heading ) : ?>
                            <tr>
                                <th scope="row"><label for="muu-footer-address"><?php esc_html_e( 'Address', 'muu-element-controller' ); ?></label></th>
                                <td><textarea class="large-text" id="muu-footer-address" name="muu_footer_settings[address]" rows="3"><?php echo esc_textarea( $options['address'] ); ?></textarea></td>
                            </tr>
                        <?php endif; ?>
                    </tbody></table>
                </div>
            <?php endforeach; ?>

            <div class="muu-admin-note">
                <code>[muu_footer]</code>
                <span><?php esc_html_e( 'Tablet layout uses the theme-owned pyramid composition. Link fields accept #, anchors, relative paths, or full URLs.', 'muu-element-controller' ); ?></span>
            </div>

            <?php submit_button( __( 'Save Footer Settings', 'muu-element-controller' ) ); ?>
        </form>
        <?php
    }


}
