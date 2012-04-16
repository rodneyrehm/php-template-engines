<?php echo $this->fetch("page/header.tpl"); ?>
<?php $_index = 0; ?>
<?php foreach ($this->articles as $article): ?>
    <?php $_id = ""; $_index++; ?>
    <?php if ($_index === 3): ?>
        <div class="ed homepagepremedtargetwrapper clearfix">
            <span class="declare">Advertisement</span>
            <span class="awithus"><a class="sprite ed-us" title="Advertise with us!" href="mailto:&#97;&#100;&#118;&#101;&#114;&#116;&#105;&#115;&#105;&#110;&#103;&#64;&#115;&#109;&#97;&#115;&#104;&#105;&#110;&#103;&#109;&#97;&#103;&#97;&#122;&#105;&#110;&#101;&#46;&#99;&#111;&#109;">Advertise with us!</a></span>
            <div id="homepagepremedtarget"></div>
        </div>
        <?php $_id = "jump"; ?>
    <?php endif; ?>
    <?php
        $this->post = $article;
        $this->id = $_id;
        echo $this->fetch("page/components/article-teaser.tpl");
    ?>
<?php endforeach; ?>

<?php echo $this->fetch("page/components/paging.tpl"); ?>
<?php echo $this->fetch("page/footer.tpl"); ?>