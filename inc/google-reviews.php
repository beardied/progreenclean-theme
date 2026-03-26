<?php
/**
 * ProGreenClean: Google Business Profile Review Sync System
 * 
 * Handles OAuth2, API calls, database storage, and cron jobs for syncing
 * Google reviews to the website.
 */

if (!defined('ABSPATH')) exit;

// =============================================================================
// SECTION 1: DATABASE TABLE
// =============================================================================

/**
 * Creates the custom database table for storing reviews.
 */
function pgc_create_reviews_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pgc_reviews';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        review_id VARCHAR(255) NOT NULL,
        reviewer_display_name VARCHAR(255) DEFAULT '' NOT NULL,
        reviewer_profile_photo_url TEXT,
        star_rating VARCHAR(20) NOT NULL,
        comment TEXT,
        create_time DATETIME NOT NULL,
        update_time DATETIME NOT NULL,
        reply_comment TEXT,
        reply_update_time DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY review_id (review_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
add_action('after_setup_theme', 'pgc_create_reviews_table');

// =============================================================================
// SECTION 2: ADMIN MENU & SETTINGS
// =============================================================================

/**
 * Add Google Reviews menu under PGC Pricing
 */
add_action('admin_menu', function() {
    add_submenu_page(
        'progreenclean-pricing',
        __('Google Reviews', 'progreenclean'),
        __('Google Reviews', 'progreenclean'),
        'manage_options',
        'pgc-google-reviews',
        'pgc_google_reviews_page_html'
    );
});

/**
 * Register settings
 */
add_action('admin_init', function() {
    register_setting('pgc_google_reviews_settings', 'pgc_google_client_id');
    register_setting('pgc_google_reviews_settings', 'pgc_google_client_secret');
    register_setting('pgc_google_reviews_settings', 'pgc_google_account_id');
    register_setting('pgc_google_reviews_settings', 'pgc_google_location_id');
});

// =============================================================================
// SECTION 3: OAUTH2 & API FUNCTIONS
// =============================================================================

/**
 * Generates the Google OAuth2 authorization URL.
 */
function pgc_get_google_auth_url() {
    $client_id = get_option('pgc_google_client_id');
    $redirect_uri = admin_url('admin.php?page=pgc-google-reviews');
    $scope = 'https://www.googleapis.com/auth/business.manage';
    $state = wp_create_nonce('pgc_google_oauth');
    set_transient('pgc_google_oauth_state', $state, 600);

    $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'response_type' => 'code',
        'scope' => $scope,
        'access_type' => 'offline',
        'prompt' => 'consent',
        'state' => $state,
    ]);
    return $auth_url;
}

/**
 * Handles the OAuth2 callback from Google.
 */
function pgc_handle_google_oauth_callback($code) {
    $client_id = get_option('pgc_google_client_id');
    $client_secret = get_option('pgc_google_client_secret');
    $redirect_uri = admin_url('admin.php?page=pgc-google-reviews');
    $token_url = 'https://oauth2.googleapis.com/token';

    $response = wp_remote_post($token_url, [
        'body' => [
            'code' => $code,
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri,
            'grant_type' => 'authorization_code',
        ]
    ]);

    if (is_wp_error($response)) {
        wp_die('Error exchanging authorization code: ' . $response->get_error_message());
    }

    $token_data = json_decode(wp_remote_retrieve_body($response), true);

    if (isset($token_data['access_token'])) {
        update_option('pgc_google_access_token', $token_data['access_token']);
        update_option('pgc_google_token_expires_at', time() + $token_data['expires_in']);
    }

    // Only update refresh token if we receive a new one
    if (isset($token_data['refresh_token'])) {
        update_option('pgc_google_refresh_token', $token_data['refresh_token']);
        delete_option('pgc_google_token_invalid');
    } else {
        $existing_refresh = get_option('pgc_google_refresh_token');
        if (empty($existing_refresh)) {
            wp_die('Error: Refresh token not received on first authorization. Please ensure you:<br>1. Revoke access in your Google Account settings<br>2. Try authorizing again');
        }
    }
    
    delete_transient('pgc_google_oauth_state');
}

/**
 * Gets a valid access token, using refresh token if expired.
 */
