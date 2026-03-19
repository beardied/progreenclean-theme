<?php
/**
 * Service Grid Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/service-grid', [
    'render_callback' => 'pgc_render_service_grid_block',
    'attributes' => [
        'columns' => ['type' => 'number', 'default' => 3],
        'filterEnabled' => ['type' => 'boolean', 'default' => false],
        'showAll' => ['type' => 'boolean', 'default' => false],
        'animate' => ['type' => 'boolean', 'default' => true],
        'location' => ['type' => 'string', 'default' => ''],
    ],
]);

function pgc_render_service_grid_block(array $attributes): string {
    $columns = $attributes['columns'] ?? 3;
    $show_all = $attributes['showAll'] ?? false;
    $animate = $attributes['animate'] ?? true;
    
    $services = pgc_get_services($show_all ? -1 : 6);
    
    if (empty($services)) {
        return '<p class="pgc-no-services">' . __('No services found.', 'progreenclean') . '</p>';
    }
    
    ob_start();
    ?>
    <div class="pgc-service-grid pgc-service-grid--<?php echo esc_attr($columns); ?>-cols">
        <?php foreach ($services as $i => $service) : 
            $icon = get_post_meta($service->ID, '_pgc_service_icon', true) ?: 'sparkles';
            $price_from = get_post_meta($service->ID, '_pgc_price_from', true);
            $price_note = get_post_meta($service->ID, '_pgc_price_note', true);
            $delay = $animate ? ($i * 80) : 0;
        ?>
            <div class="pgc-service-card <?php echo $animate ? 'pgc-animate-scale' : ''; ?>" 
                 style="<?php echo $animate ? 'transition-delay: ' . $delay . 'ms' : ''; ?>">
                <a href="<?php echo esc_url(get_permalink($service)); ?>" class="pgc-service-card__link">
                    <div class="pgc-service-card__icon">
                        <?php echo pgc_get_service_icon($icon); ?>
                    </div>
                    <h3 class="pgc-service-card__title"><?php echo esc_html($service->post_title); ?></h3>
                    <p class="pgc-service-card__description">
                        <?php echo esc_html($service->post_excerpt ?: pgc_truncate($service->post_content, 100)); ?>
                    </p>
                    <?php if ($price_from) : ?>
                        <div class="pgc-service-card__price">
                            <span class="pgc-service-card__from">From</span>
                            <span class="pgc-service-card__amount"><?php echo esc_html($price_from); ?></span>
                            <?php if ($price_note) : ?>
                                <span class="pgc-service-card__note"><?php echo esc_html($price_note); ?></span>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div class="pgc-service-card__price">
                            <span class="pgc-service-card__note">Custom quote</span>
                        </div>
                    <?php endif; ?>
                    <span class="pgc-service-card__cta">
                        <?php _e('Get Quote', 'progreenclean'); ?> 
                        <span aria-hidden="true">&rarr;</span>
                    </span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
