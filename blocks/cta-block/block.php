<?php
/**
 * CTA Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/cta-block', [
    'render_callback' => 'pgc_render_cta_block',
    'attributes' => [
        'style' => ['type' => 'string', 'default' => 'boxed'],
        'backgroundColor' => ['type' => 'string', 'default' => 'primary'],
        'headline' => ['type' => 'string', 'default' => 'Ready to Get Started?'],
        'description' => ['type' => 'string', 'default' => ''],
        'primaryCtaText' => ['type' => 'string', 'default' => 'Get Your Free Quote'],
        'primaryCtaUrl' => ['type' => 'string', 'default' => '/get-a-quote/'],
        'secondaryCtaText' => ['type' => 'string', 'default' => ''],
        'secondaryCtaUrl' => ['type' => 'string', 'default' => ''],
        'urgencyElement' => ['type' => 'string', 'default' => ''],
        'urgencyText' => ['type' => 'string', 'default' => ''],
        'animate' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_cta_block(array $attributes): string {
    $style = $attributes['style'] ?? 'boxed';
    $bg_color = $attributes['backgroundColor'] ?? 'primary';
    $headline = $attributes['headline'] ?? 'Ready to Get Started?';
    $description = $attributes['description'] ?? '';
    $primary_text = $attributes['primaryCtaText'] ?? 'Get Your Free Quote';
    $primary_url = $attributes['primaryCtaUrl'] ?? '/get-a-quote/';
    $secondary_text = $attributes['secondaryCtaText'] ?? '';
    $secondary_url = $attributes['secondaryCtaUrl'] ?? '';
    $urgency = $attributes['urgencyElement'] ?? '';
    $urgency_text = $attributes['urgencyText'] ?? '';
    $animate = $attributes['animate'] ?? true;
    
    $class = 'pgc-cta pgc-cta--' . $style . ' pgc-cta--bg-' . $bg_color;
    if ($animate) {
        $class .= ' pgc-animate';
    }
    
    ob_start();
    ?>
    <section class="<?php echo esc_attr($class); ?>">
        <div class="pgc-container">
            <div class="pgc-cta__content">
                <?php if ($urgency && $urgency_text) : ?>
                    <span class="pgc-cta__urgency"><?php echo esc_html($urgency_text); ?></span>
                <?php endif; ?>
                <h2 class="pgc-cta__headline"><?php echo esc_html($headline); ?></h2>
                <?php if ($description) : ?>
                    <p class="pgc-cta__description"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
                <div class="pgc-cta__buttons">
                    <a href="<?php echo esc_url($primary_url); ?>" class="pgc-btn pgc-btn-accent">
                        <?php echo esc_html($primary_text); ?>
                    </a>
                    <?php if ($secondary_text && $secondary_url) : ?>
                        <a href="<?php echo esc_url($secondary_url); ?>" class="pgc-btn pgc-btn-outline-white">
                            <?php echo esc_html($secondary_text); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
