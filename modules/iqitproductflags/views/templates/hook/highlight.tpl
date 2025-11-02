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
    {foreach from=$flags item=flag}
        <div class="p-4 d-flex gap-2 flex-column my-3" style="{$flag.style}">
            <p class="h6 text-reset mb-0">{$flag.title}</p>
            {if $flag.description}<p class="mb-2">{$flag.description nofilter}</p>{/if}
            {if $flag.link}<a href="{$flag.link}" 
            class="text-reset text-decoration-underline align-self-start">{l s='Read more' d='Modules.Iqitproductflags.Shop'}</a>{/if}
    </div>
{/foreach}
{/if}