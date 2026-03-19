<?php
$columns = $attributes['columns'] ?? 2;
$animate = $attributes['animate'] ?? true;

$features = [
    ['icon' => 'leaf', 'title' => 'Eco-Friendly Cleaning', 'description' => 'Our ProGreen commitment means biodegradable products, minimal environmental impact, and safe solutions for families and pets.'],
    ['icon' => 'shield-check', 'title' => 'Fully Insured & Vetted', 'description' => 'Complete peace of mind with comprehensive public liability insurance, DBS-checked staff, and rigorous quality standards.'],
    ['icon' => 'badge-check', 'title' => 'Satisfaction Guaranteed', 'description' => 'Not completely satisfied? We will return and re-clean at no extra charge. Our reputation is built on your complete satisfaction.'],
    ['icon' => 'map-pin', 'title' => 'Local Surrey Experts', 'description' => 'Based in Epsom with intimate knowledge of Surrey properties. From Victorian terraces to modern apartments.'],
];
?>
<div class="pgc-features-grid pgc-features-grid--cols-<?php echo esc_attr($columns); ?>" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <?php foreach ($features as $feature): ?>
        <div class="pgc-feature-item">
            <div class="pgc-feature-item__icon">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="currentColor">
                    <?php if ($feature['icon'] === 'leaf'): ?>
                        <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/>
                    <?php elseif ($feature['icon'] === 'shield-check'): ?>
                        <path d="M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9M12,1L3,5V11C3,16.55 6.84,21.74 12,23C17.16,21.74 21,16.55 21,11V5L12,1Z"/>
                    <?php elseif ($feature['icon'] === 'badge-check'): ?>
                        <path d="M18.5,1.15C17.97,1.15 17.46,1.24 16.96,1.4L16.26,1.62L15.96,1.31C14.66,0.01 12.64,-0.36 10.9,0.4C9.87,0.85 9.07,1.64 8.57,2.58L8.32,3.04L7.84,2.93C7.4,2.83 6.95,2.78 6.5,2.78C3.46,2.78 1,5.24 1,8.28C1,9.87 1.67,11.32 2.76,12.36L3,12.58V20.5A1.5,1.5 0 0,0 4.5,22H19.5A1.5,1.5 0 0,0 21,20.5V12.58L21.24,12.36C22.33,11.32 23,9.87 23,8.28C23,5.24 20.54,2.78 17.5,2.78C17.05,2.78 16.6,2.83 16.16,2.93L15.68,3.04L15.43,2.58C14.93,1.64 14.13,0.85 13.1,0.4C11.36,-0.36 9.34,0.01 8.04,1.31L7.74,1.62L7.04,1.4C6.54,1.24 6.03,1.15 5.5,1.15C2.46,1.15 0,3.61 0,6.65C0,7.94 0.5,9.14 1.34,10.04L12,21L22.66,10.04C23.5,9.14 24,7.94 24,6.65C24,3.61 21.54,1.15 18.5,1.15M10,17L6,13L7.41,11.59L10,14.17L16.59,7.58L18,9L10,17Z"/>
                    <?php else: ?>
                        <path d="M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z"/>
                    <?php endif; ?>
                </svg>
            </div>
            <h3 class="pgc-feature-item__title"><?php echo esc_html($feature['title']); ?></h3>
            <p class="pgc-feature-item__description"><?php echo esc_html($feature['description']); ?></p>
        </div>
    <?php endforeach; ?>
</div>
