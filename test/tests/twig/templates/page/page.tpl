{% extends "/page/framework.tpl" %}
{% block main %}
    {% for article in articles %}
        {% set _id = '' %}
        {% if loop.index == 3 %}
            <div class="ed homepagepremedtargetwrapper clearfix">
                <span class="declare">Advertisement</span>
                <span class="awithus"><a class="sprite ed-us" title="Advertise with us!" href="mailto:&#97;&#100;&#118;&#101;&#114;&#116;&#105;&#115;&#105;&#110;&#103;&#64;&#115;&#109;&#97;&#115;&#104;&#105;&#110;&#103;&#109;&#97;&#103;&#97;&#122;&#105;&#110;&#101;&#46;&#99;&#111;&#109;">Advertise with us!</a></span>
                <div id="homepagepremedtarget"></div>
            </div>
            {% set _id = 'jump' %}
        {% endif %}
        {% include '/page/components/article-teaser.tpl' with {'post':article, 'id':_id} %}
    {% endfor %}
    {% include '/page/components/paging.tpl' %}
{% endblock %}