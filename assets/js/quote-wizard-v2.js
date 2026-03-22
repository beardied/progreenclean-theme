/**
 * ProGreenClean Quote Wizard V2 - Based on JSON Flow
 * Services: Window, Gutter, Domestic, End of Tenancy, Post Construction, 
 * Commercial, Carpet, Oven, Pressure Washing, Solar Panel, Graffiti Removal
 */
(function($) {
    'use strict';
    
    let currentStep = 'service_selection';
    let answers = {};
    let calculatedPrice = 0;
    let priceBreakdown = [];
    let stepHistory = [];
    
    // Service display names and icons
    const serviceInfo = {
        'window-cleaning': { label: 'Window Cleaning', icon: '🪟' },
        'gutter-cleaning': { label: 'Gutter Cleaning', icon: '🏠' },
        'domestic-cleaning': { label: 'Domestic Cleaning', icon: '🏡' },
        'end-of-tenancy': { label: 'End of Tenancy Cleaning', icon: '📦' },
        'post-construction': { label: 'Post Construction Cleaning', icon: '🚧' },
        'commercial-cleaning': { label: 'Commercial Cleaning', icon: '🏢' },
        'carpet-cleaning': { label: 'Carpet Cleaning', icon: '🧹' },
        'oven-cleaning': { label: 'Oven Cleaning', icon: '🔥' },
        'pressure-washing': { label: 'Pressure Washing', icon: '💧' },
        'solar-panel': { label: 'Solar Panel Cleaning', icon: '☀️' },
        'graffiti-removal': { label: 'Graffiti Removal', icon: '🎨' }
    };
    
    // Question flow definition based on JSON
    const questionFlow = {
        'service_selection': {
            question: 'Select a Service',
            type: 'single',
            options: [
                { value: 'window-cleaning', label: 'Window Cleaning', next: 'win_prop_type' },
                { value: 'gutter-cleaning', label: 'Gutter Cleaning', next: 'gut_prop_type' },
                { value: 'domestic-cleaning', label: 'Domestic Cleaning', next: 'dom_type' },
                { value: 'end-of-tenancy', label: 'Deep Clean/End of Tenancy Cleaning', next: 'eot_prop_type' },
                { value: 'post-construction', label: 'Post Construction Cleaning', next: 'contact_form' },
                { value: 'commercial-cleaning', label: 'Commercial Cleaning Exterior and Interior', next: 'contact_form' },
                { value: 'carpet-cleaning', label: 'Carpet Cleaning from Pricing Only', next: 'carpet_prop_type' },
                { value: 'oven-cleaning', label: 'Oven Cleaning', next: 'oven_size' },
                { value: 'pressure-washing', label: 'Pressure Washing', next: 'pw_location' },
                { value: 'solar-panel', label: 'Solar Panel Cleaning', next: 'contact_form' },
                { value: 'graffiti-removal', label: 'Graffiti Removal', next: 'contact_form' }
            ]
        },
        
        // Window Cleaning Flow
        'win_prop_type': {
            question: 'What type of property do you have?',
            type: 'single',
            options: [
                { value: 'detached', label: 'Detached', next: 'win_bedrooms' },
                { value: 'semi-detached', label: 'Semi-Detached', next: 'win_bedrooms' },
                { value: 'terraced', label: 'Terraced', next: 'win_bedrooms' },
                { value: 'town-house', label: 'Town House', next: 'win_bedrooms' },
                { value: 'bungalow', label: 'Bungalow', next: 'win_bedrooms' },
                { value: 'flat', label: 'Flat', next: 'win_bedrooms' }
            ]
        },
        'win_bedrooms': {
            question: 'Number of bedrooms?',
            type: 'single',
            priceField: true,
            options: [
                { value: '1-2', label: '1-2 Bedrooms', priceKey: 'ow_win_base_1_2', next: 'win_extension' },
                { value: '3', label: '3 Bedrooms', priceKey: 'ow_win_base_3', next: 'win_extension' },
                { value: '4', label: '4 Bedrooms', priceKey: 'ow_win_base_4', next: 'win_extension' },
                { value: '5', label: '5 Bedrooms', priceKey: 'ow_win_base_5', next: 'win_extension' },
                { value: '6+', label: '6+ Bedrooms', priceKey: 'ow_win_base_6plus', next: 'win_extension' }
            ]
        },
        'win_extension': {
            question: 'Does your property have an extension?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_win_addon_extension', next: 'win_skylights_check' },
                { value: 'no', label: 'No', next: 'win_skylights_check' }
            ]
        },
        'win_skylights_check': {
            question: 'Does your property have sky lights?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'win_skylights_qty' },
                { value: 'no', label: 'No', next: 'win_conservatory_check' }
            ]
        },
        'win_skylights_qty': {
            question: 'How many sky lights does your property have?',
            type: 'single',
            priceField: true,
            options: [
                { value: '1', label: '1', priceKey: 'ow_win_skylight_1', next: 'win_conservatory_check' },
                { value: '2', label: '2', priceKey: 'ow_win_skylight_2', next: 'win_conservatory_check' },
                { value: '3', label: '3', priceKey: 'ow_win_skylight_3', next: 'win_conservatory_check' },
                { value: '4+', label: '4+', priceKey: 'ow_win_skylight_4plus', next: 'win_conservatory_check' }
            ]
        },
        'win_conservatory_check': {
            question: 'Does your property have a conservatory?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_win_addon_conservatory', next: 'win_cons_roof' },
                { value: 'no', label: 'No', next: 'win_velux_check' }
            ]
        },
        'win_cons_roof': {
            question: 'Would you like the roof of your conservatory cleaned?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_win_addon_cons_roof', next: 'win_velux_check' },
                { value: 'no', label: 'No', next: 'win_velux_check' }
            ]
        },
        'win_velux_check': {
            question: 'Does your property have any Velux windows?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'win_velux_qty' },
                { value: 'no', label: 'No', next: 'win_access' }
            ]
        },
        'win_velux_qty': {
            question: 'How many Velux windows?',
            type: 'number',
            priceKey: 'ow_win_velux_unit_price',
            next: 'win_access'
        },
        'win_access': {
            question: 'Can we access rear windows without coming through property?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'win_parking' },
                { value: 'no', label: 'No', next: 'win_parking' }
            ]
        },
        'win_parking': {
            question: 'Parking available?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'display_quote' },
                { value: 'no', label: 'No', next: 'contact_form' }
            ]
        },
        
        // Gutter Cleaning Flow
        'gut_prop_type': {
            question: 'What type of property do you have?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'detached', label: 'Detached', priceKey: 'ow_gut_base_detached', next: 'gut_bedrooms' },
                { value: 'semi-detached', label: 'Semi-Detached', priceKey: 'ow_gut_base_semi', next: 'gut_bedrooms' },
                { value: 'terraced', label: 'Terraced', priceKey: 'ow_gut_base_terraced', next: 'gut_bedrooms' },
                { value: 'town-house', label: 'Town House', priceKey: 'ow_gut_base_townhouse', next: 'gut_bedrooms' },
                { value: 'bungalow', label: 'Bungalow', priceKey: 'ow_gut_base_bungalow', next: 'gut_bedrooms' },
                { value: 'flat', label: 'Flat', priceKey: 'ow_gut_base_flat', next: 'gut_bedrooms' }
            ]
        },
        'gut_bedrooms': {
            question: 'Number of bedrooms?',
            type: 'single',
            options: [
                { value: '1-2', label: '1-2 Bedrooms', next: 'gut_addons' },
                { value: '3', label: '3 Bedrooms', next: 'gut_addons' },
                { value: '4', label: '4 Bedrooms', next: 'gut_addons' },
                { value: '5+', label: '5+ Bedrooms', next: 'gut_addons' }
            ]
        },
        'gut_addons': {
            question: 'Additional features?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'extension', label: 'Extension', priceKey: 'ow_gut_addon_ext', next: 'gut_soffits' },
                { value: 'conservatory', label: 'Conservatory', priceKey: 'ow_gut_addon_cons', next: 'gut_soffits' },
                { value: 'neither', label: 'Neither', next: 'gut_soffits' }
            ]
        },
        'gut_soffits': {
            question: 'Soffit and fascia cleaning?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_gut_addon_soffit', next: 'display_quote' },
                { value: 'no', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Domestic Cleaning Flow
        'dom_type': {
            question: 'Cleaning type?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'weekly', label: 'Weekly', priceKey: 'ow_dom_hourly_rate', next: 'dom_prop_type' },
                { value: 'fortnightly', label: 'Fortnightly', priceKey: 'ow_dom_hourly_rate', next: 'dom_prop_type' },
                { value: 'monthly', label: 'Monthly', priceKey: 'ow_dom_hourly_rate', next: 'dom_prop_type' },
                { value: 'one-off', label: 'One Off Deep Clean', priceKey: 'ow_dom_deep_base', next: 'dom_prop_type' }
            ]
        },
        'dom_prop_type': {
            question: 'Property type?',
            type: 'single',
            options: [
                { value: 'bungalow', label: 'Bungalow', next: 'dom_bedrooms' },
                { value: 'flat', label: 'Flat', next: 'dom_bedrooms' },
                { value: 'house', label: 'House', next: 'dom_bedrooms' },
                { value: 'town-house', label: 'Town House', next: 'dom_bedrooms' }
            ]
        },
        'dom_bedrooms': {
            question: 'Number of bedrooms?',
            type: 'single',
            options: [
                { value: '1', label: '1 Bed', next: 'dom_bathrooms' },
                { value: '2', label: '2 Bed', next: 'dom_bathrooms' },
                { value: '3', label: '3 Bed', next: 'dom_bathrooms' },
                { value: '4', label: '4 Bed', next: 'dom_bathrooms' },
                { value: '5+', label: '5 Bed +', next: 'dom_bathrooms' }
            ]
        },
        'dom_bathrooms': {
            question: 'Number of bathrooms?',
            type: 'single',
            options: [
                { value: '1', label: '1', next: 'dom_hours' },
                { value: '2', label: '2', next: 'dom_hours' },
                { value: '3+', label: '3+', next: 'dom_hours' }
            ]
        },
        'dom_hours': {
            question: 'Hours required? (Min 2)',
            type: 'single',
            priceField: true,
            options: [
                { value: '2', label: '2 Hours', priceKey: 'ow_dom_hourly_rate', next: 'dom_addons' },
                { value: '3', label: '3 Hours', priceKey: 'ow_dom_hourly_rate', next: 'dom_addons' },
                { value: '4', label: '4 Hours', priceKey: 'ow_dom_hourly_rate', next: 'dom_addons' },
                { value: '5+', label: '5+ Hours', priceKey: 'ow_dom_hourly_rate', next: 'dom_addons' }
            ]
        },
        'dom_addons': {
            question: 'Extra services?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'fridge', label: 'Inside Fridge', priceKey: 'ow_price_fridge', next: 'display_quote' },
                { value: 'microwave', label: 'Inside Microwave', priceKey: 'ow_price_microwave', next: 'display_quote' },
                { value: 'bedsheets', label: 'Bed Sheet Changing', priceKey: 'ow_price_bedsheets', next: 'display_quote' },
                { value: 'none', label: 'No Extras', next: 'display_quote' }
            ]
        },
        
        // End of Tenancy Flow
        'eot_prop_type': {
            question: 'Property type?',
            type: 'single',
            options: [
                { value: 'bungalow', label: 'Bungalow', next: 'eot_size' },
                { value: 'flat', label: 'Flat', next: 'eot_size' },
                { value: 'house', label: 'House', next: 'eot_size' },
                { value: 'town-house', label: 'Town House', next: 'eot_size' }
            ]
        },
        'eot_size': {
            question: 'Number of bedrooms?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'studio', label: 'Studio Flat', priceKey: 'ow_eot_studio', next: 'eot_furnished' },
                { value: '1', label: '1 Bed', priceKey: 'ow_eot_1bed', next: 'eot_furnished' },
                { value: '2', label: '2 Bed', priceKey: 'ow_eot_2bed', next: 'eot_furnished' },
                { value: '3', label: '3 Bed', priceKey: 'ow_eot_3bed', next: 'eot_furnished' },
                { value: '4', label: '4 Bed', priceKey: 'ow_eot_4bed', next: 'eot_furnished' },
                { value: '5+', label: '5 Bed +', priceKey: 'ow_eot_5bed', next: 'eot_furnished' }
            ]
        },
        'eot_furnished': {
            question: 'Furnished or unfurnished?',
            type: 'single',
            options: [
                { value: 'furnished', label: 'Furnished', next: 'eot_carpets_check' },
                { value: 'unfurnished', label: 'Unfurnished', next: 'eot_carpets_check' }
            ]
        },
        'eot_carpets_check': {
            question: 'Carpet cleaning required?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'eot_carpets_qty' },
                { value: 'no', label: 'No', next: 'eot_oven_check' }
            ]
        },
        'eot_carpets_qty': {
            question: 'How many rooms for carpets?',
            type: 'single',
            priceField: true,
            options: [
                { value: '1', label: '1', priceKey: 'ow_carpet_unit', next: 'eot_oven_check' },
                { value: '2', label: '2', priceKey: 'ow_carpet_unit', next: 'eot_oven_check' },
                { value: '3', label: '3', priceKey: 'ow_carpet_unit', next: 'eot_oven_check' },
                { value: '4', label: '4', priceKey: 'ow_carpet_unit', next: 'eot_oven_check' },
                { value: '5+', label: '5+', priceKey: 'ow_carpet_unit', next: 'eot_oven_check' }
            ]
        },
        'eot_oven_check': {
            question: 'Do you require oven cleaning?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'oven_size' },
                { value: 'no', label: 'No', next: 'eot_fridge_check' }
            ]
        },
        'eot_fridge_check': {
            question: 'Do you require fridge cleaning?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_price_fridge', next: 'display_quote' },
                { value: 'no', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Carpet Cleaning Flow
        'carpet_prop_type': {
            question: 'Property type?',
            type: 'single',
            options: [
                { value: 'bungalow', label: 'Bungalow', next: 'carpet_qty' },
                { value: 'flat', label: 'Flat', next: 'carpet_qty' },
                { value: 'house', label: 'House', next: 'carpet_qty' },
                { value: 'town-house', label: 'Town House', next: 'carpet_qty' }
            ]
        },
        'carpet_qty': {
            question: 'Number of carpets requiring cleaning?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'small', label: 'Small Room', priceKey: 'ow_carpet_small', next: 'carpet_stairs' },
                { value: 'medium', label: 'Medium Room', priceKey: 'ow_carpet_medium', next: 'carpet_stairs' },
                { value: 'large', label: 'Large Room', priceKey: 'ow_carpet_large', next: 'carpet_stairs' }
            ]
        },
        'carpet_stairs': {
            question: 'Stairs or landing?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_carpet_stairs_landing', next: 'carpet_parking' },
                { value: 'no', label: 'No', next: 'carpet_parking' }
            ]
        },
        'carpet_parking': {
            question: 'Parking available?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'display_quote' },
                { value: 'no', label: 'No', next: 'contact_form' }
            ]
        },
        
        // Oven Cleaning Flow
        'oven_size': {
            question: 'What size is your oven?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'single', label: 'Single Oven', priceKey: 'ow_price_oven_single', next: 'oven_extras' },
                { value: 'double', label: 'Double Oven', priceKey: 'ow_price_oven_double', next: 'oven_extras' },
                { value: 'range', label: 'Range / Rangemaster', priceKey: 'ow_price_oven_range', next: 'oven_extras' },
                { value: 'aga', label: 'AGA Oven', priceKey: 'ow_price_oven_aga', next: 'oven_extras' }
            ]
        },
        'oven_extras': {
            question: 'Other appliance cleaning?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'fridge', label: 'Fridge', priceKey: 'ow_price_fridge', next: 'display_quote' },
                { value: 'microwave', label: 'Microwave', priceKey: 'ow_price_microwave', next: 'display_quote' },
                { value: 'none', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Pressure Washing Flow
        'pw_location': {
            question: 'Where do you require pressure washing?',
            type: 'single',
            options: [
                { value: 'patio', label: 'Patio', next: 'contact_form' },
                { value: 'driveway', label: 'Driveway', next: 'contact_form' },
                { value: 'both', label: 'Patio and Driveway', next: 'contact_form' }
            ]
        }
    };
    
    function init() {
        bindEvents();
        renderStep('service_selection');
    }
    
    function bindEvents() {
        $(document).on('click', '.quote-option', function() {
            const step = $(this).data('step');
            const value = $(this).data('value');
            const next = $(this).data('next');
            const priceKey = $(this).data('price-key');
            
            // Save answer
            answers[step] = {
                value: value,
                label: $(this).find('.option-label').text(),
                priceKey: priceKey || null
            };
            
            // Add to history
            stepHistory.push(currentStep);
            
            // Handle next step
            if (next === 'display_quote') {
                calculateAndShowQuote();
            } else if (next === 'contact_form') {
                showContactForm();
            } else {
                renderStep(next);
            }
        });
        
        $(document).on('click', '#quote-back', function() {
            goBack();
        });
        
        $(document).on('click', '#submit-quote', function() {
            submitQuote();
        });
    }
    
    function renderStep(stepId) {
        currentStep = stepId;
        const step = questionFlow[stepId];
        if (!step) return;
        
        const container = $('#quote-wizard-container');
        let html = '<div class="quote-step" data-step="' + stepId + '">';
        
        // Progress bar
        html += '<div class="quote-progress" style="margin-bottom: 30px;">';
        html += '<div style="height: 4px; background: var(--pgc-gray-200); border-radius: 2px;">';
        html += '<div style="height: 100%; background: linear-gradient(90deg, var(--pgc-primary), var(--pgc-secondary)); width: ' + getProgress() + '%; transition: width 0.3s;"></div>';
        html += '</div></div>';
        
        // Question
        html += '<h2 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 30px; text-align: center;">' + step.question + '</h2>';
        
        // Options
        html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">';
        
        if (step.type === 'single') {
            step.options.forEach(function(opt) {
                html += '<div class="quote-option" data-step="' + stepId + '" data-value="' + opt.value + '" data-next="' + opt.next + '" data-price-key="' + (opt.priceKey || '') + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s;">';
                html += '<div class="option-label" style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
                html += '</div>';
            });
        }
        
        html += '</div>';
        
        // Back button (if not first step)
        if (stepHistory.length > 0) {
            html += '<div style="text-align: center; margin-top: 30px;">';
            html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline" style="padding: 12px 30px;">← Back</button>';
            html += '</div>';
        }
        
        html += '</div>';
        container.html(html);
    }
    
    function getProgress() {
        // Simple progress based on history length
        const maxSteps = 8;
        return Math.min((stepHistory.length / maxSteps) * 100, 90);
    }
    
    function goBack() {
        if (stepHistory.length > 0) {
            const prevStep = stepHistory.pop();
            delete answers[currentStep];
            renderStep(prevStep);
        }
    }
    
    function calculateAndShowQuote() {
        // Collect all price keys
        const priceKeys = [];
        for (const key in answers) {
            if (answers[key].priceKey) {
                priceKeys.push(answers[key].priceKey);
            }
        }
        
        // AJAX call to calculate price
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pgc_calculate_quote_v3',
                nonce: pgc_ajax.nonce,
                service: answers['service_selection'].value,
                price_keys: priceKeys,
                answers: JSON.stringify(answers)
            },
            success: function(response) {
                if (response.success) {
                    calculatedPrice = response.data.total;
                    priceBreakdown = response.data.breakdown;
                    showQuoteSummary();
                }
            }
        });
    }
    
    function showQuoteSummary() {
        const container = $('#quote-wizard-container');
        let html = '<div class="quote-summary">';
        
        html += '<h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-gray-900); margin-bottom: 30px; text-align: center;">Your Quote Summary</h2>';
        
        // Price display
        html += '<div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border: 2px solid rgba(8, 145, 178, 0.2); border-radius: 16px; padding: 40px; text-align: center; margin-bottom: 30px;">';
        html += '<div style="font-size: 3rem; font-weight: 800; background: linear-gradient(135deg, var(--pgc-primary), var(--pgc-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">£' + calculatedPrice.toFixed(2) + '</div>';
        html += '<div style="color: var(--pgc-gray-500); margin-top: 10px;">Estimated Price</div>';
        html += '</div>';
        
        // Breakdown
        if (priceBreakdown && priceBreakdown.length > 0) {
            html += '<div style="margin-bottom: 30px;">';
            html += '<h3 style="font-size: 1.1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 15px;">Price Breakdown</h3>';
            priceBreakdown.forEach(function(item) {
                html += '<div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--pgc-gray-100);">';
                html += '<span style="color: var(--pgc-gray-600);">' + item.label + '</span>';
                html += '<span style="font-weight: 600;">£' + item.price.toFixed(2) + '</span>';
                html += '</div>';
            });
            html += '</div>';
        }
        
        // Buttons
        html += '<div style="display: flex; gap: 15px; justify-content: center;">';
        html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline">← Back</button>';
        html += '<button type="button" class="pgc-btn pgc-btn-primary" onclick="showContactForm()">Proceed to Book →</button>';
        html += '</div>';
        
        html += '</div>';
        container.html(html);
    }
    
    function showContactForm() {
        const container = $('#quote-wizard-container');
        let html = '<div class="quote-contact">';
        
        html += '<h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-gray-900); margin-bottom: 10px; text-align: center;">Complete Your Booking</h2>';
        
        if (calculatedPrice > 0) {
            html += '<p style="text-align: center; color: var(--pgc-gray-500); margin-bottom: 30px;">Quote: £' + calculatedPrice.toFixed(2) + '</p>';
        }
        
        html += '<form id="quote-contact-form">';
        
        // Hidden field with quote breakdown
        html += '<input type="hidden" name="quote_data" value="' + encodeURIComponent(JSON.stringify({
            service: answers['service_selection'].value,
            answers: answers,
            price: calculatedPrice,
            breakdown: priceBreakdown
        })) + '">';
        
        // Name fields
        html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        html += '<div><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">First Name *</label><input type="text" name="first_name" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Last Name *</label><input type="text" name="last_name" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '</div>';
        
        // Email
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Email *</label><input type="email" name="email" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        
        // Phone
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Phone *</label><input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        
        // Address
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Address *</label><input type="text" name="address" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        
        // Postcode
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Postcode *</label><input type="text" name="postcode" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        
        // Notes
        html += '<div style="margin-bottom: 20px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Additional Notes</label><textarea name="notes" rows="3" style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px; resize: vertical;"></textarea></div>';
        
        // Buttons
        html += '<div style="display: flex; gap: 15px; justify-content: center;">';
        html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline">← Back</button>';
        html += '<button type="submit" class="pgc-btn pgc-btn-primary">Submit Booking Request</button>';
        html += '</div>';
        
        html += '</form></div>';
        container.html(html);
    }
    
    function submitQuote() {
        const form = $('#quote-contact-form');
        const formData = form.serialize();
        
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=pgc_submit_quote_v3&nonce=' + pgc_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#quote-wizard-container').html('<div style="text-align: center; padding: 60px 20px;"><div style="font-size: 64px; margin-bottom: 20px;">✓</div><h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 16px;">Thank You!</h2><p style="color: var(--pgc-gray-600);">Your quote request has been submitted. We will be in touch shortly.</p></div>');
                }
            }
        });
    }
    
    $(document).ready(init);
    
})(jQuery);
