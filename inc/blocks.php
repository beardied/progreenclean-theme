<?php
/**
 * Custom Gutenberg Blocks Registration
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom blocks
 */
add_action('init', function (): void {
    $blocks = [
        'hero',
        'service-grid',
        'service-card',
        'testimonial-carousel',
        'faq-accordion',
        'faq-item',
        'location-map',
        'cta-block',
        'process-steps',
        'features-grid',
        'feature-item',
        'pricing-table',
        'team-grid',
        'related-services',
        'trust-badge',
        'rating-summary',
        'before-after',
        'contact-methods',
        'quote-wizard',
    ];
    
    foreach ($blocks as $block) {
        register_block_type(PGC_PATH . '/blocks/' . $block);
    }
});

/**
 * Enqueue block assets
 */
add_action('enqueue_block_editor_assets', function (): void {
    wp_enqueue_style(
        'progreenclean-editor',
        PGC_URL . '/assets/css/editor-style.css',
        [],
        PGC_VERSION
    );
});

/**
 * Block categories
 */
add_filter('block_categories_all', function (array $categories): array {
    $categories[] = [
        'slug' => 'progreenclean',
        'title' => __('ProGreenClean', 'progreenclean'),
        'icon' => 'admin-tools',
    ];
    return $categories;
});

/**
 * Register block pattern categories
 */
add_action('init', function (): void {
    register_block_pattern_category('progreenclean/pages', [
        'label' => __('ProGreenClean Pages', 'progreenclean'),
    ]);
    
    register_block_pattern_category('progreenclean/sections', [
        'label' => __('ProGreenClean Sections', 'progreenclean'),
    ]);
});
