<?php
/**
 * Template Name: Services Hub
 * Description: Displays all service pages with editor content support
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get editor content for above the grid
$editor_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));

// Get all service pages
$services = get_posts([
    'post_type' => 'page',
    'posts_per_page' => -1,
    'orderby' => 'menu_order title',
    'order' => 'ASC',
    'meta_query' => [
        [
            'key' => '_wp_page_template',
            'value' => 'page-service.php'
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
        
        <?php if (!empty($services)) : ?>
        <div class="pgc-services-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php foreach ($services as $service) : 
                $icon = get_post_meta($service->ID, '_pgc_service_icon', true) ?: '🧹';
                $excerpt = $service->post_excerpt ?: wp_trim_words($service->post_content, 20);
            ?>
            <article class="pgc-service-card" style="background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); transition: transform 0.3s, box-shadow 0.3s;">
                <div class="pgc-service-icon" style="font-size: 48px; margin-bottom: 20px;"><?php echo esc_html($icon); ?></div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px; color: var(--pgc-gray-900);">
                    <a href="<?php echo get_permalink($service->ID); ?>" style="color: inherit; text-decoration: none;"><?php echo esc_html($service->post_title); ?></a>
                </h3>
                <p style="color: var(--pgc-gray-600); line-height: 1.6; margin-bottom: 20px;"><?php echo esc_html($excerpt); ?></p>
                <a href="<?php echo get_permalink($service->ID); ?>" class="pgc-btn pgc-btn-outline" style="display: inline-block;">Learn More</a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else : ?>
        <p style="text-align: center; color: var(--pgc-gray-500);">No services found.</p>
        <?php endif; ?>
        
    </div>
</main>

<style>
.pgc-service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.12);
}
</style>

<?php get_footer(); ?>
