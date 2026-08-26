<?php
/**
 * Home Discovery section.
 *
 * @package MuuHotel
 */

$post_id = (int) get_queried_object_id();
$image   = muu_hotel_home_field( 'discovery_image', $post_id );
?>
<section class="home-discovery" aria-labelledby="home-discovery-title">
    <div class="home-discovery__label"><?php echo esc_html( muu_hotel_home_field( 'discovery_label', $post_id ) ); ?></div>

    <div class="home-discovery__image"<?php echo $image ? ' style="background-image:url(' . esc_url( $image ) . ')"' : ''; ?> aria-hidden="true"></div>

    <button class="home-section-play home-discovery__play" type="button" aria-label="<?php esc_attr_e( 'Play discovery video', 'muu-hotel' ); ?>">
        <span aria-hidden="true"></span>
    </button>

    <div class="home-discovery__wordmark" aria-hidden="true">muu</div>

    <h2 id="home-discovery-title" class="home-discovery__title">
        <?php echo esc_html( muu_hotel_home_field( 'discovery_heading', $post_id ) ); ?>
    </h2>

    <p class="home-discovery__body">
        <?php echo esc_html( muu_hotel_home_field( 'discovery_body', $post_id ) ); ?>
    </p>

    <div class="home-discovery__line" aria-hidden="true"></div>
</section>
