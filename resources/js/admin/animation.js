const { createHigherOrderComponent } = wp.compose;
const { InspectorControls } = wp.blockEditor;
const { PanelBody, ToggleControl } = wp.components;
const { createElement: el, Fragment } = wp.element;

const addAnimationControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    const { attributes, setAttributes, name } = props;
    const allowedBlocks = ['core/heading', 'core/group', 'core/columns', 'core/column', 'core/image'];

    if (!allowedBlocks.includes(name)) return el(BlockEdit, props);

    return el(
      Fragment,
      null,
      el(BlockEdit, props),
      el(InspectorControls, null,
        el(PanelBody, { title: 'Animations' },
          el(ToggleControl, {
            label: "Animer l'apparition",
            checked: attributes.animateOnScroll,
            onChange: (val) => setAttributes({ animateOnScroll: val }),
          })
        )
      )
    );
  };
}, 'addAnimationControl');

wp.hooks.addFilter('editor.BlockEdit', 'my-plugin/animation-control', addAnimationControl);

function addAnimationClass(extraProps, blockType, attributes) {
  if (attributes.animateOnScroll) {
    extraProps.className = `${extraProps.className || ''} is-animated`.trim();
  }
  return extraProps;
}

wp.hooks.addFilter('blocks.getSaveContent.extraProps', 'my-plugin/add-class', addAnimationClass);