function pgc_get_google_access_token() {
    if (get_option('pgc_google_token_invalid')) {
        return new WP_Error('token_invalid', 'Refresh token is invalid. Please re-authorize.');
    }

    $access_token = get_option('pgc_google_access_token');
    $expires_at = get_option('pgc_google_token_expires_at', 0);
    
    if (time() > ($expires_at - 60)) {
        $refresh_token = get_option('pgc_google_refresh_token');
        if (empty($refresh_token)) {
            return new WP_Error('no_refresh_token', 'Refresh token is missing. Please re-authorize.');
        }

        $client_id = get_option('pgc_google_client_id');
        $client_secret = get_option('pgc_google_client_secret');
        $token_url = 'https://oauth2.googleapis.com/token';

        $response = wp_remote_post($token_url, [
            'body' => [
                'client_id' => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token',
            ],
            'timeout' => 15,
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('token_refresh_network_error', 'Network error: ' . $response->get_error_message());
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $token_data = json_decode(wp_remote_retrieve_body($response), true);

        if ($http_code === 200 && isset($token_data['access_token'])) {
            $access_token = $token_data['access_token'];
            update_option('pgc_google_access_token', $access_token);
            update_option('pgc_google_token_expires_at', time() + $token_data['expires_in']);
            delete_option('pgc_google_token_invalid');
            return $access_token;
        } else {
            if (isset($token_data['error']) && $token_data['error'] === 'invalid_grant') {
                update_option('pgc_google_token_invalid', true);
                return new WP_Error('token_revoked', 'Refresh token revoked. Please re-authorize.');
            }
            return new WP_Error('token_refresh_failed', 'Failed to refresh token (HTTP ' . $http_code . ')');
        }
    }
    
    return $access_token;
}

/**
 * Clears token invalid flag before new OAuth flow.
 */
function pgc_clear_token_invalid_flag() {
    delete_option('pgc_google_token_invalid');
    delete_option('pgc_token_failure_email_sent');
}

/**
 * Sends email notification when authorization is needed.
 */
function pgc_send_token_failure_email($error_message) {
    $last_email_sent = get_option('pgc_token_failure_email_sent');
    if ($last_email_sent && (time() - $last_email_sent) < 86400) {
        return;
    }
    
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $auth_url = admin_url('admin.php?page=pgc-google-reviews');
    
    $subject = "[{$site_name}] Google Reviews: Re-Authorization Required";
    
    $message = "Hello,\n\n";
    $message .= "The Google Business Profile review sync on {$site_name} requires attention.\n\n";
    $message .= "Error: {$error_message}\n\n";
    $message .= "Please re-authorize:\n{$auth_url}\n\n";
    $message .= "Steps:\n";
    $message .= "1. Go to Google Reviews in admin\n";
    $message .= "2. Click 'Re-Authorize with Google'\n";
    $message .= "3. Grant permission when prompted\n";
    
    wp_mail($admin_email, $subject, $message);
    update_option('pgc_token_failure_email_sent', time());
}

// =============================================================================
// SECTION 4: FETCH & STORE REVIEWS
// =============================================================================

/**
 * Fetches all Google reviews using pagination.
 */
function pgc_fetch_google_reviews() {
    global $wpdb;
    $account_id = get_option('pgc_google_account_id');
    $location_id = get_option('pgc_google_location_id');

    if (empty($account_id) || empty($location_id)) {
        return 'Error: Account ID or Location ID not set.';
    }

    $access_token = pgc_get_google_access_token();
    if (is_wp_error($access_token)) {
        return $access_token->get_error_message();
    }

    $all_reviews = [];
    $page_token = null;
    $max_pages = 20;
    $page_count = 0;
    $existing_review_found = false;

    do {
        $api_url = "https://mybusiness.googleapis.com/v4/accounts/{$account_id}/locations/{$location_id}/reviews";
        
        $query_args = ['pageSize' => 50];
        if ($page_token) {
            $query_args['pageToken'] = $page_token;
        }
        $api_url = add_query_arg($query_args, $api_url);

        $response = wp_remote_get($api_url, [
            'headers' => ['Authorization' => 'Bearer ' . $access_token],
            'timeout' => 30
        ]);

        if (is_wp_error($response)) {
            return 'API request failed: ' . $response->get_error_message();
        }

        $http_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if ($http_code !== 200) {
            return "API Error (HTTP {$http_code}): " . print_r($data, true);
        }

        // Capture aggregate data on first page
        if ($page_count === 0 && isset($data['totalReviewCount'])) {
            if (is_numeric($data['averageRating'])) {
                update_option('pgc_reviews_average_rating', floatval($data['averageRating']));
            }
            if (is_numeric($data['totalReviewCount'])) {
                update_option('pgc_reviews_total_count', intval($data['totalReviewCount']));
            }
        }

        if (!empty($data['reviews'])) {
            foreach($data['reviews'] as $review) {
                $existing_review = $wpdb->get_var($wpdb->prepare(
                    "SELECT id FROM {$wpdb->prefix}pgc_reviews WHERE review_id = %s",
                    $review['reviewId']
                ));
                if ($existing_review) {
                    $existing_review_found = true;
                    break;
                }
                $all_reviews[] = $review;
            }
        }

        if ($existing_review_found) {
            break;
        }

        $page_token = $data['nextPageToken'] ?? null;
        $page_count++;

    } while ($page_token && $page_count < $max_pages);

    return $all_reviews;
}

/**
 * Inserts reviews into the database.
 */
function pgc_insert_reviews($reviews) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pgc_reviews';
    $inserted_count = 0;

    foreach ($reviews as $review) {
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM $table_name WHERE review_id = %s",
            $review['reviewId']
        ));
        if ($existing) {
            continue;
        }
        
        $result = $wpdb->insert(
            $table_name,
            [
                'review_id' => $review['reviewId'],
                'reviewer_display_name' => $review['reviewer']['displayName'] ?? '',
                'reviewer_profile_photo_url' => $review['reviewer']['profilePhotoUrl'] ?? '',
                'star_rating' => $review['starRating'] ?? 'ZERO',
                'comment' => $review['comment'] ?? '',
                'create_time' => gmdate('Y-m-d H:i:s', strtotime($review['createTime'])),
                'update_time' => gmdate('Y-m-d H:i:s', strtotime($review['updateTime'])),
                'reply_comment' => $review['reviewReply']['comment'] ?? null,
                'reply_update_time' => isset($review['reviewReply']) ? gmdate('Y-m-d H:i:s', strtotime($review['reviewReply']['updateTime'])) : null,
            ]
        );

        if ($result) {
            $inserted_count++;
        }
    }
    return $inserted_count;
}

