<?php
/**
 * Template Name: Service Page
 * Description: Individual service page template
 */

if (!defined('ABSPATH')) exit;

get_header();

$service_icon = get_post_meta(get_the_ID(), '_pgc_service_icon', true) ?: '🧹';
?>

<header class="pgc-page-header" style="background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%);">
    <div class="pgc-container">
        <div class="pgc-page-header-content" style="text-align: center; color: #fff;">
            <div style="font-size: 64px; margin-bottom: 20px;"><?php echo esc_html($service_icon); ?></div>
            <h1 class="pgc-page-title" style="color: #fff;"><?php the_title(); ?></h1>
            <?php if (has_excerpt()) : ?>
            <p class="pgc-page-desc" style="color: rgba(255,255,255,0.9); max-width: 600px; margin: 0 auto;"><?php echo wp_strip_all_tags(get_the_excerpt()); ?></p>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="pgc-section">
    <div class="pgc-container" style="max-width: 900px;">
        <?php
        if (have_posts()) :
            while (have_posts()) :
                the_post();
                ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
                
                <!-- Service CTA -->
                <div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border: 2px solid rgba(8, 145, 178, 0.1); border-radius: 16px; padding: 40px; margin-top: 60px; text-align: center;">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 12px;">Ready to get started?</h3>
                    <p style="color: var(--pgc-gray-600); margin-bottom: 24px;">Get an instant quote for <?php the_title(); ?> today.</p>
                    <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn pgc-btn-primary" style="display: inline-block;">Get a Quote</a>
                </div>
                <?php
            endwhile;
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
