{**
 * Manufacturer (Brand) page template - faceted search enabled
 * Forces left column layout to show faceted search even on full-width layouts
 *}
{extends file='catalog/listing/product-list.tpl'}

{* Override layout blocks to ensure left column shows *}
{block name='left_column'}
    <div id="left-column" class="col-12 col-md-3 {if $iqitTheme.g_sidebars_width == 'narrow'}col-lg-2{/if} order-md-first">
        {if $iqitTheme.h_layout != 6 && $iqitTheme.h_layout != 7}{hook h="displayVerticalMenu"}{/if}
        {hook h="displayLeftColumn"}
    </div>
{/block}

{* Adjust content wrapper to work with left column *}
{block name='content_wrapper'}
    <div id="content-wrapper" class="js-content-wrapper left-column col-12 col-md-9 {if $iqitTheme.g_sidebars_width == 'narrow'}col-lg-10{/if}">
        {$smarty.block.parent}
    </div>
{/block}

{* Ensure row structure exists *}
{block name='layout_row_start'}
    <div class="row">
{/block}

{block name='layout_row_end'}
    </div>
{/block}

{block name='product_list_header'}
    <h1 class="h1 page-title">
        <span>{l s='List of products by brand %brand_name%' sprintf=['%brand_name%' => $manufacturer.name] d='Shop.Theme.Catalog'}</span></h1>

    {if $manufacturer.short_description || $manufacturer.description}
    <div id="manufacturer-description-wrapper" class="mb-3">
    {if $manufacturer.short_description}
        <div class="card">
        <div id="manufacturer-short-description" class="rte-content">
                {$manufacturer.short_description nofilter}

                {if $manufacturer.description}
                    <a class="btn btn-secondary btn-brands-more float-right collapsed "  data-bs-toggle="collapse" data-parent="#manufacturer-description"
                   href="#manufacturer-description">
                        {l s='More' d='Shop.Warehousetheme'}
                    </a>
                {/if}

        </div>
        </div>

        {if $manufacturer.description}
            <div class="card">
                <div id="manufacturer-description" class="collapse rte-content" role="tabpanel">
                    {$manufacturer.description nofilter}
                    <a class="btn btn-secondary float-right"  data-bs-toggle="collapse" data-parent="#manufacturer-description"
                       href="#manufacturer-description">
                        {l s='Less' d='Shop.Warehousetheme'}
                    </a>
                </div>   </div>
        {/if}
        {else}
        <div class="card">
            <div id="manufacturer-description" class="rte-content">
                {$manufacturer.description nofilter}
            </div>
        </div>
    {/if}
    </div>
    {/if}
    {hook h='displayManufacturerElementor' manufacturerId = $manufacturer.id}
{/block}
