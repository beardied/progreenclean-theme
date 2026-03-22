<?php
/**
 * Section Background Block
 * Full-width section with customizable background
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/section-background', [
    'render_callback' => 'pgc_render_section_background_block',
    'attributes' => [
        'backgroundType' => ['type' => 'string', 'default' => 'gradient'], // gradient, blue, gray, white, custom
        'customBackground' => ['type' => 'string', 'default' => ''],
        'paddingSize' => ['type' => 'string', 'default' => 'large'], // small, medium, large
        'marginTop' => ['type' => 'string', 'default' => '0'],
        'marginBottom' => ['type' => 'string', 'default' => '0'],
    ],
]);

function pgc_render_section_background_block(array $attributes, string $content): string {
    $bg_type = $attributes['backgroundType'] ?? 'gradient';
    $custom_bg = $attributes['customBackground'] ?? '';
    $padding = $attributes['paddingSize'] ?? 'large';
    $margin_top = $attributes['marginTop'] ?? '0';
    $margin_bottom = $attributes['marginBottom'] ?? '0';
    
    // Background class mapping
    $bg_class = 'pgc-section--' . $bg_type;
    
    // Padding class
    $padding_class = 'pgc-section--padding-' . $padding;
    
    // Build inline styles for custom background and margins
    $inline_styles = [];
    if ($bg_type === 'custom' && $custom_bg) {
        $inline_styles[] = 'background: ' . esc_attr($custom_bg);
    }
    if ($margin_top !== '0') {
        $inline_styles[] = 'margin-top: ' . esc_attr($margin_top);
    }
    if ($margin_bottom !== '0') {
        $inline_styles[] = 'margin-bottom: ' . esc_attr($margin_bottom);
    }
    
    $style_attr = !empty($inline_styles) ? ' style="' . implode('; ', $inline_styles) . '"' : '';
    
    ob_start();
    ?>
    <section class="pgc-section <?php echo esc_attr($bg_class . ' ' . $padding_class); ?>"<?php echo $style_attr; ?>>
        <div class="pgc-container">
            <?php echo $content; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
