<div class = "box">
	<h2>Command Metadata</h2>

	{if empty($listCommands)}
		<p>There are 0 commands.</p>
		<p>Commands are used to add common metadata to services, like add a "ping" icon to all ping commands.</p>
		<p>To create a command, go to <strong>Actions</strong> &raquo; <strong>Create Command</strong>.</p>
	{else}
	<table>
		<thead>
			<tr>
				<th>Command Identifier</th>
				<th>Used by # Reporting Services</th>
				<th>Used by # of Remote Config commands</th>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listCommands item = itemCommand}	
			<tr>
				<td>
					{if !empty($itemCommand.icon)}
					<img src = "resources/images/serviceIcons/{$itemCommand.icon}" alt = "serviceIcon" class = "inlineIcon"/>
					{/if}

					<a href = "updateCommand.php?id={$itemCommand.id}">{$itemCommand.commandIdentifier}</a>
				</td>
				<td>{$itemCommand.serviceCount}</td>
				<td>{$itemCommand.remoteConfigCommandCount}</td>
			</tr>	
		{/foreach}
		</tbody>
	</table>
	{/if}
</div>
