<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<opt:root include="snippets.tpl">
<opt:prolog />
<opt:dtd template="xhtml10transitional" />
{@formInvalidFieldRowClass is 'error'} <!-- for form displaying -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$title} - Sample guestbook</title>
	<link rel="stylesheet" parse:href="$global.baseHref~'design/design.css'" type="text/css" />
	<!--[if IE 6]>
	<link rel="stylesheet" parse:href="$global.baseHref~'/design/ie6.css'" type="text/css" />
	<![endif]-->
</head>
<body>
<div id="header">
	<h1>OPT Guestbook</h1>
	<h3>A sample guest book written with Open Power Template 2</h3>
</div>

<div id="content">
	<opt:insert snippet="content">
	<p class="error">We are sorry, but there is nothing to be displayed.</p>
	</opt:insert>
</div>

<div id="footer">
	<p>Copyright &copy; <a href="http://www.invenzzia.org">Invenzzia Group</a> 2009</p>
	<p>Distributed under <a href="http://www.invenzzia.org/license/new-bsd">New BSD License</a></p>
</div>
</body>
</html>
</opt:root>
