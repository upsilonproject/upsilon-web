<div class = "box">
	<h2>Remote Node Configurations</h2>

	<p>
		<a href = "amqpSendPings.php">Request Node Summaries</a>
		<a href = "createRemoteConfiguration.php">Create</a>
	</p>
	
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Name</th>
				<th>Node count</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listRemoteConfigs item = itemRemoteConfig}
			<tr>
				<td>{$itemRemoteConfig.id}</td>
				<td><a href = "viewRemoteConfig.php?id={$itemRemoteConfig.id}">{$itemRemoteConfig.name}</a></td>
				<td>{$itemRemoteConfig.nodeCount}</td>
				<td>

					<a href = "updateRemoteConfig.php">Touch</a>
					<a href = "generateConfig.php?id={$itemRemoteConfig.id}">View</a> 
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<div class = "box">
	<h2>Services</h2>
	
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Command</th>
				<th>Parent</th>
				<th>Instance count</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listServices item = service}
			<tr>
				<td>
					<a href = "updateRemoteConfigurationService.php?id={$service.id}">{$service.id}</a>
				</td>
				<td>
					{if !empty($service.icon)}
					<img src = "resources/images/serviceIcons/{$service.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					<a href = "updateRemoteConfigurationService.php?id={$service.id}">{$service.name}</a>
				</td>
				<td><a href = "updateRemoteConfigurationCommand.php?id={$service.commandId}">{$service.commandIdentifier}</a></td>
				<td>{$service.parent}</a>
				<td>{if $service.instanceCount == 0}<em>Not allocated to a node config</em>{else}{$service.instanceCount}{/if}</td>
				<td>-</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<div class = "box">
	<h2>Commands (Available)</h2>

	<p>
		<a href = "createRemoteConfigCommand.php">Create</a>
	</p>
	
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Metadata</th>
				<th>Command line</th>
				<th>Instance count</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listCommands item = command}
			<tr>
				<td>
					<a href = "updateRemoteConfigurationCommand.php?id={$command.id}">{$command.id}</a>
				</td>
				<td>
					{if !empty($command.icon)}
					<img src = "resources/images/serviceIcons/{$command.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					<a href = "updateRemoteConfigurationCommand.php?id={$command.id}">{$command.identifier}</a>
				</td>
				<td><a href = "updateCommand.php?id={$command.metadataId}">{$command.metadataIdentifier}</a></td>
				<td>{$command.command_line}</td>
				<td>{if $command.instanceCount == 0}<em>0 services using this command</em>{else}{$command.instanceCount}{/if}</td>
				<td><a href = "createRemoteConfigService.php?commandId={$command.id}">Create service...</a></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
