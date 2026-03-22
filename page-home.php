<?php
/**
 * Template Name: Homepage
 * Description: Homepage template with full Gutenberg support
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<main>
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
    endif;
    ?>
</main>

<?php get_footer(); ?>
