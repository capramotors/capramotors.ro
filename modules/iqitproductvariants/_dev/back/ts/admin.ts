/* eslint-disable */
declare const $: any;
declare const prestashop: any;
declare global {
  interface Window {
    prestashop: any;
  }
}
/* eslint-enable */

export class IqitProductVariants {
  constructor() {}
  init() {
    new window.prestashop.component.EntitySearchInput($('#product_description_variants'), {
      onRemovedContent: () => {
        prestashop.component.EventEmitter.emit('updateSubmitButtonState');
      },
      onSelectedContent: () => {
        prestashop.component.EventEmitter.emit('updateSubmitButtonState');
      }
    });
  }
}

$(() => {
  const iqitExtendedProduct = new IqitProductVariants();
  iqitExtendedProduct.init();
});

export {};
