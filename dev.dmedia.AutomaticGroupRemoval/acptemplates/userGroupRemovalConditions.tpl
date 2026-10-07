<section class="section">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{unsafe:$field->getLabel()}</h2>

		{if $field->getDescription() !== null}
			<p class="sectionDescription">{unsafe:$field->getDescription()}</p>
		{/if}
	</header>

	{assign var='noConditionsError' value=$field->getNoConditionsErrorMessage()}
	{if $noConditionsError !== null}
		<woltlab-core-notice type="error">{unsafe:$noConditionsError}</woltlab-core-notice>
	{/if}

	{include file='shared_userConditions' groupedObjectTypes=$field->getGroupedObjectTypes()}
</section>
