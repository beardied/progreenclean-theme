<?php
/**
 * Template: Individual Location Page
 * Used for child pages of Locations
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get parent location info
$parent_page = get_post_parent(get_the_ID());
$location_name = str_replace('Cleaning Services in ', '', get_the_title());
?>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Professional Cleaning Services in <?php echo esc_html($location_name); ?></h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem; max-width: 700px; margin-left: auto; margin-right: auto;">Eco-friendly cleaning solutions for homes and businesses in <?php echo esc_html($location_name); ?> and surrounding areas</p>
    </div>
</section>

<!-- Main Content -->
<section class="pgc-section" style="padding: 60px 0;">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 50px; align-items: start;">
            
            <!-- Content Area -->
            <div>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="entry-content" style="color: var(--pgc-gray-700); line-height: 1.8; font-size: 1.05rem;">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; endif; ?>
                
                <!-- Services Available -->
                <div style="margin-top: 50px;">
                    <h2 style="font-size: 1.75rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 30px 0;">Services Available in <?php echo esc_html($location_name); ?></h2>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                        <a href="<?php echo home_url('/services/window-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">🪟</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">Window Cleaning</div>
                            </div>
                        </a>
                        <a href="<?php echo home_url('/services/gutter-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">🏠</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">Gutter Cleaning</div>
                            </div>
                        </a>
                        <a href="<?php echo home_url('/services/domestic-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">🏡</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">Domestic Cleaning</div>
                            </div>
                        </a>
                        <a href="<?php echo home_url('/services/end-of-tenancy-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">📦</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">End of Tenancy</div>
                            </div>
                        </a>
                        <a href="<?php echo home_url('/services/oven-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">🔥</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">Oven Cleaning</div>
                            </div>
                        </a>
                        <a href="<?php echo home_url('/services/carpet-cleaning/'); ?>" style="text-decoration: none; color: inherit;">
                            <div style="padding: 24px; background: var(--pgc-gray-50); border-radius: 12px; border: 2px solid transparent; transition: all 0.3s;">
                                <div style="font-size: 24px; margin-bottom: 10px;">🧹</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);">Carpet Cleaning</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- CTA Card -->
                <div class="pgc-card pgc-card--glass" style="padding: 30px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Get a Free Quote</h3>
                    <p style="color: var(--pgc-gray-600); margin: 0 0 20px 0; font-size: 0.95rem;">Get an instant estimate for your cleaning needs in <?php echo esc_html($location_name); ?>.</p>
                    <a href="<?php echo home_url('/get-a-quote/'); ?>" style="display: block; text-align: center; width: 100%; padding: 16px; font-size: 16px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); color: #fff; text-decoration: none; border-radius: 8px; font-weight: 600;">Get Your Quote →</a>
                </div>
                
                <!-- Contact Card -->
                <div class="pgc-card pgc-card--glass" style="padding: 30px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Contact Us</h3>
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 13px; color: var(--pgc-gray-500); margin-bottom: 4px;">Phone</div>
                        <a href="tel:<?php echo esc_attr(get_option('pgc_phone', '08001234567')); ?>" style="color: var(--pgc-primary); font-weight: 600; text-decoration: none;"><?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?></a>
                    </div>
                    <div style="margin-bottom: 16px;">
                        <div style="font-size: 13px; color: var(--pgc-gray-500); margin-bottom: 4px;">Email</div>
                        <a href="mailto:<?php echo esc_attr(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?>" style="color: var(--pgc-primary); font-weight: 600; text-decoration: none;"><?php echo esc_html(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?></a>
                    </div>
                    <div>
                        <div style="font-size: 13px; color: var(--pgc-gray-500); margin-bottom: 4px;">Hours</div>
                        <div style="color: var(--pgc-gray-700); font-size: 0.95rem;"><?php echo nl2br(esc_html(get_option('pgc_opening_hours', "Mon-Fri: 8am-6pm\nSat: 9am-2pm"))); ?></div>
                    </div>
                </div>
                
                <!-- Other Locations -->
                <div class="pgc-card pgc-card--glass" style="padding: 30px;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Other Areas</h3>
                    <nav style="display: flex; flex-direction: column; gap: 10px;">
                        <?php
                        $other_locations = get_pages([
                            'child_of' => $parent_page ? $parent_page->ID : 0,
                            'exclude' => get_the_ID(),
                            'sort_column' => 'menu_order',
                        ]);
                        foreach ($other_locations as $loc) :
                            $loc_name = str_replace('Cleaning Services in ', '', $loc->post_title);
                        ?>
                            <a href="<?php echo get_permalink($loc->ID); ?>" style="color: var(--pgc-gray-600); text-decoration: none; padding: 8px 0; border-bottom: 1px solid var(--pgc-gray-100); display: flex; justify-content: space-between; align-items: center;">
                                <span><?php echo esc_html($loc_name); ?></span>
                                <span style="color: var(--pgc-primary);">→</span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%);">
    <div class="pgc-container" style="text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin: 0 0 16px 0;">Ready to Book Your Clean?</h2>
        <p style="color: rgba(255,255,255,0.9); margin: 0 0 30px 0; font-size: 1.1rem;">Join hundreds of satisfied customers in <?php echo esc_html($location_name); ?></p>
        <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn" style="background: #fff; color: var(--pgc-primary); padding: 16px 40px; font-weight: 600; border-radius: 8px; display: inline-block; text-decoration: none;">Get Your Free Quote</a>
    </div>
</section>

<style>
.entry-content h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--pgc-gray-900);
    margin: 40px 0 20px;
}
.entry-content h3 {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--pgc-gray-900);
    margin: 30px 0 15px;
}
.entry-content p {
    margin: 0 0 20px 0;
}
.entry-content ul {
    margin: 0 0 20px 0;
    padding-left: 25px;
}
.entry-content li {
    margin-bottom: 8px;
}
@media (max-width: 768px) {
    .pgc-container > div {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php get_footer(); ?>
