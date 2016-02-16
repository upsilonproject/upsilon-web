<div class = "box">
<h2>List services ({$listServices|@count})</h2>
<table class = "dataTable hover">
	<thead>
		<tr>
			<th>Description</th>
			<th><nobr>Last updated</nobr></th>
			<th>Output</th>
			<th>Remote config?</th>
			<th>Karma</th>
		</tr>
	</thead>

	<tbody>
{foreach from = $listServices item = itemService}
	<tr>
		<td>
			<a href = "viewService.php?id={$itemService.id}">{$itemService.identifier}</a>
		</td>
		<td>{$itemService.lastUpdated}</td>
		<td><pre>{$itemService.output}</pre></td>
		<td>{$itemService.remote_config_service_identifier} (<a href = "viewRemoteConfig.php?id={$itemService.remote_config}">{$itemService.remote_config}</a>)</td>
		<td class = "{$itemService.karma|strtolower}">{$itemService.karma}</td>
	</tr>
{/foreach}
	</tbody>
</table>
</div>
