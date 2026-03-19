<?php
/**
 * Hero Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/hero', [
    'render_callback' => 'pgc_render_hero_block',
    'attributes' => [
        'headline' => ['type' => 'string', 'default' => 'Professional Cleaning Services'],
        'subheadline' => ['type' => 'string', 'default' => ''],
        'primaryCtaText' => ['type' => 'string', 'default' => 'Get Your Free Quote'],
        'primaryCtaUrl' => ['type' => 'string', 'default' => '/get-a-quote/'],
        'secondaryCtaText' => ['type' => 'string', 'default' => ''],
        'secondaryCtaUrl' => ['type' => 'string', 'default' => ''],
        'backgroundType' => ['type' => 'string', 'default' => 'image'],
        'backgroundImage' => ['type' => 'object', 'default' => null],
        'backgroundColor' => ['type' => 'string', 'default' => ''],
        'overlayOpacity' => ['type' => 'number', 'default' => 40],
        'headlineAnimation' => ['type' => 'string', 'default' => 'words'],
        'trustBar' => ['type' => 'boolean', 'default' => true],
        'size' => ['type' => 'string', 'default' => 'large'],
    ],
]);

function pgc_render_hero_block(array $attributes): string {
    $headline = $attributes['headline'] ?? 'Professional Cleaning Services';
    $subheadline = $attributes['subheadline'] ?? '';
    $primary_text = $attributes['primaryCtaText'] ?? 'Get Your Free Quote';
    $primary_url = $attributes['primaryCtaUrl'] ?? '/get-a-quote/';
    $secondary_text = $attributes['secondaryCtaText'] ?? '';
    $secondary_url = $attributes['secondaryCtaUrl'] ?? '';
    $bg_type = $attributes['backgroundType'] ?? 'image';
    $bg_image = $attributes['backgroundImage'] ?? null;
    $bg_color = $attributes['backgroundColor'] ?? '';
    $overlay = $attributes['overlayOpacity'] ?? 40;
    $trust_bar = $attributes['trustBar'] ?? true;
    $size = $attributes['size'] ?? 'large';
    
    $height_class = $size === 'large' ? 'pgc-hero--large' : ($size === 'medium' ? 'pgc-hero--medium' : 'pgc-hero--small');
    
    $bg_style = '';
    if ($bg_type === 'image' && $bg_image) {
        $bg_style = 'background-image: url(' . esc_url($bg_image['url']) . ');';
    } elseif ($bg_type === 'color' && $bg_color) {
        $bg_style = 'background-color: ' . esc_attr($bg_color) . ';';
    } elseif ($bg_type === 'gradient') {
        $bg_style = 'background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-primary-light) 100%);';
    }
    
    ob_start();
    ?>
    <section class="pgc-hero <?php echo esc_attr($height_class); ?>" style="<?php echo esc_attr($bg_style); ?>">
        <div class="pgc-hero__overlay" style="opacity: <?php echo esc_attr($overlay / 100); ?>"></div>
        <div class="pgc-container">
            <div class="pgc-hero__content">
                <h1 class="pgc-hero__headline pgc-animate"><?php echo esc_html($headline); ?></h1>
                <?php if ($subheadline) : ?>
                    <p class="pgc-hero__subheadline pgc-animate pgc-delay-1"><?php echo esc_html($subheadline); ?></p>
                <?php endif; ?>
                <div class="pgc-hero__buttons pgc-animate pgc-delay-2">
                    <a href="<?php echo esc_url($primary_url); ?>" class="pgc-btn pgc-btn-accent">
                        <?php echo esc_html($primary_text); ?>
                    </a>
                    <?php if ($secondary_text && $secondary_url) : ?>
                        <a href="<?php echo esc_url($secondary_url); ?>" class="pgc-btn pgc-btn-white">
                            <?php echo esc_html($secondary_text); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php if ($trust_bar) : ?>
            <div class="pgc-hero__trust-bar pgc-animate pgc-delay-3">
                <div class="pgc-container">
                    <div class="pgc-trust-badges">
                        <div class="pgc-trust-badge">
                            <span class="pgc-trust-badge__icon"><?php echo pgc_get_service_icon('star-filled'); ?></span>
                            <span class="pgc-trust-badge__value">4.9</span>
                            <span class="pgc-trust-badge__label">/5 Google Rating</span>
                        </div>
                        <div class="pgc-trust-badge">
                            <span class="pgc-trust-badge__icon"><?php echo pgc_get_service_icon('calendar'); ?></span>
                            <span class="pgc-trust-badge__value">10+</span>
                            <span class="pgc-trust-badge__label">Years Experience</span>
                        </div>
                        <div class="pgc-trust-badge">
                            <span class="pgc-trust-badge__icon"><?php echo pgc_get_service_icon('check-circle'); ?></span>
                            <span class="pgc-trust-badge__value">5000+</span>
                            <span class="pgc-trust-badge__label">Jobs Completed</span>
                        </div>
                        <div class="pgc-trust-badge">
                            <span class="pgc-trust-badge__icon"><?php echo pgc_get_service_icon('shield-check'); ?></span>
                            <span class="pgc-trust-badge__label">Fully Insured</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}
