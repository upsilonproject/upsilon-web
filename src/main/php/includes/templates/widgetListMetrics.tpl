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
	<ul class = "subresults">
	{foreach from = $service.listMetrics item = itemMetric}
		<li>
			<span class = "metricIndicator {$itemMetric.karma|strtolower}">&nbsp;</span>
				{if not empty($itemMetric.caption)}
					{$itemMetric.caption}
				{else}
					{$itemMetric.name}
				{/if}
				: {$itemMetric.value} 
			{if not empty($itemMetric.comment)}<span class = "subtle">({$itemMetric.comment})</span>{/if}
		</li>
	{/foreach}
	</ul>
	{else}
	No subresults!
	{/if}
</div>
