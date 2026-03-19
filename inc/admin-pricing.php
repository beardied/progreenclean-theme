<?php
/**
 * Admin Pricing Management Interface
 *
 * @package ProGreenClean
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Admin Menu
 */
add_action('admin_menu', function(): void {
    add_menu_page(
        __('ProGreenClean Pricing', 'progreenclean'),
        __('PGC Pricing', 'progreenclean'),
        'manage_options',
        'pgc-pricing',
        'pgc_render_pricing_page',
        'dashicons-money-alt',
        30
    );
    
    add_submenu_page(
        'pgc-pricing',
        __('All Pricing', 'progreenclean'),
        __('All Pricing', 'progreenclean'),
        'manage_options',
        'pgc-pricing',
        'pgc_render_pricing_page'
    );
    
    add_submenu_page(
        'pgc-pricing',
        __('Add New Price', 'progreenclean'),
        __('Add New', 'progreenclean'),
        'manage_options',
        'pgc-pricing-add',
        'pgc_render_pricing_add_page'
    );
    
    add_submenu_page(
        'pgc-pricing',
        __('Quote Submissions', 'progreenclean'),
        __('Quotes', 'progreenclean'),
        'manage_options',
        'pgc-quotes',
        'pgc_render_quotes_page'
    );
});

/**
 * Enqueue Admin Assets
 */
add_action('admin_enqueue_scripts', function(string $hook): void {
    if (strpos($hook, 'pgc-') === false) {
        return;
    }
    
    wp_enqueue_style(
        'pgc-admin-css',
        PGC_URL . '/assets/css/admin.css',
        [],
        PGC_VERSION
    );
    
    wp_enqueue_script(
        'pgc-admin-js',
        PGC_URL . '/assets/js/admin.js',
        ['jquery'],
        PGC_VERSION,
        true
    );
});

/**
 * Render Pricing Page
 */
