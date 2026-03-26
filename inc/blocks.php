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
		'test-block',
        'hero',
        //'service-grid',
        //'service-card',
        //'testimonial-carousel',
        //'faq-accordion',
        //'faq-item',
        //'faq-custom',
        //'location-map',
        //'cta-block',
        //'process-steps',
        //'features-grid',
        //'feature-item',
        //'pricing-table',
        //'team-grid',
        //'related-services',
        //'trust-badge',
        //'rating-summary',
        //'before-after',
        //'contact-methods',
        //'quote-wizard',
        //'section-background',
    ];
    
    foreach ($blocks_with_json as $block) {
        $block_path = PGC_PATH . '/blocks/' . $block;
        if (file_exists($block_path . '/block.json')) {
            $result = register_block_type($block_path);
        }
    }
}, 5);

/**
 * Block categories
 */
add_filter('block_categories_all', function (array $categories): array {
    array_unshift($categories, [
        'slug' => 'progreenclean',
        'title' => 'ProGreenClean',
        'icon' => 'admin-tools',
    ]);
    return $categories;
}, 5);

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
		// Skip template parts explicitly
		if ($block_type === 'core/template-part') {
			continue;
		}
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



add_action('admin_footer', function(): void {
    if (!is_admin()) return;
    ?>
    <script>
    window.addEventListener('load', function() {
        var blocks = wp.blocks.getBlockTypes();
        var pgc = blocks.filter(b => b.name.includes('progreenclean'));
        console.log('=== PGC BLOCKS REGISTERED ===');
        console.log('Total blocks:', blocks.length);
        console.log('ProGreenClean blocks:', pgc.length);
        pgc.forEach(b => console.log(' -', b.name));
        
        var cats = wp.blocks.getCategories();
        console.log('=== CATEGORIES ===');
        cats.forEach(c => console.log(' -', c.slug, ':', c.title));
    });
    </script>
    <?php
});