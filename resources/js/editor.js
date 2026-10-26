(function () {
  'use strict';

  const wp = window.wp;

  if (!wp || !wp.hooks || !wp.element) {
    return;
  }

  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, SelectControl, ToggleControl, TextControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;

  const catalogue = window.mpCodeLanguages || { '': 'Detectar automáticamente' };
  const options = Object.keys(catalogue).map((value) => ({
    value,
    label: catalogue[value],
  }));

  addFilter(
    'blocks.registerBlockType',
    'microplagio/code-attributes',
    (settings, name) => {
      if (name !== 'core/code') {
        return settings;
      }

      return {
        ...settings,
        attributes: {
          ...settings.attributes,
          language: { type: 'string', default: '' },
          showLineNumbers: { type: 'boolean', default: true },
          wrapLines: { type: 'boolean', default: false },
          highlightedLines: { type: 'string', default: '' },
        },
      };
    }
  );

  const withCodeControls = createHigherOrderComponent(
    (BlockEdit) => (props) => {
      if (props.name !== 'core/code') {
        return el(BlockEdit, props);
      }

      const { attributes, setAttributes } = props;

      return el(
        Fragment,
        null,
        el(BlockEdit, props),
        el(
          InspectorControls,
          null,
          el(
            PanelBody,
            {
              title: 'Resaltado de código',
              initialOpen: true,
              className: 'mp-code-inspector',
            },
            el(SelectControl, {
              label: 'Lenguaje',
              value: attributes.language || '',
              options,
              onChange: (value) => setAttributes({ language: value }),
            }),
            el(ToggleControl, {
              label: 'Números de línea',
              checked: attributes.showLineNumbers !== false,
              onChange: (value) => setAttributes({ showLineNumbers: value }),
            }),
            el(ToggleControl, {
              label: 'Ajustar líneas largas',
              checked: Boolean(attributes.wrapLines),
              onChange: (value) => setAttributes({ wrapLines: value }),
            }),
            el(TextControl, {
              label: 'Líneas resaltadas',
              help: 'Ejemplo: 2,5-8. Déjalo vacío para no resaltar ninguna.',
              value: attributes.highlightedLines || '',
              onChange: (value) => setAttributes({ highlightedLines: value }),
            })
          )
        )
      );
    },
    'withMicroplagioCodeControls'
  );

  addFilter('editor.BlockEdit', 'microplagio/code-controls', withCodeControls);
})();
