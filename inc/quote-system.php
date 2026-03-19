<?php
/**
 * Quote System AJAX Handlers
 *
 * @package ProGreenClean
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX: Get Pricing for Service
 */
add_action('wp_ajax_pgc_get_pricing', 'pgc_ajax_get_pricing');
add_action('wp_ajax_nopriv_pgc_get_pricing', 'pgc_ajax_get_pricing');

function pgc_ajax_get_pricing(): void {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $service = sanitize_text_field($_POST['service'] ?? '');
    if (!$service) {
        wp_send_json_error('Service not specified');
    }
    
    $pricing = pgc_get_pricing($service);
    wp_send_json_success($pricing);
}

/**
 * AJAX: Calculate Quote
 */
add_action('wp_ajax_pgc_calculate_quote', 'pgc_ajax_calculate_quote');
add_action('wp_ajax_nopriv_pgc_calculate_quote', 'pgc_ajax_calculate_quote');

function pgc_ajax_calculate_quote(): void {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $service = sanitize_text_field($_POST['service'] ?? '');
    $answers = json_decode(stripslashes($_POST['answers'] ?? '{}'), true);
    
    $calculator = new PGC_Quote_Calculator($service, $answers);
    $result = $calculator->calculate();
    
    wp_send_json_success($result);
}

/**
 * AJAX: Submit Quote
 */
add_action('wp_ajax_pgc_submit_quote', 'pgc_ajax_submit_quote');
add_action('wp_ajax_nopriv_pgc_submit_quote', 'pgc_ajax_submit_quote');

function pgc_ajax_submit_quote(): void {
    check_ajax_referer('pgc_nonce', 'nonce');
    
    $data = [
        'service' => sanitize_text_field($_POST['service'] ?? ''),
        'first_name' => sanitize_text_field($_POST['first_name'] ?? ''),
        'last_name' => sanitize_text_field($_POST['last_name'] ?? ''),
        'email' => sanitize_email($_POST['email'] ?? ''),
        'phone' => sanitize_text_field($_POST['phone'] ?? ''),
        'address_line_1' => sanitize_text_field($_POST['address_line_1'] ?? ''),
        'address_line_2' => sanitize_text_field($_POST['address_line_2'] ?? ''),
        'postcode' => sanitize_text_field($_POST['postcode'] ?? ''),
        'hear_about' => sanitize_text_field($_POST['hear_about'] ?? ''),
        'additional_info' => sanitize_textarea_field($_POST['additional_info'] ?? ''),
        'quote_answers' => json_decode(stripslashes($_POST['quote_answers'] ?? '{}'), true),
        'calculated_price' => floatval($_POST['calculated_price'] ?? 0),
    ];
    
    // Validate required fields
    $required = ['service', 'first_name', 'last_name', 'email', 'phone', 'postcode'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            wp_send_json_error("Please fill in all required fields");
        }
    }
    
    // Save quote
    $quote_id = pgc_save_quote($data);
    
    if (!$quote_id) {
        wp_send_json_error("Failed to save quote. Please try again.");
    }
    
    // Send emails
    pgc_send_quote_emails($data, $quote_id);
    
    wp_send_json_success([
        'quote_id' => $quote_id,
        'message' => "Thank you! Your quote request has been received. We'll be in touch shortly.",
    ]);
}

/**
 * Quote Calculator Class
 */
class PGC_Quote_Calculator {
    private string $service;
    private array $answers;
    private float $base_price = 0;
    private array $breakdown = [];
    private array $addons = [];
    
    public function __construct(string $service, array $answers) {
        $this->service = $service;
        $this->answers = $answers;
    }
    
    public function calculate(): array {
        switch ($this->service) {
            case 'window-cleaning':
                $this->calculate_window_cleaning();
                break;
            case 'gutter-cleaning':
                $this->calculate_gutter_cleaning();
                break;
            case 'domestic-cleaning':
                $this->calculate_domestic_cleaning();
                break;
            case 'end-of-tenancy':
                $this->calculate_end_of_tenancy();
                break;
            case 'oven-cleaning':
                $this->calculate_oven_cleaning();
                break;
            case 'carpet-cleaning':
                $this->calculate_carpet_cleaning();
                break;
            case 'pressure-washing':
                return $this->manual_quote_required('Images or site visit required for accurate pricing');
            case 'solar-panel-cleaning':
                $this->calculate_solar_panel_cleaning();
                break;
            default:
                return $this->manual_quote_required('Please contact us for a customised quote');
        }
        
        return [
            'base_price' => $this->base_price,
            'total_price' => $this->base_price + array_sum(array_column($this->addons, 'price')),
            'breakdown' => $this->breakdown,
            'addons' => $this->addons,
            'requires_manual' => false,
        ];
    }
    
