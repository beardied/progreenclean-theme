/**
 * ProGreenClean Quote Wizard - Fixed Back Button & Consistent Yes/No
 */
(function($) {
    'use strict';
    
    let currentStep = 0;
    let selectedService = '';
    let answers = {};
    let calculatedPrice = 0;
    let questionQueue = [];
    let availableUpsells = [];
    let selectedUpsells = [];
    let questionHistory = []; // Track question history for back button
    
    // Question flows - Yes/No questions always have NO first (left), YES second (right)
    const questionFlows = {
        'window-cleaning': [
            { id: 'property_type', label: 'What type of property do you have?', type: 'single', options: [
                { value: 'detached', label: 'Detached' },
                { value: 'semi-detached', label: 'Semi-Detached' },
                { value: 'terraced', label: 'Terraced' },
                { value: 'townhouse', label: 'Town House' },
                { value: 'bungalow', label: 'Bungalow' },
                { value: 'flat', label: 'Flat' },
            ]},
            { id: 'bedrooms', label: 'Number of bedrooms?', type: 'single', options: [
                { value: '1-2', label: '1-2 Bedrooms' },
                { value: '3', label: '3 Bedrooms' },
                { value: '4', label: '4 Bedrooms' },
                { value: '5', label: '5 Bedrooms' },
                { value: '6+', label: '6+ Bedrooms' },
            ]},
            { id: 'frequency', label: 'How often would you like cleaning?', type: 'single', options: [
                { value: '4week', label: 'Every 4 weeks' },
                { value: '8week', label: 'Every 8 weeks' },
                { value: '12week', label: 'Every 12 weeks' },
                { value: 'once', label: 'One-off' },
            ]},
            { id: 'has_extension', label: 'Does your property have an extension?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'has_conservatory', label: 'Does your property have a conservatory?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'skylights', label: 'Does your property have skylights?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'skylights_count', label: 'How many skylights?', type: 'single', showIf: { skylights: 'yes' }, options: [
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4+', label: '4+' },
            ]},
            { id: 'has_conservatory_roof', label: 'Would you like the roof of your conservatory cleaned?', type: 'single', yesNo: true, showIf: { has_conservatory: 'yes' }, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'velux', label: 'Does your property have Velux windows?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'velux_count', label: 'How many Velux windows?', type: 'single', showIf: { velux: 'yes' }, options: [
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4+', label: '4+' },
            ]},
            { id: 'rear_access', label: 'Can we access rear windows without coming through your property?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'parking', label: 'Is parking available?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
        ],
        'gutter-cleaning': [
            { id: 'property_type', label: 'What type of property do you have?', type: 'single', options: [
                { value: 'detached', label: 'Detached' },
                { value: 'semi-detached', label: 'Semi-Detached' },
                { value: 'terraced', label: 'Terraced' },
                { value: 'townhouse', label: 'Town House' },
                { value: 'bungalow', label: 'Bungalow' },
                { value: 'flat', label: 'Flat' },
            ]},
            { id: 'bedrooms', label: 'Number of bedrooms?', type: 'single', options: [
                { value: '1-2', label: '1-2 Bedrooms' },
                { value: '3', label: '3 Bedrooms' },
                { value: '4', label: '4 Bedrooms' },
                { value: '5', label: '5 Bedrooms' },
                { value: '6+', label: '6+ Bedrooms' },
            ]},
            { id: 'has_extension', label: 'Does your property have an extension?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'storeys', label: 'How many storeys?', type: 'single', options: [
                { value: '1', label: '1 (Bungalow)' },
                { value: '2', label: '2 (House)' },
                { value: '3', label: '3 (Townhouse)' },
            ]},
            { id: 'heavy_blockage', label: 'Heavy blockage?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'gutter_guards', label: 'Gutter guards installed?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'coverage', label: 'Coverage required?', type: 'single', options: [
                { value: 'full', label: 'Full property' },
                { value: 'front', label: 'Front only' },
                { value: 'rear', label: 'Rear only' },
            ]},

            { id: 'parking', label: 'Is parking available?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
        ],
        'domestic-cleaning': [
            { id: 'cleaning_type', label: 'Cleaning type?', type: 'single', options: [
                { value: 'weekly', label: 'Weekly' },
                { value: 'fortnightly', label: 'Fortnightly' },
                { value: 'monthly', label: 'Monthly' },
                { value: 'one-off', label: 'One-off deep clean' },
            ]},
            { id: 'property_type', label: 'Property type?', type: 'single', options: [
                { value: 'bungalow', label: 'Bungalow' },
                { value: 'flat', label: 'Flat' },
                { value: 'house', label: 'House' },
                { value: 'townhouse', label: 'Town House' },
            ]},
            { id: 'bedrooms', label: 'Number of bedrooms?', type: 'single', options: [
                { value: '1', label: '1 Bed' },
                { value: '2', label: '2 Bed' },
                { value: '3', label: '3 Bed' },
                { value: '4', label: '4 Bed' },
                { value: '5+', label: '5 Bed +' },
            ]},
            { id: 'bathrooms', label: 'Number of bathrooms?', type: 'single', options: [
                { value: '1', label: '1 Bathroom' },
                { value: '2', label: '2 Bathrooms' },
                { value: '3+', label: '3+ Bathrooms' },
            ]},
            { id: 'hours', label: 'Hours required?', type: 'single', options: [
                { value: '2', label: '2 hours' },
                { value: '3', label: '3 hours' },
                { value: '4', label: '4 hours' },
                { value: '5+', label: '5+ hours' },
            ]},
            { id: 'parking', label: 'Is parking available?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'extras', label: 'Would you like any extra services included in your clean?', type: 'multi', options: [
                { value: 'fridge', label: 'Inside Fridge' },
                { value: 'microwave', label: 'Inside Microwave' },
                { value: 'oven', label: 'Oven clean' },
                { value: 'windows', label: 'Interior windows' },
                { value: 'bedsheets', label: 'Bedsheet changing' },
            ]},
        ],
        'end-of-tenancy': [
            { id: 'property_type', label: 'Property type?', type: 'single', options: [
                { value: 'bungalow', label: 'Bungalow' },
                { value: 'flat', label: 'Flat' },
                { value: 'house', label: 'House' },
                { value: 'townhouse', label: 'Town House' },
            ]},
            { id: 'bedrooms', label: 'Number of bedrooms?', type: 'single', options: [
                { value: 'studio', label: 'Studio' },
                { value: '1', label: '1 Bed' },
                { value: '2', label: '2 Bed' },
                { value: '3', label: '3 Bed' },
                { value: '4', label: '4 Bed' },
                { value: '5+', label: '5 Bed +' },
            ]},
            { id: 'bathrooms', label: 'Number of bathrooms?', type: 'single', options: [
                { value: '1', label: '1 Bathroom' },
                { value: '2', label: '2 Bathrooms' },
                { value: '3+', label: '3+ Bathrooms' },
            ]},
            { id: 'furnished', label: 'Will your property be furnished or unfurnished?', type: 'single', options: [
                { value: 'furnished', label: 'Furnished' },
                { value: 'unfurnished', label: 'Unfurnished' },
            ]},
            { id: 'carpet_cleaning', label: 'Carpet cleaning required?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'carpet_rooms', label: 'Number of rooms for carpet cleaning?', type: 'single', showIf: { carpet_cleaning: 'yes' }, options: [
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4', label: '4' },
                { value: '5+', label: '5+' },
            ]},
            { id: 'stairs_landing', label: 'Stairs and/or landing?', type: 'single', showIf: { carpet_cleaning: 'yes' }, options: [
                { value: 'no', label: 'No' },
                { value: 'hallway', label: 'Hallway' },
                { value: 'stairs', label: 'Stairs' },
                { value: 'both', label: 'Both' },
            ]},
            { id: 'heavy_stains', label: 'Heavy stains?', type: 'single', yesNo: true, showIf: { carpet_cleaning: 'yes' }, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'oven_cleaning', label: 'Do you require oven cleaning?', type: 'single', options: [
                { value: 'no', label: 'No' },
                { value: 'single', label: 'Single Oven' },
                { value: 'double', label: 'Double Oven' },
                { value: 'range', label: 'Range / Rangemaster' },
                { value: 'aga', label: 'AGA' },
            ]},
            { id: 'fridge_cleaning', label: 'Do you require fridge cleaning?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
        ],
        'oven-cleaning': [
            { id: 'oven_type', label: 'What size is your oven?', type: 'single', options: [
                { value: 'single', label: 'Single Oven' },
                { value: 'double', label: 'Double Oven' },
                { value: 'range', label: 'Range / Rangemaster' },
                { value: 'aga', label: 'AGA' },
            ]},
            { id: 'extras', label: 'Do you require any other appliance cleaning?', type: 'multi', options: [
                { value: 'fridge', label: 'Fridge' },
                { value: 'microwave', label: 'Microwave' },
            ]},
        ],
        'carpet-cleaning': [
            { id: 'property_type', label: 'Property type?', type: 'single', options: [
                { value: 'bungalow', label: 'Bungalow' },
                { value: 'flat', label: 'Flat' },
                { value: 'house', label: 'House' },
                { value: 'townhouse', label: 'Town House' },
            ]},
            { id: 'small_rooms', label: 'Number of small rooms (4x4m)?', type: 'single', options: [
                { value: '0', label: '0' },
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4+', label: '4+' },
            ]},
            { id: 'medium_rooms', label: 'Number of medium rooms (5x5m)?', type: 'single', options: [
                { value: '0', label: '0' },
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4+', label: '4+' },
            ]},
            { id: 'large_rooms', label: 'Number of large rooms (6x6m)?', type: 'single', options: [
                { value: '0', label: '0' },
                { value: '1', label: '1' },
                { value: '2', label: '2' },
                { value: '3', label: '3' },
                { value: '4+', label: '4+' },
            ]},
            { id: 'stairs', label: 'Stairs and/or landing?', type: 'single', options: [
                { value: 'no', label: 'No' },
                { value: 'stairs', label: 'Stairs' },
                { value: 'landing', label: 'Landing' },
                { value: 'both', label: 'Both' },
            ]},
            { id: 'heavy_stains', label: 'Heavy stains?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
            { id: 'parking', label: 'Is parking available?', type: 'single', yesNo: true, options: [
                { value: 'no', label: 'No' },
                { value: 'yes', label: 'Yes' },
            ]},
        ],
        'pressure-washing': [
            { id: 'surface_type', label: 'Where do you require pressure washing?', type: 'single', options: [
                { value: 'patio', label: 'Patio' },
                { value: 'driveway', label: 'Driveway' },
                { value: 'both', label: 'Patio and Driveway' },
            ]},
        ],
        'one-off-cleaning': [],
        'commercial-window': [],
        'office-cleaning': [],
        'gardening': [],
        'builders-cleaning': [],
    };
    
    function init() {
        bindEvents();
    }
    
    function bindEvents() {
        $(document).on('click', '.quote-service-card', function() {
            $('.quote-service-card').removeClass('selected');
            $(this).addClass('selected');
            selectedService = $(this).data('service');
            buildQuestionQueue();
            questionHistory = []; // Reset history
            setTimeout(showNextQuestion, 300);
        });
        
        $(document).on('click', '.question-tile.single', function() {
            const questionId = $(this).data('question');
            const value = $(this).data('value');
            
            // Save current question to history before moving forward
            if (questionQueue[currentStep] && !questionHistory.includes(currentStep)) {
                questionHistory.push(currentStep);
            }
            
            answers[questionId] = value;
            $(this).siblings().removeClass('selected');
            $(this).addClass('selected');
            setTimeout(showNextQuestion, 300);
        });
        
        $(document).on('click', '.question-tile.multi', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).toggleClass('selected');
            return false;
        });
        
        $(document).on('click', '#question-continue', function() {
            const questionId = $(this).data('question');
            const selected = [];
            $('.question-tile.multi.selected').each(function() {
                selected.push($(this).data('value'));
            });
            if (selected.length === 0) {
                alert('Please select at least one option');
                return;
            }
            
            // Save current question to history
            if (questionQueue[currentStep] && !questionHistory.includes(currentStep)) {
                questionHistory.push(currentStep);
            }
            
            answers[questionId] = selected;
            showNextQuestion();
        });
        
        $(document).on('click', '#question-back', function() {
            goBack();
        });
        
        $(document).on('click', '.upsell-toggle', function() {
            $(this).toggleClass('selected');
            const upsellId = $(this).data('upsell-id');
            const price = parseFloat($(this).data('price'));
            
            if ($(this).hasClass('selected')) {
                selectedUpsells.push({ id: upsellId, price: price });
            } else {
                selectedUpsells = selectedUpsells.filter(u => u.id !== upsellId);
            }
            
            updateQuoteTotal();
        });
        
        $(document).on('click', '#proceed-to-contact', function() {
            showContactForm();
        });
        
        $(document).on('click', '#quote-submit', submitQuote);
    }
    
    function buildQuestionQueue() {
        const flow = questionFlows[selectedService] || [];
        questionQueue = [];
        flow.forEach(function(q) {
            if (q.showIf) {
                const conditionKey = Object.keys(q.showIf)[0];
                const conditionValue = q.showIf[conditionKey];
                questionQueue.push({ ...q, condition: { key: conditionKey, value: conditionValue } });
            } else {
                questionQueue.push(q);
            }
        });
        currentStep = 0;
        selectedUpsells = [];
    }
    
    function showNextQuestion() {
        // Skip questions that don't meet conditions
        while (currentStep < questionQueue.length) {
            const q = questionQueue[currentStep];
            if (q.condition && answers[q.condition.key] !== q.condition.value) {
                currentStep++;
                continue;
            }
            break;
        }
        
        if (currentStep >= questionQueue.length) {
            showQuoteWithUpsells();
            return;
        }
        
        const q = questionQueue[currentStep];
        renderQuestion(q);
        updateProgress();
        currentStep++;
    }
    
    function renderQuestion(q) {
        const container = $('#quote-questions-container');
        
        // For yes/no questions, use a special 2-column layout with fixed positions
        let gridStyle = 'display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px;';
        if (q.yesNo) {
            // Force exactly 2 columns for yes/no so they're side by side
            gridStyle = 'display: grid; grid-template-columns: 1fr 1fr; gap: 16px; max-width: 400px; margin: 0 auto;';
        }
        
        let html = '<div class="question-step" data-step="' + currentStep + '" data-question-id="' + q.id + '">';
        html += '<h3 class="question-label" style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 32px; text-align: center;">' + q.label + '</h3>';
        
        if (q.type === 'single') {
            html += '<div style="' + gridStyle + '">';
            q.options.forEach(function(opt) {
                const isSelected = answers[q.id] === opt.value ? 'selected' : '';
                html += '<div class="question-tile single ' + isSelected + '" data-question="' + q.id + '" data-value="' + opt.value + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 32px 16px; text-align: center; cursor: pointer; transition: all 0.3s ease;">';
                html += '<div style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
                html += '</div>';
            });
            html += '</div>';
        } else if (q.type === 'multi') {
            html += '<div style="' + gridStyle + '">';
            q.options.forEach(function(opt) {
                const isSelected = (answers[q.id] || []).includes(opt.value) ? 'selected' : '';
                html += '<div class="question-tile multi ' + isSelected + '" data-question="' + q.id + '" data-value="' + opt.value + '" style="background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 16px; padding: 32px 16px; text-align: center; cursor: pointer; transition: all 0.3s ease;">';
                html += '<div style="font-weight: 600; font-size: 16px; color: var(--pgc-gray-700);">' + opt.label + '</div>';
                html += '</div>';
            });
            html += '</div>';
            html += '<div style="text-align: center; margin-top: 32px;">';
            html += '<button type="button" id="question-continue" data-question="' + q.id + '" class="pgc-btn pgc-btn-primary" style="padding: 16px 48px; font-size: 16px;">Continue</button>';
            html += '</div>';
        }
        html += '</div>';
        
        // Add back button (always show during questions)
        html += '<div style="margin-top: 24px; text-align: center;">';
        html += '<button type="button" id="question-back" class="pgc-btn pgc-btn-outline" style="padding: 10px 24px; font-size: 14px;">← Back</button>';
        html += '</div>';
        
        container.html(html);
        
        $('#quote-service-selection').hide();
        container.show();
        $('#quote-summary').hide();
        $('#quote-contact-form').hide();
    }
    
    function showQuoteWithUpsells() {
        calculatePrice(function(result) {
            availableUpsells = result.upsells || [];
            calculatedPrice = result.total_price;
            
            const container = $('#quote-summary');
            let html = '<div style="text-align: center; margin-bottom: 32px;">';
            html += '<h3 style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 8px;">Your Quote Summary</h3>';
            html += '<p style="color: var(--pgc-gray-500);">Review your price and add optional extras</p>';
            html += '</div>';
            
            html += '<div style="background: linear-gradient(135deg, rgba(8, 145, 178, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%); border: 2px solid rgba(8, 145, 178, 0.1); border-radius: 16px; padding: 32px; margin-bottom: 32px; text-align: center;">';
            html += '<p style="margin: 0 0 8px 0; color: #64748b; font-size: 14px;">Estimated Price</p>';
            html += '<div id="quote-total-price" style="font-size: 3.5rem; font-weight: 800; background: linear-gradient(135deg, var(--pgc-primary) 0%, var(--pgc-secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; line-height: 1;">£' + calculatedPrice.toFixed(2) + '</div>';
            html += '</div>';
            
            if (result.breakdown && result.breakdown.length > 0) {
                html += '<div style="margin-bottom: 32px;">';
                html += '<h4 style="font-size: 1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 16px;">Price Breakdown</h4>';
                result.breakdown.forEach(function(item) {
                    html += '<div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--pgc-gray-100);">';
                    html += '<span style="color: var(--pgc-gray-600);">' + item.item + '</span>';
                    html += '<span style="font-weight: 600; color: var(--pgc-gray-800);">£' + item.price.toFixed(2) + '</span>';
                    html += '</div>';
                });
                html += '</div>';
            }
            
            if (availableUpsells.length > 0) {
                html += '<div style="margin-bottom: 32px;">';
                html += '<h4 style="font-size: 1rem; font-weight: 600; color: var(--pgc-gray-700); margin-bottom: 16px;">Optional Extras</h4>';
                html += '<div style="display: grid; gap: 12px;">';
                availableUpsells.forEach(function(upsell) {
                    html += '<div class="upsell-toggle" data-upsell-id="' + upsell.id + '" data-price="' + upsell.price + '" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: var(--pgc-gray-50); border: 2px solid transparent; border-radius: 12px; cursor: pointer; transition: all 0.3s ease;">';
                    html += '<span style="font-weight: 500; color: var(--pgc-gray-700);">' + upsell.item + '</span>';
                    html += '<span style="font-weight: 700; color: var(--pgc-primary);">' + (upsell.price < 0 ? '-' : '+') + '£' + Math.abs(upsell.price).toFixed(2) + '</span>';
                    html += '</div>';
                });
                html += '</div>';
                html += '</div>';
            }
            
            html += '<div style="text-align: center;">';
            html += '<button type="button" id="proceed-to-contact" class="pgc-btn pgc-btn-primary" style="padding: 18px 48px; font-size: 18px;">I am happy to proceed →</button>';
            html += '</div>';
            html += '<div style="text-align: center; margin-top: 16px;">';
            html += '<button type="button" id="question-back" class="pgc-btn pgc-btn-outline" style="padding: 10px 24px; font-size: 14px;">← Back to Questions</button>';
            html += '</div>';
            
            container.html(html);
            
            $('#quote-service-selection').hide();
            $('#quote-questions-container').hide();
            container.show();
            $('#quote-contact-form').hide();
            updateProgress();
        });
    }
    
    function updateQuoteTotal() {
        let total = calculatedPrice;
        selectedUpsells.forEach(function(u) {
            total += u.price;
        });
        $('#quote-total-price').text('£' + total.toFixed(2));
    }
    
    function showContactForm() {
        let finalTotal = calculatedPrice;
        selectedUpsells.forEach(function(u) {
            finalTotal += u.price;
        });
        calculatedPrice = finalTotal;
        
        $('#quote-service-selection').hide();
        $('#quote-questions-container').hide();
        $('#quote-summary').hide();
        $('#quote-contact-form').show();
        updateProgress();
    }
    
    function goBack() {
        if ($('#quote-contact-form').is(':visible')) {
            // Go from contact form back to summary
            $('#quote-contact-form').hide();
            $('#quote-summary').show();
            updateProgress();
        } else if ($('#quote-summary').is(':visible')) {
            // Go from summary back to last question
            if (questionHistory.length > 0) {
                currentStep = questionHistory.pop();
                // Remove the answer for the current question so it can be re-answered
                const q = questionQueue[currentStep];
                if (q) {
                    delete answers[q.id];
                }
                showNextQuestion();
            } else {
                // No history, go back to service selection
                $('#quote-summary').hide();
                $('#quote-service-selection').show();
                currentStep = 0;
                updateProgress();
            }
        } else {
            // Going back through questions
            if (questionHistory.length > 0) {
                currentStep = questionHistory.pop();
                // Remove the answer for the question we're going back to
                const q = questionQueue[currentStep];
                if (q) {
                    delete answers[q.id];
                }
                showNextQuestion();
            } else {
                // No more history, go to service selection
                $('#quote-questions-container').hide();
                $('#quote-service-selection').show();
                currentStep = 0;
                updateProgress();
            }
        }
    }
    
    function updateProgress() {
        let current = currentStep;
        if ($('#quote-summary').is(':visible')) current = questionQueue.length + 1;
        if ($('#quote-contact-form').is(':visible')) current = questionQueue.length + 2;
        const total = questionQueue.length + 3;
        const percentage = (current / total) * 100;
        $('.quote-progress-fill').css('width', percentage + '%');
    }
    
    function calculatePrice(callback) {
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'pgc_calculate_quote_v2',
                nonce: pgc_ajax.nonce,
                service: selectedService,
                answers: JSON.stringify(answers),
            },
            success: function(response) {
                if (response.success) {
                    if (callback) callback(response.data);
                } else {
                    if (callback) callback({ total_price: 0, upsells: [], breakdown: [] });
                }
            },
            error: function() {
                if (callback) callback({ total_price: 0, upsells: [], breakdown: [] });
            }
        });
    }
    
    function submitQuote() {
        const required = ['first_name', 'last_name', 'email', 'phone', 'address_line1', 'postcode'];
        let valid = true;
        
        required.forEach(function(field) {
            const $input = $('#' + field);
            if (!$input.val()) {
                valid = false;
                $input.css('border-color', '#ef4444');
            } else {
                $input.css('border-color', '');
            }
        });
        
        if (!valid) {
            alert('Please fill in all required fields');
            return;
        }
        
        answers.selected_upsells = selectedUpsells;
        
        const formData = {
            action: 'pgc_submit_quote_v2',
            nonce: pgc_ajax.nonce,
            service: selectedService,
            answers: JSON.stringify(answers),
            calculated_price: calculatedPrice,
            first_name: $('#first_name').val(),
            last_name: $('#last_name').val(),
            email: $('#email').val(),
            phone: $('#phone').val(),
            address_line1: $('#address_line1').val(),
            address_line2: $('#address_line2').val(),
            postcode: $('#postcode').val(),
            heard_from: $('#heard_from').val(),
            notes: $('#notes').val(),
        };
        
        $('#quote-submit').text('Sending...').prop('disabled', true);
        
        $.ajax({
            url: pgc_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#quote-contact-form').html('<div style="text-align: center; padding: 60px 20px;"><div style="font-size: 64px; margin-bottom: 24px;">✓</div><h2 style="font-size: 1.75rem; font-weight: 800; color: var(--pgc-primary); margin-bottom: 16px;">Quote Submitted!</h2><p style="color: var(--pgc-gray-600); margin-bottom: 8px;">Your quote reference:</p><p style="font-size: 1.5rem; font-weight: 700; color: var(--pgc-gray-900); margin-bottom: 24px;">' + response.data.quote_id + '</p><p style="color: var(--pgc-gray-500);">Check your email for details. We will be in touch shortly!</p></div>');
                } else {
                    alert('Something went wrong. Please try again or call us.');
                    $('#quote-submit').text('Submit Quote Request').prop('disabled', false);
                }
            }
        });
    }
    
    $(document).ready(init);
    
})(jQuery);