function pgc_render_pricing_page(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    // Handle actions
    if (isset($_POST['pgc_action']) && check_admin_referer('pgc_pricing_nonce')) {
        if ($_POST['pgc_action'] === 'update' && isset($_POST['item_id'])) {
            pgc_update_pricing(intval($_POST['item_id']), [
                'item_value' => sanitize_text_field($_POST['item_value']),
                'price' => floatval($_POST['price']),
                'price_type' => sanitize_text_field($_POST['price_type']),
                'frequency_4wk' => $_POST['frequency_4wk'] ? floatval($_POST['frequency_4wk']) : null,
                'frequency_8wk' => $_POST['frequency_8wk'] ? floatval($_POST['frequency_8wk']) : null,
                'frequency_12wk' => $_POST['frequency_12wk'] ? floatval($_POST['frequency_12wk']) : null,
                'addon_price' => $_POST['addon_price'] ? floatval($_POST['addon_price']) : null,
                'active' => isset($_POST['active']) ? 1 : 0,
            ]);
            echo '<div class="notice notice-success"><p>Pricing updated successfully.</p></div>';
        }
        
        if ($_POST['pgc_action'] === 'delete' && isset($_POST['item_id'])) {
            pgc_delete_pricing(intval($_POST['item_id']));
            echo '<div class="notice notice-success"><p>Pricing item deleted.</p></div>';
        }
    }
    
    // Get filter values
    $service_filter = isset($_GET['service']) ? sanitize_text_field($_GET['service']) : '';
    $category_filter = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
    
    // Build query
    $sql = "SELECT * FROM $table WHERE 1=1";
    $params = [];
    
    if ($service_filter) {
        $sql .= " AND service_slug = %s";
        $params[] = $service_filter;
    }
    if ($category_filter) {
        $sql .= " AND category = %s";
        $params[] = $category_filter;
    }
    
    $sql .= " ORDER BY service_slug, category, id";
    
    $pricing_items = $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
    $services = pgc_get_service_slugs();
    ?>
    <div class="wrap">
        <h1><?php _e('ProGreenClean Pricing Management', 'progreenclean'); ?></h1>
        
        <form method="get" class="pgc-filter-form">
            <input type="hidden" name="page" value="pgc-pricing">
            <select name="service">
                <option value=""><?php _e('All Services', 'progreenclean'); ?></option>
                <?php foreach ($services as $service): ?>
                    <option value="<?php echo esc_attr($service); ?>" <?php selected($service_filter, $service); ?>>
                        <?php echo esc_html(str_replace('-', ' ', ucwords($service))); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="category">
                <option value=""><?php _e('All Categories', 'progreenclean'); ?></option>
                <option value="residential" <?php selected($category_filter, 'residential'); ?>>Residential</option>
                <option value="addon" <?php selected($category_filter, 'addon'); ?>>Add-ons</option>
                <option value="specialist" <?php selected($category_filter, 'specialist'); ?>>Specialist</option>
                <option value="hourly" <?php selected($category_filter, 'hourly'); ?>>Hourly</option>
            </select>
            <?php submit_button(__('Filter', 'progreenclean'), 'secondary', '', false); ?>
            <a href="<?php echo admin_url('admin.php?page=pgc-pricing'); ?>" class="button">
                <?php _e('Reset', 'progreenclean'); ?>
            </a>
        </form>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Service', 'progreenclean'); ?></th>
                    <th><?php _e('Category', 'progreenclean'); ?></th>
                    <th><?php _e('Item', 'progreenclean'); ?></th>
                    <th><?php _e('Price', 'progreenclean'); ?></th>
                    <th><?php _e('Type', 'progreenclean'); ?></th>
                    <th><?php _e('4wk', 'progreenclean'); ?></th>
                    <th><?php _e('8wk', 'progreenclean'); ?></th>
                    <th><?php _e('12wk', 'progreenclean'); ?></th>
                    <th><?php _e('Status', 'progreenclean'); ?></th>
                    <th><?php _e('Actions', 'progreenclean'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pricing_items as $item): ?>
                    <tr>
                        <form method="post">
                            <?php wp_nonce_field('pgc_pricing_nonce'); ?>
                            <input type="hidden" name="pgc_action" value="update">
                            <input type="hidden" name="item_id" value="<?php echo esc_attr($item['id']); ?>">
                            
                            <td><?php echo esc_html(str_replace('-', ' ', ucwords($item['service_slug']))); ?></td>
                            <td><?php echo esc_html(ucfirst($item['category'])); ?></td>
                            <td><input type="text" name="item_value" value="<?php echo esc_attr($item['item_value']); ?>" class="regular-text"></td>
                            <td><input type="number" name="price" value="<?php echo esc_attr($item['price']); ?>" step="0.01" style="width:80px;"></td>
                            <td>
                                <select name="price_type">
                                    <option value="fixed" <?php selected($item['price_type'], 'fixed'); ?>>Fixed</option>
                                    <option value="per_hour" <?php selected($item['price_type'], 'per_hour'); ?>>Per Hour</option>
                                    <option value="per_unit" <?php selected($item['price_type'], 'per_unit'); ?>>Per Unit</option>
                                    <option value="per_sqm" <?php selected($item['price_type'], 'per_sqm'); ?>>Per Sqm</option>
                                </select>
                            </td>
                            <td><input type="number" name="frequency_4wk" value="<?php echo esc_attr($item['frequency_4wk'] ?? ''); ?>" step="0.01" style="width:60px;" placeholder="-"></td>
                            <td><input type="number" name="frequency_8wk" value="<?php echo esc_attr($item['frequency_8wk'] ?? ''); ?>" step="0.01" style="width:60px;" placeholder="-"></td>
                            <td><input type="number" name="frequency_12wk" value="<?php echo esc_attr($item['frequency_12wk'] ?? ''); ?>" step="0.01" style="width:60px;" placeholder="-"></td>
                            <td>
                                <label>
                                    <input type="checkbox" name="active" <?php checked($item['active'], 1); ?>>
                                    <?php echo $item['active'] ? __('Active', 'progreenclean') : __('Inactive', 'progreenclean'); ?>
                                </label>
                            </td>
                            <td>
                                <?php submit_button(__('Save', 'progreenclean'), 'small', '', false); ?>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}

/**
 * Render Add Pricing Page
 */
