
                        <!-- <span class="current-pages">Page 1 of 159 ... redundante information...</span> -->					
                    </div><!-- .col.main -->								
                </div><!-- .grid -->

                <p><a href="#top" class="backtotop" title="Jump to the top of the page">&uarr; Back to top</a></p>
            </div><!-- .fluid -->
        </div><!-- #container -->
        
        <div id="wpsidebar">
            <?php echo $this->fetch("page/components/sidebar.tpl"); ?>
    	</div>
	
	    <div id="footer">
			<div id="the-smashing-cat">
				<a href="http://www.smashingmagazine.com/about/"><img title="The Smashing Cat" alt="The Smashing Cat" src="http://media.smashingmagazine.com/themes/smashingv4/images/smashing-cat.png"></a>
			</div> 
		    <?php foreach ($this->footer_resources as $resource): ?>
		        <div class="contribute <?php if ($resource['is_our_content']): ?>our-content<?php endif; ?>">
	                <h4><?php $this->eprint($resource['title']); ?></h4>
				    <ul>
                        <?php foreach ($resource['links'] as $name => $url): ?>
				            <?php if (is_array($url)): ?>
				                <li>
				                <?php $_index = 0; $_total = count($url); ?>
                                <?php foreach ($url as $_name => $_url): $_index++; ?>
    					            <a href="<?php $this->eprint($_url); ?>"><?php $this->eprint($_name); ?></a><?php if ($_index < $_total): ?>,<?php endif; ?>
    					        <?php endforeach; ?>
    					        </li>
    					    <?php else: ?>
    					        <a href="<?php $this->eprint($url); ?>"><?php $this->eprint($name); ?></a>
    					    <?php endif; ?>
    					<?php endforeach; ?>
    				</ul>
	            </div>
		    <?php endforeach; ?>

		    <p class="statement"><em>With a commitment to quality content for the design community.</em><br /><a href="http://www.smashingmagazine.com/about/">Smashing Media GmbH</a>. Made in Germany. 2006-2012.</p>
		</div>
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
    </body>
</html>