<article {if !empty($id)}id="{$id|escape}"{/if} class="post-{$post.id|escape} post type-post status-{$post.status|escape} format-{$post.format|escape} {if $post.hentry}hentry{/if} {foreach from=$post.categories item="category"}category-{$category.key|escape}{/foreach} {foreach from=$post.tags item="tag"}tag-{$tag.key|escape}{/foreach} post">
    <h2><a href="{$post.url|escape}" rel="bookmark" title="Read '{$post.title|escape}'">{$post.title|escape}</a></h2>

    <ul class="postmetadata clearfix">
        <li class="author">By <a rel="author" href="{$post.author.url|escape}" title="Posts by {$post.author.name|escape}">{$post.author.name|escape}</a></li>
        <li class="date">{$post.date|escape}</li>
        <li class="tags">{foreach from=$post.tags item="tag" name="tag"}<a href="{$tag.url|escape}">{$tag.name|escape}</a>{if !$smarty.foreach.tag.last}, {/if}{/foreach}</li>
        <li class="comments"><a href="{$post.url|escape}#comments" title="Comment on {$post.title|escape}">{if $post.comments}{$post.comments|escape} {/if}Comments</a> </li>
    </ul>
    
    {$post.teaser}{* contains html *}

    <a href="{$post.url|escape}" class="continue-reading">Read more...</a>
</article>