<article {% if id %}id="{{ id }}"{% endif %} class="post-{{ post.id }} post type-post status-{{ post.status }} format-{{ post.format }} {% if post.hentry %}hentry{% endif %} {% for category in post.categories %}category-{{ category.key }}{% endfor %} {% for tag in post.tags %}tag-{{ tag.key }}{% endfor %} post">
    <h2><a href="{{ post.url }}" rel="bookmark" title="Read '{{ post.title }}'">{{ post.title }}</a></h2>

    <ul class="postmetadata clearfix">
        <li class="author">By <a rel="author" href="{{ post.author.url }}" title="Posts by {{ post.author.name }}">{{ post.author.name }}</a></li>
        <li class="date">{{ post.date }}</li>
        <li class="tags">{% for tag in post.tags %}<a href="{{ tag.url }}">{{ tag.name }}</a>{% if not loop.last %}, {% endif %}{% endfor %}</li>
        <li class="comments"><a href="{{ post.url }}#comments" title="Comment on {{ post.title }}">{% if post.comments %}{{ post.comments }} {% endif %}Comments</a> </li>
    </ul>

    {{ post.teaser|raw }}
    
    <a href="{{ post.url }}" class="continue-reading">Read more...</a>
</article>