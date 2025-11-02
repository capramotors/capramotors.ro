/**
 * Copyright since 2025 iqit-commerce.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Envato Regular License,
 * which is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at the following URL:
 * https://themeforest.net/licenses/terms/regular
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@iqit-commerce.com so we can send you a copy immediately.
 *
 * @author    iqit-commerce.com <support@iqit-commerce.com>
 * @copyright Since 2025 iqit-commerce.com
 * @license   Envato Regular License
 */
export class IqitProductVariants {
  private variantsWrapperEl: HTMLElement | null;
  private showMoreBtnEl: NodeListOf<HTMLElement>;
  private productsEl: NodeListOf<HTMLElement>;
  private imageSrcBckp: string;

  constructor() {
    this.variantsWrapperEl = document.querySelector('.js-iqitproductvariants');

    if (!this.variantsWrapperEl) {
      this.showMoreBtnEl = document.querySelectorAll('.js-iqitproductvariants__btn-more');
      this.productsEl = document.querySelectorAll('.js-iqitproductvariants__product');
      this.imageSrcBckp = '';
      return;
    }

    this.showMoreBtnEl = this.variantsWrapperEl.querySelectorAll(
      '.js-iqitproductvariants__btn-more'
    );
    this.productsEl = this.variantsWrapperEl.querySelectorAll('.js-iqitproductvariants__product');
    this.imageSrcBckp = '';
  }

  init(): void {
    if (!this.variantsWrapperEl) {
      return;
    }
    this.showBtnAction();
    this.imagePreview();
  }

  private showBtnAction(): void {
    this.showMoreBtnEl.forEach((btn) => {
      btn.addEventListener('click', (e: Event) => {
        e.preventDefault();

        // Usuń wszystkie przyciski z DOM
        this.showMoreBtnEl.forEach((button) => {
          button.remove();
        });

        // Wyświetl wszystkie produkty
        this.productsEl.forEach((product) => {
          product.classList.remove('iqitproductvariants__product--hidden-mobile');
          product.classList.remove('iqitproductvariants__product--hidden-desktop');
        });
      });
    });
  }

  private imagePreview(): void {
    this.productsEl.forEach((product) => {
      product.addEventListener('mouseenter', () => {
        const fullSizeImageUrl = product.getAttribute('data-full-size-image-url');
        if (fullSizeImageUrl) {
          const imagesWrapperEl = document.querySelector(
            '#product-images-large'
          ) as HTMLElement | null;
          const currImageEl = imagesWrapperEl?.querySelector(
            '.js-thumb-selected img'
          ) as HTMLImageElement | null;

          if (currImageEl) {
            this.imageSrcBckp = currImageEl.src;
            currImageEl.src = fullSizeImageUrl;
          }
        }
      });

      product.addEventListener('mouseleave', () => {
        if (this.imageSrcBckp) {
          const imagesWrapperEl = document.querySelector(
            '#product-images-large'
          ) as HTMLElement | null;
          const currImageEl = imagesWrapperEl?.querySelector(
            '.js-thumb-selected img'
          ) as HTMLImageElement | null;

          if (currImageEl) {
            currImageEl.src = this.imageSrcBckp;
            this.imageSrcBckp = '';
          }
        }
      });
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const iqitExtendedProduct = new IqitProductVariants();
  iqitExtendedProduct.init();
});

export {};
