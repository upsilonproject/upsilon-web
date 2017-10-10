<div class = "box">
	<h2>Remote Node Configurations</h2>

	<p>
		<a href = "amqpSendPings.php">Request Node Summaries</a>
		<a href = "createRemoteConfiguration.php">Create</a>
	</p>
	
	<table class = "hover">
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

