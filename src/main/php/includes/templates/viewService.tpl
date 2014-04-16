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
		<p><strong>Last Check:</strong> {$itemService.lastUpdated} <span class = "subtle">{$itemService.lastUpdatedRelative}</span></p>
		<p><strong>Estimated Next Check:</strong> {$itemService.estimatedNextCheck} <span class = "subtle">{$itemService.estimatedNextCheckRelative}</span></p>

		<p>
			<strong>Karma:</strong> <span class = "metricIndicator {$itemService.karma|strtolower}">{$itemService.karma} ({$itemService.consecutiveCount} in a row)</span>
		</p>

		{if isset($metadata.criticalCast)}
		<p><strong>Critical Cast:</strong> <span class = "metricIndicator {$metadata.criticalCast|strtolower}">{$metadata.criticalCast|default:'none'}</span></p>
		{/if}
		{if isset($metadata.goodCast)}
		<p><strong>Good Cast:</strong> <span class = "metricIndicator {$metadata.goodCast|strtolower}">{$metadata.goodCast|default:'none'}</span></p>
		{/if}
	</div>

	<div style = "float: right; vertical-align: top;">
		<h3>Debug info</h3>
		<p><strong>ID:</strong> {$itemService.id}</p>
		<p><strong><abbr title = "Command identifier: {$itemService.commandIdentifier}">Command line</abbr>:</strong> {$itemService.commandLine} 
		{if $metadata.commandMetadataId}(<a href = "updateCommand.php?id={$metadata.commandMetadataId}">{$itemService.commandIdentifier}</a>){/if}
		</p>

		<p><strong>Group memberships:</strong>
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

		<p><strong>Node:</strong> <a href = "viewNode.php?identifier={$itemService.node}">{$itemService.node}</a></p>

	</div>

	<div style = "margin-right: 2em; float: right; vertica-align: top">
		{if not empty($metadata.room)}
		<h3>Room (Location)</h3>
		<p>View service in <a href = "viewRoom.php?id={$metadata.room}">room</a></a>
		{/if}
	</div>

	<div style = "clear: both;"></div>
</div>

<div class = "box" id = "graphContainer">
	<h2 id = "graphTitle">Graph</h2>

	{include file = "widgetGraphMetric.tpl"}

	<p>

	{if !empty($metadata.metrics)}
		<strong>Metric:</strong>
		{foreach from = $metadata.metrics item = metric}
			<a href = "#graphContainer" onclick = "javascript:fetchServiceMetricResultGraph('{$metric|trim}', [{$itemService.id}], 0)">{$metric|trim}</a>&nbsp;&nbsp;&nbsp;&nbsp;
		{/foreach}
	{/if}
	</p>
</div>

<div class = "recentResults box">
	<h2>Results</h2>

{if $listResults|@count == 0}
	<p>No results stored in the results table.</p>
{else}
	<table class = "hover dataTable" />
		<thead>
			<tr>
				<th>Timestamp</th>
				<th>Output</th>
				<th>Karma</th>
			</tr>
		</thead>

		<tbody>
			{foreach from = $listResults item = itemResult}
			<tr>
				<td>{$itemResult.checked} &nbsp;&nbsp;<span class = "metricOutput">{$itemResult.relative}</span></td>
				<td><pre>{$itemResult.output|htmlspecialchars|wordwrap}</pre></td>
				<td class = "{$itemResult.karma|strtolower}">{$itemResult.karma}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
</div>
