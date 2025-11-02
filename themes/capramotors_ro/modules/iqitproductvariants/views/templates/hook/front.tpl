{*
* 2017 IQIT-COMMERCE.COM
*
* NOTICE OF LICENSE
*
* This file is licenced under the Software License Agreement.
* With the purchase or the installation of the software in your application
* you accept the licence agreement
*
* @author    IQIT-COMMERCE.COM <support@iqit-commerce.com>
* @copyright 2017 IQIT-COMMERCE.COM
* @license   Commercial license (You can not resell or redistribute this software.)
*
*}

{$productsNb = $products|count}
{$visibleNb = 7}
{$visibleNbMobile = 7}
{$hiddenNb = $productsNb - $visibleNb}
{$hiddenNbMobile = $productsNb - $visibleNbMobile  }


{if $productsNb > 1 }
    <div class="iqitproductvariants js-iqitproductvariants mb-4 mt-4">
        <div class="fw-bold iqitproductvariants__label mb-2">
            {l s='Variants' d='Modules.Iqitproductvariants.Shop'}
        </div>

        <div class="iqitproductvariants__products d-flex flex-wrap">
            {foreach from=$products item=product key=key name=productsLoop}
                <div class="iqitproductvariants__product-col flex-grow-0">
                    <a href="{$product.url}"
                    {if $product.cover} 
                        {if $currentId != $product.id_product}
                        data-full-size-image-url="{$product.cover.bySize.large_default.url}" {/if} 
                    {/if}
                        class="iqitproductvariants__product {if $currentId == $product.id_product} iqitproductvariants__product--current{/if}
                     {if $smarty.foreach.productsLoop.index + 2 > $visibleNb && $productsNb > $visibleNb}iqitproductvariants__product--hidden-desktop{/if} 
                    {if $smarty.foreach.productsLoop.index + 2 > $visibleNbMobile && $productsNb > $visibleNbMobile}iqitproductvariants__product--hidden-mobile{/if} js-iqitproductvariants__product">
                        {if $product.cover}
                            <img src="{$product.cover.bySize.small_default.url}"
                                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:60:'...'}{/if}"
                                width="{$product.cover.bySize.small_default.width}"
                                height="{$product.cover.bySize.small_default.height}"
                                class="img-fluid js-iqitproductvariants__img {if $smarty.foreach.productsLoop.first} iqitproductvariants__img--first{/if}"
                                loading="lazy">
                        {else}
                            <img class="img-fluid {if $smarty.foreach.productsLoop.first} iqitproductvariants__img--first{/if}"
                                src="{$urls.no_picture_image.bySize.small_default.url}"
                                alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:60:'...'}{/if}"
                                width="{$urls.no_picture_image.small.small_default.width}"
                                height="{$urls.no_picture_image.bySize.small_default.height}" loading="lazy">
                        {/if}
                    </a>
                    {if $smarty.foreach.productsLoop.index + 1 == $visibleNb && $productsNb > $visibleNb}
                        <div class="iqitproductvariants__btn-more btn-secondary  js-iqitproductvariants__btn-more d-none d-xl-flex h6">
                            +{$hiddenNb + 1}</div>
                    {/if}

                    {if $smarty.foreach.productsLoop.index + 1 == $visibleNbMobile && $productsNb > $visibleNbMobile}
                        <div class="iqitproductvariants__btn-more btn-secondary js-iqitproductvariants__btn-more d-xl-none h6">
                            +{$hiddenNbMobile + 1}</div>
                    {/if}
                </div>
            {/foreach}
        </div>
    </div>
{/if}