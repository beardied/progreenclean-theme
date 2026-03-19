<?php
/**
 * Features Grid Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/features-grid', [
    'render_callback' => 'pgc_render_features_grid_block',
    'attributes' => [
        'columns' => ['type' => 'number', 'default' => 2],
        'features' => ['type' => 'array', 'default' => []],
        'animate' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_features_grid_block(array $attributes): string {
    $columns = $attributes['columns'] ?? 2;
    $features = $attributes['features'] ?? [];
    $animate = $attributes['animate'] ?? true;
    
    if (empty($features)) {
        $features = [
            [
                'title' => 'Eco-Friendly Cleaning',
                'description' => 'Our ProGreen commitment means biodegradable products, minimal environmental impact, and safe solutions for families and pets.',
                'icon' => 'leaf',
            ],
            [
                'title' => 'Fully Insured & Vetted',
                'description' => 'Complete peace of mind with comprehensive public liability insurance and DBS-checked staff.',
                'icon' => 'shield-check',
            ],
            [
                'title' => 'Satisfaction Guaranteed',
                'description' => 'Not completely satisfied? We will return and re-clean at no extra charge.',
                'icon' => 'badge-check',
            ],
            [
                'title' => 'Local Surrey Experts',
                'description' => 'Based in Epsom with intimate knowledge of Surrey properties and local cleaning challenges.',
                'icon' => 'map-pin',
            ],
        ];
    }
    
    ob_start();
    ?>
    <div class="pgc-features-grid pgc-features-grid--<?php echo esc_attr($columns); ?>-cols">
        <?php foreach ($features as $i => $feature) : 
            $delay = $animate ? ($i * 100) : 0;
            $icon = $feature['icon'] ?? 'check-circle';
        ?>
            <div class="pgc-feature-item <?php echo $animate ? 'pgc-animate-scale' : ''; ?>" 
                 style="<?php echo $animate ? 'transition-delay: ' . $delay . 'ms' : ''; ?>">
                <div class="pgc-feature-item__icon">
                    <?php echo pgc_get_service_icon($icon); ?>
                </div>
                <h3 class="pgc-feature-item__title"><?php echo esc_html($feature['title'] ?? ''); ?></h3>
                <p class="pgc-feature-item__description"><?php echo esc_html($feature['description'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
