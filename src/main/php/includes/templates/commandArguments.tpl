<div class = "box">
	<h2>Command Arguments</h2>

	<p>
		<a href = "createCommandArgument.php?command={$commandId}">Create Argument</a>
	</p>

	<table>
		<thead>
			<tr>
				<th>Name</th>
				<th>Datatype</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
		{foreach from = $arguments item = argument}
		<tr>
			<td>{$argument.name}</td>
			<td>{$argument.datatype}</td>
			<td>-</td>
		</tr>
		{/foreach}
		</tbody>
	</table>
</div>