    private function calculate_window_cleaning(): void {
        $bedrooms = $this->answers['bedrooms'] ?? '2-bed';
        $frequency = $this->answers['frequency'] ?? 'one-off';
        
        $pricing = pgc_get_price_item('window-cleaning', $bedrooms);
        if ($pricing) {
            $price = floatval($pricing['price']);
            if ($frequency === '4-weeks' && $pricing['frequency_4wk']) {
                $price = floatval($pricing['frequency_4wk']);
            } elseif ($frequency === '8-weeks' && $pricing['frequency_8wk']) {
                $price = floatval($pricing['frequency_8wk']);
            } elseif ($frequency === '12-weeks' && $pricing['frequency_12wk']) {
                $price = floatval($pricing['frequency_12wk']);
            }
            
            $this->base_price = $price;
            $this->breakdown[] = ['item' => $pricing['item_value'], 'price' => $price];
        }
        
        // Add-ons
        if (!empty($this->answers['conservatory'])) {
            $addon = pgc_get_price_item('window-cleaning', 'conservatory');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
        
        if (!empty($this->answers['extension'])) {
            $addon = pgc_get_price_item('window-cleaning', 'extension');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
        
        if (!empty($this->answers['velux_count']) && $this->answers['velux_count'] > 0) {
            $velux = pgc_get_price_item('window-cleaning', 'velux');
            if ($velux) {
                $price = floatval($velux['price']) * intval($this->answers['velux_count']);
                $this->addons[] = ['item' => $this->answers['velux_count'] . 'x Velux Window', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['skylight_count']) && $this->answers['skylight_count'] > 0) {
            $skylight = pgc_get_price_item('window-cleaning', 'skylight');
            if ($skylight) {
                $price = floatval($skylight['price']) * intval($this->answers['skylight_count']);
                $this->addons[] = ['item' => $this->answers['skylight_count'] . 'x Skylight', 'price' => $price];
            }
        }
    }
    
    private function calculate_gutter_cleaning(): void {
        $property_type = $this->answers['property_type'] ?? '2-bed-semi';
        
        $pricing = pgc_get_price_item('gutter-cleaning', $property_type);
        if ($pricing) {
            $this->base_price = floatval($pricing['price']);
            $this->breakdown[] = ['item' => $pricing['item_value'], 'price' => $this->base_price];
        }
        
        if (!empty($this->answers['extension'])) {
            $addon = pgc_get_price_item('gutter-cleaning', 'extension');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
        
        if (!empty($this->answers['conservatory'])) {
            $addon = pgc_get_price_item('gutter-cleaning', 'conservatory');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
        

    }
    
    private function calculate_domestic_cleaning(): void {
        $hours = intval($this->answers['hours'] ?? 2);
        
        $pricing = pgc_get_price_item('domestic-cleaning', 'standard');
        if ($pricing) {
            $this->base_price = floatval($pricing['price']) * $hours;
            $this->breakdown[] = ['item' => $hours . ' hours domestic cleaning', 'price' => $this->base_price];
        }
        
        $addons_map = ['inside_fridge' => 'inside-fridge', 'inside_microwave' => 'inside-microwave', 'oven_clean' => 'oven-clean'];
        foreach ($addons_map as $key => $slug) {
            if (!empty($this->answers[$key])) {
                $addon = pgc_get_price_item('domestic-cleaning', $slug);
                if ($addon) {
                    $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
                }
            }
        }
        
        if (!empty($this->answers['interior_windows_count'])) {
            $addon = pgc_get_price_item('domestic-cleaning', 'interior-windows');
            if ($addon) {
                $price = floatval($addon['price']) * intval($this->answers['interior_windows_count']);
                $this->addons[] = ['item' => $this->answers['interior_windows_count'] . ' interior windows', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['bedsheet_count'])) {
            $addon = pgc_get_price_item('domestic-cleaning', 'bedsheet-change');
            if ($addon) {
                $price = floatval($addon['price']) * intval($this->answers['bedsheet_count']);
                $this->addons[] = ['item' => $this->answers['bedsheet_count'] . ' bedsheet changes', 'price' => $price];
            }
        }
    }
    
    private function calculate_end_of_tenancy(): void {
        $bedrooms = $this->answers['bedrooms'] ?? '2-bed';
        
        $pricing = pgc_get_price_item('end-of-tenancy', $bedrooms);
        if ($pricing) {
            $this->base_price = floatval($pricing['price']);
            $this->breakdown[] = ['item' => $pricing['item_value'] . ' end of tenancy clean', 'price' => $this->base_price];
        }
        
        if (!empty($this->answers['carpet_cleaning'])) {
            $rooms = intval($this->answers['carpet_rooms'] ?? 1);
            $pricing = pgc_get_price_item('carpet-cleaning', 'medium');
            if ($pricing) {
                $price = floatval($pricing['price']) * $rooms;
                $this->addons[] = ['item' => 'Carpet cleaning (' . $rooms . ' rooms)', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['oven_cleaning'])) {
            $oven_type = $this->answers['oven_type'] ?? 'single';
            $pricing = pgc_get_price_item('oven-cleaning', $oven_type);
            if ($pricing) {
                $this->addons[] = ['item' => $pricing['item_value'], 'price' => floatval($pricing['price'])];
            }
        }
    }
    
    private function calculate_oven_cleaning(): void {
        $oven_type = $this->answers['oven_type'] ?? 'single';
        
        $pricing = pgc_get_price_item('oven-cleaning', $oven_type);
        if ($pricing) {
            $this->base_price = floatval($pricing['price']);
            $this->breakdown[] = ['item' => $pricing['item_value'], 'price' => $this->base_price];
        }
        
        if (!empty($this->answers['fridge'])) {
            $addon = pgc_get_price_item('oven-cleaning', 'fridge');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
        
        if (!empty($this->answers['microwave'])) {
            $addon = pgc_get_price_item('oven-cleaning', 'microwave');
            if ($addon) {
                $this->addons[] = ['item' => $addon['item_value'], 'price' => floatval($addon['price'])];
            }
        }
    }
    
    private function calculate_carpet_cleaning(): void {
        $total = 0;
        
        if (!empty($this->answers['small_rooms'])) {
            $count = intval($this->answers['small_rooms']);
            $pricing = pgc_get_price_item('carpet-cleaning', 'small');
            if ($pricing) {
                $price = floatval($pricing['price']) * $count;
                $total += $price;
                $this->breakdown[] = ['item' => $count . ' small room(s)', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['medium_rooms'])) {
            $count = intval($this->answers['medium_rooms']);
            $pricing = pgc_get_price_item('carpet-cleaning', 'medium');
            if ($pricing) {
                $price = floatval($pricing['price']) * $count;
                $total += $price;
                $this->breakdown[] = ['item' => $count . ' medium room(s)', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['large_rooms'])) {
            $count = intval($this->answers['large_rooms']);
            $pricing = pgc_get_price_item('carpet-cleaning', 'large');
            if ($pricing) {
                $price = floatval($pricing['price']) * $count;
                $total += $price;
                $this->breakdown[] = ['item' => $count . ' large room(s)', 'price' => $price];
            }
        }
        
        if (!empty($this->answers['stairs_landing'])) {
            $pricing = pgc_get_price_item('carpet-cleaning', 'stairs-landing');
            if ($pricing) {
                $total += floatval($pricing['price']);
                $this->breakdown[] = ['item' => $pricing['item_value'], 'price' => floatval($pricing['price'])];
            }
        }
        
        $this->base_price = $total;
    }
    
    private function calculate_solar_panel_cleaning(): void {
        $panel_count = $this->answers['panel_count'] ?? '1-10';
        
        if ($panel_count === '21-plus') {
            $this->breakdown[] = ['item' => 'Custom quote required', 'price' => 0];
            return;
        }
        
        $key = $panel_count === '1-10' ? '1-10-panels' : '11-20-panels';
        $pricing = pgc_get_price_item('solar-panel-cleaning', $key);
        
        if ($pricing) {
            $this->base_price = floatval($pricing['price']);
            $this->breakdown[] = ['item' => $pricing['item_value'], 'price' => $this->base_price];
        }
    }
    
    private function manual_quote_required(string $reason): array {
        return [
            'base_price' => 0,
            'total_price' => 0,
            'breakdown' => [],
            'addons' => [],
            'requires_manual' => true,
            'reason' => $reason,
        ];
    }
}
