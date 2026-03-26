<?php
/**
 * ProGreenClean Theme Functions
 */

if (!defined('ABSPATH')) exit;

define('PGC_VERSION', '3.0.7');
define('PGC_PATH', get_template_directory());
define('PGC_URL', get_template_directory_uri());

/**
 * Theme Setup
 */
add_action('after_setup_theme', function() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('custom-spacing');
    
    register_nav_menus([
        'primary' => __('Primary Menu', 'progreenclean'),
        'footer-services' => __('Footer - Services', 'progreenclean'),
        'footer-locations' => __('Footer - Locations', 'progreenclean'),
    ]);
});

/**
 * Disable All Comments
 */
add_action('after_setup_theme', function() {
    // Remove comment support from all post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}, 100);

// Close comments on frontend
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments from admin menu
add_action('admin_menu', function() {
    remove_menu_page('edit-comments.php');
});

// Remove comments from admin bar
add_action('wp_before_admin_bar_render', function() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
});

// Disable comments feed
add_action('wp', function() {
    if (is_comment_feed()) {
        wp_redirect(home_url(), 301);
        exit;
    }
});

/**
 * Page Meta Box - Price Display Settings
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'pgc_page_price_settings',
        'Price Display Settings',
        'pgc_render_page_price_meta_box',
        'page',
        'side',
        'default'
    );
});

function pgc_render_page_price_meta_box($post) {
    wp_nonce_field('pgc_page_price_meta', 'pgc_page_price_nonce');
    
    $price_key = get_post_meta($post->ID, '_pgc_price_key', true);
    $price_prefix = get_post_meta($post->ID, '_pgc_price_prefix', true);
    $price_suffix = get_post_meta($post->ID, '_pgc_price_suffix', true);
    ?>
    <p>
        <label for="pgc_price_key">Price Key:</label><br>
        <input type="text" id="pgc_price_key" name="pgc_price_key" value="<?php echo esc_attr($price_key); ?>" style="width: 100%;">
        <small>Enter the price key from the pricing table (e.g., "ow_win_2bed_oneoff")</small>
    </p>
    <p>
        <label for="pgc_price_prefix">Price Prefix:</label><br>
        <input type="text" id="pgc_price_prefix" name="pgc_price_prefix" value="<?php echo esc_attr($price_prefix); ?>" style="width: 100%;">
        <small>Text before price (e.g., "From ")</small>
    </p>
    <p>
        <label for="pgc_price_suffix">Price Suffix:</label><br>
        <input type="text" id="pgc_price_suffix" name="pgc_price_suffix" value="<?php echo esc_attr($price_suffix); ?>" style="width: 100%;">
        <small>Text after price (e.g., "/hr" or "+")</small>
    </p>
    <p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;">
        <strong>Shortcode:</strong><br>
        <code>[pgc_children parent="<?php echo esc_html($post->post_title); ?>"]</code>
    </p>
    <?php
}

add_action('save_post', function($post_id) {
    if (!isset($_POST['pgc_page_price_nonce']) || !wp_verify_nonce($_POST['pgc_page_price_nonce'], 'pgc_page_price_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_page', $post_id)) {
        return;
    }
    
    if (isset($_POST['pgc_price_key'])) {
        update_post_meta($post_id, '_pgc_price_key', sanitize_text_field($_POST['pgc_price_key']));
    }
    if (isset($_POST['pgc_price_prefix'])) {
        update_post_meta($post_id, '_pgc_price_prefix', sanitize_text_field($_POST['pgc_price_prefix']));
    }
    if (isset($_POST['pgc_price_suffix'])) {
        update_post_meta($post_id, '_pgc_price_suffix', sanitize_text_field($_POST['pgc_price_suffix']));
    }
});

/**
 * Enqueue Scripts and Styles
 */
add_action('wp_enqueue_scripts', function() {
    $cache_buster = '3.0.7.' . time();
    wp_enqueue_style('progreenclean-style', get_stylesheet_uri(), [], $cache_buster);
    wp_enqueue_style('progreenclean-blocks', PGC_URL . '/assets/css/blocks.css', [], $cache_buster);
    wp_enqueue_script('progreenclean-main', PGC_URL . '/assets/js/main.js', [], $cache_buster, true);
    
    if (is_page('get-a-quote')) {
        wp_enqueue_script('progreenclean-quote-v2', PGC_URL . '/assets/js/quote-wizard-v2.js', ['jquery'], $cache_buster, true);
        wp_localize_script('progreenclean-quote-v2', 'pgc_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pgc_nonce'),
            'conservatory_sizes' => [
                'small' => pgc_get_text_option('ow_win_cons_roof_small_size') ?: '3x3m',
                'medium' => pgc_get_text_option('ow_win_cons_roof_medium_size') ?: '4x4m',
                'large' => pgc_get_text_option('ow_win_cons_roof_large_size') ?: '5x5m',
            ],
            'carpet_sizes' => [
                'small' => pgc_get_text_option('ow_carpet_small_size') ?: '4x4m',
                'medium' => pgc_get_text_option('ow_carpet_medium_size') ?: '5x5m',
                'large' => pgc_get_text_option('ow_carpet_large_size') ?: '6x6m',
            ],
        ]);
    }
});

/**
 * Admin Menu - Pricing and Settings
 */
add_action('admin_menu', function() {
    add_menu_page('PGC Pricing', 'PGC Pricing', 'manage_options', 'pgc-pricing', 'pgc_render_pricing_page', 'dashicons-money-alt', 30);
    add_submenu_page('pgc-pricing', 'PGC Settings', 'Settings', 'manage_options', 'pgc-settings', 'pgc_render_settings_page');
});

/**
 * Settings Page
 */
