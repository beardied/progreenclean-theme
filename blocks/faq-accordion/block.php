<?php
/**
 * FAQ Accordion Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/faq-accordion', [
    'render_callback' => 'pgc_render_faq_accordion_block',
    'attributes' => [
        'category' => ['type' => 'string', 'default' => ''],
        'limit' => ['type' => 'number', 'default' => -1],
        'previewCount' => ['type' => 'number', 'default' => 4],
        'expandable' => ['type' => 'boolean', 'default' => true],
        'schemaEnabled' => ['type' => 'boolean', 'default' => true],
        'animate' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_faq_accordion_block(array $attributes): string {
    $category = $attributes['category'] ?? '';
    $limit = $attributes['limit'] ?? -1;
    $preview_count = $attributes['previewCount'] ?? 4;
    $expandable = $attributes['expandable'] ?? true;
    $schema = $attributes['schemaEnabled'] ?? true;
    $animate = $attributes['animate'] ?? true;
    
    $faqs = pgc_get_faqs($category, $limit > 0 ? $limit : -1);
    
    if (empty($faqs)) {
        return '';
    }
    
    $faq_data = [];
    ob_start();
    ?>
    <div class="pgc-faq-accordion" data-preview="<?php echo esc_attr($preview_count); ?>" data-expandable="<?php echo $expandable ? 'true' : 'false'; ?>">
        <?php foreach ($faqs as $i => $faq) : 
            $delay = $animate ? ($i * 50) : 0;
            $is_hidden = $i >= $preview_count && $expandable;
            
            $faq_data[] = [
                'question' => $faq->post_title,
                'answer' => wp_strip_all_tags($faq->post_content),
            ];
        ?>
            <div class="pgc-faq-item <?php echo $is_hidden ? 'pgc-faq-item--hidden' : ''; ?> <?php echo $animate ? 'pgc-animate' : ''; ?>" 
                 style="<?php echo $animate ? 'transition-delay: ' . $delay . 'ms' : ''; ?>">
                <button class="pgc-faq-item__question" aria-expanded="false">
                    <?php echo esc_html($faq->post_title); ?>
                    <span class="pgc-faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="pgc-faq-item__answer">
                    <?php echo wp_kses_post($faq->post_content); ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if ($expandable && count($faqs) > $preview_count) : ?>
            <button class="pgc-faq__toggle pgc-btn pgc-btn-outline">
                <?php _e('View All FAQs', 'progreenclean'); ?>
            </button>
        <?php endif; ?>
    </div>
    
    <?php if ($schema) : ?>
        <script type="application/ld+json">
            <?php echo wp_json_encode(pgc_get_faq_schema($faq_data)); ?>
        </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}
