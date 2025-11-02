{**
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
 *}
{if $flags|@count > 0}

    <div class="product-campagains d-flex flex-wrap gap-1 mt-2 mb-2 {if isset($params.align)} justify-content-{$params.align}{/if}">
        {foreach from=$flags item=flag}
            <span 
                class="product-flags__flag btn btn-light btn-sm cursor-initial" 
                style="{$flag.style}">
                {$flag.title}
            </span>
        {/foreach}
    </div>
{/if}