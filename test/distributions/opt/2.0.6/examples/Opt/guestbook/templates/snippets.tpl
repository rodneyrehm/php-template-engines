<?xml version="1.0" ?>
<opt:root>
	<opt:snippet name="fieldLayout">
	<com:div>
		<opt:onEvent name="error">
		<p class="error">{$errorMessage}</p>
		</opt:onEvent>
		
		<label parse:for="'form_'~$system.component.name~'_id'">
			{$system.component.title}
			<opt:onEvent name="isRequired">
			<strong>*</strong>
		</opt:onEvent>
		</label>
		

		<opt:display />
		<p opt:if="$system.component.description" class="desc">{$system.component.description}</p>
	</com:div>
	</opt:snippet>
</opt:root>
