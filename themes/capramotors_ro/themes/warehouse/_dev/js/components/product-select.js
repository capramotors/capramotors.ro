/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';
import updateSources from 'update-sources';

export default class ProductSelect {
  init() {
    let easyzoomInstance;
    let carouselInstance;
    let $productThumbs;
    const $wrapper = $('#wrapper');

    $wrapper.on('show.bs.modal', '#product-modal', (e) => {
      let newSrc;
      let $sourcesProvider;
      let $modalCover = $('.js-modal-product-cover');

      if (iqitTheme.pp_image_layout === 'carousel') {
        $sourcesProvider = $('#product-images-large .swiper-slide-active img').first();
        newSrc = $sourcesProvider.data('image-large-src');
        
        updateSources(
          $modalCover,
          $sourcesProvider.data('image-large-sources'),
        );

      } else {
        $sourcesProvider = $(e.relatedTarget);
        newSrc = $sourcesProvider.data('image-large-src');

        updateSources(
          $modalCover,
          $sourcesProvider.data('image-large-sources'),
        );
  
      }

      $('.js-modal-product-cover-easyzoom').first().attr('href', newSrc);
      $modalCover.first().attr('src', newSrc);


     
      if (iqitTheme.pp_zoom === 'inner' || iqitTheme.pp_zoom === 'modalzoom') {
        const easyzoom = $('.easyzoom-modal').easyZoom();
        easyzoomInstance = easyzoom.data('easyZoom');
      } else {
        $wrapper.on('click', '.js-modal-product-cover-easyzoom', (event) => {
          event.preventDefault();
        });
      }
    });

    $wrapper.on('shown.bs.modal', '#product-modal', () => {
      $productThumbs = $('#modal-product-thumbs');

      if ($productThumbs.length) {
        carouselInstance = new Swiper($productThumbs[0], {
          loop: false,
          slidesPerView: 6,
          slidesPerGroup: 6,
          centerInsufficientSlides: true,
          navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
          },
          breakpoints: {
            768: {
              slidesPerView: 10,
              slidesPerGroup: 10,
            },
          },
        });
      }
    });

    $wrapper.on('hidden.bs.modal', '#product-modal', (e) => {
      if (iqitTheme.pp_zoom === 'inner' || iqitTheme.pp_zoom === 'modalzoom') {
        easyzoomInstance.teardown();
      }
      if (carouselInstance !== undefined) {
        carouselInstance.destroy();
      }
    });

    $('body').on('click', '.js-modal-thumb', (event) => {
      let $sourcesProvider;
      if (iqitTheme.pp_zoom === 'inner' || iqitTheme.pp_zoom === 'modalzoom') {
        $sourcesProvider = $(event.target);

        easyzoomInstance.swap($sourcesProvider.data('image-large-src'), $sourcesProvider.data('image-large-src'));
        let $modalCover = $('.js-modal-product-cover');
        updateSources(
          $modalCover,
          $sourcesProvider.data('image-large-sources'),
        );
        
      } else {
        let $modalCover = $('.js-modal-product-cover');
        $sourcesProvider = $(event.target)

        $modalCover.attr('src', $sourcesProvider.data('image-large-src'));

        updateSources(
          $modalCover,
          $sourcesProvider.data('image-large-sources'),
        );

      }
    });
  }
}
