<section>
	<h2>Users</h2>

	<table>
		<thead>
		<tr>
			<th class = "small">ID</th>
			<th>Username</th>
		</tr>
		</thead>

		<tbody>
			{foreach from = $listUsers item = itemUser}
			<tr>
				<td>{$itemUser.id}</td>
				<td><a href = "updateUser.php?id={$itemUser.id}">{$itemUser.username}</a></td>
			</tr>
			{/foreach}
		</body>
	</table>
</section>