function pgc_render_pricing_add_page(): void {
    $message = '';
    
    if (isset($_POST['pgc_add_pricing']) && check_admin_referer('pgc_add_pricing_nonce')) {
        pgc_add_pricing([
            'service_slug' => sanitize_text_field($_POST['service_slug']),
            'category' => sanitize_text_field($_POST['category']),
            'item_key' => sanitize_title($_POST['item_value']),
            'item_value' => sanitize_text_field($_POST['item_value']),
            'price' => floatval($_POST['price']),
            'price_type' => sanitize_text_field($_POST['price_type']),
            'description' => sanitize_textarea_field($_POST['description']),
        ]);
        $message = '<div class="notice notice-success"><p>Pricing item added successfully.</p></div>';
    }
    
    $services = [
        'window-cleaning', 'gutter-cleaning', 'solar-panel-cleaning', 'pressure-washing',
        'domestic-cleaning', 'end-of-tenancy', 'post-construction', 'oven-cleaning',
        'carpet-cleaning', 'conservatory-cleaning', 'commercial-cleaning', 'graffiti-removal'
    ];
    ?>
    <div class="wrap">
        <h1><?php _e('Add New Pricing', 'progreenclean'); ?></h1>
        
        <?php echo $message; ?>
        
        <form method="post" class="pgc-form">
            <?php wp_nonce_field('pgc_add_pricing_nonce'); ?>
            <input type="hidden" name="pgc_add_pricing" value="1">
            
            <table class="form-table">
                <tr>
                    <th><label for="service_slug"><?php _e('Service', 'progreenclean'); ?></label></th>
                    <td>
                        <select name="service_slug" id="service_slug" required>
                            <?php foreach ($services as $service): ?>
                                <option value="<?php echo esc_attr($service); ?>">
                                    <?php echo esc_html(str_replace('-', ' ', ucwords($service))); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="category"><?php _e('Category', 'progreenclean'); ?></label></th>
                    <td>
                        <select name="category" id="category" required>
                            <option value="residential">Residential</option>
                            <option value="addon">Add-on</option>
                            <option value="specialist">Specialist</option>
                            <option value="hourly">Hourly</option>
                            <option value="commercial">Commercial</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="item_value"><?php _e('Item Name', 'progreenclean'); ?></label></th>
                    <td><input type="text" name="item_value" id="item_value" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="price"><?php _e('Price (£)', 'progreenclean'); ?></label></th>
                    <td><input type="number" name="price" id="price" step="0.01" required></td>
                </tr>
                <tr>
                    <th><label for="price_type"><?php _e('Price Type', 'progreenclean'); ?></label></th>
                    <td>
                        <select name="price_type" id="price_type">
                            <option value="fixed">Fixed</option>
                            <option value="per_hour">Per Hour</option>
                            <option value="per_unit">Per Unit</option>
                            <option value="per_sqm">Per Square Metre</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="description"><?php _e('Description', 'progreenclean'); ?></label></th>
                    <td><textarea name="description" id="description" class="large-text" rows="3"></textarea></td>
                </tr>
            </table>
            
            <?php submit_button(__('Add Pricing', 'progreenclean'), 'primary'); ?>
        </form>
    </div>
    <?php
}

/**
 * Render Quotes Page
 */
function pgc_render_quotes_page(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_quotes';
    
    $quotes = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 100", ARRAY_A);
    ?>
    <div class="wrap">
        <h1><?php _e('Quote Submissions', 'progreenclean'); ?></h1>
        
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('Quote ID', 'progreenclean'); ?></th>
                    <th><?php _e('Service', 'progreenclean'); ?></th>
                    <th><?php _e('Customer', 'progreenclean'); ?></th>
                    <th><?php _e('Price', 'progreenclean'); ?></th>
                    <th><?php _e('Status', 'progreenclean'); ?></th>
                    <th><?php _e('Date', 'progreenclean'); ?></th>
                    <th><?php _e('Actions', 'progreenclean'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote): 
                    $quote_data = json_decode($quote['quote_data'], true);
                ?>
                    <tr>
                        <td><code><?php echo esc_html($quote['quote_id']); ?></code></td>
                        <td><?php echo esc_html(str_replace('-', ' ', ucwords($quote['service_slug']))); ?></td>
                        <td>
                            <?php echo esc_html($quote['customer_name']); ?><br>
                            <small><?php echo esc_html($quote['customer_email']); ?></small>
                        </td>
                        <td>£<?php echo number_format(floatval($quote['calculated_price']), 2); ?></td>
                        <td>
                            <span class="pgc-status pgc-status-<?php echo esc_attr($quote['status']); ?>">
                                <?php echo esc_html(ucfirst($quote['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html(date('d/m/Y H:i', strtotime($quote['created_at']))); ?></td>
                        <td>
                            <a href="mailto:<?php echo esc_attr($quote['customer_email']); ?>" class="button button-small">
                                <?php _e('Email', 'progreenclean'); ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
