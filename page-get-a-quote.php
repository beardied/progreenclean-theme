<?php
/**
 * Template Name: Get a Quote
 * Description: Page template with quote wizard - editor content appears above the wizard
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get editor content
$editor_content = apply_filters('the_content', get_post_field('post_content', get_the_ID()));
?>

<!-- Compact Header -->
<section style="padding: 120px 0 40px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 12px 0;"><?php the_title(); ?></h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem;">Get an instant estimate for our professional cleaning services</p>
    </div>
</section>

<!-- Editor Content (if any) -->
<?php if (!empty($editor_content) && trim(strip_tags($editor_content)) !== '') : ?>
<section class="pgc-section" style="padding: 40px 0 20px;">
    <div class="pgc-container" style="max-width: 800px; margin: 0 auto;">
        <div class="entry-content">
            <?php echo $editor_content; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Quote Wizard -->
<section class="pgc-section" style="padding: 40px 0 60px;">
    <div class="pgc-container">
        <div class="pgc-card pgc-card--glass" style="max-width: 800px; margin: 0 auto; padding: 40px;">
            <div id="quote-wizard-container">
                <!-- Wizard content loaded by JavaScript -->
                <div style="text-align: center; padding: 40px;">
                    <div style="border: 3px solid var(--pgc-gray-200); border-top-color: var(--pgc-primary); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto;"></div>
                    <p style="margin-top: 20px; color: var(--pgc-gray-500);">Loading quote wizard...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}

.quote-option:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px -8px rgba(8, 145, 178, 0.25);
    border-color: var(--pgc-primary) !important;
}

.quote-option.selected {
    border-color: var(--pgc-primary) !important;
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%) !important;
}

input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--pgc-primary) !important;
}

@media (max-width: 768px) {
    .pgc-card {
        padding: 24px !important;
    }
}
</style>

<?php get_footer(); ?>
