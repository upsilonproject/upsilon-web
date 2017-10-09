
{if !empty($metadata.metrics)}
<div class = "box">
	<h2>Chart</h2>
{/if}

	<div id = "chartService{$instanceChartIndex}" class = "chart">

	</div>

	{if isset($legend)}
	<div id = "legend{$instanceChartIndex}">legend</div>
	{/if}

	<a class = "fakeButton" href = "viewServiceResultChart.php?serviceIds[]={$listServiceId|implode:','}">Larger</a>

	<br />
	
	<script type = "text/javascript">
dataset = {
	serviceIds: []
}

{foreach from = $listServiceId item = serviceId}
dataset.serviceIds.push("{$serviceId}");
{/foreach}

	fetchServiceMetricResultChart('{$metric}', dataset, '{$instanceChartIndex}');

	{literal}
pm = window.chartMarkings[{/literal}{$instanceChartIndex}{literal}] = [];
	{/literal}

	{foreach from = $yAxisMarkings item = marking}
pm.push({$marking})
	{/foreach}
	</script>

	{if !empty($metadata.metrics)}
		<strong>Metric:</strong>
		{foreach from = $metadata.metrics item = metric}
			<a href = "#chartContainer" onclick = "javascript:fetchServiceMetricResultChart('{$metric|trim}', {literal}{{/literal}node: '{$itemService.node}', serviceIds: ['{$itemService.id}']{literal}}{/literal}, 0)">{$metric|trim}</a>&nbsp;&nbsp;&nbsp;&nbsp;
		{/foreach}

</div>
	{/if}
