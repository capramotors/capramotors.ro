/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

import './front.scss';

class IqitProductVariants {
    constructor() {
        this.variantsWrapperEl = document.querySelector('.js-iqitproductvariants');

        if (!this.variantsWrapperEl) {
            return;
        }
        this.showMoreBtnEl = this.variantsWrapperEl.querySelectorAll('.js-iqitproductvariants__btn-more');
        this.productsEl = this.variantsWrapperEl.querySelectorAll('.js-iqitproductvariants__product');

        this.imageSrcBckp = '';

    }

    init() {
        if (!this.variantsWrapperEl) {
            return;
        }
        this.showBtnAction();
        this.imagePreview();
    }

    showBtnAction() {
        this.showMoreBtnEl.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
        
                // Usuń wszystkie przyciski z DOM
                this.showMoreBtnEl.forEach(button => {
                    button.remove();
                });
        
                // Wyświetl wszystkie produkty
                this.productsEl.forEach(product => {
                    product.classList.remove('iqitproductvariants__product--hidden-mobile');
                    product.classList.remove('iqitproductvariants__product--hidden-desktop');
                });
            });
        });
    }


    imagePreview() {
        this.productsEl.forEach(product => {
            product.addEventListener('mouseenter', (e) => {

                if (product.dataset.fullSizeImageUrl) {

                    let imagesWrapperEl = document.querySelector('#product-images-large');
                    let currImageEl = imagesWrapperEl.querySelector('.js-thumb-selected  img');

                    this.imageSrcBckp = currImageEl.src;
                    currImageEl.src = product.dataset.fullSizeImageUrl
                }

            });

            product.addEventListener('mouseleave', (e) => {

                if (product.dataset.fullSizeImageUrl) {

                    let imagesWrapperEl = document.querySelector('#product-images-large');
                    let currImageEl = imagesWrapperEl.querySelector('.js-thumb-selected   img');

                    currImageEl.src = this.imageSrcBckp;
                    this.imageSrcBckp = '';
                }

            });
        })
    }
   
}

document.addEventListener('DOMContentLoaded', function () {

    const iqitExtendedProduct = new IqitProductVariants();
    iqitExtendedProduct.init();

})

