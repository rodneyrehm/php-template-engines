{extends file="page/framework.tpl"}
{block name="main"}
    {foreach $articles as $article}
        {$_id = ""}
        {if $article@iteration == 3}
            <div class="ed homepagepremedtargetwrapper clearfix">
                <span class="declare">Advertisement</span>
                <span class="awithus"><a class="sprite ed-us" title="Advertise with us!" href="mailto:&#97;&#100;&#118;&#101;&#114;&#116;&#105;&#115;&#105;&#110;&#103;&#64;&#115;&#109;&#97;&#115;&#104;&#105;&#110;&#103;&#109;&#97;&#103;&#97;&#122;&#105;&#110;&#101;&#46;&#99;&#111;&#109;">Advertise with us!</a></span>
                <div id="homepagepremedtarget"></div>
            </div>
            {$_id = "jump"}
        {/if}
        {include file="page/components/article-teaser.tpl" post=$article id=$_id}
    {/foreach}
{/block}