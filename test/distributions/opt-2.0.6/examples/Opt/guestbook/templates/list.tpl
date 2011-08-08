<?xml version="1.0" ?>
<opt:extend file="layout.tpl">
<opt:snippet name="content">
	<ul class="buttons">
		<li><a href="index.php?action=add">Add your entry</a></li>
	</ul>

	{$count is count($entries) + 1}
	<opt:section name="entries">
	<div class="entry" parse:id="'e'~$entries.id">
		<p class="header"><a parse:href="'#e'~$entries.id" class="number">#{--$count}</a> 
		Written by <a parse:href="$entries.website" opt:on="$entries.website"><strong>{$entries.author}</strong></a>
		on {$entries.date}</p>

		<p>{u:nl2br(autoLink($entries.body))}</p>
	</div>
	<opt:sectionelse>
		<p class="message">There are no entries in the database now. Add one!</p>
	</opt:sectionelse>
	</opt:section>

	<ul class="buttons" opt:if="count($entries) gt 0">
		<li><a href="index.php?action=add">Add your entry</a></li>
	</ul>
</opt:snippet>
</opt:extend>
