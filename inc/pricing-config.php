<?php
/**
 * ProGreenClean Pricing Configuration
 * All value_location fields from JSON mapped to admin pricing structure
 */

if (!defined('ABSPATH')) exit;

// Pricing sections configuration
$pgc_pricing_sections = [
    'Window Cleaning - Base Prices & Addons' => [
        'description' => 'Base prices by bedroom, frequency, and addon options (all prices per bedroom)',
        'type' => 'table',
        'headers' => ['Bedrooms', 'One-Off', '4 Weekly', '8 Weekly', '12 Weekly', 'Extension', 'Conservatory'],
        'rows' => [
            ['label' => '2 Bedrooms', 'keys' => ['ow_win_2bed_oneoff', 'ow_win_2bed_4week', 'ow_win_2bed_8week', 'ow_win_2bed_12week', 'ow_win_ext_2bed', 'ow_win_cons_2bed']],
            ['label' => '3 Bedrooms', 'keys' => ['ow_win_3bed_oneoff', 'ow_win_3bed_4week', 'ow_win_3bed_8week', 'ow_win_3bed_12week', 'ow_win_ext_3bed', 'ow_win_cons_3bed']],
            ['label' => '4 Bedrooms', 'keys' => ['ow_win_4bed_oneoff', 'ow_win_4bed_4week', 'ow_win_4bed_8week', 'ow_win_4bed_12week', 'ow_win_ext_4bed', 'ow_win_cons_4bed']],
            ['label' => '5 Bedrooms', 'keys' => ['ow_win_5bed_oneoff', 'ow_win_5bed_4week', 'ow_win_5bed_8week', 'ow_win_5bed_12week', 'ow_win_ext_5bed', 'ow_win_cons_5bed']],
        ]
    ],
    'Window Cleaning - Conservatory Roof' => [
        'description' => 'Conservatory roof size definitions and pricing (Internal & External)',
        'type' => 'settings',
        'fields' => [
            ['key' => 'ow_win_cons_roof_small_size', 'label' => 'Small Size Definition (e.g., 3x3m)', 'type' => 'text'],
            ['key' => 'ow_win_cons_roof_small_int', 'label' => 'Small - Internal Price', 'type' => 'price'],
            ['key' => 'ow_win_cons_roof_small_ext', 'label' => 'Small - External Price', 'type' => 'price'],
            ['key' => 'ow_win_cons_roof_medium_size', 'label' => 'Medium Size Definition (e.g., 4x4m / Lean to)', 'type' => 'text'],
            ['key' => 'ow_win_cons_roof_medium_int', 'label' => 'Medium - Internal Price', 'type' => 'price'],
            ['key' => 'ow_win_cons_roof_medium_ext', 'label' => 'Medium - External Price', 'type' => 'price'],
            ['key' => 'ow_win_cons_roof_large_size', 'label' => 'Large Size Definition (e.g., 5x5m)', 'type' => 'text'],
            ['key' => 'ow_win_cons_roof_large_int', 'label' => 'Large - Internal Price', 'type' => 'price'],
            ['key' => 'ow_win_cons_roof_large_ext', 'label' => 'Large - External Price', 'type' => 'price'],
        ]
    ],
    'Window Cleaning - Other Addons' => [
        'description' => 'Additional window cleaning services',
        'fields' => [
            ['key' => 'ow_win_skylight_unit', 'label' => 'Skylight (per unit)', 'type' => 'price'],
            ['key' => 'ow_win_velux_unit', 'label' => 'Velux Window (per unit)', 'type' => 'price'],
        ]
    ],
    'Gutter Cleaning' => [
        'description' => 'Gutter clearing and maintenance',
        'fields' => [
            ['key' => 'ow_gut_base_detached', 'label' => 'Detached House', 'type' => 'price'],
            ['key' => 'ow_gut_base_semi', 'label' => 'Semi-Detached House', 'type' => 'price'],
            ['key' => 'ow_gut_base_terraced', 'label' => 'Terraced House', 'type' => 'price'],
            ['key' => 'ow_gut_base_townhouse', 'label' => 'Town House', 'type' => 'price'],
            ['key' => 'ow_gut_base_bungalow', 'label' => 'Bungalow', 'type' => 'price'],
            ['key' => 'ow_gut_base_flat', 'label' => 'Flat', 'type' => 'price'],
            ['key' => 'ow_gut_addon_ext', 'label' => 'Add-on: Extension', 'type' => 'price'],
            ['key' => 'ow_gut_addon_cons', 'label' => 'Add-on: Conservatory', 'type' => 'price'],
            ['key' => 'ow_gut_addon_soffit', 'label' => 'Add-on: Soffit & Fascia', 'type' => 'price'],
        ]
    ],
    'Domestic Cleaning' => [
        'description' => 'Regular domestic cleaning services',
        'fields' => [
            ['key' => 'ow_dom_hourly_rate', 'label' => 'Hourly Rate', 'type' => 'price'],
        ]
    ],
    'End of Tenancy' => [
        'description' => 'End of tenancy and deep cleaning',
        'fields' => [
            ['key' => 'ow_eot_studio', 'label' => 'Studio Flat', 'type' => 'price'],
            ['key' => 'ow_eot_1bed', 'label' => '1 Bedroom', 'type' => 'price'],
            ['key' => 'ow_eot_2bed', 'label' => '2 Bedroom', 'type' => 'price'],
            ['key' => 'ow_eot_3bed', 'label' => '3 Bedroom', 'type' => 'price'],
            ['key' => 'ow_eot_4bed', 'label' => '4 Bedroom', 'type' => 'price'],
            ['key' => 'ow_eot_5bed', 'label' => '5+ Bedroom', 'type' => 'price'],
        ]
    ],
    'Post Construction' => [
        'description' => 'Post construction and builders cleaning',
        'fields' => [
            ['key' => 'ow_pc_studio', 'label' => 'Studio Flat', 'type' => 'price'],
            ['key' => 'ow_pc_1bed', 'label' => '1 Bedroom', 'type' => 'price'],
            ['key' => 'ow_pc_2bed', 'label' => '2 Bedroom', 'type' => 'price'],
            ['key' => 'ow_pc_3bed', 'label' => '3 Bedroom', 'type' => 'price'],
            ['key' => 'ow_pc_4bed', 'label' => '4-5 Bedroom', 'type' => 'price'],
            ['key' => 'ow_pc_5bed', 'label' => '6+ Bedroom', 'type' => 'price'],
        ]
    ],
    'Carpet Cleaning - Room Size Definitions' => [
        'description' => 'Define carpet room sizes and pricing',
        'type' => 'settings',
        'fields' => [
            ['key' => 'ow_carpet_small_size', 'label' => 'Small Room Size Definition (e.g., 4x4m)', 'type' => 'text'],
            ['key' => 'ow_carpet_small', 'label' => 'Small Room Price', 'type' => 'price'],
            ['key' => 'ow_carpet_medium_size', 'label' => 'Medium Room Size Definition (e.g., 5x5m)', 'type' => 'text'],
            ['key' => 'ow_carpet_medium', 'label' => 'Medium Room Price', 'type' => 'price'],
            ['key' => 'ow_carpet_large_size', 'label' => 'Large Room Size Definition (e.g., 6x6m)', 'type' => 'text'],
            ['key' => 'ow_carpet_large', 'label' => 'Large Room Price', 'type' => 'price'],
        ]
    ],
    'Carpet Cleaning - Other' => [
        'description' => 'Additional carpet cleaning options',
        'fields' => [
            ['key' => 'ow_carpet_stairs_landing', 'label' => 'Stairs & Landing', 'type' => 'price'],
            ['key' => 'ow_carpet_unit', 'label' => 'Per Room (EOT)', 'type' => 'price'],
        ]
    ],
    'Oven Cleaning' => [
        'description' => 'Professional oven and appliance cleaning',
        'fields' => [
            ['key' => 'ow_price_oven_single', 'label' => 'Single Oven', 'type' => 'price'],
            ['key' => 'ow_price_oven_double', 'label' => 'Double Oven', 'type' => 'price'],
            ['key' => 'ow_price_oven_range', 'label' => 'Range / Rangemaster', 'type' => 'price'],
            ['key' => 'ow_price_oven_aga', 'label' => 'AGA Oven', 'type' => 'price'],
        ]
    ],
    'Extras' => [
        'description' => 'Additional services and add-ons',
        'fields' => [
            ['key' => 'ow_price_fridge', 'label' => 'Inside Fridge', 'type' => 'price'],
            ['key' => 'ow_price_microwave', 'label' => 'Inside Microwave', 'type' => 'price'],
            ['key' => 'ow_price_bedsheets', 'label' => 'Bed Sheet Changing', 'type' => 'price'],
            ['key' => 'ow_price_interior_window', 'label' => 'Interior Window (each)', 'type' => 'price'],
        ]
    ],
];

