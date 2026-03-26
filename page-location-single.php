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

// Get all services (child pages of /services/)
$services_page = get_page_by_path('services');
$services = [];
if ($services_page) {
    $services = get_pages([
        'child_of' => $services_page->ID,
        'sort_column' => 'menu_order',
        'sort_order' => 'ASC'
    ]);
}
?>

<!-- Page Header -->
<section class="pgc-page-header" style="padding: 140px 0 80px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.08) 0%, rgba(16, 185, 129, 0.08) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <span class="pgc-section-eyebrow" style="display: inline-block; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); color: #fff; padding: 8px 20px; border-radius: 20px; font-size: 0.85rem; font-weight: 600; margin-bottom: 20px;">📍 <?php echo esc_html($location_name); ?></span>
        <h1 style="font-size: 3rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 20px 0; line-height: 1.2;">Professional Cleaning Services in <?php echo esc_html($location_name); ?></h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.2rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">Eco-friendly cleaning solutions for homes and businesses in <?php echo esc_html($location_name); ?> and surrounding areas</p>
    </div>
</section>

<!-- Main Content -->
<section class="pgc-section" style="padding: 80px 0;">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 60px; align-items: start;">
            
            <!-- Content Area -->
            <div>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="entry-content" style="color: var(--pgc-gray-700); line-height: 1.9; font-size: 1.05rem;">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; endif; ?>
                
                <!-- Services Available -->
                <div style="margin-top: 60px; padding-top: 60px; border-top: 1px solid var(--pgc-gray-200);">
                    <h2 style="font-size: 2rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 30px 0;">Services Available in <?php echo esc_html($location_name); ?></h2>
                    
                    <?php if (!empty($services)) : ?>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <?php foreach ($services as $service) : ?>
                        <a href="<?php echo get_permalink($service->ID); ?>" style="text-decoration: none; color: inherit;">
                            <div class="pgc-service-link" style="padding: 20px 24px; background: var(--pgc-white); border-radius: 12px; border: 1px solid var(--pgc-gray-200); transition: all 0.3s; display: flex; align-items: center; justify-content: space-between;">
                                <span style="font-weight: 600; color: var(--pgc-gray-900);"><?php echo esc_html($service->post_title); ?></span>
                                <span style="color: var(--pgc-primary); font-size: 1.2rem;">→</span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php else : ?>
                    <p style="color: var(--pgc-gray-500);">No services found.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div>
                <!-- CTA Card -->
                <div class="pgc-card" style="padding: 35px; margin-bottom: 30px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px;">
                    <h3 style="font-size: 1.4rem; font-weight: 700; color: #fff; margin: 0 0 12px 0;">Get a Free Quote</h3>
                    <p style="color: rgba(255,255,255,0.9); margin: 0 0 25px 0; font-size: 0.95rem; line-height: 1.6;">Get an instant estimate for your cleaning needs in <?php echo esc_html($location_name); ?>.</p>
                    <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn" style="display: block; text-align: center; width: 100%; padding: 16px; font-size: 16px; background: #fff; color: var(--pgc-primary); text-decoration: none; border-radius: 8px; font-weight: 600; transition: transform 0.3s;">Get a Quote</a>
                </div>
                
                <!-- Contact Card -->
                <div class="pgc-card" style="padding: 35px; margin-bottom: 30px; background: var(--pgc-white); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid var(--pgc-gray-100);">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 24px 0;">Contact Us</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--pgc-primary); font-size: 18px;">📞</div>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Phone</div>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_option('pgc_phone', '0800 123 4567'))); ?>" style="color: var(--pgc-gray-900); font-weight: 600; text-decoration: none; font-size: 1rem;"><?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?></a>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--pgc-primary); font-size: 18px;">✉️</div>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Email</div>
                                <a href="mailto:<?php echo esc_attr(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?>" style="color: var(--pgc-gray-900); font-weight: 600; text-decoration: none; font-size: 1rem;"><?php echo esc_html(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?></a>
                            </div>
                        </div>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px;">
                            <div style="width: 44px; height: 44px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--pgc-primary); font-size: 18px;">🕐</div>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px; text-transform: uppercase; letter-spacing: 0.5px;">Hours</div>
                                <div style="color: var(--pgc-gray-900); font-size: 0.95rem; line-height: 1.5;"><?php echo nl2br(esc_html(get_option('pgc_opening_hours', "Mon-Fri: 8am-6pm\nSat: 9am-2pm"))); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Other Locations -->
                <div class="pgc-card" style="padding: 35px; background: var(--pgc-white); border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid var(--pgc-gray-100);">
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 20px 0;">Other Areas</h3>
                    <nav style="display: flex; flex-direction: column; gap: 8px;">
                        <?php
                        $other_locations = get_pages([
                            'child_of' => $parent_page ? $parent_page->ID : 0,
                            'exclude' => get_the_ID(),
                            'sort_column' => 'menu_order',
                        ]);
                        foreach ($other_locations as $loc) :
                            $loc_name = str_replace('Cleaning Services in ', '', $loc->post_title);
                        ?>
                            <a href="<?php echo get_permalink($loc->ID); ?>" style="color: var(--pgc-gray-600); text-decoration: none; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; transition: all 0.3s; background: var(--pgc-gray-50);">
                                <span style="font-weight: 500;"><?php echo esc_html($loc_name); ?></span>
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
<section style="padding: 100px 0; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%);">
    <div class="pgc-container" style="text-align: center;">
        <h2 style="font-size: 2.5rem; font-weight: 800; color: #fff; margin: 0 0 20px 0;">Ready to Book Your Clean?</h2>
        <p style="color: rgba(255,255,255,0.9); margin: 0 0 40px 0; font-size: 1.2rem;">Join hundreds of satisfied customers in <?php echo esc_html($location_name); ?></p>
        <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn" style="background: #fff; color: var(--pgc-primary); padding: 20px 48px; font-weight: 600; border-radius: 10px; display: inline-block; text-decoration: none; font-size: 1.1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">Get Your Free Quote</a>
    </div>
</section>

<style>
.entry-content h2 {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--pgc-gray-900);
    margin: 50px 0 24px;
}
.entry-content h3 {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--pgc-gray-900);
    margin: 35px 0 16px;
}
.entry-content p {
    margin: 0 0 24px 0;
}
.entry-content ul {
    margin: 0 0 24px 0;
    padding-left: 25px;
}
.entry-content li {
    margin-bottom: 12px;
}
.pgc-service-link:hover {
    border-color: var(--pgc-primary) !important;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(8, 145, 178, 0.15);
}
@media (max-width: 991px) {
    .pgc-container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
@media (max-width: 600px) {
    .entry-content h2 {
        font-size: 1.5rem;
    }
    .entry-content h3 {
        font-size: 1.2rem;
    }
}
</style>

<?php get_footer(); ?>
