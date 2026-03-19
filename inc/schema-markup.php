<?php
/**
 * Schema Markup Functions
 *
 * @package ProGreenClean
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Output LocalBusiness Schema
 */
function pgc_get_localbusiness_schema(): array {
    return [
        '@context' => 'https://schema.org',
        '@type' => ['LocalBusiness', 'ProfessionalService'],
        '@id' => home_url('/#business'),
        'name' => 'ProGreenClean',
        'description' => 'Professional eco-friendly cleaning services in Epsom and Surrey',
        'url' => home_url(),
        'telephone' => get_option('pgc_phone', ''),
        'email' => 'info@progreenclean.co.uk',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => get_option('pgc_street_address', ''),
            'addressLocality' => 'Epsom',
            'addressRegion' => 'Surrey',
            'postalCode' => get_option('pgc_postcode', ''),
            'addressCountry' => 'GB',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 51.3346,
            'longitude' => -0.2686,
        ],
        'areaServed' => [
            '@type' => 'GeoCircle',
            'geoMidpoint' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 51.3346,
                'longitude' => -0.2686,
            ],
            'geoRadius' => '25000',
        ],
        'serviceType' => [
            'Domestic Cleaning',
            'Window Cleaning',
            'Gutter Cleaning',
            'End of Tenancy Cleaning',
            'Conservatory Cleaning',
            'Post-Construction Cleaning',
            'Commercial Cleaning',
            'Carpet Cleaning',
            'Oven Cleaning',
            'Pressure Washing',
            'Solar Panel Cleaning',
            'Graffiti Removal',
        ],
        'openingHours' => ['Mo-Fr 08:00-18:00', 'Sa 09:00-14:00'],
        'priceRange' => '££',
        'paymentAccepted' => ['Cash', 'Credit Card', 'Bank Transfer'],
        'currenciesAccepted' => 'GBP',
        'foundingDate' => '2014',
    ];
}

/**
 * Output Service Schema
 */
function pgc_get_service_schema(string $service_name, string $service_slug): array {
    $pricing = pgc_get_pricing($service_slug, 'residential');
    $offers = [];
    
    foreach ($pricing as $item) {
        $offers[] = [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => $item['item_value'],
            ],
            'price' => number_format(floatval($item['price']), 2),
            'priceCurrency' => 'GBP',
            'priceValidUntil' => date('Y-12-31'),
            'availability' => 'https://schema.org/InStock',
        ];
    }
    
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        '@id' => home_url("/services/{$service_slug}/#service"),
        'serviceType' => $service_name,
        'provider' => ['@id' => home_url('/#business')],
        'areaServed' => ['@id' => home_url('/#serviceArea')],
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => $service_name,
            'itemListElement' => $offers,
        ],
    ];
}

/**
 * Output FAQ Schema
 */
function pgc_get_faq_schema(array $faqs): array {
    $main_entity = [];
    
    foreach ($faqs as $faq) {
        $main_entity[] = [
            '@type' => 'Question',
            'name' => get_the_title($faq),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => wp_strip_all_tags(get_the_content(null, false, $faq)),
            ],
        ];
    }
    
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $main_entity,
    ];
}

/**
 * Output Breadcrumb Schema
 */
function pgc_get_breadcrumb_schema(array $items): array {
    $item_list = [];
    $position = 1;
    
    foreach ($items as $name => $url) {
        $item_list[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $name,
            'item' => $url,
        ];
        $position++;
    }
    
    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $item_list,
    ];
}

/**
 * Output Schema JSON-LD
 */
function pgc_output_schema(array $schema): void {
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_PRETTY_PRINT) . '</script>';
}

/**
 * Add Schema to Head
 */
add_action('wp_head', function(): void {
    // LocalBusiness schema on all pages
    pgc_output_schema(pgc_get_localbusiness_schema());
    
    // Service schema on service pages
    if (is_singular('pgc_service')) {
        $service_slug = get_post_field('post_name', get_the_ID());
        $service_name = get_the_title();
        pgc_output_schema(pgc_get_service_schema($service_name, $service_slug));
    }
    
    // FAQ schema on FAQs page
    if (is_page('faqs')) {
        $faqs = pgc_get_faqs();
        if (!empty($faqs)) {
            pgc_output_schema(pgc_get_faq_schema($faqs));
        }
    }
    
    // Breadcrumb schema
    $breadcrumbs = [];
    $breadcrumbs['Home'] = home_url();
    
    if (is_singular('pgc_service')) {
        $breadcrumbs['Services'] = home_url('/services/');
        $breadcrumbs[get_the_title()] = get_permalink();
    } elseif (is_singular('pgc_location')) {
        $breadcrumbs['Locations'] = home_url('/locations/');
        $breadcrumbs[get_the_title()] = get_permalink();
    } elseif (is_page()) {
        $breadcrumbs[get_the_title()] = get_permalink();
    }
    
    if (count($breadcrumbs) > 1) {
        pgc_output_schema(pgc_get_breadcrumb_schema($breadcrumbs));
    }
});
