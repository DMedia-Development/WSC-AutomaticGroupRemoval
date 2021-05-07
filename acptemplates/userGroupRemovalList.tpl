{include file='header' pageTitle='wcf.acp.group.removal.list'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\user\\group\\removal\\UserGroupRemovalAction', '.jsUserGroupRemovalRow');
		new WCF.Action.Toggle('wcf\\data\\user\\group\\removal\\UserGroupRemovalAction', '.jsUserGroupRemovalRow');
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.group.removal.list{/lang}{if $items} <span class="badge badgeInverse">{#$items}</span>{/if}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='UserGroupRemovalAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.group.removal.button.add{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{hascontent}
	<div class="paginationTop">
		{content}{pages print=true assign=pagesLinks controller="UserGroupRemovalList" link="pageNo=%d"}{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox" id="UserGroupRemovalTableContainer">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnRemovalID" colspan="2"><span>{lang}wcf.global.objectID{/lang}</span></th>
					<th class="columnTitle columnRemovalName"><span>{lang}wcf.global.name{/lang}</span></th>
					<th class="columnTitle columnGroupName"><span>{lang}wcf.acp.group.removal.userGroup{/lang}</span></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody class="jsReloadPageWhenEmpty">
				{foreach from=$objects item='removal'}
					<tr class="jsUserGroupRemovalRow">
						<td class="columnIcon">
							<span class="icon icon16 fa-{if !$removal->isDisabled}check-{/if}square-o jsToggleButton jsTooltip pointer" title="{lang}wcf.global.button.{if $removal->isDisabled}enable{else}disable{/if}{/lang}" data-object-id="{@$removal->removalID}"></span>
							<a href="{link controller='UserGroupRemovalEdit' object=$removal}{/link}" title="{lang}wcf.global.button.edit{/lang}" class="jsTooltip"><span class="icon icon16 fa-pencil"></span></a>
							<span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$removal->removalID}" data-confirm-message-html="{lang __encode=true}wcf.acp.group.removal.delete.confirmMessage{/lang}"></span>
							
							{event name='rowButtons'}
						</td>
						<td class="columnID columnRemovalID">{@$removal->removalID}</td>
						<td class="columnTitle columnRemovalName">
							<a href="{link controller='UserGroupRemovalEdit' object=$removal}{/link}">{$removal->title}</a>
						</td>
						<td class="columnDigits columnGroupName">
							{$removal->getUserGroup()->getName()}
						</td>
						
						{event name='columns'}
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<footer class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{@$pagesLinks}{/content}
			</div>
		{/hascontent}
		
		<nav class="contentFooterNavigation">
			<ul>
				<li><a href="{link controller='UserGroupRemovalAdd'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.acp.group.removal.button.add{/lang}</span></a></li>
				
				{event name='contentFooterNavigation'}
			</ul>
		</nav>
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
