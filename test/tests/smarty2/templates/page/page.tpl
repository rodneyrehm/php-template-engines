{include file="page/header.tpl"}
{foreach from=$articles item="article" name="article"}
    {assign var="_id" value=""}
    {if $smarty.foreach.article.iteration == 3}
        <div class="ed homepagepremedtargetwrapper clearfix">
            <span class="declare">Advertisement</span>
            <span class="awithus"><a class="sprite ed-us" title="Advertise with us!" href="mailto:&#97;&#100;&#118;&#101;&#114;&#116;&#105;&#115;&#105;&#110;&#103;&#64;&#115;&#109;&#97;&#115;&#104;&#105;&#110;&#103;&#109;&#97;&#103;&#97;&#122;&#105;&#110;&#101;&#46;&#99;&#111;&#109;">Advertise with us!</a></span>
            <div id="homepagepremedtarget"></div>
        </div>
        {assign var="_id" value="jump"}
    {/if}
    {include file="page/components/article-teaser.tpl" post=$article id=$_id}
{/foreach}

{include file="page/components/paging.tpl"}
{include file="page/footer.tpl"}