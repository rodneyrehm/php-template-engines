<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
<opt:snippet name="content">
	<form parse:action="$form.action" method="post">
	<fieldset>
		<legend>New entry</legend>

		<opt:input name="author" template="fieldLayout">
			<opt:set str:name="title" str:value="Your name" />
		</opt:input>

		<opt:input name="email" template="fieldLayout">
			<opt:set str:name="title" str:value="Your e-mail" />
			<opt:set str:name="description" str:value="Will not be published" />
		</opt:input>

		<opt:input name="website" template="fieldLayout">
			<opt:set str:name="title" str:value="Website" />
			<opt:set str:name="description" str:value="Begin with http://" />
		</opt:input>

		<opt:textarea name="body" template="fieldLayout">
			<opt:set str:name="title" str:value="Message" />
		</opt:textarea>

		<div><input type="submit" class="inputSubmit" value="Send!" /></div>
	</fieldset>
	</form>

	<ul class="buttons">
		<li><a href="index.php?action=list">Back</a></li>
	</ul>
</opt:snippet>
</opt:extend>
