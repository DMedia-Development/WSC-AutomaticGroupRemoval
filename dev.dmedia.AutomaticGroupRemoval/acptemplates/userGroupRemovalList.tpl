{include file='header' pageTitle='wcf.acp.group.removal.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.group.removal.list{/lang} {if $gridView->countRows()} <span class="badge badgeInverse">{#$gridView->countRows()}</span>{/if}</h1>
	</div>

	<nav class="contentHeaderNavigation">
		<ul>
			<li>
				<a href="{link controller='UserGroupRemovalAdd'}{/link}" class="button">{icon name='plus'} <span>{lang}wcf.acp.group.removal.button.add{/lang}</span></a>
			</li>

			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

<div class="section">
	{unsafe:$gridView->render()}
</div>

{include file='footer'}
