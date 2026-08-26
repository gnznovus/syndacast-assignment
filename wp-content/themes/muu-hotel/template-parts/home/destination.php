<?php
/**
 * Home Destination section.
 *
 * @package MuuHotel
 */

$post_id         = (int) get_queried_object_id();
$primary_image   = muu_hotel_home_field( 'destination_image_primary', $post_id );
$secondary_image = muu_hotel_home_field( 'destination_image_secondary', $post_id );
?>
<section class="home-destination" aria-labelledby="home-destination-title">
    <div class="home-destination__line" aria-hidden="true"></div>

    <div class="home-destination__copy">
        <div class="home-destination__label"><?php echo esc_html( muu_hotel_home_field( 'destination_label', $post_id ) ); ?></div>
        <h2 id="home-destination-title" class="home-destination__title">
            <?php echo esc_html( muu_hotel_home_field( 'destination_heading', $post_id ) ); ?>
        </h2>
        <p class="home-destination__intro">
            <?php echo esc_html( muu_hotel_home_field( 'destination_intro', $post_id ) ); ?>
        </p>
        <p class="home-destination__body">
            <?php echo esc_html( muu_hotel_home_field( 'destination_body', $post_id ) ); ?>
        </p>

        <div class="home-slider-nav" aria-label="<?php esc_attr_e( 'Destination slide navigation', 'muu-hotel' ); ?>">
            <button class="home-slider-nav__arrow home-slider-nav__arrow--prev" type="button" aria-label="<?php esc_attr_e( 'Previous destination', 'muu-hotel' ); ?>"></button>
            <span class="home-slider-nav__count"><strong>01</strong> / 02</span>
            <button class="home-slider-nav__arrow home-slider-nav__arrow--next" type="button" aria-label="<?php esc_attr_e( 'Next destination', 'muu-hotel' ); ?>"></button>
        </div>
    </div>

    <div class="home-destination__gallery" aria-hidden="true">
        <div class="home-destination__image home-destination__image--primary"<?php echo $primary_image ? ' style="background-image:url(' . esc_url( $primary_image ) . ')"' : ''; ?>></div>
        <div class="home-destination__image home-destination__image--secondary"<?php echo $secondary_image ? ' style="background-image:url(' . esc_url( $secondary_image ) . ')"' : ''; ?>></div>
        <span class="home-section-play home-destination__play"><span></span></span>
    </div>
</section>
