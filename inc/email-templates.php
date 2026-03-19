<?php
/**
 * Email Template Functions
 *
 * @package ProGreenClean
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Send Quote Emails
 */
function pgc_send_quote_emails(array $data, string $quote_id): void {
    // Send to customer
    pgc_send_customer_quote_email($data, $quote_id);
    
    // Send to admin
    pgc_send_admin_quote_email($data, $quote_id);
}

/**
 * Send Customer Quote Confirmation Email
 */
function pgc_send_customer_quote_email(array $data, string $quote_id): void {
    $to = $data['email'];
    $subject = 'Your ProGreenClean Quote Request - ' . $quote_id;
    
    $service_name = str_replace('-', ' ', ucwords($data['service']));
    $price = number_format(floatval($data['calculated_price']), 2);
    
    $message = pgc_get_email_template('customer-quote', [
        'customer_name' => $data['first_name'],
        'quote_id' => $quote_id,
        'service' => $service_name,
        'price' => $price,
        'phone' => get_option('pgc_phone', ''),
    ]);
    
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ProGreenClean <info@progreenclean.co.uk>',
    ];
    
    wp_mail($to, $subject, $message, $headers);
}

/**
 * Send Admin Quote Notification Email
 */
function pgc_send_admin_quote_email(array $data, string $quote_id): void {
    $to = get_option('admin_email');
    $subject = 'New Quote Request - ' . $quote_id;
    
    $service_name = str_replace('-', ' ', ucwords($data['service']));
    $price = number_format(floatval($data['calculated_price']), 2);
    
    $answers_html = '';
    if (!empty($data['quote_answers'])) {
        $answers_html = '<h3>Quote Details:</h3><ul>';
        foreach ($data['quote_answers'] as $key => $value) {
            $label = str_replace('_', ' ', ucwords($key));
            $answers_html .= '<li><strong>' . esc_html($label) . ':</strong> ' . esc_html(is_array($value) ? implode(', ', $value) : $value) . '</li>';
        }
        $answers_html .= '</ul>';
    }
    
    $message = pgc_get_email_template('admin-quote', [
        'quote_id' => $quote_id,
        'service' => $service_name,
        'price' => $price,
        'customer_name' => $data['first_name'] . ' ' . $data['last_name'],
        'customer_email' => $data['email'],
        'customer_phone' => $data['phone'],
        'customer_address' => ($data['address_line_1'] ?? '') . ', ' . ($data['postcode'] ?? ''),
        'additional_info' => $data['additional_info'] ?? '',
        'answers_html' => $answers_html,
    ]);
    
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: ProGreenClean Website <info@progreenclean.co.uk>',
    ];
    
    wp_mail($to, $subject, $message, $headers);
}

/**
 * Get Email Template
 */
function pgc_get_email_template(string $template, array $vars = []): string {
    $templates = [
        'customer-quote' => pgc_get_customer_quote_template(),
        'admin-quote' => pgc_get_admin_quote_template(),
    ];
    
    $template_content = $templates[$template] ?? '';
    
    // Replace variables
    foreach ($vars as $key => $value) {
        $template_content = str_replace('{{' . $key . '}}', $value, $template_content);
    }
    
    return $template_content;
}

/**
 * Customer Quote Email Template
 */
function pgc_get_customer_quote_template(): string {
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your ProGreenClean Quote</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1a1a1a; margin: 0; padding: 0; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #2E7D32 0%, #1B5E20 100%); padding: 30px; text-align: center; }
        .header img { max-height: 50px; margin-bottom: 15px; }
        .header h1 { color: #ffffff; margin: 0; font-size: 24px; }
        .content { padding: 40px 30px; }
        .quote-box { background: #E8F5E9; border-left: 4px solid #2E7D32; padding: 20px; margin: 20px 0; border-radius: 4px; }
        .quote-id { font-size: 14px; color: #666; margin-bottom: 10px; }
        .price { font-size: 32px; font-weight: bold; color: #2E7D32; margin: 10px 0; }
        .service { font-size: 18px; color: #1a1a1a; margin-bottom: 5px; }
        .button { display: inline-block; background: #2E7D32; color: #ffffff; padding: 15px 30px; text-decoration: none; border-radius: 4px; margin: 20px 0; }
        .footer { background: #1a1a1a; color: #ffffff; padding: 30px; text-align: center; font-size: 14px; }
        .footer a { color: #81C784; }
        .social { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Quote Request</h1>
        </div>
        <div class="content">
            <p>Hi {{customer_name}},</p>
            <p>Thank you for requesting a quote from ProGreenClean. We have received your enquiry and will be in touch shortly to confirm your booking.</p>
            
            <div class="quote-box">
                <div class="quote-id">Quote Reference: {{quote_id}}</div>
                <div class="service">{{service}}</div>
                <div class="price">£{{price}}</div>
                <p style="margin:0; color:#666;">Estimated price based on your requirements</p>
            </div>
            
            <p>Please note this is an estimated price. Final pricing may vary based on site assessment.</p>
            
            <p>If you have any questions, please do not hesitate to contact us:</p>
            <p><strong>Phone:</strong> {{phone}}<br>
            <strong>Email:</strong> info@progreenclean.co.uk</p>
            
            <center>
                <a href="tel:{{phone}}" class="button">Call Us Now</a>
            </center>
        </div>
        <div class="footer">
            <p><strong>ProGreenClean</strong><br>
            Professional Eco-Friendly Cleaning Services<br>
            Epsom, Surrey</p>
            <p><a href="https://progreenclean.co.uk">www.progreenclean.co.uk</a></p>
        </div>
    </div>
</body>
</html>';
}

/**
 * Admin Quote Email Template
 */
function pgc_get_admin_quote_template(): string {
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Quote Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #1a1a1a; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #2E7D32; color: #ffffff; padding: 20px 30px; }
        .header h1 { margin: 0; font-size: 20px; }
        .content { padding: 30px; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #2E7D32; border-bottom: 2px solid #E8F5E9; padding-bottom: 10px; margin-bottom: 15px; }
        .info-row { display: flex; margin-bottom: 10px; }
        .info-label { width: 150px; font-weight: bold; color: #666; }
        .info-value { flex: 1; }
        .price-highlight { font-size: 24px; font-weight: bold; color: #2E7D32; background: #E8F5E9; padding: 15px; border-radius: 4px; text-align: center; }
        .button { display: inline-block; background: #2E7D32; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Quote Request: {{quote_id}}</h1>
        </div>
        <div class="content">
            <div class="price-highlight">
                £{{price}} - {{service}}
            </div>
            
            <div class="section">
                <h3>Customer Information</h3>
                <div class="info-row">
                    <div class="info-label">Name:</div>
                    <div class="info-value">{{customer_name}}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><a href="mailto:{{customer_email}}">{{customer_email}}</a></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Phone:</div>
                    <div class="info-value"><a href="tel:{{customer_phone}}">{{customer_phone}}</a></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Address:</div>
                    <div class="info-value">{{customer_address}}</div>
                </div>
            </div>
            
            {{answers_html}}
            
            <div class="section">
                <h3>Additional Information</h3>
                <p>{{additional_info}}</p>
            </div>
            
            <center>
                <a href="mailto:{{customer_email}}" class="button">Reply to Customer</a>
            </center>
        </div>
    </div>
</body>
</html>';
}
