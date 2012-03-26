<?xml version="1.0" ?>
<opt:root>
<!-- generate an XML prolog for the browser -->
<opt:prolog version="1.0" />
<!-- generate the DTD for the browser from a template -->
<opt:dtd template="xhtml10transitional" />
<html>
  <head>
    <title>Open Power Template 2 - Basic example</title>
  </head>
  <body>
    <ul>
      <opt:foreach array="$values" index="name" value="value">
        <li><strong>{@name}:</strong>{@value}</li>
      </opt:foreach>
    </ul>
  </body>
</html>
</opt:root>