<div class = "box">
	<h2>Logs</h2>
	<table>
		<tr>
			<th>timestamp</th>
			<th>message</th>
		</tr>
		<tbody>
	{foreach from = $listLogs item = log}
		<tr>
			<td class = "short"><span class = "date">{$log.timestamp}</span></td>
			<td>{$log.message}</td>
		</tr>
	{/foreach}
		</tbody>
	</table>
</div>
