<div class = "box">
	<h2>Commands (Available)</h2>

	<p>
		<a href = "createRemoteConfigCommand.php">Create</a>
	</p>
	
	<table>
		<thead>
			<tr>
				<th class = "small media-width-prio-low">ID</th>
				<th>Identifier</th>
				<th class = "media-width-prio-low">Metadata</th>
				<th class = "media-width-prio-low">Command line</th>
				<th>Instance count</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listCommands item = command}
			<tr>
				<td class = "media-width-prio-low">
					<a href = "updateRemoteConfigurationCommand.php?id={$command.id}">{$command.id}</a>
				</td>
				<td>
					{if !empty($command.icon)}
					<img src = "resources/images/serviceIcons/{$command.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					<a href = "updateRemoteConfigurationCommand.php?id={$command.id}">{$command.identifier}</a>
				</td>
				<td class = "media-width-prio-low"><a href = "updateCommand.php?id={$command.metadataId}">{$command.metadataIdentifier}</a></td>
				<td class = "media-width-prio-low">{$command.command_line}</td>
				<td>{if $command.instanceCount == 0}<em>0 services using this command</em>{else}{$command.instanceCount}{/if}</td>
				<td><a href = "createRemoteConfigService.php?commandId={$command.id}">Create service...</a></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
