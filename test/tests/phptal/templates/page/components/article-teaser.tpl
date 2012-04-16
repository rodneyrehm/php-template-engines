<tal:block metal:define-macro="main">

    <article tal:attributes="id id|" class="post-${post/id} post type-post status-${post/status} format-${post/format} post ${post/_categories_class} ${post/_tags_class} ${post/_hentry_class}">
        <h2><a tal:attributes="href post/url" tal:content="post/title" rel="bookmark" title="Read '${post/title}'">Title</a></h2>

        <ul class="postmetadata clearfix">
            <li class="author">By <a rel="author" tal:attributes="href post/author/url" tal:content="post/author/name" title="Posts by ${post/author/name}">Author</a></li>
            <li class="date" tal:content="post/date"></li>
            <li class="tags"><tal:block tal:repeat="tag post/tags" ><a tal:attributes="href tag/url" tal:content="tag/name">Tag</a><tal:block tal:condition="not:repeat/tag/end">, </tal:block></tal:block></li>
            <li class="comments"><a href="${post/url}#comments" title="Comment on ${post/title}"><tal:block condition="post/comments">${post/comments} </tal:block>Comments</a> </li>
        </ul>

        <tal:block tal:replace="structure post/teaser" />

        <a tal:attributes="href post/url" class="continue-reading">Read more...</a>
    </article>
    
</tal:block>