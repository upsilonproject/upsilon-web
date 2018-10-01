{if !empty($metadata.metrics)}
<div class = "box">
	<h2>Chart</h2>
{/if}

	<div id = "chartService{$instanceChartIndex}" class = "chart">

	</div>

	{if isset($legend)}
	<div id = "legend{$instanceChartIndex}">legend</div>
	{/if}

	<a class = "fakeButton" href = "viewServiceResultChart.php?serviceIds[]={$service}">Larger</a>

	<br />
	
	<script type = "text/javascript">
dataset = {
	serviceIds: []
}

{foreach from = $listServiceId item = serviceId}
dataset.serviceIds.push("{$service}");
{/foreach}

	fetchServiceSingleMetricResultChart('{$metric}', dataset, '{$instanceChartIndex}');

	</script>

</div>
