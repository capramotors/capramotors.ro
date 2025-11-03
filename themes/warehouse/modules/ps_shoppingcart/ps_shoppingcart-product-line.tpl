<div class="row no-gutters align-items-center">
    <div class="col-3">
        <span class="product-image media-middle">


            <a href="{$product.url}">
                {if $product.default_image}
                    <picture>
                        {if !empty($product.default_image.bySize.cart_default.sources.avif)}
                        <source srcset="{$product.default_image.bySize.cart_default.sources.avif}" type="image/avif">{/if}
                        {if !empty($product.default_image.bySize.cart_default.sources.webp)}
                        <source srcset="{$product.default_image.bySize.cart_default.sources.webp}" type="image/webp">{/if}
                        <img src="{$product.default_image.bySize.cart_default.url}" alt="{$product.name|escape:'quotes'}"
                            class="img-fluid" loading="lazy">
                    </picture>
                {else}
                    <picture>
                        {if !empty($urls.no_picture_image.bySize.cart_default.sources.avif)}
                        <source srcset="{$urls.no_picture_image.bySize.cart_default.sources.avif}" type="image/avif">{/if}
                        {if !empty($urls.no_picture_image.bySize.cart_default.sources.webp)}
                        <source srcset="{$urls.no_picture_image.bySize.cart_default.sources.webp}" type="image/webp">{/if}
                        <img src="{$urls.no_picture_image.bySize.cart_default.url}" class="img-fluid" loading="lazy" />
                    </picture>
                {/if}
            </a>

        </span>
    </div>
    <div class="col col-info">
        <div class="pb-1">
            <a href="{$product.url}">{$product.name}</a>
        </div>

        {block name='product_add_cart'}
            <div id="updated-add-cart-btn-{$product.id}-{$product.id_product_attribute}" class="d-none">
                {math equation='x - y' x=$product.quantity_available y=$product.quantity assign='max_quantity'}
                {include file='catalog/_partials/miniatures/_partials/product-miniature-btn.tpl' max_quantity=$max_quantity}
            </div>
        {/block}


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

        <div class="row align-items-center mt-2 no-gutters gap-2">
            <div class="col ps-0 ">
                {if isset($product.is_gift) && $product.is_gift}
                    <input class="form-control block-cart-product-quantity-gift" type="number" value="{$product.quantity}"
                        disabled />
                {else}
                    <input class="block-cart-product-quantity form-control js-cart-line-product-quantity"
                        data-down-url="{$product.down_quantity_url}" data-up-url="{$product.up_quantity_url}"
                        data-update-url="{$product.update_quantity_url}"
                        data-product-id="{$product.id_product|escape:'javascript'}"
                        data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
                        data-id-product="{$product.id_product|escape:'javascript'}" data-link-place="cart-preview"
                        type="number" value="{$product.quantity}" name="product-quantity-spin"
                        min="{$product.minimal_quantity}" />
                {/if}
            </div>
            <div class="col p-0 text-right">
            <span>{$product.price}</span>

                <a class="remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}"
                    data-link-action="delete-from-cart" data-link-place="cart-preview"
                    data-id-product="{$product.id_product|escape:'javascript'}"
                    data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
                    data-id-customization="{$product.id_customization|escape:'javascript'}"
                    data-new-add-btn='{include file='catalog/_partials/miniatures/_partials/product-miniature-btn.tpl' max_quantity=$product.quantity_available}'
                    title="{l s='remove from cart' d='Shop.Theme.Actions'}">
                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                </a>

                
            </div>
        </div>

    </div>
</div>