// =============================================================================
// SECTION 5: CRON JOB
// =============================================================================

/**
 * Sets up the daily cron job.
 */
function pgc_setup_review_cron() {
    if (!wp_next_scheduled('pgc_daily_review_fetch_event')) {
        $time = strtotime('02:00:00');
        if ($time < time()) {
            $time = strtotime('+1 day', $time);
        }
        wp_schedule_event($time, 'daily', 'pgc_daily_review_fetch_event');
    }
}
add_action('init', 'pgc_setup_review_cron');

/**
 * Main cron job function.
 */
function pgc_fetch_reviews_cron($is_manual_run = false) {
    $start_time = microtime(true);
    $log = [];
    $log[] = 'Starting review sync at ' . date('Y-m-d H:i:s') . ' (UTC).';
    
    $max_retries = 3;
    $retry_delay = 30;
    $success = false;
    $google_result = null;
    
    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
        if ($attempt > 1) {
            $log[] = "Retry attempt {$attempt}...";
            sleep($retry_delay);
        }
        
        $google_result = pgc_fetch_google_reviews();
        
        if (is_array($google_result)) {
            $success = true;
            break;
        } else {
            if (strpos($google_result, 'token_revoked') !== false ||
                strpos($google_result, 'token_invalid') !== false ||
                strpos($google_result, 'invalid_grant') !== false) {
                $log[] = "Auth failure: " . $google_result;
                pgc_send_token_failure_email($google_result);
                break;
            }
            $log[] = "Attempt {$attempt} failed: " . $google_result;
        }
    }

    if ($success) {
        $inserted = pgc_insert_reviews($google_result);
        $log[] = "Success: Fetched " . count($google_result) . " reviews, inserted {$inserted} new.";
        update_option('pgc_reviews_last_run', time());
    }

    $duration = round(microtime(true) - $start_time, 4);
    $log[] = "Finished in {$duration} seconds.";
    
    update_option('pgc_review_cron_last_log', implode("\n", $log));
}
add_action('pgc_daily_review_fetch_event', 'pgc_fetch_reviews_cron');

// =============================================================================
// SECTION 6: ADMIN PAGE HTML
// =============================================================================

