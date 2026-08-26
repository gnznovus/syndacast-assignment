<?php
/**
 * Default page template.
 *
 * @package MuuHotel
 */

get_header();
?>
<main id="main" class="site-main">
    <?php
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    ?>
</main>
<?php
get_footer();
