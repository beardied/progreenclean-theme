<?php
$style = $attributes['style'] ?? 'boxed';
$headline = $attributes['headline'] ?? '';
$description = $attributes['description'] ?? '';
$primary_cta = $attributes['primaryCta'] ?? ['text' => 'Get a Quote', 'url' => '/get-a-quote/'];
$secondary_cta = $attributes['secondaryCta'] ?? null;
$urgency = $attributes['urgencyText'] ?? '';
$bg_color = $attributes['backgroundColor'] ?? 'primary';
$animate = $attributes['animate'] ?? true;
?>
<div class="pgc-cta pgc-cta--<?php echo esc_attr($style); ?> pgc-cta--bg-<?php echo esc_attr($bg_color); ?>" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <div class="pgc-cta__content">
        <h2 class="pgc-cta__headline"><?php echo esc_html($headline); ?></h2>
        <?php if ($description): ?>
            <p class="pgc-cta__description"><?php echo esc_html($description); ?></p>
        <?php endif; ?>
        <?php if ($urgency): ?>
            <p class="pgc-cta__urgency"><?php echo esc_html($urgency); ?></p>
        <?php endif; ?>
        <div class="pgc-cta__buttons">
            <a href="<?php echo esc_url($primary_cta['url']); ?>" class="pgc-button pgc-button--primary pgc-button--large">
                <?php echo esc_html($primary_cta['text']); ?>
            </a>
            <?php if ($secondary_cta && !empty($secondary_cta['text'])): ?>
                <a href="<?php echo esc_url($secondary_cta['url']); ?>" class="pgc-button pgc-button--outline pgc-button--large">
                    <?php echo esc_html($secondary_cta['text']); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
