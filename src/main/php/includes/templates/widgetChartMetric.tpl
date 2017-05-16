	<div id = "graphService{$instanceGraphIndex}" class = "graph">

	</div>
	<script type = "text/javascript">
dataset = {
	node: "{$itemService['node']}",
	services: []
}

{foreach from = $listServiceId item = serviceId}
dataset.services.push("{$serviceId}");
{/foreach}


	fetchServiceMetricResultGraph('{$metric}', dataset, '{$instanceGraphIndex}');

	{literal}
pm = window.plotMarkings[{/literal}{$instanceGraphIndex}{literal}] = [];
	{/literal}

	{foreach from = $yAxisMarkings item = marking}
pm.push({$marking})

	{/foreach}
	</script>
