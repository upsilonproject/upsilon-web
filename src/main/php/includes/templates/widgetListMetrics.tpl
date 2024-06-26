<div>
	{if $lastUpdatedShort}
	<div style = "float: right"><span class = "date relative">{$service.lastUpdated}</span></div>
	{/if}

	{if $serviceDetail}
	<h2>Details</h2>
	<p><strong>Service:</strong> {if $sessionOptions->drawNavigation}<a href = "viewService.php?id={$service.id}">{/if}{$service.identifier}{if $sessionOptions->drawNavigation}</a>{/if}<p>
	<p><strong>Karma:</strong> <span class = "metricIndicator {$service.karma|strtolower}">{$service.karma}</span></p>
	<p><strong>Last Updated:</strong> <span><span class = "date">{$service.lastUpdated}</span></span></p>

	{/if}
	{if isset($service.listMetrics)} 

	<h2>{$metricsTitle|default:'Metrics'}</h2>
	<div class = "metricList">
	{foreach from = $service.listMetrics item = itemMetric}
		<span class = "metricIndicator {$itemMetric.karma|strtolower}">&nbsp;</span>
			{if not empty($itemMetric.caption)}
				{$itemMetric.caption}
			{else}
				{if not empty($itemMetric.url)}
				<a target = "_blank" href = "{$itemMetric.url}">{$itemMetric.name}</a>
				{else}
				{$itemMetric.name}
				{/if}
			{/if}
			: {$itemMetric.value} 
		{if not empty($itemMetric.comment)}<span class = "subtle">({$itemMetric.comment})</span>{/if}
	{/foreach}
	</div>
	{else}
	No subresults!
	{/if}
</div>
