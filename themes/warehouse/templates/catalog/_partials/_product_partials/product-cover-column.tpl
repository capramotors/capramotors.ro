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
{block name='product_cover'}
    <div class="product-cover">

        {include file='catalog/_partials/product-flags.tpl'}






        <div id="product-images-large" class="product-images-large swiper swiper-container column-images">
            <div id="swiper-wrapper-column-images" class="swiper-wrapper">
            {hook h='displayAsFirstProductImage' product=$product imageCarusel='large'}
                {if $product.images}
                    {foreach from=$product.images item=image name=covers}
                        <div class="product-lmage-large swiper-slide  {if $image.id_image == $product.default_image.id_image} js-thumb-selected{/if}">
                            <div class="easyzoom easyzoom-product">
                                <a href="{if !empty($image.large.sources.webp)}{$image.large.sources.webp}{elseif !empty($image.large.sources.avif)}{$image.large.sources.avif}{else}{$image.large.url}{/if}" class="js-easyzoom-trigger" rel="nofollow"></a>
                            </div>
                            {if $product.images}<a class="expander" data-bs-toggle="modal"  data-image-large-src="{$image.large.url}" data-bs-target="#product-modal"><span><i class="fa fa-expand" aria-hidden="true"></i></span></a>{/if}
                            <picture>
                            {if !empty($image.bySize.large_default.sources.avif)}<source srcset="{$image.bySize.large_default.sources.avif}" type="image/avif">{/if}
                            {if !empty($image.bySize.large_default.sources.webp)}<source srcset="{$image.bySize.large_default.sources.webp}" type="image/webp">{/if}
                            <img
                                    src="{$image.bySize.large_default.url}"
                                    data-image-large-src="{$image.large.url}" {if !empty($image.large.sources)}data-image-large-sources="{$image.large.sources|@json_encode}"{/if}
                                    alt="{if !empty($image.legend)}{$image.legend}{else}{$product.name|truncate:40:'...'}{/if}"
                                    title="{if !empty($image.legend)}{$image.legend}{else}{$product.name|truncate:40:'...'}{/if}"
                                    content="{$image.bySize.large_default.url}"
                                    width="{$image.bySize.large_default.width}"
                                    height="{$image.bySize.large_default.height}"
                                     loading="lazy"
                                    class="img-fluid swiper-lazy js-lazy-product-image"
                            >
                            </picture>
                        </div>
                    {/foreach}
                {else}
                    <div class="swiper-slide">
                        <picture>
                        {if !empty($urls.no_picture_image.bySize.large_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.avif}" type="image/avif">{/if}
                        {if !empty($urls.no_picture_image.bySize.large_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.large_default.sources.webp}" type="image/webp">{/if}
                        <img src="{$urls.no_picture_image.bySize.large_default.url}"
                             data-image-large-src="{$urls.no_picture_image.large.url}"
                             content="{$urls.no_picture_image.bySize.large_default.url}"
                             width="{$urls.no_picture_image.bySize.large_default.width}"
                             height="{$urls.no_picture_image.bySize.large_default.height}"
                             alt="{$product.name|truncate:40:'...'}"
                             class="img-fluid"
                        >
                        </picture>
                    </div>
                {/if}
                {hook h='displayAsLastProductImage' product=$product imageCarusel='large'}
            </div>
            <div class="swiper-button-prev swiper-button-inner-prev swiper-button-arrow no-quick-view-d-md-none"></div>
            <div class="swiper-button-next swiper-button-inner-next swiper-button-arrow no-quick-view-d-md-none"></div>
            <div class="swiper-pagination swiper-pagination-product no-quick-view-d-md-none"></div>
        </div>
    </div>
{/block}
