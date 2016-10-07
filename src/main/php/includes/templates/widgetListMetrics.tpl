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
	{if isset($service.listSubresults)} 

	<h2>{$subresultsTitle|default:'Subresults'}</h2>
	<ul class = "subresults">
	{foreach from = $service.listSubresults item = subResult}
		<li><span class = "metricIndicator {$subResult.karma|strtolower}">&nbsp;</span>{$subResult.name} {if not empty($subResult.comment)}<span class = "subtle">({$subResult.comment})</span>{/if}</li>
	{/foreach}
	</ul>
	{else}
	No subresults!
	{/if}
</div>
