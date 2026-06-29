import domReady from '@wordpress/dom-ready';
import { addFilter } from '@wordpress/hooks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';
import { Fragment, createElement } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';

addFilter(
  'blocks.registerBlockType',
  'sopara/accordion-faq-schema-attribute',
  (settings, name) => {
    if (name !== 'core/accordion') return settings;

    settings.attributes = {
      ...settings.attributes,
      addToFaqSchema: { type: 'boolean', default: false },
    };

    return settings;
  }
);

const withFaqSchemaControl = createHigherOrderComponent((BlockEdit) => (props) => {
  if (props.name !== 'core/accordion') {
    return createElement(BlockEdit, props);
  }

  const { attributes, setAttributes } = props;

  return createElement(
    Fragment,
    null,
    createElement(BlockEdit, props),
    createElement(
      InspectorControls,
      null,
      createElement(
        PanelBody,
        { title: 'Données structurées', initialOpen: false },
        createElement(ToggleControl, {
          label: 'Ajouter aux données structurées',
          help: 'Inclut les questions/réponses de cet accordéon dans le schema FAQPage.',
          checked: !!attributes.addToFaqSchema,
          onChange: (value) => setAttributes({ addToFaqSchema: value }),
        })
      )
    )
  );
}, 'withFaqSchemaControl');

addFilter('editor.BlockEdit', 'sopara/accordion-faq-schema-control', withFaqSchemaControl);

domReady(() => {
  wp.blocks.registerBlockStyle('core/accordion', {
    name: 'with-icon',
    label: 'Avec icône',
  });
});
