<div class = "box">
	<h3>Upgrade</h3>
	<p>Some upgrade tasks are necessary.</p>

	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Description</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
	{foreach from = $upgradeTasks item = task}
			<tr>
				<td>{$task->getName()}</td>
				<td>{$task->getDescription()}</td>
				<td class = "{if $task->isPossible()}good{else}bad{/if}">{if $task->isPossible()}Ready{else}Not possible{/if}</td>
			</tr>
	{/foreach}
		</tbody>
	</table>

	<p>
		{if $canStartUpgrade}
		<a href = "upgrade.php?doUpgrade">Start Upgrade</a>
		{else}
		<strong>The upgrade cannot be run automatically.</strong>
		{/if}
	</p>
</div>
