<div class = "box">
	<h2>Class Candidates</h2>

	<table>
		<tr>
			<th>Identifier</th>
			<th>Alias</th>
			<th>Existing classes</th>
			<th>Add</th>
		</tr>
		{foreach from = $listCandidates item = candidate}
			<tr>
				<td>{$candidate.externalIdentifier}</td>
				<td>{$candidate.externalAlias}</td>
				<td>-</td>
				<td><a href = "addCandidateCoverage.php?candidate={$candidate.id}">Associate</a></td>
			</tr>
		{/foreach}
	</table>
</div>
