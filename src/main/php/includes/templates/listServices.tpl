<div class = "box">
<h2>List services ({$listServices|@count})</h2>
<table class = "dataTable hover">
	<thead>
		<tr>
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
		<td>
			{if empty($itemService.remote_config_id)}
				<em>Configured locally</em>
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
</div>
