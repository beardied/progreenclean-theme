<?php
$preselected_service = $attributes['service'] ?? '';
?>
<div id="pgc-quote-wizard" class="pgc-quote-wizard" data-preselected-service="<?php echo esc_attr($preselected_service); ?>">
    <!-- Step 1: Service Selection -->
    <div class="pgc-quote-step pgc-quote-step--active" data-step="1">
        <h2 class="pgc-quote-step__title">What service do you need?</h2>
        <p class="pgc-quote-step__description">Select the cleaning service you're interested in</p>
        
        <div class="pgc-quote-services">
            <?php
            $services = [
                ['slug' => 'domestic-cleaning', 'name' => 'Domestic Cleaning', 'icon' => '🏠'],
                ['slug' => 'window-cleaning', 'name' => 'Window Cleaning', 'icon' => '🪟'],
                ['slug' => 'gutter-cleaning', 'name' => 'Gutter Cleaning', 'icon' => '🌧️'],
                ['slug' => 'end-of-tenancy', 'name' => 'End of Tenancy', 'icon' => '🔑'],
                ['slug' => 'carpet-cleaning', 'name' => 'Carpet Cleaning', 'icon' => '🧹'],
                ['slug' => 'oven-cleaning', 'name' => 'Oven Cleaning', 'icon' => '🔥'],
                ['slug' => 'pressure-washing', 'name' => 'Pressure Washing', 'icon' => '💦'],
                ['slug' => 'conservatory-cleaning', 'name' => 'Conservatory', 'icon' => '🏡'],
                ['slug' => 'solar-panel-cleaning', 'name' => 'Solar Panels', 'icon' => '☀️'],
                ['slug' => 'graffiti-removal', 'name' => 'Graffiti Removal', 'icon' => '🎨'],
                ['slug' => 'commercial-cleaning', 'name' => 'Commercial', 'icon' => '🏢'],
                ['slug' => 'post-construction', 'name' => 'Post-Construction', 'icon' => '🏗️'],
            ];
            foreach ($services as $service):
            ?>
                <button type="button" class="pgc-quote-service" data-service="<?php echo esc_attr($service['slug']); ?>">
                    <span class="pgc-quote-service__icon"><?php echo esc_html($service['icon']); ?></span>
                    <span class="pgc-quote-service__name"><?php echo esc_html($service['name']); ?></span>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Step 2: Service Questions (Dynamic) -->
    <div class="pgc-quote-step" data-step="2" style="display:none;">
        <button type="button" class="pgc-quote-back">&larr; Back</button>
        <h2 class="pgc-quote-step__title" id="quote-step-2-title">Service Details</h2>
        <div id="quote-questions-container" class="pgc-quote-questions">
            <!-- Questions injected via JavaScript -->
        </div>
        <button type="button" class="pgc-button pgc-button--primary pgc-quote-next">Continue</button>
    </div>
    
    <!-- Step 3: Contact Details -->
    <div class="pgc-quote-step" data-step="3" style="display:none;">
        <button type="button" class="pgc-quote-back">&larr; Back</button>
        <h2 class="pgc-quote-step__title">Your Details</h2>
        
        <div class="pgc-quote-form">
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
                    <label for="quote-email">Email Address *</label>
                    <input type="email" id="quote-email" name="email" required>
                </div>
                <div class="pgc-form-field">
                    <label for="quote-phone">Phone Number *</label>
                    <input type="tel" id="quote-phone" name="phone" required>
                </div>
            </div>
            <div class="pgc-form-field">
                <label for="quote-address">Address Line 1 *</label>
                <input type="text" id="quote-address" name="address_line_1" required>
            </div>
            <div class="pgc-form-field">
                <label for="quote-address-2">Address Line 2</label>
                <input type="text" id="quote-address-2" name="address_line_2">
            </div>
            <div class="pgc-form-field">
                <label for="quote-postcode">Postcode *</label>
                <input type="text" id="quote-postcode" name="postcode" required>
            </div>
            <div class="pgc-form-field">
                <label for="quote-hear-about">How did you hear about us?</label>
                <select id="quote-hear-about" name="hear_about">
                    <option value="">Please select...</option>
                    <option value="google">Google Search</option>
                    <option value="facebook">Facebook</option>
                    <option value="recommendation">Friend/Family Recommendation</option>
                    <option value="local">Local Advert</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="pgc-form-field">
                <label for="quote-additional">Additional Information</label>
                <textarea id="quote-additional" name="additional_info" rows="3"></textarea>
            </div>
        </div>
        
        <div class="pgc-quote-summary" id="quote-summary">
            <!-- Summary injected via JavaScript -->
        </div>
        
        <button type="submit" class="pgc-button pgc-button--primary pgc-button--large pgc-quote-submit">Get My Quote</button>
    </div>
    
    <!-- Step 4: Confirmation -->
    <div class="pgc-quote-step" data-step="4" style="display:none;">
        <div class="pgc-quote-success">
            <div class="pgc-quote-success__icon">✓</div>
            <h2 class="pgc-quote-success__title">Thank You!</h2>
            <p class="pgc-quote-success__message">Your quote request has been received. We'll be in touch shortly.</p>
            <div class="pgc-quote-success__reference">
                Quote Reference: <span id="quote-reference"></span>
            </div>
        </div>
    </div>
</div>
