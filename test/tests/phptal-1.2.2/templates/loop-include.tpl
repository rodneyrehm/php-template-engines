<?xml version="1.0"?>
<html>
  <head>
    <title>PHPTAL</title>
  </head>
  <body>
	<ul>
	  <li tal:repeat="value values" metal:use-macro="loop-include-child.tpl/main"></li>
	</ul>
  </body>
</html>