// Default pricing values
$pgc_default_pricing = [
    // Window Cleaning - 2 Bedroom
    'ow_win_2bed_oneoff' => 36.75,
    'ow_win_2bed_4week' => 24.50,
    'ow_win_2bed_8week' => 26.50,
    'ow_win_2bed_12week' => 28.50,
    'ow_win_ext_2bed' => 6.00,
    'ow_win_cons_2bed' => 10.00,
    
    // Window Cleaning - 3 Bedroom
    'ow_win_3bed_oneoff' => 41.25,
    'ow_win_3bed_4week' => 27.50,
    'ow_win_3bed_8week' => 29.50,
    'ow_win_3bed_12week' => 32.50,
    'ow_win_ext_3bed' => 6.00,
    'ow_win_cons_3bed' => 10.00,
    
    // Window Cleaning - 4 Bedroom
    'ow_win_4bed_oneoff' => 47.25,
    'ow_win_4bed_4week' => 31.50,
    'ow_win_4bed_8week' => 33.50,
    'ow_win_4bed_12week' => 35.50,
    'ow_win_ext_4bed' => 6.00,
    'ow_win_cons_4bed' => 10.00,
    
    // Window Cleaning - 5 Bedroom
    'ow_win_5bed_oneoff' => 59.25,
    'ow_win_5bed_4week' => 39.50,
    'ow_win_5bed_8week' => 41.50,
    'ow_win_5bed_12week' => 43.50,
    'ow_win_ext_5bed' => 6.00,
    'ow_win_cons_5bed' => 14.00,
    
    // Window Cleaning - Conservatory Roof Sizes & Prices
    'ow_win_cons_roof_small_size' => '3x3m',
    'ow_win_cons_roof_small_int' => 30.00,
    'ow_win_cons_roof_small_ext' => 60.00,
    'ow_win_cons_roof_medium_size' => '4x4m / Lean to',
    'ow_win_cons_roof_medium_int' => 60.00,
    'ow_win_cons_roof_medium_ext' => 80.00,
    'ow_win_cons_roof_large_size' => '5x5m',
    'ow_win_cons_roof_large_int' => 90.00,
    'ow_win_cons_roof_large_ext' => 110.00,
    
    // Window Cleaning - Other Addons
    'ow_win_skylight_unit' => 6.00,
    'ow_win_velux_unit' => 4.00,
    
    // Gutter Cleaning
    'ow_gut_base_detached' => 110.00,
    'ow_gut_base_semi' => 100.00,
    'ow_gut_base_terraced' => 90.00,
    'ow_gut_base_townhouse' => 190.00,
    'ow_gut_base_bungalow' => 85.00,
    'ow_gut_base_flat' => 70.00,
    'ow_gut_addon_ext' => 10.00,
    'ow_gut_addon_cons' => 10.00,
    'ow_gut_addon_soffit' => 40.00,
    
    // Domestic Cleaning
    'ow_dom_hourly_rate' => 25.00,
    
    // End of Tenancy
    'ow_eot_studio' => 190.00,
    'ow_eot_1bed' => 230.00,
    'ow_eot_2bed' => 270.00,
    'ow_eot_3bed' => 310.00,
    'ow_eot_4bed' => 390.00,
    'ow_eot_5bed' => 430.00,
    
    // Post Construction
    'ow_pc_studio' => 214.00,
    'ow_pc_1bed' => 259.00,
    'ow_pc_2bed' => 304.00,
    'ow_pc_3bed' => 349.00,
    'ow_pc_4bed' => 439.00,
    'ow_pc_5bed' => 484.00,
    
    // Carpet Cleaning - Size Definitions
    'ow_carpet_small_size' => '4x4m',
    'ow_carpet_small' => 62.00,
    'ow_carpet_medium_size' => '5x5m',
    'ow_carpet_medium' => 73.00,
    'ow_carpet_large_size' => '6x6m',
    'ow_carpet_large' => 90.00,
    'ow_carpet_stairs_landing' => 101.00,
    'ow_carpet_unit' => 62.00,
    
    // Oven Cleaning
    'ow_price_oven_single' => 68.00,
    'ow_price_oven_double' => 79.00,
    'ow_price_oven_range' => 101.00,
    'ow_price_oven_aga' => 146.00,
    
    // Extras
    'ow_price_fridge' => 40.00,
    'ow_price_microwave' => 25.00,
    'ow_price_bedsheets' => 6.00,
    'ow_price_interior_window' => 6.00,
];

/**
 * Get price for a given key
 */
function pgc_get_price($key) {
    $price = get_option('pgc_price_' . $key, null);
    if ($price === null) {
        global $pgc_default_pricing;
        $price = $pgc_default_pricing[$key] ?? 0;
    }
    return floatval($price);
}

/**
 * Get text option for a given key
 */
function pgc_get_text_option($key) {
    $value = get_option('pgc_price_' . $key, null);
    if ($value === null) {
        global $pgc_default_pricing;
        $value = $pgc_default_pricing[$key] ?? '';
    }
    return sanitize_text_field($value);
}

/**
 * Initialize default pricing on theme activation
 */
function pgc_init_pricing() {
    global $pgc_default_pricing;
    foreach ($pgc_default_pricing as $key => $value) {
        if (get_option('pgc_price_' . $key, null) === null) {
            update_option('pgc_price_' . $key, $value);
        }
    }
}
