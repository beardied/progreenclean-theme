<?php
/**
 * Pricing Table Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/pricing-table', [
    'render_callback' => 'pgc_render_pricing_table_block',
    'attributes' => [
        'tiers' => ['type' => 'array', 'default' => []],
        'serviceSlug' => ['type' => 'string', 'default' => ''],
    ],
]);

function pgc_render_pricing_table_block(array $attributes): string {
    $tiers = $attributes['tiers'] ?? [];
    $service_slug = $attributes['serviceSlug'] ?? '';
    
    // If no tiers provided but service slug is set, get from database
    if (empty($tiers) && $service_slug) {
        $pricing_data = pgc_get_pricing($service_slug);
        foreach ($pricing_data as $item) {
            $tiers[] = [
                'name' => $item['option_value'],
                'price' => '£' . number_format($item['price'], 2),
                'features' => [],
                'highlighted' => false,
            ];
        }
    }
    
    if (empty($tiers)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="pgc-pricing-table">
        <?php foreach ($tiers as $tier) : ?>
            <div class="pgc-pricing-tier <?php echo !empty($tier['highlighted']) ? 'pgc-pricing-tier--highlighted' : ''; ?>">
                <h3 class="pgc-pricing-tier__name"><?php echo esc_html($tier['name'] ?? ''); ?></h3>
                <div class="pgc-pricing-tier__price"><?php echo esc_html($tier['price'] ?? ''); ?></div>
                <?php if (!empty($tier['features'])) : ?>
                    <ul class="pgc-pricing-tier__features">
                        <?php foreach ($tier['features'] as $feature) : ?>
                            <li><?php echo esc_html($feature); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
