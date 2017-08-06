<div class = "box">
	<h2>Remote service configs for node: {$remoteConfig.name}</h2>
	<p>This is a remote config. Once you're happy with it, you need to send it over the message bus to the specific node by clicking here: 
		<a href = "generateConfig.php?id={$remoteConfig.id}">View</a>
	</p>
	<p>
		<strong>Updated:</strong> <span><span class = "date">{$remoteConfig.mtime}</span><span> ({$remoteConfig.modifiedTimestamp})
	</p>
</div>

<div class = "box">
	<h2>Logs</h2>
	<table class = "hover">
	{foreach from = $logs item = log}
		<tr>
			<td class = "smedium"><span class = "date">{$log.timestamp}</span></td>
			<td>{$log.message}</td>
		</tr>
	{/foreach}
	</table>
</div>

<div class = "box">
	<h2>Allocated Nodes</h2>
	<p><a href = "createRemoteConfigNodeAllocation.php?id={$remoteConfig.id}">Allocate Node</a></p>
	<table class = "hover">
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Last Updated</th>
				<th>Actions</th>
				<th>Config status</th>
			</tr>
		</thead>
		<tbody>
		{foreach from = $nodes item = node} 
			<tr>
				<td>{$node.id}</td>
				<td><a href = "viewNode.php?id={$node.nodeId}">{$node.identifier}</a></td>
				<td><span class = "date">{$node.lastUpdated}</span></td>
				<td>
					<a href = "deleteRemoteConfigAllocatedNode.php?id={$node.id}">Unallocate</a>
					<a href = "amqpSendRemoteConfig.php?configId={$remoteConfig.id}&amp;node={$node.identifier}">Send</a>
				</td>
				<td class = "{$node.reportKarma|strtolower}">{$node.reportStatus} {if not empty($node.reportVersion)}Version: <span class = "date">{$node.reportVersion}</span>{/if}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<div class = "box">	
	<h2>Allocated Services</h2>
	<p>
		<a href = "createRemoteConfigServiceInstance.php?id={$remoteConfig.id}">Allocate existing service</a> | 
		<a href = "createRemoteConfigService.php?id={$remoteConfig.id}&config={$remoteConfig.id}">Create &amp; allocate service</a>
	</p>
	<table class = "hover">
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Command</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			{foreach from = $services item = service}
			<tr>
				<td>{$service.id}</td>
				<td>
					{if !empty($service.icon)}
					<img src = "resources/images/serviceIcons/{$service.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					<a href = "updateRemoteConfigurationService.php?id={$service.serviceId}">{$service.name}</a>
				</td>
				<td>
                    <a href = "updateRemoteConfigurationCommand.php?id={$service.commandId}">{$service.commandIdentifier}</a>
				</td>
				<td>
					Service: 
					<a href = "updateRemoteConfigurationService.php?id={$service.serviceId}">Update</a>
					&nbsp; &nbsp; &nbsp; &nbsp;                                 

					Allocation: 
					<a href = "updateRemoteConfigurationServiceInstance.php?id={$service.id}">Update</a>
					<a href = "deleteRemoteConfigurationServiceInstance.php?id={$service.id}">Delete</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>

<div class = "box">
	<h2>Manually Allocated Commands</h2>
	<p>The config generator will automatically allocate used commands from allocated services. If you want to configure some services locally, you can manually allocate remote commands here.</p>
	<p><a href = "createRemoteConfigCommandInstance.php?id={$remoteConfig.id}">Allocate Command</a></p>
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Actions</th>
			</tr>
		</thead>

		<tbody>
			{foreach from = $commands item = command}
			<tr>
				<td><a href = "updateRemoteConfigurationCommandInstance.php?id={$command.id}">{$command.id}</a></td>
				<td>
					{if !empty($command.icon)}
					<img src = "resources/images/serviceIcons/{$command.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					{$command.identifier}
				</td>
				<td>
					<a href = "updateRemoteConfigurationCommandInstance.php?id={$command.id}">Update</a>
					<a href = "deleteRemoteConfigurationCommandInstance.php?id={$command.id}">Delete</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

</div>
