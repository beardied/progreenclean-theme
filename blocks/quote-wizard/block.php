<?php
/**
 * Quote Wizard Block
 *
 * @package ProGreenClean
 */

if (!defined('ABSPATH')) {
    exit;
}

register_block_type('progreenclean/quote-wizard', [
    'render_callback' => 'pgc_render_quote_wizard_block',
    'attributes' => [
        'showServiceSelection' => ['type' => 'boolean', 'default' => true],
    ],
]);

function pgc_render_quote_wizard_block(array $attributes): string {
    $services = [
        'domestic-cleaning' => 'Domestic Cleaning',
        'window-cleaning' => 'Window Cleaning',
        'gutter-cleaning' => 'Gutter Cleaning',
        'end-of-tenancy' => 'End of Tenancy Cleaning',
        'carpet-cleaning' => 'Carpet Cleaning',
        'oven-cleaning' => 'Oven Cleaning',
        'conservatory-cleaning' => 'Conservatory Cleaning',
        'pressure-washing' => 'Pressure Washing',
        'solar-panel-cleaning' => 'Solar Panel Cleaning',
        'post-construction' => 'Post Construction Cleaning',
        'commercial-cleaning' => 'Commercial Cleaning',
        'graffiti-removal' => 'Graffiti Removal',
    ];
    
    ob_start();
    ?>
    <div class="pgc-quote-wizard" id="pgc-quote-wizard">
        <!-- Step 1: Service Selection -->
        <div class="pgc-quote-step pgc-quote-step--active" data-step="1">
            <h2 class="pgc-quote-step__title">What service do you need?</h2>
            <p class="pgc-quote-step__description">Select the cleaning service you're interested in.</p>
            <div class="pgc-quote-services">
                <?php foreach ($services as $slug => $name) : 
                    $requires_manual = in_array($slug, ['pressure-washing', 'solar-panel-cleaning', 'post-construction', 'commercial-cleaning', 'graffiti-removal']);
                ?>
                    <button type="button" class="pgc-quote-service" data-service="<?php echo esc_attr($slug); ?>" data-manual="<?php echo $requires_manual ? '1' : '0'; ?>">
                        <span class="pgc-quote-service__name"><?php echo esc_html($name); ?></span>
                        <?php if ($requires_manual) : ?>
                            <span class="pgc-quote-service__note">Requires assessment</span>
                        <?php endif; ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Step 2: Service-specific questions (dynamic) -->
        <div class="pgc-quote-step" data-step="2">
            <button type="button" class="pgc-quote-back">&larr; Back</button>
            <h2 class="pgc-quote-step__title" id="step2-title">Tell us more</h2>
            <div class="pgc-quote-questions" id="step2-content">
                <!-- Dynamic content inserted by JS -->
            </div>
        </div>
        
        <!-- Step 3: Price Display -->
        <div class="pgc-quote-step" data-step="3">
            <button type="button" class="pgc-quote-back">&larr; Back</button>
            <h2 class="pgc-quote-step__title">Your Estimated Quote</h2>
            <div class="pgc-quote-price-display">
                <div class="pgc-quote-price" id="quote-price">£0.00</div>
                <p class="pgc-quote-price-note">This is an estimated price based on the information provided.</p>
            </div>
            <button type="button" class="pgc-btn pgc-btn-primary pgc-quote-continue">Continue to Booking</button>
        </div>
        
        <!-- Step 4: Contact Details -->
        <div class="pgc-quote-step" data-step="4">
            <button type="button" class="pgc-quote-back">&larr; Back</button>
            <h2 class="pgc-quote-step__title">Your Details</h2>
            <form class="pgc-quote-form" id="pgc-quote-form">
                <div class="pgc-form-row">
                    <div class="pgc-form-field">
                        <label for="quote-first-name">First Name *</label>
                        <input type="text" id="quote-first-name" name="first_name" required>
                    </div>
                    <div class="pgc-form-field">
                        <label for="quote-last-name">Last Name *</label>
                        <input type="text" id="quote-last-name" name="last_name" required>
                    </div>
                </div>
                <div class="pgc-form-row">
                    <div class="pgc-form-field">
                        <label for="quote-phone">Phone Number *</label>
                        <input type="tel" id="quote-phone" name="phone" required>
                    </div>
                    <div class="pgc-form-field">
                        <label for="quote-email">Email Address *</label>
                        <input type="email" id="quote-email" name="email" required>
                    </div>
                </div>
                <div class="pgc-form-field">
                    <label for="quote-address1">Address Line 1 *</label>
                    <input type="text" id="quote-address1" name="address_line1" required>
                </div>
                <div class="pgc-form-field">
                    <label for="quote-address2">Address Line 2</label>
                    <input type="text" id="quote-address2" name="address_line2">
                </div>
                <div class="pgc-form-field">
                    <label for="quote-postcode">Postcode *</label>
                    <input type="text" id="quote-postcode" name="postcode" required>
                </div>
                <div class="pgc-form-field">
                    <label for="quote-hear">Where did you hear about us?</label>
                    <select id="quote-hear" name="hear_about">
                        <option value="">Please select...</option>
                        <option value="google">Google Search</option>
                        <option value="social">Social Media</option>
                        <option value="friend">Friend/Family</option>
                        <option value="local">Local Advert</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="pgc-form-field">
                    <label for="quote-notes">Additional Information</label>
                    <textarea id="quote-notes" name="notes" rows="3"></textarea>
                </div>
                <button type="submit" class="pgc-btn pgc-btn-primary">Submit Quote Request</button>
            </form>
        </div>
        
        <!-- Step 5: Manual Quote / Contact Form -->
        <div class="pgc-quote-step" data-step="manual">
            <button type="button" class="pgc-quote-back">&larr; Back</button>
            <h2 class="pgc-quote-step__title">Request a Quote</h2>
            <p>This service requires a manual assessment. Please provide your details and we'll get back to you shortly.</p>
            <form class="pgc-quote-form" id="pgc-manual-quote-form">
                <input type="hidden" name="service_type" id="manual-service-type">
                <div class="pgc-form-row">
                    <div class="pgc-form-field">
                        <label>First Name *</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="pgc-form-field">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" required>
                    </div>
                </div>
                <div class="pgc-form-row">
                    <div class="pgc-form-field">
                        <label>Phone *</label>
                        <input type="tel" name="phone" required>
                    </div>
                    <div class="pgc-form-field">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                </div>
                <div class="pgc-form-field">
                    <label>Address *</label>
                    <input type="text" name="address_line1" required>
                </div>
                <div class="pgc-form-field">
                    <label>Postcode *</label>
                    <input type="text" name="postcode" required>
                </div>
                <div class="pgc-form-field">
                    <label>Upload Images (if applicable)</label>
                    <input type="file" name="images[]" multiple accept="image/*">
                </div>
                <div class="pgc-form-field">
                    <label>Additional Details</label>
                    <textarea name="notes" rows="4" placeholder="Please describe what you need..."></textarea>
                </div>
                <button type="submit" class="pgc-btn pgc-btn-primary">Request Quote</button>
            </form>
        </div>
        
        <!-- Success Message -->
        <div class="pgc-quote-step" data-step="success">
            <div class="pgc-quote-success">
                <div class="pgc-quote-success__icon">&#10004;</div>
                <h2 class="pgc-quote-success__title">Thank You!</h2>
                <p class="pgc-quote-success__message">Your quote request has been submitted. We'll be in touch shortly.</p>
                <p class="pgc-quote-success__ref">Quote Reference: <span id="quote-reference"></span></p>
                <a href="/" class="pgc-btn pgc-btn-primary">Return Home</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
