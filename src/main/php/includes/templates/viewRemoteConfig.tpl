<div class = "box">
	<h2>Remote service configs for node: {$remoteConfig.name}</h2>
	<p>This is a remote config. Once you're happy with it, you need to send it over the message bus to the specific node by clicking here: 
		<a href = "generateConfig.php?id={$remoteConfig.id}">View</a>
	</p>
</div>

<div class = "box">
	<h2>Allocated Nodes</h2>
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
				<th>Last Updated</th>
				<th>Actions</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
		{foreach from = $nodes item = node} 
			<tr>
				<td>{$node.id}</td>
				<td><a href = "viewNode.php?id={$node.nodeId}">{$node.identifier}</a></td>
				<td>{$node.lastUpdated}</td>
				<td>
					<a href = "deleteRemoteConfigAllocatedNode.php?id={$node.id}">Delete</a>
					<a href = "amqpSendRemoteConfig.php?node={$node.identifier}">Send</a>
				</td>
				<td class = "{$node.karma|strtolower} small">{$node.karma}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>

<div class = "box">	
	<h2>Allocated Services</h2>
	<p><a href = "createRemoteConfigServiceInstance.php?id={$remoteConfig.id}">Allocate Service</a></p>
	<p><a href = "createRemoteConfigService.php?id={$remoteConfig.id}">Create Service</a></p>
	<table>
		<thead>
			<tr>
				<th class = "small">ID</th>
				<th>Identifier</th>
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

					<a href = "updateRemoteConfigurationServiceInstance.php?id={$service.id}">{$service.name}</a>
				</td>
				<td>
					Service: 
					<a href = "updateRemoteConfigurationService.php?id={$service.serviceId}">Update</a>
					&nbsp;
					&nbsp;
					&nbsp;
					&nbsp;
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
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

</div>
