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
    <?php
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</main>
<?php
get_footer();
