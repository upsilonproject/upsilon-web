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

	<br />{$service} {$metric}
	
	<script type = "text/javascript">
dataset = {
	serviceIds: []
}

{if isset($service)}
dataset.serviceIds.push("{$service}");
{/if}

	fetchServiceSingleMetricResultChart('{$metric}', dataset, '{$instanceChartIndex}');

	</script>

</div>
