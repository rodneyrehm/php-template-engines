<html metal:use-macro="page/framework.tpl/main">
    <tal:block metal:fill-slot="content">
        
        <tal:block tal:repeat="article articles" tal:define="global my_id string:">
            <tal:block tal:define="idx repeat/article/number" tal:condition="php: idx === 3">
                <div class="ed homepagepremedtargetwrapper clearfix">
                    <span class="declare">Advertisement</span>
                    <span class="awithus"><a class="sprite ed-us" title="Advertise with us!" href="mailto:&#97;&#100;&#118;&#101;&#114;&#116;&#105;&#115;&#105;&#110;&#103;&#64;&#115;&#109;&#97;&#115;&#104;&#105;&#110;&#103;&#109;&#97;&#103;&#97;&#122;&#105;&#110;&#101;&#46;&#99;&#111;&#109;">Advertise with us!</a></span>
                    <div id="homepagepremedtarget"></div>
                </div>
                <tal:block tal:define="global my_id string:jump" />
            </tal:block>
            <tal:block metal:use-macro="page/components/article-teaser.tpl/main" tal:define="post article; id my_id" />
        </tal:block>

        <tal:block metal:use-macro="page/components/paging.tpl/main" />
    </tal:block>
</html>