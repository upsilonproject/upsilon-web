<div class = "box">
<h2>Service Results ({$listServices|@count})</h2>
	{include "filters.tpl"}
<form action = "workWithGroup.php" class = "unstyled">
	{include file = "selectedServiceActions.tpl"}
<table class = "dataTable hover">
	<thead>
		<tr>
			<th>Actions</th>
			<th>Source</th>
			<th>Name</th>
			<th><nobr>Last updated</nobr></th>
			<th>Output</th>
			<th>Karma</th>
		</tr>
	</thead>

	<tbody>
{foreach from = $listServices item = itemService}
	<tr>
		<td><input type = "checkbox" name = "services[]" value = "{$itemService.identifier}" /></td>
		<td>
			{if empty($itemService.remote_config_id)}
				<em>Configured locally on </em><a href = "viewNode.php?identifier={$itemService.node}">{$itemService.node}</a>
			{else}
				<a href = "updateRemoteConfigurationService.php?id={$itemService.remote_config_service_id}">{$itemService.remote_config_service_identifier}</a>
				from <a href = "viewRemoteConfig.php?id={$itemService.remote_config_id}">{$itemService.remote_config_name}</a>
			{/if}
		</td>
		<td>
			{if empty($itemService.identifier)}
				<em>Not yet reported</em>
			{else}
				<a href = "viewService.php?id={$itemService.id}">{$itemService.identifier}</a>
			{/if}
		</td>
		<td><span class = "date">{$itemService.lastUpdated}</a></td>
		<td><pre>{$itemService.output}</pre></td>
		<td class = "{$itemService.karma|strtolower}">{$itemService.karma}</td>
	</tr>
{/foreach}
	</tbody>
</table>
	{include file = "selectedServiceActions.tpl"}
</form>
</div>
