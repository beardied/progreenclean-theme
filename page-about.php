<?php
/**
 * Template Name: About Us
 * Description: About page with team section support
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<header class="pgc-page-header">
    <div class="pgc-container">
        <div class="pgc-page-header-content">
            <h1 class="pgc-page-title"><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
            <p class="pgc-page-desc"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></p>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="pgc-section">
    <div class="pgc-container">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                <?php
            endwhile;
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
