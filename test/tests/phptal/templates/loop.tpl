<?xml version="1.0"?>
<html>
  <head>
    <title>PHPTAL</title>
  </head>
  <body>
	<ul>
	  <li tal:repeat="value values">
	    <span tal:content="repeat/value/key"/>:
	    <span tal:content="value"/>:
	  </li>
	</ul>
  </body>
</html>