function pgc_google_reviews_page_html() {
    if (!current_user_can('manage_options')) return;

    // Handle OAuth callback
    if (isset($_GET['code']) && isset($_GET['state']) && get_transient('pgc_google_oauth_state') === $_GET['state']) {
        pgc_clear_token_invalid_flag();
        pgc_handle_google_oauth_callback($_GET['code']);
        wp_redirect(admin_url('admin.php?page=pgc-google-reviews'));
        exit;
    }

    // Handle manual sync
    if (isset($_POST['pgc_run_review_sync']) && check_admin_referer('pgc_run_review_sync_nonce')) {
        pgc_fetch_reviews_cron(true);
        echo '<div class="notice notice-success"><p>Review sync completed! Check the log below.</p></div>';
    }

    $client_id = get_option('pgc_google_client_id');
    $client_secret = get_option('pgc_google_client_secret');
    $account_id = get_option('pgc_google_account_id');
    $location_id = get_option('pgc_google_location_id');
    $refresh_token = get_option('pgc_google_refresh_token');
    
    global $wpdb;
    $total_reviews = $wpdb->get_var("SELECT COUNT(id) FROM {$wpdb->prefix}pgc_reviews");
    $next_scheduled = wp_next_scheduled('pgc_daily_review_fetch_event');
    $last_run = get_option('pgc_reviews_last_run');
    $last_log = get_option('pgc_review_cron_last_log');
    $avg_rating = get_option('pgc_reviews_average_rating', 0);
    $total_count = get_option('pgc_reviews_total_count', 0);
    ?>
    <div class="wrap">
        <h1><?php _e('Google Reviews Management', 'progreenclean'); ?></h1>
        <p>Sync your Google Business Profile reviews to your website.</p>

        <?php if ($avg_rating && $total_count) : ?>
        <div class="card" style="margin-bottom: 20px; padding: 20px; background: #f0f9ff; border-left: 4px solid #0891b2;">
            <h2 style="margin-top: 0;">Current Rating</h2>
            <p style="font-size: 24px; margin: 10px 0;">
                <strong><?php echo number_format($avg_rating, 1); ?></strong> / 5 stars 
                <span style="color: #666; font-size: 16px;">(<?php echo $total_count; ?> reviews)</span>
            </p>
        </div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('pgc_google_reviews_settings'); ?>
            
            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px;">
                <h2>Step 1: API Credentials</h2>
                <p>Enter your Google Cloud OAuth2 credentials. <a href="https://console.cloud.google.com/" target="_blank">Get credentials here</a></p>
                <table class="form-table">
                    <tr>
                        <th><label for="pgc_google_client_id">Client ID</label></th>
                        <td><input name="pgc_google_client_id" type="text" id="pgc_google_client_id" value="<?php echo esc_attr($client_id); ?>" class="large-text"></td>
                    </tr>
                    <tr>
                        <th><label for="pgc_google_client_secret">Client Secret</label></th>
                        <td><input name="pgc_google_client_secret" type="password" id="pgc_google_client_secret" value="<?php echo esc_attr($client_secret); ?>" class="large-text" placeholder="Value saved but hidden for security"></td>
                    </tr>
                </table>
                <?php submit_button('Save Credentials'); ?>
            </div>
        </form>

        <?php if ($client_id && $client_secret) : ?>
        <div class="card" style="margin-top: 20px; padding: 1px 20px 20px;">
            <h2>Step 2: Authorize</h2>
            <?php if ($refresh_token) : ?>
                <p style="color:green; font-weight:bold;">&#x2705; Authorized</p>
            <?php else: ?>
                <p>Click to grant permission to access your Google Business Profile.</p>
            <?php endif; ?>
            <p><a href="<?php echo esc_url(pgc_get_google_auth_url()); ?>" class="button button-primary">
                <?php echo $refresh_token ? 'Re-Authorize with Google' : 'Authorize with Google'; ?>
            </a></p>
        </div>
        <?php endif; ?>

        <?php if ($refresh_token) : ?>
        <form method="post" action="options.php">
            <?php settings_fields('pgc_google_reviews_settings'); ?>
            <div class="card" style="margin-top: 20px; padding: 1px 20px 20px;">
                <h2>Step 3: Location IDs</h2>
                <p>Enter your Google Business Profile Account ID and Location ID.</p>
                <table class="form-table">
                    <tr>
                        <th><label for="pgc_google_account_id">Account ID</label></th>
                        <td><input name="pgc_google_account_id" type="text" id="pgc_google_account_id" value="<?php echo esc_attr($account_id); ?>" class="regular-text" placeholder="accounts/123456789"></td>
                    </tr>
                    <tr>
                        <th><label for="pgc_google_location_id">Location ID</label></th>
                        <td><input name="pgc_google_location_id" type="text" id="pgc_google_location_id" value="<?php echo esc_attr($location_id); ?>" class="regular-text" placeholder="locations/987654321"></td>
                    </tr>
                </table>
                <?php submit_button('Save Location IDs'); ?>
            </div>
        </form>
        <?php endif; ?>

        <?php if ($account_id && $location_id) : ?>
        <div class="card" style="margin-top: 20px; padding: 1px 20px 20px;">
            <h2>Sync Status</h2>
            <table class="form-table">
                <tr>
                    <th>Reviews in Database</th>
                    <td><strong><?php echo esc_html($total_reviews ?? '0'); ?></strong></td>
                </tr>
                <tr>
                    <th>Next Scheduled Sync</th>
                    <td><?php echo $next_scheduled ? date('Y-m-d H:i:s', $next_scheduled) . ' (UTC)' : 'Not scheduled'; ?></td>
                </tr>
                <tr>
                    <th>Last Successful Sync</th>
                    <td><?php echo $last_run ? date('Y-m-d H:i:s', $last_run) . ' (UTC)' : 'Never'; ?></td>
                </tr>
            </table>
            
            <form method="post">
                <?php wp_nonce_field('pgc_run_review_sync_nonce'); ?>
                <?php submit_button('Sync Reviews Now', 'secondary', 'pgc_run_review_sync'); ?>
            </form>
            
            <?php if ($last_log) : ?>
            <h3>Last Sync Log</h3>
            <pre style="background:#f0f0f1; padding: 10px; border-radius: 4px; white-space: pre-wrap; max-height: 300px; overflow-y: auto;"><?php echo esc_html($last_log); ?></pre>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// =============================================================================
// SECTION 7: DISPLAY SHORTCODES
// =============================================================================

/**
 * Shortcode: [pgc_google_reviews count="6"]
 */
add_shortcode('pgc_google_reviews', function($atts) {
    global $wpdb;
    
    $atts = shortcode_atts(['count' => 6], $atts);
    $count = intval($atts['count']);
    
    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM {$wpdb->prefix}pgc_reviews ORDER BY create_time DESC LIMIT %d",
        $count
    ));
    
    if (empty($reviews)) {
        return '<p>No reviews yet.</p>';
    }
    
    $output = '<div class="pgc-reviews-container">';
    foreach ($reviews as $review) {
        $output .= pgc_render_review_card($review);
    }
    $output .= '</div>';
    
    return $output;
});

