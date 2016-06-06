<div class = "box">
<h2>Node</h2>

<div style = "float: left">
<h3>Details</h3>
<p><strong>ID</strong>: {$itemNode.identifier}</p>
<p><strong>Last Updated</strong>: <span class = "date">{$itemNode.lastUpdated}</span></p>
<p><strong>App Version</strong>: {$itemNode.instanceApplicationVersion}</p>
</div>


<div style = "float:right; vertical-align: top;">
	<h3>Remote Configurations</h3>

	<table>
		<thead>
			<tr>
				<th>Reported</th>
				<th>State</th>
				<th>Name</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
	{if empty($remoteConfigs)}
	<tr><td colspan = "99">None assigned</td></tr>
	{else}
	{foreach from = $remoteConfigs item = remoteConfig}
	<tr>
		<td class = "{if isset($remoteConfig.reported)}good{else}bad{/if}">
		{if isset($remoteConfig.reported)}
			REPORTED
		{else}
			NOT REPORTED
		{/if}
		</td>
		<td class = "{if isset($remoteConfig.reported) and $remoteConfig.reported.errors}bad{else}good{/if}">
		{if isset($remoteConfig.reported)}
			{if $remoteConfig.reported.errors}
			ERRORS
			{else}
			OK
			{/if}
		{else}
			<em>unknown - send again?</em>
		{/if}
		</td>
	
		<td><a href = "viewRemoteConfig.php?id={$remoteConfig.id}">{$remoteConfig.name}</a> </td>
		<td>
			<a href = "amqpSendRemoteConfig.php?configId={$remoteConfig.id}&amp;node={$itemNode.identifier}">Send</a>, 
			<a href = "createRemoteConfigService.php?config={$remoteConfig.id}">Create service in config</a>
		</td>
	</tr>
	{/foreach}
	{/if}
		</tbody>
	</table>
	<p><a href = "createRemoteConfiguration.php?node={$itemNode.identifier}">Create</a></p>
</div>
<div style = "clear: both;">.</div>
</div>
