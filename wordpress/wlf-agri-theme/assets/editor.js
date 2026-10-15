(function (wp) {
    'use strict';
    const supported = ['core/heading', 'core/paragraph', 'core/button'];
    wp.hooks.addFilter('blocks.registerBlockType', 'wlf/translations', function (settings, name) {
        if (!supported.includes(name)) return settings;
        return Object.assign({}, settings, { attributes: Object.assign({}, settings.attributes, {
            wlfEs: { type: 'string', default: '' }, wlfRu: { type: 'string', default: '' }
        }) });
    });
    const el = wp.element.createElement;
    wp.hooks.addFilter('editor.BlockEdit', 'wlf/translation-controls', wp.compose.createHigherOrderComponent(function (BlockEdit) {
        return function (props) {
            if (!supported.includes(props.name)) return el(BlockEdit, props);
            return el(wp.element.Fragment, null,
                el(BlockEdit, props),
                props.isSelected && el(wp.blockEditor.InspectorControls, null,
                    el(wp.components.PanelBody, { title: 'WLF — Dil çevirileri', initialOpen: true },
                        el('p', null, 'İngilizceyi doğrudan blokta düzenleyin. Çeviri boşsa İngilizce gösterilir.'),
                        el(wp.components.TextareaControl, { label: 'Español — İspanyolca', value: props.attributes.wlfEs || '', onChange: function (value) { props.setAttributes({ wlfEs: value }); } }),
                        el(wp.components.TextareaControl, { label: 'Русский — Rusça', value: props.attributes.wlfRu || '', onChange: function (value) { props.setAttributes({ wlfRu: value }); } })
                    )
                )
            );
        };
    }, 'WLFTranslationControls'));
})(window.wp);
