<?php
$icon = $attributes['icon'] ?? 'star';
$value = $attributes['value'] ?? '4.9';
$label = $attributes['label'] ?? 'Google Rating';
$suffix = $attributes['suffix'] ?? '/5';
$animate = $attributes['animate'] ?? true;
?>
<div class="pgc-trust-badge" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <div class="pgc-trust-badge__icon">
        <?php if ($icon === 'star-filled'): ?>
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M12,17.27L18.18,21L16.54,13.97L22,9.24L14.81,8.62L12,2L9.19,8.62L2,9.24L7.45,13.97L5.82,21L12,17.27Z"/></svg>
        <?php elseif ($icon === 'calendar'): ?>
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M19,19H5V8H19M16,1V3H8V1H6V3H5C3.89,3 3,3.89 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19V5C21,3.89 20.1,3 19,3H18V1M17,12H12V17H17V12Z"/></svg>
        <?php elseif ($icon === 'check-circle'): ?>
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M12 2C6.5 2 2 6.5 2 12S6.5 22 12 22 22 17.5 22 12 17.5 2 12 2M10 17L5 12L6.41 10.59L10 14.17L17.59 6.58L19 8L10 17Z"/></svg>
        <?php else: ?>
            <svg viewBox="0 0 24 24" width="32" height="32" fill="currentColor"><path d="M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/></svg>
        <?php endif; ?>
    </div>
    <div class="pgc-trust-badge__content">
        <div class="pgc-trust-badge__value"><?php echo esc_html($value); ?><span class="pgc-trust-badge__suffix"><?php echo esc_html($suffix); ?></span></div>
        <div class="pgc-trust-badge__label"><?php echo esc_html($label); ?></div>
    </div>
</div>
