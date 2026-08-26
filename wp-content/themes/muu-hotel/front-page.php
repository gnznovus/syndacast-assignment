<?php
/**
 * Front page template.
 *
 * Page layout and content are owned by the WordPress editor / Elementor.
 * The theme only provides the global site foundation.
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
