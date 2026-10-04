<section class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{unsafe:$field->getLabel()}</h2>
		{if $field->getDescription() !== null}
			<p class="sectionDescription">{unsafe:$field->getDescription()}</p>
		{/if}
	</header>

	{foreach from=$field->getValidationErrors() item='validationError'}
		{unsafe:$validationError->getHtml()}
	{/foreach}

	{include file='shared_userConditions' groupedObjectTypes=$field->getGroupedObjectTypes()}
</section>
