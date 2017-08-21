<?php

require_once 'jsonCommon.php';

use \libAllure\DatabaseFactory;
use \libAllure\Sanitizer;

function extractNagiosMetric($service, $field) {
	$listMetrics = explode(",", $service['output']);

	$match = preg_match_all('#([\|\w]+)=([\d\.]+)#i', $service['output'], $matches, PREG_SET_ORDER);

	$metric = new stdClass;
	$metric->date = $service['checked'];
	$metric->karma = $service['karma'];
	$metric->value = &$service['value'];

	foreach ($matches as $match) {
			if ($match[1] == $field) {
				$metric->value = $match[2];
			}
	}

	return $metric;
}

if (empty($_REQUEST['metrics'])) {
	$fields = array('karma');
} else {
	$fields = array();

	foreach ($_REQUEST['metrics'] as $metric) {
		$fields[] = $metric;
	}
}

$metrics = array();

foreach ($_REQUEST['serviceIds'] as $serviceId) {
	$service = getServiceById($serviceId);

	foreach ($fields as $field) {
		$results = getServiceResults($service['identifier'], $service['node'], 7);
		$results = array_reverse($results);

		$metrics[] = array(
			'serviceId' => $serviceId,
			'field' => $field,
			'metrics' => getServiceMetrics($results, $field)
		);
	}
}

$ret = array(
	'chartIndex' => $_REQUEST['chartIndex'],
	'metric' => implode($fields, ' + '),
	'services' => $metrics
);

header('Content-Type: application/json');
echo json_encode($ret);

?>
