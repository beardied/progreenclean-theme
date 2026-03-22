<?php
/**
 * Custom FAQ Block
 * Manual FAQ entries with schema markup
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/faq-custom', [
    'render_callback' => 'pgc_render_faq_custom_block',
    'attributes' => [
        'title' => ['type' => 'string', 'default' => 'Frequently Asked Questions'],
        'description' => ['type' => 'string', 'default' => ''],
        'faqs' => ['type' => 'array', 'default' => []],
        'showSchema' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_faq_custom_block(array $attributes): string {
    $title = $attributes['title'] ?? 'Frequently Asked Questions';
    $description = $attributes['description'] ?? '';
    $faqs = $attributes['faqs'] ?? [];
    $show_schema = $attributes['showSchema'] ?? true;
    
    if (empty($faqs)) {
        return '';
    }
    
    $faq_data = [];
    ob_start();
    ?>
    <div class="pgc-faq-section">
        <?php if ($title) : ?>
            <h2 class="pgc-faq-section__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        
        <?php if ($description) : ?>
            <p class="pgc-faq-section__description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        
        <div class="pgc-faq-accordion">
            <?php foreach ($faqs as $i => $faq) : 
                $question = $faq['question'] ?? '';
                $answer = $faq['answer'] ?? '';
                
                if (!$question || !$answer) continue;
                
                $faq_data[] = [
                    'question' => $question,
                    'answer' => wp_strip_all_tags($answer),
                ];
            ?>
                <div class="pgc-faq-item pgc-animate" style="transition-delay: <?php echo $i * 50; ?>ms">
                    <button class="pgc-faq-item__question" aria-expanded="false">
                        <?php echo esc_html($question); ?>
                        <span class="pgc-faq-item__icon" aria-hidden="true">+</span>
                    </button>
                    <div class="pgc-faq-item__answer">
                        <?php echo wp_kses_post($answer); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if ($show_schema && !empty($faq_data)) : ?>
        <script type="application/ld+json">
            <?php echo wp_json_encode(pgc_get_faq_schema($faq_data)); ?>
        </script>
    <?php endif; ?>
    
    <?php
    return ob_get_clean();
}
