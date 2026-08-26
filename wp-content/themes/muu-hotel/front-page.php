<?php
/**
 * Front page template.
 *
 * Main page content remains editable through WordPress / Elementor.
 *
 * @package MuuHotel
 */

get_header();
?>
<main id="main" class="site-main site-main--home">
    <?php get_template_part( 'template-parts/home/hero' ); ?>

    <div class="home-content" id="home-content">
        <?php
        while ( have_posts() ) {
            the_post();
            the_content();
        }
        ?>
    </div>
</main>
<?php
get_footer();
