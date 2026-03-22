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
 * Register custom blocks using block.json
 */
add_action('init', function (): void {
    // Blocks with block.json files (WordPress 5.8+ method)
    $blocks_with_json = [
        'hero',
        'service-grid',
        'service-card',
        'testimonial-carousel',
        'faq-accordion',
        'faq-item',
        'faq-custom',
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
        'section-background',
    ];
    
    foreach ($blocks_with_json as $block) {
        $block_path = PGC_PATH . '/blocks/' . $block;
        
        // Check if block.json exists
        if (file_exists($block_path . '/block.json')) {
            // Use register_block_type with block.json (modern method)
            register_block_type($block_path);
        } elseif (file_exists($block_path . '/block.php')) {
            // Fallback to PHP registration (legacy method)
            require_once $block_path . '/block.php';
        }
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

/**
 * Remove unwanted theme blocks from inserter
 */
add_filter('allowed_block_types_all', function($allowed_blocks, $editor_context) {
    // Get all registered blocks
    $block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
    
    $allowed = [];
    
    foreach ($block_types as $block_type => $block) {
        // Allow core blocks
        if (strpos($block_type, 'core/') === 0) {
            $allowed[] = $block_type;
        }
        // Allow ProGreenClean blocks
        elseif (strpos($block_type, 'progreenclean/') === 0) {
            $allowed[] = $block_type;
        }
        // Explicitly remove theme template parts
        elseif ($block_type === 'core/template-part') {
            continue;
        }
    }
    
    return $allowed;
}, 10, 2);
