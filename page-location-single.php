<?php
/**
 * Template: Individual Location Page
 * Used for child pages of Locations
 */

if (!defined('ABSPATH')) exit;

get_header();

$parent_page = get_post_parent(get_the_ID());
$location_name = str_replace('Cleaning Services in ', '', get_the_title());

// Get services for sidebar
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

<!-- Hero Header -->
<section style="padding: 140px 0 100px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); position: relative; overflow: hidden;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22none%22 stroke=%22rgba(255,255,255,0.1)%22 stroke-width=%220.5%22/></svg>'); background-size: 60px 60px; opacity: 0.5;"></div>
    <div class="pgc-container" style="text-align: center; position: relative; z-index: 1;">
        <span style="display: inline-block; background: rgba(255,255,255,0.2); color: #fff; padding: 10px 24px; border-radius: 30px; font-size: 0.9rem; font-weight: 600; margin-bottom: 24px; backdrop-filter: blur(10px);">📍 <?php echo esc_html($location_name); ?></span>
        <h1 style="font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 800; color: #fff; margin: 0 0 20px 0; line-height: 1.2;">Cleaning Services in <?php echo esc_html($location_name); ?></h1>
        <p style="color: rgba(255,255,255,0.9); margin: 0; font-size: 1.25rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">Professional eco-friendly cleaning for homes and businesses</p>
    </div>
</section>

<!-- Main Content with Sidebar -->
<section style="padding: 80px 0; background: var(--pgc-gray-50);">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: 1fr 380px; gap: 50px; align-items: start;">
            
            <!-- Main Content -->
            <div style="background: #fff; border-radius: 20px; padding: 50px; box-shadow: 0 4px 30px rgba(0,0,0,0.06);">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="entry-content" style="color: var(--pgc-gray-700); line-height: 1.9; font-size: 1.05rem;">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; endif; ?>
            </div>
            
            <!-- Right Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                
                <!-- Quick Quote Card -->
                <div style="background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 20px; padding: 35px; color: #fff;">
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin: 0 0 12px 0;">Get a Free Quote</h3>
                    <p style="margin: 0 0 25px 0; opacity: 0.9; line-height: 1.6;">Instant estimate for your cleaning needs in <?php echo esc_html($location_name); ?>.</p>
                    <a href="/get-a-quote/" style="display: block; text-align: center; padding: 16px; background: #fff; color: var(--pgc-primary); text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 1rem; box-shadow: 0 4px 15px rgba(0,0,0,0.2); transition: transform 0.3s;">Get Your Quote →</a>
                </div>
                
                <!-- Contact Info Card -->
                <div style="background: #fff; border-radius: 20px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 25px 0;">Contact Us</h3>
                    
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', get_option('pgc_phone', '08001234567'))); ?>" style="display: flex; align-items: center; gap: 15px; text-decoration: none; padding: 15px; background: var(--pgc-gray-50); border-radius: 12px; transition: background 0.3s;">
                            <span style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;">📞</span>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px;">Call Us</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);"><?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?></div>
                            </div>
                        </a>
                        
                        <a href="mailto:<?php echo esc_attr(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?>" style="display: flex; align-items: center; gap: 15px; text-decoration: none; padding: 15px; background: var(--pgc-gray-50); border-radius: 12px; transition: background 0.3s;">
                            <span style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px;">✉️</span>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px;">Email Us</div>
                                <div style="font-weight: 600; color: var(--pgc-gray-900);"><?php echo esc_html(get_option('pgc_display_email', 'info@progreenclean.co.uk')); ?></div>
                            </div>
                        </a>
                        
                        <div style="display: flex; align-items: flex-start; gap: 15px; padding: 15px; background: var(--pgc-gray-50); border-radius: 12px;">
                            <span style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 18px; flex-shrink: 0;">🕐</span>
                            <div>
                                <div style="font-size: 12px; color: var(--pgc-gray-500); margin-bottom: 2px;">Opening Hours</div>
                                <div style="font-weight: 500; color: var(--pgc-gray-900); line-height: 1.6; white-space: pre-line;"><?php echo esc_html(get_option('pgc_opening_hours', "Mon-Fri: 8am-6pm\nSat: 9am-2pm")); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Our Services Card -->
                <div style="background: #fff; border-radius: 20px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 20px 0;">Our Services</h3>
                    <nav style="display: flex; flex-direction: column; gap: 8px;">
                        <?php foreach ($services as $service) : ?>
                            <a href="<?php echo get_permalink($service->ID); ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; background: var(--pgc-gray-50); border-radius: 10px; text-decoration: none; color: var(--pgc-gray-700); font-weight: 500; transition: all 0.3s;">
                                <span><?php echo esc_html($service->post_title); ?></span>
                                <span style="color: var(--pgc-primary);">→</span>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>
                
                <!-- Other Locations Card -->
                <div style="background: #fff; border-radius: 20px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 20px 0;">Other Areas</h3>
                    <nav style="display: flex; flex-direction: column; gap: 8px;">
                        <?php
                        $other_locations = get_pages([
                            'child_of' => $parent_page ? $parent_page->ID : 0,
                            'exclude' => get_the_ID(),
                            'sort_column' => 'menu_order',
                            'number' => 6
                        ]);
                        foreach ($other_locations as $loc) :
                            $loc_name = str_replace('Cleaning Services in ', '', $loc->post_title);
                        ?>
                            <a href="<?php echo get_permalink($loc->ID); ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; background: var(--pgc-gray-50); border-radius: 10px; text-decoration: none; color: var(--pgc-gray-700); font-weight: 500; transition: all 0.3s;">
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

<style>
.entry-content h2 {
    font-size: 1.8rem;
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
    margin-bottom: 10px;
}
@media (max-width: 1100px) {
    .pgc-container > div[style*="grid-template-columns"] {
        grid-template-columns: 1fr !important;
    }
}
@media (max-width: 768px) {
    section[style*="padding: 140px"] {
        padding: 100px 0 60px !important;
    }
    div[style*="padding: 50px"] {
        padding: 30px !important;
    }
}
</style>

<?php get_footer(); ?>
