<?php
$animate = $attributes['animate'] ?? true;

$steps = [
    ['number' => 1, 'title' => 'Free Quote', 'description' => 'Get your instant quote online or by phone'],
    ['number' => 2, 'title' => 'Schedule', 'description' => 'Book at your convenience'],
    ['number' => 3, 'title' => 'Clean', 'description' => 'Our trained technicians arrive fully equipped'],
    ['number' => 4, 'title' => 'Inspect', 'description' => 'We check everything meets our exacting standards'],
    ['number' => 5, 'title' => 'Enjoy', 'description' => 'Sparkling results guaranteed to impress'],
];
?>
<div class="pgc-process-steps" data-animate="<?php echo $animate ? 'true' : 'false'; ?>">
    <?php foreach ($steps as $step): ?>
        <div class="pgc-process-step">
            <div class="pgc-process-step__number"><?php echo esc_html($step['number']); ?></div>
            <div class="pgc-process-step__content">
                <h4 class="pgc-process-step__title"><?php echo esc_html($step['title']); ?></h4>
                <p class="pgc-process-step__description"><?php echo esc_html($step['description']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
