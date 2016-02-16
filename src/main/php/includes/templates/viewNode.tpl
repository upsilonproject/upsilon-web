<div class = "box">
<h2>Node</h2>

<div style = "float: left">
<p><strong>ID</strong>: {$itemNode.identifier}</p>
<p><strong>Last Updated</strong>: {$itemNode.lastUpdated}</p>
<p><strong>App Version</strong>: {$itemNode.instanceApplicationVersion}</p>
</div>


<div style = "float:right; vertical-align: top;">
	<h3>Remote Configurations</h3>
	{foreach from = $remoteConfigs item = remoteConfig}
		<a href = "viewRemoteConfig.php?id={$remoteConfig.id}">{$remoteConfig.name}</a> 
			| <a href = "amqpSendRemoteConfig.php?node={$itemNode.identifier}">Send</a>
			| <a href = "createRemoteConfigService.php?config={$remoteConfig.id}">Create service in config</a>
	{/foreach}
</div>
<div style = "clear: both;">.</div>
</div>
