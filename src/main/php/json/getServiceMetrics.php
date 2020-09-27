<?php

require_once 'jsonCommon.php';

$serviceId = $_REQUEST['serviceIds'][0];
$service = getServiceById($serviceId, true);
##$results = getServiceResultsMostRecent($service['identifier'], $service['node']);

$requestedMetrics = $_REQUEST['metrics'];

$retmets = array();
foreach ($service['listMetrics'] as $metric) {
	if (in_array($metric['name'], $_REQUEST['metrics'])) {
		$retmets[] = $metric;
	}
}

$service['listMetrics'] = $retmets;

if (count($requestedMetrics) > 0) {
	$metricsTitle = implode($requestedMetrics, ',');
} else {
	$metricsTitle = $requestedMetrics[0];
}

$ret = array(
	'chartIndex' => $_REQUEST['chartIndex'],
	'metric' => $metricsTitle,
	'services' => array($service)
);

outputJson($ret);
?>
