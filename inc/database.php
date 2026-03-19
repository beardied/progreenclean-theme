<?php
/**
 * Database Functions for Pricing System
 *
 * @package ProGreenClean
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create Pricing Tables
 */
function pgc_create_pricing_tables(): void {
    global $wpdb;
    
    $charset_collate = $wpdb->get_charset_collate();
    
    // Main pricing table
    $table_pricing = $wpdb->prefix . 'pgc_pricing';
    $sql = "CREATE TABLE IF NOT EXISTS $table_pricing (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        service_slug varchar(100) NOT NULL,
        category varchar(100) NOT NULL,
        item_key varchar(100) NOT NULL,
        item_value varchar(255) NOT NULL,
        price decimal(10,2) NOT NULL DEFAULT 0.00,
        price_type enum('fixed','per_hour','per_unit','per_sqm') DEFAULT 'fixed',
        frequency_4wk decimal(10,2) DEFAULT NULL,
        frequency_8wk decimal(10,2) DEFAULT NULL,
        frequency_12wk decimal(10,2) DEFAULT NULL,
        addon_price decimal(10,2) DEFAULT NULL,
        description text,
        active tinyint(1) DEFAULT 1,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY service_slug (service_slug),
        KEY category (category),
        KEY active (active)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Quote submissions table
    $table_quotes = $wpdb->prefix . 'pgc_quotes';
    $sql2 = "CREATE TABLE IF NOT EXISTS $table_quotes (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        quote_id varchar(50) NOT NULL,
        service_slug varchar(100) NOT NULL,
        customer_name varchar(100) NOT NULL,
        customer_email varchar(100) NOT NULL,
        customer_phone varchar(20) NOT NULL,
        customer_address text,
        postcode varchar(20) NOT NULL,
        quote_data longtext NOT NULL,
        calculated_price decimal(10,2) NOT NULL,
        final_price decimal(10,2) DEFAULT NULL,
        status enum('pending','approved','rejected','completed') DEFAULT 'pending',
        notes text,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY quote_id (quote_id),
        KEY service_slug (service_slug),
        KEY status (status)
    ) $charset_collate;";
    
    dbDelta($sql2);
}

/**
 * Seed Default Pricing Data
 */
