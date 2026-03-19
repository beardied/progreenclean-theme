<?php
/**
 * Template: Get a Quote - Quote Wizard
 */

if (!defined('ABSPATH')) exit;

get_header();
?>

<!-- Compact Header -->
<section style="padding: 120px 0 30px; background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border-bottom: 1px solid rgba(8, 145, 178, 0.1);">
    <div class="pgc-container" style="text-align: center;">
        <h1 style="font-size: 2rem; font-weight: 800; color: var(--pgc-gray-900); margin: 0 0 8px 0;">Get a Quote</h1>
        <p style="color: var(--pgc-gray-500); margin: 0; font-size: 1rem;">Get an instant estimate for our cleaning services</p>
    </div>
</section>

<section class="pgc-section" style="padding: 40px 0;">
    <div class="pgc-container">
        <div class="pgc-card pgc-card--glass" style="max-width: 800px; margin: 0 auto;">
            <!-- Progress Bar -->
            <div class="quote-progress" style="margin-bottom: 40px;">
                <div class="quote-progress-bar" style="height: 4px; background: var(--pgc-gray-100); border-radius: 2px; overflow: hidden;">
                    <div class="quote-progress-fill" style="height: 100%; background: linear-gradient(90deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); width: 0%; transition: width 0.3s ease;"></div>
                </div>
            </div>
            
            <!-- Step 1: Service Selection -->
            <div id="quote-service-selection">
                <h2 style="text-align: center; margin-bottom: 8px; font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900);">Select a Service</h2>
                <p style="text-align: center; color: var(--pgc-gray-500); margin-bottom: 32px;">Choose the cleaning service you need</p>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div class="quote-service-card" data-service="window-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🪟</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Window<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="gutter-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🏠</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Gutter<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="domestic-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🏡</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Domestic<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="end-of-tenancy" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">📦</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">End of<br>Tenancy</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="oven-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🔥</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Oven<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="carpet-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🧹</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Carpet<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="pressure-washing" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">💧</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Pressure<br>Washing</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="one-off-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">✨</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">One-off<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="commercial-window" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🏢</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Commercial<br>Windows</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="office-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🖥️</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Office<br>Cleaning</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="gardening" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🌳</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Gardening<br>Services</div>
                    </div>
                    
                    <div class="quote-service-card" data-service="builders-cleaning" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 20px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.3s ease;">
                        <div class="quote-service-icon" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 28px;">🚧</div>
                        <div style="font-weight: 700; font-size: 15px; color: var(--pgc-gray-900);">Post<br>Construction</div>
                    </div>
                </div>
            </div>
            
            <!-- Step 2: Questions -->
            <div id="quote-questions-container" style="display: none;">
                <!-- Questions rendered by JS -->
            </div>
            
            <!-- Step 3: Quote Summary with Upsells -->
            <div id="quote-summary" style="display: none;">
                <!-- Summary rendered by JS -->
            </div>
            
            <!-- Step 4: Contact Form -->
            <div id="quote-contact-form" style="display: none;">
                <h2 style="text-align: center; margin-bottom: 8px; font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900);">Your Details</h2>
                <p style="text-align: center; color: var(--pgc-gray-500); margin-bottom: 32px;">Fill in your details to complete your quote request</p>
                
                <form id="quote-contact-form-el">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">First Name *</label>
                            <input type="text" id="first_name" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Last Name *</label>
                            <input type="text" id="last_name" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Email Address *</label>
                        <input type="email" id="email" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Phone Number *</label>
                        <input type="tel" id="phone" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Address Line 1 *</label>
                        <input type="text" id="address_line1" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Address Line 2</label>
                        <input type="text" id="address_line2" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Postcode *</label>
                        <input type="text" id="postcode" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">How did you hear about us?</label>
                        <select id="heard_from" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; background: #fff;">
                            <option value="">Please select...</option>
                            <option value="google">Google</option>
                            <option value="facebook">Facebook</option>
                            <option value="instagram">Instagram</option>
                            <option value="recommendation">Recommendation</option>
                            <option value="leaflet">Leaflet</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: 14px; font-weight: 500; color: var(--pgc-gray-700); margin-bottom: 6px;">Additional Notes</label>
                        <textarea id="notes" rows="3" style="width: 100%; padding: 14px 16px; border: 2px solid var(--pgc-gray-200); border-radius: 10px; font-size: 15px; transition: all 0.2s; resize: vertical; background: #fff;"></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 16px; justify-content: center; align-items: center;">
                        <button type="button" id="question-back" class="pgc-btn pgc-btn-outline" style="padding: 14px 32px; font-size: 15px;">← Back</button>
                        <button type="button" id="quote-submit" class="pgc-btn pgc-btn-primary" style="padding: 16px 48px; font-size: 16px;">Submit Quote Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<style>
.quote-service-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px -8px rgba(8, 145, 178, 0.15);
}
.quote-service-card.selected {
    border-color: var(--pgc-primary);
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
}
.question-tile:hover {
    border-color: var(--pgc-primary);
    background: rgba(8, 145, 178, 0.05);
}
.question-tile.selected {
    border-color: var(--pgc-primary);
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
}
.upsell-toggle:hover {
    border-color: var(--pgc-primary);
    background: rgba(8, 145, 178, 0.05);
}
.upsell-toggle.selected {
    border-color: var(--pgc-primary);
    background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%);
}
input:focus, select:focus, textarea:focus {
    outline: none;
    border-color: var(--pgc-primary) !important;
}

@media (max-width: 768px) {
    #quote-service-selection > div {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    .quote-service-card {
        padding: 24px 12px !important;
    }
    .quote-service-icon {
        width: 48px !important;
        height: 48px !important;
        font-size: 22px !important;
    }
}
</style>

<?php get_footer(); ?>
