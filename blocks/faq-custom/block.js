/**
 * Custom FAQ Block
 */
(function(wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var RichText = wp.blockEditor.RichText;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var Button = wp.components.Button;
    var ToggleControl = wp.components.ToggleControl;
    var el = wp.element.createElement;

    registerBlockType('progreenclean/faq-custom', {
        title: 'Custom FAQ',
        icon: 'editor-help',
        category: 'progreenclean',
        
        attributes: {
            title: {
                type: 'string',
                default: 'Frequently Asked Questions'
            },
            description: {
                type: 'string',
                default: ''
            },
            faqs: {
                type: 'array',
                default: [
                    { question: 'What areas do you cover?', answer: 'We provide cleaning services throughout Surrey and South London.' },
                    { question: 'Are you insured?', answer: 'Yes, we are fully insured for all types of cleaning work.' }
                ]
            },
            showSchema: {
                type: 'boolean',
                default: true
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var updateFaq = function(index, field, value) {
                var newFaqs = attributes.faqs.slice();
                newFaqs[index] = Object.assign({}, newFaqs[index], { [field]: value });
                setAttributes({ faqs: newFaqs });
            };

            var addFaq = function() {
                var newFaqs = attributes.faqs.slice();
                newFaqs.push({ question: 'New Question', answer: 'Answer text here...' });
                setAttributes({ faqs: newFaqs });
            };

            var removeFaq = function(index) {
                var newFaqs = attributes.faqs.slice();
                newFaqs.splice(index, 1);
                setAttributes({ faqs: newFaqs });
            };

            return el('div', { className: 'pgc-faq-section-editor' }, [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'FAQ Settings', initialOpen: true }, [
                        el(ToggleControl, {
                            label: 'Show Schema Markup (SEO)',
                            checked: attributes.showSchema,
                            onChange: function(value) {
                                setAttributes({ showSchema: value });
                            }
                        })
                    ])
                ),
                el(TextControl, {
                    label: 'Section Title',
                    value: attributes.title,
                    onChange: function(value) {
                        setAttributes({ title: value });
                    }
                }),
                el(TextControl, {
                    label: 'Section Description (optional)',
                    value: attributes.description,
                    onChange: function(value) {
                        setAttributes({ description: value });
                    }
                }),
                el('h4', { style: { marginTop: '20px' } }, 'Questions & Answers'),
                el('div', { className: 'pgc-faq-list' },
                    attributes.faqs.map(function(faq, index) {
                        return el('div', { 
                            key: index, 
                            className: 'pgc-faq-item-editor',
                            style: { 
                                border: '1px solid #ddd', 
                                padding: '15px', 
                                marginBottom: '10px',
                                background: '#fff',
                                borderRadius: '4px'
                            }
                        }, [
                            el(TextControl, {
                                label: 'Question',
                                value: faq.question,
                                onChange: function(value) {
                                    updateFaq(index, 'question', value);
                                }
                            }),
                            el(TextControl, {
                                label: 'Answer',
                                value: faq.answer,
                                multiline: true,
                                rows: 3,
                                onChange: function(value) {
                                    updateFaq(index, 'answer', value);
                                }
                            }),
                            el(Button, {
                                isDestructive: true,
                                onClick: function() {
                                    removeFaq(index);
                                }
                            }, 'Remove FAQ')
                        ]);
                    })
                ),
                el(Button, {
                    isPrimary: true,
                    onClick: addFaq,
                    style: { marginTop: '10px' }
                }, 'Add FAQ')
            ]);
        },

        save: function() {
            return null; // Rendered via PHP
        }
    });
})(window.wp);
