<div class = "box">
	<h2>Logs</h2>
	<tr>
		<th>timestamp</th>
		<th>message</th>
	</tr>
	<tbody>
{foreach from = $listLogs item = log}
	<tr>
	<td class = "date">{$log.timestamp}</td>
	<td>{$log.message}</td>
	</tr>
{/foreach}
	</tbody>
</table>
