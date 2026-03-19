<?php
$columns = $attributes['columns'] ?? 3;
$filter_enabled = $attributes['filterEnabled'] ?? true;
$show_all = $attributes['showAll'] ?? false;
$category = $attributes['category'] ?? '';
$animate = $attributes['animate'] ?? true;

$args = ['post_type' => 'pgc_service', 'posts_per_page' => $show_all ? -1 : 6, 'orderby' => 'menu_order'];
if ($category) {
    $args['tax_query'] = [['taxonomy' => 'pgc_service_category', 'field' => 'slug', 'terms' => $category]];
}
$services = get_posts($args);
?>
<div class="pgc-service-grid pgc-service-grid--cols-<?php echo esc_attr($columns); ?>" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <?php foreach ($services as $service): 
        $price_from = get_post_meta($service->ID, 'price_from', true);
        $price_note = get_post_meta($service->ID, 'price_note', true);
    ?>
        <div class="pgc-service-card">
            <a href="<?php echo get_permalink($service); ?>" class="pgc-service-card__link">
                <?php if (has_post_thumbnail($service)): ?>
                    <div class="pgc-service-card__image">
                        <?php echo get_the_post_thumbnail($service, 'medium'); ?>
                    </div>
                <?php endif; ?>
                <div class="pgc-service-card__content">
                    <h3 class="pgc-service-card__title"><?php echo esc_html($service->post_title); ?></h3>
                    <p class="pgc-service-card__excerpt"><?php echo esc_html(wp_trim_words($service->post_excerpt, 15)); ?></p>
                    <?php if ($price_from): ?>
                        <div class="pgc-service-card__price">
                            <span class="pgc-service-card__from">From</span>
                            <span class="pgc-service-card__amount"><?php echo esc_html($price_from); ?></span>
                            <?php if ($price_note): ?>
                                <span class="pgc-service-card__note"><?php echo esc_html($price_note); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <span class="pgc-service-card__cta">Get a Quote &rarr;</span>
                </div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
