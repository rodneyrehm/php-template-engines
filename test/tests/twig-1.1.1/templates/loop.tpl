<ul>
  {% for item in values %}
    <li> {{ loop.index }} : {{ item }} </li>
  {% endfor %}
</ul>
