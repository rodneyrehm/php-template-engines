
                        <div class="wp-pagenavi clearfix">
                            <div class="pagenavi clearfix">
                                <div class="previous"></div>
                                <div class="pagelist clearfix">
                                    <span class="current">1</span>
                                    <a href="http://www.smashingmagazine.com/page/2/" class="page" title="2">2</a>
                                    <a href="http://www.smashingmagazine.com/page/3/" class="page" title="3">3</a>
                                    <a href="http://www.smashingmagazine.com/page/4/" class="page" title="4">4</a>
                                    <a href="http://www.smashingmagazine.com/page/5/" class="page" title="5">5</a>
                                    <a href="http://www.smashingmagazine.com/page/6/" class="page" title="6">6</a>
                                    <a href="http://www.smashingmagazine.com/page/7/" class="page" title="7">7</a>
                                    <a href="http://www.smashingmagazine.com/page/8/" class="page" title="8">8</a>
                                    <a href="http://www.smashingmagazine.com/page/9/" class="page" title="9">9</a>
                                    <a href="http://www.smashingmagazine.com/page/10/" class="page" title="10">10</a>
                                </div>
                                <div class="next">
                                    <a href="http://www.smashingmagazine.com/page/2/" >next &raquo;</a>
                                </div>
                            </div>
                        </div>
                        <!-- <span class="current-pages">Page 1 of 159 ... redundante information...</span> -->					
                    </div><!-- .col.main -->								
                </div><!-- .grid -->

                <p><a href="#top" class="backtotop" title="Jump to the top of the page">&uarr; Back to top</a></p>
            </div><!-- .fluid -->
        </div><!-- #container -->
        
        <div id="wpsidebar">
            {include file="page/components/sidebar.tpl"}
    	</div>
	
	    <div id="footer">
			<div id="the-smashing-cat">
				<a href="http://www.smashingmagazine.com/about/"><img title="The Smashing Cat" alt="The Smashing Cat" src="http://media.smashingmagazine.com/themes/smashingv4/images/smashing-cat.png"></a>
			</div> 
            {foreach from=$footer_resources item="resource"}
		        <div class="contribute {if $resource.is_our_content}our-content{/if}">
	                <h4>{$resource.title|escape}</h4>
				    <ul>
                        {foreach from=$resource.links key="name" item="url"}
				            {if is_array($url)}
				                <li>
                                {foreach from=$url key="_name" item="_url" name="_url"}
    					            <a href="{$_url|escape}">{$_name|escape}</a>{if !$smarty.foreach._url.last},{/if}
    					        {/foreach}
    					        </li>
    					    {else}
    					        <li><a href="{$url|escape}">{$name|escape}</a></li>
    					    {/if}
    					{/foreach}
    				</ul>
	            </div>
		    {/foreach}

		    <p class="statement"><em>With a commitment to quality content for the design community.</em><br /><a href="http://www.smashingmagazine.com/about/">Smashing Media GmbH</a>. Made in Germany. 2006-2012.</p>
		</div>
		{literal}
		<!-- Anfang Bannersammelstelle -->
        <script type='text/javascript'>/* <![CDATA[ */  ads_init();   /* ]]> */ </script>
        <script type="text/javascript" src="http://auslieferung.commindo-media-ressourcen.de/www/delivery/spcjs.php?id=4&amp;block=1&amp;blockcampaign=1&amp;withtext=1"></script>
        <script type='text/javascript'>/* <![CDATA[ */  ads_display();   /* ]]> */ </script>
        <!-- Ende Bannersammelstelle -->
        <script type="text/javascript">
			setTimeout(function(){ var a=document.createElement("script");
			var b=document.getElementsByTagName('script')[0];
			a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0011/1125.js";
			a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
		</script>
		<script src="//static.getclicky.com/js" type="text/javascript"></script>
		<script type="text/javascript">
			try{ clicky.init(2727); }catch(e){}
		</script>
		{/literal}
    </body>
</html>