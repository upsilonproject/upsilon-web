<section>
	<h2>Widgets</h2>

	<p>List of widgets.</p>
	
	<table class = "hover">
		<thead>
		<tr>
			<th>Classes</th>
			<th>Instances</th>
		</tr>
		</thead>

		<tbody>
			{foreach from = $listWidgets item = itemWidget}
			<tr>
				<td>{$itemWidget.class}</td>
				<td>{$itemWidget.instances}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</section>
