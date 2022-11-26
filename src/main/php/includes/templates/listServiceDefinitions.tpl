<div class = "box">
	<h2>Services Definitions</h2>
	
	{if empty($listServices)}
	<p>There are 0 service definitions.</p>
	{else}
		{include "filters.tpl"}
		<table class = "hover">
			<thead>
				<tr>
					<th class = "small media-width-prio-low">ID</th>
					<th>Identifier</th>
					<th>Command</th>
					<th class = "media-width-prio-low">Parent</th>
					<th class = "media-width-prio-low">Instance count</th>
					<th class = "media-width-prio-low">Actions</th>
				</tr>
			</thead>

			<tbody>
			{foreach from = $listServices item = service}
				<tr>
					<td class = "media-width-prio-low">
						<a href = "updateRemoteConfigurationService.php?id={$service.id}">{$service.id}</a>
					</td>
					<td>
						{if !empty($service.icon)}
						<img src = "resources/images/serviceIcons/{$service.icon}" alt = "serviceIcon" class = "inlineIcon"/>
						{/if}

						<a href = "updateRemoteConfigurationService.php?id={$service.id}">{$service.name}</a>
					</td>
					<td><a href = "updateRemoteConfigurationCommand.php?id={$service.commandId}">{$service.commandIdentifier}</a></td>
					<td class = "media-width-prio-low">{$service.parent}</a>
					<td class = "media-width-prio-low">{if $service.instanceCount == 0}<em>Not allocated to a node config</em>{else}{$service.instanceCount}{/if}</td>
					<td class = "media-width-prio-low">-</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	{/if}
</div>
