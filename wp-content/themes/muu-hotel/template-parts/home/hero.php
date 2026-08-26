<?php
/**
 * Home hero.
 *
 * @package MuuHotel
 */

$hero_image = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
?>
<section class="home-hero" aria-label="<?php esc_attr_e( 'Hotel introduction', 'muu-hotel' ); ?>">
    <?php if ( $hero_image ) : ?>
        <div class="home-hero__media" style="background-image:url('<?php echo esc_url( $hero_image ); ?>');" aria-hidden="true"></div>
    <?php else : ?>
        <div class="home-hero__media home-hero__media--placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <div class="home-hero__overlay" aria-hidden="true"></div>
    <div class="home-hero__rail" aria-hidden="true"></div>

    <p class="home-hero__vertical-label">LOREM IPSUM DO</p>

    <button class="home-hero__play" type="button" aria-label="<?php esc_attr_e( 'Play hotel video', 'muu-hotel' ); ?>">
        <span aria-hidden="true"></span>
    </button>

    <div class="home-hero__social" aria-label="<?php esc_attr_e( 'Social media links', 'muu-hotel' ); ?>">
        <a href="#" aria-label="YouTube">YT</a>
        <a href="#" aria-label="TikTok">TT</a>
        <a href="#" aria-label="Instagram">IG</a>
    </div>
</section>
