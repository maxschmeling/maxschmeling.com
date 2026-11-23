(function (blocks, element, editor) {
    blocks.registerBlockType('nasa-atlas/record-facts', {
        apiVersion: 3, title: 'NASA record facts', icon: 'info-outline', category: 'widgets',
        attributes: { archiveYear: { type: 'integer', default: 0 }, hasImage: { type: 'boolean', default: false } },
        usesContext: ['remote-data-blocks/remoteData'],
        edit: function (props) { return element.createElement('div', editor.useBlockProps({ className: 'atlas-record-facts' }), element.createElement('span', null, 'Archive year ', element.createElement('strong', null, props.attributes.archiveYear)), element.createElement('span', null, 'Image available ', element.createElement('strong', null, props.attributes.hasImage ? 'Yes' : 'No'))); },
        save: function () { return null; }
    });
    blocks.registerBlockType('nasa-atlas/search', {
        apiVersion: 3,
        title: 'NASA Atlas search form',
        icon: 'search', category: 'widgets',
        edit: function () { return element.createElement('div', editor.useBlockProps({ className: 'atlas-search-preview' }), element.createElement('strong', null, 'NASA archive search'), element.createElement('p', null, 'The public page shows a labeled search form and topic links. The NASA Archive block below opts into its search query.')); },
        save: function () { return null; }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor);
