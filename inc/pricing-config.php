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
    'Gutter Cleaning - 2 Bedroom' => [
        'description' => 'Gutter clearing prices for 2 bedroom properties',
        'type' => 'table',
        'headers' => ['Property Type', 'Base Price', 'Extension', 'Conservatory', 'Soffit & Fascia', 'Before/After Survey'],
        'rows' => [
            ['label' => '2 Bed Semi', 'keys' => ['ow_gut_2bed_semi', 'ow_gut_2bed_semi_ext', 'ow_gut_2bed_semi_cons', 'ow_gut_2bed_semi_soffit', 'ow_gut_2bed_semi_survey']],
            ['label' => '2 Bed Detached', 'keys' => ['ow_gut_2bed_detached', 'ow_gut_2bed_detached_ext', 'ow_gut_2bed_detached_cons', 'ow_gut_2bed_detached_soffit', 'ow_gut_2bed_detached_survey']],
            ['label' => '2 Bed Terraced', 'keys' => ['ow_gut_2bed_terraced', 'ow_gut_2bed_terraced_ext', 'ow_gut_2bed_terraced_cons', 'ow_gut_2bed_terraced_soffit', 'ow_gut_2bed_terraced_survey']],
            ['label' => '2 Bed Bungalow', 'keys' => ['ow_gut_2bed_bungalow', 'ow_gut_2bed_bungalow_ext', 'ow_gut_2bed_bungalow_cons', 'ow_gut_2bed_bungalow_soffit', 'ow_gut_2bed_bungalow_survey']],
        ]
    ],
    'Gutter Cleaning - 3 Bedroom' => [
        'description' => 'Gutter clearing prices for 3 bedroom properties',
        'type' => 'table',
        'headers' => ['Property Type', 'Base Price', 'Extension', 'Conservatory', 'Soffit & Fascia', 'Before/After Survey'],
        'rows' => [
            ['label' => '3 Bed Semi', 'keys' => ['ow_gut_3bed_semi', 'ow_gut_3bed_semi_ext', 'ow_gut_3bed_semi_cons', 'ow_gut_3bed_semi_soffit', 'ow_gut_3bed_semi_survey']],
            ['label' => '3 Bed Detached', 'keys' => ['ow_gut_3bed_detached', 'ow_gut_3bed_detached_ext', 'ow_gut_3bed_detached_cons', 'ow_gut_3bed_detached_soffit', 'ow_gut_3bed_detached_survey']],
            ['label' => '3 Bed Terraced', 'keys' => ['ow_gut_3bed_terraced', 'ow_gut_3bed_terraced_ext', 'ow_gut_3bed_terraced_cons', 'ow_gut_3bed_terraced_soffit', 'ow_gut_3bed_terraced_survey']],
            ['label' => '3 Bed Bungalow', 'keys' => ['ow_gut_3bed_bungalow', 'ow_gut_3bed_bungalow_ext', 'ow_gut_3bed_bungalow_cons', 'ow_gut_3bed_bungalow_soffit', 'ow_gut_3bed_bungalow_survey']],
        ]
    ],
    'Gutter Cleaning - 4 Bedroom' => [
        'description' => 'Gutter clearing prices for 4 bedroom properties',
        'type' => 'table',
        'headers' => ['Property Type', 'Base Price', 'Extension', 'Conservatory', 'Soffit & Fascia', 'Before/After Survey'],
        'rows' => [
            ['label' => '4 Bed Semi', 'keys' => ['ow_gut_4bed_semi', 'ow_gut_4bed_semi_ext', 'ow_gut_4bed_semi_cons', 'ow_gut_4bed_semi_soffit', 'ow_gut_4bed_semi_survey']],
            ['label' => '4 Bed Detached', 'keys' => ['ow_gut_4bed_detached', 'ow_gut_4bed_detached_ext', 'ow_gut_4bed_detached_cons', 'ow_gut_4bed_detached_soffit', 'ow_gut_4bed_detached_survey']],
            ['label' => '4 Bed Terraced', 'keys' => ['ow_gut_4bed_terraced', 'ow_gut_4bed_terraced_ext', 'ow_gut_4bed_terraced_cons', 'ow_gut_4bed_terraced_soffit', 'ow_gut_4bed_terraced_survey']],
            ['label' => '4 Bed Bungalow', 'keys' => ['ow_gut_4bed_bungalow', 'ow_gut_4bed_bungalow_ext', 'ow_gut_4bed_bungalow_cons', 'ow_gut_4bed_bungalow_soffit', 'ow_gut_4bed_bungalow_survey']],
        ]
    ],
    'Gutter Cleaning - 5+ Bedroom & Other' => [
        'description' => 'Gutter clearing prices for 5+ bedroom properties and other types',
        'type' => 'table',
        'headers' => ['Property Type', 'Base Price', 'Extension', 'Conservatory', 'Soffit & Fascia', 'Before/After Survey'],
        'rows' => [
            ['label' => '5 Bed Semi', 'keys' => ['ow_gut_5bed_semi', 'ow_gut_5bed_semi_ext', 'ow_gut_5bed_semi_cons', 'ow_gut_5bed_semi_soffit', 'ow_gut_5bed_semi_survey']],
            ['label' => '5 Bed Detached', 'keys' => ['ow_gut_5bed_detached', 'ow_gut_5bed_detached_ext', 'ow_gut_5bed_detached_cons', 'ow_gut_5bed_detached_soffit', 'ow_gut_5bed_detached_survey']],
            ['label' => '5 Bed Terraced', 'keys' => ['ow_gut_5bed_terraced', 'ow_gut_5bed_terraced_ext', 'ow_gut_5bed_terraced_cons', 'ow_gut_5bed_terraced_soffit', 'ow_gut_5bed_terraced_survey']],
            ['label' => '5 Bed Bungalow', 'keys' => ['ow_gut_5bed_bungalow', 'ow_gut_5bed_bungalow_ext', 'ow_gut_5bed_bungalow_cons', 'ow_gut_5bed_bungalow_soffit', 'ow_gut_5bed_bungalow_survey']],
            ['label' => 'Townhouse', 'keys' => ['ow_gut_townhouse', 'ow_gut_townhouse_ext', 'ow_gut_townhouse_cons', 'ow_gut_townhouse_soffit', 'ow_gut_townhouse_survey']],
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
    
    // Gutter Cleaning - 2 Bedroom
    'ow_gut_2bed_semi' => 90.00,
    'ow_gut_2bed_semi_ext' => 15.00,
    'ow_gut_2bed_semi_cons' => 15.00,
    'ow_gut_2bed_semi_soffit' => 35.00,
    'ow_gut_2bed_semi_survey' => 25.00,
    'ow_gut_2bed_detached' => 100.00,
    'ow_gut_2bed_detached_ext' => 15.00,
    'ow_gut_2bed_detached_cons' => 15.00,
    'ow_gut_2bed_detached_soffit' => 40.00,
    'ow_gut_2bed_detached_survey' => 25.00,
    'ow_gut_2bed_terraced' => 85.00,
    'ow_gut_2bed_terraced_ext' => 10.00,
    'ow_gut_2bed_terraced_cons' => 10.00,
    'ow_gut_2bed_terraced_soffit' => 30.00,
    'ow_gut_2bed_terraced_survey' => 25.00,
    'ow_gut_2bed_bungalow' => 80.00,
    'ow_gut_2bed_bungalow_ext' => 15.00,
    'ow_gut_2bed_bungalow_cons' => 15.00,
    'ow_gut_2bed_bungalow_soffit' => 35.00,
    'ow_gut_2bed_bungalow_survey' => 25.00,
    
    // Gutter Cleaning - 3 Bedroom
    'ow_gut_3bed_semi' => 100.00,
    'ow_gut_3bed_semi_ext' => 20.00,
    'ow_gut_3bed_semi_cons' => 20.00,
    'ow_gut_3bed_semi_soffit' => 40.00,
    'ow_gut_3bed_semi_survey' => 25.00,
    'ow_gut_3bed_detached' => 110.00,
    'ow_gut_3bed_detached_ext' => 20.00,
    'ow_gut_3bed_detached_cons' => 20.00,
    'ow_gut_3bed_detached_soffit' => 45.00,
    'ow_gut_3bed_detached_survey' => 25.00,
    'ow_gut_3bed_terraced' => 95.00,
    'ow_gut_3bed_terraced_ext' => 15.00,
    'ow_gut_3bed_terraced_cons' => 15.00,
    'ow_gut_3bed_terraced_soffit' => 35.00,
    'ow_gut_3bed_terraced_survey' => 25.00,
    'ow_gut_3bed_bungalow' => 90.00,
    'ow_gut_3bed_bungalow_ext' => 20.00,
    'ow_gut_3bed_bungalow_cons' => 20.00,
    'ow_gut_3bed_bungalow_soffit' => 40.00,
    'ow_gut_3bed_bungalow_survey' => 25.00,
    
    // Gutter Cleaning - 4 Bedroom
    'ow_gut_4bed_semi' => 110.00,
    'ow_gut_4bed_semi_ext' => 25.00,
    'ow_gut_4bed_semi_cons' => 25.00,
    'ow_gut_4bed_semi_soffit' => 45.00,
    'ow_gut_4bed_semi_survey' => 25.00,
    'ow_gut_4bed_detached' => 120.00,
    'ow_gut_4bed_detached_ext' => 25.00,
    'ow_gut_4bed_detached_cons' => 25.00,
    'ow_gut_4bed_detached_soffit' => 50.00,
    'ow_gut_4bed_detached_survey' => 25.00,
    'ow_gut_4bed_terraced' => 105.00,
    'ow_gut_4bed_terraced_ext' => 20.00,
    'ow_gut_4bed_terraced_cons' => 20.00,
    'ow_gut_4bed_terraced_soffit' => 40.00,
    'ow_gut_4bed_terraced_survey' => 25.00,
    'ow_gut_4bed_bungalow' => 100.00,
    'ow_gut_4bed_bungalow_ext' => 25.00,
    'ow_gut_4bed_bungalow_cons' => 25.00,
    'ow_gut_4bed_bungalow_soffit' => 45.00,
    'ow_gut_4bed_bungalow_survey' => 25.00,
    
    // Gutter Cleaning - 5+ Bedroom & Other
    'ow_gut_5bed_semi' => 120.00,
    'ow_gut_5bed_semi_ext' => 30.00,
    'ow_gut_5bed_semi_cons' => 30.00,
    'ow_gut_5bed_semi_soffit' => 50.00,
    'ow_gut_5bed_semi_survey' => 25.00,
    'ow_gut_5bed_detached' => 130.00,
    'ow_gut_5bed_detached_ext' => 30.00,
    'ow_gut_5bed_detached_cons' => 30.00,
    'ow_gut_5bed_detached_soffit' => 55.00,
    'ow_gut_5bed_detached_survey' => 25.00,
    'ow_gut_5bed_terraced' => 115.00,
    'ow_gut_5bed_terraced_ext' => 25.00,
    'ow_gut_5bed_terraced_cons' => 25.00,
    'ow_gut_5bed_terraced_soffit' => 45.00,
    'ow_gut_5bed_terraced_survey' => 25.00,
    'ow_gut_5bed_bungalow' => 110.00,
    'ow_gut_5bed_bungalow_ext' => 30.00,
    'ow_gut_5bed_bungalow_cons' => 30.00,
    'ow_gut_5bed_bungalow_soffit' => 50.00,
    'ow_gut_5bed_bungalow_survey' => 25.00,
    'ow_gut_townhouse' => 140.00,
    'ow_gut_townhouse_ext' => 25.00,
    'ow_gut_townhouse_cons' => 25.00,
    'ow_gut_townhouse_soffit' => 45.00,
    'ow_gut_townhouse_survey' => 25.00,
    
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