function pgc_seed_default_pricing(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    // Check if data already exists
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    if ($count > 0) {
        return;
    }
    
    $pricing_data = [
        // Window Cleaning - Residential
        ['window-cleaning', 'residential', '2-bed', '2 Bedroom', 24.50, 'fixed', 26.50, 28.50, null, 'Includes windows, frames, sills and doors'],
        ['window-cleaning', 'residential', '3-bed', '3 Bedroom', 27.50, 'fixed', 29.50, 32.50, null, 'Includes windows, frames, sills and doors'],
        ['window-cleaning', 'residential', '4-bed', '4 Bedroom', 31.50, 'fixed', 33.50, 35.50, null, 'Includes windows, frames, sills and doors'],
        ['window-cleaning', 'residential', '5-bed', '5 Bedroom', 39.50, 'fixed', 41.50, 43.50, null, 'Includes windows, frames, sills and doors'],
        
        // Window Cleaning - Addons
        ['window-cleaning', 'addon', 'conservatory', 'Conservatory', 10.00, 'fixed', null, null, null, 'Add-on service'],
        ['window-cleaning', 'addon', 'extension', 'Extension', 6.00, 'fixed', null, null, null, 'Add-on service'],
        ['window-cleaning', 'addon', 'interior-windows', 'Interior Windows Only', 25.00, 'per_unit', null, null, null, 'Plus 25% on base price'],
        ['window-cleaning', 'addon', 'first-clean', 'First Clean', 50.00, 'per_unit', null, null, null, 'Plus 50% on base price'],
        
        // Velux & Skylights
        ['window-cleaning', 'specialist', 'velux', 'Velux Window', 4.00, 'per_unit', null, null, null, 'Per window'],
        ['window-cleaning', 'specialist', 'skylight', 'Skylight', 6.00, 'per_unit', null, null, null, 'Per skylight'],
        
        // Conservatory Roof
        ['window-cleaning', 'conservatory-roof', 'small', 'Small Conservatory', 90.00, 'fixed', null, null, null, 'Roof cleaning only'],
        ['window-cleaning', 'conservatory-roof', 'medium', 'Medium Conservatory', 120.00, 'fixed', null, null, null, 'Roof cleaning only'],
        ['window-cleaning', 'conservatory-roof', 'large', 'Large Conservatory', 160.00, 'fixed', null, null, null, 'Roof cleaning only'],
        
        // Internal Conservatory
        ['window-cleaning', 'conservatory-internal', '3x3', '3x3 Conservatory', 45.00, 'fixed', null, null, null, '£30-60 range, average'],
        ['window-cleaning', 'conservatory-internal', '4x4', '4x4/Lean-to', 75.00, 'fixed', null, null, null, '£60-90 range, average'],
        ['window-cleaning', 'conservatory-internal', '5x5', '5x5 Conservatory', 110.00, 'fixed', null, null, null, '£90-130 range, average'],
        
        // Gutter Cleaning
        ['gutter-cleaning', 'residential', '2-bed-semi', '2 Bed Semi-Detached', 100.00, 'fixed', null, null, 40.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '2-bed-detached', '2 Bed Detached', 110.00, 'fixed', null, null, 45.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '3-bed-semi', '3 Bed Semi-Detached', 130.00, 'fixed', null, null, 50.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '3-bed-detached', '3 Bed Detached', 140.00, 'fixed', null, null, 55.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '4-bed-semi', '4 Bed Semi-Detached', 150.00, 'fixed', null, null, 60.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '4-bed-detached', '4 Bed Detached', 160.00, 'fixed', null, null, 65.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', '5-bed', '5 Bed House', 170.00, 'fixed', null, null, 70.00, 'Includes standard clean'],
        ['gutter-cleaning', 'residential', 'town-house', 'Town House', 190.00, 'fixed', null, null, 70.00, 'Includes standard clean'],
        
        // Gutter Addons
        ['gutter-cleaning', 'addon', 'extension', 'Extension', 10.00, 'fixed', null, null, null, 'Additional extension'],
        ['gutter-cleaning', 'addon', 'conservatory', 'Conservatory Gutters', 10.00, 'fixed', null, null, null, 'Conservatory gutter cleaning'],
        ['gutter-cleaning', 'addon', 'soffit-fascia', 'Soffit & Fascia', 40.00, 'fixed', null, null, null, 'Soffit and fascia cleaning'],
        
        // Solar Panel Cleaning
        ['solar-panel-cleaning', 'residential', '1-10-panels', '1-10 Panels', 100.00, 'fixed', null, null, null, '£80-120 range, average'],
        ['solar-panel-cleaning', 'residential', '11-20-panels', '11-20 Panels', 150.00, 'fixed', null, null, null, '£120-180 range, average'],
        
        // Pressure Washing
        ['pressure-washing', 'concrete', 'basic', 'Concrete - Basic', 4.00, 'per_sqm', null, null, null, 'Water only, £100 minimum'],
        ['pressure-washing', 'concrete', 'deep', 'Concrete - Deep Clean', 6.50, 'per_sqm', null, null, null, 'With bio treatment'],
        ['pressure-washing', 'block-paving', 'basic', 'Block Paving - Basic', 5.00, 'per_sqm', null, null, null, 'Water only, £100 minimum'],
        ['pressure-washing', 'block-paving', 'deep', 'Block Paving - Deep Clean', 7.50, 'per_sqm', null, null, null, 'With bio treatment'],
        ['pressure-washing', 'indian-sandstone', 'basic', 'Indian Sandstone - Basic', 5.00, 'per_sqm', null, null, null, 'Water only, £100 minimum'],
        ['pressure-washing', 'indian-sandstone', 'deep', 'Indian Sandstone - Deep Clean', 7.50, 'per_sqm', null, null, null, 'With bio treatment'],
        ['pressure-washing', 'tarmac', 'basic', 'Tarmac - Basic', 4.00, 'per_sqm', null, null, null, 'Water only, £100 minimum'],
        ['pressure-washing', 'tarmac', 'deep', 'Tarmac - Deep Clean', 6.50, 'per_sqm', null, null, null, 'With bio treatment'],
        ['pressure-washing', 'resin', 'basic', 'Resin - Basic', 4.00, 'per_sqm', null, null, null, 'Water only, £100 minimum'],
        ['pressure-washing', 'resin', 'deep', 'Resin - Deep Clean', 6.50, 'per_sqm', null, null, null, 'With bio treatment'],
        
        // Pressure Washing Addons
        ['pressure-washing', 'addon', 'sealing', 'Sealing (Block Paving)', 8.00, 'per_sqm', null, null, null, 'Sealing service'],
        ['pressure-washing', 'addon', 'sand-refill', 'Sand Refill', 3.00, 'per_sqm', null, null, null, 'Kiln-dried sand refill'],
        
        // Domestic Cleaning
        ['domestic-cleaning', 'hourly', 'standard', 'Standard Rate', 25.00, 'per_hour', null, null, null, 'Minimum 2 hours'],
        
        // Domestic Addons
        ['domestic-cleaning', 'addon', 'inside-fridge', 'Inside Fridge', 15.00, 'fixed', null, null, null, 'Internal fridge clean'],
        ['domestic-cleaning', 'addon', 'inside-microwave', 'Inside Microwave', 10.00, 'fixed', null, null, null, 'Internal microwave clean'],
        ['domestic-cleaning', 'addon', 'oven-clean', 'Oven Clean', 45.00, 'fixed', null, null, null, 'Oven cleaning add-on'],
        ['domestic-cleaning', 'addon', 'interior-windows', 'Interior Windows', 6.00, 'per_unit', null, null, null, 'Per window'],
        ['domestic-cleaning', 'addon', 'bedsheet-change', 'Bedsheet Changing', 6.00, 'per_unit', null, null, null, 'Per bed'],
        
        // End of Tenancy
        ['end-of-tenancy', 'studio', 'standard', 'Studio Flat', 190.00, 'fixed', null, null, null, 'From price'],
        ['end-of-tenancy', '1-bed', 'standard', '1 Bedroom', 230.00, 'fixed', null, null, null, 'From price'],
        ['end-of-tenancy', '2-bed', 'standard', '2 Bedroom', 270.00, 'fixed', null, null, null, 'From price'],
        ['end-of-tenancy', '3-bed', 'standard', '3 Bedroom', 310.00, 'fixed', null, null, null, 'From price'],
        ['end-of-tenancy', '4-5-bed', 'standard', '4-5 Bedroom', 390.00, 'fixed', null, null, null, 'From price'],
        ['end-of-tenancy', '6-bed-plus', 'standard', '6 Bedroom+', 430.00, 'fixed', null, null, null, 'From price'],
        
        // Post Construction
        ['post-construction', 'studio', 'standard', 'Studio Flat', 214.00, 'fixed', null, null, null, 'From price'],
        ['post-construction', '1-bed', 'standard', '1 Bedroom', 259.00, 'fixed', null, null, null, 'From price'],
        ['post-construction', '2-bed', 'standard', '2 Bedroom', 304.00, 'fixed', null, null, null, 'From price'],
        ['post-construction', '3-bed', 'standard', '3 Bedroom', 349.00, 'fixed', null, null, null, 'From price'],
        ['post-construction', '4-5-bed', 'standard', '4-5 Bedroom', 439.00, 'fixed', null, null, null, 'From price'],
        ['post-construction', '6-bed-plus', 'standard', '6 Bedroom+', 484.00, 'fixed', null, null, null, 'From price'],
        
        // Oven Cleaning
        ['oven-cleaning', 'single', 'standard', 'Single Oven', 68.00, 'fixed', null, null, null, 'Standard single oven'],
        ['oven-cleaning', 'double', 'standard', 'Double Oven', 79.00, 'fixed', null, null, null, 'Double oven'],
        ['oven-cleaning', 'range', 'standard', 'Range/Rangemaster', 101.00, 'fixed', null, null, null, 'Range cooker'],
        ['oven-cleaning', 'aga', 'standard', 'AGA Oven', 146.00, 'fixed', null, null, null, 'AGA/Rayburn'],
        
        // Oven Addons
        ['oven-cleaning', 'addon', 'fridge', 'Fridge Clean', 40.00, 'fixed', null, null, null, 'Additional appliance'],
        ['oven-cleaning', 'addon', 'microwave', 'Microwave Clean', 25.00, 'fixed', null, null, null, 'Additional appliance'],
        
        // Carpet Cleaning
        ['carpet-cleaning', 'small', 'standard', 'Small Room (4x4)', 62.00, 'fixed', null, null, null, 'Per room'],
        ['carpet-cleaning', 'medium', 'standard', 'Medium Room (5x5)', 73.00, 'fixed', null, null, null, 'Per room'],
        ['carpet-cleaning', 'large', 'standard', 'Large Room (6x6)', 90.00, 'fixed', null, null, null, 'Per room'],
        ['carpet-cleaning', 'stairs-landing', 'standard', 'Stairs + Landing', 101.00, 'fixed', null, null, null, 'Staircase and landing'],
        ['carpet-cleaning', 'commercial', 'standard', 'Commercial', 5.00, 'per_sqm', null, null, null, 'Per square metre'],
    ];
    
    foreach ($pricing_data as $item) {
        $wpdb->insert($table, [
            'service_slug' => $item[0],
            'category' => $item[1],
            'item_key' => $item[2],
            'item_value' => $item[3],
            'price' => $item[4],
            'price_type' => $item[5],
            'frequency_4wk' => $item[6],
            'frequency_8wk' => $item[7],
            'frequency_12wk' => $item[8],
            'addon_price' => $item[9],
            'description' => $item[10] ?? '',
        ]);
    }
}

/**
 * Get Pricing for Service
 */
function pgc_get_pricing(string $service_slug, string $category = ''): array {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    $sql = "SELECT * FROM $table WHERE service_slug = %s AND active = 1";
    $params = [$service_slug];
    
    if ($category) {
        $sql .= " AND category = %s";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY category, id";
    
    return $wpdb->get_results($wpdb->prepare($sql, $params), ARRAY_A);
}

/**
 * Get Single Price Item
 */
function pgc_get_price_item(string $service_slug, string $item_key): ?array {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    return $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE service_slug = %s AND item_key = %s AND active = 1",
        $service_slug,
        $item_key
    ), ARRAY_A);
}

