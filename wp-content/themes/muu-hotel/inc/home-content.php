<?php
/**
 * Native editable fields for the bespoke Home page sections.
 *
 * @package MuuHotel
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function muu_hotel_home_defaults(): array {
    return array(
        'discovery_label'            => 'Discovery.',
        'discovery_heading'          => '– LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISCING ELIT. PELLE NTESQUE VESTIBULUM LIBERO LOREM, EGET CURSUS TORTOR.',
        'discovery_body'             => 'Muu is a new, unpretentious yet luxurious hotel brand that is built around a belief that happiness comes from being oneself. Through dynamic, artistic spaces that live and breathe with the whims of its guests, muu is personable, ever-changing and never boring.',
        'discovery_image'            => 'https://www.figma.com/api/mcp/asset/a146771e-f607-4d30-8cb0-51c0ce3afdd5.png',
        'destination_label'          => 'Destination.',
        'destination_heading'        => '– THONG LO, BANGKOK’S TRENDIEST DISTRICT',
        'destination_intro'          => 'Thong lo won’t disappoint when it comes to lifestyle and the things that you can do in the area. There are countless restaurants, cafes, and bars to visit whenever you desire!',
        'destination_body'           => 'There are many expats living in Thonglor and the neighbouring area of Ekamai, so there are many international restaurants like Japanese and Italian. Prefer local food? No problem, there are many street vendors and Thai restaurants in Thonglor as well. Are you addicted to coffee or have a sweet tooth? Maybe both? There is no shortage of options in Thonglor when it comes to cafes. There are street style coffee places to the high-end stylish cafes. You will find a lot of the younger crowd visiting the stylish modern cafes and international restaurants.',
        'destination_image_primary'  => 'https://www.figma.com/api/mcp/asset/8c6d3a82-79aa-4251-909e-48aa518b31fc.png',
        'destination_image_secondary'=> 'https://www.figma.com/api/mcp/asset/ead45b2b-c3ac-4094-8110-27109a5a3092.png',
    );
}

function muu_hotel_home_field( string $key, int $post_id = 0 ): string {
    $post_id  = $post_id ?: (int) get_queried_object_id();
    $defaults = muu_hotel_home_defaults();
    $value    = get_post_meta( $post_id, '_muu_' . $key, true );

    if ( '' === $value && isset( $defaults[ $key ] ) ) {
        return (string) $defaults[ $key ];
    }

    return (string) $value;
}

function muu_hotel_register_home_meta_box(): void {
    add_meta_box(
        'muu-home-sections',
        __( 'MUU Home Sections', 'muu-hotel' ),
        'muu_hotel_render_home_meta_box',
        'page',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'muu_hotel_register_home_meta_box' );

function muu_hotel_render_home_meta_box( WP_Post $post ): void {
    if ( 'page' !== $post->post_type ) {
        return;
    }

    wp_nonce_field( 'muu_hotel_save_home_fields', 'muu_home_fields_nonce' );

    $fields = array(
        'discovery_label'             => array( 'label' => 'Discovery label', 'type' => 'text' ),
        'discovery_heading'           => array( 'label' => 'Discovery heading', 'type' => 'textarea' ),
        'discovery_body'              => array( 'label' => 'Discovery body', 'type' => 'textarea' ),
        'discovery_image'             => array( 'label' => 'Discovery image URL', 'type' => 'url' ),
        'destination_label'           => array( 'label' => 'Destination label', 'type' => 'text' ),
        'destination_heading'         => array( 'label' => 'Destination heading', 'type' => 'textarea' ),
        'destination_intro'           => array( 'label' => 'Destination intro', 'type' => 'textarea' ),
        'destination_body'            => array( 'label' => 'Destination body', 'type' => 'textarea' ),
        'destination_image_primary'   => array( 'label' => 'Destination primary image URL', 'type' => 'url' ),
        'destination_image_secondary' => array( 'label' => 'Destination secondary image URL', 'type' => 'url' ),
    );

    echo '<p>' . esc_html__( 'These fields feed the bespoke Figma-matched Home sections. Upload an image to Media Library, then paste its file URL into the matching image field.', 'muu-hotel' ) . '</p>';

    foreach ( $fields as $key => $field ) {
        $value = muu_hotel_home_field( $key, $post->ID );
        echo '<p><label for="muu_' . esc_attr( $key ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label></p>';

        if ( 'textarea' === $field['type'] ) {
            echo '<textarea class="widefat" rows="4" id="muu_' . esc_attr( $key ) . '" name="muu_' . esc_attr( $key ) . '">' . esc_textarea( $value ) . '</textarea>';
        } else {
            echo '<input class="widefat" type="' . esc_attr( $field['type'] ) . '" id="muu_' . esc_attr( $key ) . '" name="muu_' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '">';
        }
    }
}

function muu_hotel_save_home_fields( int $post_id ): void {
    if ( ! isset( $_POST['muu_home_fields_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['muu_home_fields_nonce'] ) ), 'muu_hotel_save_home_fields' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    foreach ( array_keys( muu_hotel_home_defaults() ) as $key ) {
        $input_key = 'muu_' . $key;
        if ( ! isset( $_POST[ $input_key ] ) ) {
            continue;
        }

        $raw   = wp_unslash( $_POST[ $input_key ] );
        $value = str_contains( $key, 'image' ) ? esc_url_raw( $raw ) : sanitize_textarea_field( $raw );
        update_post_meta( $post_id, '_muu_' . $key, $value );
    }
}
add_action( 'save_post_page', 'muu_hotel_save_home_fields' );
