<div>
<div class = "serviceDetail box">
	<h2>
{if !empty($metadata.icon)}
<img src = "resources/images/serviceIcons/{$metadata.icon}" alt = "serviceIcon" class = "inlineIcon"/>
{/if}
	Service Configuration</h2>

	<div style = "float: left; vertical-align: top;">
		<h3>Basics</h3>
		<p><strong>Identifier:</strong> {$itemService.identifier}</p>
		<p><span><strong>Last Check:</strong> <span class = "date">{$itemService.lastUpdated}</span></span> <a href = "amqpExecuteSingle.php?serviceId={$itemService.id}">Execute Now</a></p>
		<p><strong>Estimated Next Check:</strong> <span class = "date">{$itemService.estimatedNextCheck}</span></p>

		<p>
			<strong>Karma:</strong> <span class = "metricIndicator {$itemService.karma|strtolower}">{$itemService.karma} ({$itemService.consecutiveCount} in a row)</span>
		</p>

		{if isset($metadata.criticalCast)}
		<p><strong>Critical Cast:</strong> <span class = "metricIndicator {$metadata.criticalCast|strtolower}">{$metadata.criticalCast|default:'none'}</span></p>
		{/if}
		{if isset($metadata.goodCast)}
		<p><strong>Good Cast:</strong> <span class = "metricIndicator {$metadata.goodCast|strtolower}">{$metadata.goodCast|default:'none'}</span></p>
		{/if}

		<p><strong>Last output:</strong></p>
		<pre>{$itemService.output}</pre>
	</div>

	<div style = "float: right; vertical-align: top;">
		<h3>Service</h3>
		<p><strong>ID:</strong> {$itemService.id}</p>
		<p><strong>Node: </strong> <a href = "viewNode.php?identifier={$itemService.node}">{$itemService.node}</a></p>
		<p><strong><abbr title = "Command identifier: {$itemService.commandIdentifier}">Reported command line</abbr>:</strong> {$itemService.commandLine} 
		{if $metadata.commandMetadataId}(<a href = "updateCommand.php?id={$metadata.commandMetadataId}">{$itemService.commandIdentifier}</a>){/if}
		</p>

		<h3>Configuration</h3>
		<p>
			{if $configSource == "local"} 
				<strong>Configuration:</strong> <em>Configured locally</em><br /> 
				<strong>Service: </strong> {$itemService.identifier} (local config only) </br >
				<strong>Command: </strong> - <br />
			{else}
				<strong>Configuration:</strong> <em>Centrally, in</em> <a href = "viewRemoteConfig.php?id={$configSource.remote_config_id}">{$configSource.remote_config_name}</a> <br />
				<strong>Service: </strong> {$itemService.identifier} (<a href = "updateRemoteConfigurationService.php?id={$configSource.remote_configuration_service_id}">update</a>)<br />
				<strong>Command: </strong> <a href = "updateRemoteConfigurationCommand.php?id={$configSource.remote_config_command_id}">{$configSource.remote_config_command_name}</a>  <br />
				<strong>Command line: </strong> <br /><br /> <pre>{$commandLineClickable}</pre>
			{/if}
		</p>
	</div>

	<div style = "float: right; vertical-align: top; margin-right: 2em;">
		<h3>Classes</h3>

		{if empty($listClassInstances)}
			<em>No classes.</em>
		{else}
			{foreach from = $listClassInstances item = itemClass}
				<strong><a href = "viewClassInstance.php?id={$itemClass.id}">{$itemClass.title}</a></strong>
				<table>
					<tr>
						<th>Requirement</th>
						<th>Service</th>
						<th>Karma</th>
					</tr>
					{foreach from = $itemClass.requirements item = requirement}
					<tr>
						<td>{$requirement.requirementTitle}</td>
						<td><a href = "viewService.php?id={$requirement.service}">{$requirement.serviceIdentifier}</a></td>

						{if empty($requirement.karma)}
						<td>-</td>
						{else}
						<td class = "{$requirement.karma|strtolower}">{$requirement.karma}</td>
						{/if}
					</tr>
					{/foreach}
				</table>
			{/foreach}
		{/if}
	</div>

	<div style = "float: right; vertical-align: top; margin-right: 2em;">
		<h3>Group Memberships</h3>
		<p>
		{if $listGroupMemberships|@count eq 0}
			<em>No memberships.</em>
		{else}
			<ul>
			{foreach from = $listGroupMemberships item = itemGroupMembership}
				<li><a href = "viewGroup.php?id={$itemGroupMembership.groupId}">{$itemGroupMembership.groupName}</a> [<a href = "deleteGroupMembership.php?id={$itemGroupMembership.id}">X</a>]</li>
			{/foreach}
			</ul>
		{/if}
			<br />
		</p>

	</div>


	<div style = "margin-right: 2em; float: right; vertica-align: top">
		{if not empty($metadata.room)}
		<h3>Room (Location)</h3>
		<p>View service in <a href = "viewRoom.php?id={$metadata.room}">room</a></a>
		{/if}
	</div>

	<div style = "clear: both;"></div>
</div>

<div class = "box" id = "chartContainer">
	<h2 id = "chartTitle">Chart</h2>

	{include file = "widgetChartMetric.tpl"}
</div>
