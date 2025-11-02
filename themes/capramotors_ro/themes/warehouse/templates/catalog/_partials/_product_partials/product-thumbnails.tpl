{**
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
 *}

{block name='product_images'}
    {if $product.images}
    <div class="js-qv-mask mask position-relative">
        <div id="product-images-thumbs" class="product-images js-qv-product-images swiper swiper-container swiper-cls-fix desktop-swiper-cls-fix-5 swiper-cls-row-fix-1 tablet-swiper-cls-fix-5 mobile-swiper-cls-fix-5 ">
            <div class="swiper-wrapper">
            {hook h='displayAsFirstProductImage' product=$product imageCarusel='thumb'}
            {foreach from=$product.images item=image name=thumbs}
               <div class="swiper-slide"> <div class="thumb-container js-thumb-container">
                    <picture>
                    {if !empty($image.bySize.medium_default.sources.avif)}<source srcset="{$image.bySize.medium_default.sources.avif}" type="image/avif">{/if}
                    {if !empty($image.bySize.medium_default.sources.webp)}<source srcset="{$image.bySize.medium_default.sources.webp}" type="image/webp">{/if}
                    <img
                            class="thumb js-thumb {if $image.id_image == $product.default_image.id_image} selected js-thumb-selected{/if}  img-fluid swiper-lazy"
                            data-image-medium-src="{$image.bySize.medium_default.url}"
                            data-image-large-src="{$image.large.url}" {if !empty($image.large.sources)}data-image-large-sources="{$image.large.sources|@json_encode}"{/if}
                            src="{$image.bySize.medium_default.url}"
                            {if !empty($image.legend)}
                                alt="{$image.legend}"
                                title="{$image.legend}"
                            {else}
                                alt="{$product.name}"
                            {/if}
                            title="{$image.legend}"
                            width="{$image.bySize.medium_default.width}"
                            height="{$image.bySize.medium_default.height}"
                            loading="lazy"
                    >
                    </picture>
                </div> </div>
            {/foreach}
            {hook h='displayAsLastProductImage' product=$product imageCarusel='thumb'}
            </div>
            <div class="swiper-button-prev swiper-button-inner-prev swiper-button-arrow"></div>
            <div class="swiper-button-next swiper-button-inner-next swiper-button-arrow"></div>
        </div>
    </div>
    {/if}
{/block}