/**
 * Shortcode: [pgc_google_reviews_all]
 */
add_shortcode('pgc_google_reviews_all', function() {
    global $wpdb;
    
    $reviews = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}pgc_reviews ORDER BY create_time DESC");
    
    if (empty($reviews)) {
        return '<p>No reviews have been imported yet.</p>';
    }
    
    $avg_rating = get_option('pgc_reviews_average_rating', 0);
    $total_count = get_option('pgc_reviews_total_count', 0);
    
    $output = '';
    if ($avg_rating && $total_count) {
        $output .= '<div class="pgc-reviews-summary" style="text-align: center; margin-bottom: 30px;">';
        $output .= '<h2 style="font-size: 2rem; margin-bottom: 10px;">' . number_format($avg_rating, 1) . ' / 5</h2>';
        $output .= '<p>Based on ' . $total_count . ' Google reviews</p>';
        $output .= '</div>';
    }
    
    $output .= '<div class="pgc-reviews-container">';
    foreach ($reviews as $review) {
        $output .= pgc_render_review_card($review, true);
    }
    $output .= '</div>';
    
    return $output;
});

/**
 * Helper: Render a single review card
 */
function pgc_render_review_card($review, $show_reply = false) {
    $star_map = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];
    $rating = $star_map[$review->star_rating] ?? 0;
    
    $stars = str_repeat('&#9733;', $rating) . str_repeat('&#9734;', 5 - $rating);
    
    $output = '<div class="pgc-review-card">';
    $output .= '<div class="pgc-review-card__header">';
    
    if (!empty($review->reviewer_profile_photo_url)) {
        $output .= '<img class="pgc-review-card__avatar" src="' . esc_url($review->reviewer_profile_photo_url) . '" alt="' . esc_attr($review->reviewer_display_name) . '">';
    }
    
    $output .= '<div class="pgc-review-card__info">';
    $output .= '<div class="pgc-review-card__name">' . esc_html($review->reviewer_display_name) . '</div>';
    $output .= '<div class="pgc-review-card__stars">' . $stars . '</div>';
    $output .= '</div>';
    $output .= '</div>';
    
    if (!empty($review->comment)) {
        $output .= '<p class="pgc-review-card__comment">' . nl2br(esc_html($review->comment)) . '</p>';
    }
    
    $output .= '<div class="pgc-review-card__date">' . date('F j, Y', strtotime($review->create_time)) . '</div>';
    
    if ($show_reply && !empty($review->reply_comment)) {
        $output .= '<div class="pgc-review-card__reply">';
        $output .= '<p class="pgc-review-card__reply-label">Response from ProGreenClean</p>';
        $output .= '<p class="pgc-review-card__reply-text">' . nl2br(esc_html($review->reply_comment)) . '</p>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
}
