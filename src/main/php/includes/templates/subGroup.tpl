<p>
	<strong>subgroup: {$itemSubgroup.name}</strong>

	{foreach from = $listSubservices item = "itemService"}
		<li>
			<span class = "metricIndicator {$itemService.karma|strtolower}"></span>
			<span class = "metricTitle" title = "{$itemService.output}">{$itemService.description}/{$itemService.executableShort}</span>
		</li>
	{/foreach}

</p>
