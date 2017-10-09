<div class = "box">
	<h2>Service: {$service.name}</h2>

	<p><strong>Service ID:</strong> {$service.id}</p>

	{if empty($listNodes)}
	<h3>Not Allocated!</h3>
	<span class = "warning">Warning</span>: This service is not allocated to any drones. This means it will never be executed.
	{else}
	<h3>Used by Nodes</h3>
	<ul>
	{foreach from = $listNodes item = node}
		<li>Config <a href = "viewRemoteConfig.php?id={$node.configId}">{$node.configName}</a> on node <a href = "viewNode.php?id={$node.id}">{$node.identifier}</a> <span class = "{$node.reportedServiceKarma|strtolower}"><a href = "viewService.php?id={$node.reportedServiceId}">{$node.reportedServiceIdentifier}</a></span></li>
	{/foreach}
	</ul>
	{/if}
	<a href = "createRemoteConfigServiceInstanceInConfig.php?serviceInstanceId={$service.id}" role = "button">Allocate To Drone</a>
</div>
