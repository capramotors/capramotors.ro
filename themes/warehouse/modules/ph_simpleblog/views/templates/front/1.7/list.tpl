{extends file='page.tpl'}




{block name='page_title'}
    	{if $is_category eq true}
			{$blogCategory->name}
		{else}
			{$blogMainTitle}
		{/if}
{/block}

{block name='head_seo_title'}{strip}{$meta_title}{/strip}{/block}
{block name='head_seo_description'}{strip}{$meta_description}{/strip}{/block}

	

	{block name='head_hreflang'}

		{if $pagination.current_page > 1}
				{assign var="paginationHreflang" value="/page/{$pagination.current_page}"}
			{else}
				{assign var="paginationHreflang" value=""}
		{/if}


		{if $is_category eq true}
			{widget_block name="ps_languageselector"}
			{foreach from=$languages item=language name=alter_langs_loop}
				{assign var="categoryForHrefLang" value=SimpleBlogCategory::getRewriteByCategory($blogCategory->id_simpleblog_category, $language.id_lang)}
				
				<link rel="alternate" href="{Context::getContext()->link->getModuleLink('ph_simpleblog', 'category', [ 'sb_category' => $categoryForHrefLang], null, $language.id_lang)}{$paginationHreflang}" hreflang="{$language.iso_code}">
				{if $smarty.foreach.alter_langs_loop.index == 0}
					<link rel="alternate" href="{Context::getContext()->link->getModuleLink('ph_simpleblog', 'category', [ 'sb_category' => $categoryForHrefLang], null, $language.id_lang)}{$paginationHreflang}" hreflang="x-default">
				{/if}

			{/foreach}
			{/widget_block}
			{else}
				{widget_block name="ps_languageselector"}
				{foreach from=$languages item=language name=alter_langs_loop}
					
					<link rel="alternate" href="{Context::getContext()->link->getModuleLink('ph_simpleblog', 'list', [], null, $language.id_lang)}{$paginationHreflang}" hreflang="{$language.iso_code}">
					{if $smarty.foreach.alter_langs_loop.index == 0}
						<link rel="alternate" href="{Context::getContext()->link->getModuleLink('ph_simpleblog', 'list', [], null, $language.id_lang)}{$paginationHreflang}" hreflang="x-default">
					{/if}
	
		
				{/foreach}
				{/widget_block}
		{/if}
	  {/block}

	  


{block name='page_content'}
	{block name='category_elements'}
		{block name='category_heading'}
		{if $is_category eq true}
			{if $displayCategoryImage && isset($blogCategory->image) || !empty($blogCategory->description) && $displayCategoryDescription}
				<div class="cardblog">
					{if $displayCategoryImage && isset($blogCategory->image)}
						<div class="simpleblog-category-image">
							<img src="{$blogCategory->image}" alt="{$blogCategory->name}" class="img-fluid" />
						</div>
					{/if}

					{if !empty($blogCategory->description) && $displayCategoryDescription}
						<div class="ph_cat_description rte card-block pb-1">
							{$blogCategory->description nofilter}
						</div>
					{/if}
				</div>
			{/if}
		{/if}
		{/block}

		{block name='category_children'}
		{if $displayCategoryChildrens}
			{if $blogCategories && $blogCategories|count > 0}
			<div class="blogSubcat mb-2">
				<ul class="blogSubcat__list my-1">
					{foreach from=$blogCategories item=category}
						<li class="blogSubcat__item">
							<a
								class="blogSubcat__link cardblog btn mb-0"
								href="{$category.url}"
								>	
								{$category.name}
							</a>
						</li>
					{/foreach}
				</ul>
			</div>
			{/if}
		{/if}
		{/block}
	{/block}

	{block name='listing'}
	<div class="simpleblog__listing">
		<div class="{if $useMasonry}blog-masonry-list{else}row{/if}">
			{if isset($posts) && count($posts)}
			{foreach from=$posts item=post}
	        	{include file="module:ph_simpleblog/views/templates/front/1.7/_partials/post-miniature.tpl" masonry=$useMasonry}
		    {/foreach}
	        {else}
			<div class="warning alert alert-warning col-xs-12">{l s='There are no posts' d='Modules.Simpleblog.Shop'}</div>
	        {/if}
		</div><!-- .row -->
	</div><!-- .simpleblog__listing -->
	{/block}

	{block name='pagination'}
	{if isset($posts) && count($posts)}
	    {if $is_category}
	    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=$blogCategory->link_rewrite type='category'}
	    {else}
	    	{include file="module:ph_simpleblog/views/templates/front/1.7/pagination.tpl" rewrite=false type=false}
	    {/if}
	{/if}
	{/block}
{/block}