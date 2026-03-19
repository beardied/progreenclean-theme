<?php
/**
 * Template: Contact Us
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<!-- Compact Header -->
<section style="padding: 120px 0 40px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 12px 0;">Contact Us</h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1.1rem;">We'd love to hear from you</p>
    </div>
</section>

<!-- Contact Section -->
<section style="padding: 60px 0;">
    <div class="pgc-container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
            
            <!-- Contact Info -->
            <div>
                <h2 style="font-size: 1.75rem; font-weight: 700; color: var(--pgc-gray-900); margin: 0 0 24px 0;">Get in Touch</h2>
                <p style="color: var(--pgc-gray-600); margin: 0 0 30px 0; line-height: 1.7;">Have a question or need a custom quote? Fill out the form and we will get back to you within 24 hours.</p>
                
                <div style="margin-bottom: 24px;">
                    <div style="font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 6px;">Phone</div>
                    <a href="tel:<?php echo esc_attr(get_option('pgc_phone', '08001234567')); ?>" style="color: var(--pgc-primary); font-size: 1.1rem; text-decoration: none;"><?php echo esc_html(get_option('pgc_phone', '0800 123 4567')); ?></a>
                </div>
                
                <div style="margin-bottom: 24px;">
                    <div style="font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 6px;">Email</div>
                    <a href="mailto:info@progreenclean.co.uk" style="color: var(--pgc-primary); font-size: 1.1rem; text-decoration: none;">info@progreenclean.co.uk</a>
                </div>
                
                <div>
                    <div style="font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 6px;">Business Hours</div>
                    <div style="color: var(--pgc-gray-600);">Mon - Fri: 8am - 6pm<br>Sat: 9am - 2pm<br>Sun: Closed</div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="pgc-card pgc-card--glass" style="padding: 40px;">
                <?php if (isset($_GET['sent'])) : ?>
                    <div style="background: #10b981; color: #fff; padding: 16px; border-radius: 8px; margin-bottom: 24px; text-align: center;">Thank you! We will be in touch soon.</div>
                <?php endif; ?>
                
                <form method="post" action="/wp-admin/admin-post.php">
                    <input type="hidden" name="action" value="pgc_contact_form">
                    <?php wp_nonce_field('pgc_contact_form'); ?>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 8px;">Name *</label>
                        <input type="text" name="name" required style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; background: #fff; box-sizing: border-box;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 8px;">Email *</label>
                        <input type="email" name="email" required style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; background: #fff; box-sizing: border-box;">
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 8px;">Phone</label>
                        <input type="tel" name="phone" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; background: #fff; box-sizing: border-box;">
                    </div>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 8px;">Message *</label>
                        <textarea name="message" required rows="5" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; background: #fff; resize: vertical; box-sizing: border-box;"></textarea>
                    </div>
                    
                    <button type="submit" class="pgc-btn pgc-btn-primary" style="width: 100%; padding: 16px; font-size: 16px;">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
