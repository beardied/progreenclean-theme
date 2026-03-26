<?php
/**
 * Template Name: Locations Hub
 * Description: Displays all location pages that are children of this page
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get all child pages of the current Locations page
$parent_id = get_the_ID();
$locations = get_posts([
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'post_parent' => $parent_id
]);
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

<main>
    <!-- Intro Section -->
    <section class="pgc-section" style="padding-top: 40px; padding-bottom: 20px;">
        <div class="pgc-container">
            <div class="pgc-section-header">
                <span class="pgc-section-eyebrow">Areas We Cover</span>
                <h2 class="pgc-section-title">Serving Surrey &amp; South West London</h2>
                <p class="pgc-section-desc">Based in Epsom, we provide professional cleaning services across Surrey and South West London.</p>
            </div>
        </div>
    </section>

    <!-- Location Tiles -->
    <section class="pgc-section" style="padding-top: 20px;">
        <div class="pgc-container">
            <?php if (!empty($locations)) : ?>
            <div class="pgc-locations-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
                <?php foreach ($locations as $location) : 
                    $excerpt = $location->post_excerpt ?: wp_trim_words($location->post_content, 15);
                ?>
                <article class="pgc-location-card" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s;">
                    <div style="height: 160px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                        <span style="font-size: 64px;">📍</span>
                    </div>
                    <div style="padding: 24px;">
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px; color: var(--pgc-gray-900);">
                            <a href="<?php echo get_permalink($location->ID); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html($location->post_title); ?></a>
                        </h3>
                        <p style="color: var(--pgc-gray-600); line-height: 1.6; margin-bottom: 16px; font-size: 0.95rem;"><?php echo esc_html($excerpt); ?></p>
                        <a href="<?php echo get_permalink($location->ID); ?>" class="pgc-btn pgc-btn-sm" style="display: inline-block;">View Details →</a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php else : ?>
            <p style="text-align: center; color: var(--pgc-gray-500);">No locations found. Please create location pages as children of this page.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="pgc-cta-section" style="margin-top: 60px;">
        <div class="pgc-container">
            <div class="pgc-cta-inner" style="text-align: center; padding: 60px 40px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 20px;">
                <span class="pgc-cta-badge" style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; padding: 8px 16px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px;">NOT IN THESE AREAS?</span>
                <h2 class="pgc-cta-title" style="color: #fff; font-size: 2rem; font-weight: 800; margin-bottom: 16px;">We May Still Be Able To Help</h2>
                <p class="pgc-cta-desc" style="color: rgba(255,255,255,0.9); font-size: 1.1rem; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto;">Contact us to check availability in your location. We are constantly expanding.</p>
                <a href="/get-a-quote/" class="pgc-btn" style="display: inline-block; padding: 20px 48px; font-size: 18px; background: white; color: var(--pgc-primary); text-decoration: none; border-radius: 8px; font-weight: 600;">Get in Touch</a>
            </div>
        </div>
    </section>
</main>

<style>
.pgc-location-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
</style>

<?php get_footer(); ?>
