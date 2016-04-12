<div class = "box">
	<h3>Upgrade</h3>
	<p>Some upgrade tasks are necessary.</p>

	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Status</th>
			</tr>
		</thead>
		<tbody>
	{foreach from = $upgradeTasks item = task}
			<tr>
				<td>{$task->getName()}</td>
				<td class = "{if $task->isPossible()}good{else}bad{/if}">{if $task->isPossible()}Ready{else}Not possible{/if}</td>
			</tr>
	{/foreach}
		</tbody>
	</table>

	<p>
	<a href = "upgrade.php?doUpgrade">Start Upgrade</a>
	</p>
</div>
