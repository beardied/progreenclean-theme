<?php get_header(); ?>

<?php if (!is_front_page()) : ?>
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
<?php endif; ?>

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
