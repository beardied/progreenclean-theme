<?php
/**
 * Template Name: Locations Hub
 * Description: Displays all location pages with editor content support
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get editor content for above the grid
$editor_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));

// Get all location pages
$locations = get_posts([
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'title',
    'order' => 'ASC',
    'meta_query' => [
        [
            'key' => '_wp_page_template',
            'value' => 'page-location-single.php'
        ]
    ]
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

<main class="pgc-section">
    <div class="pgc-container">
        
        <?php if (!empty($editor_content) && trim(strip_tags($editor_content)) !== '') : ?>
        <div class="entry-content" style="margin-bottom: 60px;">
            <?php echo $editor_content; ?>
        </div>
        <?php endif; ?>
        
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
        <p style="text-align: center; color: var(--pgc-gray-500);">No locations found.</p>
        <?php endif; ?>
        
    </div>
</main>

<style>
.pgc-location-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
</style>

<?php get_footer(); ?>
