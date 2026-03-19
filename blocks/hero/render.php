<?php
$headline = $attributes['headline'] ?? '';
$subheadline = $attributes['subheadline'] ?? '';
$bg_type = $attributes['backgroundType'] ?? 'image';
$bg_image = $attributes['backgroundImage'] ?? null;
$overlay = $attributes['overlayOpacity'] ?? 40;
$primary_cta = $attributes['primaryCta'] ?? ['text' => 'Get a Quote', 'url' => '/get-a-quote/'];
$secondary_cta = $attributes['secondaryCta'] ?? null;
$size = $attributes['size'] ?? 'large';

$height = $size === 'large' ? 'min-height: 90vh;' : ($size === 'medium' ? 'min-height: 60vh;' : 'min-height: 40vh;');
$bg_style = '';
if ($bg_type === 'image' && $bg_image) {
    $bg_style = 'background-image: url(' . esc_url($bg_image['url']) . '); background-size: cover; background-position: center;';
} else {
    $bg_style = 'background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%);';
}
?>
<section class="pgc-hero pgc-hero--<?php echo esc_attr($size); ?>" style="<?php echo esc_attr($height . $bg_style); ?>">
    <div class="pgc-hero__overlay" style="opacity: <?php echo esc_attr($overlay / 100); ?>"></div>
    <div class="pgc-hero__content">
        <h1 class="pgc-hero__headline"><?php echo esc_html($headline); ?></h1>
        <p class="pgc-hero__subheadline"><?php echo esc_html($subheadline); ?></p>
        <div class="pgc-hero__buttons">
            <a href="<?php echo esc_url($primary_cta['url']); ?>" class="pgc-button pgc-button--primary">
                <?php echo esc_html($primary_cta['text']); ?>
            </a>
            <?php if ($secondary_cta && !empty($secondary_cta['text'])): ?>
                <a href="<?php echo esc_url($secondary_cta['url']); ?>" class="pgc-button pgc-button--outline">
                    <?php echo esc_html($secondary_cta['text']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>
