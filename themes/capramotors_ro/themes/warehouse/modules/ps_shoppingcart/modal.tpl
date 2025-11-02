<div id="blockcart-modal-wrap">
    <div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                {if $product}

                    <div class="modal-header">
                        <span class="modal-title"><i class="fa fa-check rtl-no-flip" aria-hidden="true"></i>
                            {l s='Product successfully added to your shopping cart' d='Shop.Theme.Checkout'}</span>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row align-items-center">
                    <div class=" col-md-5 divide-right mb-1">
                        <div class="row no-gutters align-items-center">



                            <div class="col-6 text-center">
                                <a href="{$product.url}">
                                    {if $product.default_image}
                                        <picture>
                                            {if !empty($product.default_image.bySize.medium_default.sources.avif)}<source srcset="{$product.default_image.bySize.medium_default.sources.avif}" type="image/avif">{/if}
                                            {if !empty($product.default_image.bySize.medium_default.sources.webp)}<source srcset="{$product.default_image.bySize.medium_default.sources.webp}" type="image/webp">{/if}
                                            <img src="{$product.default_image.bySize.medium_default.url}"
                                                data-full-size-image-url="{$product.default_image.large.url}"
                                                title="{$product.default_image.legend}" alt="{$product.default_image.legend}"
                                                loading="lazy" class="product-image img-fluid">
                                        </picture>
                                    {else}
                                        <picture>
                                            {if !empty($urls.no_picture_image.bySize.medium_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.medium_default.sources.avif}" type="image/avif">{/if}
                                            {if !empty($urls.no_picture_image.bySize.medium_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.medium_default.sources.webp}" type="image/webp">{/if}
                                            <img src="{$urls.no_picture_image.bySize.medium_default.url}" loading="lazy"
                                                class="product-image img-fluid" />
                                        </picture>       
                                    {/if}
                                </a>
                            </div>


                            <div class="col col-info">
                                <div class="pb-1">
                                    <span class="product-name"><a href="{$product.url}">{$product.name}</a></span>
                                </div>
                                {if isset($product.attributes) && $product.attributes}
                                <div class="product-attributes text-muted pb-1">
                                    {foreach from=$product.attributes key="attribute" item="value"}
                                    <div class="product-line-info">
                                        <span class="label">{$attribute}:</span>
                                        <span class="value">{$value}</span>
                                    </div>
                                    {/foreach}
                                </div>
                                {/if}
                                <span class="text-muted">{$product.quantity} x</span> <span>{$product.price}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="cart-content pt-3">
                            {if $cart.products_count > 1}
                            <p class="cart-products-count">
                                {l s='There are %products_count% items in your cart.' sprintf=['%products_count%' => $cart.products_count] d='Shop.Theme.Checkout'}
                            </p>
                            {else}
                            <p class="cart-products-count">
                                {l s='There is %product_count% item in your cart.' sprintf=['%product_count%' =>$cart.products_count] d='Shop.Theme.Checkout'}
                            </p>
                            {/if}


                            <div class="cart-subtotals">
                                {foreach from=$cart.subtotals item="subtotal"}
                                {if (isset($subtotal.value) && $subtotal.value) && (isset($subtotal.type) && $subtotal.type !== 'tax')}
                                <div>
                                    <strong>
                                        {if 'products' == $subtotal.type}
                                        {$cart.summary_string}
                                        {else}
                                        {$subtotal.label}
                                        {/if}
                                        :</strong>
                                    <span class="value">
                                        {if 'discount' == $subtotal.type}-&nbsp;{/if}{$subtotal.value}
                                    </span>
                                    {if $subtotal.type === 'shipping'}
                                    <div><small
                                            class="value">{hook h='displayCheckoutSubtotalDetails' subtotal=$subtotal}</small>
                                    </div>
                                    {/if}
                                </div>
                                {/if}
                                {/foreach}
                            </div>

                            {if isset($configuration.display_prices_tax_incl)}
                            {if !$configuration.display_prices_tax_incl && $configuration.taxes_enabled}
                            <div class="clearfix mb-2">
                                <strong>{$cart.totals.total.label}&nbsp;{if $configuration.display_taxes_label && $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}:</strong>
                                <span class="value">{$cart.totals.total.value}</span>
                            </div>
                            <div class="clearfix mb-2">
                                <strong>{$cart.totals.total_including_tax.label}:</strong>
                                <span class="value">{$cart.totals.total_including_tax.value}</span>
                            </div>
                            {else}
                            <div class="clearfix mb-2">
                                <strong>{$cart.totals.total.label}&nbsp;{if $configuration.display_taxes_label && $configuration.taxes_enabled}{$cart.labels.tax_short}{/if}:</strong>
                                <span class="value">{$cart.totals.total.value}</span>
                            </div>
                            {/if}
                            {else}
                            <div class="clearfix mb-2">
                                <strong>{$cart.totals.total.label} {$cart.labels.tax_short}:</strong>
                                <span class="value">{$cart.totals.total.value}</span>
                            </div>
                            {/if}


                            {hook h='displayCartAjaxInfo'}
                            {hook h='displayCartAjaxInfoModal'}
                            {hook h='displayCartModalContent' product=$product}
                            <div class="cart-content-btn">
                                <a href="{$cart_url}"
                                    class="btn btn-primary btn-block btn-lg mb-2">{l s='Proceed to checkout' d='Shop.Theme.Actions'}</a>
                                <button type="button" class="btn btn-secondary btn-block"
                                    data-bs-dismiss="modal">{l s='Continue shopping' d='Shop.Theme.Actions'}</button>
                            </div>
                        </div>
                    </div>
                </div>

                {hook h='displayModalCartCrosseling' product=$product}
                {hook h='displayCartModalFooter' product=$product}


            </div>

            {else}
            <div class="modal-header">
                <span class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    {l s='There are not enough products in stock' d='Shop.Theme.Checkout'}</span>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="cart-content">
                            <div class="cart-content-btn">
                                <button type="button" class="btn btn-secondary btn-block"
                                    data-bs-dismiss="modal">{l s='Continue shopping' d='Shop.Theme.Actions'}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/if}

        </div>
    </div>
</div>

<div id="blockcart-notification" class="ns-box {if !$product}ns-box-danger{/if} ns-effect-thumbslider">
    <div class="ns-box-inner row align-items-center small-gutters">
        {if $product}
        <div class="ns-thumb col-3">
            {if $product.default_image}
            <img src="{$product.default_image.bySize.small_default.url}"
                data-full-size-image-url="{$product.default_image.large.url}" title="{$product.default_image.legend}"
                alt="{$product.default_image.legend}" loading="lazy" class="product-image img-fluid">
            {else}
            <img src="{$urls.no_picture_image.bySize.small_default.url}" loading="lazy"
                class="product-image img-fluid" />
            {/if}
        </div>
        <div class="ns-content col-9">
            <span class="ns-title"><i class="fa fa-check" aria-hidden="true"></i> <strong>{$product.name}</strong>
                {l s='is added to your shopping cart' d='Shop.Warehousetheme'}</span>
        </div>
        <div class="ns-delivery col-12 mt-4">{hook h='displayCartAjaxInfo'}</div>
        {else}
        <div class="ns-content col-12">
            <span class="ns-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    {l s='There are not enough products in stock' d='Shop.Theme.Checkout'}</span>
            </div>
        {/if}

    </div>
</div>
</div>