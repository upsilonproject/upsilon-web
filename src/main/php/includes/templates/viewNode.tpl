<div class = "box">
<h2>Node</h2>

<div style = "float: left">
<p><strong>ID</strong>: {$itemNode.identifier}</p>
<p><strong>Last Updated</strong>: <span class = "date">{$itemNode.lastUpdated}</span></p>
<p><strong>App Version</strong>: {$itemNode.instanceApplicationVersion}</p>
</div>


<div style = "float:right; vertical-align: top;">
	<h3>Remote Configurations</h3>
	{foreach from = $remoteConfigs item = remoteConfig}
		<a href = "viewRemoteConfig.php?id={$remoteConfig.id}">{$remoteConfig.name}</a> 
			| <a href = "amqpSendRemoteConfig.php?node={$itemNode.identifier}">Send</a>
			| <a href = "createRemoteConfigService.php?config={$remoteConfig.id}">Create service in config</a><br />
	{/foreach}
	<p><a href = "createRemoteConfiguration.php?node={$itemNode.identifier}">Create</a></p>
</div>
<div style = "clear: both;">.</div>
</div>