function pgc_render_settings_page() {
    if (isset($_POST['pgc_save_settings']) && check_admin_referer('pgc_settings_nonce')) {
        update_option('pgc_quote_email', sanitize_email($_POST['quote_email']));
        update_option('pgc_contact_email', sanitize_email($_POST['contact_email']));
        update_option('pgc_from_email', sanitize_email($_POST['from_email']));
        update_option('pgc_phone', sanitize_text_field($_POST['phone']));
        echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
    }
    
    $quote_email = get_option('pgc_quote_email', get_option('admin_email'));
    $contact_email = get_option('pgc_contact_email', get_option('admin_email'));
    $from_email = get_option('pgc_from_email', 'quotes@progreenclean.co.uk');
    $phone = get_option('pgc_phone', '0800 123 4567');
    ?>
    <div class="wrap">
        <h1>ProGreenClean Settings</h1>
        <form method="post">
            <?php wp_nonce_field('pgc_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="quote_email">Quote Notifications Email</label></th>
                    <td><input type="email" id="quote_email" name="quote_email" value="<?php echo esc_attr($quote_email); ?>" class="regular-text">
                        <p class="description">Where quote requests are sent</p></td>
                </tr>
                <tr>
                    <th><label for="contact_email">Contact Form Email</label></th>
                    <td><input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr($contact_email); ?>" class="regular-text">
                        <p class="description">Where contact form submissions are sent</p></td>
                </tr>
                <tr>
                    <th><label for="from_email">From Email Address</label></th>
                    <td><input type="email" id="from_email" name="from_email" value="<?php echo esc_attr($from_email); ?>" class="regular-text">
                        <p class="description">Email address shown as sender for customer emails</p></td>
                </tr>
                <tr>
                    <th><label for="phone">Company Phone</label></th>
                    <td><input type="text" id="phone" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
                </tr>
            </table>
            <?php submit_button('Save Settings', 'primary', 'pgc_save_settings'); ?>
        </form>
    </div>
    <?php
}

/**
 * Handle All Includes
 */
//require_once PGC_PATH . '/inc/blocks.php';
require_once PGC_PATH . '/inc/pricing-config.php';
//require_once PGC_PATH . '/inc/database.php';
//require_once PGC_PATH . '/inc/quote-system.php';
//require_once PGC_PATH . '/inc/email-templates.php';
//require_once PGC_PATH . '/inc/schema-markup.php';
//require_once PGC_PATH . '/inc/template-functions.php';
//require_once PGC_PATH . '/inc/admin-pricing.php';
//require_once PGC_PATH . '/inc/admin-menu.php';


/**
 * Pricing Page with Multi-Column Layout
 */
function pgc_render_pricing_page() {
    if (isset($_POST['pgc_save_pricing']) && check_admin_referer('pgc_pricing_nonce')) {
        foreach ($_POST['prices'] as $key => $price) {
            update_option('pgc_price_' . sanitize_text_field($key), floatval($price));
        }
        echo '<div class="notice notice-success"><p>Pricing saved!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>ProGreenClean Pricing</h1>
        <form method="post">
            <?php wp_nonce_field('pgc_pricing_nonce'); ?>
            
            <!-- Window Cleaning -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Window Cleaning</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Property Size</th>
                                <th style="width: 25%; text-align: center;">4 Weeks (£)</th>
                                <th style="width: 25%; text-align: center;">8 Weeks (£)</th>
                                <th style="width: 25%; text-align: center;">12 Weeks (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>2 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-2bed-4week]" value="<?php echo esc_attr(get_option('pgc_price_window-2bed-4week', 24.50)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-2bed-8week]" value="<?php echo esc_attr(get_option('pgc_price_window-2bed-8week', 27)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-2bed-12week]" value="<?php echo esc_attr(get_option('pgc_price_window-2bed-12week', 30)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>3 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-3bed-4week]" value="<?php echo esc_attr(get_option('pgc_price_window-3bed-4week', 29)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-3bed-8week]" value="<?php echo esc_attr(get_option('pgc_price_window-3bed-8week', 31.50)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-3bed-12week]" value="<?php echo esc_attr(get_option('pgc_price_window-3bed-12week', 35)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>4 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-4bed-4week]" value="<?php echo esc_attr(get_option('pgc_price_window-4bed-4week', 33.50)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-4bed-8week]" value="<?php echo esc_attr(get_option('pgc_price_window-4bed-8week', 36)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-4bed-12week]" value="<?php echo esc_attr(get_option('pgc_price_window-4bed-12week', 40)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>5 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-5bed-4week]" value="<?php echo esc_attr(get_option('pgc_price_window-5bed-4week', 38)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-5bed-8week]" value="<?php echo esc_attr(get_option('pgc_price_window-5bed-8week', 41)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[window-5bed-12week]" value="<?php echo esc_attr(get_option('pgc_price_window-5bed-12week', 45)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                    <h3 style="margin: 20px 0 10px; font-size: 14px; color: #666;">Add-ons</h3>
                    <table class="wp-list-table widefat striped">
                        <tbody>
                            <tr>
                                <td style="width: 50%;">Conservatory</td>
                                <td style="width: 50%;"><input type="number" name="prices[window-conservatory]" value="<?php echo esc_attr(get_option('pgc_price_window-conservatory', 10)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td>Extension</td>
                                <td><input type="number" name="prices[window-extension]" value="<?php echo esc_attr(get_option('pgc_price_window-extension', 6)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td>Velux Window (each)</td>
                                <td><input type="number" name="prices[window-velux]" value="<?php echo esc_attr(get_option('pgc_price_window-velux', 4)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td>Skylight (each)</td>
                                <td><input type="number" name="prices[window-skylight]" value="<?php echo esc_attr(get_option('pgc_price_window-skylight', 6)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Gutter Cleaning -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Gutter Cleaning</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 34%;">Property Size</th>
                                <th style="width: 33%; text-align: center;">Semi-Detached (£)</th>
                                <th style="width: 33%; text-align: center;">Detached (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>2 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-2bed-semi]" value="<?php echo esc_attr(get_option('pgc_price_gutter-2bed-semi', 75)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-2bed-detached]" value="<?php echo esc_attr(get_option('pgc_price_gutter-2bed-detached', 90)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>3 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-3bed-semi]" value="<?php echo esc_attr(get_option('pgc_price_gutter-3bed-semi', 90)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-3bed-detached]" value="<?php echo esc_attr(get_option('pgc_price_gutter-3bed-detached', 110)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>4 Bed</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-4bed-semi]" value="<?php echo esc_attr(get_option('pgc_price_gutter-4bed-semi', 110)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-4bed-detached]" value="<?php echo esc_attr(get_option('pgc_price_gutter-4bed-detached', 140)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                            <tr>
                                <td><strong>5 Bed / Townhouse</strong></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-5bed]" value="<?php echo esc_attr(get_option('pgc_price_gutter-5bed', 140)); ?>" step="0.01" style="width: 100px;"></td>
                                <td style="text-align: center;"><input type="number" name="prices[gutter-townhouse]" value="<?php echo esc_attr(get_option('pgc_price_gutter-townhouse', 190)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Domestic Cleaning -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Domestic Cleaning</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <tbody>
                            <tr>
                                <td style="width: 50%;"><strong>Hourly Rate</strong></td>
                                <td style="width: 50%;"><input type="number" name="prices[domestic-hourly]" value="<?php echo esc_attr(get_option('pgc_price_domestic-hourly', 25)); ?>" step="0.01" style="width: 100px;"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- End of Tenancy -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">End of Tenancy</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Property Size</th>
                                <th style="width: 50%;">Price (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Studio Flat</td><td><input type="number" name="prices[endoftenancy-studio]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-studio', 190)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>1 Bedroom</td><td><input type="number" name="prices[endoftenancy-1bed]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-1bed', 230)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>2 Bedroom</td><td><input type="number" name="prices[endoftenancy-2bed]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-2bed', 270)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>3 Bedroom</td><td><input type="number" name="prices[endoftenancy-3bed]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-3bed', 320)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>4 Bedroom</td><td><input type="number" name="prices[endoftenancy-4bed]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-4bed', 380)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>6+ Bedroom</td><td><input type="number" name="prices[endoftenancy-6bed]" value="<?php echo esc_attr(get_option('pgc_price_endoftenancy-6bed', 450)); ?>" step="0.01" style="width: 100px;"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Oven Cleaning -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Oven Cleaning</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Oven Type</th>
                                <th style="width: 50%;">Price (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Single Oven</td><td><input type="number" name="prices[oven-single]" value="<?php echo esc_attr(get_option('pgc_price_oven-single', 68)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Double Oven</td><td><input type="number" name="prices[oven-double]" value="<?php echo esc_attr(get_option('pgc_price_oven-double', 85)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Range Cooker</td><td><input type="number" name="prices[oven-range]" value="<?php echo esc_attr(get_option('pgc_price_oven-range', 105)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>AGA</td><td><input type="number" name="prices[oven-aga]" value="<?php echo esc_attr(get_option('pgc_price_oven-aga', 150)); ?>" step="0.01" style="width: 100px;"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Carpet Cleaning -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Carpet Cleaning</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Room Size</th>
                                <th style="width: 50%;">Price (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Small Room (4x4m)</td><td><input type="number" name="prices[carpet-small]" value="<?php echo esc_attr(get_option('pgc_price_carpet-small', 62)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Medium Room (5x5m)</td><td><input type="number" name="prices[carpet-medium]" value="<?php echo esc_attr(get_option('pgc_price_carpet-medium', 73)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Large Room (6x6m)</td><td><input type="number" name="prices[carpet-large]" value="<?php echo esc_attr(get_option('pgc_price_carpet-large', 90)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Stairs + Landing</td><td><input type="number" name="prices[carpet-stairs]" value="<?php echo esc_attr(get_option('pgc_price_carpet-stairs', 101)); ?>" step="0.01" style="width: 100px;"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Extras -->
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: #f1f1f1; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <h2 style="margin: 0; font-size: 18px;">Extras</h2>
                    <span class="dashicons dashicons-arrow-up"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    <table class="wp-list-table widefat striped">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Service</th>
                                <th style="width: 50%;">Price (£)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Inside Fridge</td><td><input type="number" name="prices[extra-fridge]" value="<?php echo esc_attr(get_option('pgc_price_extra-fridge', 40)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Inside Microwave</td><td><input type="number" name="prices[extra-microwave]" value="<?php echo esc_attr(get_option('pgc_price_extra-microwave', 25)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Interior Window (each)</td><td><input type="number" name="prices[extra-interior-window]" value="<?php echo esc_attr(get_option('pgc_price_extra-interior-window', 6)); ?>" step="0.01" style="width: 100px;"></td></tr>
                            <tr><td>Bedsheet Changing (per bed)</td><td><input type="number" name="prices[extra-bedsheet]" value="<?php echo esc_attr(get_option('pgc_price_extra-bedsheet', 6)); ?>" step="0.01" style="width: 100px;"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php submit_button('Save All Pricing', 'primary', 'pgc_save_pricing'); ?>
        </form>
    </div>
    <?php
}

/**
 * AJAX: Calculate Quote V2 with Upsells
 */
add_action('wp_ajax_pgc_calculate_quote_v2', 'pgc_ajax_calculate_quote_v2');
add_action('wp_ajax_nopriv_pgc_calculate_quote_v2', 'pgc_ajax_calculate_quote_v2');

function pgc_ajax_calculate_quote_v2() {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $service = sanitize_text_field($_POST['service'] ?? '');
    $answers = json_decode(stripslashes($_POST['answers'] ?? '{}'), true);
    
    $total = 0;
    $breakdown = [];
    $upsells = [];
    
    switch ($service) {
        case 'window-cleaning':
            $bed = $answers['bedrooms'] ?? '1-2';
            $freq = $answers['frequency'] ?? '4week';
            $bedKey = str_replace(['1-2', '3', '4', '5', '6+'], ['2bed', '3bed', '4bed', '5bed', '5bed'], $bed);
            $priceKey = 'pgc_price_window-' . $bedKey . '-' . $freq;
            $basePrice = floatval(get_option($priceKey, 24.50));
            $total += $basePrice;
            $breakdown[] = ['item' => 'Window cleaning (' . $bed . ', ' . $freq . ')', 'price' => $basePrice];
            
            if (($answers['has_extension'] ?? 'no') === 'yes') {
                $total += 6;
                $breakdown[] = ['item' => 'Extension', 'price' => 6];
            }
            if (($answers['has_conservatory'] ?? 'no') === 'yes') {
                $total += 10;
                $breakdown[] = ['item' => 'Conservatory', 'price' => 10];
            } else {
                $upsells[] = ['item' => 'Add conservatory cleaning', 'price' => 10, 'id' => 'conservatory'];
            }
            if (($answers['skylights'] ?? 'no') === 'yes') {
                $skylightCount = intval($answers['skylights_count'] ?? 1);
                if ($skylightCount > 4) $skylightCount = 4;
                $skylightPrice = $skylightCount * 6;
                $total += $skylightPrice;
                $breakdown[] = ['item' => 'Skylights (' . $skylightCount . ')', 'price' => $skylightPrice];
            }
            if (($answers['velux'] ?? 'no') === 'yes') {
                $veluxCount = intval($answers['velux_count'] ?? 1);
                if ($veluxCount > 4) $veluxCount = 4;
                $veluxPrice = $veluxCount * 4;
                $total += $veluxPrice;
                $breakdown[] = ['item' => 'Velux windows (' . $veluxCount . ')', 'price' => $veluxPrice];
            }

            break;
            
        case 'gutter-cleaning':
            $propType = $answers['property_type'] ?? 'semi-detached';
            $bedrooms = $answers['bedrooms'] ?? '1-2';
            
            if ($propType === 'townhouse') {
                $basePrice = floatval(get_option('pgc_price_gutter-townhouse', 190));
            } else {
                $bedKey = str_replace(['1-2', '3', '4', '5', '6+'], ['2bed', '3bed', '4bed', '5bed', '5bed'], $bedrooms);
                $typeKey = ($propType === 'detached') ? 'detached' : 'semi';
                $priceKey = 'pgc_price_gutter-' . $bedKey . '-' . $typeKey;
                $basePrice = floatval(get_option($priceKey, 100));
            }
            $total += $basePrice;
            $breakdown[] = ['item' => 'Gutter cleaning (' . $propType . ', ' . $bedrooms . ')', 'price' => $basePrice];
            
            if (($answers['has_extension'] ?? 'no') === 'yes') {
                $total += 10;
                $breakdown[] = ['item' => 'Extension', 'price' => 10];
            }

            break;
            
        case 'domestic-cleaning':
            $hours = intval($answers['hours'] ?? 2);
            if ($hours > 5) $hours = 5;
            $hourlyRate = floatval(get_option('pgc_price_domestic-hourly', 25));
            $basePrice = $hourlyRate * $hours;
            $total += $basePrice;
            $breakdown[] = ['item' => 'Domestic cleaning (' . $hours . ' hours)', 'price' => $basePrice];
            
            $extras = $answers['extras'] ?? [];
            if (is_array($extras)) {
                if (in_array('fridge', $extras)) {
                    $total += 40;
                    $breakdown[] = ['item' => 'Inside Fridge', 'price' => 40];
                } else {
                    $upsells[] = ['item' => 'Add fridge cleaning', 'price' => 40, 'id' => 'fridge'];
                }
                if (in_array('microwave', $extras)) {
                    $total += 25;
                    $breakdown[] = ['item' => 'Inside Microwave', 'price' => 25];
                } else {
                    $upsells[] = ['item' => 'Add microwave cleaning', 'price' => 25, 'id' => 'microwave'];
                }
                if (in_array('oven', $extras)) {
                    $ovenPrice = floatval(get_option('pgc_price_oven-single', 68));
                    $total += $ovenPrice;
                    $breakdown[] = ['item' => 'Oven clean', 'price' => $ovenPrice];
                } else {
                    $upsells[] = ['item' => 'Add oven cleaning', 'price' => 68, 'id' => 'oven'];
                }
                if (in_array('windows', $extras)) {
                    $total += 6;
                    $breakdown[] = ['item' => 'Interior windows (1)', 'price' => 6];
                }
                if (in_array('bedsheets', $extras)) {
                    $total += 6;
                    $breakdown[] = ['item' => 'Bedsheet changing (1 bed)', 'price' => 6];
                }
            }
            $upsells[] = ['item' => 'Upgrade to weekly service (10% off)', 'price' => -round($basePrice * 0.1, 2), 'id' => 'weekly_discount'];
            break;
            
        case 'end-of-tenancy':
            $bed = $answers['bedrooms'] ?? '2';
            $priceKey = 'pgc_price_endoftenancy-' . $bed;
            $basePrice = floatval(get_option($priceKey, 270));
            $total += $basePrice;
            $breakdown[] = ['item' => 'End of tenancy (' . $bed . ' bed)', 'price' => $basePrice];
            
            if (($answers['carpet_cleaning'] ?? 'no') === 'yes') {
                $rooms = intval($answers['carpet_rooms'] ?? 1);
                $carpetPrice = $rooms * 62;
                $total += $carpetPrice;
                $breakdown[] = ['item' => 'Carpet cleaning (' . $rooms . ' rooms)', 'price' => $carpetPrice];
                
                $stairs = $answers['stairs_landing'] ?? 'no';
                if ($stairs !== 'no') {
                    $total += 101;
                    $breakdown[] = ['item' => 'Stairs/landing', 'price' => 101];
                }
            } else {
                $upsells[] = ['item' => 'Add carpet cleaning', 'price' => 62, 'id' => 'carpet'];
            }
            
            $ovenType = $answers['oven_cleaning'] ?? 'no';
            if ($ovenType !== 'no') {
                $ovenPrice = floatval(get_option('pgc_price_oven-' . $ovenType, 68));
                $total += $ovenPrice;
                $breakdown[] = ['item' => 'Oven cleaning (' . $ovenType . ')', 'price' => $ovenPrice];
            } else {
                $upsells[] = ['item' => 'Add oven cleaning (single)', 'price' => 68, 'id' => 'oven'];
            }
            
            if (($answers['fridge_cleaning'] ?? 'no') === 'yes') {
                $total += 40;
                $breakdown[] = ['item' => 'Fridge cleaning', 'price' => 40];
            } else {
                $upsells[] = ['item' => 'Add fridge cleaning', 'price' => 40, 'id' => 'fridge'];
            }
            break;
            
        case 'oven-cleaning':
            $type = $answers['oven_type'] ?? 'single';
            $price = floatval(get_option('pgc_price_oven-' . $type, 68));
            $total += $price;
            $breakdown[] = ['item' => 'Oven cleaning (' . $type . ')', 'price' => $price];
            
            $extras = $answers['extras'] ?? [];
            if (is_array($extras)) {
                if (in_array('fridge', $extras)) {
                    $total += 40;
                    $breakdown[] = ['item' => 'Fridge', 'price' => 40];
                } else {
                    $upsells[] = ['item' => 'Add fridge cleaning', 'price' => 40, 'id' => 'fridge'];
                }
                if (in_array('microwave', $extras)) {
                    $total += 25;
                    $breakdown[] = ['item' => 'Microwave', 'price' => 25];
                } else {
                    $upsells[] = ['item' => 'Add microwave cleaning', 'price' => 25, 'id' => 'microwave'];
                }
            }
            $upsells[] = ['item' => 'Add hob deep clean', 'price' => 35, 'id' => 'hob'];
            break;
            
        case 'carpet-cleaning':
            $small = intval($answers['small_rooms'] ?? 0);
            $medium = intval($answers['medium_rooms'] ?? 0);
            $large = intval($answers['large_rooms'] ?? 0);
            
            if ($small > 0) {
                $smallPrice = $small * 62;
                $total += $smallPrice;
                $breakdown[] = ['item' => 'Small rooms (' . $small . ')', 'price' => $smallPrice];
            }
            if ($medium > 0) {
                $mediumPrice = $medium * 73;
                $total += $mediumPrice;
                $breakdown[] = ['item' => 'Medium rooms (' . $medium . ')', 'price' => $mediumPrice];
            }
            if ($large > 0) {
                $largePrice = $large * 90;
                $total += $largePrice;
                $breakdown[] = ['item' => 'Large rooms (' . $large . ')', 'price' => $largePrice];
            }
            
            $stairs = $answers['stairs'] ?? 'no';
            if ($stairs !== 'no') {
                $total += 101;
                $breakdown[] = ['item' => 'Stairs/landing', 'price' => 101];
            }
            
            $totalRooms = $small + $medium + $large;
            if ($totalRooms > 0) {
                $upsells[] = ['item' => 'Add stain protection treatment', 'price' => $totalRooms * 15, 'id' => 'stain_protection'];
            }
            break;
            
        case 'pressure-washing':
        case 'one-off-cleaning':
        case 'commercial-window':
        case 'office-cleaning':
        case 'gardening':
        case 'builders-cleaning':
            wp_send_json_success(['requires_manual' => true, 'reason' => 'Please contact us for a custom quote']);
            return;
            
        default:
            wp_send_json_success(['requires_manual' => true, 'reason' => 'Please contact us for a quote']);
            return;
    }
    
    wp_send_json_success([
        'base_price' => $total,
        'total_price' => $total,
        'breakdown' => $breakdown,
        'upsells' => $upsells,
        'requires_manual' => false,
    ]);
}

/**
 * Styled Email Templates
 */
function pgc_get_email_header() {
    // Base64 encoded logo to prevent email client blocking
    $logo_base64 = file_exists(PGC_PATH . '/assets/images/logo-email.base64') 
        ? file_get_contents(PGC_PATH . '/assets/images/logo-email.base64')
        : '';
    $logo_src = $logo_base64 ? 'data:image/png;base64,' . $logo_base64 : PGC_URL . '/assets/images/logo-email.png';
    
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProGreenClean</title>
</head>
<body style="margin: 0; padding: 0; font-family: \'Plus Jakarta Sans\', -apple-system, BlinkMacSystemFont, sans-serif; background-color: #f8fafc;">
    <table role="presentation" style="width: 100%; border-collapse: collapse;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table role="presentation" style="width: 600px; border-collapse: collapse; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #0891b2 0%, #10b981 100%); padding: 32px; text-align: center;">
                            <img src="' . $logo_src . '" alt="ProGreenClean" width="150" height="40" style="height: 40px; width: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">';
}

function pgc_get_email_footer() {
    $phone = get_option('pgc_phone', '0800 123 4567');
    return '</td>
                    </tr>
                    <tr>
                        <td style="background: #1e293b; padding: 32px; text-align: center; color: #94a3b8; font-size: 14px;">
                            <p style="margin: 0 0 8px 0;">ProGreenClean - Professional Eco-Friendly Cleaning</p>
                            <p style="margin: 0;">Phone: ' . $phone . ' | Email: info@progreenclean.co.uk</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
}

/**
 * AJAX: Submit Quote V2 with Styled Email
 */
add_action('wp_ajax_pgc_submit_quote_v2', 'pgc_ajax_submit_quote_v2');
add_action('wp_ajax_nopriv_pgc_submit_quote_v2', 'pgc_ajax_submit_quote_v2');

function pgc_ajax_submit_quote_v2() {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $quote_id = 'PGC-' . date('Y') . '-' . strtoupper(wp_generate_password(6, false));
    $from_email = get_option('pgc_from_email', 'quotes@progreenclean.co.uk');
    $admin_email = get_option('pgc_quote_email', get_option('admin_email'));
    
    $data = [
        'quote_id' => $quote_id,
        'service' => sanitize_text_field($_POST['service'] ?? ''),
        'answers' => json_decode(stripslashes($_POST['answers'] ?? '{}'), true),
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'address_line1' => sanitize_text_field($_POST['address_line1'] ?? ''),
        'address_line2' => sanitize_text_field($_POST['address_line2'] ?? ''),
        'postcode' => sanitize_text_field($_POST['postcode'] ?? ''),
        'heard_from' => sanitize_text_field($_POST['heard_from'] ?? ''),
        'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
        'price' => floatval($_POST['calculated_price'] ?? 0),
    ];
    
    // Customer Email
    $customer_subject = 'Your ProGreenClean Quote - ' . $quote_id;
    $customer_message = pgc_get_email_header();
    $customer_message .= '<h2 style="color: #0891b2; margin-top: 0;">Thank you for your quote request, ' . $data['first_name'] . '!</h2>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">We have received your quote request and our team will be in touch shortly to confirm your booking.</p>';
    $customer_message .= '<div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border: 2px solid rgba(8, 145, 178, 0.1); border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center;">';
    $customer_message .= '<p style="margin: 0 0 8px 0; color: #64748b; font-size: 14px;">Quote Reference</p>';
    $customer_message .= '<p style="margin: 0; color: #0891b2; font-size: 24px; font-weight: 700;">' . $quote_id . '</p>';
    $customer_message .= '<p style="margin: 16px 0 0 0; color: #64748b; font-size: 14px;">Estimated Price</p>';
    $customer_message .= '<p style="margin: 0; color: #10b981; font-size: 32px; font-weight: 800;">£' . number_format($data['price'], 2) . '</p>';
    $customer_message .= '</div>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">Service: <strong>' . ucwords(str_replace('-', ' ', $data['service'])) . '</strong></p>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">If you have any questions, please do not hesitate to contact us.</p>';
    $customer_message .= '<div style="text-align: center; margin-top: 32px;">';
    $customer_message .= '<a href="' . home_url('/contact/') . '" style="display: inline-block; background: linear-gradient(135deg, #0891b2 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600;">Contact Us</a>';
    $customer_message .= '</div>';
    $customer_message .= pgc_get_email_footer();
    
    $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ProGreenClean <' . $from_email . '>'];
    wp_mail($data['email'], $customer_subject, $customer_message, $headers);
    
    // Admin Email
    $admin_subject = 'New Quote Request: ' . $quote_id;
    $admin_message = pgc_get_email_header();
    $admin_message .= '<h2 style="color: #0891b2; margin-top: 0;">New Quote Request</h2>';
    $admin_message .= '<table style="width: 100%; border-collapse: collapse;">';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Quote ID</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600;">' . $quote_id . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Service</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . ucwords(str_replace('-', ' ', $data['service'])) . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Price</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #10b981;">£' . number_format($data['price'], 2) . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Customer</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Email</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['email'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Phone</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['phone'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; color: #64748b;">Address</td><td style="padding: 8px 0;">' . $data['address_line1'] . '<br>' . $data['postcode'] . '</td></tr>';
    $admin_message .= '</table>';
    if (!empty($data['notes'])) {
        $admin_message .= '<h3 style="color: #0891b2; margin-top: 24px;">Additional Notes</h3>';
        $admin_message .= '<p style="color: #475569;">' . nl2br($data['notes']) . '</p>';
    }
    $admin_message .= pgc_get_email_footer();
    
    // Handle image uploads
    $attachments = [];
    if (!empty($_FILES['quote_images'])) {
        $upload_dir = wp_upload_dir();
        $quote_upload_dir = $upload_dir['basedir'] . '/quote-uploads/' . $quote_id;
        
        if (!file_exists($quote_upload_dir)) {
            wp_mkdir_p($quote_upload_dir);
        }
        
        $file_count = count($_FILES['quote_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['quote_images']['error'][$i] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['quote_images']['tmp_name'][$i];
                $name = sanitize_file_name($_FILES['quote_images']['name'][$i]);
                $file_path = $quote_upload_dir . '/' . $name;
                
                // Validate file type
                $file_type = wp_check_filetype($name, ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp']);
                if ($file_type['type'] && move_uploaded_file($tmp_name, $file_path)) {
                    $attachments[] = $file_path;
                }
            }
        }
        
        // Add image reference to admin email
        if (!empty($attachments)) {
            $admin_message = str_replace('</body>', '<p style="color: #0891b2; margin-top: 24px;"><strong>Images Attached:</strong> ' . count($attachments) . ' image(s) uploaded with this quote.</p></body>', $admin_message);
        }
    }
    
    // Send admin email with attachments
    if (!empty($attachments)) {
        wp_mail($admin_email, $admin_subject, $admin_message, $headers, $attachments);
    } else {
        wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    }
    
    wp_send_json_success(['quote_id' => $quote_id]);
}

/**
 * Contact Form Handler
 */
add_action('admin_post_pgc_contact_form', 'pgc_handle_contact_form');
add_action('admin_post_nopriv_pgc_contact_form', 'pgc_handle_contact_form');

function pgc_handle_contact_form() {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'pgc_contact_form')) {
        wp_die('Invalid nonce');
    }
    
    $from_email = get_option('pgc_from_email', 'quotes@progreenclean.co.uk');
    $admin_email = get_option('pgc_contact_email', get_option('admin_email'));
    
    $name = sanitize_text_field($_POST['name'] ?? '');
    $email = sanitize_email($_POST['email'] ?? '');
    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $message = sanitize_textarea_field($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        wp_die('Please fill in all required fields');
    }
    
    // Admin Email
    $admin_subject = 'New Contact Form Submission from ' . $name;
    $admin_message = pgc_get_email_header();
    $admin_message .= '<h2 style="color: #0891b2; margin-top: 0;">New Contact Form Submission</h2>';
    $admin_message .= '<table style="width: 100%; border-collapse: collapse;">';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Name</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600;">' . $name . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Email</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $email . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Phone</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . ($phone ?: 'Not provided') . '</td></tr>';
    $admin_message .= '</table>';
    $admin_message .= '<h3 style="color: #0891b2; margin-top: 24px;">Message</h3>';
    $admin_message .= '<p style="color: #475569; background: #f1f5f9; padding: 16px; border-radius: 8px;">' . nl2br($message) . '</p>';
    $admin_message .= pgc_get_email_footer();
    
    $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ProGreenClean <' . $from_email . '>'];
    wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    
    // Customer Confirmation Email
    $customer_subject = 'Thank you for contacting ProGreenClean';
    $customer_message = pgc_get_email_header();
    $customer_message .= '<h2 style="color: #0891b2; margin-top: 0;">Thank you, ' . $name . '!</h2>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">We have received your message and will get back to you within 24 hours.</p>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">If you need urgent assistance, please call us at ' . get_option('pgc_phone', '0800 123 4567') . '.</p>';
    $customer_message .= '<div style="text-align: center; margin-top: 32px;">';
    $customer_message .= '<a href="' . home_url('/get-a-quote/') . '" style="display: inline-block; background: linear-gradient(135deg, #0891b2 0%, #10b981 100%); color: #ffffff; text-decoration: none; padding: 16px 32px; border-radius: 8px; font-weight: 600;">Get a Quote</a>';
    $customer_message .= '</div>';
    $customer_message .= pgc_get_email_footer();
    
    wp_mail($email, $customer_subject, $customer_message, $headers);
    
    wp_redirect(home_url('/contact/?sent=1'));
    exit;
}

/**
 * Phone Shortcode
 */
add_shortcode('pgc_phone', function() {
    return esc_html(get_option('pgc_phone', '0800 123 4567'));
});

/**
 * Price Shortcode - Display pricing values from admin
 * Usage: [pgc_price key="ow_win_2bed_oneoff"] or [pgc_price key="ow_eot_2bed" prefix="From £" suffix="+"]
 */
add_shortcode('pgc_price', function($atts) {
    $atts = shortcode_atts([
        'key' => '',
        'prefix' => '£',
        'suffix' => '',
        'decimals' => 2,
    ], $atts);
    
    if (empty($atts['key'])) {
        return '';
    }
    
    $price = pgc_get_price($atts['key']);
    
    if ($price <= 0) {
        return '';
    }
    
    return esc_html($atts['prefix'] . number_format($price, intval($atts['decimals'])) . $atts['suffix']);
});

/**
 * Phone Link Shortcode - Clickable phone number
 * Usage: [pgc_phone_link class="optional-css-class"]
 * Displays: The phone number as clickable text with tel: link
 */
add_shortcode('pgc_phone_link', function($atts) {
    $atts = shortcode_atts([
        'class' => '',
    ], $atts);
    
    $phone = get_option('pgc_phone', '0800 123 4567');
    $class = $atts['class'] ? ' class="' . esc_attr($atts['class']) . '"' : '';
    
    return '<a href="tel:' . esc_attr(preg_replace('/[^0-9+]/', '', $phone)) . '"' . $class . '>' . esc_html($phone) . '</a>';
});

/**
 * Email Link Shortcode - Clickable email
 * Usage: [pgc_email_link class="optional-css-class"]
 * Displays: The email address as clickable text with mailto: link
 */
add_shortcode('pgc_email_link', function($atts) {
    $atts = shortcode_atts([
        'class' => '',
    ], $atts);
    
    $email = get_option('pgc_contact_email', 'info@progreenclean.co.uk');
    $class = $atts['class'] ? ' class="' . esc_attr($atts['class']) . '"' : '';
    
    return '<a href="mailto:' . esc_attr($email) . '"' . $class . '>' . esc_html($email) . '</a>';
});

/**
 * Contact Form Shortcode
 * Usage: [pgc_contact_form]
 */
add_shortcode('pgc_contact_form', function() {
    ob_start();
    ?>
    <form class="pgc-contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('pgc_contact_form'); ?>
        <input type="hidden" name="action" value="pgc_contact_form">
        
        <div class="pgc-form-row">
            <div class="pgc-form-field">
                <label for="pgc-name">Name *</label>
                <input type="text" id="pgc-name" name="name" required>
            </div>
            <div class="pgc-form-field">
                <label for="pgc-email">Email *</label>
                <input type="email" id="pgc-email" name="email" required>
            </div>
        </div>
        
        <div class="pgc-form-field">
            <label for="pgc-phone">Phone</label>
            <input type="tel" id="pgc-phone" name="phone">
        </div>
        
        <div class="pgc-form-field">
            <label for="pgc-message">Message *</label>
            <textarea id="pgc-message" name="message" rows="5" required></textarea>
        </div>
        
        <button type="submit" class="pgc-btn pgc-btn-primary">Send Message</button>
    </form>
    <?php
    return ob_get_clean();
});

/**
 * Children Pages Shortcode
 * Usage: [pgc_children parent="Services"]
 * Each child page can have meta fields:
 * - _pgc_price_key: Price key from pricing table (e.g., "ow_eot_2bed")
 * - _pgc_price_prefix: Text before price (e.g., "From ")
 * - _pgc_price_suffix: Text after price (e.g., "+")
 */
add_shortcode('pgc_children', function($atts) {
    $atts = shortcode_atts([
        'parent' => '',
        'orderby' => 'menu_order title',
        'order' => 'ASC',
    ], $atts);
    
    if (empty($atts['parent'])) {
        return '';
    }
    
    // Find parent page
    $parent = get_page_by_path(sanitize_title($atts['parent']));
    if (!$parent) {
        $parent = get_page_by_title($atts['parent']);
    }
    
    if (!$parent) {
        return '';
    }
    
    $children = get_posts([
        'post_type' => 'page',
        'post_parent' => $parent->ID,
        'posts_per_page' => -1,
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    ]);
    
    if (empty($children)) {
        return '';
    }
    
    ob_start();
    ?>
    <div class="pgc-service-grid">
        <?php foreach ($children as $child) : 
            $excerpt = $child->post_excerpt ?: wp_trim_words($child->post_content, 20);
            
            // Get price meta fields from each child page
            $price_key = get_post_meta($child->ID, '_pgc_price_key', true);
            $price_prefix = get_post_meta($child->ID, '_pgc_price_prefix', true);
            $price_suffix = get_post_meta($child->ID, '_pgc_price_suffix', true);
            
            $price_html = '';
            $has_price = false;
            
            // Check if we have a valid price from key
            if ($price_key) {
                $price = pgc_get_price($price_key);
                if ($price > 0) {
                    $has_price = true;
                }
            }
            
            // Build price HTML if we have any price-related content
            if ($has_price || $price_prefix || $price_suffix) {
                $price_html = '<span class="pgc-service-card__price">';
                
                // Prefix - always show if set, styled as prefix
                if ($price_prefix) {
                    $price_html .= '<span class="pgc-service-card__price-prefix">' . esc_html($price_prefix) . '</span>';
                }
                
                // Price value (if key exists and valid)
                if ($has_price) {
                    $price_html .= '<span class="pgc-service-card__price-value">£' . number_format($price, 2) . '</span>';
                }
                
                // Suffix - no space before it, appended directly
                if ($price_suffix) {
                    $price_html .= '<span class="pgc-service-card__price-suffix">' . esc_html($price_suffix) . '</span>';
                }
                
                $price_html .= '</span>';
            }
            
            // Get featured image
            $image_html = '';
            if (has_post_thumbnail($child->ID)) {
                $image_html = get_the_post_thumbnail($child->ID, 'medium', ['class' => 'pgc-service-card__image']);
            }
        ?>
            <article class="pgc-service-card">
                <?php if ($image_html) : ?>
                    <div class="pgc-service-card__image-wrap"><?php echo $image_html; ?></div>
                <?php endif; ?>
                <h3 class="pgc-service-card__title"><?php echo esc_html($child->post_title); ?></h3>
                <p class="pgc-service-card__description"><?php echo esc_html($excerpt); ?></p>
                <div class="pgc-service-card__footer">
                    <?php if ($price_html) : ?>
                        <?php echo $price_html; ?>
                    <?php else: ?>
                        <span></span>
                    <?php endif; ?>
                    <a href="<?php echo get_permalink($child->ID); ?>" class="pgc-service-card__link">Learn More →</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
});

/**
 * Schema Markup
 */
add_action('wp_head', function() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'ProGreenClean',
        'description' => 'Professional eco-friendly cleaning services in Epsom and Surrey',
        'url' => home_url(),
        'telephone' => get_option('pgc_phone', ''),
        'email' => 'info@progreenclean.co.uk',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Epsom',
            'addressRegion' => 'Surrey',
            'addressCountry' => 'GB',
        ],
        'openingHours' => 'Mo-Fr 08:00-18:00, Sa 09:00-14:00',
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
});

/**
 * New Admin Pricing Page V3
 */
function pgc_render_pricing_page_v3() {
    // Save pricing
    if (isset($_POST['pgc_save_pricing']) && check_admin_referer('pgc_pricing_nonce')) {
        global $pgc_pricing_sections;
        foreach ($pgc_pricing_sections as $section_name => $section) {
            // Handle regular fields
            if (isset($section['fields'])) {
                foreach ($section['fields'] as $field) {
                    $key = $field['key'];
                    $type = $field['type'] ?? 'price';
                    
                    if ($type === 'checkbox') {
                        $checked = isset($_POST['prices'][$key]) ? 1 : 0;
                        update_option('pgc_price_' . $key, $checked);
                    } elseif ($type === 'text') {
                        $text_value = sanitize_text_field($_POST['prices'][$key] ?? '');
                        update_option('pgc_price_' . $key, $text_value);
                    } elseif (isset($_POST['prices'][$key])) {
                        update_option('pgc_price_' . $key, floatval($_POST['prices'][$key]));
                    }
                }
            }
            // Handle table rows
            if (isset($section['rows'])) {
                foreach ($section['rows'] as $row) {
                    if (isset($row['keys'])) {
                        foreach ($row['keys'] as $key) {
                            if (isset($_POST['prices'][$key])) {
                                update_option('pgc_price_' . $key, floatval($_POST['prices'][$key]));
                            }
                        }
                    }
                }
            }
        }
        echo '<div class="notice notice-success"><p>Pricing saved successfully!</p></div>';
    }
    
    global $pgc_pricing_sections;
    ?>
    <div class="wrap">
        <h1>ProGreenClean Pricing Management</h1>
        <p style="color: #666; margin-bottom: 20px;">Configure pricing for all services. These prices are used in the quote calculator.</p>
        
        <!-- Shortcode Documentation -->
        <div class="pgc-shortcode-docs" style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; margin-bottom: 30px;">
            <div class="pgc-shortcode-header" style="padding: 15px 20px; background: #e0f2fe; border-radius: 8px 8px 0 0; cursor: pointer;" onclick="jQuery(this).next().toggle();">
                <h3 style="margin: 0; font-size: 16px; color: #0369a1;">📖 Shortcode Documentation (Click to Expand)</h3>
            </div>
            <div class="pgc-shortcode-content" style="padding: 20px; display: none;">
                <p style="margin-bottom: 15px;">Use these shortcodes to display dynamic pricing and content anywhere on your site:</p>
                
                <h4 style="margin-top: 20px; margin-bottom: 10px; color: #334155;">Price Shortcode</h4>
                <code style="background: #f1f5f9; padding: 8px 12px; border-radius: 4px; display: block; margin-bottom: 10px;">[pgc_price key="KEY_HERE" prefix="From £" suffix="+" decimals="2"]</code>
                <ul style="margin-left: 20px; list-style: disc;">
                    <li><strong>key</strong> - The price identifier (shown in brackets next to each price field)</li>
                    <li><strong>prefix</strong> - Text before the price (default: "£")</li>
                    <li><strong>suffix</strong> - Text after the price</li>
                    <li><strong>decimals</strong> - Number of decimal places (default: 2)</li>
                </ul>
                <p><strong>Example:</strong> <code>[pgc_price key="ow_win_2bed_oneoff" prefix="From £"]</code> displays: From £36.75</p>
                
                <h4 style="margin-top: 20px; margin-bottom: 10px; color: #334155;">Contact Shortcodes</h4>
                <ul style="margin-left: 20px; list-style: disc;">
                    <li><code>[pgc_phone_link text="Call Us"]</code> - Clickable phone number link</li>
                    <li><code>[pgc_email_link text="Email Us"]</code> - Clickable email link</li>
                    <li><code>[pgc_contact_form]</code> - Full contact form</li>
                </ul>
                
                <h4 style="margin-top: 20px; margin-bottom: 10px; color: #334155;">Children Pages Shortcode</h4>
                <code style="background: #f1f5f9; padding: 8px 12px; border-radius: 4px; display: block; margin-bottom: 10px;">[pgc_children parent="Services" feature_price="ow_eot_2bed" feature_price_prefix="From £"]</code>
                <ul style="margin-left: 20px; list-style: disc;">
                    <li><strong>parent</strong> - Parent page title or slug</li>
                    <li><strong>feature_price</strong> - Price key to display on tiles (optional)</li>
                    <li><strong>feature_price_prefix</strong> - Text before price (optional)</li>
                    <li><strong>feature_price_suffix</strong> - Text after price (optional)</li>
                </ul>
            </div>
        </div>
        
        <form method="post">
            <?php wp_nonce_field('pgc_pricing_nonce'); ?>
            
            <?php foreach ($pgc_pricing_sections as $section_name => $section) : 
                $is_table = isset($section['type']) && $section['type'] === 'table';
                $is_settings = isset($section['type']) && $section['type'] === 'settings';
            ?>
            <div class="pgc-pricing-section" style="background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; margin-bottom: 20px;">
                <div class="pgc-section-header" style="background: linear-gradient(135deg, #0891b2 0%, #10b981 100%); color: #fff; padding: 15px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;" onclick="jQuery(this).next().toggle(); jQuery(this).find('.dashicons').toggleClass('dashicons-arrow-down dashicons-arrow-up');">
                    <div>
                        <h2 style="margin: 0; font-size: 18px; color: #fff;"><?php echo esc_html($section_name); ?></h2>
                        <p style="margin: 5px 0 0 0; font-size: 13px; opacity: 0.9;"><?php echo esc_html($section['description']); ?></p>
                    </div>
                    <span class="dashicons dashicons-arrow-up" style="color: #fff;"></span>
                </div>
                <div class="pgc-section-content" style="padding: 20px;">
                    
                    <?php if ($is_table && isset($section['headers'])) : ?>
                        <!-- Table Format -->
                        <table class="wp-list-table widefat striped">
                            <thead>
                                <tr>
                                    <?php foreach ($section['headers'] as $header) : ?>
                                        <th><?php echo esc_html($header); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($section['rows'] as $row) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html($row['label']); ?></strong></td>
                                    <?php foreach ($row['keys'] as $key) : 
                                        $price = pgc_get_price($key);
                                    ?>
                                    <td>
                                        £<input type="number" name="prices[<?php echo esc_attr($key); ?>]" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" style="width: 70px;">
                                        <br><small style="color: #999; font-size: 10px;">[<?php echo esc_html($key); ?>]</small>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($is_settings && isset($section['fields'])) : ?>
                        <!-- Settings Format -->
                        <table class="wp-list-table widefat striped">
                            <tbody>
                                <?php foreach ($section['fields'] as $field) : 
                                    $field_type = $field['type'] ?? 'price';
                                    $value = ($field_type === 'text') 
                                        ? pgc_get_text_option($field['key']) 
                                        : pgc_get_price($field['key']);
                                ?>
                                <tr>
                                    <td style="width: 60%;"><?php echo esc_html($field['label']); ?><br><small style="color: #999; font-size: 10px;">[<?php echo esc_html($field['key']); ?>]</small></td>
                                    <td>
                                        <?php if ($field_type === 'checkbox') : ?>
                                            <input type="checkbox" name="prices[<?php echo esc_attr($field['key']); ?>]" value="1" <?php checked($value, 1); ?>>
                                        <?php elseif ($field_type === 'text') : ?>
                                            <input type="text" name="prices[<?php echo esc_attr($field['key']); ?>]" value="<?php echo esc_attr($value); ?>" style="width: 150px;">
                                        <?php elseif ($field_type === 'percent') : ?>
                                            <input type="number" name="prices[<?php echo esc_attr($field['key']); ?>]" value="<?php echo esc_attr($value); ?>" step="1" min="0" style="width: 80px;"> %
                                        <?php else : ?>
                                            £<input type="number" name="prices[<?php echo esc_attr($field['key']); ?>]" value="<?php echo esc_attr($value); ?>" step="0.01" min="0" style="width: 100px;">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif (isset($section['fields'])) : ?>
                        <!-- Standard Field Format -->
                        <table class="wp-list-table widefat striped">
                            <thead>
                                <tr>
                                    <th style="width: 60%;">Service / Option</th>
                                    <th>Price (£)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($section['fields'] as $field) : 
                                    $price = pgc_get_price($field['key']);
                                ?>
                                <tr>
                                    <td><?php echo esc_html($field['label']); ?><br><small style="color: #999; font-size: 10px;">[<?php echo esc_html($field['key']); ?>]</small></td>
                                    <td>£<input type="number" name="prices[<?php echo esc_attr($field['key']); ?>]" value="<?php echo esc_attr($price); ?>" step="0.01" min="0" style="width: 100px;"></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                    
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php submit_button('Save All Pricing', 'primary', 'pgc_save_pricing'); ?>
        </form>
    </div>
    <?php
}

// Replace the old pricing page hook
add_action('admin_menu', function() {
    remove_menu_page('pgc-pricing'); // Remove old if exists
    add_menu_page('PGC Pricing', 'PGC Pricing', 'manage_options', 'pgc-pricing-v3', 'pgc_render_pricing_page_v3', 'dashicons-money-alt', 30);
    add_submenu_page('pgc-pricing-v3', 'PGC Settings', 'Settings', 'manage_options', 'pgc-settings', 'pgc_render_settings_page');
}, 20);

/**
 * AJAX: Calculate Quote V3
 */
add_action('wp_ajax_pgc_calculate_quote_v3', 'pgc_ajax_calculate_quote_v3');
add_action('wp_ajax_nopriv_pgc_calculate_quote_v3', 'pgc_ajax_calculate_quote_v3');

function pgc_ajax_calculate_quote_v3() {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $service = sanitize_text_field($_POST['service'] ?? '');
    $price_keys = $_POST['price_keys'] ?? [];
    $answers = json_decode(stripslashes($_POST['answers'] ?? '{}'), true);
    
    $total = 0;
    $breakdown = [];
    
    // Window Cleaning - Special Pricing Logic
    if ($service === 'window-cleaning') {
        $frequency = $answers['win_frequency']['value'] ?? 'one-off';
        $bedrooms = $answers['win_bedrooms']['value'] ?? '2';
        $internal_external = $answers['win_internal_external']['value'] ?? 'external';
        
        // Build base price key based on bedrooms and frequency
        $base_key = 'ow_win_' . $bedrooms . 'bed_' . str_replace('-', '', $frequency);
        $base_price = pgc_get_price($base_key);
        
        $external_price = 0;
        $internal_price = 0;
        
        // Calculate external price
        if ($internal_external === 'external' || $internal_external === 'both') {
            $external_price = $base_price;
        }
        
        // Calculate internal price (same price as external - no markup)
        if ($internal_external === 'internal' || $internal_external === 'both') {
            $internal_price = $base_price;
        }
        
        $window_subtotal = $external_price + $internal_price;
        
        // Add to breakdown
        if ($internal_external === 'external') {
            $breakdown[] = ['label' => 'External Windows (' . $bedrooms . ' bed, ' . $frequency . ')', 'price' => $external_price];
        } elseif ($internal_external === 'internal') {
            $breakdown[] = ['label' => 'Internal Windows (' . $bedrooms . ' bed, ' . $frequency . ')', 'price' => $internal_price];
        } else {
            $breakdown[] = ['label' => 'External Windows (' . $bedrooms . ' bed, ' . $frequency . ')', 'price' => $external_price];
            $breakdown[] = ['label' => 'Internal Windows (' . $bedrooms . ' bed, ' . $frequency . ')', 'price' => $internal_price];
        }
        
        $total = $window_subtotal;
        
        // Addons by bedroom
        if (isset($answers['win_extension']) && $answers['win_extension']['value'] === 'yes') {
            $ext_key = 'ow_win_ext_' . $bedrooms . 'bed';
            $ext_price = pgc_get_price($ext_key);
            if ($ext_price > 0) {
                $total += $ext_price;
                $breakdown[] = ['label' => 'Extension (' . $bedrooms . ' bed)', 'price' => $ext_price];
            }
        }
        
        if (isset($answers['win_conservatory_check']) && $answers['win_conservatory_check']['value'] === 'yes') {
            $cons_key = 'ow_win_cons_' . $bedrooms . 'bed';
            $cons_price = pgc_get_price($cons_key);
            if ($cons_price > 0) {
                $total += $cons_price;
                $breakdown[] = ['label' => 'Conservatory (' . $bedrooms . ' bed)', 'price' => $cons_price];
            }
        }
        
        // Conservatory Roof - Size based with Internal/External pricing
        if (isset($answers['win_cons_roof_clean']) && $answers['win_cons_roof_clean']['value'] === 'yes' && isset($answers['win_cons_size'])) {
            $cons_size = $answers['win_cons_size']['value']; // small, medium, large
            $roof_external_price = 0;
            $roof_internal_price = 0;
            
            // Get size label for display
            $size_label = ucfirst($cons_size);
            $size_def = pgc_get_text_option('ow_win_cons_roof_' . $cons_size . '_size');
            if ($size_def) {
                $size_label .= ' (' . $size_def . ')';
            }
            
            // Calculate based on internal/external selection
            if ($internal_external === 'external' || $internal_external === 'both') {
                $roof_external_price = pgc_get_price('ow_win_cons_roof_' . $cons_size . '_ext');
            }
            if ($internal_external === 'internal' || $internal_external === 'both') {
                $roof_internal_price = pgc_get_price('ow_win_cons_roof_' . $cons_size . '_int');
            }
            
            if ($roof_external_price > 0) {
                $total += $roof_external_price;
                $breakdown[] = ['label' => 'Conservatory Roof External - ' . $size_label, 'price' => $roof_external_price];
            }
            if ($roof_internal_price > 0) {
                $total += $roof_internal_price;
                $breakdown[] = ['label' => 'Conservatory Roof Internal - ' . $size_label, 'price' => $roof_internal_price];
            }
        }
        
        if (isset($answers['win_skylights_qty'])) {
            $skylight_qty = intval($answers['win_skylights_qty']['value'] ?? 0);
            if ($skylight_qty > 0) {
                $skylight_unit = pgc_get_price('ow_win_skylight_unit');
                $skylight_total = $skylight_qty * $skylight_unit;
                $total += $skylight_total;
                $breakdown[] = ['label' => 'Skylights (' . $skylight_qty . ')', 'price' => $skylight_total];
            }
        }
        
        if (isset($answers['win_velux_qty'])) {
            $velux_qty = intval($answers['win_velux_qty']['value'] ?? 0);
            if ($velux_qty > 0) {
                $velux_unit = pgc_get_price('ow_win_velux_unit');
                $velux_total = $velux_qty * $velux_unit;
                $total += $velux_total;
                $breakdown[] = ['label' => 'Velux Windows (' . $velux_qty . ')', 'price' => $velux_total];
            }
        }
        
        wp_send_json_success([
            'total' => $total,
            'breakdown' => $breakdown,
        ]);
        return;
    }
    
    // Standard calculation for other services
    if (is_array($price_keys)) {
        foreach ($price_keys as $key) {
            $price = pgc_get_price(sanitize_text_field($key));
            if ($price > 0) {
                $total += $price;
                $label = $key;
                foreach ($answers as $step => $answer) {
                    if (isset($answer['priceKey']) && $answer['priceKey'] === $key) {
                        $label = $answer['label'] ?? $key;
                        break;
                    }
                }
                $breakdown[] = ['label' => $label, 'price' => $price];
            }
        }
    }
    
    // Special calculations for multi-unit items
    if ($service === 'end-of-tenancy' && isset($answers['eot_carpets_qty'])) {
        $carpet_qty = intval($answers['eot_carpets_qty']['value'] ?? 0);
        if ($carpet_qty > 0 && is_numeric($answers['eot_carpets_qty']['value'])) {
            $unit_price = pgc_get_price('ow_carpet_unit');
            $carpet_total = $carpet_qty * $unit_price;
            $total += $carpet_total;
            $breakdown[] = ['label' => 'Carpet Cleaning (' . $carpet_qty . ' rooms)', 'price' => $carpet_total];
        }
    }
    
    // Hourly rate calculations
    if ($service === 'domestic-cleaning' && isset($answers['dom_hours'])) {
        $hours = intval($answers['dom_hours']['value'] ?? 2);
        $hourly_rate = pgc_get_price('ow_dom_hourly_rate');
        $domestic_total = $hours * $hourly_rate;
        $total = $domestic_total;
        foreach ($breakdown as $i => $item) {
            if ($item['label'] === '2 Hours' || $item['label'] === '3 Hours' || $item['label'] === '4 Hours' || $item['label'] === '5+ Hours') {
                unset($breakdown[$i]);
            }
        }
        $breakdown = array_values($breakdown);
        $breakdown[] = ['label' => 'Domestic Cleaning (' . $hours . ' hours @ £' . $hourly_rate . '/hr)', 'price' => $domestic_total];
    }
    
    wp_send_json_success([
        'total' => $total,
        'breakdown' => $breakdown,
    ]);
}

/**
 * AJAX: Submit Quote V3
 */
add_action('wp_ajax_pgc_submit_quote_v3', 'pgc_ajax_submit_quote_v3');
add_action('wp_ajax_nopriv_pgc_submit_quote_v3', 'pgc_ajax_submit_quote_v3');

function pgc_ajax_submit_quote_v3() {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $quote_id = 'PGC-' . date('Y') . '-' . strtoupper(wp_generate_password(6, false));
    $from_email = get_option('pgc_from_email', 'quotes@progreenclean.co.uk');
    $admin_email = get_option('pgc_quote_email', get_option('admin_email'));
    
    $quote_data = json_decode(urldecode($_POST['quote_data'] ?? '{}'), true);
    
    $data = [
        'quote_id' => $quote_id,
        'service' => sanitize_text_field($quote_data['service'] ?? ''),
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'address' => sanitize_textarea_field($_POST['address'] ?? ''),
        'postcode' => sanitize_text_field($_POST['postcode'] ?? ''),
        'notes' => sanitize_textarea_field($_POST['notes'] ?? ''),
        'price' => floatval($quote_data['price'] ?? 0),
        'breakdown' => $quote_data['breakdown'] ?? [],
        'answers' => $quote_data['answers'] ?? [],
        'summary' => sanitize_textarea_field($quote_data['summary'] ?? ''),
    ];
    
    // Use pre-formatted summary from JS, or build fallback
    $quote_summary = $data['summary'];
    if (empty($quote_summary)) {
        foreach ($data['answers'] as $step => $answer) {
            if (is_array($answer) && isset($answer['label'])) {
                $quote_summary .= $answer['label'] . "\n";
            }
        }
    }
    
    // Customer Email
    $customer_subject = 'Your ProGreenClean Quote - ' . $quote_id;
    $customer_message = pgc_get_email_header();
    $customer_message .= '<h2 style="color: #0891b2; margin-top: 0;">Thank you for your quote request, ' . $data['first_name'] . '!</h2>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">We have received your quote request and our team will be in touch shortly to confirm your booking.</p>';
    
    if ($data['price'] > 0) {
        $customer_message .= '<div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border: 2px solid rgba(8, 145, 178, 0.1); border-radius: 12px; padding: 24px; margin: 24px 0; text-align: center;">';
        $customer_message .= '<p style="margin: 0 0 8px 0; color: #64748b; font-size: 14px;">Estimated Price</p>';
        $customer_message .= '<p style="margin: 0; color: #10b981; font-size: 32px; font-weight: 800;">£' . number_format($data['price'], 2) . '</p>';
        $customer_message .= '</div>';
    }
    
    // Quote Summary for Customer
    if (!empty($quote_summary)) {
        $customer_message .= '<div style="background: #f8fafc; border-radius: 12px; padding: 20px; margin: 24px 0;">';
        $customer_message .= '<h3 style="color: #0891b2; margin-top: 0; font-size: 16px;">Quote Summary</h3>';
        $customer_message .= '<pre style="margin: 0; font-family: inherit; font-size: 14px; line-height: 1.6; color: #475569; white-space: pre-wrap;">' . nl2br(esc_html($quote_summary)) . '</pre>';
        $customer_message .= '</div>';
    }
    
    $service_display_name = ucwords(str_replace('-', ' ', $data['service']));
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">Service: <strong>' . esc_html($service_display_name) . '</strong></p>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">Quote Reference: <strong>' . $quote_id . '</strong></p>';
    $customer_message .= '<p style="color: #475569; font-size: 16px; line-height: 1.6;">If you have any questions, please do not hesitate to contact us.</p>';
    $customer_message .= pgc_get_email_footer();
    
    $headers = ['Content-Type: text/html; charset=UTF-8', 'From: ProGreenClean <' . $from_email . '>'];
    wp_mail($data['email'], $customer_subject, $customer_message, $headers);
    
    // Admin Email
    $admin_subject = 'New Quote Request: ' . $quote_id;
    $admin_message = pgc_get_email_header();
    $admin_message .= '<h2 style="color: #0891b2; margin-top: 0;">New Quote Request</h2>';
    $admin_message .= '<table style="width: 100%; border-collapse: collapse;">';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Quote ID</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600;">' . $quote_id . '</td></tr>';
    $service_display_name = ucwords(str_replace('-', ' ', $data['service']));
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Service</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . esc_html($service_display_name) . '</td></tr>';
    if ($data['price'] > 0) {
        $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Price</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; font-weight: 600; color: #10b981;">£' . number_format($data['price'], 2) . '</td></tr>';
    }
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Customer</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['first_name'] . ' ' . $data['last_name'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Email</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['email'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Phone</td><td style="padding: 8px 0; border-bottom: 1px solid #e2e8f0;">' . $data['phone'] . '</td></tr>';
    $admin_message .= '<tr><td style="padding: 8px 0; color: #64748b;">Address</td><td style="padding: 8px 0;">' . nl2br($data['address']) . '<br>' . $data['postcode'] . '</td></tr>';
    $admin_message .= '</table>';
    
    // Merged Quote Details with Price Breakdown
    if (!empty($quote_summary) || !empty($data['breakdown'])) {
        $admin_message .= '<h3 style="color: #0891b2; margin-top: 24px;">Quote Summary & Price Breakdown</h3>';
        
        // Show quote summary
        if (!empty($quote_summary)) {
            $admin_message .= '<pre style="background: #f1f5f9; padding: 16px; border-radius: 8px; font-family: inherit; line-height: 1.6; margin-bottom: 20px;">' . nl2br(esc_html($quote_summary)) . '</pre>';
        }
        
        // Show detailed price breakdown
        if (!empty($data['breakdown'])) {
            $admin_message .= '<table style="width: 100%; border-collapse: collapse;">';
            $admin_message .= '<thead><tr style="background: #f1f5f9;"><th style="padding: 10px; text-align: left; border-bottom: 2px solid #cbd5e1;">Item</th><th style="padding: 10px; text-align: right; border-bottom: 2px solid #cbd5e1;">Price</th></tr></thead>';
            $admin_message .= '<tbody>';
            foreach ($data['breakdown'] as $item) {
                $admin_message .= '<tr>';
                $admin_message .= '<td style="padding: 10px; border-bottom: 1px solid #e2e8f0;">' . esc_html($item['label']) . '</td>';
                $admin_message .= '<td style="padding: 10px; border-bottom: 1px solid #e2e8f0; text-align: right;">£' . number_format($item['price'], 2) . '</td>';
                $admin_message .= '</tr>';
            }
            $admin_message .= '<tr style="background: #f8fafc; font-weight: 600;">';
            $admin_message .= '<td style="padding: 10px;">Total</td>';
            $admin_message .= '<td style="padding: 10px; text-align: right; color: #10b981;">£' . number_format($data['price'], 2) . '</td>';
            $admin_message .= '</tr>';
            $admin_message .= '</tbody></table>';
        }
    }
    
    if (!empty($data['notes'])) {
        $admin_message .= '<h3 style="color: #0891b2; margin-top: 24px;">Additional Notes</h3>';
        $admin_message .= '<p style="color: #475569;">' . nl2br($data['notes']) . '</p>';
    }
    
    $admin_message .= pgc_get_email_footer();
    
    wp_mail($admin_email, $admin_subject, $admin_message, $headers);
    
    wp_send_json_success(['quote_id' => $quote_id]);
}
