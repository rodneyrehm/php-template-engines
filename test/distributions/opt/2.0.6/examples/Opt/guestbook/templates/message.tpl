<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
<opt:snippet name="content">
	<h2>Message</h2>
	
	<p class="message">{$message}</p>

	<ul class="buttons">
		<li><a parse:href="$redirect">Back to the guestbook</a></li>
	</ul>
</opt:snippet>
</opt:extend>
