<?php
/**
 * Template: Reviews
 * Displays Google Reviews from the database
 */

if (!defined('ABSPATH')) exit;

get_header();

// Get reviews from database
global $wpdb;
$reviews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pgc_reviews ORDER BY create_time DESC");

// Get aggregate data
$avg_rating = get_option('pgc_reviews_average_rating', 0);
$total_count = get_option('pgc_reviews_total_count', 0);
$display_rating = $avg_rating ? number_format($avg_rating, 1) : '0.0';
$display_count = $total_count ? $total_count : 0;
?>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 16px 0;">Customer Reviews</h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem; max-width: 600px; margin-left: auto; margin-right: auto;">See what our customers say about our professional cleaning services</p>
        
        <!-- Overall Rating -->
        <div style="margin-top: 30px; display: flex; align-items: center; justify-content: center; gap: 15px;">
            <div style="display: flex; gap: 4px;">
                <?php 
                $full_stars = floor($display_rating);
                $has_half = ($display_rating - $full_stars) >= 0.5;
                for ($i = 0; $i < 5; $i++) : 
                    if ($i < $full_stars) {
                        echo '<span style="font-size: 28px; color: #f59e0b;">★</span>';
                    } elseif ($i == $full_stars && $has_half) {
                        echo '<span style="font-size: 28px; color: #f59e0b;">★</span>';
                    } else {
                        echo '<span style="font-size: 28px; color: var(--pgc-gray-300);">★</span>';
                    }
                endfor; 
                ?>
            </div>
            <div style="text-align: left;">
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--pgc-gray-900);"><?php echo $display_rating; ?></div>
                <div style="font-size: 0.9rem; color: var(--pgc-gray-500);">Based on <?php echo number_format($display_count); ?> Google reviews</div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Grid -->
<section class="pgc-section" style="padding: 80px 0;">
    <div class="pgc-container">
        <?php if (empty($reviews)) : ?>
            <div style="text-align: center; padding: 60px 20px;">
                <p style="color: var(--pgc-gray-500); font-size: 1.1rem;">No reviews available yet. Check back soon!</p>
            </div>
        <?php else : ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px;">
                <?php 
                $star_map = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
                foreach ($reviews as $review) : 
                    $rating = $star_map[$review->star_rating] ?? 0;
                ?>
                    <div class="pgc-card pgc-card--glass" style="padding: 30px; display: flex; flex-direction: column; height: 100%;">
                        <!-- Rating Stars -->
                        <div style="display: flex; gap: 3px; margin-bottom: 15px;">
                            <?php for ($i = 0; $i < 5; $i++) : ?>
                                <span style="color: <?php echo $i < $rating ? '#f59e0b' : 'var(--pgc-gray-300)'; ?>; font-size: 18px;">★</span>
                            <?php endfor; ?>
                        </div>
                        
                        <!-- Review Text -->
                        <p style="color: var(--pgc-gray-700); line-height: 1.7; margin: 0 0 20px 0; flex-grow: 1; font-size: 1rem;">"<?php echo esc_html($review->comment); ?>"</p>
                        
                        <!-- Date Badge -->
                        <div style="margin-bottom: 15px;">
                            <span style="display: inline-block; background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); color: var(--pgc-primary); padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;">
                                <?php echo date('F j, Y', strtotime($review->create_time)); ?>
                            </span>
                        </div>
                        
                        <!-- Author -->
                        <div style="display: flex; align-items: center; gap: 12px; border-top: 1px solid var(--pgc-gray-100); padding-top: 15px;">
                            <?php if (!empty($review->reviewer_profile_photo_url)) : ?>
                                <img src="<?php echo esc_url($review->reviewer_profile_photo_url); ?>" alt="" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                            <?php else : ?>
                                <div style="width: 45px; height: 45px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 1.1rem;">
                                    <?php echo esc_html(substr($review->reviewer_display_name, 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div style="font-weight: 700; color: var(--pgc-gray-900);"><?php echo esc_html($review->reviewer_display_name); ?></div>
                                <div style="font-size: 0.85rem; color: var(--pgc-gray-500);">Verified Google Review</div>
                            </div>
                        </div>
                        
                        <!-- Owner Reply (if exists) -->
                        <?php if (!empty($review->reply_comment)) : ?>
                            <div style="margin-top: 15px; padding: 15px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-radius: 8px; border-left: 3px solid var(--pgc-primary);">
                                <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--pgc-primary); margin-bottom: 6px;">Response from ProGreenClean</div>
                                <p style="color: var(--pgc-gray-600); margin: 0; font-size: 0.9rem; line-height: 1.5;"><?php echo esc_html($review->reply_comment); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Trust Badges -->
<section style="padding: 60px 0; background: var(--pgc-gray-50);">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px; text-align: center;">
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;"><?php echo number_format($display_count); ?>+</div>
                <div style="color: var(--pgc-gray-600);">Google Reviews</div>
            </div>
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;">10+</div>
                <div style="color: var(--pgc-gray-600);">Years Experience</div>
            </div>
            <div>
                <div style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 8px;"><?php echo $display_rating; ?>★</div>
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
