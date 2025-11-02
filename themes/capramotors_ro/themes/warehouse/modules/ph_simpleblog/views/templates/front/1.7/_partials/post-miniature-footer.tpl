<div class="simpleblog__listing__post__wrapper__footer pt-3 mt-3 text-muted">

        {if $PH_BLOG_DISPLAY_DATE}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-calendar"></i>
            <time datetime="{$post.date_add|date_format:'c'}">
                {$post.date_add|date_format:$PH_BLOG_DATEFORMAT}
            </time>
        </div>
        {/if}
        {if $is_category eq false && $PH_BLOG_DISPLAY_CATEGORY}
        <div class="simpleblog__listing__post__wrapper__footer__block d-inline-block mr-2">
            <i class="fa fa-tags"></i>
            <a href="{$post.category_url}" title="{$post.category}" rel="category" class="text-muted">{$post.category}</a>
        </div>
        {/if}
</div><!-- .simpleblog__listing__post__wrapper__footer -->