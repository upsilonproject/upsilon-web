<div class = "box">
	<h2>Maintenence Periods</h2>

	{if empty($listMaintPeriods)}
	<p>0 maintenance periods have been defined.</p>
	{else}
	<table class = "sortable">
		<thead>
			<tr>
				<th>ID</th>
				<th>Title</th>
				<th>Content</th>
				<th>Services</td>
			</tr>
		</thead>

		<tbody>
		{foreach from = $listMaintPeriods item = itemMaintPeriod}
			<tr>
				<td><a href = "updateMaintPeriod.php?id={$itemMaintPeriod.id}">{$itemMaintPeriod.id}</a></td>
				<td><a href = "updateMaintPeriod.php?id={$itemMaintPeriod.id}">{$itemMaintPeriod.title}</a></td>
				<td><pre>{$itemMaintPeriod.content}</pre></td>
				<td><a href = "viewList.php?maintPeriod={$itemMaintPeriod.id}">{$itemMaintPeriod.countServices}</a></td>
			</td>
		{/foreach}
		</tbody>
	</table>
	{/if}
</div>
