<?php
/**
 * Template: Locations
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get location pages
$location_pages = get_pages([
    'child_of' => get_the_ID(),
    'sort_column' => 'menu_order',
    'sort_order' => 'ASC',
]);
?>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 16px 0;"><?php the_title(); ?></h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem; max-width: 600px; margin-left: auto; margin-right: auto;">Professional cleaning services across Surrey and South West London</p>
    </div>
</section>

<!-- Locations Grid -->
<section class="pgc-section" style="padding: 80px 0;">
    <div class="pgc-container">
        <?php if ($location_pages) : ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                <?php foreach ($location_pages as $location) : 
                    $location_url = get_permalink($location->ID);
                    $location_title = $location->post_title;
                    // Extract location name from title (remove "Cleaning Services in ")
                    $location_name = str_replace('Cleaning Services in ', '', $location_title);
                ?>
                    <a href="<?php echo esc_url($location_url); ?>" style="text-decoration: none; color: inherit;">
                        <div class="pgc-card pgc-card--glass" style="padding: 40px 30px; text-align: center; transition: all 0.3s ease; height: 100%;">
                            <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 28px;">
                                📍
                            </div>
                            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 12px 0;"><?php echo esc_html($location_name); ?></h3>
                            <p style="color: var(--pgc-gray-500); margin: 0; font-size: 0.95rem;">Professional cleaning services in <?php echo esc_html($location_name); ?></p>
                            <div style="margin-top: 20px; color: var(--pgc-primary); font-weight: 600; font-size: 0.9rem;">View Services →</div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div style="text-align: center; padding: 60px 20px;">
                <p style="color: var(--pgc-gray-500);">No locations found.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pc-secondary) 100%);">
    <div class="pgc-container" style="text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin: 0 0 16px 0;">Need a Custom Quote?</h2>
        <p style="color: rgba(255,255,255,0.9); margin: 0 0 30px 0; font-size: 1.1rem;">Contact us today for a free, no-obligation quote</p>
        <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn" style="background: #fff; color: var(--pgc-primary); padding: 16px 40px; font-weight: 600; border-radius: 8px; display: inline-block; text-decoration: none;">Get a Quote</a>
    </div>
</section>

<style>
.pgc-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -12px rgba(8, 145, 178, 0.2);
}
</style>

<?php get_footer(); ?>
