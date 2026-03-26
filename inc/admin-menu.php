<?php
/**
 * Admin Menu for Pricing Management
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu
 */
add_action('admin_menu', function (): void {
    add_menu_page(
        __('ProGreenClean', 'progreenclean'),
        __('ProGreenClean', 'progreenclean'),
        'manage_options',
        'progreenclean',
        'pgc_admin_dashboard',
        'dashicons-admin-tools',
        6
    );
    
    add_submenu_page(
        'progreenclean',
        __('Pricing Manager', 'progreenclean'),
        __('Pricing', 'progreenclean'),
        'manage_options',
        'progreenclean-pricing',
        'pgc_admin_pricing'
    );
    
    add_submenu_page(
        'progreenclean',
        __('Quote Management', 'progreenclean'),
        __('Quotes', 'progreenclean'),
        'manage_options',
        'progreenclean-quotes',
        'pgc_admin_quotes'
    );
    
    add_submenu_page(
        'progreenclean',
        __('Settings', 'progreenclean'),
        __('Settings', 'progreenclean'),
        'manage_options',
        'progreenclean-settings',
        'pgc_admin_settings'
    );
});

/**
 * Admin Dashboard
 */
function pgc_admin_dashboard(): void {
    ?>
    <div class="wrap">
        <h1><?php _e('ProGreenClean Dashboard', 'progreenclean'); ?></h1>
        <div class="pgc-dashboard-widgets">
            <div class="pgc-widget">
                <h2><?php _e('Quick Stats', 'progreenclean'); ?></h2>
                <?php
                global $wpdb;
                $quote_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pgc_quotes");
                $pending_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}pgc_quotes WHERE status = 'pending'");
                ?>
                <p><strong><?php _e('Total Quotes:', 'progreenclean'); ?></strong> <?php echo esc_html($quote_count); ?></p>
                <p><strong><?php _e('Pending Quotes:', 'progreenclean'); ?></strong> <?php echo esc_html($pending_count); ?></p>
            </div>
            <div class="pgc-widget">
                <h2><?php _e('Quick Links', 'progreenclean'); ?></h2>
                <ul>
                    <li><a href="<?php echo admin_url('admin.php?page=progreenclean-pricing'); ?>"><?php _e('Manage Pricing', 'progreenclean'); ?></a></li>
                    <li><a href="<?php echo admin_url('admin.php?page=progreenclean-quotes'); ?>"><?php _e('View Quotes', 'progreenclean'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit.php?post_type=pgc_service'); ?>"><?php _e('Manage Services', 'progreenclean'); ?></a></li>
                    <li><a href="<?php echo admin_url('edit.php?post_type=pgc_faq'); ?>"><?php _e('Manage FAQs', 'progreenclean'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Pricing Management
 */
function pgc_admin_pricing(): void {
    global $wpdb;
    
    // Handle form submission
    if (isset($_POST['pgc_save_pricing']) && wp_verify_nonce($_POST['pgc_pricing_nonce'], 'pgc_pricing_action')) {
        $service_slug = sanitize_text_field($_POST['service_slug']);
        $option_key = sanitize_text_field($_POST['option_key']);
        $option_value = sanitize_text_field($_POST['option_value']);
        $price = floatval($_POST['price']);
        $price_type = sanitize_text_field($_POST['price_type']);
        
        $wpdb->replace(
            $wpdb->prefix . 'pgc_pricing',
            [
                'service_slug' => $service_slug,
                'option_key' => $option_key,
                'option_value' => $option_value,
                'price' => $price,
                'price_type' => $price_type,
            ],
            ['%s', '%s', '%s', '%f', '%s']
        );
        
        echo '<div class="notice notice-success"><p>' . esc_html__('Pricing saved successfully!', 'progreenclean') . '</p></div>';
    }
    
    // Handle contact settings save
    if (isset($_POST['pgc_save_contact_settings']) && check_admin_referer('pgc_contact_settings_nonce')) {
        update_option('pgc_contact_email', sanitize_email($_POST['pgc_contact_email'] ?? ''));
        update_option('pgc_opening_hours', sanitize_textarea_field($_POST['pgc_opening_hours'] ?? ''));
        echo '<div class="notice notice-success"><p>' . esc_html__('Contact settings saved!', 'progreenclean') . '</p></div>';
    }
    
    // Handle delete
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $wpdb->delete($wpdb->prefix . 'pgc_pricing', ['id' => $id], ['%d']);
        echo '<div class="notice notice-success"><p>' . esc_html__('Pricing deleted!', 'progreenclean') . '</p></div>';
    }
    
    // Get all pricing data grouped by service
    $pricing = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pgc_pricing ORDER BY service_slug, option_key", ARRAY_A);
    $grouped = [];
    foreach ($pricing as $item) {
        $grouped[$item['service_slug']][] = $item;
    }
    
    $services = [
        'window-cleaning' => 'Window Cleaning',
        'gutter-cleaning' => 'Gutter Cleaning',
        'domestic-cleaning' => 'Domestic Cleaning',
        'end-of-tenancy' => 'End of Tenancy',
        'post-construction' => 'Post Construction',
        'oven-cleaning' => 'Oven Cleaning',
        'carpet-cleaning' => 'Carpet Cleaning',
        'conservatory-cleaning' => 'Conservatory Cleaning',
        'pressure-washing' => 'Pressure Washing',
        'solar-panel-cleaning' => 'Solar Panel Cleaning',
        'addons' => 'Add-ons',
    ];
    ?>
    <div class="wrap">
        <h1><?php _e('Pricing Manager', 'progreenclean'); ?></h1>
        
        <h2><?php _e('Add/Edit Pricing', 'progreenclean'); ?></h2>
        <form method="post" class="pgc-pricing-form">
            <?php wp_nonce_field('pgc_pricing_action', 'pgc_pricing_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="service_slug"><?php _e('Service', 'progreenclean'); ?></label></th>
                    <td>
                        <select name="service_slug" id="service_slug" required>
                            <?php foreach ($services as $slug => $name) : ?>
                                <option value="<?php echo esc_attr($slug); ?>"><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="option_key"><?php _e('Option Key', 'progreenclean'); ?></label></th>
                    <td>
                        <input type="text" name="option_key" id="option_key" class="regular-text" required>
                        <p class="description"><?php _e('Unique identifier (e.g., 2bed_4weeks, hourly_rate)', 'progreenclean'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="option_value"><?php _e('Option Label', 'progreenclean'); ?></label></th>
                    <td>
                        <input type="text" name="option_value" id="option_value" class="regular-text" required>
                        <p class="description"><?php _e('Human-readable label (e.g., "2 Bedroom - 4 Weeks")', 'progreenclean'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="price"><?php _e('Price (£)', 'progreenclean'); ?></label></th>
                    <td>
                        <input type="number" name="price" id="price" step="0.01" min="0" required>
                    </td>
                </tr>
                <tr>
                    <th><label for="price_type"><?php _e('Price Type', 'progreenclean'); ?></label></th>
                    <td>
                        <select name="price_type" id="price_type">
                            <option value="fixed">Fixed</option>
                            <option value="per_hour">Per Hour</option>
                            <option value="per_unit">Per Unit</option>
                            <option value="per_sqm">Per Square Meter</option>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="pgc_save_pricing" class="button button-primary" value="<?php _e('Save Pricing', 'progreenclean'); ?>">
            </p>
        </form>
        
        <h2><?php _e('Current Pricing', 'progreenclean'); ?></h2>
        <?php foreach ($grouped as $service_slug => $items) : ?>
            <h3><?php echo esc_html($services[$service_slug] ?? $service_slug); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Label', 'progreenclean'); ?></th>
                        <th><?php _e('Key', 'progreenclean'); ?></th>
                        <th><?php _e('Price', 'progreenclean'); ?></th>
                        <th><?php _e('Type', 'progreenclean'); ?></th>
                        <th><?php _e('Actions', 'progreenclean'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) : ?>
                        <tr>
                            <td><?php echo esc_html($item['option_value']); ?></td>
                            <td><code><?php echo esc_html($item['option_key']); ?></code></td>
                            <td>£<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo esc_html($item['price_type']); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=progreenclean-pricing&action=delete&id=' . $item['id']), 'delete-pricing'); ?>" 
                                   class="button button-small" 
                                   onclick="return confirm('<?php _e('Are you sure?', 'progreenclean'); ?>')">
                                    <?php _e('Delete', 'progreenclean'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
        
        <hr style="margin: 40px 0;">
        
        <h2><?php _e('Contact Settings', 'progreenclean'); ?></h2>
        <form method="post">
            <?php wp_nonce_field('pgc_contact_settings_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="pgc_contact_email"><?php _e('Contact Email', 'progreenclean'); ?></label></th>
                    <td>
                        <input type="email" name="pgc_contact_email" id="pgc_contact_email" value="<?php echo esc_attr(get_option('pgc_contact_email', '')); ?>" class="regular-text">
                        <p class="description"><?php _e('Email address used for quote notifications and contact forms', 'progreenclean'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="pgc_opening_hours"><?php _e('Opening Hours', 'progreenclean'); ?></label></th>
                    <td>
                        <textarea name="pgc_opening_hours" id="pgc_opening_hours" rows="4" class="regular-text" style="font-family: monospace;"><?php echo esc_textarea(get_option('pgc_opening_hours', "Mon-Fri: 8am-6pm\nSat: 9am-2pm\nSun: Closed")); ?></textarea>
                        <p class="description"><?php _e('Displayed in footer, contact page, and emails', 'progreenclean'); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="pgc_save_contact_settings" class="button button-primary" value="<?php _e('Save Contact Settings', 'progreenclean'); ?>">
            </p>
        </form>
    </div>
    <?php
}

/**
 * Quote Management
 */
function pgc_admin_quotes(): void {
    global $wpdb;
    
    // Handle status update
    if (isset($_POST['pgc_update_quote']) && isset($_POST['quote_id'])) {
        $quote_id = sanitize_text_field($_POST['quote_id']);
        $status = sanitize_text_field($_POST['status']);
        $final_price = floatval($_POST['final_price']);
        $notes = sanitize_textarea_field($_POST['notes']);
        
        $wpdb->update(
            $wpdb->prefix . 'pgc_quotes',
            [
                'status' => $status,
                'final_price' => $final_price,
                'notes' => $notes,
            ],
            ['quote_id' => $quote_id],
            ['%s', '%f', '%s'],
            ['%s']
        );
        
        echo '<div class="notice notice-success"><p>' . esc_html__('Quote updated!', 'progreenclean') . '</p></div>';
    }
    
    // Get quotes
    $quotes = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pgc_quotes ORDER BY created_at DESC", ARRAY_A);
    ?>
    <div class="wrap">
        <h1><?php _e('Quote Management', 'progreenclean'); ?></h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Quote ID', 'progreenclean'); ?></th>
                    <th><?php _e('Date', 'progreenclean'); ?></th>
                    <th><?php _e('Service', 'progreenclean'); ?></th>
                    <th><?php _e('Customer', 'progreenclean'); ?></th>
                    <th><?php _e('Price', 'progreenclean'); ?></th>
                    <th><?php _e('Status', 'progreenclean'); ?></th>
                    <th><?php _e('Actions', 'progreenclean'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote) : 
                    $quote_data = json_decode($quote['quote_data'], true);
                ?>
                    <tr>
                        <td><code><?php echo esc_html($quote['quote_id']); ?></code></td>
                        <td><?php echo esc_html(date('d M Y', strtotime($quote['created_at']))); ?></td>
                        <td><?php echo esc_html($quote['service_type']); ?></td>
                        <td>
                            <?php echo esc_html($quote['customer_name']); ?><br>
                            <small><?php echo esc_html($quote['customer_email']); ?></small>
                        </td>
                        <td>£<?php echo number_format($quote['calculated_price'], 2); ?></td>
                        <td>
                            <span class="pgc-status pgc-status-<?php echo esc_attr($quote['status']); ?>">
                                <?php echo esc_html(ucfirst($quote['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <button type="button" class="button" onclick="document.getElementById('quote-<?php echo $quote['id']; ?>').style.display='block'">
                                <?php _e('View/Edit', 'progreenclean'); ?>
                            </button>
                        </td>
                    </tr>
                    <tr id="quote-<?php echo $quote['id']; ?>" style="display:none;">
                        <td colspan="7">
                            <form method="post" class="pgc-quote-form">
                                <input type="hidden" name="quote_id" value="<?php echo esc_attr($quote['quote_id']); ?>">
                                <h3><?php _e('Quote Details', 'progreenclean'); ?></h3>
                                <p><strong><?php _e('Address:', 'progreenclean'); ?></strong> <?php echo esc_html($quote['customer_address']); ?></p>
                                <p><strong><?php _e('Postcode:', 'progreenclean'); ?></strong> <?php echo esc_html($quote['postcode']); ?></p>
                                <p><strong><?php _e('Quote Data:', 'progreenclean'); ?></strong></p>
                                <pre style="background:#f5f5f5;padding:10px;max-height:200px;overflow:auto;"><?php print_r($quote_data); ?></pre>
                                
                                <table class="form-table">
                                    <tr>
                                        <th><label><?php _e('Status', 'progreenclean'); ?></label></th>
                                        <td>
                                            <select name="status">
                                                <option value="pending" <?php selected($quote['status'], 'pending'); ?>><?php _e('Pending', 'progreenclean'); ?></option>
                                                <option value="contacted" <?php selected($quote['status'], 'contacted'); ?>><?php _e('Contacted', 'progreenclean'); ?></option>
                                                <option value="confirmed" <?php selected($quote['status'], 'confirmed'); ?>><?php _e('Confirmed', 'progreenclean'); ?></option>
                                                <option value="completed" <?php selected($quote['status'], 'completed'); ?>><?php _e('Completed', 'progreenclean'); ?></option>
                                                <option value="cancelled" <?php selected($quote['status'], 'cancelled'); ?>><?php _e('Cancelled', 'progreenclean'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><label><?php _e('Final Price (£)', 'progreenclean'); ?></label></th>
                                        <td><input type="number" name="final_price" step="0.01" value="<?php echo esc_attr($quote['final_price']); ?>"></td>
                                    </tr>
                                    <tr>
                                        <th><label><?php _e('Notes', 'progreenclean'); ?></label></th>
                                        <td><textarea name="notes" rows="3" cols="50"><?php echo esc_textarea($quote['notes']); ?></textarea></td>
                                    </tr>
                                </table>
                                <p>
                                    <input type="submit" name="pgc_update_quote" class="button button-primary" value="<?php _e('Update Quote', 'progreenclean'); ?>">
                                    <button type="button" class="button" onclick="document.getElementById('quote-<?php echo $quote['id']; ?>').style.display='none'"><?php _e('Close', 'progreenclean'); ?></button>
                                </p>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Settings Page
 */
function pgc_admin_settings(): void {
    ?>
    <div class="wrap">
        <h1><?php _e('ProGreenClean Settings', 'progreenclean'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('pgc_settings');
            do_settings_sections('progreenclean-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
add_action('admin_init', function (): void {
    register_setting('pgc_settings', 'pgc_business_name');
    register_setting('pgc_settings', 'pgc_phone');
    register_setting('pgc_settings', 'pgc_email');
    register_setting('pgc_settings', 'pgc_contact_email');
    register_setting('pgc_settings', 'pgc_address');
    register_setting('pgc_settings', 'pgc_postcode');
    register_setting('pgc_settings', 'pgc_opening_hours');
    register_setting('pgc_settings', 'pgc_google_reviews_url');
    
    add_settings_section('pgc_general', __('General Settings', 'progreenclean'), function (): void {
        echo '<p>' . __('Configure your business details.', 'progreenclean') . '</p>';
    }, 'progreenclean-settings');
    
    // Regular text fields
    $fields = [
        'pgc_business_name' => __('Business Name', 'progreenclean'),
        'pgc_phone' => __('Phone Number', 'progreenclean'),
        'pgc_email' => __('Email Address', 'progreenclean'),
        'pgc_contact_email' => __('Contact Email (for quotes/forms)', 'progreenclean'),
        'pgc_address' => __('Street Address', 'progreenclean'),
        'pgc_postcode' => __('Postcode', 'progreenclean'),
        'pgc_google_reviews_url' => __('Google Reviews URL', 'progreenclean'),
    ];
    
    foreach ($fields as $field => $label) {
        add_settings_field($field, $label, function ($args) use ($field): void {
            $value = get_option($field);
            printf('<input type="text" name="%s" value="%s" class="regular-text">', esc_attr($field), esc_attr($value));
        }, 'progreenclean-settings', 'pgc_general', ['field' => $field]);
    }
    
    // Opening hours textarea field
    add_settings_field('pgc_opening_hours', __('Opening Hours', 'progreenclean'), function (): void {
        $value = get_option('pgc_opening_hours', "Mon-Fri: 8am-6pm\nSat: 9am-2pm\nSun: Closed");
        printf('<textarea name="pgc_opening_hours" rows="4" class="regular-text" style="font-family: monospace;">%s</textarea>', esc_textarea($value));
        echo '<p class="description">Enter each day/time on a new line. Use HTML &lt;br&gt; for line breaks in display.</p>';
    }, 'progreenclean-settings', 'pgc_general');
});
