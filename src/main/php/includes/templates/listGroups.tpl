<section>
	<h2>Groups</h2>

	{if $listGroups|@count == 0}
	<p>There are no groups defined at the moment. Create a group by going to <strong>Group Actions</strong> &raquo; <strong>Create Group</strong> on the menu.</p>
	{else}

	<p>This is a list of service groups.</p>

	<table class = "dataTable">
		<thead>
			<tr>
				<th class = "media-width-prio-low">ID</th>
				<th>Title</th>
				<th>Parent</th>
				<th>Number of Services</th>
				<th class = "media-width-prio-low">Number of Nodes</th>
				<th class = "media-width-prio-low">Description</th>
			</tr>
		</thead>

	<tbody>
	{foreach from = $listGroups item = itemGroup}
		<tr>
			<td class = "media-width-prio-low"><a href = "viewGroup.php?id={$itemGroup.id}">{$itemGroup.id}</a></td>
			<td><a href = "viewGroup.php?id={$itemGroup.id}">{$itemGroup.name}</a></td>
			<td>{if empty($itemGroup.parentId)}-{else}<a href = "viewGroup.php?id={$itemGroup.parentId}">{$itemGroup.parentName}</a>{/if}</td>
			<td>{$itemGroup.serviceCount}</td>
			<td class = "media-width-prio-low">{$itemGroup.nodeCount}</td>
			<td class = "media-width-prio-low">{$itemGroup.description}</td>
		</tr>
	{/foreach}
	</tbody>
	</table>
	{/if}
</section>
