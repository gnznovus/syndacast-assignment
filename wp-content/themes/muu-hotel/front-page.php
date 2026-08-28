<?php
/**
 * Classic fallback for sites that disable the block-template hierarchy.
 * Elementor filters the_content() and renders the editable page document.
 *
 * @package MuuHotel
 */

get_header();
?>
<main id="main" class="site-main muu-elementor-content">
    <?php
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</main>
<?php
get_footer();
