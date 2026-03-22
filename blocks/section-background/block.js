/**
 * Section Background Block
 */
(function(wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var InnerBlocks = wp.blockEditor.InnerBlocks;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var el = wp.element.createElement;

    registerBlockType('progreenclean/section-background', {
        title: 'Section Background',
        icon: 'cover-image',
        category: 'progreenclean',
        
        attributes: {
            backgroundType: {
                type: 'string',
                default: 'gradient'
            },
            customBackground: {
                type: 'string',
                default: ''
            },
            paddingSize: {
                type: 'string',
                default: 'large'
            },
            marginTop: {
                type: 'string',
                default: '0'
            },
            marginBottom: {
                type: 'string',
                default: '0'
            }
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            var backgroundClass = 'pgc-section pgc-section--' + attributes.backgroundType;
            var paddingClass = 'pgc-section--padding-' + attributes.paddingSize;

            var backgroundStyle = {};
            if (attributes.backgroundType === 'custom' && attributes.customBackground) {
                backgroundStyle.background = attributes.customBackground;
            }

            return el('div', {}, [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Background Settings', initialOpen: true }, [
                        el(SelectControl, {
                            label: 'Background Type',
                            value: attributes.backgroundType,
                            options: [
                                { label: 'Gradient (Blue to Green)', value: 'gradient' },
                                { label: 'Blue', value: 'blue' },
                                { label: 'Gray', value: 'gray' },
                                { label: 'White', value: 'white' },
                                { label: 'Custom', value: 'custom' }
                            ],
                            onChange: function(value) {
                                setAttributes({ backgroundType: value });
                            }
                        }),
                        attributes.backgroundType === 'custom' && el(TextControl, {
                            label: 'Custom Background (CSS)',
                            value: attributes.customBackground,
                            onChange: function(value) {
                                setAttributes({ customBackground: value });
                            }
                        }),
                        el(SelectControl, {
                            label: 'Padding Size',
                            value: attributes.paddingSize,
                            options: [
                                { label: 'Small (40px)', value: 'small' },
                                { label: 'Medium (60px)', value: 'medium' },
                                { label: 'Large (100px)', value: 'large' }
                            ],
                            onChange: function(value) {
                                setAttributes({ paddingSize: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Margin Top',
                            value: attributes.marginTop,
                            onChange: function(value) {
                                setAttributes({ marginTop: value });
                            }
                        }),
                        el(TextControl, {
                            label: 'Margin Bottom',
                            value: attributes.marginBottom,
                            onChange: function(value) {
                                setAttributes({ marginBottom: value });
                            }
                        })
                    ])
                ),
                el('section', {
                    className: backgroundClass + ' ' + paddingClass,
                    style: backgroundStyle
                },
                    el('div', { className: 'pgc-container' },
                        el(InnerBlocks, {
                            allowedBlocks: ['core/heading', 'core/paragraph', 'core/buttons', 'core/columns', 'core/media-text']
                        })
                    )
                )
            ]);
        },

        save: function() {
            return el(InnerBlocks.Content);
        }
    });
})(window.wp);
