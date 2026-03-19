<?php
/**
 * Template Functions
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get all services
 */
function pgc_get_services(int $limit = -1): array {
    $args = [
        'post_type' => 'pgc_service',
        'posts_per_page' => $limit,
        'orderby' => 'meta_value_num',
        'meta_key' => '_pgc_display_order',
        'order' => 'ASC',
    ];
    
    $query = new WP_Query($args);
    return $query->posts;
}

/**
 * Get service by slug
 */
function pgc_get_service(string $slug): ?WP_Post {
    $args = [
        'post_type' => 'pgc_service',
        'name' => $slug,
        'posts_per_page' => 1,
    ];
    
    $query = new WP_Query($args);
    return $query->have_posts() ? $query->posts[0] : null;
}

/**
 * Get locations
 */
function pgc_get_locations(int $limit = -1): array {
    $args = [
        'post_type' => 'pgc_location',
        'posts_per_page' => $limit,
        'orderby' => 'title',
        'order' => 'ASC',
    ];
    
    $query = new WP_Query($args);
    return $query->posts;
}

/**
 * Get FAQs
 */
function pgc_get_faqs(string $category = '', int $limit = -1): array {
    $args = [
        'post_type' => 'pgc_faq',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    ];
    
    if ($category) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'pgc_faq_category',
                'field' => 'slug',
                'terms' => $category,
            ],
        ];
    }
    
    $query = new WP_Query($args);
    return $query->posts;
}

/**
 * Get testimonials
 */
function pgc_get_testimonials(int $limit = -1, string $location = ''): array {
    $args = [
        'post_type' => 'pgc_testimonial',
        'posts_per_page' => $limit,
        'orderby' => 'date',
        'order' => 'DESC',
    ];
    
    $query = new WP_Query($args);
    return $query->posts;
}

/**
 * Get team members
 */
function pgc_get_team(int $limit = -1): array {
    $args = [
        'post_type' => 'pgc_team',
        'posts_per_page' => $limit,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ];
    
    $query = new WP_Query($args);
    return $query->posts;
}

/**
 * Service icon SVG
 */
function pgc_get_service_icon(string $icon): string {
    $icons = [
        'sparkles' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>',
        'home-heart' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/><path d="M12 17.5a2.5 2.5 0 0 1 0-5 2.5 2.5 0 0 1 0 5Z"/></svg>',
        'cloud-rain' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M16 14v6"/><path d="M8 14v6"/><path d="M12 16v6"/></svg>',
        'key-return' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m11 16-5 5"/><path d="M21 16v5h-5"/><circle cx="7" cy="7" r="5"/><path d="m21 21-5-5"/></svg>',
        'building-store' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4 8 4v14"/><path d="M5 7h16"/><path d="M8 21v-6a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v6"/></svg>',
        'droplet' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/></svg>',
        'leaf' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.77 10.68-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
        'shield-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>',
        'badge-check' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"/><path d="m9 12 2 2 4-4"/></svg>',
        'map-pin' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>',
        'star-filled' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>',
        'check-circle' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
        'sun' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
    ];
    
    return $icons[$icon] ?? $icons['sparkles'];
}

/**
 * Get phone number formatted
 */
function pgc_get_phone(): string {
    return get_option('pgc_phone') ?: '';
}

/**
 * Format phone for tel: link
 */
function pgc_get_phone_link(string $phone = ''): string {
    $phone = $phone ?: pgc_get_phone();
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

/**
 * Get business email
 */
function pgc_get_email(): string {
    return get_option('pgc_email') ?: 'info@progreenclean.co.uk';
}

/**
 * Truncate text
 */
function pgc_truncate(string $text, int $length = 150): string {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
}

/**
 * Body classes
 */
add_filter('body_class', function (array $classes): array {
    if (is_page_template('service-page')) {
        $classes[] = 'pgc-service-page';
    }
    if (is_page_template('location-page')) {
        $classes[] = 'pgc-location-page';
    }
    if (is_page_template('quote-page')) {
        $classes[] = 'pgc-quote-page';
    }
    return $classes;
});

/**
 * Add defer to scripts
 */
add_filter('script_loader_tag', function (string $tag, string $handle): string {
    if (strpos($handle, 'progreenclean') !== false) {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);
