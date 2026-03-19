<?php
/**
 * Process Steps Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/process-steps', [
    'render_callback' => 'pgc_render_process_steps_block',
    'attributes' => [
        'steps' => ['type' => 'array', 'default' => []],
        'animate' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_process_steps_block(array $attributes): string {
    $steps = $attributes['steps'] ?? [];
    $animate = $attributes['animate'] ?? true;
    
    if (empty($steps)) {
        $steps = [
            ['number' => 1, 'title' => 'Free Quote', 'description' => 'Get your instant quote online or by phone'],
            ['number' => 2, 'title' => 'Schedule', 'description' => 'Book at your convenience'],
            ['number' => 3, 'title' => 'Clean', 'description' => 'Our trained technicians arrive fully equipped'],
            ['number' => 4, 'title' => 'Enjoy', 'description' => 'Sit back and enjoy your sparkling clean space'],
        ];
    }
    
    ob_start();
    ?>
    <div class="pgc-process-steps">
        <?php foreach ($steps as $i => $step) : 
            $delay = $animate ? ($i * 150) : 0;
        ?>
            <div class="pgc-process-step <?php echo $animate ? 'pgc-animate' : ''; ?>" 
                 style="<?php echo $animate ? 'transition-delay: ' . $delay . 'ms' : ''; ?>">
                <div class="pgc-process-step__number"><?php echo esc_html($step['number'] ?? ($i + 1)); ?></div>
                <div class="pgc-process-step__content">
                    <h3 class="pgc-process-step__title"><?php echo esc_html($step['title'] ?? ''); ?></h3>
                    <p class="pgc-process-step__description"><?php echo esc_html($step['description'] ?? ''); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
