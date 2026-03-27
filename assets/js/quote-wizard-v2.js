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
    let allServiceAnswers = {};
    let currentServiceKey = 'service_1';
    let includedServices = []; // Services included as add-ons (won't show in upsell)
    
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
        'fridge-cleaning': { label: 'Fridge Cleaning', icon: '🧊' },
        'microwave-cleaning': { label: 'Microwave Cleaning', icon: '📟' },
        'pressure-washing': { label: 'Pressure Washing', icon: '💧' },
        'solar-panel': { label: 'Solar Panel Cleaning', icon: '☀️' },
        'graffiti-removal': { label: 'Graffiti Removal', icon: '🎨' }
    };
    
    // Generate human-readable quote summary
    function generateQuoteSummary() {
        const service = answers['service_selection']?.label || 'Cleaning Service';
        let summary = service + '\n\n';
        
        // Build readable summary based on service
        for (const [stepId, answer] of Object.entries(answers)) {
            if (stepId === 'service_selection') continue;
            
            const stepLabels = {
                'win_internal_external': 'Service type',
                'win_frequency': 'Frequency',
                'win_prop_type': 'Property type',
                'win_bedrooms': 'Bedrooms',
                'win_extension': 'Extension',
                'win_skylights_check': 'Sky lights',
                'win_skylights_qty': 'Number of sky lights',
                'win_conservatory_check': 'Conservatory',
                'win_cons_size': 'Conservatory size',
                'win_cons_roof_clean': 'Conservatory roof clean',
                'win_velux_check': 'Velux windows',
                'win_velux_qty': 'Number of Velux windows',
                'win_access': 'Rear access',
                'win_parking': 'Parking',
                'gut_bedrooms': 'Bedrooms',
                'gut_prop_type': 'Property type',
                'gut_extension': 'Extension',
                'gut_conservatory': 'Conservatory',
                'gut_soffits': 'Soffit and fascia',
                'gut_survey': 'Before/after survey',
                'dom_type': 'Cleaning type',
                'dom_prop_type': 'Property type',
                'dom_bedrooms': 'Bedrooms',
                'dom_bathrooms': 'Bathrooms',
                'dom_internal_windows': 'Internal windows',
                'dom_hours': 'Hours required',
                'dom_addons': 'Extra services',
                'eot_prop_type': 'Property type',
                'eot_size': 'Property size',
                'eot_furnished': 'Furnished status',
                'eot_carpets_check': 'Carpet cleaning',
                'eot_carpets_qty': 'Carpet rooms',
                'eot_oven_check': 'Oven cleaning',
                'eot_fridge_check': 'Fridge cleaning',
                'carpet_prop_type': 'Property type',
                'carpet_rooms': 'Room sizes',
                'carpet_stairs': 'Stairs/landing',
                'carpet_parking': 'Parking',
                'oven_size': 'Oven size',

                'pw_location': 'Pressure washing location'
            };
            
            const label = stepLabels[stepId] || stepId;
            let valueText = answer.label;
            
            // Special formatting for certain answers
            if (stepId === 'win_velux_qty' && answer.value) {
                valueText = answer.value + ' Velux window' + (parseInt(answer.value) > 1 ? 's' : '');
            }
            if (stepId === 'eot_carpets_qty' && answer.value) {
                valueText = answer.value + ' room' + (parseInt(answer.value) > 1 ? 's' : '');
            }
            if (stepId === 'dom_hours' && answer.value) {
                valueText = answer.value + ' hour' + (parseInt(answer.value) > 1 ? 's' : '');
            }
            // Skip individual carpet room size steps from summary - we'll summarize them together
            if (stepId.startsWith('carpet_room_')) {
                continue;
            }
            // Skip carpet_rooms step itself - we'll summarize room sizes
            if (stepId === 'carpet_rooms') {
                continue;
            }
            // Skip internal flags
            if (stepId.endsWith('_has_oven')) {
                continue;
            }
            if (answer.value === 'no' && !stepId.includes('check')) {
                continue; // Skip "no" answers unless they're specific questions
            }
            if (answer.value === 'neither') {
                continue; // Skip "neither" answers
            }
            if (answer.value === 'none') {
                continue; // Skip "none" answers
            }
            
            summary += label + ': ' + valueText + '\n';
        }
        
        // Add carpet room sizes summary
        const roomSizes = [];
        const roomCount = parseInt(answers['carpet_qty']?.value || '0');
        for (let i = 1; i <= roomCount; i++) {
            const roomKey = 'carpet_room_' + i;
            if (answers[roomKey] && answers[roomKey].value) {
                roomSizes.push(answers[roomKey].value);
            }
        }
        if (roomSizes.length > 0) {
            const sizeCounts = {};
            roomSizes.forEach(size => {
                sizeCounts[size] = (sizeCounts[size] || 0) + 1;
            });
            const sizeSummary = Object.entries(sizeCounts)
                .map(([size, count]) => count + 'x ' + size.charAt(0).toUpperCase() + size.slice(1))
                .join(', ');
            summary += 'Room sizes: ' + sizeSummary + '\n';
        }
        
        summary += '\nTotal: £' + calculatedPrice.toFixed(2);
        return summary;
    }
    
    // Question flow definition based on JSON
    const questionFlow = {
        'service_selection': {
            question: 'Select a Service',
            type: 'single',
            options: [
                { value: 'window-cleaning', label: 'Window Cleaning', next: 'win_internal_external' },
                { value: 'gutter-cleaning', label: 'Gutter Cleaning', next: 'gut_bedrooms' },
                { value: 'domestic-cleaning', label: 'Domestic Cleaning', next: 'dom_type' },
                { value: 'end-of-tenancy', label: 'Deep Clean/End of Tenancy Cleaning', next: 'eot_prop_type' },
                { value: 'post-construction', label: 'Post Construction Cleaning', next: 'contact_form' },
                { value: 'commercial-cleaning', label: 'Commercial Cleaning Exterior and Interior', next: 'contact_form' },
                { value: 'carpet-cleaning', label: 'Carpet Cleaning', next: 'carpet_prop_type' },
                { value: 'oven-cleaning', label: 'Oven Cleaning', next: 'oven_size' },
                { value: 'fridge-cleaning', label: 'Fridge Cleaning', next: 'fridge_cleaning_single' },
                { value: 'microwave-cleaning', label: 'Microwave Cleaning', next: 'microwave_cleaning_single' },
                { value: 'pressure-washing', label: 'Pressure Washing', next: 'pw_location' },
                { value: 'solar-panel', label: 'Solar Panel Cleaning', next: 'contact_form' },
                { value: 'graffiti-removal', label: 'Graffiti Removal', next: 'contact_form' }
            ]
        },
        
        // Window Cleaning Flow
        'win_internal_external': {
            question: 'Internal or External?',
            type: 'single',
            options: [
                { value: 'external', label: 'External Only', next: 'win_frequency' },
                { value: 'internal', label: 'Internal Only', next: 'win_frequency' },
                { value: 'both', label: 'Both Internal and External', next: 'win_frequency' }
            ]
        },
        'win_frequency': {
            question: 'Frequency required?',
            type: 'single',
            options: [
                { value: 'one-off', label: 'One Off', next: 'win_prop_type' },
                { value: '4-weekly', label: '4 Weekly', next: 'win_prop_type' },
                { value: '8-weekly', label: '8 Weekly', next: 'win_prop_type' },
                { value: '12-weekly', label: '12 Weekly', next: 'win_prop_type' }
            ]
        },
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
                { value: '2', label: '2 Bedrooms', priceKey: 'ow_win_2bed', next: 'win_extension' },
                { value: '3', label: '3 Bedrooms', priceKey: 'ow_win_3bed', next: 'win_extension' },
                { value: '4', label: '4 Bedrooms', priceKey: 'ow_win_4bed', next: 'win_extension' },
                { value: '5', label: '5 Bedrooms', priceKey: 'ow_win_5bed', next: 'win_extension' }
            ]
        },
        'win_extension': {
            question: 'Does your property have an extension?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'yes', label: 'Yes', priceKey: 'ow_win_addon_ext', next: 'win_skylights_check' },
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
            type: 'number',
            priceKey: 'ow_win_skylight_unit',
            next: 'win_conservatory_check'
        },
        'win_conservatory_check': {
            question: 'Does your property have a conservatory?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'win_cons_roof_clean' },
                { value: 'no', label: 'No', next: 'win_velux_check' }
            ]
        },
        'win_cons_roof_clean': {
            question: 'Would you like the roof of your conservatory cleaned?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'win_cons_size' },
                { value: 'no', label: 'No', next: 'win_velux_check' }
            ]
        },
        'win_cons_size': {
            question: 'What size is your conservatory?',
            type: 'single',
            options: [
                { value: 'small', label: 'Small', next: 'win_velux_check' },
                { value: 'medium', label: 'Medium', next: 'win_velux_check' },
                { value: 'large', label: 'Large', next: 'win_velux_check' }
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
                { value: 'no', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Gutter Cleaning Flow
        'gut_bedrooms': {
            question: 'How many bedrooms?',
            type: 'single',
            options: [
                { value: '2', label: '2 Bedrooms', next: 'gut_prop_type' },
                { value: '3', label: '3 Bedrooms', next: 'gut_prop_type' },
                { value: '4', label: '4 Bedrooms', next: 'gut_prop_type' },
                { value: '5', label: '5+ Bedrooms', next: 'gut_prop_type' }
            ]
        },
        'gut_prop_type': {
            question: 'What type of property?',
            type: 'single',
            options: [
                { value: 'semi', label: 'Semi-Detached', next: 'gut_extension' },
                { value: 'detached', label: 'Detached', next: 'gut_extension' },
                { value: 'terraced', label: 'Terraced', next: 'gut_extension' },
                { value: 'bungalow', label: 'Bungalow', next: 'gut_extension' },
                { value: 'townhouse', label: 'Townhouse', next: 'gut_extension' }
            ]
        },
        'gut_extension': {
            question: 'Do you have an extension?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'gut_conservatory' },
                { value: 'no', label: 'No', next: 'gut_conservatory' }
            ]
        },
        'gut_conservatory': {
            question: 'Do you have a conservatory?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'gut_soffits' },
                { value: 'no', label: 'No', next: 'gut_soffits' }
            ]
        },
        'gut_soffits': {
            question: 'Add soffit and fascia cleaning?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'gut_survey' },
                { value: 'no', label: 'No', next: 'gut_survey' }
            ]
        },
        'gut_survey': {
            question: 'Add before and after survey?',
            subtitle: 'This provides photographic evidence of your gutter cleaning for insurance purposes and your peace of mind. A valuable addition to verify the work has been completed correctly.',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'display_quote' },
                { value: 'no', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Domestic Cleaning Flow
        'dom_type': {
            question: 'Cleaning type?',
            type: 'single',
            options: [
                { value: 'weekly', label: 'Weekly', next: 'dom_prop_type' },
                { value: 'fortnightly', label: 'Fortnightly', next: 'dom_prop_type' },
                { value: 'monthly', label: 'Monthly', next: 'dom_prop_type' },
                { value: 'one-off-deep', label: 'One Off Deep Clean', next: 'eot_prop_type' }
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
                { value: '1', label: '1', next: 'dom_internal_windows' },
                { value: '2', label: '2', next: 'dom_internal_windows' },
                { value: '3+', label: '3+', next: 'dom_internal_windows' }
            ]
        },
        'dom_internal_windows': {
            question: 'How many internal windows need cleaning?',
            type: 'number',
            priceKey: 'ow_price_interior_window',
            next: 'dom_hours'
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
            type: 'multi',
            options: [
                { value: 'fridge', label: 'Inside Fridge', priceKey: 'ow_price_fridge' },
                { value: 'microwave', label: 'Inside Microwave', priceKey: 'ow_price_microwave' },
                { value: 'bedsheets', label: 'Bed Sheet Changing', priceKey: 'ow_price_bedsheets' },
                { value: 'oven', label: 'Oven Cleaning', priceKey: 'oven_followup' }
            ],
            nextStep: 'dom_addons_next'
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
                { value: '1', label: '1', priceKey: 'ow_carpet_unit', next: 'eot_addons' },
                { value: '2', label: '2', priceKey: 'ow_carpet_unit', next: 'eot_addons' },
                { value: '3', label: '3', priceKey: 'ow_carpet_unit', next: 'eot_addons' },
                { value: '4', label: '4', priceKey: 'ow_carpet_unit', next: 'eot_addons' },
                { value: '5+', label: '5+', priceKey: 'ow_carpet_unit', next: 'eot_addons' }
            ]
        },
        'eot_addons': {
            question: 'Extra services?',
            type: 'multi',
            options: [
                { value: 'fridge', label: 'Inside Fridge', priceKey: 'ow_price_fridge' },
                { value: 'microwave', label: 'Inside Microwave', priceKey: 'ow_price_microwave' },
                { value: 'oven', label: 'Oven Cleaning', priceKey: 'oven_followup' }
            ],
            nextStep: 'eot_addons_next'
        },
        'eot_oven_check': {
            question: 'Oven cleaning required?',
            type: 'single',
            options: [
                { value: 'yes', label: 'Yes', next: 'oven_size' },
                { value: 'no', label: 'No', next: 'eot_fridge_check' }
            ]
        },
        'eot_fridge_check': {
            question: 'Inside fridge cleaning required?',
            type: 'single',
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
                { value: 'bungalow', label: 'Bungalow', next: 'carpet_rooms' },
                { value: 'flat', label: 'Flat', next: 'carpet_rooms' },
                { value: 'house', label: 'House', next: 'carpet_rooms' },
                { value: 'town-house', label: 'Town House', next: 'carpet_rooms' }
            ]
        },
        'carpet_rooms': {
            question: 'Select room sizes',
            type: 'carpet_room_selector',
            next: 'carpet_stairs'
        },
        'carpet_stairs': {
            question: 'Stairs and/or landing?',
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
                { value: 'no', label: 'No', next: 'display_quote' }
            ]
        },
        
        // Oven Cleaning Flow
        'oven_size': {
            question: 'What size is your oven?',
            type: 'single',
            priceField: true,
            options: [
                { value: 'single', label: 'Single Oven', priceKey: 'ow_price_oven_single', next: 'display_quote' },
                { value: 'double', label: 'Double Oven', priceKey: 'ow_price_oven_double', next: 'display_quote' },
                { value: 'range', label: 'Range / Rangemaster', priceKey: 'ow_price_oven_range', next: 'display_quote' },
                { value: 'aga', label: 'AGA Oven', priceKey: 'ow_price_oven_aga', next: 'display_quote' }
            ]
        },
        // Fridge Cleaning Flow - Single price, auto-selected
        'fridge_cleaning_single': {
            question: 'Fridge Cleaning',
            subtitle: 'Standard single fridge cleaning',
            type: 'single',
            priceField: true,
            options: [
                { value: 'standard', label: 'Standard Fridge Clean (£40)', priceKey: 'ow_price_fridge', next: 'display_quote' }
            ]
        },
        
        // Microwave Cleaning Flow - Single price, auto-selected
        'microwave_cleaning_single': {
            question: 'Microwave Cleaning',
            subtitle: 'Standard single microwave cleaning',
            type: 'single',
            priceField: true,
            options: [
                { value: 'standard', label: 'Standard Microwave Clean (£25)', priceKey: 'ow_price_microwave', next: 'display_quote' }
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
        },
        'upsell_services': {
            question: 'Do you require any other services?',
            type: 'upsell',
            options: [] // Populated dynamically
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
            
            // Skip if this is the upsell step (handled by separate handler)
            if (step === 'upsell_services') {
                return;
            }
            
            // Save answer
            answers[step] = {
                value: value,
                label: $(this).find('.option-label').text(),
                priceKey: priceKey || null
            };
            
            // Track included services from EOT flow
            if (step === 'eot_oven_check' && value === 'yes') {
                if (includedServices.indexOf('oven-cleaning') === -1) {
                    includedServices.push('oven-cleaning');
                }
            }
            if (step === 'eot_fridge_check' && value === 'yes') {
                if (includedServices.indexOf('fridge-cleaning') === -1) {
                    includedServices.push('fridge-cleaning');
                }
            }
            // Track carpet cleaning when selected in EOT flow
            if (step === 'eot_carpets_check' && value === 'yes') {
                if (includedServices.indexOf('carpet-cleaning') === -1) {
                    includedServices.push('carpet-cleaning');
                }
            }
            // Add to history
            stepHistory.push(currentStep);
            
            // Handle next step
            if (next === 'display_quote') {
                // Save current service before showing upsell
                if (answers['service_selection']) {
                    allServiceAnswers[currentServiceKey] = {
                        service: answers['service_selection'].value,
                        serviceLabel: answers['service_selection'].label,
                        answers: Object.assign({}, answers)
                    };
                    // Also add to includedServices so other services know it's already selected
                    const serviceValue = answers['service_selection'].value;
                    if (includedServices.indexOf(serviceValue) === -1) {
                        includedServices.push(serviceValue);
                    }
                    // Increment service key for next service
                    const currentNum = parseInt(currentServiceKey.replace('service_', ''));
                    currentServiceKey = 'service_' + (currentNum + 1);
                }
                // Clear answers and reset for next service
                answers = {};
                stepHistory = [];
                renderStep('upsell_services');
            } else if (next === 'contact_form') {
                // Service requires manual quote - save and go to upsell
                // Check if we have a current service being worked on
                if (answers['service_selection']) {
                    allServiceAnswers[currentServiceKey] = {
                        service: answers['service_selection'].value,
                        serviceLabel: answers['service_selection'].label,
                        answers: Object.assign({}, answers),
                        manualQuote: true
                    };
                    // Also add to includedServices so other services know it's already selected
                    const serviceValue = answers['service_selection'].value;
                    if (includedServices.indexOf(serviceValue) === -1) {
                        includedServices.push(serviceValue);
                    }
                    // Increment service key for next service
                    const currentNum = parseInt(currentServiceKey.replace('service_', ''));
                    currentServiceKey = 'service_' + (currentNum + 1);
                }
                // Clear answers and reset for next service
                answers = {};
                stepHistory = [];
                renderStep('upsell_services');
            } else {
                renderStep(next);
            }
        });
        
        $(document).on('click', '#quote-back', function() {
            goBack();
        });
        
        // Handle number input for Velux windows
        $(document).on('click', '#velux-continue', function() {
            const qty = $('#velux-qty').val();
            if (!qty || qty < 1) {
                alert('Please enter a valid number');
                return;
            }
            
            answers['win_velux_qty'] = {
                value: qty,
                label: qty + ' Velux windows',
                priceKey: 'ow_win_velux_unit_price'
            };
            
            stepHistory.push(currentStep);
            renderStep('win_access');
        });
        
        // Handle number input for internal windows (domestic cleaning)
        $(document).on('click', '#internal-windows-continue', function() {
            const qty = $('#internal-windows-qty').val();
            if (!qty || qty < 0) {
                alert('Please enter a valid number');
                return;
            }
            
            if (qty == 0) {
                answers['dom_internal_windows'] = {
                    value: '0',
                    label: 'No internal windows',
                    priceKey: null
                };
            } else {
                answers['dom_internal_windows'] = {
                    value: qty,
                    label: qty + ' internal window' + (parseInt(qty) > 1 ? 's' : ''),
                    priceKey: 'ow_price_interior_window'
                };
            }
            
            stepHistory.push(currentStep);
            renderStep('dom_hours');
        });
        
        // Handle multi-select options
        $(document).on('click', '.quote-option-multi', function() {
            const $this = $(this);
            const isSelected = $this.attr('data-selected') === 'true';
            
            if (isSelected) {
                $this.attr('data-selected', 'false');
                $this.css({
                    'background': 'var(--pgc-gray-50)',
                    'border-color': 'transparent'
                });
            } else {
                $this.attr('data-selected', 'true');
                $this.css({
                    'background': 'linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%)',
                    'border-color': 'var(--pgc-primary)'
                });
            }
            
            // Update button text if any selected
            const stepId = $this.data('step');
            const hasSelection = $('.quote-option-multi[data-step="' + stepId + '"][data-selected="true"]').length > 0;
            const $btn = $('#multi-continue[data-step="' + stepId + '"]');
            if (hasSelection) {
                $btn.text('Next →').removeClass('pgc-btn-outline').addClass('pgc-btn-primary');
            } else {
                $btn.text('No Extras').removeClass('pgc-btn-primary').addClass('pgc-btn-outline');
            }
        });
        
        // Handle multi-select continue
        $(document).on('click', '#multi-continue', function() {
            const stepId = $(this).data('step');
            const nextLogic = $(this).data('next');
            const step = questionFlow[stepId];
            
            const selected = [];
            const selectedLabels = [];
            let hasOven = false;
            let hasFridge = false;
            let hasMicrowave = false;
            
            $('.quote-option-multi[data-step="' + stepId + '"][data-selected="true"]').each(function() {
                const value = $(this).data('value');
                const priceKey = $(this).data('price-key');
                const label = $(this).find('.option-label').text();
                selected.push({ value: value, priceKey: priceKey });
                selectedLabels.push(label);
                if (value === 'oven') hasOven = true;
                if (value === 'fridge') hasFridge = true;
                if (value === 'microwave') hasMicrowave = true;
            });
            
            answers[stepId] = {
                value: selected.length > 0 ? selected.map(s => s.value).join(',') : 'none',
                label: selectedLabels.length > 0 ? selectedLabels.join(', ') : 'No extras',
                multi: selected
            };
            
            // Track included services so they don't show in upsell
            if (hasOven && includedServices.indexOf('oven-cleaning') === -1) {
                includedServices.push('oven-cleaning');
            }
            if (hasFridge && includedServices.indexOf('fridge-cleaning') === -1) {
                includedServices.push('fridge-cleaning');
            }
            if (hasMicrowave && includedServices.indexOf('microwave-cleaning') === -1) {
                includedServices.push('microwave-cleaning');
            }
            
            stepHistory.push(currentStep);
            
            // Determine next step
            if (hasOven) {
                answers[stepId + '_has_oven'] = true;
                renderStep('oven_size');
            } else {
                renderStep('upsell_services');
            }
        });
        
        // Handle upsell service selection - when clicking a service in upsell step
        $(document).on('click', '.quote-option[data-step="upsell_services"]', function(e) {
            e.stopPropagation(); // Prevent the regular quote-option handler
            
            const value = $(this).data('value');
            const next = $(this).data('next');
            const label = $(this).find('.option-label').text();
            
            console.log('Upsell service clicked:', value, 'next:', next, 'currentServiceKey:', currentServiceKey);
            
            // Save current service answers (if there's a current service being worked on AND not already saved)
            if (answers['service_selection']) {
                // Check if this service is already saved
                let alreadySaved = false;
                for (const key in allServiceAnswers) {
                    if (allServiceAnswers[key].service === answers['service_selection'].value) {
                        alreadySaved = true;
                        break;
                    }
                }
                if (!alreadySaved) {
                    allServiceAnswers[currentServiceKey] = {
                        service: answers['service_selection'].value,
                        serviceLabel: answers['service_selection'].label,
                        answers: Object.assign({}, answers)
                    };
                    console.log('Saved current service to', currentServiceKey);
                }
            }
            
            // Find the next available service number
            let maxNum = 0;
            for (const key in allServiceAnswers) {
                if (key.startsWith('service_')) {
                    const num = parseInt(key.replace('service_', ''));
                    if (num > maxNum) maxNum = num;
                }
            }
            currentServiceKey = 'service_' + (maxNum + 1);
            console.log('Next service key:', currentServiceKey, 'allServiceAnswers:', Object.keys(allServiceAnswers));
            
            // Check if this service goes straight to contact form (manual quote)
            if (next === 'contact_form') {
                console.log('Saving manual quote service:', value, 'to', currentServiceKey);
                // Save as manual quote service and return to upsell
                allServiceAnswers[currentServiceKey] = {
                    service: value,
                    serviceLabel: label,
                    answers: { 'service_selection': { value: value, label: label } },
                    manualQuote: true
                };
                
                // Find next key for future services
                maxNum = 0;
                for (const key in allServiceAnswers) {
                    if (key.startsWith('service_')) {
                        const num = parseInt(key.replace('service_', ''));
                        if (num > maxNum) maxNum = num;
                    }
                }
                currentServiceKey = 'service_' + (maxNum + 1);
                
                // Clear answers and go back to upsell
                answers = {};
                stepHistory = [];
                console.log('Rendering upsell, allServiceAnswers:', allServiceAnswers);
                renderStep('upsell_services');
                return;
            }
            
            // Start the new service flow with fresh answers
            answers = {
                'service_selection': { value: value, label: label }
            };
            stepHistory = [];
            
            renderStep(next);
        });
        
        // Handle upsell finish - show final quote
        $(document).on('click', '#upsell-finish', function() {
            stepHistory.push(currentStep);
            // Save current service before calculating (if there's an active service being worked on)
            if (answers['service_selection']) {
                allServiceAnswers[currentServiceKey] = {
                    service: answers['service_selection'].value,
                    serviceLabel: answers['service_selection'].label,
                    answers: Object.assign({}, answers)
                };
            }
            calculateAndShowQuote();
        });
        
        // Carpet room count change - regenerate rows
        $(document).on('input', '#carpet-room-count', function() {
            let count = parseInt($(this).val()) || 1;
            if (count < 1) count = 1;
            if (count > 20) count = 20;
            $('#carpet-room-sizes').html(generateRoomSizeRows(count));
        });
        
        // Carpet size button selection
        $(document).on('click', '.carpet-size-btn', function() {
            const room = $(this).data('room');
            const size = $(this).data('size');
            
            // Remove selected class from other buttons in this room
            $('.carpet-size-btn[data-room="' + room + '"]').css({
                'background': 'white',
                'border-color': 'var(--pgc-gray-200)',
                'color': 'var(--pgc-gray-600)'
            });
            
            // Add selected style to clicked button
            $(this).css({
                'background': 'linear-gradient(135deg, var(--pgc-primary), var(--pgc-secondary))',
                'border-color': 'var(--pgc-primary)',
                'color': 'white'
            });
            
            // Store selection
            $(this).closest('.carpet-room-row').attr('data-selected', size);
        });
        
        // Carpet rooms continue button
        $(document).on('click', '#carpet-rooms-continue', function() {
            const roomCount = parseInt($('#carpet-room-count').val()) || 1;
            const roomSelections = {};
            let allSelected = true;
            
            for (let i = 1; i <= roomCount; i++) {
                const selectedSize = $('.carpet-room-row[data-room="' + i + '"]').attr('data-selected');
                if (!selectedSize) {
                    allSelected = false;
                    break;
                }
                roomSelections['carpet_room_' + i] = {
                    value: selectedSize,
                    label: selectedSize.charAt(0).toUpperCase() + selectedSize.slice(1)
                };
            }
            
            if (!allSelected) {
                alert('Please select a size for each room');
                return;
            }
            
            // Save all room selections to answers
            Object.assign(answers, roomSelections);
            
            // Save room count
            answers['carpet_qty'] = {
                value: roomCount.toString(),
                label: roomCount + ' room' + (roomCount > 1 ? 's' : '')
            };
            
            stepHistory.push(currentStep);
            renderStep('carpet_stairs');
        });
        
        // Initialize room rows when carpet_rooms step is rendered
        $(document).on('renderStepComplete', function(e, stepId) {
            if (stepId === 'carpet_rooms') {
                $('#carpet-room-sizes').html(generateRoomSizeRows(1));
            }
        });
    }
    
    function renderStep(stepId) {
        currentStep = stepId;
        const step = questionFlow[stepId];
        if (!step) return;
        
        // Special handling for dom_addons - filter out already selected appliances
        if (stepId === 'dom_addons') {
            // Check which appliance services are already included
            const hasOven = includedServices.indexOf('oven-cleaning') !== -1;
            const hasFridge = includedServices.indexOf('fridge-cleaning') !== -1;
            const hasMicrowave = includedServices.indexOf('microwave-cleaning') !== -1;
            
            // Filter options - only show appliances not already selected
            const filteredOptions = step.options.filter(function(opt) {
                if (opt.value === 'oven' && hasOven) return false;
                if (opt.value === 'fridge' && hasFridge) return false;
                if (opt.value === 'microwave' && hasMicrowave) return false;
                return true;
            });
            
            // If only bedsheets remains (or nothing), auto-answer and skip
            const applianceOptions = filteredOptions.filter(function(opt) {
                return opt.value !== 'bedsheets';
            });
            
            if (applianceOptions.length === 0) {
                // No appliance options left, save empty and skip to upsell
                answers['dom_addons'] = {
                    value: 'none',
                    label: 'No extras',
                    multi: []
                };
                stepHistory.push('dom_addons');
                renderStep('upsell_services');
                return;
            }
            
            // Render with filtered options
            renderDomAddonsWithOptions(filteredOptions);
            return;
        }
        
        // Scroll to top of wizard on every step change
        const wizardContainer = document.getElementById('quote-wizard-container');
        if (wizardContainer) {
            wizardContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            window.scrollTo({ top: wizardContainer.offsetTop - 100, behavior: 'smooth' });
        }
        
        const container = $('#quote-wizard-container');
        let html = '<div class="quote-step" data-step="' + stepId + '">';
        
        // Progress bar
        html += '<div class="quote-progress" style="margin-bottom: 30px;">';
        html += '<div style="height: 4px; background: var(--pgc-gray-200); border-radius: 2px;">';
        html += '<div style="height: 100%; background: linear-gradient(90deg, var(--pgc-primary), var(--pgc-secondary)); width: ' + getProgress() + '%; transition: width 0.3s;"></div>';
        html += '</div></div>';
        
        // Question
        html += '<h2 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 10px; text-align: center;">' + step.question + '</h2>';
        
        // Subtitle if present
        if (step.subtitle) {
            html += '<p style="font-size: 14px; color: var(--pgc-gray-500); text-align: center; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.5;">' + step.subtitle + '</p>';
        }
        
        // Special handling for number input
        if (step.type === 'number') {
            html += '<div style="max-width: 300px; margin: 0 auto;">';
            if (stepId === 'dom_internal_windows') {
                html += '<input type="number" id="internal-windows-qty" min="0" value="0" style="width: 100%; padding: 16px; font-size: 18px; border: 2px solid var(--pgc-gray-200); border-radius: 12px; text-align: center; margin-bottom: 20px;">';
                html += '<button type="button" id="internal-windows-continue" class="pgc-btn pgc-btn-primary" style="width: 100%; padding: 16px;">Continue</button>';
            } else {
                html += '<input type="number" id="velux-qty" min="1" value="1" style="width: 100%; padding: 16px; font-size: 18px; border: 2px solid var(--pgc-gray-200); border-radius: 12px; text-align: center; margin-bottom: 20px;">';
                html += '<button type="button" id="velux-continue" class="pgc-btn pgc-btn-primary" style="width: 100%; padding: 16px;">Continue</button>';
            }
            html += '</div>';
        } 
        // Special handling for carpet room selector
        else if (step.type === 'carpet_room_selector') {
            html += renderCarpetRoomSelector(stepId, step);
        }
        // Special handling for multi-select
        else if (step.type === 'multi') {
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">';
            step.options.forEach(function(opt) {
                html += '<div class="quote-option-multi" data-step="' + stepId + '" data-value="' + opt.value + '" data-price-key="' + (opt.priceKey || '') + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s;">';
                html += '<div class="option-label" style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
                html += '</div>';
            });
            html += '</div>';
            html += '<div style="text-align: center;">';
            html += '<button type="button" id="multi-continue" data-step="' + stepId + '" data-next="' + step.nextStep + '" class="pgc-btn pgc-btn-outline" style="padding: 16px 48px; font-size: 16px;">No Extras</button>';
            html += '</div>';
        }
        // Special handling for upsell step
        else if (step.type === 'upsell') {
            // Get already selected services from allServiceAnswers + current answers
            const selectedServices = [];
            const selectedServiceKeys = [];
            const addedKeys = new Set(); // For deduplication
            
            // Add from allServiceAnswers (previously completed services)
            for (const key in allServiceAnswers) {
                if (key.startsWith('service_')) {
                    const svc = allServiceAnswers[key].service;
                    const label = allServiceAnswers[key].serviceLabel;
                    if (!addedKeys.has(svc)) {
                        selectedServices.push(label);
                        selectedServiceKeys.push(svc);
                        addedKeys.add(svc);
                    }
                }
            }
            
            // Add current service being worked on (if not already saved and not already in list)
            if (answers['service_selection'] && !addedKeys.has(answers['service_selection'].value)) {
                // Check if this service is already saved with a different key
                let alreadySaved = false;
                for (const key in allServiceAnswers) {
                    if (key.startsWith('service_') && allServiceAnswers[key].service === answers['service_selection'].value) {
                        alreadySaved = true;
                        break;
                    }
                }
                if (!alreadySaved) {
                    selectedServices.push(answers['service_selection'].label);
                    selectedServiceKeys.push(answers['service_selection'].value);
                }
            }
            
            // Add included services (add-ons) to the selected list so they don't show as options
            includedServices.forEach(function(svc) {
                if (selectedServiceKeys.indexOf(svc) === -1) {
                    selectedServiceKeys.push(svc);
                    if (serviceInfo[svc]) {
                        selectedServices.push(serviceInfo[svc].label);
                    }
                }
            });
            
            // Show selected services
            if (selectedServices.length > 0) {
                html += '<div style="background: var(--pgc-gray-50); border-radius: 12px; padding: 20px; margin-bottom: 30px;">';
                html += '<h3 style="font-size: 1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 15px;">Services already selected:</h3>';
                html += '<ul style="margin: 0; padding-left: 20px; color: var(--pgc-gray-600);">';
                selectedServices.forEach(function(svc) {
                    html += '<li style="margin-bottom: 5px;">' + svc + '</li>';
                });
                html += '</ul>';
                html += '</div>';
            }
            
            // Use same format as original service_selection
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">';
            
            // Get options from service_selection (same as first screen)
            const serviceOptions = questionFlow['service_selection'].options;
            
            serviceOptions.forEach(function(opt) {
                // Skip already selected services only
                if (selectedServiceKeys.indexOf(opt.value) === -1) {
                    html += '<div class="quote-option" data-step="' + stepId + '" data-value="' + opt.value + '" data-next="' + opt.next + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s;">';
                    html += '<div class="option-label" style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
                    html += '</div>';
                }
            });
            html += '</div>';
            html += '<div style="text-align: center; margin-top: 30px;">';
            html += '<button type="button" id="upsell-finish" class="pgc-btn pgc-btn-primary" style="padding: 16px 48px; font-size: 16px;">No thanks, that\'s all for now</button>';
            html += '</div>';
        } 
        else {
            // Options
            html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">';
            
            if (step.type === 'single') {
                // Get carpet room quantity to filter options
                const carpetQty = answers['carpet_qty']?.value || '1';
                const maxRooms = parseInt(carpetQty) || 1;
                
                step.options.forEach(function(opt) {
                    // Skip "No more rooms" option if we haven't reached that room number
                    if (opt.hiddenIfLess && maxRooms < opt.hiddenIfLess) {
                        return;
                    }
                    // Also skip "No more rooms" if this is the first room
                    if (opt.value === 'skip' && stepId === 'carpet_room_1') {
                        return;
                    }
                    
                    let label = opt.label;
                    
                    // Add size definitions for conservatory size step
                    if (stepId === 'win_cons_size' && window.pgc_ajax && window.pgc_ajax.conservatory_sizes) {
                        const sizeDef = window.pgc_ajax.conservatory_sizes[opt.value];
                        if (sizeDef) {
                            label += ' <span style="color: var(--pgc-gray-500); font-size: 14px;">(' + sizeDef + ')</span>';
                        }
                    }
                    
                    // Add size definitions for carpet room size steps
                    if (stepId.startsWith('carpet_room_') && window.pgc_ajax && window.pgc_ajax.carpet_sizes) {
                        const sizeDef = window.pgc_ajax.carpet_sizes[opt.value];
                        if (sizeDef) {
                            label += ' <span style="color: var(--pgc-gray-500); font-size: 14px;">(' + sizeDef + ')</span>';
                        }
                    }
                    
                    html += '<div class="quote-option" data-step="' + stepId + '" data-value="' + opt.value + '" data-next="' + opt.next + '" data-price-key="' + (opt.priceKey || '') + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s;">';
                    html += '<div class="option-label" style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + label + '</div>';
                    html += '</div>';
                });
            }
            
            html += '</div>';
        }
        
        // Back button (if not first step)
        if (stepHistory.length > 0) {
            html += '<div style="text-align: center; margin-top: 30px;">';
            html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline" style="padding: 12px 30px;">← Back</button>';
            html += '</div>';
        }
        
        html += '</div>';
        container.html(html);
        
        // Trigger event for steps that need post-render initialization
        $(document).trigger('renderStepComplete', [stepId]);
    }
    
    // Render carpet room selector with number input and dynamic size rows
    function renderCarpetRoomSelector(stepId, step) {
        let html = '';
        
        // Room count input
        html += '<div style="max-width: 200px; margin: 0 auto 30px;">';
        html += '<label style="display: block; font-weight: 600; margin-bottom: 10px; color: var(--pgc-gray-700); text-align: center;">Number of rooms</label>';
        html += '<input type="number" id="carpet-room-count" min="1" max="20" value="1" style="width: 100%; padding: 16px; font-size: 18px; border: 2px solid var(--pgc-gray-200); border-radius: 12px; text-align: center;">';
        html += '</div>';
        
        // Container for room size selectors
        html += '<div id="carpet-room-sizes" style="margin-bottom: 30px;">';
        html += '</div>';
        
        // Next button
        html += '<div style="text-align: center;">';
        html += '<button type="button" id="carpet-rooms-continue" class="pgc-btn pgc-btn-primary" style="padding: 16px 48px; font-size: 16px;">Next →</button>';
        html += '</div>';
        
        return html;
    }
    
    // Generate room size selector rows
    function generateRoomSizeRows(count) {
        let html = '';
        const sizes = window.pgc_ajax?.carpet_sizes || { small: '4x4m', medium: '5x5m', large: '6x6m' };
        
        for (let i = 1; i <= count; i++) {
            html += '<div class="carpet-room-row" data-room="' + i + '" style="background: var(--pgc-gray-50); border-radius: 12px; padding: 16px 20px; margin-bottom: 12px; display: flex; align-items: center; gap: 20px;">';
            html += '<div style="font-weight: 600; color: var(--pgc-gray-700); min-width: 80px;">Room ' + i + ':</div>';
            html += '<div style="display: flex; gap: 10px; flex-wrap: wrap;">';
            
            ['small', 'medium', 'large'].forEach(function(size) {
                const sizeDef = sizes[size] || '';
                const sizeLabel = size.charAt(0).toUpperCase() + size.slice(1);
                html += '<button type="button" class="carpet-size-btn" data-room="' + i + '" data-size="' + size + '" style="padding: 10px 20px; border: 2px solid var(--pgc-gray-200); border-radius: 8px; background: white; cursor: pointer; font-weight: 500; color: var(--pgc-gray-600); transition: all 0.2s;">';
                html += sizeLabel + ' <span style="font-size: 12px; color: var(--pgc-gray-400);">(' + sizeDef + ')</span>';
                html += '</button>';
            });
            
            html += '</div>';
            html += '</div>';
        }
        
        return html;
    }
    
    // Render dom_addons with filtered options (for when appliances already selected)
    function renderDomAddonsWithOptions(filteredOptions) {
        const container = $('#quote-wizard-container');
        const stepId = 'dom_addons';
        const step = questionFlow[stepId];
        
        let html = '<div class="quote-step" data-step="' + stepId + '">';
        
        // Progress bar
        html += '<div class="quote-progress" style="margin-bottom: 30px;">';
        html += '<div style="height: 4px; background: var(--pgc-gray-200); border-radius: 2px;">';
        html += '<div style="height: 100%; background: linear-gradient(90deg, var(--pgc-primary), var(--pgc-secondary)); width: ' + getProgress() + '%; transition: width 0.3s;"></div>';
        html += '</div></div>';
        
        // Question
        html += '<h2 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 10px; text-align: center;">' + step.question + '</h2>';
        
        // Subtitle if present
        if (step.subtitle) {
            html += '<p style="font-size: 14px; color: var(--pgc-gray-500); text-align: center; margin-bottom: 30px; max-width: 500px; margin-left: auto; margin-right: auto; line-height: 1.5;">' + step.subtitle + '</p>';
        }
        
        // Options - use filtered options
        html += '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">';
        filteredOptions.forEach(function(opt) {
            html += '<div class="quote-option-multi" data-step="' + stepId + '" data-value="' + opt.value + '" data-price-key="' + (opt.priceKey || '') + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 24px; text-align: center; cursor: pointer; transition: all 0.3s;">';
            html += '<div class="option-label" style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
            html += '</div>';
        });
        html += '</div>';
        html += '<div style="text-align: center;">';
        html += '<button type="button" id="multi-continue" data-step="' + stepId + '" data-next="' + step.nextStep + '" class="pgc-btn pgc-btn-outline" style="padding: 16px 48px; font-size: 16px;">No Extras</button>';
        html += '</div>';
        
        // Back button
        if (stepHistory.length > 0) {
            html += '<div style="text-align: center; margin-top: 30px;">';
            html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline" style="padding: 12px 30px;">← Back</button>';
            html += '</div>';
        }
        
        html += '</div>';
        container.html(html);
        
        // Trigger event
        $(document).trigger('renderStepComplete', [stepId]);
    }
    
    function getProgress() {
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
    
    // Store multiple service calculations
    let serviceCalculations = [];
    
    function calculateAndShowQuote() {
        // Calculate all services stored in allServiceAnswers
        serviceCalculations = [];
        const serviceKeys = Object.keys(allServiceAnswers).sort();
        
        if (serviceKeys.length === 0) {
            // No services stored yet, calculate current one
            const service = answers['service_selection']?.value || '';
            calculateServiceQuote(service, answers, function(result) {
                serviceCalculations.push({
                    service: service,
                    serviceLabel: serviceInfo[service]?.label || service,
                    price: result.total,
                    breakdown: result.breakdown
                });
                showQuoteAndContactForm();
            });
            return;
        }
        
        calculateNextService(serviceKeys, 0, function() {
            showQuoteAndContactForm();
        });
    }
    
    function calculateNextService(serviceKeys, index, callback) {
        if (index >= serviceKeys.length) {
            callback();
            return;
        }
        
        const key = serviceKeys[index];
        const serviceData = allServiceAnswers[key];
        const service = serviceData.service;
        const serviceAnswers = serviceData.answers;
        
        // Handle manual quote services (skip AJAX calculation)
        if (serviceData.manualQuote) {
            serviceCalculations.push({
                service: service,
                serviceLabel: serviceInfo[service]?.label || service,
                price: 0,
                breakdown: [],
                manualQuote: true
            });
            calculateNextService(serviceKeys, index + 1, callback);
            return;
        }
        
        let priceKeys = [];
        
        if (service !== 'window-cleaning') {
            for (const ansKey in serviceAnswers) {
                if (serviceAnswers[ansKey].priceKey) {
                    priceKeys.push(serviceAnswers[ansKey].priceKey);
                }
            }
        }
        
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pgc_calculate_quote_v3',
                nonce: pgc_ajax.nonce,
                service: service,
                price_keys: priceKeys,
                answers: JSON.stringify(serviceAnswers)
            },
            success: function(response) {
                if (response.success) {
                    serviceCalculations.push({
                        service: service,
                        serviceLabel: serviceInfo[service]?.label || service,
                        price: response.data.total,
                        breakdown: response.data.breakdown
                    });
                }
                calculateNextService(serviceKeys, index + 1, callback);
            },
            error: function() {
                calculateNextService(serviceKeys, index + 1, callback);
            }
        });
    }
    
    function calculateServiceQuote(service, serviceAnswers, callback) {
        let priceKeys = [];
        
        if (service !== 'window-cleaning') {
            for (const key in serviceAnswers) {
                if (serviceAnswers[key].priceKey) {
                    priceKeys.push(serviceAnswers[key].priceKey);
                }
            }
        }
        
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pgc_calculate_quote_v3',
                nonce: pgc_ajax.nonce,
                service: service,
                price_keys: priceKeys,
                answers: JSON.stringify(serviceAnswers)
            },
            success: function(response) {
                if (response.success) {
                    callback({
                        total: response.data.total,
                        breakdown: response.data.breakdown
                    });
                } else {
                    callback({ total: 0, breakdown: [] });
                }
            },
            error: function() {
                callback({ total: 0, breakdown: [] });
            }
        });
    }
    
    
    function showQuoteAndContactForm() {
        const container = $('#quote-wizard-container');
        
        // Scroll to top of wizard container for mobile users
        const wizardContainer = document.getElementById('quote-wizard-container');
        if (wizardContainer) {
            wizardContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            // Also scroll window to ensure we're at the top
            window.scrollTo({ top: wizardContainer.offsetTop - 100, behavior: 'smooth' });
        }
        
        // Calculate grand total from all services
        let grandTotal = 0;
        for (let i = 0; i < serviceCalculations.length; i++) {
            grandTotal += serviceCalculations[i].price;
        }
        
        // Count manual quote services from serviceCalculations
        const manualQuoteServices = [];
        for (let i = 0; i < serviceCalculations.length; i++) {
            if (serviceCalculations[i].manualQuote) {
                manualQuoteServices.push(serviceCalculations[i].serviceLabel);
            }
        }
        
        // Generate summary for all services
        let fullSummary = '';
        for (let i = 0; i < serviceCalculations.length; i++) {
            const calc = serviceCalculations[i];
            fullSummary += '=== ' + calc.serviceLabel + ' ===\n';
            
            // Handle manual quote services differently
            if (calc.manualQuote) {
                fullSummary += '(Please upload images to assist us with your quote and we will be in contact shortly)\n\n';
            } else {
                for (let j = 0; j < calc.breakdown.length; j++) {
                    fullSummary += calc.breakdown[j].label + ': £' + calc.breakdown[j].price.toFixed(2) + '\n';
                }
                fullSummary += 'Subtotal: £' + calc.price.toFixed(2) + '\n\n';
            }
        }
        
        // Add included services (add-ons) to summary
        if (includedServices.length > 0) {
            fullSummary += '=== Included Add-on Services ===\n';
            includedServices.forEach(function(svc) {
                if (serviceInfo[svc]) {
                    fullSummary += serviceInfo[svc].label + ' (included with main service)\n';
                }
            });
            fullSummary += '\n';
        }
        
        let html = '<form id="quote-contact-form">';
        
        // Hidden field with full quote data for admin
        html += '<input type="hidden" name="quote_data" value="' + encodeURIComponent(JSON.stringify({
            services: serviceCalculations,
            manualServices: manualQuoteServices,
            grandTotal: grandTotal,
            summary: fullSummary
        })) + '">';
        
        // Page Title
        html += '<h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-gray-900); margin-bottom: 10px; text-align: center;">Complete Your Booking</h2>';
        
        // Price Display with Grand Total
        html += '<div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.1) 0%, rgba(16, 185, 129, 0.1) 100%); border: 2px solid rgba(8, 145, 178, 0.2); border-radius: 16px; padding: 30px; text-align: center; margin-bottom: 30px;">';
        
        // Show individual service totals (skip manual quote services)
        for (let i = 0; i < serviceCalculations.length; i++) {
            if (!serviceCalculations[i].manualQuote) {
                html += '<div style="font-size: 1rem; color: var(--pgc-gray-600); margin-bottom: 5px;">' + serviceCalculations[i].serviceLabel + ': £' + serviceCalculations[i].price.toFixed(2) + '</div>';
            }
        }
        
        // Show manual quote services separately
        if (manualQuoteServices.length > 0) {
            manualQuoteServices.forEach(function(svc) {
                html += '<div style="font-size: 1rem; color: var(--pgc-gray-500); margin-bottom: 5px;">' + svc + ': (Please upload images to assist us with your quote and we will be in contact shortly)</div>';
            });
        }
        
        if (serviceCalculations.length > 0) {
            html += '<div style="border-top: 1px solid rgba(8, 145, 178, 0.2); margin: 15px 0;"></div>';
            html += '<div style="font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, var(--pgc-primary), var(--pgc-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">£' + grandTotal.toFixed(2) + '</div>';
        }
        
        html += '<div style="color: var(--pgc-gray-500); margin-top: 5px;">Grand Total*</div>';
        html += '<div style="color: var(--pgc-gray-400); font-size: 12px; margin-top: 8px;">*Prices are estimates and may vary based on actual conditions</div>';
        html += '</div>';
        
        // Human-readable quote summary
        html += '<div style="background: var(--pgc-gray-50); border-radius: 12px; padding: 20px; margin-bottom: 30px;">';
        html += '<h3 style="font-size: 1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 15px;">Quote Summary</h3>';
        html += '<pre style="margin: 0; font-family: inherit; font-size: 0.95rem; line-height: 1.6; color: var(--pgc-gray-600); white-space: pre-wrap;">' + fullSummary + '</pre>';
        html += '</div>';
        
        // Contact Form Fields
        html += '<div style="border-top: 2px solid var(--pgc-gray-100); padding-top: 30px;">';
        html += '<h3 style="font-size: 1.1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 20px;">Your Details</h3>';
        
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
        
        // Image Uploads (up to 5 images)
        html += '<div style="margin-bottom: 25px;">';
        html += '<label style="display: block; font-weight: 500; margin-bottom: 10px; color: var(--pgc-gray-700);">Upload Images (Optional)</label>';
        html += '<p style="font-size: 13px; color: var(--pgc-gray-500); margin: 0 0 10px 0;">Upload photos of areas to be cleaned (max 5 images, 5MB each)</p>';
        html += '<div id="image-upload-container">';
        for (let i = 1; i <= 5; i++) {
            html += '<div style="margin-bottom: 8px;"><input type="file" name="quote_images[]" accept="image/*" style="width: 100%; padding: 8px; border: 2px solid var(--pgc-gray-200); border-radius: 8px; font-size: 14px;"></div>';
        }
        html += '</div>';
        html += '</div>';
        
        // Buttons
        html += '<div style="display: flex; gap: 15px; justify-content: center;">';
        html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline">← Back</button>';
        html += '<button type="submit" class="pgc-btn pgc-btn-primary">Submit Booking Request</button>';
        html += '</div>';
        
        html += '</div></form>';
        container.html(html);
        
        // Bind form submission
        $('#quote-contact-form').on('submit', function(e) {
            e.preventDefault();
            submitQuote();
        });
    }
    
    function showContactForm() {
        const container = $('#quote-wizard-container');
        
        // Scroll to top of wizard container for mobile users
        const wizardContainer = document.getElementById('quote-wizard-container');
        if (wizardContainer) {
            wizardContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            window.scrollTo({ top: wizardContainer.offsetTop - 100, behavior: 'smooth' });
        }
        
        let html = '<form id="quote-contact-form">';
        
        html += '<input type="hidden" name="quote_data" value="' + encodeURIComponent(JSON.stringify({
            service: answers['service_selection'].value,
            answers: answers,
            price: 0,
            requires_quote: true
        })) + '">';
        
        html += '<h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-gray-900); margin-bottom: 10px; text-align: center;">Request a Quote</h2>';
        html += '<p style="text-align: center; color: var(--pgc-gray-500); margin-bottom: 30px;">This service requires a bespoke quote. Please provide your details and we will contact you shortly.</p>';
        
        // Name fields
        html += '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">';
        html += '<div><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">First Name *</label><input type="text" name="first_name" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Last Name *</label><input type="text" name="last_name" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '</div>';
        
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Email *</label><input type="email" name="email" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Phone *</label><input type="tel" name="phone" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Address *</label><input type="text" name="address" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div style="margin-bottom: 15px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Postcode *</label><input type="text" name="postcode" required style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px;"></div>';
        html += '<div style="margin-bottom: 20px;"><label style="display: block; font-weight: 500; margin-bottom: 5px; color: var(--pgc-gray-700);">Additional Notes</label><textarea name="notes" rows="3" style="width: 100%; padding: 12px; border: 2px solid var(--pgc-gray-200); border-radius: 8px; resize: vertical;"></textarea></div>';
        
        // Image Uploads (up to 5 images)
        html += '<div style="margin-bottom: 25px;">';
        html += '<label style="display: block; font-weight: 500; margin-bottom: 10px; color: var(--pgc-gray-700);">Upload Images (Optional)</label>';
        html += '<p style="font-size: 13px; color: var(--pgc-gray-500); margin: 0 0 10px 0;">Upload photos of areas to be cleaned (max 5 images, 5MB each)</p>';
        html += '<div id="image-upload-container">';
        for (let i = 1; i <= 5; i++) {
            html += '<div style="margin-bottom: 8px;"><input type="file" name="quote_images[]" accept="image/*" style="width: 100%; padding: 8px; border: 2px solid var(--pgc-gray-200); border-radius: 8px; font-size: 14px;"></div>';
        }
        html += '</div>';
        html += '</div>';
        
        html += '<div style="display: flex; gap: 15px; justify-content: center;">';
        html += '<button type="button" id="quote-back" class="pgc-btn pgc-btn-outline">← Back</button>';
        html += '<button type="submit" class="pgc-btn pgc-btn-primary">Request Quote</button>';
        html += '</div>';
        
        html += '</form>';
        container.html(html);
        
        $('#quote-contact-form').on('submit', function(e) {
            e.preventDefault();
            submitQuote();
        });
    }
    
    function submitQuote() {
        const form = $('#quote-contact-form')[0];
        const formData = new FormData(form);
        formData.append('action', 'pgc_submit_quote_v3');
        formData.append('nonce', pgc_ajax.nonce);
        
        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;
        
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#quote-wizard-container').html('<div style="text-align: center; padding: 60px 20px;"><div style="font-size: 64px; margin-bottom: 20px;">✓</div><h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 16px;">Thank You!</h2><p style="color: var(--pgc-gray-600);">Your quote request has been submitted. We will be in touch shortly.</p></div>');
                } else {
                    alert('There was an error submitting your quote. Please try again.');
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            },
            error: function() {
                alert('There was an error submitting your quote. Please try again.');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    $(document).ready(init);
    
})(jQuery);