/**
 * Update Pricing
 */
function pgc_update_pricing(int $id, array $data): bool {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    return $wpdb->update($table, $data, ['id' => $id]) !== false;
}

/**
 * Add New Pricing Item
 */
function pgc_add_pricing(array $data): int {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    $wpdb->insert($table, $data);
    return $wpdb->insert_id;
}

/**
 * Delete Pricing Item
 */
function pgc_delete_pricing(int $id): bool {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    return $wpdb->delete($table, ['id' => $id]) !== false;
}

/**
 * Get All Service Slugs
 */
function pgc_get_service_slugs(): array {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_pricing';
    
    return $wpdb->get_col("SELECT DISTINCT service_slug FROM $table ORDER BY service_slug");
}

/**
 * Save Quote Submission
 */
function pgc_save_quote(array $data): ?string {
    global $wpdb;
    $table = $wpdb->prefix . 'pgc_quotes';
    
    $quote_id = 'PGC-' . date('Y') . '-' . strtoupper(wp_generate_password(6, false));
    
    $result = $wpdb->insert($table, [
        'quote_id' => $quote_id,
        'service_slug' => $data['service'],
        'customer_name' => $data['first_name'] . ' ' . $data['last_name'],
        'customer_email' => $data['email'],
        'customer_phone' => $data['phone'],
        'customer_address' => ($data['address_line_1'] ?? '') . ', ' . ($data['address_line_2'] ?? ''),
        'postcode' => $data['postcode'] ?? '',
        'quote_data' => json_encode($data),
        'calculated_price' => $data['calculated_price'] ?? 0,
        'status' => 'pending',
    ]);
    
    return $result ? $quote_id : null;
}
