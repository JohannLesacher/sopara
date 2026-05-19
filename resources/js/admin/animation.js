const {createHigherOrderComponent} = wp.compose;
const {InspectorControls} = wp.blockEditor;
const {PanelBody, ToggleControl, SelectControl} = wp.components;
const {createElement: el, Fragment} = wp.element;

const ANIMATION_OPTIONS = [
  {label: 'Fondu', value: 'fade'},
  {label: 'Fondu vers le haut', value: 'fade-up'},
  {label: 'Fondu depuis la gauche', value: 'fade-left'},
  {label: 'Fondu depuis la droite', value: 'fade-right'},
  {label: 'Zoom', value: 'scale'},
  {label: 'Texte par caractères', value: 'text-chars'},
];

const ALLOWED_BLOCKS = ['core/heading', 'core/group', 'core/columns', 'core/column', 'core/image', 'core/button', 'core/cover', 'core/accordion-item', 'sur-mesure/slide'];

const addAnimationControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    const {attributes, setAttributes, name} = props;

    if (!ALLOWED_BLOCKS.includes(name)) return el(BlockEdit, props);

    const {animateOnScroll, animationType} = attributes;

    return el(
      Fragment,
      null,
      el(BlockEdit, props),
      el(InspectorControls, null,
        el(PanelBody, {title: 'Animations'},
          el(ToggleControl, {
            label: "Animer l'apparition",
            checked: !!animateOnScroll,
            onChange: (val) => setAttributes({
              animateOnScroll: val,
              animationType: val ? (animationType || 'fade-up') : undefined,
            }),
          }),
          animateOnScroll && el(SelectControl, {
            label: "Type d'animation",
            value: animationType || 'fade-up',
            options: ANIMATION_OPTIONS,
            onChange: (val) => setAttributes({animationType: val}),
          })
        )
      )
    );
  };
}, 'addAnimationControl');

wp.hooks.addFilter('editor.BlockEdit', 'heat/animation-control', addAnimationControl);

function addAnimationProps(extraProps, blockType, attributes) {
  if (!attributes.animateOnScroll) return extraProps;

  extraProps.className = `${extraProps.className || ''} is-animated`.trim();
  extraProps['data-animation'] = attributes.animationType || 'fade-up';

  return extraProps;
}

wp.hooks.addFilter('blocks.getSaveContent.extraProps', 'heat/add-class', addAnimationProps);

wp.hooks.addFilter('blocks.registerBlockType', 'heat/attributes', (settings, name) => {
  if (!ALLOWED_BLOCKS.includes(name)) return settings;

  settings.attributes = {
    ...settings.attributes,
    animateOnScroll: {type: 'boolean', default: false},
    animationType: {type: 'string', default: 'fade-up'},
  };

  return settings;
});
