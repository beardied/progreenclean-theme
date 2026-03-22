/**
 * Hero Block
 */
(function(wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var RangeControl = wp.components.RangeControl;
    var Button = wp.components.Button;
    var el = wp.element.createElement;

    registerBlockType('progreenclean/hero', {
        title: 'Hero Section',
        icon: 'format-image',
        category: 'progreenclean',
        
        attributes: {
            headline: {
                type: 'string',
                default: 'Professional Cleaning Services'
            },
            subheadline: {
                type: 'string',
                default: 'Eco-friendly cleaning for homes and businesses in Surrey'
            },
            primaryCtaText: {
                type: 'string',
                default: 'Get Your Free Quote'
            },
            primaryCtaUrl: {
                type: 'string',
                default: '/get-a-quote/'
            },
            secondaryCtaText: {
                type: 'string',
                default: 'Our Services'
            },
            secondaryCtaUrl: {
                type: 'string',
                default: '/services/'
            },
            backgroundType: {
                type: 'string',
                default: 'gradient'
            },
            backgroundImage: {
                type: 'object',
                default: null
            },
            backgroundColor: {
                type: 'string',
                default: ''
            },
            overlayOpacity: {
                type: 'number',
                default: 40
            },
            trustBar: {
                type: 'boolean',
                default: true
            },
            size: {
                type: 'string',
                default: 'large'
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', {}, [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Hero Content', initialOpen: true }, [
                        el(TextControl, {
                            label: 'Headline',
                            value: attributes.headline,
                            onChange: function(value) {
                                setAttributes({ headline: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Subheadline',
                            value: attributes.subheadline,
                            onChange: function(value) {
                                setAttributes({ subheadline: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Primary CTA Text',
                            value: attributes.primaryCtaText,
                            onChange: function(value) {
                                setAttributes({ primaryCtaText: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Primary CTA URL',
                            value: attributes.primaryCtaUrl,
                            onChange: function(value) {
                                setAttributes({ primaryCtaUrl: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Secondary CTA Text (optional)',
                            value: attributes.secondaryCtaText,
                            onChange: function(value) {
                                setAttributes({ secondaryCtaText: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Secondary CTA URL',
                            value: attributes.secondaryCtaUrl,
                            onChange: function(value) {
                                setAttributes({ secondaryCtaUrl: value });
                            }
                        })
                    ]),
                    el(PanelBody, { title: 'Background Settings' }, [
                        el(SelectControl, {
                            label: 'Background Type',
                            value: attributes.backgroundType,
                            options: [
                                { label: 'Gradient', value: 'gradient' },
                                { label: 'Image', value: 'image' },
                                { label: 'Color', value: 'color' }
                            ],
                            onChange: function(value) {
                                setAttributes({ backgroundType: value });
                            }
                        }),
                        attributes.backgroundType === 'image' && el(MediaUpload, {
                            onSelect: function(media) {
                                setAttributes({ backgroundImage: media });
                            },
                            allowedTypes: ['image'],
                            render: function(obj) {
                                return el(Button, {
                                    onClick: obj.open,
                                    isPrimary: true
                                }, attributes.backgroundImage ? 'Change Image' : 'Upload Image');
                            }
                        }),
                        attributes.backgroundType === 'color' && el(TextControl, {
                            label: 'Background Color (hex)',
                            value: attributes.backgroundColor,
                            onChange: function(value) {
                                setAttributes({ backgroundColor: value });
                            }
                        }),
                        el(RangeControl, {
                            label: 'Overlay Opacity (%)',
                            value: attributes.overlayOpacity,
                            onChange: function(value) {
                                setAttributes({ overlayOpacity: value });
                            },
                            min: 0,
                            max: 100
                        })
                    ]),
                    el(PanelBody, { title: 'Display Options' }, [
                        el(SelectControl, {
                            label: 'Hero Size',
                            value: attributes.size,
                            options: [
                                { label: 'Small', value: 'small' },
                                { label: 'Medium', value: 'medium' },
                                { label: 'Large', value: 'large' }
                            ],
                            onChange: function(value) {
                                setAttributes({ size: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: 'Show Trust Bar',
                            checked: attributes.trustBar,
                            onChange: function(value) {
                                setAttributes({ trustBar: value });
                            }
                        })
                    ])
                ),
                el('div', {
                    className: 'pgc-hero-editor',
                    style: {
                        background: attributes.backgroundType === 'gradient' 
                            ? 'linear-gradient(135deg, #0891b2 0%, #10b981 100%)'
                            : (attributes.backgroundType === 'color' && attributes.backgroundColor 
                                ? attributes.backgroundColor 
                                : '#0891b2'),
                        padding: '60px 40px',
                        textAlign: 'center',
                        color: '#fff',
                        borderRadius: '8px'
                    }
                }, [
                    el('h2', { 
                        style: { 
                            fontSize: '32px', 
                            fontWeight: 'bold',
                            marginBottom: '16px'
                        } 
                    }, attributes.headline),
                    el('p', { 
                        style: { 
                            fontSize: '18px', 
                            marginBottom: '24px',
                            opacity: 0.9
                        } 
                    }, attributes.subheadline),
                    el('div', {}, [
                        el('span', {
                            style: {
                                display: 'inline-block',
                                background: '#10b981',
                                color: '#fff',
                                padding: '12px 24px',
                                borderRadius: '8px',
                                marginRight: '10px',
                                fontWeight: '600'
                            }
                        }, attributes.primaryCtaText),
                        attributes.secondaryCtaText && el('span', {
                            style: {
                                display: 'inline-block',
                                background: '#fff',
                                color: '#0891b2',
                                padding: '12px 24px',
                                borderRadius: '8px',
                                fontWeight: '600'
                            }
                        }, attributes.secondaryCtaText)
                    ])
                ])
            ]);
        },

        save: function() {
            return null; // Rendered via PHP
        }
    });
})(window.wp);
