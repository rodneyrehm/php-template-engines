<article <?php if (!empty($this->id)) $this->eprint($this->id); ?> class="post-<?php $this->eprint($this->post['id']); ?> post type-post status-<?php $this->eprint($this->post['status']); ?> format-<?php $this->eprint($this->post['format']); ?> <?php if (!empty($this->post['hentry'])): ?>hentry<?php endif; ?> <?php foreach ($this->post['categories'] as $category): ?>category-<?php $this->eprint($category['key']); ?><?php endforeach; ?> <?php foreach ($this->post['tags'] as $tag): ?>tag-<?php $this->eprint($tag['key']); ?><?php endforeach; ?> post">
    <h2><a href="<?php $this->eprint($this->post['url']); ?>" rel="bookmark" title="Read '<?php $this->eprint($this->post['title']); ?>'"><?php $this->eprint($this->post['title']); ?></a></h2>

    <ul class="postmetadata clearfix">
        <li class="author">By <a rel="author" href="<?php $this->eprint($this->post['author']['url']); ?>" title="Posts by <?php $this->eprint($this->post['author']['name']); ?>"><?php $this->eprint($this->post['author']['name']); ?></a></li>
        <li class="date"><?php $this->eprint($this->post['date']); ?></li>
        <li class="tags"><?php $_total = count($this->post['tags']); $_index = 0; foreach ($this->post['tags'] as $tag): $_index++; ?><a href="<?php $this->eprint($tag['url']); ?>"><?php $this->eprint($tag['name']); ?></a><?php if ($_index < $_total): ?>, <?php endif; endforeach; ?></li>
        <li class="comments"><a href="<?php $this->eprint($this->post['url']); ?>#comments" title="Comment on <?php $this->eprint($this->post['title']); ?>"><?php if (!empty($this->post['comments'])): ?><?php $this->eprint($this->post['comments']); ?> <?php endif; ?>Comments</a> </li>
    </ul>
    
    <?php print($this->post['teaser']); ?><?php /* contains html */ ?>

    <a href="<?php $this->eprint($this->post['url']); ?>" class="continue-reading">Read more...</a>
</article>