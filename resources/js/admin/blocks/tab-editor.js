import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { createElement as el, Fragment } from '@wordpress/element';

registerBlockType('sur-mesure/tab', {
  apiVersion: 3,
  title: 'Tab',
  icon: 'images-alt2',
  category: 'common',
  parent: ['sur-mesure/tabs'],
  attributes: {
    title: { type: 'string', default: 'Titre onglet' },
    showFirstChildInHeaderMobile: { type: 'boolean', default: false },
  },
  supports: {
    color: { background: true, text: true },
    spacing: { blockGap: true },
    layout: { allowEditing: false, default: 'flex' },
  },

  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps();
    const { title, showFirstChildInHeaderMobile } = attributes;
    const bg = blockProps.style?.backgroundColor;

    return el(
      Fragment,
      {},
      el(
        InspectorControls,
        {},
        el(
          PanelBody,
          { title: 'Réglages', initialOpen: true },
          el(ToggleControl, {
            label: 'Inclure premier enfant direct dans le header sur mobile',
            help: 'Affiche le premier bloc enfant dans le header de la tab sur mobile, avant ouverture.',
            checked: !!showFirstChildInHeaderMobile,
            onChange: (val) => setAttributes({ showFirstChildInHeaderMobile: val }),
          }),
        ),
      ),
      el(
        'div',
        blockProps,
        el(
          'div',
          { style: { backgroundColor: '#fff', paddingTop: '2rem' } },
          el(
            'header',
            {
              style: {
                backgroundColor: bg,
                padding: '1rem',
                borderRadius: '0.5rem 0.5rem 0 0',
                position: 'relative',
                width: 'max-content',
                maxWidth: '40ch',
                marginLeft: '1.5rem',
              },
            },
            el(RichText, {
              tagName: 'p',
              value: title,
              onChange: (newTitle) => setAttributes({ title: newTitle }),
              placeholder: "Titre de l'onglet",
              style: { display: 'block', fontSize: '1rem' },
            }),
          ),
        ),
        el(
          'div',
          { style: { backgroundColor: bg, padding: '1.5rem' } },
          el(InnerBlocks, {}),
        ),
      ),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});
