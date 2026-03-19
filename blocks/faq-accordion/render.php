<?php
$category = $attributes['category'] ?? '';
$limit = $attributes['limit'] ?? 0;
$schema_enabled = $attributes['schemaEnabled'] ?? true;
$animate = $attributes['animate'] ?? true;

$faqs = pgc_get_faqs($category);
if ($limit > 0) {
    $faqs = array_slice($faqs, 0, $limit);
}

if (empty($faqs)) return;
?>
<div class="pgc-faq-accordion" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <?php foreach ($faqs as $faq): ?>
        <details class="pgc-faq-item">
            <summary class="pgc-faq-item__question">
                <?php echo esc_html($faq->post_title); ?>
                <span class="pgc-faq-item__icon"></span>
            </summary>
            <div class="pgc-faq-item__answer">
                <?php echo wp_kses_post(apply_filters('the_content', $faq->post_content)); ?>
            </div>
        </details>
    <?php endforeach; ?>
</div>
