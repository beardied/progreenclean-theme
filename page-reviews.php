<?php
/**
 * Template: Reviews
 */

if (!defined('ABSPATH')) exit;

get_header();

// Sample reviews data - in production this could come from a custom post type or database
$reviews = [
    [
        'name' => 'Sarah Johnson',
        'location' => 'Epsom',
        'rating' => 5,
        'text' => 'Absolutely fantastic service! The team was professional, punctual, and left my windows sparkling clean. Will definitely be using ProGreenClean again.',
        'service' => 'Window Cleaning',
    ],
    [
        'name' => 'Michael Brown',
        'location' => 'Sutton',
        'rating' => 5,
        'text' => 'Had my gutters cleaned and was amazed at how much debris they removed. Great price and excellent customer service. Highly recommend!',
        'service' => 'Gutter Cleaning',
    ],
    [
        'name' => 'Emma Williams',
        'location' => 'Kingston',
        'rating' => 5,
        'text' => 'ProGreenClean did an end of tenancy clean for my apartment. The landlord was so impressed, I got my full deposit back. Thank you!',
        'service' => 'End of Tenancy',
    ],
    [
        'name' => 'David Smith',
        'location' => 'Wimbledon',
        'rating' => 5,
        'text' => 'Regular domestic cleaning service for 6 months now. Always reliable, thorough, and the eco-friendly products are a big plus for our family.',
        'service' => 'Domestic Cleaning',
    ],
    [
        'name' => 'Lisa Anderson',
        'location' => 'Mitcham',
        'rating' => 5,
        'text' => 'My oven looks brand new after their cleaning service. I had tried everything to clean it myself but they worked miracles!',
        'service' => 'Oven Cleaning',
    ],
    [
        'name' => 'James Taylor',
        'location' => 'Croydon',
        'rating' => 5,
        'text' => 'Carpet cleaning was brilliant. Removed stains I thought were permanent. Professional service from start to finish.',
        'service' => 'Carpet Cleaning',
    ],
];
?>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Customer Reviews</h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem; max-width: 600px; margin-left: auto; margin-right: auto;">See what our customers say about our professional cleaning services</p>
        
        <!-- Overall Rating -->
        <div style="margin-top: 30px; display: flex; align-items: center; justify-content: center; gap: 15px;">
            <div style="display: flex; gap: 4px;">
                <?php for ($i = 0; $i < 5; $i++) : ?>
                    <span style="font-size: 28px; color: #f59e0b;">★</span>
                <?php endfor; ?>
            </div>
            <div style="text-align: left;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--pgc-gray-900);">5.0</div>
                <div style="font-size: 0.9rem; color: var(--pgc-gray-500);">Based on 500+ reviews</div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Grid -->
<section class="pgc-section" style="padding: 80px 0;">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
            <?php foreach ($reviews as $review) : ?>
                <div class="pgc-card pgc-card--glass" style="padding: 30px; display: flex; flex-direction: column; height: 100%;">
                    <!-- Rating Stars -->
                    <div style="display: flex; gap: 3px; margin-bottom: 15px;">
                        <?php for ($i = 0; $i < $review['rating']; $i++) : ?>
                            <span style="color: #f59e0b; font-size: 18px;">★</span>
                        <?php endfor; ?>
                    </div>
                    
                    <!-- Review Text -->
                    <p style="color: var(--pgc-gray-700); line-height: 1.7; margin: 0 0 20px 0; flex-grow: 1; font-size: 1rem;">"<?php echo esc_html($review['text']); ?>"</p>
                    
                    <!-- Service Badge -->
                    <div style="margin-bottom: 15px;">
                        <span style="display: inline-block; background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); color: var(--pgc-primary); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;"><?php echo esc_html($review['service']); ?></span>
                    </div>
                    
                    <!-- Author -->
                    <div style="display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--pgc-gray-100); padding-top: 15px;">
                        <div style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.1rem;">
                            <?php echo esc_html(substr($review['name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div style="font-weight: 700; color: var(--pgc-gray-900);"><?php echo esc_html($review['name']); ?></div>
                            <div style="font-size: 0.85rem; color: var(--pgc-gray-500);"><?php echo esc_html($review['location']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Trust Badges -->
<section style="padding: 60px 0; background: var(--pgc-gray-50);">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; text-align: center;">
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;">500+</div>
                <div style="color: var(--pgc-gray-600);">Happy Customers</div>
            </div>
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;">10+</div>
                <div style="color: var(--pgc-gray-600);">Years Experience</div>
            </div>
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;">5★</div>
                <div style="color: var(--pgc-gray-600);">Average Rating</div>
            </div>
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;">100%</div>
                <div style="color: var(--pgc-gray-600);">Eco-Friendly</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 80px 0; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%);">
    <div class="pgc-container" style="text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin: 0 0 16px 0;">Join Our Satisfied Customers</h2>
        <p style="color: rgba(255,255,255,0.9); margin: 0 0 30px 0; font-size: 1.1rem;">Experience the ProGreenClean difference today</p>
        <a href="<?php echo home_url('/get-a-quote/'); ?>" class="pgc-btn" style="background: #fff; color: var(--pgc-primary); padding: 16px 40px; font-weight: 600; border-radius: 8px; display: inline-block; text-decoration: none;">Get Your Free Quote</a>
    </div>
</section>

<?php get_footer(); ?>
